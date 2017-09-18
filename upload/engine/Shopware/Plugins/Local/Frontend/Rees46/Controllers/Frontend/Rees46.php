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
}
