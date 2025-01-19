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

namespace Tabsl\DhlTracking\Model;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class Order
 */
class Order extends Order_parent
{

    /**
     * Get all parcel events
     *
     * @return array
     */
    public function getTabslDhlEvents()
    {
        $infos = [];
        $data = $this->oxorder__tabsldhltracking_info->rawValue;
        if (!empty($data)) {
            $data = json_decode($data, true);
            foreach ($data['shipments'][0]['events'] as $event) {
                $infos[] = $event;
            }
        }
        return $infos;
    }

    /**
     * Get latest parcel status
     *
     * @return mixed|null
     */
    public function getTabslDhlStatus()
    {
        $data = $this->oxorder__tabsldhltracking_info->rawValue;
        if (!empty($data)) {
            $data = json_decode($data, true);
            return $data['shipments'][0]['status'];
        }
        return null;
    }

    /**
     * Get full parcel information
     * @return void
     */
    public function getTabslDhlInfo()
    {
        $data = $this->oxorder__tabsldhltracking_info->rawValue;
        if (!empty($data)) {
            $data = json_decode($data, true);
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        }
        return;
    }

    /**
     * Update parcel information in database
     *
     * @return bool
     */
    public function updateTabslDhl()
    {
        $trackingNumber = $this->oxorder__oxtrackcode->value;
        $trackingNumber = str_replace(' ', '', $trackingNumber);
        // only take the first tracking number if multiple are present
        if (strpos($trackingNumber, ',') !== false || strpos($trackingNumber, ';') !== false) {
            $parts = preg_split('/[,;]/', $trackingNumber);
            $trackingNumber = $parts[0];
        }

        if (!$trackingNumber) {
            return false;
        }

        $debug = $this->getConfig()->getConfigParam('tabsldhltracking_debug');
        $apiKey = $this->getConfig()->getConfigParam('tabsldhltracking_api_key');
        $apiUrl = $this->getConfig()->getConfigParam('tabsldhltracking_api_url');
        $senderCountry = $this->getConfig()->getConfigParam('tabsldhltracking_senderCountry');

        if (!$apiKey || !$apiUrl) {
            return false;
        }

        $deliveryZip = $this->oxorder__oxbillzip->value;
        $deliveryCountryId = $this->oxorder__oxdelcountryid->value != '' ? $this->oxorder__oxdelcountryid->value : $this->oxorder__oxbillcountryid->value;

        $sql = "SELECT oxisoalpha2 FROM oxcountry WHERE oxid = " . DatabaseProvider::getDb()->quote($deliveryCountryId);
        $deliveryCountry = DatabaseProvider::getDb()->getOne($sql);

        $url = sprintf(
            '%s?trackingNumber=%s&recipientPostalCode=%s&language=de&requesterCountryCode=%s&originCountryCode=%s',
            $apiUrl,
            $trackingNumber,
            $deliveryZip,
            $deliveryCountry,
            $senderCountry
        );

        if ($debug) {
            Registry::get('oxUtilsView')->addErrorToDisplay($url);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'DHL-API-Key: ' . $apiKey
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        if ($debug) {
            Registry::get('oxUtilsView')->addErrorToDisplay($response);
        }

        if (!$response) {
            return false;
        }

        $jsonData = json_decode($response, true);

        if (!$jsonData) {
            return false;
        }

        $deliveryDate = null;
        if ($jsonData['shipments'][0]['status']['statusCode'] == "delivered") {
            $deliveryDate = $jsonData['shipments'][0]['status']['timestamp'];
        }

        $this->oxorder__tabsldhltracking_deliverydate = new \OxidEsales\Eshop\Core\Field($deliveryDate);
        $this->oxorder__tabsldhltracking_info = new \OxidEsales\Eshop\Core\Field($response);
        $this->save();

        return true;
    }
}
