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

namespace Tabsl\DhlTracking\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class OrderMain
 */
class OrderMain extends OrderMain_parent
{

    /**
     * @return void
     */
    public function updateTabslDhl()
    {
        $order = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        if ($order->load($this->getEditObjectId())) {
            if ($order->updateTabslDhl()) {
                Registry::get('oxUtilsView')->addErrorToDisplay('<span style="color: green;">DHL-Daten wurden aktualisiert</span>');
            }
        }
    }

}
