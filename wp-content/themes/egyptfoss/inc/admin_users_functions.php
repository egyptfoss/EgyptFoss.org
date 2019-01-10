<?php
//add additional columns to the users.php admin page
function modify_user_columns($column) {
    $column = array(
    "cb" => "",
    "username" => "Username",
    "displayname" => "Display Name",
    "email" => "E-mail",
    "role" => "Role",
    "posts" => "Posts",
    "type" => "Type"  //the new column
    );
    return $column;
}
add_filter('manage_users_columns','modify_user_columns');

//add content to your new custom column
function modify_user_column_content($val,$column_name,$user_id) {
    switch ($column_name) {
      case 'displayname':
        $displayname = get_userdata($user_id);
        return $displayname->display_name;
      case 'type':
      $meta = get_user_meta($user_id, 'type', true);
      return $meta ;
      break;
      //I have additional custom columns, hence the switch. But am only showing one here
      default:
    }
    return $return;
}
add_filter('manage_users_custom_column', 'modify_user_column_content', 10, 3);

//make the new column sortable
function user_sortable_columns( $columns ) {
  $columns['type'] = 'type';
  return $columns;
}
add_filter( 'manage_users_sortable_columns', 'user_sortable_columns' );

//set instructions on how to sort the new column
if(is_admin()) {//prolly not necessary, but I do want to be sure this only runs within the admin
  add_action('pre_user_query', 'my_user_query');
}
function my_user_query($userquery){
  if('type'==$userquery->query_vars['orderby']) {//check if type is the column being sorted
    global $wpdb;
    $userquery->query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS alias ON ($wpdb->users.ID = alias.user_id) ";//note use of alias
    $userquery->query_where .= " AND alias.meta_key = 'type' ";//which meta are we sorting with?
    $userquery->query_orderby = " ORDER BY alias.meta_value ".($userquery->query_vars["order"] == "ASC" ? "asc " : "desc ");//set sort order
  }
}

function new_contact_methods( $contactmethods ) {
  $contactmethods['type'] = 'Type';
  return $contactmethods;
}
add_filter( 'user_contactmethods', 'new_contact_methods', 10, 1 );

function user_profile_updated( $user_id, $old_user_data ) {
  $user_data = get_registration_data($user_id);
  $lang = get_user_meta( $user_id, 'prefered_language', true );
  if(array_key_exists('type', $_POST)) {
    // TODO YF: check types value exists
    $user_data['type'] = $_POST['type'];
    $user_data['sub_type'] = $_POST['sub_type']; // Added sub type in edit user form
  }

  if(array_key_exists('telephone_number', $_POST)) {
    $user_data['contact_phone'] = $_POST['telephone_number'];
  }

  $meta_key = 'registration_data';
  $meta_value = serialize($user_data);
  update_user_meta($user_id, $meta_key, $meta_value);

  if( is_admin() ) {
    if(array_key_exists('is_expert', $_POST)) {
      $is_expert = TRUE;
      update_user_meta($user_id, 'is_expert', 1);
      sendMarkedExpertEmail($user_id);
    }else
    {
      $is_expert = FALSE;
      update_user_meta($user_id, 'is_expert', 0);
    }

    load_orm();
    // expert badge management
    $badge = new Badge( $user_id );
    $badge->efb_manage_expert_badge( $is_expert );

    // send emails to expert with earned badges;
    foreach( $badge->badges_earned as $badge ) {
      global $wpdb;
      $query = "SELECT * FROM {$wpdb->base_prefix}efb_badges WHERE name = '{$badge->name}'";
      $result = $wpdb->get_results($query, ARRAY_A);

      if( class_exists( 'EFBBadges' ) && !empty( $result ) ) {
        sendNewBadgeAchiever( $user_id, new EFBBadges( $result[0] ) );
      }
    }
  }

  // save users content to marmotta
  saveUserContent( $user_id, $_POST['nickname'], $user_data['functionality'], $user_data['type'] );
}
add_action( 'profile_update', 'user_profile_updated', 10, 2 );

function mod_backend_new_user_fields() { ?>
  <script type="text/javascript">
    var hideFields = [ "first_name", "last_name", "url" ];
    jQuery.each( jQuery( "tr.form-field" ), function() {
      var field = jQuery( this ).find( "input" ).attr( "id" );
      if ( hideFields.indexOf( field ) != -1 ) {
        jQuery( this ).remove();
      }
    });
  </script> <?php
}
add_action( 'admin_footer-user-new.php', 'mod_backend_new_user_fields' );

