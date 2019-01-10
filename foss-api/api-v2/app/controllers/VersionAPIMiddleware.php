<?php

class VersionAPIMiddleware {  
  /**
   * Example middleware invokable class
   *
   * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
   * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
   * @param  callable                                 $next     Next middleware
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function __invoke($request, $response, $next) {
    $is_deprecated = constant('is_deprecated');
    if($is_deprecated)
    {
      $deprecated = array(
          'force-update' => true
      );
      echo json_encode($deprecated);
      exit;
    }
    
    $response = $next($request, $response);
    return $response;
  }
}
