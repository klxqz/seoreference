<?php

$model = new waModel();
$sql = "ALTER TABLE `shop_seoreference` ADD `domain` VARCHAR( 255 ) NOT NULL FIRST";
try {
    $model->query($sql);
} catch (waDbException $e) {
    if (class_exists('waLog')) {
        waLog::log(basename(__FILE__) . ': ' . $e->getMessage(), 'shop-update.log');
    }
    throw $e;
}
