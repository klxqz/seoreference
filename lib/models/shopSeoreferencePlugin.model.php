<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopSeoreferencePluginModel extends waModel {

    protected $table = 'shop_seoreference';

    public function deleteAll() {
        $sql = "DELETE FROM {$this->table}";
        return $this->query($sql);
    }

}
