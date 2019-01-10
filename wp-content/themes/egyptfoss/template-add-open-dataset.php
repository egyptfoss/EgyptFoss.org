<?php

/**
 * Template Name: Add Open DataSet
 *
 * @package egyptfoss
 */

if (!empty($_POST['action']) && $_POST['action'] == "add_open_dataset") {
  $nonce = $_REQUEST['_wpnonce'];
  if ( ! wp_verify_nonce( $nonce, 'add-open-dataset' ) ) {
    wp_redirect( home_url( '/?status=403' ) );
    exit;
  }
  $open_dataset_id = ef_add_open_dataset_front_end();
  $ef_open_dataset_messages = get_query_var("ef_open_dataset_messages");
  if(!isset($ef_open_dataset_messages['errors'])) {
     $isGrantedToPublish = false;
    $efb_permissions = EFBBadgesUsers::getBadgesPermByUser(get_current_user_id());   
    if($efb_permissions)
    {
      foreach ($efb_permissions as $perm) {  
        $permTypes = split("__", $perm["granted_permission"]);
        if (in_array("open_dataset", $permTypes)) {
          $isGrantedToPublish = true;
        }
      }
    }
    if($isGrantedToPublish){
      setMessageBySession("ef_dataset_messages", "success", array($_POST['open_dataset_title'] .' '.__("added successfully",'egyptfoss'))) ;
    }else{
      setMessageBySession("ef_dataset_messages", "success", array($_POST['open_dataset_title'] .' '.__("added successfully, it is now under review",'egyptfoss'))) ;
    }
    wp_redirect( get_permalink( $open_dataset_id ) );
  }
}
get_header(); 
 ?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <h1 class="entry-title"><?php echo __("Suggest", "egyptfoss") .' '._n("Open Dataset","Open Datasets",0,"egyptfoss"); ?></h1>
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
<body>
    <div id="content" role="main">
      <div class="new-coupon-form">
        <form id="add_open_dataset" name="add_open_dataset" method="post" action="" enctype="multipart/form-data">
          <div class="required">
            <?php
            $ef_open_dataset_messages = get_query_var("ef_open_dataset_messages");
            $open_dataset_title = $open_dataset_description =
                    $open_dataset_usage = $open_dataset_publisher =
                    $open_dataset_link_to_source = $open_dataset_references = "";
            if(isset($ef_open_dataset_messages['errors'])) {
              $open_dataset_title = (isset($_POST['open_dataset_title']))?$_POST['open_dataset_title']:"";
              $open_dataset_description = (isset($_POST['open_dataset_description']))?$_POST['open_dataset_description']:"";  
              $open_dataset_usage = (isset($_POST['open_dataset_usage']))?$_POST['open_dataset_usage']:"";  
              $open_dataset_publisher = (isset($_POST['open_dataset_publisher']))?$_POST['open_dataset_publisher']:"";  
              $open_dataset_link_to_source = (isset($_POST['open_dataset_source']))?$_POST['open_dataset_source']:"";  
              $open_dataset_references = (isset($_POST['open_dataset_references']))?$_POST['open_dataset_references']:"";  
              $open_dataset_type = (isset($_POST['type']))?$_POST['type']:"";  
              $open_dataset_theme = (isset($_POST['theme']))?$_POST['theme']:"";  
              $open_dataset_license = (isset($_POST['license']))?$_POST['license']:"";  
              $success_story_interests_posted = (isset($_POST['interest']))?$_POST['interest']:"";  
              $open_dataset_published_date = (isset($_POST['open_dateset_published_date']))?$_POST['open_dateset_published_date']:"";  
            ?>
            <div class="alert alert-danger"><?php
            foreach($ef_open_dataset_messages['errors'] as $error ) {
              echo "<i class='fa fa-warning'></i> " . $error . "<br/>";
            }
            ?>
            </div>
            <?php }
            if(isset($ef_open_dataset_messages['success'])) { ?>
            <div class="alert alert-success"><?php
              foreach($ef_open_dataset_messages['success'] as $success ) {
              echo "<i class='fa fa-check'></i> " . $success . "<br/>";
            } ?>
            </div>
            <?php }
            set_query_var("ef_open_dataset_messages", array()); ?>
          </div>
          <?php include(locate_template('template-parts/add-form-intro.php')); ?>
          <input type="hidden" id="names" name="names" value="">
          <input type="hidden" id="sizes" name="sizes" value="">
          <div class="form-group row">
          	<div class="col-md-12">
              <label for="open_dataset_title" class="label">
                <?php _e( 'Title', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
              </label>
              <input class="form-control" type="text" id="open_dataset_title" value="<?php echo $open_dataset_title ?>" name="open_dataset_title" />
              <div id="open_dataset_title_validate"></div>
          	</div>
          </div>
          <div class="form-group row">
            <div class="col-md-12">
              <label for="description" class="label">
                <?php _e('Description', 'egyptfoss'); ?> <?php _e('(required)', 'egyptfoss'); ?>
              </label>
              <textarea rows="15" class="form-control" name="open_dataset_description" id="open_dataset_description"><?php echo $open_dataset_description; ?></textarea>
              <div id="open_dataset_description_validate"></div>
            </div>
          </div>
            <div class="form-group row">
                <div class="col-md-12">
                    <label for="open_dataset_publisher" class="label">
                      <?php _e( 'Publisher', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
                    </label>
                    <input class="form-control" type="text" id="open_dataset_publisher" value="<?php echo $open_dataset_publisher; ?>" name="open_dataset_publisher" />
                    <div id="open_dataset_publisher_validate"></div>
                </div>
            </div>
            <div class="form-group row">
        	<div class="col-md-12">
                <?php
                  $taxonomy = 'dataset_type';
                  $tax_terms = get_terms($taxonomy, array('hide_empty' => false));
                ?>
                    
                <label for="type" class="label"><?php _e( 'Type', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
                <select class="form-control" id="type" name="type" style="width:100%; ">
                  <optgroup>
                    <option value="" <?php echo (!isset($open_dataset_type)?"selected":""); ?> disabled><?php _e( 'Select', 'egyptfoss' ); ?></option>
                    <?php
                      $post_types = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );
                      foreach ($post_types as $type) {
                          if($open_dataset_type == $type->slug)
                              $selected = "selected";
                          else 
                              $selected = "";
                        echo("<option value='".$type->slug."' $selected >");
                        _e("$type->name", "egyptfoss");
                        echo ("</option>");
                      } ?>
                  </optgroup>
                </select>
                <div id="type_validate"></div>
                    </div>
            </div>   
            <div class="form-group row">
        	<div class="col-md-12">
                <?php
                  $taxonomy = 'theme';
                  $tax_terms = get_terms($taxonomy, array('hide_empty' => false));
                ?>
                    
                <label for="theme" class="label"><?php _e( 'Theme', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
                <select class="form-control" id="theme" name="theme" style="width:100%; ">
                  <optgroup>
                    <option value="" <?php echo (!isset($open_dataset_theme)?"selected":""); ?> disabled><?php _e( 'Select', 'egyptfoss' ); ?></option>
                    <?php
                      $post_themes = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );
                      foreach ($post_themes as $theme) {
                          if($open_dataset_theme == $theme->slug)
                              $selected = "selected";
                          else 
                              $selected = "";
                        echo("<option value='".$theme->slug."' $selected >");
                        _e("$theme->name", "egyptfoss");
                        echo ("</option>");
                      } ?>
                  </optgroup>
                </select>
                <div id="theme_validate"></div>
                    </div>
            </div> 
            <div class="form-group row">
        	<div class="col-md-12">
                <?php
                  $taxonomy = 'datasets_license';
                  $tax_terms = get_terms($taxonomy, array('hide_empty' => false));
                ?>
                    
                <label for="license" class="label"><?php _e( 'License', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
                <select class="form-control" id="license" name="license" style="width:100%; ">
                  <optgroup>
                    <option value="" <?php echo (!isset($open_dataset_license)?"selected":""); ?> disabled><?php _e( 'Select', 'egyptfoss' ); ?></option>
                    <?php
                      $post_licenses = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );
                      foreach ($post_licenses as $license) {
                          if( $license->slug == 'other' ) {
                            $other_term = $license->name;
                            if( pll_current_language() == 'ar' && $license->name_ar ) {
                              $other_term = $license->name_ar;
                            }
                            continue;
                          }
                          if($open_dataset_license == $license->slug)
                              $selected = "selected";
                          else 
                              $selected = "";
                        echo("<option value='".$license->slug."' $selected >");
                        _e("$license->name", "egyptfoss");
                        echo ("</option>");
                      } ?>
                    <?php if( isset( $other_term ) ): ?>
                      <option value="other" <?php selected($open_dataset_license, 'other'); ?>><?php echo $other_term; ?></option>
                    <?php endif; ?>
                  </optgroup>
                </select>
                <div id="license_validate"></div>
                    </div>
            </div> 
            <div class="form-group row">
                <div class="col-md-12">
                  <label for="open_dataset_usage" class="label">
                    <?php _e('Usage hints', 'egyptfoss'); ?>
                  </label>
                  <textarea rows="15" class="form-control" name="open_dataset_usage" id="open_dataset_usage"><?php echo $open_dataset_usage ?></textarea>
                  <div id="open_dataset_usage_validate"></div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-12">
                  <label for="open_dataset_references" class="label"> 
                    <?php _e('References', 'egyptfoss'); ?>  <?php _e( '(required)', 'egyptfoss' ); ?>
                  </label>
                  <textarea rows="15" class="form-control" name="open_dataset_references" id="open_dataset_references"><?php echo $open_dataset_references; ?></textarea>
                  <div id="open_dataset_references_validate"></div>
                </div>
            </div>   
            <div class="form-group row">
                <div class="col-md-12">
                    <label for="open_dataset_source" class="label">
                      <?php _e( 'Link to source', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
                    </label>
                    <input class="form-control" type="text" id="open_dataset_source" value="<?php echo $open_dataset_link_to_source; ?>" name="open_dataset_source" />
                    <div id="open_dataset_source_validate"></div>
                </div>
            </div>    
            <div class="form-group row">
              <div class="col-md-12 screenshot-uploader">
                <label for="open_dataset_resources" class="label"><?php _e( 'Resources', 'egyptfoss' ); ?></label>
                <input type="file" style="visibility:hidden;" name="open_dataset_resources[]" id="open_dataset_resources" multiple="multiple">
                <?php wp_nonce_field( 'open_dataset_resources', 'open_dataset_resources_nonce' ); ?>
                <div id="open_dataset_resources_validate"></div>
                <div class="upload-hint alert alert-warning">
                    <?php echo _e("HINT:","egyptfoss"). _e("Max Allowed Size per file: 20MB","egyptfoss")._e(" & ","egyptfoss")._e("Allowed formats : ","egyptfoss").implode(',',$extensions); ?>                 </div>
              </div>
            </div>            
            <!-- interest -->
            <div class="form-group row string post_interest">
              <div class="col-md-12">
                <label for="interests" class="label"><?php _e( 'Related interests', 'egyptfoss' ); ?></label>
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
                <label for="" class="label"><?php _e( 'Published date', 'egyptfoss' ); ?></label>
                <input type="text" id="open_dateset_published_date" name="open_dateset_published_date" class="form-control published-date-picker" value="<?php echo $open_dataset_published_date; ?>">
              </div>
            </div>
            
          <div class="form-group row">
            <div class="col-md-12">
              <input type="submit" class="btn btn-primary rfloat" value="<?php echo __("Save","egyptfoss"); ?>" tabindex="40" id="submit" name="submit" />
            </div>
          </div>
          <input type="hidden" name="action" value="add_open_dataset" />
          <?php wp_nonce_field( 'add-open-dataset' ); ?>
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
      $("#names").val('');
      $("#sizes").val('');
      $('#open_dataset_resources').filer({
          changeInput: changeInput,
          showThumbs: true,
          theme: "dragdropbox",
          onSelect: function(e){
              var newName = $("#names").val() +"|"+e.name;
              var newSize = $("#sizes").val() +"|"+e.size;
              $("#names").val(newName);
              $("#sizes").val(newSize);
            },
            onRemove: function(e,data)
            {
                var newName = $("#names").val().replace("|"+data.name,'');
                var newSize = $("#sizes").val().replace("|"+data.size,'');
                $("#names").val(newName);
                $("#sizes").val(newSize);
            },
          templates: {
              box: template_box,
              item: template_item,
              itemAppend: template_append,
              progressBar: '<div class="bar"></div>',
              itemAppendToEnd: false,
              removeConfirmation: false,
              extensions: ['jpg', 'jpeg', 'png','gif','xlsx','xlx','csv'],
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
<?php get_footer(); 
?>
