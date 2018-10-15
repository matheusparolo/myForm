<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Models\Entities;

class GenericEntity{

    private $data = [];
    protected $settersBlocked = [];

    public function __construct(array $data = [], array $settersBlocked = [])
    {

        $this->setData($data);
        $this->settersBlocked = $settersBlocked;

    }

    public function __call(string $name, array $args)
    {

        $method = substr($name, 0, 3);
        $fieldName = self::clear_string(substr($name, 3, strlen($name)));

        switch ($method){
            case "get":
                return $this->data[$fieldName];
                break;

            case "set":

                if(array_search($fieldName, $this->settersBlocked) !== false){
                    throw new \Error("Cannot set blocked property \$$fieldName");
                }
                $this->data[$fieldName] = $args[0];

                break;

        }

    }

    public function setData(array $data = []):void
    {

        $data = array_filter($data, function($var){
            return $var == "0" ? true : (self::clear_string($var));
        });

        foreach($data as $key => $value){

            $this->{"set" . $key}($value);

        }

    }

    public function getData():array
    {

        return $this->data;

    }

    public function isEmpty():bool
    {
        return $this->data == null ? true : false;
    }

    private static function clear_string(string $str):string
    {

        return str_replace(" ", "", str_replace("_", "", strtolower($str)));

    }

}