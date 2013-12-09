<?php

/**
 * @author Коробонв Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopSeoreferencePlugin extends shopPlugin {

    protected $routing;
    protected $app_id;

    public function getSiteMap() {
        $urls = array();
        $this->routing = wa()->getRouting();
        $sm = new waSitemapConfig();
        $routes = $sm->getRoutes();
        $this->app_id = wa()->getApp();

        $category_model = new shopCategoryModel();
        $product_model = new shopProductModel();
        $page_model = new shopPageModel();

        foreach ($routes as $route) {
            $this->routing->setRoute($route);
            $domain = $this->routing->getDomain(null, true);
            $route_url = $domain . '/' . $this->routing->getRoute('url');
            // categories
            $sql = "SELECT c.id,c.parent_id,c.left_key,c.url,c.full_url,c.create_datetime,c.edit_datetime
                    FROM shop_category c
                    LEFT JOIN shop_category_routes cr ON c.id = cr.category_id
                    WHERE c.status = 1 AND (cr.route IS NULL OR cr.route = '" . $category_model->escape($route_url) . "')
                    ORDER BY c.left_key";
            $categories = $category_model->query($sql)->fetchAll('id');
            $category_url = $this->routing->getUrl($this->app_id . '/frontend/category', array('category_url' => '%CATEGORY_URL%'), true);
            foreach ($categories as $c_id => $c) {
                if ($c['parent_id'] && !isset($categories[$c_id])) {
                    unset($categories[$c_id]);
                    continue;
                }
                if (isset($route['url_type']) && $route['url_type'] == 1) {
                    $url = $c['url'];
                } else {
                    $url = $c['full_url'];
                }
                $urls[] = str_replace('%CATEGORY_URL%', $url, $category_url);
            }

            // products
            $sql = "SELECT p.url, p.create_datetime, p.edit_datetime";
            if (isset($route['url_type']) && $route['url_type'] == 2) {
                $sql .= ', c.full_url category_url';
            }
            $sql .= " FROM " . $product_model->getTableName() . ' p';
            if (isset($route['url_type']) && $route['url_type'] == 2) {
                $sql .= " LEFT JOIN " . $category_model->getTableName() . " c ON p.category_id = c.id";
            }
            $sql .= ' WHERE p.status = 1';
            if (isset($route['type_id']) && $route['type_id']) {
                $sql .= ' AND p.type_id IN (';
                $first = true;
                foreach ((array) $route['type_id'] as $t) {
                    $sql .= ($first ? '' : ',') . (int) $t;
                }
                $sql .= ')';
            }
            $products = $product_model->query($sql);
            $product_url = $this->routing->getUrl($this->app_id . '/frontend/product', array(
                'product_url' => '%PRODUCT_URL%',
                'category_url' => '%CATEGORY_URL%'
                    ), true);
            foreach ($products as $p) {
                $url = str_replace(array('%PRODUCT_URL%', '%CATEGORY_URL%'), array($p['url'], isset($p['category_url']) ? $p['category_url'] : ''), $product_url);
                $urls[] = $url;
            }

            $main_url = $this->getUrl('');
            // pages
            $sql = "SELECT full_url, url, create_datetime, update_datetime FROM " . $page_model->getTableName() . '
                    WHERE status = 1 AND domain = s:domain AND route = s:route';
            $pages = $page_model->query($sql, array('domain' => $domain, 'route' => $route['url']))->fetchAll();
            foreach ($pages as $p) {
                $urls[] = $main_url . $p['full_url'];
            }

            // main page
            $urls[] = $main_url;
        }

        return $urls;
    }

    protected function getUrl($path, $params = array()) {
        return $this->routing->getUrl($this->app_id . '/frontend' . ($path ? '/' . $path : ''), $params, true);
    }

    public function ranging() {
        $seoreference_model = new shopSeoreferencePluginModel();
        $seoreferencelinks_model = new shopSeoreferencePluginLinksModel();


        $pages = $this->getSiteMap();
        $rows = $seoreference_model->getAll();

        $seoreferencelinks_model->deleteAll();

        foreach ($rows as $row) {
            $keywords = explode(',', $row['keywords']);
            foreach ($keywords as $index => $_keywords) {
                if (!trim($_keywords)) {
                    unset($keywords[$index]);
                }
            }

            for ($i = 0; $i < $row['count']; $i++) {
                $k_index = $i % count($keywords);
                $_keywords = $keywords[$k_index];
                $page = array_pop($pages);
                $data = array('link' => $row['link'], 'keywords' => $_keywords, 'page' => $page);
                $seoreferencelinks_model->insert($data);
            }
        }
    }

    public function frontendFooter() {

        if ($this->getSettings('default_output')) {
            return $this->display();
        }
    }

    public static function display() {
        $app_settings_model = new waAppSettingsModel();
        $seoreferencelinks_model = new shopSeoreferencePluginLinksModel();
        $domain = wa()->getRouting()->getDomain(null, true);
        $https = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : '';
        $page = 'http' . (strtolower($https) == 'on' ? 's' : '') . '://';
        $page .= wa()->getRouting()->getDomainUrl($domain) . '/' . wa()->getConfig()->getRequestUrl();

        $result = $seoreferencelinks_model->getByField('page', $page);
        if ($result) {
            $link = $app_settings_model->get(array('shop', 'seoreference'), 'tpl_link');
            $link = str_replace('{link}', $result['link'], $link);
            $link = str_replace('{keywords}', $result['keywords'], $link);
            return $link;
        }
    }

}
