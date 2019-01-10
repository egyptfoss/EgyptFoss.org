<?php
/**
 * Template Name: Add Success Story
 *
 * @package egyptfoss
 */

if (!empty($_POST['action']) && $_POST['action'] == "add_success_story") {
  $nonce = $_REQUEST['_wpnonce'];
  if ( ! wp_verify_nonce( $nonce, 'add-success-story' ) ) {
    wp_redirect( home_url( '/?status=403' ) );
    exit;
  }
  $success_story_id = ef_add_success_story_front_end();
  $ef_success_story_messages = get_query_var("ef_success_story_messages");
  if(!isset($ef_success_story_messages['errors'])) {
    $isGrantedToPublish = false;
    $efb_permissions = EFBBadgesUsers::getBadgesPermByUser(get_current_user_id());   
    if($efb_permissions)
    {
      foreach ($efb_permissions as $perm) {  
        $permTypes = split("__", $perm["granted_permission"]);
        if (in_array("success_story", $permTypes)) {
          $isGrantedToPublish = true;
        }
      }
    }
    if($isGrantedToPublish){
      setMessageBySession("ef_story_messages", "success", array($_POST['success_story_title'] .' '.__("added successfully",'egyptfoss'))) ;
    }else{
      setMessageBySession("ef_story_messages", "success", array($_POST['success_story_title'] .' '._x("Added successfully, it is now under review","feminist",'egyptfoss'))) ;
    }
    wp_redirect( get_permalink( $success_story_id ) );
    exit;
  }
}
get_header(); 
 ?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->
<div class="container">
	<div class="row">
	  <div id="primary" class="content-area col-md-12">
		  <main id="main" class="site-main" role="main">
