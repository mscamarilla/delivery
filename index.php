<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set('Europe/Kiev');

require('vendor/autoload.php');

use Entity\Item;
use Service\Form;
use APINameSpace\APINovaPoshta;
use APINameSpace\APIAutolux;

require "config.php";

if (isset($_GET['np'])) {
    $delivery = new APINovaPoshta($api_key_np);
} elseif (isset($_GET['al'])) {
    $delivery = new APIAutolux($login_al, $password_al, $login_url_al, $shipment_url_al);
} else {
    die('empty request');
}
//создаем товары
$items = array(
    new Item('item1', 1, 101),
    new Item('item2', 2, 102),
    new Item('item3', 3, 103),
    new Item('item4', 4, 104),
    new Item('item5', 5, 105)
);


$form = new Form($items, $delivery);

/*темплейт*/
print_r($form->index());
