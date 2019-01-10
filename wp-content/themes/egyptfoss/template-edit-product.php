<?php
/**
 * Template Name: Edit Product.
 *
 * @package egyptfoss
 */

  if ( !is_user_logged_in() ) {
    wp_redirect( home_url( '/wp-login.php?redirected=editproduct' ) );
    exit;
  } else {
    if (!current_user_can('perform_direct_ef_actions')) {
    // Subscriber, Contributor users should be able to view (Edit Product)
    //wp_redirect( home_url( '?action=unauthorized' ) );
    wp_redirect(home_url('/?status=403'));
    exit;
    }
  }

  $getParams["product"] = 0;
  if(is_numeric($_GET["pid"])) {
    $getParams["product"] = intval($_GET["pid"]);
    if (!empty($_POST['action']) && $_POST['action'] == "edit_product" && isset($_POST['postid'])) {
      $nonce = $_REQUEST['_wpnonce'];
      if ( ! wp_verify_nonce( $nonce, 'edit-product' ) ) {
        wp_redirect( home_url( '/?status=403' ) );
        exit;
      }
      ef_update_product_frontEnd($getParams["product"]);
      //echo '<script>parent.window.location.reload(true);</script>';
    }
    $post_data = get_post($getParams["product"]);
    $post_meta_data = get_post_meta($getParams["product"]);
    $canEdit = userCanEditProduct($post_data->post_status,(int)$post_data->post_author);
    if (!$canEdit) {
      wp_redirect(home_url('/?status=403'));
      exit;
    }
    global $wpdb;
    $sql = "select ID from $wpdb->posts where  post_type='product' AND ( post_status='publish' OR (post_status='pending' AND post_author=%s) ) and ID = %s";
    $checkExistance = $wpdb->get_col($wpdb->prepare($sql, get_current_user_id(), $getParams["product"]));
    if (!$checkExistance) {
      $ef_product_messages = array("errors"=>"you have to enter id");
      set_query_var("ef_product_messages", $ef_product_messages);
      echo "No Product Found !";
      exit;
    }

    //create array of existing images
    $screenshots = array();
    if(!empty($post_meta_data['fg_perm_metadata'][0]) && $post_meta_data['fg_perm_metadata'][0] != "") {
        $screenshot_ids = explode(",",$post_meta_data['fg_perm_metadata'][0]);
        for($i = 0; $i < sizeof($screenshot_ids); $i++) {
            //load attachment file
            if($screenshot_ids[$i] != '')
            {
            $post_attachment = get_post($screenshot_ids[$i]);
            
            if($post_attachment)
            {
              $screenshot = array(
                  'id'   => $post_attachment->ID,
                  'name' => $post_attachment->post_title,
                  'type' => $post_attachment->post_mime_type,
                  'file' => $post_attachment->guid,
                  'size'   => $post_attachment->ID,
              );
              array_push($screenshots, $screenshot);
            }
            }
        }
    } else {
      $screenshots = null;
    }
  }
  get_header();
?>
<header class="page-header">
<div class="container">
        <div class="row">
                <div class="col-md-7">
                        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </div>
                <div class="col-md-5 hidden-xs">
                        <?php if (function_exists('template_breadcrumbs')) template_breadcrumbs(); ?>
                </div>
        </div>
</div>
</header><!-- .entry-header -->

<div class="container">
  <div class="row">
      <div id="primary" class="content-area col-md-12">
    <main id="main" class="site-main" role="main">
<!-- form code -->

