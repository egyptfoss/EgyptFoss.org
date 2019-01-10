<?php
/**
 * Template Name: Service Single.
 *
 * @package egyptfoss
 */
$getParams = $_GET;

$lang = get_locale();
if($lang == 'ar') {
  global $ar_sub_types;
  $sub_types = $ar_sub_types;
} else {
  global $en_sub_types;
  $sub_types = $en_sub_types;
}

get_header();
$service_id = get_the_ID();
$count = getThreadsCount($service_id);
$reviewers_count = get_post_meta($service_id, 'reviewers_count', true);
$reviewers_count = ($reviewers_count == NULL) ? 0 : $reviewers_count;
$rate = get_post_meta($service_id, 'rate', true);
$rate = ($rate == NULL) ? 0 : $rate;
?>

<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-12">
        <?php the_title( '<h1 class="entry-title" property="name">', '</h1>' ); ?>
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->

<div class="container">
  <div class="row">
    <?php if($post->post_status == 'archive') { ?>
      <div class="alert alert-warning"><i class="fa fa-archive"></i>
        <?php _e('Service is archived, no further requests.','egyptfoss'); ?>
      </div>
    <?php } ?>
    <div class="alert alert-success request-archived" style="display:none;">
      <i class='fa fa-check'></i>
      <?php _e('Service Archived Successfully', 'egyptfoss'); ?>
      <br/>
    </div>
    <div class="alert alert-danger request-not-archived" style="display:none;">
      <i class='fa fa-warning'></i>
      <?php _e('Error Archiving Service', 'egyptfoss'); ?>
      <br/>
    </div>
    <?php $edit_service = getMessageBySession("edit-service");
    if(!empty($edit_service)) { ?>
      <div class="alert alert-success">
        <i class='fa fa-check'></i>
        <?php echo $edit_service['success']; ?>
        <br/>
      </div>
    <?php } ?>

    <?php $add_service = getMessageBySession("add-service");
    if(!empty($add_service)) { ?>
      <div class="alert alert-success">
        <i class='fa fa-check'></i>
        <?php echo $add_service['success']; ?>
        <br/>
      </div>
    <?php } ?>
  </div>
  <?php if( is_user_logged_in() && $post->post_author == get_current_user_id() && $post->post_status != 'archive') { ?>
    <div class="row respond-btns-parent">
      <div class="col-md-12">
        <div class="respond-btns rfloat">
        <?php if($post->post_author == get_current_user_id() && !current_user_can('add_new_ef_posts')) { ?>
          <a href="javascript:void(0)" class="btn btn-light disabled" data-toggle="tooltip" data-placement="top" title="<?php _e('You are not authorized to perform this action. Please contact us for more information.', 'egyptfoss'); ?>">
            <i class="fa fa-pencil"></i>
            <?php _e("Edit","egyptfoss"); ?>
          </a>
        <?php } else if($post->post_author == get_current_user_id() && $post->post_status != 'archive') { ?>
          <a href="<?php echo get_current_lang_page_by_template("MarketPlace/template-edit-service.php")."?sid=".$post->ID ?>" class="btn btn-light">
            <i class="fa fa-pencil"></i>
            <?php _e("Edit","egyptfoss"); ?>
          </a>
        <?php } ?>

        <?php if(is_user_logged_in() && ($post->post_status != 'archive') && ($post->post_author == get_current_user_id())) { ?>
          <?php if (!current_user_can('add_new_ef_posts')) { ?>
            <a href="javascript:void(0)" class="btn btn-light disabled rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e('You are not authorized to perform this action. Please contact us for more information.', 'egyptfoss'); ?>"><i class="fa fa-archive"></i> <?php _e('Archive','egyptfoss'); ?></a>
          <?php } else if($post->post_status == 'publish') { ?>
            <a href="#" data-toggle="modal" data-target="#archive-request-modal" class="btn btn-light archive-request-button" title="<?php _e('Archiving a service will prevent further requests and you can not undo this action.','egyptfoss'); ?>"><i class="fa fa-archive"></i> <?php _e('Archive','egyptfoss'); ?></a>
            <div class="modal fade" id="archive-request-modal" tabindex="-1" role="dialog">
              <div class="modal-dialog modal-md">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php _e('Archive Service','egyptfoss'); ?></h4>
                  </div>
                  <div class="modal-body">
                    <div class="row form-group">
                      <div class="col-md-12">
                        <?php _e('Archiving a service will prevent further requests and you can not undo this action.','egyptfoss'); ?>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><?php _e('Cancel','egyptfoss'); ?></button>
                    <button type="button" name="confirm-archive-request" class="btn btn-primary archive-request" id="<?php echo $post->ID ?>" data-dismiss="modal"><i class="fa fa-archive"></i> <?php _e('Archive','egyptfoss'); ?></button>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
        <?php } ?>
        </div>
      </div>
    </div>
  <?php } ?>

	<div class="row" vocab="http://schema.org/" typeof="Service">
    <div id="primary" class="col-md-8">
      <div class="row">
        <div class="col-md-12 content-area">
          <div class="service-rating">
            <span class="provider-rating rating-readonly" data-rating="<?php echo $rate; ?>" title="<?php echo round($rate, 2); ?>">
            </span>
            <span class="rating-count" title="<?php echo __('Rated by ','egyptfoss') .' '. $reviewers_count .' '. __(' customers','egyptfoss') ?>">
              <?php if( $reviewers_count > 0 ): ?>
                <a href="#reviews" class="custom-scroll">(<?php echo $reviewers_count ?>)</a>
              <?php endif; ?>
            </span>
          </div>
          <div class="service-cover full">
            <?php if (has_post_thumbnail($post)) { ?>
              <img src="<?php echo get_the_post_thumbnail_url($post); ?>" class="post-img" alt="" property="image">
            <?php } ?>
          </div>
          <div class="service-content">
            <?php if(!empty($post->post_content)) { ?>
              <h3 property="description"><?php _e('Description','egyptfoss'); ?></h3>
              <p class="expand-text-large"><?php echo nl2br(do_shortcode($post->post_content));  ?></p>
            <?php } ?>

            <?php $constraints = get_post_meta($post->ID, 'constraints', true);
            if(!empty($constraints)) { ?>
              <h3><?php _e('Constraints','egyptfoss'); ?></h3>
              <p class="expand-text-large"><?php echo nl2br($constraints);  ?></p>
            <?php } ?>

            <?php $conditions = get_post_meta($post->ID, 'conditions', true);
            if(!empty($conditions)) { ?>
              <h3><?php _e('Conditions','egyptfoss'); ?></h3>
              <p class="expand-text-large"><?php echo nl2br($conditions);  ?></p>
            <?php } ?>
          </div>
        </div>
      </div>

      <?php if($reviewers_count > 0) { ?>
        <div class="row">
          <div class="col-md-12 reviews-area" id="reviews">
            <h3><?php _e('Reviews','egyptfoss'); ?> (<?php echo $reviewers_count; ?>)</h3>
            <?php $reviews = getRecentReviews($post->ID);
            foreach ($reviews as $review) {
              $reviewer_name = bp_core_get_user_displayname($review->reviewer_id);
              ?>
              <div class="review-panel clearfix">
                <div class="review-panel-header clearfix">
                  <img src="<?php echo bp_core_fetch_avatar( array( 'item_id' => $review->reviewer_id, 'html' => false ) ); ?>" class="user-avatar lfloat" alt="<?php echo $reviewer_name; ?>" />
                  <div class="reviewer-identity lfloat">
                    <h3>
                      <a href="<?php echo home_url() . "/members/" . bp_core_get_username($review->reviewer_id)."/about/" ?>">
                        <?php echo $reviewer_name; ?>
                      </a>
                    </h3>
                    <br>
                    <small class="post-date">
                      <i class="fa fa-clock-o"></i> <?php echo date('d/m/Y - h:i A', strtotime($review->created_at)); ?>
                    </small>
                  </div>
                  <div class="rating-stars rfloat">
                    <span class="provider-rating rating-readonly" data-rating="<?php echo $review->rate; ?>" title="<?php echo round($review->rate, 2); ?>">
                    </span>
                  </div>
                </div>
                <p><?php echo $review->review; ?></p>
              </div>
            <?php } ?>
            <?php if( $reviewers_count > 3 ): ?>
              <div class="view-all text-center">
                <a href="#" id="show-all-reviews" data-toggle="modal" data-target="#all-reviews-modal" class="btn btn-primary" title="<?php _e('View All Reviews','egyptfoss'); ?>">
                  <?php _e('View All Reviews','egyptfoss'); ?>
                </a>
              </div>
              <?php include(locate_template('MarketPlace/list-reviews-modal.php')); ?>
            <?php endif; ?>
          </div>
        </div>
      <?php } ?>

    </div>

  	<div class="col-md-4">
      <ul class="list-group basic-info-box" id="info-bar">
        <li class="list-group-item clearfix">
           <div class="provider-avatar lfloat">
              <?php echo get_avatar( $post->post_author, 32 ); ?>
           </div>
           <div class="user-name lfloat">
             <h3>
              <?php if(bp_core_get_username($post->post_author) != '') { ?>
              <a href="<?php echo home_url()."/members/".bp_core_get_username($post->post_author).'/about/' ?>" property="author">
                <?php echo bp_core_get_user_displayname($post->post_author); ?>
              </a>
              <?php } else { 
                echo bp_core_get_user_displayname($post->post_author); 
              }?>
             </h3>
             <br>
            <?php $author = get_registration_data($post->post_author);
            if (!empty($author['sub_type'])) { ?>
              <small><?php echo ((isset($author['sub_type']) && !empty($author['sub_type'])) ? $sub_types[$author['sub_type']] : ''); ?></small>
            <?php } ?>
           </div>
        </li>
        <?php $theme_id = get_post_meta($post->ID, 'theme', true);
        if(!empty($theme_id)) { ?>
          <li class="list-group-item">
            <strong><?php _e('Theme','egyptfoss'); ?>:</strong>
            <?php echo get_term_name_by_lang($theme_id, pll_current_language()); ?>
          </li>
        <?php } ?>
        <?php $category_id = get_post_meta($post->ID, 'service_category', true);
        if(!empty($category_id)) { ?>
          <li class="list-group-item" property="category">
            <?php $category = get_term( $category_id, 'service_category' ); ?>
            <strong><?php _e('Category','egyptfoss'); ?>:</strong>
            <?php echo get_term_name_by_lang($category_id, pll_current_language()); ?>
          </li>
        <?php } ?>
        <?php $technologies = get_field('technology', $post->ID, true);
        if(!empty($technologies)) { ?>
          <li class="list-group-item">
            <strong><?php _e('Technologies','egyptfoss'); ?>:</strong>
            <?php foreach ($technologies as $technology_id) {
              $technology = get_term( $technology_id, 'technology' );?>
              <span class="interest-badge"><?php echo $technology->name; ?></span>
            <?php } ?>
          </li>
        <?php } ?>
        <?php $interests = get_field('interest', $post->ID, true);
        if(!empty($interests)) { ?>
          <li class="list-group-item">
            <strong><?php _e('Interests','egyptfoss'); ?>:</strong>
            <?php foreach ($interests as $interest_id) {
              $interest = get_term( $interest_id, 'interest' );?>
              <span class="interest-badge"><?php echo $interest->name; ?></span>
            <?php } ?>
          </li>
        <?php } ?>
        <?php if(in_array($post->post_status, ['publish', 'archive'])) { ?>
          <li class="list-group-item">
            <div class="request-action">
              <?php if($post->post_author == get_current_user_id()) { // owner ?>
                <?php if($count == 0) { ?>
                  <?php if (current_user_can('add_new_ef_posts') ) { ?>
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#no-responses-modal" class="btn btn-primary btn-block disable" title="<?php _e('No requests','egyptfoss'); ?>">
                      <i class="fa fa-briefcase"></i> <?php _e('Requests','egyptfoss'); ?>
                    </a>
                  <?php } else { ?>
                    <a href="javascript:void(0)" class="btn btn-primary btn-block" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>">
                      <i class="fa fa-briefcase"></i> <?php _e('Requests','egyptfoss'); ?>
                    </a>
                  <?php } ?>
                <?php } else { ?>
                  <a href="<?php echo get_current_lang_page_by_template("template-service-thread.php")."?pid=".get_the_ID() ?>" class="btn btn-primary btn-block" title="<?php echo ownerUnseenThreads($post->ID).' '; _e("new responses","egyptfoss"); ?>">
                    <i class="fa fa-briefcase"></i>
                    <?php _e('Requests','egyptfoss'); ?>
                    <?php $new_responses = ownerUnseenThreads($post->ID); 
                    if ($new_responses > 0) { ?>
                      <span class="new-responses">
                        <?php echo $new_responses; ?>
                      </span>
                    <?php } ?>
                  </a>
                <?php }
                } else { // user
                  $thread = currentUserThread($post->ID);
                  if ($thread != null) { ?>
                    <a href="<?php echo get_current_lang_page_by_template("template-service-thread.php")."?pid=".get_the_ID() ?>" class="btn btn-primary btn-block" title="<?php ($thread->seen_by_user) ? _e('seen','egyptfoss') : _e('unseen','egyptfoss') ?>">
                      <i class="fa fa-briefcase"></i> <?php _e('Check your request','egyptfoss'); ?>
                      <?php if (!$thread->seen_by_user) { ?>
                        <span class="new-responses owner-notify"></span>
                      <?php } ?>
                    </a>
                  <?php } else { ?>
                    <?php if(!is_user_logged_in()) { ?>
                      <a href="<?php echo home_url( pll_current_language().'/login/?redirected=respondtoservice&redirect_to='.get_current_lang_page_by_template("template-service-thread.php")."?pid=".get_the_ID() ); ?>" class="btn btn-primary btn-block">
                        <i class="fa fa-briefcase"></i> <?php _e('Request Service','egyptfoss'); ?>
                      </a>
                    <?php } else if($post->post_status == 'archive') { ?>
                      <a href="#" data-toggle="modal" data-target="#cant-responsd" class="btn btn-primary btn-block" title="<?php _e("Service is archived, no further requests.","egyptfoss"); ?>">
                      <i class="fa fa-briefcase"></i> <?php _e('Request Service','egyptfoss'); ?></a>
                      <div class="modal fade" id="cant-responsd" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-md">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title"><?php _e('Service Archived','egyptfoss'); ?></h4>
                            </div>
                            <div class="modal-body">
                              <div class="row form-group">
                                <div class="col-md-12">
                                  <?php _e('Service is archived, no further requests.','egyptfoss'); ?>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-dismiss="modal"><?php _e('Ok','egyptfoss'); ?></button>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php } else { ?>
                      <?php if (!current_user_can('add_new_ef_posts') ) { ?>
                        <a href="javascript:void(0)" class="btn btn-primary btn-block" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>">
                          <i class="fa fa-briefcase"></i> <?php _e('Request Service','egyptfoss'); ?>
                        </a>
                      <?php } else if (!user_can($post->post_author, 'add_new_ef_posts') ) { ?>
                        <a href="javascript:void(0)" class="btn btn-primary btn-block" data-toggle="tooltip" data-placement="top" title="<?php _e("This service author is no longer active.", "egyptfoss"); ?>">
                          <i class="fa fa-briefcase"></i> <?php _e('Request Service','egyptfoss'); ?>
                        </a>
                      <?php } else { ?>
                        <a href="<?php echo get_current_lang_page_by_template("template-service-thread.php")."?pid=".get_the_ID() ?>" class="btn btn-primary btn-block" title="">
                          <i class="fa fa-briefcase"></i> <?php _e('Request Service','egyptfoss'); ?>
                        </a>
                      <?php } ?>
                    <?php } ?>
                  <?php }
                } ?>
            </div>
          </li>
        <?php } ?>
      </ul>
  	</div>

    <div class="modal fade" id="no-responses-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php _e('No requests','egyptfoss'); ?></h4>
          </div>
          <div class="modal-body">
            <div class="row form-group">
              <div class="col-md-12">
                <?php _e('The service has no requests','egyptfoss'); ?>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-dismiss="modal"><?php _e('Ok','egyptfoss'); ?></button>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<input type="hidden" id="displayed_container_id" value="<?php echo $post->ID; ?>" />
<?php get_footer();?>
