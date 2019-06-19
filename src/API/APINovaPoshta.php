<?php

namespace APINameSpace;

use Service\Delivery;

/**
 * Class APINovaPoshta
 * @package APINameSpace
 */
class APINovaPoshta extends Delivery
{
    private $api_key;

    /**
     * APINovaPoshta constructor.
     * @param $api_key
     */
    public function __construct($api_key)
    {
        $this->api_key = $api_key;
        $this->getCities();
    }

    /**
     * Get array of all cities from API
     */
    public function getCities()
    {
        $cities = array();
        $cities_data = array();
        $get_cities = curl_init();
        curl_setopt($get_cities, CURLOPT_URL, 'https://api.novaposhta.ua/v2.0/json/');

        curl_setopt($get_cities, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($get_cities, CURLOPT_HTTPHEADER, Array("Content-Type: text/plain"));
        curl_setopt($get_cities, CURLOPT_HEADER, 0);
        curl_setopt($get_cities, CURLOPT_POSTFIELDS, '{"modelName": "Address","calledMethod": "getCities","methodProperties": {"": ""},"apiKey":"' . $this->api_key . '"}');
        curl_setopt($get_cities, CURLOPT_POST, 1);
        curl_setopt($get_cities, CURLOPT_SSL_VERIFYPEER, 0);
        $cities = json_decode(curl_exec($get_cities), TRUE);
        curl_close($get_cities);

        if (!$cities) {
            $cities['data'][0] = array('Ref' => 'db5c88f5-391c-11dd-90d9-001a92567626', 'Description' => 'Львів', 'DescriptionRu' => 'Львов');
        }
        foreach ($cities['data'] as $city) {
            $cities_data[] = array(
                'id' => $city['Ref'],
                'name' => $city['DescriptionRu']
            );
        }
        $this->setAllCities($cities_data);
    }

    /**
     * Calculate delivery price
     * @return bool
     */
    public function getCostFromApi()
    {
        $get_cost = curl_init();
        curl_setopt($get_cost, CURLOPT_URL, 'https://api.novaposhta.ua/v2.0/json/');
        curl_setopt($get_cost, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($get_cost, CURLOPT_HTTPHEADER, Array("Content-Type: text/plain"));
        curl_setopt($get_cost, CURLOPT_HEADER, 0);
        curl_setopt($get_cost, CURLOPT_POSTFIELDS, '{"apiKey": "' . $this->api_key . '","modelName": "InternetDocument","calledMethod": "getDocumentPrice","methodProperties": {"DateTime": "' . date("d.m.Y") . '","ServiceType": "' . $this->getServiceType() . '","Weight": "' . $this->getWeight() . '","Cost": "' . $this->getCost() . '","CitySender": "' . $this->getCityFrom() . '","CityRecipient": "' . $this->getCityTo() . '"}}');
        curl_setopt($get_cost, CURLOPT_POST, 1);
        curl_setopt($get_cost, CURLOPT_SSL_VERIFYPEER, 0);
        $cost = json_decode(curl_exec($get_cost), TRUE);
        curl_close($get_cost);
        if ($cost['success'] == 1 && count($cost['data']) == 1) {
            $this->setPrice($cost['data'][0]['Cost'] . ' UAH');
        } else {
            return FALSE;
        }
    }

}
