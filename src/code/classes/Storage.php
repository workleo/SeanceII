<?php
namespace Code\Classes;


class Storage
{
    protected $json;

    function get($key)
    {

        $value = $this->json[$key];
        if ($value === null) return $value;

        if (preg_match("/(a|O|s|b)\:[0-9]*?((\:((\{?(.+)\})|(\"(.+)\"\;)))|(\;))/", $value) == 1) {
            return unserialize($value);
        } else {
            return $value;
        }
    }

    function set($key, $value)
    {
        if ($value === null) {
            $value = '';
        }

        if (is_object($value) || is_array($value)) {
            $this->json[$key] = serialize($value);
        } else {
            $this->json[$key] = $value;
        }
    }

    function remove($key)
    {

        foreach ($this->json as $id => $row) {
            if ($id === $key) {
                unset($this->json[$id]);
                break;
            }
        }
    }

}