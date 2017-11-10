<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 30.10.2017
 * Time: 21:48
 */

namespace App\Classes;

class NeedItem
{
    public $name;
    public $full_name;
    public $url;
    public $chat_id;
    public $phase;
    public $float;
    public $pattern;

    public function __construct($name, $url, $chat_id, $phase, $float, $pattern)
    {
        $this->name = $name;
        $this->full_name = $name;
        if ($phase) {
            if (strpos($this->name, '(') !== false) {
                $parts_name = explode('(', $this->name);
                $this->full_name = $parts_name[0];
                $this->full_name .= $phase . ' (';
                $this->full_name .= $parts_name[1];
            } else {
                $this->full_name .= " {$this->phase}";
            }
        }
        $this->url = $url;
        $this->chat_id = $chat_id;
        $this->phase = $phase;
        $this->float = $float;
        $this->pattern = $pattern;
    }
}