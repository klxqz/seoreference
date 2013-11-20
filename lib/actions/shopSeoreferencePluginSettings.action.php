<?php

/**
 * @author Коробонв Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopSeoreferencePluginSettingsAction extends waViewAction {

    public function execute() {
        $seoreference = wa()->getPlugin('seoreference');
        $urls = $seoreference->getSiteMap();

        $seoreference_model = new shopSeoreferencePluginModel();
        $rows = $seoreference_model->getAll();

        $settings = $seoreference->getSettings();

        $this->view->assign('count_page', count($urls));
        $this->view->assign('rows', $rows);
        $this->view->assign('settings', $settings);
    }

}