<body>
    <div id="content" role="main">
      <div class="new-coupon-form">
        <form id="add_success_story" name="add_success_story" method="post" action="" enctype="multipart/form-data">
          <div class="required">
            <?php
            $ef_success_story_messages = get_query_var("ef_success_story_messages");
            $success_story_title = $success_story_description = "";
            if(isset($ef_success_story_messages['errors'])) {
              $success_story_title = (isset($_POST['success_story_title']))?$_POST['success_story_title']:"";
              $success_story_description = (isset($_POST['success_story_description']))?$_POST['success_story_description']:"";  
              $success_story_category = (isset($_POST['post_category']))?$_POST['post_category']:"";  
              $success_story_interests_posted = (isset($_POST['interest']))?$_POST['interest']:"";  
            ?>
            <div class="alert alert-danger"><?php
            foreach($ef_success_story_messages['errors'] as $error ) {
              echo "<i class='fa fa-warning'></i> " . $error . "<br/>";
            }
            ?>
            </div>
            <?php }
            if(isset($ef_success_story_messages['success'])) { ?>
            <div class="alert alert-success"><?php
              foreach($ef_success_story_messages['success'] as $success ) {
              echo "<i class='fa fa-check'></i> " . $success . "<br/>";
            } ?>
            </div>
            <?php }
            set_query_var("ef_success_story_messages", array()); ?>
          </div>
          <?php include(locate_template('template-parts/add-form-intro.php')); ?>
          <div class="form-group row">
          	<div class="col-md-12">
              <label for="success_story_title" class="label">
                <?php _e( 'Title', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
              </label>
              <input class="form-control" type="text" id="success_story_title" value="<?php echo $success_story_title ?>" name="success_story_title" />
              <div id="success_story_title_validate"></div>
          	</div>
          </div>
          <div class="form-group row">
            <div class="col-md-12">
              <label for="description" class="label">
                <?php _e('Content', 'egyptfoss'); ?> <?php _e('(required)', 'egyptfoss'); ?>
              </label>
              <textarea rows="15" class="form-control" name="success_story_description" id="success_story_description"><?php echo $success_story_description ?></textarea>
              <div id="success_story_description_validate"></div>
            </div>
          </div>
            <div class="form-group row">
        	<div class="col-md-12">
                <?php
                  $taxonomy = 'success_story_category';
                 // $tax_terms = get_terms($taxonomy, array('hide_empty' => false));
                ?>
                    
                <label for="post_category" class="label"><?php _e( 'Category', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
                <select class="form-control" id="post_category" name="post_category" style="width:100%; ">
                  <optgroup>
                    <option value="" <?php echo (!isset($success_story_category)?"selected":""); ?> disabled><?php _e( 'Select', 'egyptfoss' ); ?></option>
                    <?php
                      $orderBy = (pll_current_language() == "en")?"name":"name_ar";
                      $post_categories = get_terms( $taxonomy, array( 'hide_empty' => 0,
                        'orderby' => $orderBy,
                    'order' => 'ASC') );
                      foreach ($post_categories as $category) {
                          if($success_story_category == $category->slug)
                              $selected = "selected";
                          else 
                              $selected = "";
                        echo("<option value='".$category->slug."' $selected >");
                        _e("$category->name", "egyptfoss");
                        echo ("</option>");
                      } ?>
                  </optgroup>
                </select>
                <div id="post_category_validate"></div>
                    </div>
            </div>         
          <div class="form-group row">
            <div class="col-md-12">
              <label for="" class="label">
                <?php echo __( 'Image', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
              </label>
              <div class="input-group image-preview">
                      <input type="text" class="form-control image-preview-filename" disabled="disabled">
                      <span class="input-group-btn">
                              <!-- image-preview-clear button -->
                              <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                                      <span class="icons icon-cancel"></span> <?php _e('Clear','egyptfoss'); ?>
                              </button>
                              <!-- image-preview-input -->
                              <div class="btn btn-default image-preview-input">
                                      <span class="icons icon-folder-open"></span>
                                      <span class="image-preview-input-title"><?php _e("Browse","egyptfoss"); ?></span>
                                      <input type="file" accept="image/png, image/jpeg, image/gif" name="success_story_image" id="success_story_image" title="<?php _e( 'please enter correct image type', 'egyptfoss' ); ?>"/>
                                      <?php 
                                      
                                      wp_nonce_field( 'success_story_image', 'success_story_image_nonce' ); ?>
                              </div>
                      </span>
              </div>
              <div id="success_story_image_validate"></div>
            </div>
          </div>
          <!--
          <div class="form-group row">
            <div class="col-md-12 screenshot-uploader">
              <label for="product_title" class="label"><?php _e( 'Gallery', 'egyptfoss' ); ?></label>
              <input type="file" style="visibility:hidden;" name="files[]" id="filer_input" multiple="multiple" accept="image/*">
            </div>
          </div>
           -->
            <!-- interest -->
            <div class="form-group row string post_interest">
              <div class="col-md-12">
                <label for="interest" class="label"><?php _e( 'Related interests', 'egyptfoss' ); ?></label>
                <select data-tags="true" class="add-product-tax form-control L-validate_taxonomy" id="interest" name="interest[]" data-placeholder="<?php _e( 'Select', 'egyptfoss' ); ?>" style="width:100%; visibility: hidden;" multiple="multiple">
                  <optgroup>
                    <?php
                      $interests = get_terms( 'interest', array( 'hide_empty' => 0 ) );
                      $success_story_interests = is_array($success_story_interests_posted) ? $success_story_interests_posted : array() ;
                      foreach ($interests as $interest) {
                        if(in_array($interest->name, $success_story_interests)) {
                          $selected = 'selected';
                          $key = array_search($interest->name, $success_story_interests);
                          unset($success_story_interests[$key]);
                        } else {
                          $selected = '';
                        }
                        echo("<option value='".$interest->name."' $selected>");
                        echo($interest->name);
                        echo("</option>");
                      }
                      foreach ($success_story_interests as $interest) {
                        echo("<option value='".$interest."' selected>");
                        echo($interest);
                        echo ("</option>");
                      }
                    ?>
                  </optgroup>
                </select>
                <span id="interest-error" class="error" style="display:none;"><?php _e('Invalid interest.','egyptfoss') ?></span>
              </div>
            </div>  
            
          <div class="form-group row">
            <div class="col-md-12">
              <input type="submit" id="submit_success_story" class="btn btn-primary rfloat" value="<?php echo __("Save","egyptfoss"); ?>" tabindex="40" id="submit" name="submit" />
            </div>
          </div>
          <?php wp_nonce_field( 'add-success-story' ); ?>
          <input type="hidden" name="action" value="add_success_story" />
        </form>
      </div><!-- .entry-content -->
    </div><!-- #post-## -->
    </div><!-- #content -->
  </main><!-- #main -->
	</div><!-- #primary -->
	</div>
<!--
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
          addMore: true
      });
      });
  });
</script>
-->
<?php get_footer(); 
?>
