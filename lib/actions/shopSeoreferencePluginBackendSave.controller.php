<?php

/**
 * @author Коробонв Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopSeoreferencePluginBackendSaveController extends waJsonController {

    public function execute() {
        try {
            $link = waRequest::post('link', array());
            $keywords = waRequest::post('keywords', array());
            $count = waRequest::post('count', array());

            $app_settings_model = new waAppSettingsModel();
            $shop_seoreference = waRequest::post('shop_seoreference');

            foreach ($shop_seoreference as $setting => $value) {
                $app_settings_model->set(array('shop', 'seoreference'), $setting, $value);
            }


            foreach ($link as $_link) {
                if (!trim($_link)) {
                    throw new waException('Поле "Продвигаемая страница" обязательно для заполнения');
                }
            }

            foreach ($keywords as $_keywords) {
                if (!trim($_keywords)) {
                    throw new waException('Поле "Ключевые слова" обязательно для заполнения');
                }
            }

            $seoreference = wa()->getPlugin('seoreference');
            $urls = $seoreference->getSiteMap();
            $urls_count = count($urls);

            $sum = 0;
            foreach ($count as $_count) {
                $sum += $_count;
            }

            if ($sum > $urls_count) {
                throw new waException('Суммарное количество генерируемых ссылок не должно превышать количество страниц сайта');
            }




            $seoreference_model = new shopSeoreferencePluginModel();
            $seoreference_model->deleteAll();

            $items = array();

            foreach ($link as $index => $_link) {
                $_keywords = $keywords[$index];
                $_count = $count[$index];
                $data = array('link' => $_link, 'keywords' => $_keywords, 'count' => $_count);
                if (!$_count) {
                    $items[] = $data;
                    continue;
                }
                $seoreference_model->insert($data);
            }

            $free_pages = $urls_count - $sum;
            $count_items = count($items);
            foreach ($items as $data) {
                $_count = ceil($free_pages / $count_items);
                $data['count'] = $_count;
                $seoreference_model->insert($data);
                $count_items--;
                $free_pages -=$_count;
            }

            $seoreference->ranging();

            $this->response['message'] = "Ok";
        } catch (Exception $e) {

            $this->setError($e->getMessage());
        }
    }

}
