<?php
/**
 * Frontend controller
 */

class Shopware_Controllers_Frontend_Rees46 extends Enlight_Controller_Action
{
    public function indexAction()
    {
        $product_ids = explode(',', $this->Request()->getParam('product_ids'));
        $block_id = (int)$this->Request()->getParam('module_id');

        if (empty($product_ids) || !$block_id) {
            return;
        }

        $settings = $this->getBlock($block_id);

        foreach ($product_ids as $product_id) {
            $article = Shopware()->Modules()->Articles()->sGetArticleById((int)$product_id);

            if (!$article['isAvailable']) {
                $this->disableProduct($product_id);

                continue;
            }

            $url = $article['linkDetails'];
            $urlrw = $article['linkDetailsRewrited'];

            if (parse_url($url, PHP_URL_QUERY)) {
                $sp = '&';
            } else {
                $sp = '?';
            }

            $url = $url . $sp . 'recommended_by=' . $settings['type'];
            $urlrw = $urlrw . $sp . 'recommended_by=' . $settings['type'];

            $article['linkDetails'] = $url;
            $article['linkDetailsRewrited'] = $urlrw;

            $articles[] = $article;
        }

        if (empty($articles)) {
            return;
        }

        if ($settings['template'] == 'rees46') {
            $this->View()->loadTemplate('frontend/rees46/listing_ajax.tpl');
        } else {
            $this->View()->loadTemplate('frontend/listing/listing_ajax.tpl');
        }

        $this->View()->assign(['sArticles' => $articles, 'articles' => $articles, 'productBoxLayout' => $settings['template']]);
    }

    private function getBlock($id)
    {
        return Shopware()->Db()->fetchRow('SELECT * FROM `rees46_blocks` WHERE id = "' . $id . '";');
    }

    private function disableProduct($id)
    {
        $curl['shop_id'] = $this->Config()->get('REES46_SETTING_STORE_KEY');
        $curl['shop_secret'] = $this->Config()->get('REES46_SETTING_SECRET_KEY');
        $curl['item_ids'] = $id;

        $return = $this->curl('POST', 'https://api.rees46.com/import/disable', json_encode($curl));
    }

    private function curl($type, $url, $params = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_URL, $url);

        if (isset($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        $data = [
            'result' => curl_exec($ch),
            'info' => curl_getinfo($ch),
        ];

        curl_close($ch);

        return $data;
    }
}
