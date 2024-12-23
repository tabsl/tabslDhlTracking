<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @copyright (c) Tobias Merkl | 2024
 * @link https://oxid-module.eu
 * @link https://github.com/tabsl/tabslDhlTracking
 * @package tabslDhlTracking
 **/

$sMetadataVersion = '2.1';

// tabsl module id
$psModuleId = 'tabslDhlTracking';
$psModuleName = '<b>tabsl</b>DhlTracking';
$psModuleVersion = '1.0.1';

// tabsl module description
$psModuleDesc = 'OXID eShop module for DHL parcel tracking.';

/**
 * Module information
 */
$aModule = [
    'id' => $psModuleId,
    'title' => [
        'de' => $psModuleName,
        'en' => $psModuleName,
    ],
    'description' => [
        'de' => $psModuleDesc,
        'en' => $psModuleDesc
    ],
    'thumbnail' => '',
    'version' => $psModuleVersion,
    'author' => 'Tobias Merkl',
    'url' => 'https://github.com/tabsl/tabslDhlTracking',
    'email' => 'noreply@oxid-module.eu',
    'extend' => [
        \OxidEsales\Eshop\Application\Controller\Admin\OrderMain::class => \Tabsl\DhlTracking\Controller\Admin\OrderMain::class,
        \OxidEsales\Eshop\Application\Model\Order::class => \Tabsl\DhlTracking\Model\Order::class,
    ],
    'settings' => [
        [
            'group' => 'main',
            'name' => 'tabsldhltracking_api_key',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'main',
            'name' => 'tabsldhltracking_api_url',
            'type' => 'str',
            'value' => 'https://api-eu.dhl.com/track/shipments'
        ]
    ],
    'blocks' => [
        [
            'template' => 'order_main.tpl',
            'block' => 'admin_order_main_form',
            'file' => 'views/blocks/admin_order_main_form.tpl'
        ]
    ],
    'events' => [
        'onActivate' => '\Tabsl\DhlTracking\Core\Setup::onActivate',
        'onDeactivate' => '\Tabsl\DhlTracking\Core\Setup::onDeactivate',
    ],
];
