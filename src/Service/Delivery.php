<?php

namespace Service;

use InterfaceNameSpace\DeliveryInterface;

abstract class Delivery implements DeliveryInterface
{
    private $city_to;
    private $city_from;
    private $all_cities;
    private $cost;
    private $weight;
    private $price;
    private $service_type;


    public function setAllCities($all_cities)
    {
        $this->all_cities = $all_cities;
    }

    public function setCityTo($city_to)
    {
        $this->city_to = $city_to;
    }

    public function setCityFrom($city_from)
    {
        $this->city_from = $city_from;
    }

    public function getCityTo()
    {
        return $this->city_to;
    }

    public function getCityFrom()
    {
        return $this->city_from;
    }

    public function getAllCities()
    {
        return $this->all_cities;
    }

    public function getCost()
    {
        return $this->cost;
    }

    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function setServiceType($service_type)
    {
        $this->service_type = $service_type;
    }

    public function getServiceType()
    {
        return $this->service_type;
    }

}
