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

use OxidEsales\Eshop\Core\Registry;

/**
 * Class Order
 */
class Order extends Order_parent
{

    public function getTabslDhlEvents()
    {
        $infos = [];
        $data = $this->oxorder__tabsldhltracking_info->rawValue;
        if (!empty($data)) {
            $data = json_decode($data, true);
            foreach ($data['shipments'][0]['events'] as $event) {
                $infos[] = [
                    'date' => date_format(date_create($event['timestamp']), 'd.m.Y H:i'),
                    'status' => $event['description'],
                ];
            }
        }
        return $infos;
    }

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
     * @return bool
     */
    public function updateTabslDhl()
    {

        $trackingNumber = $this->oxorder__oxtrackcode->value;
        if (!$trackingNumber) {
            return false;
        }

        $apiKey = $this->getConfig()->getConfigParam('tabsldhltracking_api_key');
        $apiUrl = $this->getConfig()->getConfigParam('tabsldhltracking_api_url');
        if (!$apiKey || !$apiUrl) {
            return false;
        }

        $deliveryZip = $this->oxorder__oxbillzip->value;
        $deliveryCountry = $this->oxorder__oxbillcountry->value;
        $deliveryCountry = 'DE';
        $senderCountry = 'DE';

        $url = sprintf(
            '%s?trackingNumber=%s&recipientPostalCode=%s&language=de&requesterCountryCode=%s&originCountryCode=%s',
            $apiUrl,
            $trackingNumber,
            $deliveryZip,
            $deliveryCountry,
            $senderCountry
        );

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

        if (!$response) {
            return false;
        }

        $jsonData = json_decode($response, true);

        #print_r($jsonData);

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
