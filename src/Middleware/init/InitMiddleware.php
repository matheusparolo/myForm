<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Middleware\init;

class InitMiddleware{

    public function __invoke($request, $response, $next)
    {

        // Initials
        $initials = [
            new MaintenanceMode(),
            new DefineDataType(),
        ];

        foreach($initials as $initial)
            $initial($request, $response, $next);

        // Continue
        $response = $next($request, $response);
        return $response;

    }

}