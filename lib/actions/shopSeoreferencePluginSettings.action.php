<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopSeoreferencePluginSettingsAction extends waViewAction {

    public function execute() {
        $seoreference = wa()->getPlugin('seoreference');
        $sitemap = $seoreference->getSiteMap();
        $seoreference_model = new shopSeoreferencePluginModel();
        $rows = $seoreference_model->getAll();

        $domain_rows = array();
        foreach ($sitemap as $domain => $urls) {
            $domain_rows[$domain] = array();
        }
        foreach ($rows as $row) {
            $domain_rows[$row['domain']][] = $row;
        }
        $settings = $seoreference->getSettings();

        $this->view->assign('sitemap', $sitemap);
        $this->view->assign('domain_rows', $domain_rows);
        $this->view->assign('settings', $settings);
        waSystem::popActivePlugin();
    }

}
