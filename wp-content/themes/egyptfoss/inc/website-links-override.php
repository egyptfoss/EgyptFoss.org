<?php

if (!is_admin()) {

  function wplogin_filter($url, $path, $orig_scheme) {
    if (preg_match("/(wp-login\.php)/", $url)) {
      return getCustomizedLoginPage($url);
    }
    return $url;
  }
  add_filter('site_url', 'wplogin_filter', 10, 3);

// to redirect anyone trying to access wp-login.php to our template-login
  function goto_login_page() {
    $page = basename($_SERVER['REQUEST_URI']);
    if (preg_match("/(wp-login\.php)/", $page)) {
      $newUrl = getCustomizedLoginPage($page);
      wp_redirect(home_url($newUrl));
      exit;
    }
  }
  add_action('init', 'goto_login_page');

  function remove_wp_login_from_wsl($authentication_url) {
    $pages = get_pages(array(
      'meta_key' => '_wp_page_template',
      'meta_value' => 'template-login.php',
      'post_status' => 'publish'
    ));
    foreach ($pages as $login_page) {
      $post_in_langs = wp_get_object_terms($login_page->ID, 'post_translations');
      if ($post_in_langs) {
        $post_in_langs = unserialize($post_in_langs[0]->description);
        $current_lang = pll_current_language();
        if ($current_lang) {
          global $pages_to_generate_rewrite_rules;
          $page_name = get_post_field('post_name', $post_in_langs["en"]);
          $urlPage = preg_replace("/\/\?/","", $pages_to_generate_rewrite_rules[$page_name]);
          $new = "/" . $current_lang . "\/" . $urlPage . "\//";
          return preg_replace($new, "", $authentication_url, 1);
        }
      }
    }
  }
  add_filter('wsl_render_auth_widget_alter_authenticate_url', 'remove_wp_login_from_wsl');

  function rewriteLinks($url, $slug) {

    if (!is_admin() && is_singular()) {
      global $ef_translation_archive_urls;
      $post_types = $ef_translation_archive_urls;
      unset($post_types['events']);
      $key = array_search(get_post_type(),$post_types);
      if ($key) {
        $url = str_replace("/en/{$key}/", "/" . $slug . "/{$key}/", $url);
        $url = str_replace("/ar/{$key}/", "/" . $slug . "/{$key}/", $url);
        if (empty($url)) {
          $request_uri = $_SERVER['REQUEST_URI'];
          $request_uri = str_replace("/ar/{$key}/", "", $request_uri);
          $request_uri = str_replace("/en/{$key}/", "", $request_uri);
          $slug = (pll_current_language() == "en") ? "ar" : "en";
          $url = home_url($slug . "/{$key}/" . $request_uri);
        }
      }
    }

    global $page_overrided_links;
    $url_name = parse_url($url)['path'];
    foreach ($page_overrided_links as $key => $page_name) {
      $matched = preg_match('/' . $page_name['ar'] . '/', $url_name, $matched);
      if ($matched) {
        return site_url() . "/" . $slug . "/" . $key;
      }
    }
    return $url;
  }
  add_filter('pll_translation_url', 'rewriteLinks', 10, 2);

  function rewriteLinksSite($url, $path) {
    global $login_page_names;
    $url_name = (isset(parse_url($url)['path']))?parse_url($url)['path']:"";
    foreach ($login_page_names as $key => $page_name) {
      $matched = preg_match('/' . $page_name['ar'] . '/', $url_name, $matched);
      if ($matched) {
        return site_url() . "/" . pll_current_language() . "/" . $key;
      }
    }
    return $url;
  }
  add_filter('site_url', 'rewriteLinksSite', 999, 2);

  function bpml_bp_uri_filter($url) {
    $regex = ('/[\/ar\/|\/en\/]+(.*?)\//');
    $restOfUrl = preg_replace($regex, '', $url, 1);
    preg_match($regex, $url, $match);
    $url = $match[0];
    $postid = url_to_postid($url);
    if(preg_match("/register/", $url))
    {
      $postid = url_to_postid('register');
    }
    if ($postid) {
      $post_in_langs = wp_get_object_terms($postid, 'post_translations');
      if ($post_in_langs) {
        $post_in_langs = unserialize($post_in_langs[0]->description);
        $url = get_post_field('post_name', $post_in_langs["en"]);
        return $url . "/" . $restOfUrl;
      }
    }
    return $url;
  }
  add_filter('bp_uri', 'bpml_bp_uri_filter');

  // this redirect process for buddypress links because it's not familiar with translations
  function translateBpLinks($url, $slug) {
      $regex = ('/[\/ar\/|\/en\/]+(.*?)\//');
      global $wpdb;
      $members_en = $wpdb->get_row("SELECT ID,post_title,post_name FROM $wpdb->posts WHERE post_name = '" . "members" . "'");
      $post_in_langs = wp_get_object_terms($members_en->ID, 'post_translations');
      $post_in_langs = unserialize($post_in_langs[0]->description);
      $members_ar = $wpdb->get_row("SELECT ID,post_title,post_name FROM $wpdb->posts WHERE ID = '" . $post_in_langs["ar"] . "'");
      $bp_pages = array(
        "members" => array("en" => $members_en, "ar" => $members_en),
      );
      $current_lang = pll_current_language();
      foreach ($bp_pages as $key => $bp_page) {
        foreach ($bp_page as $key => $page) {
          if (preg_match("/" . $page->post_name . "/", $_SERVER['REQUEST_URI'], $match)) {
            $slug = ($current_lang == "en") ? "ar" : "en";
            $restOfUrl = preg_replace($regex, '', $_SERVER['REQUEST_URI'], 1);
            //var_dump($_SERVER['REQUEST_URI']);
            return home_url($slug . "/" . $bp_page[$slug]->post_name . "/" . $restOfUrl);
          }
        }
      }
      if (!$url) {

      global $ef_translation_archive_urls;
      foreach($ef_translation_archive_urls as $key => $archive)
      {
        if (is_post_type_archive($archive)) {

          $slug = ($current_lang == "en") ? "ar" : "en";
          $url =  home_url($slug . "/".$key);
        }
      }

      if(is_singular('tribe_events'))
      {

        $request_uri = $_SERVER['REQUEST_URI'];
        $request_uri = str_replace("/en/events/", "", $request_uri);
        $request_uri = str_replace("/ar/events/", "", $request_uri);

        $slug = ($current_lang == "en") ? "ar" : "en";
        $url = home_url($slug . "/events/".$request_uri);
      }

      if (preg_match("/register/",$_SERVER['REQUEST_URI'])) {
        $slug = ($current_lang == "en") ? "ar" : "en";
        $url =  home_url($slug . "/register");
      }
    }
    $multiRewrite = false;
    if ($url) {

      global $page_overrided_links;
      if(!is_singular() || is_singular(array('post','page','tribe_events')))
      {
        foreach ($page_overrided_links as $key => $overrided_link) {

          $slug = ($current_lang == "en") ? "ar" : "en";
          $link = $overrided_link[$slug];
          if (preg_match("/" . $link . "/", $url)) {
            $url = preg_replace("/" . $link . "/", $key, $url, 1);
          }
          if($slug != pll_current_language() && isset($overrided_link["multiRewrite"]) && $overrided_link["multiRewrite"] == true)
          {
            $url = preg_replace("/" . pll_current_language() . "/", $slug , $_SERVER['REQUEST_URI'], 1);
            $multiRewrite = true;
          }
        }
      }
    }
    if(pll_current_language() != $slug && ! $multiRewrite)
    {
      $params = parse_url($_SERVER['REQUEST_URI']);
      if(isset($params['query']))
      {
          $restOfUrl = $params['query'];
          return $url."?".$restOfUrl;
      }
    }
    return $url;
  }
  add_filter('pll_translation_url', 'translateBpLinks', 999, 2);

  function ef_change_canonical_url($canonical_url, $args)
  {
    return site_url().$_SERVER['REQUEST_URI'];
  }
  //add_filter( 'bp_get_canonical_url', 'ef_change_canonical_url',10,2 );

  function ef_bp_change_tab_links($site_url)
  {
    return $site_url."/".  pll_current_language();
  }
   add_filter( 'bp_get_root_domain', "ef_bp_change_tab_links",10,1 );
}
// functions
function get_ar_page_by_en_id($id) {
  $post_in_langs = wp_get_object_terms($id, 'post_translations');
  if ($post_in_langs) {
    $post_in_langs = unserialize($post_in_langs[0]->description);
    $lang = "ar";
    $url = get_post_field('guid', $post_in_langs[$lang]);
    return $url;
  }
}

