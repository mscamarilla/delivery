<?php

namespace Service;

use InterfaceNameSpace\DeliveryInterface;

/**
 * Class Form
 * @package Service
 */
class Form
{
    private $items;
    private $cost = 0;
    private $weight = 0;
    private $delivery;

    /**
     * Form constructor.
     * @param $items
     * @param DeliveryInterface $delivery
     */
    public function __construct($items, DeliveryInterface $delivery)
    {
        $this->items = $items;
        foreach ($items as $item) {
            $this->cost += $item->cost;
            $this->weight += $item->weight;
        }
        $this->delivery = $delivery;
    }


    /**
     * Returns all methods in this file
     * @return string
     */
    public function index()
    {
        $output = $this->addStyle();
        $output .= $this->getItemsTable();
        $output .= $this->getFormCalc();

        return $output;

    }

    /**
     * Styles for form
     * @return string
     */
    private function addStyle()
    {
        $html = '<style>
                    .left, .right {width:40%; float:left}
                    .left {margin-right: 10%}
                    table{width:100%}
                    form{width:100%;margin:0 auto;background:#eee;padding:15px;border:1px solid #ddd;}
                    input[type="text"],select,input[type="password"]{width:100%;height:34px;padding:5px;display:inline-block;margin-bottom:15px;margin-top:5px;border:1px solid #ddd;}
                    input[type="text"]:focus{border:1px solid #fff;outline:none;}
                    input[type="submit"] {margin:0 auto;background:#000;border:1px solid;color:#fff;padding:15px;border-radius:10px;cursor:pointer;display:inherit;}
                    input[type="submit"]:hover{background:#ddd;color:#000;}
                    .clear {clear: both}
                    h4{margin: 5px 0;}
            </style>';
        return $html;
    }

    /**
     * Items table
     * @return string
     */
    private function getItemsTable()
    {
        //выводим товары а-ля корзина
        $html = '<div class="left">';
        $html .= '<h1>Items</h1>';
        $html .= '<table border="1">';
        $html .= '<thead>';
        $html .= '<tr><td><b>Name</b></td><td><b>Weight</b></td><td><b>Cost</b></td></tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($this->items as $item) {
            $html .= '<tr>';
            $html .= '<td>' . $item->name . '</td>';
            $html .= '<td>' . $item->weight . '</td>';
            $html .= '<td>' . $item->cost . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Calc form
     * @return string
     */
    private function getFormCalc()
    {
        $html = '<div class="right">';
        $html .= '<h1>Calc</h1>';
        $html .= '<form name="calc" method="post">';
        $html .= '<h4>Город отправления</h4>';
        $html .= '<select name="city_from">';

        foreach ($this->delivery->getAllCities() as $city) {
            if (!empty($city['offices'])) {
                foreach ($city['offices'] as $office) {
                    if (!empty($office['address_ru'])) {
                        if (!empty($_POST['city_from']) && $_POST['city_from'] == $office['id']) {
                            $html .= '<option value="' . $office['id'] . '" selected="selected">';
                        } else {
                            $html .= '<option value="' . $office['id'] . '">';
                        }
                        $html .= $city['name'] . ' ' . $office['address_ru'];
                        $html .= '</option>';
                    }
                }
            } else {

                if (!empty($_POST['city_from']) && $_POST['city_from'] == $city['id']) {
                    $html .= '<option value="' . $city['id'] . '" selected="selected">';
                } else {
                    $html .= '<option value="' . $city['id'] . '">';
                }
                $html .= $city['name'];
                $html .= '</option>';
            }
        }
        $html .= '</select>';

        $html .= '<h4>Город получения</h4>';
        $html .= '<select name="city_to">';

        foreach ($this->delivery->getAllCities() as $city) {
            if (!empty($city['offices'])) {
                foreach ($city['offices'] as $office) {
                    if (!empty($office['address_ru'])) {
                        if (!empty($_POST['city_to']) && $_POST['city_to'] == $office['id']) {
                            $html .= '<option value="' . $office['id'] . '" selected="selected">';
                        } else {
                            $html .= '<option value="' . $office['id'] . '">';
                        }
                        $html .= $city['name'] . ' ' . $office['address_ru'];
                        $html .= '</option>';
                    }
                }
            } else {

                if (!empty($_POST['city_to']) && $_POST['city_to'] == $city['id']) {
                    $html .= '<option value="' . $city['id'] . '" selected="selected">';
                } else {
                    $html .= '<option value="' . $city['id'] . '">';
                }
                $html .= $city['name'];
                $html .= '</option>';
            }
        }
        $html .= '</select>';

        $html .= '<h4>Тип доставки</h4>';
        $html .= '<select name="service_type">';
        $html .= '<option value="DoorsDoors"';
        if (!empty($_POST['service_type']) && $_POST['service_type'] == 'DoorsDoors') {
            $html .= 'selected="selected"';
        }
        $html .= '>Адрес-Адрес</option>';
        $html .= '<option value="DoorsWarehouse"';
        if (!empty($_POST['service_type']) && $_POST['service_type'] == 'DoorsWarehouse') {
            $html .= 'selected="selected"';
        }
        $html .= '>Адрес-Отделение</option>';
        $html .= '<option value="WarehouseWarehouse"';
        if (!empty($_POST['service_type']) && $_POST['service_type'] == 'WarehouseWarehouse') {
            $html .= 'selected="selected"';
        }
        $html .= '>Отделение-Отделение</option>';
        $html .= '<option value="WarehouseDoors"';
        if (!empty($_POST['service_type']) && $_POST['service_type'] == 'WarehouseDoors') {
            $html .= 'selected="selected"';
        }
        $html .= '>Отделение-Адрес</option>';
        $html .= '</select>';
        $html .= '<h4>Сумма товаров:</h4>';
        $html .= '<input type="text" name="cost" value="' . $this->cost . '"  readonly="readonly">';
        $html .= '<h4>Вес товаров:</h4>';
        $html .= '<input type="text" name="weight" value="' . $this->weight . '"  readonly="readonly">';


        $html .= '<input type="submit">';
        if (!empty($_POST)) {
            $this->delivery->setCityFrom($_POST['city_from']);
            $this->delivery->setCityTo($_POST['city_to']);
            $this->delivery->setServiceType($_POST['service_type']);
            $this->delivery->setCost($this->cost);
            $this->delivery->setWeight($this->weight);
            $this->delivery->getCostFromApi();

            $html .= '<h4>Стоимость доставки: ' . $this->delivery->getPrice() . '</h4>';
        }
        $html .= '</form>';
        $html .= '</div>';
        $html .= '<div class="clear"></div>';

        return $html;
    }
}
