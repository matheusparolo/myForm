<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Core;

use \TCC\Middleware\init\InitMiddleware;

class App extends \Slim\App{

    private $routeFiles;

    public function __construct()
    {

        session_start();

        $this->load_env();

        parent::__construct(['settings' => [
            'displayErrorDetails' => self::env("slimDebug", false)
        ]]);

        $this->load_routes();

    }


    private function load_routes():void
    {

        $routesPath = $_SERVER["DOCUMENT_ROOT"] . "/../src/routes/";

        $files = scandir($routesPath);
        $files = array_slice($files, "2", count($files));

        foreach($files as &$file)
            $file = $routesPath . $file;

        $this->routeFiles = $files;

        $this->group("", function(){

            foreach($this->routeFiles as $file)
                require_once $file;

        })->add(new InitMiddleware());

    }

    private function load_env():void
    {

        $envSets = file($_SERVER["DOCUMENT_ROOT"] . "/../.env");
        $envSets = array_filter(array_map('trim', $envSets));
        if($envSets)
            foreach ($envSets as $envSet)
                putenv(trim($envSet));

    }


    public static function add_env(string $name, string $val):void
    {

        putenv(trim($name."=".$val));

    }

    public static function env(string $var, string $default = null)
    {

        $env = getenv($var);

        switch ($env){

            case null:    return $default;
            case "true":  return true;
            case "false": return false;
            default:      return $env;

        }

    }


    public static function code_json_response(string $code):void
    {

        self::json_response(["code" => $code]);

    }

    public static function json_response(array $toJson):void
    {

        exit(json_encode($toJson));

    }


    public static function location_replace(string $location):void
    {

        header("location: " . $location);
        exit;

    }

    public static function response_type(array $callBacks):void
    {

        $dataType = App::env("dataType");
        if($callBacks[$dataType] != null)
        {
            $callBacks[$dataType]();

        }else{

            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
            exit;

        }

    }

    public static function rmdir($src, $recursive = false):void
    {

        if(is_dir($src)){

            if($recursive){


                $dir = scandir($src);
                $dir = array_slice($dir, "2", count($dir));
                foreach ($dir as $route) {

                    $route = $src . "/" . $route;

                    if (is_dir($route)) {

                        self::rmdir($route, true);

                    } else {

                        unlink($route);

                    }


                }

            }

            rmdir($src);


        }

    }

}