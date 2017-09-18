<?php
/**
 * Export controller
 */

class Shopware_Controllers_Frontend_Rees46Export extends Enlight_Controller_Action
{
    private $limit = 500;
    private $em;
    private $config;

    /**
     * Init controller method
     */
    public function init()
    {
        $this->em = Shopware()->Models();
        $this->config = Shopware()->Plugins()->Frontend()->Rees46()->Config()->toArray();
        $this->Front()->Plugins()->ScriptRenderer()->setRender(false);
        $this->Front()->Plugins()->ViewRenderer()->setNoRender(true);
        $this->Front()->Plugins()->Json()->setRenderer(false);
    }

    /**
     * Index action method
     */
    public function indexAction()
    {
        if (!$this->config['REES46_SETTING_STORE_KEY'] || !$this->config['REES46_SETTING_SECRET_KEY']) {
            $this->Response()->setHttpResponseCode(404);

            return;
        } else {
            $this->redirect(['controller' => 'rees46_export', 'action' => 'category']);
        }
    }

    /**
     * Export product categories
     */
    public function categoryAction()
    {
        if (!$this->config['REES46_SETTING_STORE_KEY'] || !$this->config['REES46_SETTING_SECRET_KEY']) {
            $this->Response()->setHttpResponseCode(404);

            return;
        }

        $curl = [
            'shop_id' => $this->config['REES46_SETTING_STORE_KEY'],
            'shop_secret' => $this->config['REES46_SETTING_SECRET_KEY'],
            'categories' => []
        ];

        $context = Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext();
        $shop_parent_category = $context->getShop()->getCategory()->getId();

        $categories = $this->em->createQueryBuilder()
                ->select('category.id as id', 'category.name as name', 'category.parentId as parentId', 'category.path as path')
                ->from('Shopware\Models\Category\Category', 'category')
                ->where('category.active = 1')
                ->andWhere('category.blog = 0')
                ->addOrderBy('category.parentId')
                ->addOrderBy('category.position')
                ->getQuery()
                ->getArrayResult();

        if (count($categories) == 0){
            return;
        }

        foreach ($categories as $category) {
            $path = array_pop(explode('|', mb_substr($category['path'], 0, -1)));

            if ($category['id'] == $shop_parent_category || $path == $shop_parent_category) {
                $curl['categories'][] = [
                    'id' => $category['id'],
                    'name' => $category['name'],
                    'parent' => $category['parentId'],
                ];
            }
        }

        $return = $this->curl('POST', 'https://api.rees46.com/import/categories', json_encode($curl));

        if ($return['info']['http_code'] == 204) {
            $this->redirect(['controller' => 'rees46_export', 'action' => 'product']);
        } else {
            Shopware()->PluginLogger()->error('REES46: could not export categories (' . $return['info']['http_code'] . ').');
        }
    }

