<?php

class wp_helper {
  
  static function wp_generate_password($length = 12, $special_chars = true, $extra_special_chars = false) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    if ($special_chars)
      $chars .= '!@#$%^&*()';
    if ($extra_special_chars)
      $chars .= '-_ []{}<>~`+=,.;:/?|';

    $password = '';
    for ($i = 0; $i < $length; $i++) {
      $password .= substr($chars, self::wp_rand(0, strlen($chars) - 1), 1);
    }
    return $password;
  }

  static function wp_rand($min = 0, $max = 0) {
    $rnd_value = "";

    // Reset $rnd_value after 14 uses
    // 32(md5) + 40(sha1) + 40(sha1) / 8 = 14 random numbers from $rnd_value
    $seed = Option::limit(1)->Where('option_name', '=', "_transient_random_seed")->first();
    $seed = $seed->option_value;
    $rnd_value = md5(uniqid(microtime() . mt_rand(), true) . $seed);
    $rnd_value .= sha1($rnd_value);
    $rnd_value .= sha1($rnd_value . $seed);
    $seed = md5($seed . $rnd_value);
    // Take the first 8 digits for our value
    $value = substr($rnd_value, 0, 8);

    // Strip the first eight, leaving the remainder for the next call to wp_rand().
    $rnd_value = substr($rnd_value, 8);

    $value = abs(hexdec($value));

    // Some misconfigured 32bit environments (Entropy PHP, for example) truncate integers larger than PHP_INT_MAX to PHP_INT_MAX rather than overflowing them to floats.
    $max_random_number = 3000000000 === 2147483647 ? (float) "4294967295" : 4294967295; // 4294967295 = 0xffffffff
    // Reduce the value to be within the min - max range
    if ($max != 0)
      $value = $min + ( $max - $min + 1 ) * $value / ( $max_random_number + 1 );

    return abs(intval($value));
  }
  
  static function wp_hash($data, $scheme = 'auth') {
    $salt = self::wp_salt($scheme);
    return hash_hmac('md5', $data, $salt);
  }
  
  static function wp_salt($scheme = 'auth') {
    define('AUTH_KEY',         't[|rd-m[]X=1x~&Db?m^D4FHN7^GCFN9VN7!0#TZ8b)1B%[zp(LJc:M9@(n@ O0Y');
    define('SECURE_AUTH_KEY',  '}L)b%.xt-,:zxwhc|-q|zgl^M]yni>O_Y.4f-ym;u6[mo.x4|]+ll!Ko!>>4?31a');
    define('LOGGED_IN_KEY',    'z3lL]Kg}sfbR$:KHG5,#pm@k47u|v{M|vDKa^Zz1B!(~u|~Bm9o7snm>O,H`@,;B');
    define('NONCE_KEY',        '<p&.KZCn+O=!f=R($0O*wG-YA@t+F&|9Pr#q:OX&^&a0-NteQ(_4vF-X;:Yj=X*K');
    define('AUTH_SALT',        'sZ$pe81><z0LPyOz]G}scE/MVv5BZ,9nD;hK?z>wS~%%47_la*NvE[zNrsnt/ju2');
    define('SECURE_AUTH_SALT', 'bvV*=wcf(08-Q6K_Q+HCl!xwu3p;cLJt:OoTl?`d4+`uk|Wsbg54Kl||-x#YyJ{R');
    define('LOGGED_IN_SALT',   '!Aojv1+{H.S24YK6`i#oc*L9a,I^0va`D.K[gYpLF|Nw-~v(Vg4I|yKZ-7$wxw+C');
    define('NONCE_SALT',       '0D^PV!nCNP@l|WZrOgVikJ h|a|y#7~|m1fo^u3].kxf{fZqd|2=n/0*3eR[{cR>');

    $values = array(
      'key' => '',
      'salt' => ''
    );

    if (in_array($scheme, array('auth', 'secure_auth', 'logged_in', 'nonce'))) {
      foreach (array('key', 'salt') as $type) {
        $const = strtoupper("{$scheme}_{$type}");
        if (defined($const) && constant($const)) {
          $values[$type] = constant($const);
        }
      }
    }
   // $values['salt'] = preg_replace("/\<+.+\>/", '', $values['salt']);
    
    return $values['key'] . $values['salt'];
  }
  
  public static function wp_unique_post_slug($post_name) {
    $newPostName = $post_name;
    $i = 1;
    do{
        $posts = Post::where("post_name","=",$newPostName)->get();
        if(count($posts) > 0)
        {
          $count = count($posts) + $i;
          $newPostName = $post_name . "-{$count}"; 
          $i = $i+1;
        }
    }while(count($posts) > 0);
    
    return $newPostName;
  }
}
