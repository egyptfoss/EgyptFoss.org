<?php

/**
 * Template Name: Edit Request center
 *
 * @package egyptfoss
 */

if ( !is_user_logged_in() ) {
    $current_url = home_url( pll_current_language()."/login/?redirected=editrequestcenter&redirect_to=".get_current_lang_page_by_template("template-edit-request-center.php")."?rid=".$_GET['rid'] );
    wp_redirect( home_url( get_current_lang_page_by_template('template-login.php')."?redirected=editrequestcenter&redirect_to={$current_url}" ) );
    exit;
} else if (!current_user_can('add_new_ef_posts')) {
    //wp_redirect( home_url( '?action=unauthorized' ) );
    wp_redirect(home_url('/?status=403'));
    exit;
  }
$request_id = -1;
if(is_numeric($_GET["rid"]))
{
    $request_id = $_GET["rid"];
}

if (!empty($_POST['action']) && $_POST['action'] == "edit_request_center" && isset($_POST['postid'])) {
    
    if($_POST['postid'] == -1 || !is_numeric($_POST['postid']))
    {
        echo "No Request Found !";
        exit;
    }
    
    //check existance
    global $wpdb;
    $sql = "select ID from $wpdb->posts where  post_type='request_center' and (post_status='pending') and ID = %s";
    $checkExistance = $wpdb->get_col($wpdb->prepare($sql, $_POST['postid']));
    if (!$checkExistance) {
        echo "No Request Found !";
        exit;
    }
    
    $nonce = $_REQUEST['_wpnonce'];
    if ( ! wp_verify_nonce( $nonce, 'edit-request-center' ) ) {
        wp_redirect( home_url( '/?status=403' ) );
        exit;
    }
    //save edited request
    ef_save_request_center_frontEnd();
}

if($request_id != -1)
{
    global $wpdb;
    $sql = "select ID from $wpdb->posts where  post_type='request_center' and (post_status='pending') and ID = %s";
    $checkExistance = $wpdb->get_col($wpdb->prepare($sql, $request_id));
    if (!$checkExistance) {
        echo "No Request Found !";
        exit;
    }
    
    $post = get_post($request_id);
    $author_id = $post->post_author;
    if($author_id != get_current_user_id())
    {
        //wp_redirect( home_url( '?action=unauthorized' ) );
        wp_redirect(home_url('/?status=403'));
        exit;
    }
    
    //valid request: retrieve its info
    $ef_request_center_title = get_the_title($request_id);
    $ef_target_bussiness_relationship = get_post_meta($request_id, "target_bussiness_relationship", true);
    $ef_type = get_post_meta($request_id, "request_center_type", true);
    $ef_theme = get_post_meta($request_id, "theme", true);
    $ef_description = get_post_meta($request_id, "description", true);
    $ef_requirements = get_post_meta($request_id, "requirements", true);
    $ef_constraints = get_post_meta($request_id, "constraints", true);
    $ef_deadline = get_post_meta($request_id, "deadline", true);
    $chosen_technologies = get_field('technology',$request_id);
    $chosen_interests = get_field('interest',$request_id);
}else
{
    echo "No Request Found !";
    exit;
}

