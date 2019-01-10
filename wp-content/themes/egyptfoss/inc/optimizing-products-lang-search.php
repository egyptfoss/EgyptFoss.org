<?php
function ef_pll_save_post($post_id, $post, $translations) {
  $special_actions = array("trash", "untrash", "delete");
  if (isset($_REQUEST["action"])) {
    $url = parse_url($_REQUEST['_wp_http_referer'], PHP_URL_QUERY);
    parse_str($url, $params);
    reset($translations);

    $post_meta = get_post_meta($post_id, 'language', true);
    $is_translated = (unserialize($post_meta)["translated_id"]);

   

    if ($_REQUEST["action"] == "trash" || $_REQUEST["action"] == "untrash") {
      if ($is_translated) {
        $post_meta = get_post_meta($is_translated, 'language', true);
        $lang_info = (unserialize($post_meta));
        $lang_info = array_merge($lang_info, array(
          "trashed" => ($_REQUEST["action"] == 'trash') ? 1 : 0
        ));
        update_post_meta($is_translated, 'language', serialize($lang_info));
      }
    }

    if (!in_array($_REQUEST['action'], $special_actions)) {
      if ($is_translated != 0) {
        $post_meta = get_post_meta($is_translated, 'language', true);
        $lang_info = (unserialize($post_meta));
        $lang_info["translated_id"] = 0;
        update_post_meta($is_translated, 'language', serialize($lang_info));
      }
      
      if ( isset( $_REQUEST['post_lang_choice'] ) || isset( $_REQUEST['inline_lang_choice'] ) ) {
        update_post_meta($post_id, 'language', serialize(array(
          "slug" => isset( $_REQUEST['post_lang_choice'] ) ? $_REQUEST['post_lang_choice'] : $_REQUEST['inline_lang_choice'],
          "translated_id" => ($translations[key($translations)] != 0) ? $translations[key($translations)] : 0)));

        update_post_meta($translations[key($translations)], 'language', serialize(array(
          "slug" => key($translations),
          "translated_id" => ($post_id))));
      }
    }
  }
}

add_action('pll_save_post', 'ef_pll_save_post', 10, 3);

add_action( 'before_delete_post', 'ef_pll_delete_post' );
function ef_pll_delete_post( $postid ){
    $post_meta = get_post_meta($postid, 'language', true);
    $is_translated = (unserialize($post_meta)["translated_id"]);


      if ($is_translated != 0) {
        $post_meta = get_post_meta($is_translated, 'language', true);
        $lang_info = (unserialize($post_meta));
        $lang_info["translated_id"] = 0;
        update_post_meta($is_translated, 'language', serialize($lang_info));
      
    }
}
