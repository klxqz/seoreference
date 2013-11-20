<?php

/**
 * @author Коробонв Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
return array(
    'shop_seoreference' => array(
        'link' => array('varchar', 255, 'null' => 0, 'default' => ''),
        'keywords' => array('text', 'null' => 0),
        'count' => array('int', 11, 'null' => 0),
    ),
    'shop_seoreference_links' => array(
        'link' => array('varchar', 255, 'null' => 0, 'default' => ''),
        'page' => array('varchar', 255, 'null' => 0, 'default' => ''),
        'keywords' => array('text', 'null' => 0),
        ':keys' => array(
            'page' => 'page',
        ),
    ),
);
