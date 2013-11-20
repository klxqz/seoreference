<?php

/**
 * @author Коробонв Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopSeoreferencePluginBackendReportAction extends waViewAction {

    public function execute() {
        $seoreferencelinks_model = new shopSeoreferencePluginLinksModel();
        $seoreferencelinks = $seoreferencelinks_model->getAll();
        $this->view->assign('seoreferencelinks', $seoreferencelinks);
    }

}