function getCustomizedLoginPage($url) {
  $old = array("/(wp-login\.php)/");
  $pages = get_pages(array(
    'meta_key' => '_wp_page_template',
    'meta_value' => 'template-login.php',
    'post_status' => 'publish'
  ));
  foreach ($pages as $login_page) {
    $post_in_langs = wp_get_object_terms($login_page->ID, 'post_translations');
    if ($post_in_langs) {
      $post_in_langs = unserialize($post_in_langs[0]->description);
      $current_lang = pll_current_language();
      if ($current_lang) {
        $page_name = get_post_field('post_name', $post_in_langs["en"]);
        global $pages_to_generate_rewrite_rules;
        $urlPage = preg_replace("/\/\?/","", $pages_to_generate_rewrite_rules[$page_name]);
        $new = array($current_lang . "/" . $urlPage . "/");
        $newUrl = preg_replace($old, $new, $url, 1);
        return $newUrl;
      }
    }
  }
  return $url;
}

function get_register_page_current_lang() {
  $postid = url_to_postid(get_option('siteurl') . "/register/");
  /* if ($postid) {
    $post_in_langs = wp_get_object_terms($postid, 'post_translations');
    if ($post_in_langs) {
    $post_in_langs = unserialize($post_in_langs[0]->description);
    $current_lang = pll_current_language();
    $url = get_post_field('post_name', $post_in_langs["en"]);
    return get_option('siteurl')."/".$current_lang."/".$url;
    }
    } */
  $current_lang = pll_current_language();
  if (!$current_lang) {
    $current_lang = ($_COOKIE['pll_language'] == 'ar') ? 'ar' : 'en';
  }
  return get_option('siteurl') . "/" . $current_lang . "/" . 'register';
}