<body>
    <div id="content" role="main">
        <div class="new-coupon-form">
        <!-- Edit FORM -->
        <?php
         
        ?>
        <form id="edit_product" name="edit_product" method="post" action="" enctype="multipart/form-data">
            <div class="required">                
                <?php
                $ef_product_messages = get_query_var("ef_product_messages");
                if(isset($ef_product_messages['errors']))
                {?>
                <div class="alert alert-danger"><?php
                foreach($ef_product_messages['errors'] as $error )
                {
                  echo "<i class='fa fa-warning'></i> " . $error . "<br/>";
                }
                ?>
                </div>
                <?php
                }
                if(isset($ef_product_messages['success']))
                {?>
                <div class="alert alert-success"><?php
                foreach($ef_product_messages['success'] as $success )
                {
                  echo "<i class='fa fa-check'></i>" . $success . "<br/>";
                }?>
                </div>
                <?php }
                set_query_var("ef_product_messages", array());
                ?>
            </div>

            <!-- post name -->
            <div class="form-group row">
              <div class="col-md-12">
                    <label for="product_title" class="label">
                            <?php _e( 'Title', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
                    </label>
                  <input class="form-control" type="text" id="product_title" value="<?php echo $post_data->post_title ?>" name="product_title" />
              </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12">
                    <label for="description" class="label">
                            <?php _e( 'Description', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
                    </label>
                    <textarea class="form-control" name="description" id="description" rows="3" placeholder="<?php _e( 'Type product description here', 'egyptfoss' ); ?>"><?php
                    $description = nl2br(get_field('description',$getParams["product"]));
                    if($description)
                      {
                        echo strip_tags( html_entity_decode( $description ) );
                      } ?></textarea>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-12">
                    <label for="" class="label">
                            <?php _e( 'Product Logo', 'egyptfoss' ); ?>
                    </label>
                    <div class="input-group image-preview">
                            <input type="text" class="form-control image-preview-filename" disabled="disabled"> <!-- don't give a name === doesn't send on POST/GET -->
                            <span class="input-group-btn">
                                    <!-- image-preview-clear button -->
                                    <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                                            <span class="icons icon-cancel"></span> <?php _e('Clear','egyptfoss'); ?>
                                    </button>
                                    <!-- image-preview-input -->
                                    <div class="btn btn-default image-preview-input">
                                            <span class="icons icon-folder-open"></span>
                                            <span class="image-preview-input-title"><?php _e("Browse","egyptfoss"); ?></span>
                                            <input type="file" accept="image/png, image/jpeg, image/gif" name="product_logo" id="product_logo" title="<?php _e( 'please enter correct image type', 'egyptfoss' ); ?>"/>
                                            <?php 
                                            
                                            wp_nonce_field( 'product_logo', 'product_logo_nonce' ); ?>
                                    </div>
                            </span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                    <div class="col-md-12">
                            <label for="product_title" class="label"><?php _e( 'Product Screenshots', 'egyptfoss' ); ?></label>
                            <!--<input type="file" class="form-control" id="file" name="product_screenshots[]" multiple="multiple" accept="image/*"/>-->
                            <input style="visibility:hidden;" type="file" name="product_screenshots[]" id="filer_input" multiple="multiple" accept="image/*">
                            <?php wp_nonce_field( 'product_screenshots', 'product_screenshots_nonce' ); ?>
                             <input type="hidden" id="screens_ids" name="screens_ids" value="" />
                             <input type="hidden" id="current_id" name="current_id" value="" />
                             
                    </div>
            </div>

            <div class="form-group row">
              <div class="col-md-12">
          <label for="developer" class="label">
            <?php _e( 'Developer', 'egyptfoss' ); ?>
          </label>
                  <input class="form-control" type="text" id="developer" 
                         <?php 
                         $developer = get_field("developer",$getParams["product"]);
                         if($developer){
                                echo "value=\"" .$developer . "\""; 
                          } ?> name="developer" />
              </div>
            </div>

            <div class="form-group row">
                    <div class="col-md-12">
                            <label for="functionality" class="label"><?php _e( 'Functionality', 'egyptfoss' ); ?></label>
                            <textarea class="form-control" name="functionality" id="functionality" rows="3" placeholder="<?php _e( 'Type product functionality here', 'egyptfoss' ); ?>"><?php
                            $func = get_field("functionality",$getParams["product"]);
                            if($func){ 
                              echo $func;
                            } 
                            ?></textarea>
                    </div>
            </div>

            <div class="form-group row">
              <div class="col-md-12">
              <?php
              $taxonomy = 'industry';
             // $tax_terms = get_terms($taxonomy, array('hide_empty' => false));
              $chosen_industry = get_field("industry",$getParams["product"]);
              $chosen_industry = is_array($chosen_industry) ? $chosen_industry[0] : $chosen_industry;
              $industry = get_term($chosen_industry, $taxonomy);
              ?>
                    <label for="post_industry" class="label"><?php _e( 'Category', 'egyptfoss' ); ?></label>
                   
                    <label for="post_industry" class="form-control disable-edit" >
                        <?php
                        if(get_class($industry) != "WP_Error")
                        {
                          if (pll_current_language() == "ar") {
                            $industry_name = $industry->name_ar;
                          } else {
                            $industry_name = $industry->name;
                          }
                          
                          if( $industry->parent ) {
                            $parent = get_term( $industry->parent, 'industry' );
                            $parent_name = ((pll_current_language() == 'ar' && $parent->name_ar != '' && $parent->name_ar != null) ? $parent->name_ar : $parent->name);
                            $industry_name = $parent_name . " -> " . $industry_name;
                          }
                          
                          echo $industry_name;
                        } else {
                          _e('Not Specified', 'egyptfoss');
                        }
                        ?>
                    </label>
                        
              </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12">
                    <label for="usage_hints" class="label"><?php _e( 'Usage hints', 'egyptfoss' ); ?></label>
                    <textarea class="form-control" name="usage_hints" id="usage_hints" rows="3" placeholder="<?php _e( 'Type product usage hints here', 'egyptfoss' ); ?>"><?php
                    $usage = get_field("usage_hints",$getParams["product"]);
                    if($usage){
                                echo $usage; 
                    } ?></textarea>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12">
                    <label for="references" class="label"><?php _e( 'References', 'egyptfoss' ); ?></label>
                    <textarea class="form-control" name="references" id="references" rows="3" placeholder="<?php _e( 'Type product references here', 'egyptfoss' ); ?>"><?php
                    $references = get_field("references",$getParams["product"]); 
                    if($references){
                                echo $references; 
                    } ?></textarea>
                </div>
            </div>

            <div class="form-group row">
              <div class="col-md-12">
                    <label for="link_to_source" class="label"><?php _e( 'Link to source', 'egyptfoss' ); ?></label>
                    <input class="form-control" type="text" id="link_to_source"  
                      <?php 
                      $link_to_source = get_field("link_to_source",$getParams["product"]);
                      if($link_to_source){
                                echo "value=\"" .$link_to_source . "\""; 
                      } ?> name="link_to_source" placeholder="<?php _e( 'http://www.example.com', 'egyptfoss' ); ?>"/>
              </div>
            </div>

            <!-- post taxonomy -->
            <?php
            global $ef_multi_add_edit_product_taxs;
            foreach($ef_multi_add_edit_product_taxs as $taxonomy)
            {
            ?>
            <div class="form-group row string post_<?php echo $taxonomy ?>">
                <div class="col-md-12">
                    <label for="post_<?php echo $taxonomy ?>" class="label"><?php _e(ucfirst($taxonomy), 'egyptfoss' ); ?></label>
                    <select 
                        <?php
                        global $ef_product_multi_uncreated_tax;
                        if(!in_array($taxonomy, $ef_product_multi_uncreated_tax) ){ ?>data-tags="true" <?php } ?>
                        class="add-product-tax form-control L-validate_taxonomy" 
                        id="post_<?php echo $taxonomy ?>" 
                        name="post_<?php echo $taxonomy ?>[]" 
                        data-placeholder="<?php _e( 'Select', 'egyptfoss' ); ?>" 
                        style="width:100%; visibility: hidden;" 
                        multiple="multiple">
                        
                        <optgroup>
                            <?php
                                $post_taxonomies = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );
                                $chosen_taxs = get_field($taxonomy,$getParams["product"]);
                                foreach ($post_taxonomies as $post_tax) {
                                  if(in_array($post_tax->term_id, $chosen_taxs)){
                                    echo("<option value='".$post_tax->slug."' selected >");
                                  }else
                                  {
                                    echo("<option value='".$post_tax->slug."'>");
                                  }
                                    _e("$post_tax->name", "egyptfoss");
                                    echo ("</option>");
                                }
                            ?>
                    </optgroup>
                    </select>
                </div>
            </div>
            <?php } ?>
            <div class="form-group row">
            <div class="col-md-12">
            <input type="submit" class="btn btn-primary rfloat" value="<?php _e("Save","egyptfoss") ?>" tabindex="40" id="submit" name="submit" />
            </div>
            </div>
            <input type="hidden" name="postid" value="<?php echo $getParams["product"]; ?>" />
            <input type="hidden" name="action" value="edit_product" />
            <?php wp_nonce_field( 'edit-product' ); ?>
        </form>
                    </div><!-- .entry-content -->
                </div><!-- #post-## -->
            </div><!-- #content -->
            </main><!-- #main -->
  </div><!-- #primary -->
  </div>
</div>
<script>
jQuery(document).ready(function ($) {
    $.getScript( "<?php echo get_template_directory_uri();?>/js/filer-template.js", function() {
$('#filer_input').filer({
    changeInput: changeInput,
    showThumbs: true,
    theme: "dragdropbox",
    templates: {
        box: template_box,
        item: template_item,
        itemAppend: template_append,
        progressBar: '<div class="bar"></div>',
        itemAppendToEnd: false,
        removeConfirmation: false,
        extensions: ['jpg', 'jpeg', 'png'],
        _selectors: {
            list: '.jFiler-items-list',
            item: '.jFiler-item',
            progressBar: '.bar',
            remove: '.jFiler-item-trash-action'
        }
    },
    dragDrop: {
        dragEnter: null,
        dragLeave: null,
        drop: null,
    },
    captions: {
        button: "Choose Files",
        feedback: "Choose files To Upload",
        feedback2: "files were chosen",
        drop: "Drop file here to Upload",
        removeConfirmation: "<?php echo _e("Are you sure you want to delete this?", "buddypress"); ?>",
        errors: {
            filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
            filesType: "Only Images are allowed to be uploaded.",
            filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
            filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
        }
    },
    files: <?php echo json_encode($screenshots );?>,       
    addMore: true,
    onRemove: function(){
        if($("#current_id").val() != '')
            $("#screens_ids").val($("#screens_ids").val() + $("#current_id").val() + ",");
    }
}); });
});
</script>
<?php get_footer(); 
?>