function mod_backend_edit_user_fields() { ?>
  <script type="text/javascript">
    jQuery(document).ready( function($) {
      var tables_titles = ['Personal Options', 'About the user', 'About Yourself'];
      for (var i = 0; i < tables_titles.length; i++) {
        $("h2:contains('"+tables_titles[i]+"')").next('table').remove();
      }
      titles = ['Personal Options', 'Name', 'Contact Info', 'About the user', 'About Yourself',
          'Account Management', 'Yoast SEO settings'];
      for (var i = 0; i < titles.length; i++) {
        $("h2:contains('"+titles[i]+"')").remove();
        $("h3:contains('"+titles[i]+"')").remove();
      }
      var fields = ['#first_name', '#last_name', '#display_name', '#url', '#type',
          '#googleplus', '#twitter', '#facebook', '#wpseo_author_title', '#wpseo_author_metadesc', '#wpseo_author_exclude'];
      for (var i = 0; i < fields.length; i++) {
        $(fields[i]).closest('tr').remove();
      }
      $('#nickname').closest('tr').hide();
    });

    jQuery(document).ready( function($) {
      var classTypeSelected = $('input[name=type]:checked').val();
      var allSubTypes = $('.registration_sub_type option');
      var currentAccountSubTypeSelected = $(".registration_sub_type")[0].selectedIndex;
      $('.registration_sub_type option:not(".'+ classTypeSelected +'")').remove();
      $('input:radio[name=type]').on('change', function () {
        $('.registration_sub_type option').remove(); //remove all options
        var classN = $('input[name=type]:checked').val();
        var opts = allSubTypes.filter('.' + classN);
        var opts_Individual = allSubTypes.filter('.Individual').length - 1;
        var classTypeSelected = $('input[name=type]:checked').val();
        if(classTypeSelected === "Individual") {
    			$('#telephoneNumber').hide();
    			opts_Individual = 0;
    		}
    		else if (classTypeSelected === "Entity") {
    			$('#telephoneNumber').show();
    		}
        $.each(opts, function (i, j) {
          $(j).appendTo('.registration_sub_type'); //append those options back
        });
        if(classTypeSelected == classN)
            $('.registration_sub_type option').eq(currentAccountSubTypeSelected - opts_Individual).prop('selected', true);
        else
            $('.registration_sub_type option').eq(0).prop('selected', true);
      });

      $("input[name='is_expert']").change(function(){
        if($("#role").val() == "subscriber" && $("input[name='is_expert']:checked").length == 1)
        {
          returnVal = confirm("Are you sure you want to set a subscriber as an expert ?");
          $(this).attr("checked", returnVal);
        }
      });
    });


  </script> <?php
}
add_action( 'admin_footer-user-edit.php', 'mod_backend_edit_user_fields' );
add_action( 'admin_head-profile.php', 'mod_backend_edit_user_fields' );
add_action( 'admin_footer-profile.php', 'mod_backend_edit_user_fields' );

function user_nickname_is_login( $meta, $user, $update ){
  //update user registered time
  $date = new DateTime('now');
  global $wpdb;
  $wpdb->update($wpdb->prefix.'users',array( 'user_registered' => $date->format('Y-m-d H:i:s') ),array( 'ID' => $user->ID ));

  $meta['nickname'] = $user->user_login;
  return $meta;
}
add_filter( 'insert_user_meta', 'user_nickname_is_login', 10, 3 );

