<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Core;

use Pug\Pug;

class PageMaker{

    public function __construct(string $path = "", array $data = [], array $options = [])
    {

        $path = explode("/", $path);
        $file = $path[count($path) - 1];
        $path = join("/", array_slice($path, 0, count($path) - 1));

        $defaultData = [
            "pageTitle" => App::env("appName", "Titulo")
        ];

        $defaultOptions = [
            "cache"   => "../src/views/cache/" . $path . "/" . $file,
            "basedir" => "../src/views/tpl/"   . $path . "/",
        ];

        $data = array_merge($defaultData, $data);
        $options = array_merge($defaultOptions, $options);

        $pug = new Pug($options);
        $pug->displayFile((strpos($file, ".pug") ? $file : $file . ".pug"), $data);

    }

}