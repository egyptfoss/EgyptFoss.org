<?php
/**
 * @SWG\Info(title="EgyptFOSS API", version="0.1")
 */
require 'settings.php';
require 'messages.php';
require 'routes.php';
require 'secure_routes.php';

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: X-Requested-With');
  header("HTTP/1.1 200 OK");
  die();
}

// API Version Middleware
$app->add(new VersionAPIMiddleware($versions));

// Authentication Middleware
$app->add(new AuthenticationAPIMiddleware());

foreach ($routes as $key => $callback) {
    $urlDetails = explode(":", $key);
    $url = $urlDetails[1];
    $requestMethod = strtolower($urlDetails[0]);
    
    $functionPath = explode("/", $callback);
    $className = $functionPath[0];
    $functionName = $functionPath[1];
    
    $app->$requestMethod($url, function($request, $response, $args) use ($secure_routes, $key, $className, $functionName) {
        if(array_key_exists($key, $secure_routes))
        {
          $result = authenticateRoutes($request,$response,$secure_routes[$key]);
          if(!$result["authenticated"])
          {
            return renderJson($response, $result["status"], $result["message"]);
          }
        } 
        $object = new $className();
        return $object->$functionName($request, $response, $args);
    });   
}

$app->run();
