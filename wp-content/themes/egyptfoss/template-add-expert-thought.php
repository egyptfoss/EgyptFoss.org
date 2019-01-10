<?php
/**
 * Template Name: Add Expert Thought
 *
 * @package egyptfoss
 */
if (!empty($_POST['action']) && $_POST['action'] == "add_expert_thought") {
  $nonce = $_REQUEST['_wpnonce'];
  if (!wp_verify_nonce($nonce, 'add-expert-thought')) {
    wp_redirect(home_url('/?status=403'));
    exit;
  }
  $isSaved = ef_add_expert_thought_front_end();
  if($isSaved)
  {
    setMessageBySession( 'ef_expert_thought_messages', 'success', __("Thought", "egyptfoss") . ' ' . __("added successfully, it is now under review", 'egyptfoss') );
    wp_redirect(get_post_permalink($isSaved));
    exit;
  }
  $ef_expert_thought_messages = get_query_var("ef_expert_thought_messages");
}
get_header();
?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
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
            <form id="expert_thought_form" name="expert_thought_form" method="post" action="" enctype="multipart/form-data">
              <div class="required">
                <?php
                $ef_expert_thought_messages = get_query_var("ef_expert_thought_messages");
                $expert_thought_title = $expert_thought_description = "";
                if (isset($ef_expert_thought_messages['errors'])) {
                  $expert_thought_title = (isset($_POST['expert_thought_title'])) ? $_POST['expert_thought_title'] : "";
                  $expert_thought_description = (isset($_POST['expert_thought_description'])) ? $_POST['expert_thought_description'] : "";
                  $expert_thought_interests_posted = (isset($_POST['interest'])) ? $_POST['interest'] : "";
                  ?>
                  <div class="alert alert-danger"><?php
                    foreach ($ef_expert_thought_messages['errors'] as $error) {
                      echo "<i class='fa fa-warning'></i> " . $error . "<br/>";
                    }
                    ?>
                  </div>
                  <?php
                }
                if (isset($ef_expert_thought_messages['success'])) {
                  ?>
                  <div class="alert alert-success"><?php
                    foreach ($ef_expert_thought_messages['success'] as $success) {
                      echo "<i class='fa fa-check'></i> " . $success . "<br/>";
                    }
                    ?>
                  </div>
                  <?php
                }
                set_query_var("ef_expert_thought_messages", array());
                ?>
              </div>
              <?php include(locate_template('template-parts/add-form-intro.php')); ?>
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="expert_thought_title" class="label">
                    <?php _e('Title', 'egyptfoss'); ?> <?php _e('(required)', 'egyptfoss'); ?>
                  </label>
                  <input class="form-control" type="text" id="expert_thought_title" value="<?php echo $expert_thought_title ?>" name="expert_thought_title" />
                  <div id="expert_thought_title_validate"></div>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="description" class="label">
                    <?php _e('Content', 'egyptfoss'); ?> <?php _e('(required)', 'egyptfoss'); ?>
                  </label>
                  <textarea rows="15" class="form-control" name="expert_thought_description" id="expert_thought_description"><?php echo $expert_thought_description ?></textarea>
                  <div id="expert_thought_description_validate"></div>
                </div>
              </div>

              <div class="form-group row">
                <div class="col-md-12">
                  <label for="" class="label">
                    <?php echo __('Image', 'egyptfoss'); ?>
                  </label>
                  <div class="input-group image-preview">
                    <input type="text" class="form-control image-preview-filename" disabled="disabled">
                    <span class="input-group-btn">
                      <!-- image-preview-clear button -->
                      <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                        <span class="icons icon-cancel"></span> <?php _e('Clear', 'egyptfoss'); ?>
                      </button>
                      <!-- image-preview-input -->
                      <div class="btn btn-default image-preview-input">
                        <span class="icons icon-folder-open"></span>
                        <span class="image-preview-input-title"><?php _e("Browse", "egyptfoss"); ?></span>
                        <input type="file" accept="image/png, image/jpeg, image/gif" name="expert_thought_image" id="expert_thought_image" title="<?php _e( 'please enter correct image type', 'egyptfoss' ); ?>"/>
                        <?php wp_nonce_field('expert_thought_image', 'expert_thought_image_nonce'); ?>
                      </div>
                    </span>
                  </div>
                  <div id="expert_thought_image_validate"></div>
                </div>
              </div>
              <!-- interest -->
              <div class="form-group row string post_interest">
                <div class="col-md-12">
                  <label for="interest" class="label"><?php _e('Related interests', 'egyptfoss'); ?></label>
                  <select data-tags="true" class="add-product-tax form-control L-validate_taxonomy" id="interest" name="interest[]" data-placeholder="<?php _e('Select', 'egyptfoss'); ?>" style="width:100%; visibility: hidden;" multiple="multiple">
                    <optgroup>
                      <?php
                      $interests = get_terms('interest', array('hide_empty' => 0));
                      $expert_thought_interests = is_array($expert_thought_interests_posted) ? $expert_thought_interests_posted : array();
                      foreach ($interests as $interest) {
                        if (in_array($interest->name, $expert_thought_interests)) {
                          $selected = 'selected';
                          $key = array_search($interest->name, $expert_thought_interests);
                          unset($expert_thought_interests[$key]);
                        } else {
                          $selected = '';
                        }
                        echo("<option value='" . $interest->name . "' $selected>");
                        echo($interest->name);
                        echo("</option>");
                      }
                      foreach ($expert_thought_interests as $interest) {
                        echo("<option value='" . $interest . "' selected>");
                        echo($interest);
                        echo ("</option>");
                      }
                      ?>
                    </optgroup>
                  </select>
                  <span id="interest-error" class="error" style="display:none;"><?php _e('Invalid interest.', 'egyptfoss') ?></span>
                </div>
              </div>  
              <div class="form-group row">
                <div class="col-md-12">
                  <input type="submit" class="btn btn-primary rfloat" value="<?php echo __("Save", "egyptfoss"); ?>" tabindex="40" id="submit" name="submit" />
                </div>
              </div>
              <?php wp_nonce_field('add-expert-thought'); ?>
              <input type="hidden" name="action" value="add_expert_thought" />
            </form>
          </div>
        </div>
      </main><!-- #main -->
    </div>
  </div>
</div>

<?php get_footer(); ?>
