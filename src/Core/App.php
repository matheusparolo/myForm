<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Core;

class App extends \Slim\App{

    public function __construct()
    {

        session_start();

        $this->load_env();

        parent::__construct(['settings' => ['displayErrorDetails' => self::env("slimDebug", false)]]);

        $this->load_routes();

    }

    private function load_routes():void
    {

        $routesPath = $_SERVER["DOCUMENT_ROOT"] . "/../src/routes/";
        $files = scandir($routesPath);
        $files = array_slice($files, "2", count($files));
        foreach($files as $file){

            require_once $routesPath . $file;

        }

    }

    private function load_env():void
    {

        $envSets = file($_SERVER["DOCUMENT_ROOT"] . "/../.env");
        $envSets = array_filter(array_map('trim',$envSets));
        if($envSets){
            foreach($envSets as $envSet){
                putenv(trim($envSet));
            }
        }

    }

    public static function env(string $var, string $default = null):string
    {

        $env = getenv($var);

        $env = ((string)$env == "true") ? true : $env;
        $env = ((string)$env == "false") ? false : $env;

        return $env ? $env : $default;

    }

    public static function action_response(string $code):void
    {

        self::json_response(["code" => $code]);

    }

    public static function json_response(array $toJson):void
    {

        exit(json_encode($toJson));

    }

}