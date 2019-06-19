<?php

namespace Entity;

/**
 * Class Item
 * @package Entity
 */
class Item
{
    public $name;
    public $weight;
    public $cost;

    /**
     * Item constructor.
     * @param $name
     * @param $weight
     * @param $cost
     */
    public function __construct($name, $weight, $cost)
    {
        $this->name = $name;
        $this->weight = $weight;
        $this->cost = $cost;
    }


}
