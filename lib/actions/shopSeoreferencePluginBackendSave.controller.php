<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopSeoreferencePluginBackendSaveController extends waJsonController {

    public function execute() {
        try {
            $link = waRequest::post('link', array());
            $keywords = waRequest::post('keywords', array());
            $count = waRequest::post('count', array());
            $domains = waRequest::post('domains', array());

            $app_settings_model = new waAppSettingsModel();
            $shop_seoreference = waRequest::post('shop_seoreference');

            foreach ($shop_seoreference as $setting => $value) {
                $app_settings_model->set(array('shop', 'seoreference'), $setting, $value);
            }

            $link = array_map('trim', $link);
            if (($key = array_search('', $link)) !== false) {
                throw new waException('Поле "Продвигаемая страница" обязательно для заполнения. Домен ' . $domains[$key]);
            }

            $keywords = array_map('trim', $keywords);
            if (($key = array_search('', $keywords)) !== false) {
                throw new waException('Поле "Ключевые слова" обязательно для заполнения. Домен ' . $domains[$key]);
            }


            $domain_count = array();
            foreach ($domains as $index => $domain) {
                if (!isset($domain_count[$domain])) {
                    $domain_count[$domain] = isset($count[$index]) && intval($count[$index]) ? $count[$index] : 0;
                } else {
                    $domain_count[$domain] += isset($count[$index]) && intval($count[$index]) ? $count[$index] : 0;
                }
            }

            $seoreference = wa()->getPlugin('seoreference');
            $domains_url = $seoreference->getSiteMap();

            foreach ($domain_count as $domain => $_count) {
                if ($_count > count($domains_url[$domain])) {
                    throw new waException('Суммарное количество генерируемых ссылок не должно превышать количество страниц домена ' . $domain
                    . '. Максимальное количество ссылок для домена ' . $domain . ': ' . count($domains_url[$domain]));
                }
            }

            $seoreference_model = new shopSeoreferencePluginModel();
            $seoreference_model->deleteAll();

            $domain_items = array();

            foreach ($link as $index => $_link) {
                $_keywords = $keywords[$index];
                $_count = $count[$index];
                $_domain = $domains[$index];
                $data = array('domain' => $_domain, 'link' => $_link, 'keywords' => $_keywords, 'count' => $_count);
                if (!$_count) {
                    $domain_items[$_domain][] = $data;
                    continue;
                }
                $seoreference_model->insert($data);
            }

            foreach ($domain_items as $domain => $items) {

                $free_pages = count($domains_url[$domain]) - $domain_count[$domain];
                $count_items = count($items);
                foreach ($items as $data) {
                    $_count = ceil($free_pages / $count_items);
                    $data['count'] = $_count;
                    $seoreference_model->insert($data);
                    $count_items--;
                    $free_pages -=$_count;
                }
            }
            $seoreference->ranging();

            $this->response['message'] = "Ok";
        } catch (Exception $e) {

            $this->setError($e->getMessage());
        }
    }

}
