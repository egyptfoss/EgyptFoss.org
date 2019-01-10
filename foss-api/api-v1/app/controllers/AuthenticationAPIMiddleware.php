<?php

class AuthenticationAPIMiddleware {

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
    
    if(sizeof($request->getHeader('x-api-key')) == 0)
    {
      header('HTTP/1.0 403 Forbidden');
      echo 'You are forbidden!';
      exit;
    }
    
    $keyToCheck = $request->getHeader('x-api-key')[0];
    $apikey = new ApiKey();
    
    // check api key is exist and enabled
    $valid_key = $apikey->where(  'api_key', '=', $keyToCheck  )
                        ->where( 'is_enabled', '=', 1 )
                        ->first();
                      
    if ( !empty( $valid_key ) ) {
      $authorized = true;
    } else {
      $authorized = false;
    }

    if (!$authorized) { //key is false
      header('HTTP/1.0 403 Forbidden');
      echo 'You are forbidden!';
      exit;
    }
    
    function ef_strip_tags(&$input) {
      $input = htmlspecialchars($input, ENT_QUOTES);
      return $input;
    }
    
    // xss attacking
    array_walk_recursive($_GET, 'ef_strip_tags');
    // skip content with html tags for now
    if (isset($_POST['content'])) {
      $content = $_POST['content'];
      array_walk_recursive($_POST, 'ef_strip_tags');
      $_POST['content'] = $content;
    } else {
      array_walk_recursive($_POST, 'ef_strip_tags');
    }

    $response = $next($request, $response);
    return $response;
  }
}