get_header(); 
 ?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <h1 class="entry-title"><?php echo __("Edit","egyptfoss").' '.__("Request","egyptfoss"); ?> </h1>
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
    <div id="content" role="main">
      <div class="new-coupon-form">
        <form id="edit_request_center" name="edit_request_center" method="post" action="" enctype="multipart/form-data">
            <?php
            $messages = get_query_var("ef_request_center_messages");
            if(isset($messages['errors'])){
            ?>
            <div class="alert alert-danger"><?php
            foreach($messages['errors'] as $error ) {
              echo "<i class='fa fa-warning'></i> " . $error . "<br/>";
            }
            ?>
            </div>  
            <?php 
            }
            if(isset($messages['success'])) { ?>
            <div class="alert alert-success"><?php
              foreach($messages['success'] as $success ) {
              echo "<i class='fa fa-check'></i> " . $success . "<br/>";
            } ?>
            </div>
            <?php }
            set_query_var("ef_success_story_messages", array()); ?>
          
          <div class="form-group row">
              <div class="col-md-12">
                  </div>
          	<div class="col-md-12">
              <label for="request_center_title" class="label">
                <?php _e( 'Title', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
              </label>
                    <input class="form-control" type="text" id="request_center_title" value="<?php echo $ef_request_center_title; ?>" name="request_center_title" />
          	</div>
          </div>
          <?php  $ef_request_center_singular_terms = array(
                                        'request_center_type'          =>"Type",
                                        'target_bussiness_relationship'=>"Target business relationship",
                                        'theme'          =>"Theme"); 
                 foreach($ef_request_center_singular_terms as $term_slug=>$term_label)
                 {
            ?> 
          <div class="form-group row">
                <div class="col-md-12">
                    <label for="post_type" class="label"><?php echo __($term_label, 'egyptfoss'); ?>
                    <?php ($term_slug != "theme")?_e('(required)', 'egyptfoss'):""; ?>
                    </label>
                    <select class="form-control" name="<?php echo $term_slug?>" id="<?php echo $term_slug?>" style="width:100%;" >
                        <optgroup>
                            <option value="" ><?php _e( 'Select', 'egyptfoss' ); ?></option>
                            <?php
                            $terms = get_terms($term_slug, array('hide_empty' => 0));
                            foreach ($terms as $term) {
                              if($term->term_id == $ef_target_bussiness_relationship
                                      || $term->term_id == $ef_type || $term->term_id == $ef_theme)
                                $selected = "selected";
                              else 
                                $selected = "";
                                echo("<option value='" . $term->slug . "' $selected >");
                              _e("$term->name", "egyptfoss");
                              echo ("</option>");
                            }
                            ?>
                        </optgroup>
                    </select>
                </div>
          </div>
            
                 <?php } ?> 
          
            <?php $textareas = array('description'=>true,'requirements'=>false,'constraints'=>false);
            foreach($textareas as $textarea=>$isrequired)
            {
            ?>
          <div class="form-group row">
            <div class="col-md-12">
                <label for="<?php echo $textarea ?>" class="label">
                <?php echo __(ucfirst($textarea), 'egyptfoss'); ?> 
                <?php ($isrequired)?_e('(required)', 'egyptfoss'):""; ?>
              </label>
                <?php 
                    $current_text_area = "";
                    if($textarea == "description"){
                        $current_text_area = $ef_description;
                    }else if($textarea == "requirements")
                    {
                        $current_text_area = $ef_requirements;
                    }else if($textarea == "constraints")
                    {
                        $current_text_area = $ef_constraints;
                    }
                    ?>
                <textarea rows="10" class="form-control" name="request_center_<?php echo $textarea ?>" id="request_center_<?php echo $textarea ?>"><?php echo $current_text_area; ?></textarea>
            </div>
          </div>
            <?php } ?>
            
            
            <?php $ef_request_center_multi_terms = array('technology'=>"Technologies",'interest'=>"Related interests");
            foreach($ef_request_center_multi_terms as $term_slug=>$term_label)
            {
            ?>
            <div class="form-group row" id="filter-<?php echo $term_slug ?>">
              <div class="col-md-12">
                  <label for="<?php echo $term_slug ?>" class="label"><?php _e( $term_label, 'egyptfoss' ); ?></label>
                  <select data-tags="true" class="add-product-tax form-control" id="<?php echo $term_slug ?>" name="<?php echo $term_slug ?>[]" data-placeholder="<?php _e( 'Select', 'egyptfoss' ); ?>" style="width:100%; visibility: hidden;" multiple="multiple">
                  <optgroup>
                    <?php
                      $terms = get_terms( $term_slug, array( 'hide_empty' => 0 ) );
                      if($term_slug == 'technology'){
                        $chosen = $chosen_technologies;
                      }
                      else if($term_slug == 'interest'){
                          $chosen = $chosen_interests;
                      }
                      foreach($terms as $term)
                      { ?>
                      <option value="<?php echo $term->slug ?>" 
                        <?php if(in_array($term->term_id, $chosen))
                        {?>
                        selected="selected"
                        <?php } ?>>
                      <?php _e("$term->name", "egyptfoss"); ?>
                      </option>
                      <?php } ?>
                  </optgroup>
                </select>
                  <span for="" class="error" id="<?php echo $term_slug."_error" ?>"> </span>
              </div>
                
              
            </div>  
            <?php } ?>
          <div class="form-group row">
                <div class="col-md-12">
                  <label for="" class="label"><?php _e( 'Deadline', 'egyptfoss' ); ?></label>
                  <input type="text" value="<?php echo $ef_deadline; ?>" placeholder="<?php echo sprintf(__("Please choose your %s","egyptfoss"),  lcfirst(__( 'Deadline', 'egyptfoss' ))); ?>" id="request_center_deadline" name="request_center_deadline" class="form-control deadline-date-picker" value="">
                </div>
                 
              </div>
            
          <div class="form-group row">
            <div class="col-md-12">
              <input type="submit" class="btn btn-primary rfloat" value="<?php echo __("Edit","egyptfoss"); ?>" tabindex="40" id="submit" name="submit" />
            </div>
          </div>
          <input type="hidden" name="postid" value="<?php echo $request_id; ?>" />
          <input type="hidden" name="action" value="edit_request_center" />
          <?php wp_nonce_field( 'edit-request-center' ); ?>
        </form>
      </div><!-- .entry-content -->
    </div><!-- #post-## -->
   
     </main><!-- #main -->
    </div><!-- #content -->
   
 
	</div><!-- #primary -->
	</div>


<?php get_footer(); 
?>
