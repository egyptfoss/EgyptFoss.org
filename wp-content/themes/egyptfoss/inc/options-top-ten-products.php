<?php
// create custom plugin settings menu
add_action('admin_menu', 'ef_select_top_ten_create_menu');

function ef_select_top_ten_create_menu() {

	//create new top-level menu
	//add_menu_page('Top 10 Products', 'Top 10 Products', 'administrator', "top_ten_products", 'ef_select_top_ten_settings_page','',27);
  add_submenu_page('edit.php?post_type=product','Top 10 Products', 'Top 10 Products', 'administrator', "top_ten_products", 'ef_select_top_ten_settings_page');
  wp_enqueue_script('ef-select-top-ten', get_template_directory_uri()."/js/options-top-ten-products.js",array(),false,true);
  wp_enqueue_script('loumultiselect-js', get_template_directory_uri()."/js/loumultiselect/js/jquery.multi-select.js",array(),false,true);
  wp_enqueue_script('loumultiselect-search-js', get_template_directory_uri()."/js/loumultiselect/js/search.js",array(),false,true);
  wp_enqueue_style('loumultiselect-css', get_template_directory_uri()."/js/loumultiselect/css/multi-select.css");
  wp_enqueue_script( 'select2', get_template_directory_uri() . '/js/select2.min.js', array('jquery'), '', true);
  wp_enqueue_style( 'select2-css', get_template_directory_uri() . '/css/select2.min.css' );
	//call register settings function
	add_action( 'admin_init', 'register_ef_select_top_ten_settings' );
}


function register_ef_select_top_ten_settings() {
	//register our settings
	//register_setting( 'ef_select_top_ten-settings-group', 'new_option_name' );
	//register_setting( 'ef_select_top_ten-settings-group', 'some_other_option' );
	register_setting( 'ef_select_top_ten-settings-group', 'admin-top-ten-products-list','ef_save_options' );
}

function ef_select_top_ten_settings_page() {
?>
<div class="wrap">
<h2>Top 10 Products</h2>
<p>You can select top 10 products for each industry 
    by choosing the industry<br/>then, one click on the product from ( all products ) list and 
    it will be moved to ( top 10 products ) list<br/>
    you have to choose just 10 products 
</p>
<form id="save_top_ten_form" method="post" action="options.php">
    <?php settings_fields( 'ef_select_top_ten-settings-group' ); ?>
    <?php do_settings_sections( 'ef_select_top_ten-settings-group' ); ?>
    <?php $taxonomies = array('industry'); ?>
    <?php $industries = get_terms( $taxonomies, array('hide_empty'=>false, 'hierarchical' => true,  'parent' => 0));?>
    <?php 
    if( isset($_REQUEST['saved']) ) {
    if( $_REQUEST['saved'] == "true" ) { ?>
    <div id="message" class="updated">
        <p><strong><?php _e('Settings saved.') ?></strong></p>
    </div>
    <?php }else{ ?>
      <div id="message" class="error">
        <p><strong><?php _e('please choose 10 products.') ?></strong></p>
    </div>
      
    <?php } } ?>
    <style>#select2-industry-results{max-height: 400px;} #select2-industry-results li {padding: 0px 6px 0px 6px;}</style>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Industry</th>
        <td>
        <select name="industry" id="industry">
          <option>None</option>
          <?php
            $firstIndustryId = 0;
            $industryFromGet = $_GET['industry'];
            foreach ($industries as $key => $industry) {
              printf(
                '<option value="%1$s" %2$s disabled>%3$s</option>',       
                $industry->term_id,
                selected( $industry->term_id, $industryFromGet ),
                $industry->name
              );

              // get child
              $subterms = get_terms( $taxonomies, array( 'parent' => $industry->term_id, 'hide_empty' => false ) );

              foreach ( $subterms as $subterm ) {
                printf(
                  '<option value="%1$s" %2$s>%3$s</option>',       
                  $subterm->term_id,
                  selected( $subterm->term_id, $industryFromGet ),
                  '— '.$subterm->name
                );
              }
            }
          ?>
        </select> 
        </td>    
        </tr>
        
        <tr valign="top">
        <th scope="row">select top 10 Products</th>
        
        <td>
            <select multiple="true"  name="admin-top-ten-products-list[]" id="admin-top-ten-products-list" style="width:400px;">
               
              <?php if( $industryFromGet ) echo ef_admin_load_products($industryFromGet); ?>
            </select>
        </td>
        </tr>
        
        <!--<tr valign="top">
        <th scope="row">Options, Etc.</th>
        <td><input type="text" name="option_etc" value="<?php //echo esc_attr( get_option('option_etc') ); ?>" /></td>
        </tr>-->
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php }

function ef_save_options($data)
{
  global $wpdb;
  $industry_id = 0;
  if (count($_POST["admin-top-ten-products-list"]) == 10) {
    if (is_numeric($_POST["industry"])) {
      $industry_id = intval($_POST["industry"]);
    }

    $delete_sql = "Delete from {$wpdb->prefix}top_ten_products where term_id = {$industry_id}";
    $wpdb->query($delete_sql);

    foreach ($_POST["admin-top-ten-products-list"] as $post_id) {
      if (is_numeric($post_id))
        $post_id = intval($post_id);
      $insert_sql = "Insert into {$wpdb->prefix}top_ten_products (post_id, term_id) values ({$post_id},{$industry_id})";
      $wpdb->query($insert_sql);
    }
    header("Location: edit.php?post_type=product&page=top_ten_products&saved=true&industry=".$industry_id);
  }else
  {
    header("Location: edit.php?post_type=product&page=top_ten_products&saved=false");
  }
  die();
}
function ef_ajax_admin_load_products() {
  
  echo ef_admin_load_products($_POST['term_id']); 
  die();
}
add_action('wp_ajax_ef_admin_load_products', 'ef_ajax_admin_load_products');

function ef_admin_load_products($term_id,$lang = 'en')
{
  $args = array(
    "post_status" => "publish",
    "post_type" => "product",
  );
  global $wpdb;
  $foreign_lang = ($lang == "en")?"ar":"en";
  if(!is_numeric($term_id))
  {
    return false;
  }
 $sql = "SELECT *
        FROM {$wpdb->prefix}posts as p
        join {$wpdb->prefix}postmeta as pmeta on p.ID = pmeta.post_id
        join {$wpdb->prefix}term_relationships as rel on p.ID = rel.object_id
        join {$wpdb->prefix}term_taxonomy as tax on rel.term_taxonomy_id = tax.term_taxonomy_id
        where (p.post_status = '{$args['post_status']}' and p.post_type = '{$args['post_type']}')
        and (pmeta.meta_key = 'language' and 
        (pmeta.meta_value like '%\"en\"%'))
        and tax.term_id = {$term_id}
        group by p.ID";      
  $results = $wpdb->get_results($sql);
  $output = ""; 
  foreach($results as $result)
  {
    $selected = (isTopTenProduct($result->ID))?"selected":"";
    $output .= "<option {$selected} value='{$result->ID}'>". $result->post_title."</option>"; 
  }
  return $output;
}