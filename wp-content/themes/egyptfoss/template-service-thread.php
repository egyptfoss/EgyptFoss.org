<?php
/**
 * Template Name: Service Thread.
 *
 * @package egyptfoss
 */

if ( is_user_logged_in()) {
  $service_id = $_GET['pid'];
  $thread_id = $_GET['tid'];
  if (!in_array(get_post_status($service_id), ["publish", "archive"])) {
    include( get_query_template( '404' ) );
    exit;
  }
  $service = get_post($service_id);
  $current_user = wp_get_current_user();
  if(!current_user_can('add_new_ef_posts'))
  {
    wp_redirect( home_url( '/?status=403' ) );
    exit; 
  }
  $user_type = ($service->post_author == $current_user->ID) ? 'owner' : 'user';
  $thread_data = validate_thread_container($service, $current_user, $thread_id);
  if(empty($thread_data)) {
    include( get_query_template( '404' ) );
    exit;
  } else {
    $displayed_thread = $thread_data['thread'];
    $thread_with = $thread_data['thread_with'];
    $responses = getThreadResponses($displayed_thread->id);
  }
  $show_form = true;
?>

<?php get_header(); ?>

<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1><?php echo get_the_title($service_id); ?></h1>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->

<div class="container">
  <div class="row content-area">
    <div class="alert alert-success thread-archived" style="display:none;">
      <i class='fa fa-check'></i>
      <?php _e('Request Archived Successfully', 'egyptfoss'); ?>
      <br/>
    </div>
    <div class="alert alert-danger thread-not-archived" style="display:none;">
      <i class='fa fa-warning'></i>
      <?php _e('Error Archiving Request', 'egyptfoss'); ?>
      <br/>
    </div>

    <?php if(($service->post_status == 'archive') && ($service->post_author == $current_user->ID)) { ?>
      <div class="alert alert-warning"><i class="fa fa-archive"></i>
        <?php _e('Service is archived, no further requests. The old requests are still active','egyptfoss'); ?>
      </div>
    <?php } else if($displayed_thread->status == 0) { ?>
      <?php $show_form = false; ?>
      <div class="alert alert-warning"><i class="fa fa-archive"></i>
        <?php _e('Request is archived, no further replies','egyptfoss'); ?>
      </div>
    <?php } ?>

    <?php if($service->post_author == $current_user->ID) { // owner ?>

      <?php if(!user_can($service->post_author, 'add_new_ef_posts')) { ?>
        <?php $show_form = false; ?>
        <div class="alert alert-warning"><i class="fa fa-archive"></i>
          <?php _e('You are not authorized to reply to this request. Please contact us for more information.','egyptfoss'); ?>
        </div>
      <?php } else if(!user_can($displayed_thread->user_id, 'add_new_ef_posts')) { ?>
        <?php $show_form = false; ?>
        <div class="alert alert-warning"><i class="fa fa-archive"></i>
          <?php _e('This Requester is no longer active.','egyptfoss'); ?>
        </div>
      <?php } ?>

      <?php $list = getThreadsList($service, $current_user); ?>
      <div class="col-md-3 responses-sidebar">
        <h3><?php _e('Requests','egyptfoss') ?></h3>
        <div class="nano">
          <ul class="responses-list-user nano-content">
            <?php foreach ($list as $thread_in_list) {
              $user_in_list_id = ($thread_in_list->owner_id != $current_user->ID) ? $thread_in_list->owner_id : $thread_in_list->user_id ;
              $user_in_list = get_user_by('id', $user_in_list_id);
            ?>
            <li>
              <a href="<?php echo get_current_lang_page_by_template('template-service-thread.php')."?pid=".$service->ID."&tid=".$thread_in_list->id ?>">
                <?php if ($thread_in_list->seen_by_owner) { ?>
                  <span class="read-indicator"><i class="fa fa-check-circle "></i></span>
                  <div class="user-cell read-response">
                <?php } else { ?>
                  <span class="unread-indicator"><i class="fa fa-check-circle "></i></span>
                  <div class="user-cell unread-response">
                <?php } ?>
                  <div class="avatar">
                    <img src="<?php echo bp_core_fetch_avatar( array( 'item_id' => $user_in_list->ID, 'html' => false ) ); ?>" alt="<?php echo $user_in_list->user_nicename; ?>" />
                  </div>
                  <div class="user-name">
                    <?php echo bp_core_get_user_displayname($user_in_list->ID); ?>
                    <br/>
                    <small><?php echo getThreadLastResponse($thread_in_list); ?></small>
                  </div>
                </div>
                <div class="last-message-date">
                  <span title="<?php echo date('d M, Y', strtotime($thread_in_list->updated_at)); ?>">
                    <?php echo date('d M', strtotime($thread_in_list->updated_at)); ?>
                  </span>
                </div>
              </a>
            </li>
            <?php } ?>
          </ul>
        </div>
      </div>
      <div class="single-request-content col-md-6">
    <?php } else { // user ?>

      <?php if(!user_can($current_user->ID, 'add_new_ef_posts')) { ?>
        <?php $show_form = false; ?>
        <div class="alert alert-warning"><i class="fa fa-archive"></i>
          <?php _e('You are not authorized to reply to this request. Please contact us for more information.','egyptfoss'); ?>
        </div>
      <?php } else if(!user_can($service->post_author, 'add_new_ef_posts')) { ?>
        <?php $show_form = false; ?>
        <div class="alert alert-warning"><i class="fa fa-archive"></i>
          <?php _e('This service author is no longer active.','egyptfoss'); ?>
        </div>
      <?php } ?>

      <div class="single-request-content col-md-9">
    <?php } ?>
      <div class="conv-body">
        <div class="conv-header">
          <div class="conv-avatar">
            <img src="<?php echo bp_core_fetch_avatar( array( 'item_id' => $thread_with->ID, 'html' => false ) ); ?>" alt="<?php echo $thread_with->user_nicename; ?>" />
          </div>
          <div class="conv-user-name">
            <h4><a href="<?php echo home_url() . "/members/" . bp_core_get_username($thread_with->ID)."/about/" ?>"><?php echo bp_core_get_user_displayname($thread_with->ID); ?></a></h4>
          </div>

          <?php if (!current_user_can('add_new_ef_posts') && ($service->post_author == $current_user->ID)) { ?>
            <div class="archive-thread">
              <a href="javascript:void(0)" class="btn btn-light disabled rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e('You are not authorized to perform this action. Please contact us for more information.', 'egyptfoss'); ?>"><i class="fa fa-archive"></i> <?php _e('Archive','egyptfoss'); ?></a>
            </div>
          <?php } else if( ($displayed_thread->status == 1) && ($service->post_author == $current_user->ID) && ($service->post_status != 'archive') ) { ?>
            <div class="archive-thread">
              <a href="javascript:void(0);" data-toggle="modal" data-target="#archive-thread-modal" class="btn btn-link archive-thread-button rfloat"><i class="fa fa-archive"></i> <?php _e('Archive', 'egyptfoss'); ?></a>
            </div>
            <div class="modal fade" id="archive-thread-modal" tabindex="-1" role="dialog">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php _e('Archive Request', 'egyptfoss'); ?></h4>
                  </div>
                  <div class="modal-body">
                    <div class="row form-group">
                      <div class="col-md-12">
                        <?php _e('Archiving a request will prevent further replies and you can not undo this action.','egyptfoss'); ?>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><?php _e('Cancel','egyptfoss'); ?></button>
                    <button type="button" name="confirm-archive-thread" class="btn btn-primary archive-my-thread" id="<?php echo $displayed_thread->id ?>" data-dismiss="modal"><?php _e('Archive','egyptfoss'); ?></button>
                  </div>
                </div>
              </div>
            </div>
          <?php } else if( ($displayed_thread->status == 0) && ($service->post_author == get_current_user_id())) { ?>
            <div class="archive-thread">
            	<a href="javascript:void(0);" class="btn btn-link rfloat disabled"><i class="fa fa-archive"></i> <?php _e('Archive', 'egyptfoss'); ?></a>
            </div>
          <?php } ?>
        </div>
        <div class="nano">
          <div class="conv-thread nano-content">
            <input type="hidden" id="me-myself" value="<?php _e('Me', 'egyptfoss'); ?>" />
            <?php 
            if(count($responses) == 0) { ?>
              <div class="empty-state-thread">
                <img src="<?php echo get_template_directory_uri(); ?>/img/empty_thread.svg" alt="empty_thread">
                  <p><?php _e('Start conversation by writing your request below', 'egyptfoss') ?></p>
                <img src="<?php echo get_template_directory_uri(); ?>/img/direction-arrow.svg" alt="arrow">
              </div>
            <?php } else {
              foreach ($responses as $response) {
                if($response->user_id == $current_user->ID) {
                  echo '<div class="response-row me">';
                  echo '<p>'.'<span class="user-name lfloat">';
                  _e('Me', 'egyptfoss');
                  echo ' : </span>'.$response->message.'</p>';
                } else {
                  echo '<div class="response-row you">';
                  echo '<p>'.'<span class="user-name lfloat">'; ?>
                  <a href="<?php echo home_url() . "/members/" . bp_core_get_username($thread_with->ID)."/about/" ?>"><?php echo bp_core_get_user_displayname($thread_with->ID); ?></a>
                <?php echo ' : </span>'.$response->message.'</p>';
                } ?>
                <div class="message-time-stamp">
                  <i class="fa fa-clock-o"></i> <?php echo date('d/m/Y - h:i A', strtotime($response->created_at)); ?>
                </div>
              </div>
              <?php }
            } ?>
          </div>
        </div>
        <?php if( $show_form ) { ?>
          <form id="add_response" name="add_response" method="post" action="">
            <?php wp_nonce_field( 'add_response' ); ?>
            <div class="form-group">
              <div class="conv-compose">
                <div class="message-text-composer">
                  <textarea id="message" name="message" rows="2" class="form-control" placeholder="<?php _e('Write your replay...','egyptfoss'); ?>" value=""></textarea>
                </div>
                <div class="options-btns clearfix">
                  <?php if (current_user_can('add_new_ef_posts') ) { ?>
                    <button type="submit" name="button" id="submit_response" class="btn btn-primary rfloat btn-sm">
                      <i class="fa fa-send"></i> <?php _e('Send','egyptfoss') ?>
                    </button>
                  <?php } else { ?>
                    <a href="javascript:void(0)" class="btn btn-primary rfloat btn-sm" data-toggle="tooltip" data-placement="top" title="<?php _e('You are not authorized to perform this action. Please contact us for more information.', 'egyptfoss'); ?>">
                      <i class="fa fa-send"></i> <?php _e('Send','egyptfoss') ?>
                    </a>
                  <?php } ?>
                </div>
              </div>
            </div>
          </form>
        <?php } ?>
      </div>
    </div>
    <div class="col-md-3">
      <div id="reviewer-section">
        <?php if( ($service->post_author != $current_user->ID) && (count($responses) > 0) && can_rate_service($service_id) ) { ?>
          <h3><?php _e('Rate This Service','egyptfoss') ?></h3>
          <div class="provider-rating rating-live"></div>
          <span class="live-rating"></span>
        <?php } else { ?>
          <h3><?php _e('Requester Rate','egyptfoss') ?></h3>
          <?php $requester_rate = requester_rate($service_id, $displayed_thread->user_id); ?>
          <span class="provider-rating rating-readonly" id="dimmed-rate" data-rating="<?php echo $requester_rate; ?>" title="<?php echo $requester_rate?round($requester_rate, 2):''; ?>"></span>
          <span class="your-rate"><?php echo $requester_rate?$requester_rate:''; ?></span>
        <?php } ?>
          <div class="modal fade" id="add-review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h3 class="modal-title" id="myModalLabel"><?php _e('Write Your Feedback','egyptfoss') ?></h3>
                </div>
                <form id="add_review" name="add_review" method="post" action="">
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-md-12">
                          <div class="form-group">
                            <textarea name="review" id="review" class="form-control" cols="30" rows="4" placeholder="<?php _e('Write Your Review For This Service...','egyptfoss') ?>" autofocus></textarea>
                          </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light cancel-review" data-dismiss="modal"><?php _e('Cancel','egyptfoss') ?></button>
                    <button type="button" class="btn btn-primary submit-review"><?php _e('Submit Review','egyptfoss') ?></button>
                  </div>
                </form>
              </div>
            </div>
          </div>
      </div>
      <span id="review-submitted" style="display: none;"><i class="fa fa-check"></i> <?php _e('Your review has been submitted','egyptfoss') ?></span>

      <h3><?php _e('Description','egyptfoss') ?></h3>
      <div class="panel panel-default service-description">
        <div class="panel-body">
          <?php echo wp_trim_words($service->post_content, 60); ?>
        </div>
      </div>

      <h3><?php _e('Service Rate','egyptfoss') ?></h3>
      <?php $reviewers_count = get_post_meta($service_id, 'reviewers_count', true); ?>
      <?php $rate = get_post_meta($service_id, 'rate', true); ?>
      <div class="panel panel-default">
        <div class="panel-body">
          <span class="provider-rating rating-readonly" id="average-rate" data-rating="<?php echo $rate; ?>" title="<?php echo round($rate, 2); ?>"></span>
          <?php if ( $reviewers_count > 0 ): ?>
              <span class="rating-count" title="<?php echo __('Rated by ','egyptfoss') . $reviewers_count . __(' customers','egyptfoss') ?>">
                <a href="#" id="show-all-reviews" data-toggle="modal" data-target="#all-reviews-modal">
                  (<?php echo $reviewers_count ?>)
                </a>
              </span>
          <?php endif; ?>
          <?php include(locate_template('MarketPlace/list-reviews-modal.php')); ?>
        </div>
      </div>
      <input type="hidden" id="displayed_thread_id" value="<?php echo $displayed_thread->id; ?>" />
      <input type="hidden" id="displayed_container_id" value="<?php echo $service_id; ?>" />
      <div class="text-center">
        <a href="<?php echo get_permalink($service_id); ?>"><i class="fa fa-external-link"></i> <?php _e('View Full Service Details', 'egyptfoss'); ?></a>
      </div>
    </div>
    </div><!-- #primary -->
  </div>
</div>

<?php get_footer(); ?>
<?php } ?>