function custom_user_profile_fields($user){
  if ($user){
    $user_id = $user->ID;
    $user_meta = get_user_meta($user_id, "registration_data", true);
    $user_meta = unserialize($user_meta);

    $account_type = $user_meta['type'];
    $sub_type = $user_meta['sub_type'];
  }

  if( !empty($_POST['type']) ) {
      $account_type = isset( $_POST['type'] )? $_POST['type'] : '';
  }

  include( ABSPATH . 'system_data.php' );
  $lang = get_locale();
  if($lang == 'ar') {
    global $ar_sub_types;
    $sub_types = $ar_sub_types;
  } else {
    global $en_sub_types;
    $sub_types = $en_sub_types;
  }
  ?>
  <h3>Extra profile information</h3>
  <table class="form-table">
    <tr>
      <th><label for="type">Type</label></th>
      <td>
        <input type="radio" name="type" value="Individual" <?php echo (($account_type=='Individual' || $account_type=='')?'checked':''); ?>>
            <?php _e( 'Individual', 'egyptfoss' ); ?>
        <input type="radio" name="type" value="Entity" <?php echo (($account_type=='Entity')?'checked':''); ?>>
            <?php _e( 'Entity', 'egyptfoss' ); ?><br>
      </td>
    </tr>
    <tr>
      <th><label for="subtype">SubType</label></th>
      <td>
        <select name="sub_type" id="sub_type" class="registration_sub_type form-control" style="width: 315px;">
          <?php
            $account_sub_type_labels = array();
            foreach ($account_sub_types as $sub => $t) {
              $account_sub_type_labels = array_merge($account_sub_type_labels,array($sub=>$sub_types[$sub]));
            }
            asort($account_sub_type_labels);
            foreach ($account_sub_type_labels as $sub => $label) {
              if($sub_type == $sub)
                echo("<option class='".$account_sub_types[$sub]."' value='".$sub."' selected=\"selected\" >");
              else
                echo("<option class='".$account_sub_types[$sub]."' value='".$sub."' >");
                echo($label);
                echo("</option>");
            }
          ?>
        </select>
      </td>
    </tr>
    <tr id="isExpert">
      <?php $is_expert = get_user_meta($user_id, "is_expert", true); ?>
      <th><label for="expert">Expert</label></th>
      <td>
        <input type="checkbox" name="is_expert" id="is_expert" value="" <?php if($is_expert){ ?>checked<?php } ?>>
      </td>
    </tr>
    <tr id="telephoneNumber" <?php echo $account_type=='Entity' ? '': 'style="display: none;"' ?>>
      <?php $telephone_number = $user_meta['contact_phone']; ?>
      <?php
        if ( !empty($_POST['telephone_number']) ) {
          $telephone_number = isset( $_POST['telephone_number'] )? $_POST['telephone_number'] : '';
        }
       ?>
      <th><label for="telephone_number">Telephone Number</label></th>
      <td>
        <input type="text" name="telephone_number" id="telephone_number" value="<?php echo $telephone_number ?>" aria-required="true">
      </td>
    </tr>
    </table>
<?php
}
add_action( 'show_user_profile', 'custom_user_profile_fields' );
add_action( 'edit_user_profile', 'custom_user_profile_fields' );
add_action( "user_new_form", "custom_user_profile_fields" );

//function save_custom_user_profile_fields($user_id){
//    # again do this only if you can
////    if(!current_user_can('manage_options'))
////        return false;
//
//    # save my custom field
////    update_usermeta($user_id, 'company', $_POST['company']);
//}
//add_action('user_register', 'save_custom_user_profile_fields');

// -- type & subtype javascript action -- //
function user_extra_fields(){
  ?>
  <script type="text/javascript">
    jQuery(document).ready( function($) {
      var classTypeSelected = $('input[name=type]:checked').val();
      var allSubTypes = $('.registration_sub_type option');
      var currentAccountSubTypeSelected = $(".registration_sub_type")[0].selectedIndex;
      $('.registration_sub_type option:not(".'+ classTypeSelected +'")').remove();
      $('input:radio[name=type]').on('change', function () {
        $('.registration_sub_type option').remove(); //remove all options
        var classN = $('input[name=type]:checked').val();
        var opts = allSubTypes.filter('.' + classN);
        var opts_Individual = allSubTypes.filter('.Individual').length - 1;
        var classTypeSelected = $('input[name=type]:checked').val();
        if(classTypeSelected === "Individual") {
    			$('#telephoneNumber').hide();
    			opts_Individual = 0;
    		}
    		else if (classTypeSelected === "Entity") {
    			$('#telephoneNumber').show();
    		}
        $.each(opts, function (i, j) {
          $(j).appendTo('.registration_sub_type'); //append those options back
        });
        if(classTypeSelected == classN)
            $('.registration_sub_type option').eq(currentAccountSubTypeSelected - opts_Individual).prop('selected', true);
        else
            $('.registration_sub_type option').eq(0).prop('selected', true);
      });
    });
  </script>
  <?php
}
add_action( 'admin_footer-user-new.php', 'user_extra_fields' );
add_action( 'admin_head-profile.php', 'user_extra_fields' );
add_action( 'admin_footer-profile.php', 'user_extra_fields' );


/**
 * We use current_user_can to apply this function to the subscriber role only.
 * This function redirects users to the site's home page after they update their profiles.
 * The function wp_redirect() must always be followed by exit;
 *
 */
//<div id="message" class="updated notice is-dismissible">
//<p>
//<strong>Profile updated.</strong>
//</p>
//<button class="notice-dismiss" type="button">
//<span class="screen-reader-text">Dismiss this notice.</span>
//</button>
//</div>
//function tgm_io_custom_profile_redirect($user_id) {
//  if ( current_user_can( 'administrator' ) ) {
//      wp-admin/user-edit.php?user_id
//    wp_redirect( trailingslashit( home_url() ) );
//    wp_redirect(home_url("/wp-admin/user-edit.php?user_id=$user_id"));
//    exit;
//  }
//}
//add_action( 'profile_update', 'tgm_io_custom_profile_redirect', 12 );
