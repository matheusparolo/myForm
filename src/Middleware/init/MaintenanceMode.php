<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Middleware\init;

use TCC\Core\App;

class MaintenanceMode{

    public function __invoke($request, $response, $next)
    {

        if(App::env("maintenanceMode", "false"))
            return $response->withStatus(503);

    }

}