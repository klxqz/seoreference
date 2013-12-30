<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopSeoreferencePluginLinksModel extends waModel {

    protected $table = 'shop_seoreference_links';

    public function getAll($key = null, $normalize = false) {
        $sql = "SELECT * FROM {$this->table}";
        return $this->query($sql)->fetchAll($key, $normalize);
    }

    public function deleteAll() {
        $sql = "DELETE FROM {$this->table}";
        return $this->query($sql);
    }

}
