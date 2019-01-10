<?php
/**
 * Template Name: Request Thread.
 *
 * @package egyptfoss
 */

if ( is_user_logged_in()) {
  $request_id = $_GET['pid'];
  $thread_id = $_GET['tid'];
  if (!in_array(get_post_status($request_id), ["publish", "archive"])) {
    wp_redirect(home_url());
    exit;
  }
  $request = get_post($request_id);
  $current_user = wp_get_current_user();
  if(!current_user_can('add_new_ef_posts'))
  {
    wp_redirect( home_url( '/?status=403' ) );
    exit; 
  }  
  $user_type = ($request->post_author == $current_user->ID) ? 'owner' : 'user';
  $thread_data = validate_thread_container($request, $current_user, $thread_id);
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
        <h1><?php echo get_the_title($request_id); ?></h1>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->

<div class="container">
  <div class="row content-area">
    <div class="alert alert-success thread-archived" style="display:none;">
      <i class='fa fa-check'></i>
      <?php _e('Response Archived Successfully', 'egyptfoss'); ?>
      <br/>
    </div>
    <div class="alert alert-danger thread-not-archived" style="display:none;">
      <i class='fa fa-warning'></i>
      <?php _e('Error Archiving Response', 'egyptfoss'); ?>
      <br/>
    </div>

    <?php if(($request->post_status == 'archive') && ($request->post_author == $current_user->ID)) { ?>
      <div class="alert alert-warning"><i class="fa fa-archive"></i>
        <?php _e("Request is archived, no further responses. The old responses are still active","egyptfoss"); ?>
      </div>
    <?php } else if($displayed_thread->status == 0) { ?>
      <?php $show_form = false; ?>
      <div class="alert alert-warning"><i class="fa fa-archive"></i>
        <?php _e("Response is archived, no further replies","egyptfoss"); ?>
      </div>
    <?php } ?>

    <?php if($request->post_author == $current_user->ID) { // owner ?>

      <?php if(!user_can($request->post_author, 'add_new_ef_posts')) { ?>
        <?php $show_form = false; ?>
        <div class="alert alert-warning"><i class="fa fa-archive"></i>
          <?php _e("You are not authorized to reply to this response. Please contact us for more information.","egyptfoss"); ?>
        </div>
      <?php } else if(!user_can($displayed_thread->user_id, 'add_new_ef_posts')) { ?>
        <?php $show_form = false; ?>
        <div class="alert alert-warning"><i class="fa fa-archive"></i>
          <?php _e("This Responder is no longer active.","egyptfoss"); ?>
        </div>
      <?php } ?>

      <?php $list = getThreadsList($request, $current_user); ?>
      <div class="col-md-3 responses-sidebar">
        <h3><?php _e('Responses','egyptfoss') ?></h3>
        <div class="nano">
          <ul class="responses-list-user nano-content">
            <?php foreach ($list as $thread_in_list) {
              $user_in_list_id = ($thread_in_list->owner_id != $current_user->ID) ? $thread_in_list->owner_id : $thread_in_list->user_id ;
              $user_in_list = get_user_by('id', $user_in_list_id);
            ?>
            <li>
              <a href="<?php echo get_current_lang_page_by_template('template-request-thread.php')."?pid=".$request->ID."&tid=".$thread_in_list->id ?>">
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
          <?php _e("You are not authorized to reply to this response. Please contact us for more information.","egyptfoss"); ?>
        </div>
      <?php } else if(!user_can($request->post_author, 'add_new_ef_posts')) { ?>
        <?php $show_form = false; ?>
        <div class="alert alert-warning"><i class="fa fa-archive"></i>
          <?php _e("This Request author is no longer active.","egyptfoss"); ?>
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

          <?php if (!current_user_can('add_new_ef_posts') && ($request->post_author == $current_user->ID)) { ?>
            <div class="archive-thread">
              <a href="javascript:void(0)" class="btn btn-light disabled rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><i class="fa fa-archive"></i> <?php _e("Archive","egyptfoss"); ?></a>
            </div>
          <?php } else if( ($displayed_thread->status == 1) && ($request->post_author == $current_user->ID) && ($request->post_status != 'archive') ) { ?>
            <div class="archive-thread">
              <a href="javascript:void(0);" data-toggle="modal" data-target="#archive-thread-modal" class="btn btn-link archive-thread-button rfloat"><i class="fa fa-archive"></i> <?php _e("Archive","egyptfoss"); ?></a>
            </div>
            <div class="modal fade" id="archive-thread-modal" tabindex="-1" role="dialog">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php _e("Archive Response","egyptfoss"); ?></h4>
                  </div>
                  <div class="modal-body">
                    <div class="row form-group">
                      <div class="col-md-12">
                        <?php _e("Archiving a response will prevent further replies and you can not undo this action.","egyptfoss"); ?>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><?php _e("Cancel","egyptfoss"); ?></button>
                    <button type="button" name="confirm-archive-thread" class="btn btn-primary archive-my-thread" id="<?php echo $displayed_thread->id ?>" data-dismiss="modal"><?php _e("Archive","egyptfoss"); ?></button>
                  </div>
                </div>
              </div>
            </div>
          <?php } else if( ($displayed_thread->status == 0) && ($request->post_author == get_current_user_id())) { ?>
            <div class="archive-thread">
            	<a href="javascript:void(0);" class="btn btn-link rfloat disabled"><i class="fa fa-archive"></i> <?php _e("Archive","egyptfoss"); ?></a>
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
                  <p><?php _e('Start conversation by writing your response below', 'egyptfoss') ?></p>
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
                    <a href="javascript:void(0)" class="btn btn-primary rfloat btn-sm" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>">
                      <i class="fa fa-send"></i> <?php _e('Send','egyptfoss') ?>
                    </a>
                  <?php } ?>
                </div>
              </div>
            </div>
            <input type="hidden" id="displayed_thread_id" value="<?php echo $displayed_thread->id; ?>" />
            <input type="hidden" id="displayed_container_id" value="<?php echo $request_id; ?>" />
          </form>
        <?php } ?>
      </div>
    </div>
    <div class="col-md-3">
      <h3><?php _e('Description','egyptfoss') ?></h3>
      <div class="panel panel-default">
        <div class="panel-body">
          <?php echo wp_trim_words(get_post_meta($request_id, "description", true), 60); ?>
        </div>
      </div>
      <div class="text-center">
        <a href="<?php echo get_permalink($request_id); ?>"><i class="fa fa-external-link"></i> <?php _e("View Full Request Details","egyptfoss"); ?></a>
      </div>
    </div>
    </div><!-- #primary -->
  </div>
</div>

<?php get_footer(); ?>
<?php } ?>