function get_current_lang_page_by_template($template, $slug = false, $rewriteKey = null,$replace_params= array()) {
  $pages = get_pages(array(
    'meta_key' => '_wp_page_template',
    'meta_value' => $template,
    'post_status' => 'publish'
  ));
  foreach ($pages as $page) {
    $post_in_langs = wp_get_object_terms($page->ID, 'post_translations');
    if ($post_in_langs) {
      $post_in_langs = unserialize($post_in_langs[0]->description);
      $current_lang = ($slug == false) ? pll_current_language() : $slug;

      if (!$current_lang) {
        $current_lang = ($_COOKIE['pll_language'] == 'ar') ? 'ar' : 'en';
      }
      global $pages_to_generate_rewrite_rules;
      $post_name = get_post_field('post_name', $post_in_langs["en"]);
      $urlPage = ($rewriteKey)?$pages_to_generate_rewrite_rules[$post_name][$rewriteKey] : $pages_to_generate_rewrite_rules[$post_name];
      $urlPage = preg_replace("/\/\?/","", $urlPage);
      $urlPage = preg_replace("/[\$]/","", $urlPage);
      foreach($replace_params as $param)
      {
        $urlPage = preg_replace("/\(.*?\)/",$param, $urlPage,1);
      }
      return get_option('siteurl') . "/" . $current_lang . "/" . $urlPage;
    }
  }
  return false;
}

function ef_pll_get_post_types($post_types)
{
  if(!is_admin())
  {
    global $ef_translation_archive_urls;
    $types = $ef_translation_archive_urls;
    foreach($types as $post_type)
    {
      if($post_type != "page" && $post_type != "post")
      {
         unset($post_types[$post_type]);
      }
    }
  }
  return $post_types;
}
add_filter( 'pll_get_post_types', 'ef_pll_get_post_types', 10, 1 );

