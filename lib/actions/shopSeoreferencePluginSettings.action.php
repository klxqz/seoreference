<?php

/**
 * @author Коробонв Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopSeoreferencePluginSettingsAction extends waViewAction {

    public function execute() {
        $seoreference = wa()->getPlugin('seoreference');
        $domains_urls = $seoreference->getSiteMap();
        $seoreference_model = new shopSeoreferencePluginModel();
        $rows = $seoreference_model->getAll();

        $settings = $seoreference->getSettings();

        $this->view->assign('domains_urls', $domains_urls);
        $this->view->assign('rows', $rows);
        $this->view->assign('settings', $settings);
        waSystem::popActivePlugin();
    }

}
