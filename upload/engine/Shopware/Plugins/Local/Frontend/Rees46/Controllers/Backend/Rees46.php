<?php
/**
 * Backend controller
 */

use Shopware\Components\CacheManager;
use Shopware\Models\Order\Order;
use Shopware\Models\Article\Article;

class Shopware_Controllers_Backend_Rees46 extends Shopware_Controllers_Backend_ExtJs
{
    private $fields = array(
        'REES46_ACTION_LEAD'              => '',
        'REES46_ACTION_AUTH'              => '',
        'REES46_ACTION_PRODUCT'           => '',
        'REES46_ACTION_ORDER'             => '',
        'REES46_ACTION_CUSTOMER'          => '',
        'REES46_ACTION_FILE1'             => '',
        'REES46_ACTION_FILE2'             => '',
        'REES46_API_CATEGORY'             => '',
        'REES46_API_KEY'                  => '',
        'REES46_API_SECRET'               => '',
        'REES46_SETTING_STORE_KEY'        => '',
        'REES46_SETTING_SECRET_KEY'       => '',
        'REES46_SETTING_ORDER_CREATED'    => '',
        'REES46_SETTING_ORDER_COMPLETED'  => '',
        'REES46_SETTING_ORDER_CANCELLED'  => '',
        'REES46_SETTING_PRODUCT_CURRENCY' => '',
        'REES46_SETTING_PRODUCT_TAX'      => '',
    );
    private $limit = 1000;
    private $em;
    private $config;

    public function __construct(Enlight_Controller_Request_Request $request, Enlight_Controller_Response_Response $response)
    {
        parent::__construct($request, $response);

        $this->em = Shopware()->Models();
        $this->config = Shopware()->Plugins()->Frontend()->Rees46()->Config()->toArray();
    }

    public function indexAction()
    {
        $this->View()->loadTemplate('backend/rees46/app.js');
    }

    public function getFieldsAction()
    {
		$this->apiLeadTracking();

        $data = $this->config;
        $data['auth_email'] = Shopware()->Config()->get('mail');
        $data['auth_country'] = Shopware()->Locale()->getRegion();
        $data['auth_currency'] = Shopware()->Currency()->getShortName();

        $this->View()->assign(array(
            'success' => true,
            'data'    => array($data),
        ));
    }

