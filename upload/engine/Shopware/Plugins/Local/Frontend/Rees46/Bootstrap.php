<?php

class Shopware_Plugins_Frontend_Rees46_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    public function getVersion()
    {
        return '1.0.0';
    }

    public function getLabel()
    {
        return 'REES46 eCommerce Marketing Suite';
    }

    public function getInfo()
    {
        return [
            'version' => $this->getVersion(),
            'label' => $this->getLabel(),
            'name' => 'Rees46',
            'author' => 'REES46',
            'supplier' => 'REES46',
            'copyright' => 'Copyright © 2017, REES46',
            'link' => 'https://rees46.com',
            'support' => 'support@rees46.com',
            'description' => '<b>Go to menu: Marketing > REES46</b>'
                . '<br><br>Go to your REES46 store dashboard to get the access to:'
                . '<br><ul>'
                . '<li>Triggered emails</li>'
                . '<li>Email marketing tool</li>'
                . '<li>Personalized search</li>'
                . '<li>Web push triggered notifications</li>'
                . '<li>Instant web push notifications</li>'
                . '<li>Audience segmentation</li>'
                . '<li>Abandoned cart remarketing tool</li>'
                . '</ul><br>'
                . '<button class="plugin-manager-action-button primary" onclick="window.open(\'https://rees46.com/customers/sign_in\');">REES46 dashboard</button><br><br>'
                . 'Documentation: <a href="http://docs.rees46.com/display/en/Shopware+Plugin" target="_blank">http://docs.rees46.com/display/en/Shopware+Plugin</a><br><br>'
                . 'Support: <a href="mailto:support@rees46.com?subject=Support for REES46 Shopware plugin">support@rees46.com</a>'
        ];
    }

    public function install()
    {
        $this->_registerController();
        $this->_createMenu();
        $this->_createEvent();
        $this->_createForm();
        $this->_createTable();
        $this->_createBlocks();
        $this->_clearCache();

        return [
            'success' => true
        ];
    }

    public function uninstall()
    {
        $this->_removeMenu();
        $this->_removeTable();
        $this->_clearCache();

        return [
            'success' => true
        ];
    }

    private function _registerController()
    {
        $this->registerController('Backend', 'Rees46');
        $this->registerController('Frontend', 'Rees46');
        $this->registerController('Frontend', 'Rees46Export');
    }

    private function _createMenu()
    {
        if ($this->assertMinimumVersion('5.2')) {
            $parent = $this->Menu()->findOneBy(['label' => 'Marketing']);
        } else {
            $parent = $this->Menu()->findOneBy('label', 'Marketing');
        }

        $item = $this->createMenuItem([
            'label' => 'REES46',
            'class' => 'rees46-menu-icon',
            'controller' => 'Rees46',
            'action' => 'index',
            'active' => 1,
            'position' => 999,
            'parent' => $parent,
        ]);
    }

    private function _removeMenu() {
        $menu = $this->Menu()->findOneBy(['label' => 'REES46']);

        Shopware()->Models()->remove($menu);
        Shopware()->Models()->flush();
    }

    private function _createEvent()
    {
        try {
            $this->subscribeEvent(
                'Enlight_Controller_Action_PostDispatch_Backend_Index',
                'onBackendIndex'
            );

            $this->subscribeEvent(
                'Enlight_Controller_Action_PostDispatchSecure_Backend_PluginManager',
                'onBackendPluginManager'
            );

            $this->subscribeEvent(
                'Enlight_Controller_Action_PostDispatch_Frontend',
                'onFrontend'
            );

            $this->subscribeEvent(
                'Shopware_Modules_Basket_AddArticle_Start',
                'onBasketAddArticle'
            );

            $this->subscribeEvent(
                'sBasket::sDeleteArticle::before',
                'onBasketDeleteArticle'
            );

            $this->subscribeEvent(
                'Shopware\Models\Order\Order::postUpdate',
                'onUpdateOrder'
            );
        } catch (Exception $exception) {
            throw new Exception("Error subscribe event. " . $exception->getMessage());
        }
    }

    private function _createForm()
    {
        $form = $this->Form();

        $form->setElement('boolean', 'REES46_ACTION_LEAD', [
            'value' => false,
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('boolean', 'REES46_ACTION_AUTH', [
            'value' => false,
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('boolean', 'REES46_ACTION_PRODUCT', [
            'value' => false,
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('boolean', 'REES46_ACTION_ORDER', [
            'value' => false,
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('boolean', 'REES46_ACTION_CUSTOMER', [
            'value' => false,
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('boolean', 'REES46_ACTION_FILE1', [
            'value' => false,
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('boolean', 'REES46_ACTION_FILE2', [
            'value' => false,
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('text', 'REES46_API_CATEGORY', [
            'value' => '',
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('text', 'REES46_API_KEY', [
            'value' => '',
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('text', 'REES46_API_SECRET', [
            'value' => '',
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('text', 'REES46_SETTING_STORE_KEY', [
            'value' => '',
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('text', 'REES46_SETTING_SECRET_KEY', [
            'value' => '',
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('text', 'REES46_SETTING_ORDER_CREATED', [
            'value' => '',
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('text', 'REES46_SETTING_ORDER_COMPLETED', [
            'value' => '',
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('text', 'REES46_SETTING_ORDER_CANCELLED', [
            'value' => '',
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('text', 'REES46_SETTING_PRODUCT_CURRENCY', [
            'value' => '',
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $form->setElement('boolean', 'REES46_SETTING_PRODUCT_TAX', [
            'value' => false,
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'hidden' => true,
        ]);

        $parent = $this->Forms()->findOneBy(['name' => 'Interface']);

        $form->setParent($parent);
    }

    private function _createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `rees46_blocks` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `page` varchar(255) NOT NULL,
                    `type` varchar(255) NOT NULL,
                    `title` varchar(255) NOT NULL,
                    `limit` int(11) NOT NULL,
                    `template` varchar(255) NOT NULL,
                    `position` int(11) NOT NULL,
                    `status` int(1) NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;';

        try {
            Shopware()->Db()->query($sql);
        } catch (Exception $exception) {
            throw new Exception('REES46: can not create table. ' . $exception->getMessage());
        }
    }

    private function _createBlocks()
    {
        $sql = 'INSERT INTO `rees46_blocks` (`page`, `type`, `title`, `limit`, `template`, `position`, `status`) VALUES ("index", "popular", "Popular Products", "6", "rees46", "1", "1");';
        $sql .= 'INSERT INTO `rees46_blocks` (`page`, `type`, `title`, `limit`, `template`, `position`, `status`) VALUES ("index", "supply", "Regular Purchase", "6", "rees46", "2", "1");';
        $sql .= 'INSERT INTO `rees46_blocks` (`page`, `type`, `title`, `limit`, `template`, `position`, `status`) VALUES ("index", "interesting", "You May Also Like", "6", "rees46", "3", "1");';

        $sql .= 'INSERT INTO `rees46_blocks` (`page`, `type`, `title`, `limit`, `template`, `position`, `status`) VALUES ("listing", "popular", "Popular Products", "6", "rees46", "4", "1");';
        $sql .= 'INSERT INTO `rees46_blocks` (`page`, `type`, `title`, `limit`, `template`, `position`, `status`) VALUES ("listing", "interesting", "You May Also Like", "6", "rees46", "5", "1");';
        $sql .= 'INSERT INTO `rees46_blocks` (`page`, `type`, `title`, `limit`, `template`, `position`, `status`) VALUES ("listing", "recently_viewed", "You Recently Viewed", "6", "rees46", "6", "1");';

        $sql .= 'INSERT INTO `rees46_blocks` (`page`, `type`, `title`, `limit`, `template`, `position`, `status`) VALUES ("detail", "also_bought", "Frequently Bought Together", "6", "rees46", "7", "1");';
        $sql .= 'INSERT INTO `rees46_blocks` (`page`, `type`, `title`, `limit`, `template`, `position`, `status`) VALUES ("detail", "similar", "Similar Products", "6", "rees46", "8", "1");';
        $sql .= 'INSERT INTO `rees46_blocks` (`page`, `type`, `title`, `limit`, `template`, `position`, `status`) VALUES ("detail", "buying_now", "Trending Products", "6", "rees46", "9", "1");';

        $sql .= 'INSERT INTO `rees46_blocks` (`page`, `type`, `title`, `limit`, `template`, `position`, `status`) VALUES ("checkout", "see_also", "Recommended For You", "6", "rees46", "10", "1");';
        $sql .= 'INSERT INTO `rees46_blocks` (`page`, `type`, `title`, `limit`, `template`, `position`, `status`) VALUES ("checkout", "interesting", "You May Also Like", "6", "rees46", "11", "1");';

        $sql .= 'INSERT INTO `rees46_blocks` (`page`, `type`, `title`, `limit`, `template`, `position`, `status`) VALUES ("search", "search", "Customers Who Looked For This Item Also Bought", "6", "rees46", "12", "1");';

        try {
            Shopware()->Db()->query($sql);
        } catch (Exception $exception) {
            throw new Exception('REES46: can not create blocks. ' . $exception->getMessage());
        }
    }

    private function _removeTable()
    {
        $sql = 'DROP TABLE IF EXISTS `rees46_blocks`;';

        try {
            Shopware()->Db()->query($sql);
        } catch (Exception $exception) {
            throw new Exception('REES46: can not delete table. ' . $exception->getMessage());
        }
    }

    private function _clearCache()
    {
        $cacheManager = $this->get('shopware.cache_manager');
        $cacheManager->clearHttpCache();
        $cacheManager->clearConfigCache();
        $cacheManager->clearTemplateCache();
    }

    public function onBackendIndex(Enlight_Event_EventArgs $args)
    {
        $controller = $args->getSubject();
        $request = $controller->Request();
        $response = $controller->Response();
        $action = $request->getActionName();
        $view = $controller->View();

        if ($action == 'index') {
            $view->addTemplateDir($this->Path() . 'Views/');
            $view->extendsTemplate('backend/override/index_header.tpl');
        }
    }

    public function onBackendPluginManager(Enlight_Event_EventArgs $args)
    {
        $controller = $args->getSubject();
        $request = $controller->Request();
        $response = $controller->Response();
        $action = $request->getActionName();
        $view = $controller->View();

        if ($action == 'load') {
            $view->addTemplateDir($this->Path() . 'Views/');
            $view->extendsTemplate('backend/override/plugin_manager_view_detail_container.js');
        }
    }

    public function onFrontend(Enlight_Event_EventArgs $args)
    {
        $front = $args->getSubject();
        $request = $front->Request();
        $response = $front->Response();
        $view = $front->View();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $view->addTemplateDir($this->Path() . 'Views/');

        if (!$request->isDispatched()
            || $response->isException()
            || $request->isXmlHttpRequest()
            || !$view->hasTemplate()
            || !$this->Config()->get('REES46_SETTING_STORE_KEY')
            || !$this->Config()->get('REES46_SETTING_SECRET_KEY')
            || $module != 'frontend'
        ) {
            return;
        }

        $js = 'r46(\'init\', \'' . $this->Config()->get('REES46_SETTING_STORE_KEY') . '\');' . "\n";

        if (!empty(Shopware()->Session()->sUserId)) {
            $customer = Shopware()->Models()->find('Shopware\Models\Customer\Customer', Shopware()->Session()->sUserId);

            if ($customer->getSalutation() == 'mr') {
                $customer_gender = 'm';
            } else {
                $customer_gender = 'f';
            }

            if ($customer->getBirthday() instanceof \DateTime) {
                $customer_birthday = $customer->getBirthday()->format('Y-m-d');
            }

            $js .= 'r46(\'profile\', \'set\', {';
            $js .= ' id: ' . $customer->getId() . ',';
            $js .= ' email: \'' . $customer->getEmail() . '\',';
            $js .= ' gender: \'' . $customer_gender . '\',';
            $js .= ' birthday: \'' . $customer_birthday . '\'';
            $js .= '});' . "\n";
        }

        if ($controller == 'detail' && $action == 'index') {
            $item = (int)$request->getParam('sArticle');

            $article = Shopware()->Modules()->Articles()->sGetArticleById($item);

            $category = $article['categoryID'];

            $categories = [];

            foreach ($this->getCategoriesByArticleId($article['articleID']) as $cat) {
                $categories[] = $cat['categoryID'];
            }

            if (isset($article['image']['thumbnails'][1]['source'])) {
                $image = $article['image']['thumbnails'][1]['source'];
            } else {
                $image = '';
            }

            $js .= 'r46(\'track\', \'view\', {';
            $js .= ' id: ' . (int)$article['articleID'] . ',';
            $js .= ' stock: ' . $article['isAvailable'] . ',';
            $js .= ' price: \'' . $article['price_numeric'] . '\',';
            $js .= ' name: \'' . $article['articleName'] . '\',';
            $js .= ' categories: ' . json_encode($categories) . ',';
            $js .= ' image: \'' . $image . '\',';
            $js .= ' url: \'' . $article['linkDetailsRewrited'] . '\',';
            $js .= '});' . "\n";
        }

        if ($controller == 'listing' && $action == 'index') {
            $category = (int)$request->getParam('sCategory');
        }

        $basket = Shopware()->Modules()->Basket()->sGetBasket();

        if ($basket['Quantity'] > 0) {
            $cart = [];

            foreach ($basket['content'] as $product) {
                if ($product['articleID'] > 0) {
                    $cart[] = $product['articleID'];
                }
            }
        }

        if ($controller == 'search' && $request->getParam('sSearch')) {
            $search_query = $request->getParam('sSearch');
        }

        if (Shopware()->Session()->offsetGet('REES46_CART')) {
            $js .= Shopware()->Session()->offsetGet('REES46_CART');

            Shopware()->Session()->offsetUnset('REES46_CART');   
        }

        if ($controller == 'checkout' && $action == 'changeQuantity') {
            foreach ($basket['content'] as $product) {
                if ($product['articleID'] > 0) {
                    $cart[] = [
                        'id' => $product['articleID'],
                        'amount' => $product['quantity'],
                    ];
                }
            }

            $js_cart = 'r46(\'track\', \'cart\', ' . json_encode($cart) . ' );' . "\n";

            Shopware()->Session()->offsetSet('REES46_CART', Shopware()->Session()->offsetGet('REES46_CART') . $js_cart);
        }

        if ($controller == 'checkout' && $action == 'finish' && Shopware()->Session()->offsetExists('sOrderVariables')) {
           $order = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->findOneBy(['number' => Shopware()->Session()->sOrderVariables['sOrderNumber']]);

            if (!empty($order)) {
                $order_data = [];

                foreach ($order->getDetails() as $order_product) {
                    $order_data['products'][] = [
                        'id' => $order_product->getArticleId(),
                        'price' => $order_product->getPrice(),
                        'amount' => $order_product->getQuantity(),
                    ];
                }

                $order_data['order'] = $order->getId();
                $order_data['order_price'] = Shopware()->Session()->sOrderVariables['sAmount'];

                $js_checkout = 'r46(\'track\', \'purchase\', ' . json_encode($order_data) . ' );' . "\n";

                Shopware()->Session()->offsetSet('REES46_CART', Shopware()->Session()->offsetGet('REES46_CART') . $js_checkout);
            }
        }

        $blocks = [];
        $css = false;

        /**
         * Loading blocks
         */
        if (($controller == 'index' && $action == 'index') ||
            ($controller == 'listing' && $action == 'index') ||
            ($controller == 'detail' && $action == 'index') ||
            ($controller == 'checkout' && ($action == 'confirm' || $action == 'cart')) ||
            ($controller == 'search'))
        {
            $blocks = $this->getBlocks($controller);

            if (!empty($blocks)) {
                foreach ($blocks as $key => $module) {
                    if (!$module['status']) {
                        continue;
                    }

                    $params = [];

                    $params['limit'] = (int)$module['limit'];

                    if ($module['template'] == 'rees46') {
                        $css = 'r46(\'add_css\', \'recommendations\');' . "\n";
                    }

                    if ($module['type'] == 'interesting') {
                        if (isset($item)) {
                            $params['item'] = $item;
                        }

                        if (isset($cart)) {
                            $params['cart'] = $cart;
                        }

                        $blocks[$key]['params'] = $params;
                    } elseif ($module['type'] == 'also_bought') {
                        if (isset($item)) {
                            $params['item'] = $item;

                            if (isset($cart)) {
                                $params['cart'] = $cart;
                            }

                            $blocks[$key]['params'] = $params;
                        }
                    } elseif ($module['type'] == 'similar') {
                        if (isset($item) && isset($cart)) {
                            $params['item'] = $item;
                            $params['cart'] = $cart;

                            $blocks[$key]['params'] = $params;
                        }
                    } elseif ($module['type'] == 'popular') {
                        if (isset($category)) {
                            $params['category'] = $category;
                        }

                        if (isset($cart)) {
                            $params['cart'] = $cart;
                        }

                        $blocks[$key]['params'] = $params;
                    } elseif ($module['type'] == 'see_also') {
                        if (isset($cart)) {
                            $params['cart'] = $cart;

                            $blocks[$key]['params'] = $params;
                        }
                    } elseif ($module['type'] == 'recently_viewed') {
                        $blocks[$key]['params'] = $params;
                    } elseif ($module['type'] == 'buying_now') {
                        if (isset($item)) {
                            $params['item'] = $item;
                        }

                        if (isset($cart)) {
                            $params['cart'] = $cart;
                        }

                        $blocks[$key]['params'] = $params;
                    } elseif ($module['type'] == 'search') {
                        if (isset($search_query)) {
                            $params['search_query'] = $search_query;

                            if (isset($cart)) {
                                $params['cart'] = $cart;
                            }

                            $blocks[$key]['params'] = $params;
                        }
                    } elseif ($module['type'] == 'supply') {
                        if (isset($item)) {
                            $params['item'] = $item;
                        }

                        if (isset($cart)) {
                            $params['cart'] = $cart;
                        }

                        $blocks[$key]['params'] = $params;
                    }
                }

                function sortModules($a, $b) {
                    return ($a['position'] - $b['position']);
                }

                uasort($blocks, 'sortModules');
            }
        }

        $view->assign('REES46_DATA', $js);
        $view->assign('REES46_CSS', $css);
        $view->assign('REES46_MODULES', $blocks);
        $view->extendsTemplate('frontend/override/index.tpl');
    }

    private function getBlocks($page)
    {
        return Shopware()->Db()->fetchAll('SELECT * FROM `rees46_blocks` WHERE page = "' . $page . '";');
    }

    public function onBasketAddArticle(Enlight_Event_EventArgs $args)
    {
        $subject = $args->get('subject');
        $ordernumber = $args->get('id');
        $quantity = $args->get('quantity');
        $product_id = Shopware()->Modules()->Articles()->sGetArticleIdByOrderNumber($ordernumber);

        $js = 'r46(\'track\', \'cart\', { id: ' . $product_id . ', amount: ' . $quantity . ' } );' . "\n";

        Shopware()->Session()->offsetSet('REES46_CART', Shopware()->Session()->offsetGet('REES46_CART') . $js);
    }

    public function onBasketDeleteArticle(Enlight_Hook_HookArgs $args)
    {
        $basket = Shopware()->Modules()->Basket()->sGetBasket();

        foreach ($basket['content'] as $basket_product) {
            if ($basket_product['id'] == $args->get('id')) {
                $js = 'r46(\'track\', \'remove_from_cart\', ' . $basket_product['articleID'] . ');' . "\n";

                Shopware()->Session()->offsetSet('REES46_CART', Shopware()->Session()->offsetGet('REES46_CART') . $js);
            }
        }
    }

    public function onUpdateOrder(Enlight_Event_EventArgs $args)
    {
        if ($this->Config()->get('REES46_SETTING_STORE_KEY') && $this->Config()->get('REES46_SETTING_SECRET_KEY')) {
            $order = $args->get('entity');
            $changes = $args->get('entityManager')->getUnitOfWork()->getEntityChangeSet($order);

            if (isset($changes['orderStatus'])) {
                $order_id = $order->getId();
                $order_status_id = $order->getOrderStatus()->getId();

                $created = !$this->Config()->get('REES46_SETTING_ORDER_CREATED') ? [] : $this->Config()->get('REES46_SETTING_ORDER_CREATED')->toArray();
                $completed = !$this->Config()->get('REES46_SETTING_ORDER_COMPLETED') ? [] : $this->Config()->get('REES46_SETTING_ORDER_COMPLETED')->toArray();
                $cancelled = !$this->Config()->get('REES46_SETTING_ORDER_CANCELLED') ? [] : $this->Config()->get('REES46_SETTING_ORDER_CANCELLED')->toArray();

                if (!empty($created) && in_array($order_status_id, $created)) {
                    $status = 0;
                } elseif (!empty($completed) && in_array($order_status_id, $completed)) {
                    $status = 1;
                } elseif (!empty($cancelled) && in_array($order_status_id, $cancelled)) {
                    $status = 2;
                }

                if (isset($status)) {
                    $data[] = [
                        'id' => $order_id,
                        'status' => $status,
                    ];

                    $curl['shop_id'] = $this->Config()->get('REES46_SETTING_STORE_KEY');
                    $curl['shop_secret'] = $this->Config()->get('REES46_SETTING_SECRET_KEY');
                    $curl['orders'] = $data;

                    $return = $this->curl('POST', 'https://api.rees46.com/import/sync_orders', json_encode($curl));
                }
            }
        }
    }

    private function getCategoriesByArticleId($articleId)
    {
        $ids = Shopware()->Container()->get('db')->fetchAll(
            'SELECT categoryID
             FROM s_articles_categories
             WHERE articleID = :articleId',
             [':articleId' => $articleId]
        );

        if ($ids) {
            return $ids;
        }
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