    /**
     * Export products
     */
    public function productAction()
    {
        if (!$this->config['REES46_SETTING_STORE_KEY'] || !$this->config['REES46_SETTING_SECRET_KEY']) {
            $this->Response()->setHttpResponseCode(404);

            return;
        }

        $curl = [
            'shop_id' => $this->config['REES46_SETTING_STORE_KEY'],
            'shop_secret' => $this->config['REES46_SETTING_SECRET_KEY'],
            'items' => []
        ];

        $start = $this->Request()->getParam('start', 0);

        $sql = '
            SELECT a.id, a.name, a.taxID, d.instock, s.name AS brand, p.price, t.tax
            FROM s_articles AS a
            LEFT JOIN s_articles_details AS d ON (a.id = d.articleID)
            LEFT JOIN s_articles_supplier AS s ON (a.supplierID = s.id)
            LEFT JOIN s_articles_prices AS p ON (d.id = p.articledetailsID)
            LEFT JOIN s_core_tax AS t ON (a.taxID = t.id)
            WHERE a.active = 1
            AND d.kind = 1
            AND d.instock > 0
            AND p.price > 0
            AND p.from = 1
            ORDER BY a.id ASC
            LIMIT ' . $start . ', ' . $this->limit . '
        ';

        $products = Shopware()->Db()->fetchAll($sql);

        $currencies = Shopware()->Shop()->getCurrencies()->toArray();

        $factor = 1;

        foreach ($currencies as $currency) {
            if ($currency->getCurrency() == $this->config['REES46_SETTING_PRODUCT_CURRENCY']) {
                $factor = $currency->getFactor();
            }
        }

        if (!empty($products)) {
            foreach ($products as $product) {
                $price = Shopware()->Modules()->Articles()->sCalculatingPriceNum($product['price'], $product['tax'], false, $this->config['REES46_SETTING_PRODUCT_TAX'] ? false : true, $product['taxID'], true);
                $price = round(floatval($price) * floatval($factor), 2);

                $images = Shopware()->Modules()->Articles()->getArticleListingCover($product['id']);

                if ($images['src']['1']) {
                    $image = $images['src']['1'];
                } elseif ($images['src']['original']) {
                    $image = $images['src']['original'];
                } else {
                    $image = '';
                }

                $base = Shopware()->Container()->get('config')->get('baseFile');
                $detail = $base . '?sViewport=detail&sArticle=' . $product['id'];
                $link = Shopware()->Modules()->Core()->sRewriteLink($detail, $product['name']);

                $sql_cat = '
                    SELECT categoryID AS id
                    FROM s_articles_categories
                    WHERE articleID = ' . $product['id'] . '
                ';

                $cats = Shopware()->Db()->fetchAll($sql_cat);

                $categories = [];

                foreach ($cats as $category) {
                    $categories[] = $category['id'];
                }

                $curl['items'][] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $price,
                    'currency' => $this->config['REES46_SETTING_PRODUCT_CURRENCY'],
                    'url' => $link,
                    'picture' => $image,
                    'available' => $product['instock'],
                    'categories' => $categories,
                    'brand' => $product['brand'],
                ];
            }
        }

        if (count($curl['items']) > 0){
            $return = $this->curl('PUT', 'https://api.rees46.com/import/products', json_encode($curl));

            if ($return['info']['http_code'] == 204) {
                if (count($curl['items']) == $this->limit) {
                    $this->forward('product', 'rees46_export', 'frontend', [
                        'start' => $start + $this->limit,
                    ]);
                } else {
                    $this->redirect(['controller' => 'rees46_export', 'action' => 'sync']);
                }
            } else {
                Shopware()->PluginLogger()->error('REES46: could not export products (' . $return['info']['http_code'] . ').');
            }
        } else {
            $this->redirect(['controller' => 'rees46_export', 'action' => 'sync']);
        }
    }

    /**
     * Deleting inactive products
     */
    public function syncAction()
    {
        if (!$this->config['REES46_SETTING_STORE_KEY'] || !$this->config['REES46_SETTING_SECRET_KEY']) {
            $this->Response()->setHttpResponseCode(404);

            return;
        }

        $curl = [
            'method' => 'PATCH',
            'shop_id' => $this->config['REES46_SETTING_STORE_KEY'],
            'shop_secret' => $this->config['REES46_SETTING_SECRET_KEY'],
            'items' => []
        ];

        $sql = '
            SELECT a.id
            FROM s_articles AS a
            LEFT JOIN s_articles_details AS d ON (a.id = d.articleID)
            WHERE a.active = 1
            AND d.kind = 1
            AND d.instock > 0
        ';

        $products = Shopware()->Db()->fetchAll($sql);

        foreach ($products as $product) {
            $curl['items'][] = $product['id'];
        }

        if (count($products) > 0) {
            $return = $this->curl('POST', 'https://api.rees46.com/import/products', json_encode($curl));

            if ($return['info']['http_code'] != 204) {
                Shopware()->PluginLogger()->error('REES46: REES46: could not sync products (' . $return['info']['http_code'] . ').');
            }
        }
    }

    /**
     * Curl helper method
     */
    private function curl($type, $url, $params = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_URL, $url);

        if (isset($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        $data = array(
            'result' => curl_exec($ch),
            'info' => curl_getinfo($ch),
        );

        curl_close($ch);

        return $data;
    }
}
