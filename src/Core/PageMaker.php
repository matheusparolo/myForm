<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Core;

use Rain\Tpl;

class PageMaker{

    private $tpl;
    private $options;
    private $defaults = [
        "header" => "templates/header",
        "footer" => "templates/footer",
        "data" => []
    ];

    public function __construct(String $path = "", array $views = [], array $options = [])
    {

        if($path[strlen($path) - 1]) $path .= "/";

        $this->options = array_merge($this->defaults, $options);

        $header = $this->options["header"];
        $footer = $this->options["footer"];


        Tpl::configure([
            "tpl_dir" => "../src/views/tpl/",
            "cache_dir" => "../src/views/cache/" . $path . "/",
            "debug" => App::env("rainTPLDebug", false)
        ]);

        $this->tpl = new Tpl;

        if($header) $this->setTpl($header, $this->options["data"]);

        foreach($views as $view => $data)
        {
            if(gettype($view) == "integer"){
                $view = $data;
                $data = [];
            }
            $this->setTpl($path . $view, $data);
        }

        if($footer) $this->setTpl($footer, $this->options["data"]);

    }

    public function setTpl(String $name, array $data = [], $return = false):void
    {

        $this->setData($data);
        $this->tpl->draw($name, $return);

    }

    private function setData(array $data = []):array
    {

        foreach($data as $key => $value){

            $this->tpl->assign($key, $value);

        }
        return $data;

    }

}