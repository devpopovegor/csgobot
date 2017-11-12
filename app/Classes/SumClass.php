<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 12.11.2017
 * Time: 19:22
 */

namespace App\Classes;


class SumClass
{
    public $name;
    public $inspect_link;
    public $float;
    public $cost;

    public function __construct($name = null, $inspect_link = null, $float = null, $cost = null)
    {
        $this->name = $name;
        $this->inspect_link = $inspect_link;
        $this->float = $float;
        $this->cost = $cost;
    }
}