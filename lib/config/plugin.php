<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
return array(
    'name' => 'Cсылочный вес',
    'description' => 'Проводит Seo оптимизацию',
    'img' => 'img/seoreference.png',
    'vendor' => '985310',
    'version' => '1.0.5',
    'rights' => false,
    'shop_settings' => true,
    'handlers' => array(
        'frontend_footer' => 'frontendFooter',
    )
);