function registeredPostTypeRewriteSinglePages($post_type, $args) {
  if( !$args->rewrite ) return;

  $args->rewrite["slug"]  = preg_replace("/\(ar\|en\)\//", "", $args->rewrite["slug"]);

  $permastruct_args = $args->rewrite;
  $permastruct_args['feed'] = isset($permastruct_args['feeds'])?$permastruct_args['feeds']:"";
  if($post_type != "page" && $post_type != "post")
  {
      add_permastruct( $post_type, "en/{$args->rewrite['slug']}/%$post_type%", $permastruct_args );
      add_permastruct( $post_type."_ar", "ar/{$args->rewrite['slug']}/%$post_type%", $permastruct_args );

  }
}
add_action( 'registered_post_type','registeredPostTypeRewriteSinglePages', 10,2 );

function applyLanguageOnLink($post_link, $post)
{
    if (is_admin()) {
      $post_link = preg_replace("/\/(ar|en)\//", "/", $post_link);
    } else {
      if($post->post_type == "tribe_events")
      {
        $lang = pll_current_language();
        $post_link = preg_replace("/\/(ar|en)\//", "/{$lang}/", $post_link);
      }else
      {
        $lang = pll_get_post_language($post->ID);
        $lang = ($lang)?$lang:pll_current_language();
        $post_link = preg_replace("/\/(ar|en)\//", "/{$lang}/", $post_link);
      }
    }
  return $post_link;
}

add_filter( 'post_type_link','applyLanguageOnLink',10,2);

function ef_custom_rewrite_rules( $wp_rules ) {
  global $pages_to_generate_rewrite_rules;
  global $wpdb;

  $rules = [];
  $postnames = join("','", array_keys($pages_to_generate_rewrite_rules));

  $sql = "select * from {$wpdb->prefix}posts where post_name in ('{$postnames}') and post_type='page'";
  $results = $wpdb->get_results($sql);
  foreach ($results as $page) {

    $url_names = $pages_to_generate_rewrite_rules[$page->post_name];

    $params = (isset(parse_url($page->guid)['query'])) ? parse_url($page->guid)['query'] : "";
    if (is_array($url_names)) {
      $en_param = $params;
      foreach ($url_names as $url_name) {
        $rules["^en/{$url_name}"] = 'index.php?' . $en_param;
        $params = parse_url(get_ar_page_by_en_id($page->ID))['query'];
        $rules["^ar/{$url_name}"] = 'index.php?' . $params;
      }
    } else {
      if ($page->post_name != "register") {
        $rules["^en/{$url_names}"] = 'index.php?' . $params;
        // prevent access to old URLs after rewriting its path
        if( $page->post_name.'/?' != $url_names ) {
          $rules["^(en|ar)/{$page->post_name}"] = 'index.php?pagename=404';
        }
      }
      $params = parse_url(get_ar_page_by_en_id($page->ID))['query'];
      $rules["^ar/{$url_names}"] = 'index.php?' . $params;
    }
  }

  return $rules + $wp_rules;
}

// add_filter('rewrite_rules_array', 'ef_custom_rewrite_rules', 1);

function ef_custom_rewrite_rules_single_event($postTypeArgs) {
  $postTypeArgs['rewrite']['slug'] = "(ar|en)/" . $postTypeArgs['rewrite']['slug'];
  return $postTypeArgs;
}

add_filter('tribe_events_register_event_type_args', 'ef_custom_rewrite_rules_single_event', 999, 3);

// force our custom rewrite rules to be appended to website rewrite rules
function force_modify_custom_rewrite_rules( $value, $old_value, $option ) {
  // 1100 is a virtual number of rewrite rules without our custom rewrites rules ( Temporarily )
  if( $value != '' && count($value) < 1100 ) {
    $value = ef_custom_rewrite_rules( $value );
  }

  return $value;
}

add_filter( "pre_update_option_rewrite_rules", 'force_modify_custom_rewrite_rules' );
