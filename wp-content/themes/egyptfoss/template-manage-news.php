<?php
/**
 * Template Name: Manage News
 *
 * @package egyptfoss
 */

if (!empty($_POST['action']) && $_POST['action'] == "add_news") {
  $nonce = $_REQUEST['_wpnonce'];
  if ( ! wp_verify_nonce( $nonce, 'add-news' ) ) {
    wp_redirect( home_url( '/?status=403' ) );
    exit;
  }
  $news_id = ef_add_news_front_end();
  $ef_news_messages = $_SESSION["ef_news_messages"];
  if(!isset($ef_news_messages['error'])) {
    $efb_permissions = EFBBadgesUsers::getBadgesPermByUser(get_current_user_id());   
    if($efb_permissions)
    {
      foreach ($efb_permissions as $perm) {  
        $permTypes = split("__", $perm["granted_permission"]);
        if (in_array("news", $permTypes)) {
          setMessageBySession("ef_news_messages", "success", array($_POST["news_title"] .' '.__("added successfully",'egyptfoss'))) ;
          wp_redirect( get_permalink( $news_id ) );
          exit;
        }
      }
    }
    wp_redirect( get_permalink( $news_id ) );
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
        <form id="add_news" name="add_news" method="post" action="" enctype="multipart/form-data">
          <?php wp_nonce_field( 'add-news' ); ?>
          <div class="required">
            <?php
            $ef_news_messages = getMessageBySession("ef_news_messages");
            $news_title = $news_subtitle = $news_description = "";
            if(isset($ef_news_messages['error'])) {
              $news_title = (isset($_POST['news_title']))?$_POST['news_title']:"";
              $news_subtitle = (isset($_POST['news_subtitle']))?$_POST['news_subtitle']:"";
              $news_description = (isset($_POST['news_description']))?$_POST['news_description']:"";
              $news_category = (isset($_POST['news_category']))?$_POST['news_category']:"";  
              $success_story_interests_posted = (isset($_POST['interest']))?$_POST['interest']:"";  
            ?>
            <div class="alert alert-danger"><?php
            foreach($ef_news_messages['error'] as $error ) {
              echo "<i class='fa fa-warning'></i> " . $error . "<br/>";
            }
            ?>
            </div>
            <?php }
            if(isset($ef_news_messages['success'])) { ?>
            <div class="alert alert-success"><?php
              foreach($ef_news_messages['success'] as $success ) {
              echo "<i class='fa fa-check'></i> " . $success . "<br/>";
            } ?>
            </div>
            <?php }
            set_query_var("ef_news_messages", array()); ?>
          </div>
          <?php include(locate_template('template-parts/add-form-intro.php')); ?>
          <div class="form-group row">
          	<div class="col-md-12">
              <label for="news_title" class="label">
                <?php _e( 'Title', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
              </label>
              <input class="form-control" type="text" id="news_title" value="<?php echo $news_title ?>" name="news_title" />
              <div id="news_title_validate"></div>
          	</div>
          </div>
          <div class="form-group row">
          	<div class="col-md-12">
              <label for="news_subtitle" class="label">
                <?php _e( 'Subtitle', 'egyptfoss' ); ?>
              </label>
              <input class="form-control" type="text" id="news_subtitle" value="<?php echo $news_subtitle ?>" name="news_subtitle" />
              <div id="news_subtitle_validate"></div>
          	</div>
          </div>
          <div class="form-group row">
            <div class="col-md-12">
              <label for="description" class="label">
                <?php _e('Description', 'egyptfoss'); ?> <?php _e('(required)', 'egyptfoss'); ?>
              </label>
              <textarea rows="15" class="form-control" name="news_description" id="news_description"><?php echo $news_description ?></textarea>
              <div id="news_description_validate"></div>
            </div>
          </div>
            <div class="form-group row">
        	<div class="col-md-12">
                <?php
                  $taxonomy = 'news_category';
                  $tax_terms = get_terms($taxonomy, array('hide_empty' => false));
                ?>
                    
                <label for="news_category" class="label"><?php _e( 'Category', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
                <select class="form-control" id="news_category" name="news_category" style="width:100%; ">
                  <optgroup>
                    <option value="" <?php echo (!isset($news_category)?"selected":""); ?> disabled><?php _e( 'Select', 'egyptfoss' ); ?></option>
                    <?php
                      $post_categories = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );
                      foreach ($post_categories as $category) {
                          if($news_category == $category->slug)
                              $selected = "selected";
                          else 
                              $selected = "";
                        echo("<option value='".$category->slug."' $selected >");
                        _e("$category->name", "egyptfoss");
                        echo ("</option>");
                      } ?>
                  </optgroup>
                </select>
                <div id="news_category_validate"></div>
                    </div>
            </div>                
          <div class="form-group row">
            <div class="col-md-12">
              <label for="" class="label">
                <?php echo __( 'News image', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
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
                                      <input type="file" accept="image/png, image/jpeg, image/gif" name="news_image" id="news_image" title="<?php _e( 'please enter correct image type', 'egyptfoss' ); ?>"/> 
                                      <?php 
                                      
                                      wp_nonce_field( 'news_image', 'news_image_nonce' ); ?>
                              </div>
                      </span>
              </div>
              <div id="news_image_validate"></div>
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
              <input type="submit" class="btn btn-primary rfloat" value="<?php echo __("Add","egyptfoss"); ?>" tabindex="40" id="submit" name="submit" />
            </div>
          </div>
          <input type="hidden" name="action" value="add_news" />
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
