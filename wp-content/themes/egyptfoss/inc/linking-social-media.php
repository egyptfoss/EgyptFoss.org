<?php

add_action('bp_setup_nav', 'add_settings_subnav_tab', 999);
function add_settings_subnav_tab() {
  global $bp;
  bp_core_new_subnav_item(array(
    'name' => __('Linking', 'egyptfoss'),
    'slug' => 'linking',
    'parent_url' => trailingslashit(bp_displayed_user_domain() . $bp->settings->slug),
    'parent_slug' => $bp->settings->slug,
    'screen_function' => 'linking_screen',
    'item_css_id' => $bp->settings->id,
    'user_has_access' => bp_is_my_profile(),
  ));
}

function linking_screen() {
  bp_core_load_template('members/single/settings/linking');
}

add_action('wp_ajax_ef_delete_user_profile', 'ef_delete_user_profile');
function ef_delete_user_profile() {
  $user_id = get_current_user_id();
  $userProfileID = $_POST["userProfileID"];
  if ($user_id) {
    global $wpdb;
    if (canDeleteLastSocialMedia($user_id, $wpdb)) {
      $result = $wpdb->delete("{$wpdb->prefix}wslusersprofiles", array('id' => $userProfileID, "user_id" => $user_id), array('%d', '%d'));
      echo "deleted";
      die();
    } else {
      echo "last-account";
      die();
    }
  }
  echo "not-deleted";
  die();
}

function isConnectedWithSocialMedia($provider) {
  $user_id = get_current_user_id();
  global $wpdb;

  $sql = "SELECT * FROM `{$wpdb->prefix}wslusersprofiles` where user_id = %d and provider = %s";
  $result = $wpdb->get_results($wpdb->prepare($sql, $user_id, $provider));
  if ($result) {
    return $result[0]->id;
  }
  return false;
}

function getSocialLinks() {
  global $WORDPRESS_SOCIAL_LOGIN_PROVIDERS_CONFIG;
  $links = array();
  $authenticate_base_url = site_url() . "?action=wordpress_social_authenticate&mode=link&";

  foreach ($WORDPRESS_SOCIAL_LOGIN_PROVIDERS_CONFIG AS $item) {
    $provider_id = isset($item["provider_id"]) ? $item["provider_id"] : '';
    $provider_name = isset($item["provider_name"]) ? $item["provider_name"] : '';
    if (get_option('wsl_settings_' . $provider_id . '_enabled')) {
      $redirect_to = home_url($_SERVER['REQUEST_URI']);
      $authenticate_url = $authenticate_base_url . "provider=" . $provider_id . "&redirect_to=" . urlencode($redirect_to);
      $authenticate_url = esc_url($authenticate_url);
      if ($wsl_settings_use_popup == 1 && $auth_mode != 'test') {
        $authenticate_url = "javascript:void(0);";
      }
      $links[$provider_name] = $authenticate_url;
    }
  }
  return $links;
}

function canDeleteLastSocialMedia($user_id, $wpdb) {
  $sql = "SELECT count(*) as count FROM `{$wpdb->prefix}wslusersprofiles` where user_id = %d limit 1";
  $result = $wpdb->get_results($wpdb->prepare($sql, $user_id));
  if ($result[0]->count == 1) {
    $user_meta = get_user_meta($user_id, "registration_data", true);
    $user_meta = unserialize($user_meta);
    if ($user_meta && isset($user_meta['registeredNormally']) && $user_meta['registeredNormally'] == 1) {
      return true;
    } else {
      return false;
    }
  }
  return true;
}
