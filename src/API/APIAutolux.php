<?php

namespace APINameSpace;


use Service\Delivery;

/**
 * Class APIAutolux
 * @package APINameSpace
 */
class APIAutolux extends Delivery
{

    private $login;
    private $password;
    private $login_url;
    private $access_token;
    private $shipment_url;


    /**
     * APIAutolux constructor.
     * @param $login
     * @param $password
     * @param $login_url
     * @param $shipment_url
     */
    public function __construct($login, $password, $login_url, $shipment_url)
    {

        $this->login = $login;
        $this->password = $password;
        $this->login_url = $login_url;
        $this->shipment_url = $shipment_url;
        $this->getToken();
        $this->getCities();
    }


    /**
     * Get API token
     */
    private function getToken()
    {
        $data = ['email' => $this->login, 'password' => $this->password];

        $get_token = curl_init();
        curl_setopt($get_token, CURLOPT_URL, $this->login_url);
        curl_setopt($get_token, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($get_token, CURLOPT_HTTPHEADER, Array("Content-Type: application/x-www-form-urlencoded"));
        curl_setopt($get_token, CURLOPT_HEADER, 0);
        curl_setopt($get_token, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($get_token, CURLOPT_POST, 1);
        curl_setopt($get_token, CURLOPT_SSL_VERIFYPEER, 0);

        $token = json_decode(curl_exec($get_token), TRUE);

        curl_close($get_token);
        if (!$token['access_token']) {
            echo 'error';
            die();
        }
        $this->access_token = $token['access_token'];

    }

    /**
     * Get array of all cities from API
     */
    public function getCities()
    {
        $cities_data = array();
        $get_cities = curl_init();
        curl_setopt($get_cities, CURLOPT_URL, 'https://api.autolux-post.com.ua/office/offices_by_territorial_units/?access_token=' . $this->access_token);
        curl_setopt($get_cities, CURLOPT_RETURNTRANSFER, true);
        $cities = json_decode(curl_exec($get_cities), TRUE);
        curl_close($get_cities);

        foreach ($cities as $city) {
            $cities_data[] = array(
                'id' => $city['id'],
                'name' => $city['name_ru'],
                'offices' => $city['offices']
            );
        }

        $this->setAllCities($cities_data);
    }

    /**
     * Calculate delivery price
     */
    public function getCostFromApi()
    {

        $sender = $this->getSender();

        $data = array(
            'box_quantity' => 1,
            'volume' => 0,
            'weight' => $this->getWeight(),
            'shipment_type_id' => 1,
            'receivers' => array(
                'persons' => array(array(
                    'phone' => '0505867374',
                    'first_name' => "Татьяна",
                    'last_name' => "Драгомирецкая"))
            ),
            'insurance' => 300,
            'use_discount' => true,
            'servicce_11' => 330,
            'door_to_door' => array('delivery_address' => $this->getCityTo(), 'take_address' => ''),
            'description' => 'взуття',
            'senders' => array('person_id' => $sender['person']['id'], 'company_id' => $sender['office']['company_id']),
            'office_from_id' => $this->getCityFrom(),
            'office_to_id' => $this->getCityTo(),
            'access_token' => $this->access_token

        );

        $data = json_encode($data);


        $get_cost = curl_init();
        curl_setopt($get_cost, CURLOPT_URL, $this->shipment_url);
        curl_setopt($get_cost, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($get_cost, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
        curl_setopt($get_cost, CURLOPT_HEADER, 0);
        curl_setopt($get_cost, CURLOPT_POSTFIELDS, $data);
        curl_setopt($get_cost, CURLOPT_POST, 1);
        curl_setopt($get_cost, CURLOPT_SSL_VERIFYPEER, 0);
        $cost = json_decode(curl_exec($get_cost), TRUE);
        curl_close($get_cost);

        $total = 0;
        foreach ($cost['invoices'] as $invoice) {
            $total += $invoice['sum_total'];
        }

        $this->setPrice($total . ' UAH');

    }

    /**
     * Get Sender information by token
     * @return mixed
     */
    private function getSender()
    {

        $get_sender = curl_init();
        curl_setopt($get_sender, CURLOPT_URL, 'https://api.autolux-post.com.ua/authentication/check_access_token/?access_token=' . $this->access_token);
        curl_setopt($get_sender, CURLOPT_RETURNTRANSFER, true);
        $sender_data = json_decode(curl_exec($get_sender), TRUE);
        curl_close($get_sender);

        return $sender_data;
    }


}