    private function apiLeadTracking()
    {
        if (!$this->config['REES46_ACTION_LEAD']) {
            $shop = $this->em->getRepository('Shopware\Models\Shop\Shop')->getActiveDefault();

            $params = [
                'website' => $shop->getAlwaysSecure() ? 'https://' . $shop->getSecureHost() . $shop->getSecureBasePath() : 'http://' . $shop->getHost() . $shop->getBasePath(),
                'cms_version' => Shopware()->Config()->version,
                'module_version' => Shopware()->Plugins()->Frontend()->Rees46()->getVersion(),
                'email' => Shopware()->Config()->get('mail'),
                'country' => $this->em->getRepository('Shopware\Models\Shop\Shop')->getDefault()->getLocale()->getTerritory(),
            ];

            if (Shopware()->Config()->get('address') != '') {
                $params['city'] = Shopware()->Config()->get('address');
            }

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_URL, 'https://app.rees46.com/trackcms/shopware?' . http_build_query($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_exec($ch);
            curl_close($ch);

            $this->save('REES46_ACTION_LEAD', true);
        }
    }

    public function apiGetShopEventsAction()
    {
        if ($this->config['REES46_SETTING_STORE_KEY'] == '' && $this->config['REES46_SETTING_SECRET_KEY'] == '') {
            return;
        }

        $curl['store_key'] = $this->config['REES46_SETTING_STORE_KEY'];
        $curl['store_secret'] = $this->config['REES46_SETTING_SECRET_KEY'];

        $return = $this->curl('GET', 'https://app.rees46.com/api/shop/events_status', json_encode($curl));

        if (!isset($return['result']) || $return['result'] == '') {
            return;
        }

        $api_shop_events = json_decode($return['result'], true);

        foreach ($api_shop_events as $id => $date) {
            switch ($id) {
                case 'view':
                    $name = 'Product viewed';
                    $compare = strtotime((new \DateTime('-5 min'))->format('Y-m-d H:i:s'));
                    break;
                case 'cart':
                    $name = 'Added to cart';
                    $compare = strtotime((new \DateTime('-12 hours'))->format('Y-m-d H:i:s'));
                    break;
                case 'remove_from_cart':
                    $name = 'Removed from cart';
                    $compare = strtotime((new \DateTime('-12 hours'))->format('Y-m-d H:i:s'));
                    break;
                case 'purchase':
                    $name = 'Purchased';
                    $compare = strtotime((new \DateTime('-24 hours'))->format('Y-m-d H:i:s'));
                    break;
            }

            if ($compare > $date) {
                $status = 0;
            } else {
                $status = 1;
            }

            if ($id != 'rate') {
                $data[] = [
                    'id' => $id,
                    'name' => $name,
                    'status' => $status,
                    'date' => date('Y-m-d H:i:s', $date),
                ];
            }
        }

        $this->View()->assign(array(
            'success' => true,
            'data'    => $data,
        ));
    }

    public function apiGetShopCategoriesAction()
    {
        $data = array();

        $return = $this->curl('GET', 'https://app.rees46.com/api/categories');

        if (isset($return['result'])) {
            $data = $return['result'];
        }

        $this->View()->assign(array(
            'success' => true,
            'data'    => json_decode($data, true),
        ));
    }

    public function apiCreateUserAction()
    {
        $data = array();
        $curl = array();

        $params = $this->Request()->getParams();

        if ($params['auth_email'] == '') {
            $data['error'] = 'error_auth_email';
        }

        if ($params['auth_phone'] == '') {
            $data['error'] = 'error_auth_phone';
        }

        if ($params['auth_firstname'] == '') {
            $data['error'] = 'error_auth_firstname';
        }

        if ($params['auth_lastname'] == '') {
            $data['error'] = 'error_auth_lastname';
        }

        if ($params['auth_country'] == '') {
            $data['error'] = 'error_auth_country';
        }

        if ($params['auth_currency'] == '') {
            $data['error'] = 'error_auth_currency';
        }

        if ($params['auth_category'] == '') {
            $data['error'] = 'error_auth_category';
        }

        if (isset($data['error'])) {
            $this->View()->assign(array(
                'success' => false,
                'data'    => $data['error'],
            ));

            return;
        }

        $curl['email'] = $params['auth_email'];
        $curl['phone'] = $params['auth_phone'];
        $curl['first_name'] = $params['auth_firstname'];
        $curl['last_name'] = $params['auth_lastname'];
        $curl['country_code'] = $params['auth_country'];
        $curl['currency_code'] = $params['auth_currency'];

        $return = $this->curl('POST', 'https://app.rees46.com/api/customers', json_encode($curl));

        $result = json_decode($return['result'], true);

        if ($return['info']['http_code'] < 200 || $return['info']['http_code'] >= 300) {
            $data['error'] = 'error_user_create';
        } else {
            if (isset($result['duplicate'])) {
                $data['error'] = 'error_user_duplicate';
            } else {
                $this->save('REES46_API_KEY', $result['api_key']);
                $this->save('REES46_API_SECRET', $result['api_secret']);
                $this->save('REES46_API_CATEGORY', $params['auth_category']);

                $data['success'] = 'success_user_create';
            }
        }

        if (isset($json['error'])) {
            $this->View()->assign(array(
                'success' => false,
                'data'    => $data['error'],
            ));
        } else {
            $this->View()->assign(array(
                'success' => true,
                'data'    => $data['success'],
            ));
        }
    }

    public function apiCreateShopAction()
    {
        $data = array();
        $curl = array();

        $params = $this->Request()->getParams();

        if ($this->config['REES46_API_KEY']  == '') {
            $data['error'] = 'error_api_key';
        }

        if ($this->config['REES46_API_SECRET']  == '') {
            $data['error'] = 'error_api_secret';
        }

        if ($this->config['REES46_API_CATEGORY']  == '') {
            $data['error'] = 'error_api_category';
        }

        if (isset($data['error'])) {
            $this->View()->assign(array(
                'success' => false,
                'data'    => $data['error'],
            ));

            return;
        }

        $shop = $this->em->getRepository('Shopware\Models\Shop\Shop')->getActiveDefault();

        $curl['api_key'] = $this->config['REES46_API_KEY'];
        $curl['api_secret'] = $this->config['REES46_API_SECRET'];
        $curl['url'] = $shop->getAlwaysSecure() ? 'https://' . $shop->getSecureHost() . $shop->getSecureBasePath() : 'http://' . $shop->getHost() . $shop->getBasePath();
        $curl['name'] = Shopware()->Config()->get('name');
        $curl['category'] = $this->config['REES46_API_CATEGORY'];
        $curl['yml_file_url'] = $shop->getAlwaysSecure() ? 'https://' . $shop->getSecureHost() . $shop->getSecureBasePath() : 'http://' . $shop->getHost() . $shop->getBasePath() . '/rees46_export';
        $curl['cms_id'] = 29;

        $return = $this->curl('POST', 'https://app.rees46.com/api/shops', json_encode($curl));

        $result = json_decode($return['result'], true);

        if ($return['info']['http_code'] < 200 || $return['info']['http_code'] >= 300) {
            $data['error'] = 'error_shop_create';
        } else {
            if (isset($result['duplicate'])) {
                $data['error'] = 'error_shop_duplicate';
            } else {
                $this->save('REES46_SETTING_STORE_KEY', $result['shop_key']);
                $this->save('REES46_SETTING_SECRET_KEY', $result['shop_secret']);

                $data['success'] = 'success_shop_create';
            }
        }

        if (isset($json['error'])) {
            $this->View()->assign(array(
                'success' => false,
                'data'    => $data['error'],
            ));
        } else {
            $this->View()->assign(array(
                'success' => true,
                'data'    => $data['success'],
            ));
        }
    }

    public function apiSetXmlAction()
    {
        $data = array();
        $curl = array();

        $params = $this->Request()->getParams();

        if ($params['store_key'] != '') {
            $curl['store_key'] = $params['store_key'];
        } elseif ($this->config['REES46_SETTING_STORE_KEY'] != '') {
            $curl['store_key'] = $this->config['REES46_SETTING_STORE_KEY'];
        } else {
            $data['error'] = 'error_store_key';
        }

        if ($params['secret_key'] != '') {
            $curl['store_secret'] = $params['secret_key'];
        } elseif ($this->config['REES46_SETTING_SECRET_KEY'] != '') {
            $curl['store_secret'] = $this->config['REES46_SETTING_SECRET_KEY'];
        } else {
            $data['error'] = 'error_secret_key';
        }

        if (isset($data['error'])) {
            $this->View()->assign(array(
                'success' => false,
                'data'    => $data['error'],
            ));

            return;
        }

        $shop = $this->em->getRepository('Shopware\Models\Shop\Shop')->getActiveDefault();

        $curl['yml_file_url'] = $shop->getAlwaysSecure() ? 'https://' . $shop->getSecureHost() . $shop->getSecureBasePath() : 'http://' . $shop->getHost() . $shop->getBasePath() . '/rees46_export';

        $return = $this->curl('PUT', 'https://app.rees46.com/api/shop/set_yml', json_encode($curl));

        if ($return['info']['http_code'] < 200 || $return['info']['http_code'] >= 300) {
            $data['error'] = 'error_xml_export';
        } else {
            $this->save('REES46_ACTION_AUTH', true);
            $this->save('REES46_ACTION_PRODUCT', true);
            $this->save('REES46_SETTING_STORE_KEY', $curl['store_key']);
            $this->save('REES46_SETTING_SECRET_KEY', $curl['store_secret']);

            $data['success'] = 'success_xml_export';
        }

        if (isset($data['error'])) {
            $this->View()->assign(array(
                'success' => false,
                'data'    => $data['error'],
            ));
        } else {
            $this->View()->assign(array(
                'success' => true,
                'data'    => $data['success'],
            ));
        }
    }

    public function apiSetOrdersAction()
    {
        $data = array();
        $curl = array();
        $export = array();

        $offset = $this->Request()->getParam('offset');
        $limit = $this->limit;

        if ($this->config['REES46_SETTING_STORE_KEY'] != '') {
            $curl['shop_id'] = $this->config['REES46_SETTING_STORE_KEY'];
        } else {
            $data['error'] = 'error_store_key';
        }

        if ($this->config['REES46_SETTING_SECRET_KEY'] != '') {
            $curl['shop_secret'] = $this->config['REES46_SETTING_SECRET_KEY'];
        } else {
            $data['error'] = 'error_secret_key';
        }

        if (isset($data['error'])) {
            $this->View()->assign(array(
                'success' => false,
                'data'    => $data['error'],
            ));

            return;
        }

        $builder = $this->em->createQueryBuilder();
        $builder->select(['orders', 'customer', 'details'])
                ->from('Shopware\Models\Order\Order', 'orders')
                ->leftJoin('orders.customer', 'customer')
                ->leftJoin('orders.details', 'details')
                ->where('orders.orderTime > ?1')
                ->andWhere('orders.number > 0')
                ->andWhere('details.articleId > 0')
                ->setParameter(1, (new \DateTime('-6 month'))->format('Y-m-d H:i:s'))
                ->addOrderBy('orders.id')
                ->setFirstResult($offset)
                ->setMaxResults($limit);

        $results = $builder->getQuery()->getArrayResult();
        $total = $this->em->getQueryCount($builder->getQuery());

        if (!empty($results)) {
            foreach ($results as $result) {
                $order_products = array();

                foreach ($result['details'] as $product) {
                    $categories_ids = array();

                    $builder = $this->em->createQueryBuilder();
                    $builder->select(['categories.id'])
                            ->from('Shopware\Models\Article\Article', 'article')
                            ->leftJoin('article.categories', 'categories', null, null, 'categories.id')
                            ->where('article.id = ?1')
                            ->setParameter(1, $product['articleId']);

                    $categories = $builder->getQuery()->getArrayResult();

                    foreach ($categories as $category) {
                        $categories_ids[] = $category['id'];
                    }

                    $builder = $this->em->createQueryBuilder();
                    $builder->select(['details.inStock'])
                            ->from('Shopware\Models\Article\Detail', 'details')
                            ->where('details.number = ?1')
                            ->setParameter(1, $product['articleNumber']);

                    $stock = $builder->getQuery()->getOneOrNullResult();

                    $order_products[] = array(
                        'id' => $product['articleId'],
                        'price' => $product['price'],
                        'categories' => $categories_ids,
                        'is_available' => $stock['inStock'],
                        'amount' => $product['quantity'],
                    );
                }

                $export[] = array(
                    'id' => $result['id'],
                    'user_id' => $result['customerId'],
                    'user_email' => $result['customer']['email'],
                    'date' => strtotime($result['orderTime']->format('Y-m-d H:i:s')),
                    'items' => $order_products,
                );
            }

            $curl['orders'] = $export;

            $return = $this->curl('POST', 'https://api.rees46.com/import/orders', json_encode($curl));
 
            if ($return['info']['http_code'] < 200 || $return['info']['http_code'] >= 300) {
                $data['error'] = 'error_orders_export';
            } else {
                $offset++;

                if ($total > $offset * $limit) {
                    $data['offset'] = $offset;
                    $data['success'] = 'process_orders_export';
                    $data['total'] = $total;
                    $data['count'] = $offset * $limit;
                } else {
                    $this->save('REES46_ACTION_ORDER', true);
                    $data['success'] = 'success_orders_export';
                    $data['total'] = $total;
                }
            }
        } else {
            $this->save('REES46_ACTION_ORDER', true);

            $data['success'] = 'empty_orders_export';
        }

        if (isset($data['error'])) {
            $this->View()->assign(array(
                'success' => false,
                'data'    => $data['error'],
            ));
        } else {
            $this->View()->assign(array(
                'success' => true,
                'data'    => $data['success'],
                'total'   => $data['total'],
                'count'   => $data['count'],
                'offset'  => $data['offset'],
            ));
        }
    }

    public function apiSetCustomersAction()
    {
        $data = array();
        $curl = array();
        $export = array();

        $offset = $this->Request()->getParam('offset');
        $limit = $this->limit;

        if ($this->config['REES46_SETTING_STORE_KEY'] != '') {
            $curl['shop_id'] = $this->config['REES46_SETTING_STORE_KEY'];
        } else {
            $data['error'] = 'error_store_key';
        }

        if ($this->config['REES46_SETTING_SECRET_KEY'] != '') {
            $curl['shop_secret'] = $this->config['REES46_SETTING_SECRET_KEY'];
        } else {
            $data['error'] = 'error_secret_key';
        }

        if (isset($data['error'])) {
            $this->View()->assign(array(
                'success' => false,
                'data'    => $data['error'],
            ));

            return;
        }

        $builder = $this->em->createQueryBuilder();
        $builder->select(['customer.id', 'customer.email'])
                ->from('Shopware\Models\Customer\Customer', 'customer')
                ->addOrderBy('customer.id', 'ASC')
                ->setFirstResult($offset)
                ->setMaxResults($limit);

        $results = $builder->getQuery()->getArrayResult();
        $total = $this->em->getQueryCount($builder->getQuery());

        if (!empty($results)) {
            foreach ($results as $result) {
                $export[] = array(
                    'id' => $result['id'],
                    'email' => $result['email'],
                );
            }

            $curl['audience'] = $export;

            $return = $this->curl('POST', 'https://api.rees46.com/import/audience', json_encode($curl));

            if ($return['info']['http_code'] < 200 || $return['info']['http_code'] >= 300) {
                $data['error'] = 'error_customers_export';
            } else {
                $offset++;

                if ($total > $offset * $limit) {
                    $data['offset'] = $offset;
                    $data['success'] = 'process_customers_export';
                    $data['total'] = $total;
                    $data['count'] = $offset * $limit;
                } else {
                    $this->save('REES46_ACTION_CUSTOMER', true);

                    $data['success'] = 'success_customers_export';
                    $data['total'] = $total;
                }
            }
        } else {
            $this->save('REES46_ACTION_CUSTOMER', true);

            $data['success'] = 'empty_customers_export';
        }

        if (isset($data['error'])) {
            $this->View()->assign(array(
                'success' => false,
                'data'    => $data['error'],
            ));
        } else {
            $this->View()->assign(array(
                'success' => true,
                'data'    => $data['success'],
                'total'   => $data['total'],
                'count'   => $data['count'],
                'offset'  => $data['offset'],
            ));
        }
    }

    public function apiGetWebPushFilesAction()
    {
        $data = array();

        $files = array(
            'manifest.json',
            'push_sw.js'
        );

        $fileSystem = $this->container->get('file_system');
        $dir = $this->container->getParameter('kernel.root_dir') . '/';

        foreach ($files as $key => $file) {
            if (!is_file($dir . $file)) {
                $ch = curl_init();

                $url = 'https://raw.githubusercontent.com/rees46/web-push-files/master/' . $file;

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($ch);
                $info = curl_getinfo($ch);

                curl_close($ch);

                if ($info['http_code'] >= 200 || $info['http_code'] < 300) {
                    file_put_contents($dir . $file, $result);
                }
            }

            if (is_file($dir . $file)) {
                if ($file == 'manifest.json') {
                    $this->save('REES46_ACTION_FILE1', true);
                } elseif ($file == 'push_sw.js') {
                    $this->save('REES46_ACTION_FILE2', true);
                }

                $data['success'] = 'success_files_load';
            } else {
                $data['error'] = 'error_files_load';
            }
        }

        if (isset($data['error'])) {
            $this->View()->assign(array(
                'success' => false,
                'data'    => $data['error'],
            ));
        } else {
            $this->View()->assign(array(
                'success' => true,
                'data'    => $data['success'],
            ));
        }
    }

    public function apiFinishConfigureAction()
    {
        if ($this->config['REES46_API_KEY'] != '' && $this->config['REES46_API_SECRET'] != '') {
            $url = 'https://app.rees46.com/api/customers/login';
            $api_key = $this->config['REES46_API_KEY'];
            $api_secret = $this->config['REES46_API_SECRET'];

            $this->View()->assign(array(
                'success'    => true,
                'url'        => $url,
                'api_key'    => $api_key,
                'api_secret' => $api_secret,
            ));
        } else {
            $link = 'https://app.rees46.com/customers/sign_in';

            $this->View()->assign(array(
                'success'    => true,
                'link'       => $link,
            ));
        }
    }

    public function apiGotoDashboardAction()
    {
        if ($this->config['REES46_API_KEY'] != '' && $this->config['REES46_API_SECRET'] != '') {
            $url = 'https://app.rees46.com/api/customers/login';
            $api_key = $this->config['REES46_API_KEY'];
            $api_secret = $this->config['REES46_API_SECRET'];

            $this->View()->assign(array(
                'success'    => true,
                'url'        => $url,
                'api_key'    => $api_key,
                'api_secret' => $api_secret,
            ));
        } else {
            $link = 'https://app.rees46.com/customers/sign_in';

            $this->View()->assign(array(
                'success'    => true,
                'link'       => $link,
            ));
        }
    }

    public function getBlocksAction()
    {
        $data = Shopware()->Db()->fetchAll('SELECT * FROM `rees46_blocks`');

        $this->View()->assign(array(
            'success' => true,
            'data'    => $data,
        ));
    }

    public function saveConfigAction()
    {
        $data = $this->Request()->getParams();
        $params = json_decode($data['params'], true);

        foreach ($params as $param => $value) {
            if (isset($this->fields[$param])) {
                $this->save($param, $value);
            }
        }

        $this->clearCache();

        $this->View()->assign(array(
            'success' => true,
            'data'    => 'success_config_save',
        ));
    }

    public function saveBlocksAction()
    {
        $data = $this->Request()->getParams();
        $params = json_decode($data['params'], true);

        if (!empty($params)) {
            $this->saveBlocks($params);
        } else {
        	$this->clearBlocks();
        }

        $this->clearCache();

        $this->View()->assign(array(
            'success' => true,
            'data'    => 'success_blocks_save',
        ));
    }

    private function saveBlocks($blocks)
    {
        $ids = [];

        foreach ($blocks as $block) {
	        $id = $block['id'] > 0 ? $block['id'] : null;
	        unset($block['id']);

	        if ($id !== null) {
	            Shopware()->Db()->update('rees46_blocks', $block, ['id=?' => $id]);

	            $ids[] = $id;
	        } else {
	            Shopware()->Db()->insert('rees46_blocks', $block);

	            $ids[] = Shopware()->Db()->lastInsertId('rees46_blocks');
	        }
        }

        $this->undeleteBlocks($ids);
    }

    private function undeleteBlocks($ids)
    {
        if (empty($ids)) {
            return;
        }

        $ids_str = implode(',', $ids);

        $sql = 'DELETE FROM `rees46_blocks` WHERE `id` NOT IN (' . $ids_str . ');';

        Shopware()->Db()->query($sql);
    }

    private function clearBlocks($ids)
    {
        $sql = 'TRUNCATE `rees46_blocks`;';

        Shopware()->Db()->query($sql);
    }

    private function save($field, $value)
    {
        $writer = $this->get('config_writer');
        $writer->save($field, $value);
    }

    private function clearCache()
    {
        $cacheManager = $this->get('shopware.cache_manager');
        $cacheManager->clearConfigCache();
    }

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
