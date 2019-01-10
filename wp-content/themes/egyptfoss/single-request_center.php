<?php
/**
 * Template Name: Single Request.
 *
 * @package egyptfoss
 */
get_header(); ?>

<!-- retrieve the technologies and interests -->
<?php
$request_center_taxs = array("technology"=>array(),"interest"=>array());
$acf_technologies = get_field('technology', $post->ID, $format_value = true);
$acf_interest = get_field('interest', $post->ID, $format_value = true);
$technology_spans = array();
$interest_spans = array();
if(!empty($acf_technologies)){
  foreach ($acf_technologies as $technology_id) {
    $technology = get_term($technology_id, 'technology');
    array_push($request_center_taxs["technology"], $technology->slug);
    array_push ($technology_spans,__("$technology->name", "egyptfoss"));
  }
  $technology_spans = join(", ", $technology_spans);
}
if(!empty($acf_interest)){
  foreach ($acf_interest as $keyword_id) {
    $keyword = get_term($keyword_id, 'interest');
    array_push($request_center_taxs["interest"], $keyword->slug);
    array_push ($interest_spans,__("$keyword->name", "egyptfoss"));
  }
  $interest_spans = join(", ", $interest_spans);
}
$count = getThreadsCount(get_the_ID());
?>

<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-12">
				<div class="single-type-thumbnail">
        <?php $ef_type = get_term_name_by_lang(get_post_meta($post->ID, "request_center_type", true), pll_current_language()); 
          $ef_type_slug = get_term(get_post_meta($post->ID, "request_center_type", true));
        ?>
				<img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/<?php echo $ef_type_slug->slug; ?>_icon.svg" width="22" alt="<?php _e('Service Request','egyptfoss') ?>">
				</div>
	 				<h1>
						<?php echo $post->post_title; ?>
						<br>
            <small><?php echo ucwords($ef_type); ?></small>
					</h1>
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->

<div class="container">
  <div class="row" vocab="http://schema.org/" typeof="Service">
    <?php if($post->post_status == 'archive') { ?>
      <div class="alert alert-warning"><i class="fa fa-archive"></i>
        <?php _e("Request is archived, no further responses.","egyptfoss"); ?>
      </div>
    <?php } ?>
    <div class="alert alert-success request-archived" style="display:none;">
      <i class='fa fa-check'></i>
      <?php _e('Request Archived Successfully', 'egyptfoss'); ?>
      <br/>
    </div>
    <div class="alert alert-danger request-not-archived" style="display:none;">
      <i class='fa fa-warning'></i>
      <?php _e('Error Archiving Request', 'egyptfoss'); ?>
      <br/>
    </div>
    <?php $edit_success_edit = getMessageBySession("edit-request-center");
        if(!empty($edit_success_edit))
        {?>
        <div class="alert alert-success">
          <i class='fa fa-check'></i>
          <?php echo $edit_success_edit['success']; ?>
          <br/>
        </div>
    <?php } ?>

    <?php $add_request = getMessageBySession("add-request-center");
        if(!empty($add_request))
        {?>
        <div class="alert alert-success">
          <i class='fa fa-check'></i>
          <?php echo $add_request['success']; ?>
          <br/>
        </div>
    <?php } ?>
 </div>
	<div class="row">
		<div class="created-by-name single-requests lfloat">
			<?php echo get_avatar( $post->post_author, 32 ); ?> <span><?php if(bp_core_get_username($post->post_author) != '') { ?> <a href="<?php echo home_url()."/members/".bp_core_get_username($post->post_author).'/about/' ?>" property="author"> <?php echo bp_core_get_user_displayname($post->post_author); ?></a><?php } else {  ?> <?php echo bp_core_get_user_displayname($post->post_author); }?> </span>
			<div class="request-date" property="datepublished">
				<i class="fa fa-clock-o"></i>
        <?php echo mysql2date('d F Y', $post->post_date); ?>
			</div>
		</div>
	<div class="respond-btns rfloat">
    <?php if($post->post_status == 'pending' && $post->post_author == get_current_user_id()) {   ?>
		<a href="<?php echo get_current_lang_page_by_template("template-edit-request-center.php")."?rid=".$post->ID ?>" class="btn btn-light">
			<i class="fa fa-pencil"></i>
			<?php _e("Edit","egyptfoss"); ?>
		</a>
    <?php } ?>
    <?php if(in_array($post->post_status, ['publish', 'archive'])) { ?>
  		<span class="responses-count" title="<?php _e("Total Responses","egyptfoss"); ?>"><i class="fa fa-comments-o"></i> <?php echo $count; ?></span>
      <?php if($post->post_author == get_current_user_id()) { // owner ?>
        <?php if($count == 0) { ?>
          <?php if (current_user_can('add_new_ef_posts') ) { ?>
            <a href="javascript:void(0);" data-toggle="modal" data-target="#no-responses-modal" class="btn btn-primary disable" title="<?php _e("No responses","egyptfoss"); ?>">
              <i class="fa fa-reply"></i> <?php _e("Responses","egyptfoss"); ?>
            </a>
            <div class="modal fade" id="no-responses-modal" tabindex="-1" role="dialog">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php _e("No responses","egyptfoss"); ?></h4>
                  </div>
                  <div class="modal-body">
                    <div class="row form-group">
                      <div class="col-md-12">
                        <?php _e("This resquest has no responses","egyptfoss"); ?>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><?php _e("Ok","egyptfoss"); ?></button>
                  </div>
                </div>
              </div>
            </div>
          <?php } else { ?>
            <a href="javascript:void(0)" class="btn btn-primary rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>">
              <i class="fa fa-reply"></i> <?php _e("Responses","egyptfoss"); ?>
            </a>
          <?php } ?>
        <?php } else { ?>
          <a href="<?php echo get_current_lang_page_by_template("template-request-thread.php")."?pid=".get_the_ID() ?>" class="btn btn-primary" title="<?php echo ownerUnseenThreads($post->ID).' '; _e("new responses","egyptfoss"); ?>">
            <i class="fa fa-reply"></i>
            <?php _e("Responses","egyptfoss"); ?>
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
            <a href="<?php echo get_current_lang_page_by_template("template-request-thread.php")."?pid=".get_the_ID() ?>" class="btn btn-primary rel-btn" title="<?php ($thread->seen_by_user) ? _e('seen','egyptfoss') : _e('unseen','egyptfoss') ?>">
              <i class="fa fa-reply"></i> <?php _e("Check your response","egyptfoss"); ?>
              <?php if (!$thread->seen_by_user) { ?>
                <span class="new-responses owner-notify"></span>
              <?php } ?>
            </a>
          <?php } else { ?>
            <?php if(!is_user_logged_in()) { ?>
              <a href="<?php echo home_url( pll_current_language().'/login/?redirected=respondtorequest&redirect_to='.get_current_lang_page_by_template("template-request-thread.php")."?pid=".get_the_ID() ); ?>" class="btn btn-primary rfloat">
                <i class="fa fa-reply"></i> <?php _e("Respond to request","egyptfoss"); ?>
              </a>
            <?php } else if($post->post_status == 'archive') { ?>
              <a href="#" data-toggle="modal" data-target="#cant-responsd" class="btn btn-primary" title="<?php _e("Request is archived, no further responses.","egyptfoss"); ?>">
              <i class="fa fa-reply"></i> <?php _e("Respond to request","egyptfoss"); ?></a>
              <div class="modal fade" id="cant-responsd" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-md">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title"><?php _e("Request Archived","egyptfoss"); ?></h4>
                    </div>
                    <div class="modal-body">
                      <div class="row form-group">
                        <div class="col-md-12">
                          <?php _e("Request is archived, no further responses.","egyptfoss"); ?>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal"><?php _e("Ok","egyptfoss"); ?></button>
                    </div>
                  </div>
                </div>
              </div>
            <?php } else { ?>
              <?php if (!current_user_can('add_new_ef_posts') ) { ?>
                <a href="javascript:void(0)" class="btn btn-primary rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>">
                  <i class="fa fa-reply"></i> <?php _e("Respond to request","egyptfoss"); ?>
                </a>
              <?php } else if (!user_can($post->post_author, 'add_new_ef_posts') ) { ?>
                <a href="javascript:void(0)" class="btn btn-primary rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e("This Request author is no longer active.", "egyptfoss"); ?>">
                  <i class="fa fa-reply"></i> <?php _e("Respond to request","egyptfoss"); ?>
                </a>
              <?php } else { ?>
                <a href="<?php echo get_current_lang_page_by_template("template-request-thread.php")."?pid=".get_the_ID() ?>" class="btn btn-primary" title="">
                  <i class="fa fa-reply"></i> <?php _e("Respond to request","egyptfoss"); ?>
                </a>
              <?php } ?>
            <?php } ?>
          <?php }
        }
    } ?>

    <?php if(is_user_logged_in() && ($post->post_status != 'archive') && ($post->post_author == get_current_user_id())) { ?>
      <?php if (!current_user_can('add_new_ef_posts')) { ?>
        <a href="javascript:void(0)" class="btn btn-light disabled rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><i class="fa fa-archive"></i> <?php _e("Archive","egyptfoss"); ?></a>
      <?php } else if($post->post_status == 'publish') { ?>
        <a href="#" data-toggle="modal" data-target="#archive-request-modal" class="btn btn-light archive-request-button" title="<?php _e("Archiving a request will prevent further responses and you can not undo this action.","egyptfoss"); ?>"><i class="fa fa-archive"></i> <?php _e("Archive","egyptfoss"); ?></a>
        <div class="modal fade" id="archive-request-modal" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-md">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php _e("Archive Request","egyptfoss"); ?></h4>
              </div>
              <div class="modal-body">
                <div class="row form-group">
                  <div class="col-md-12">
                    <?php _e("Archiving a request will prevent further responses and you can not undo this action.","egyptfoss"); ?>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?php _e("Cancel","egyptfoss"); ?></button>
                <button type="button" name="confirm-archive-request" class="btn btn-primary archive-request" id="<?php echo $post->ID ?>" data-dismiss="modal"><i class="fa fa-archive"></i> <?php _e("Archive","egyptfoss"); ?></button>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    <?php } ?>
	</div>
	</div>
	<div class="row">
    <div  class="single-request-content content-area col-md-9">
	  	<div class="row">
	  	  <div class="col-md-12">
  <?php $ef_description = get_post_meta($post->ID, "description", true);
  if(!empty($ef_description)) { ?>
<h3><?php _e("Description","egyptfoss"); ?></h3>
<p class="expand-text-large" property="description">
  <?php echo nl2br($ef_description);  ?>
</p>
<?php } ?>
<?php $ef_requirements = get_post_meta($post->ID, "requirements", true);
if(!empty($ef_requirements)) { ?>
<h3><?php _e("Requirements","egyptfoss"); ?></h3>
<p class="expand-text-large">
<?php echo nl2br($ef_requirements);  ?>
</p>
<?php } ?>
<!--<ol class="req-list">
	<li>Need a small system to handle my daily sales</li>
	<li>If possible i want mobile app for customers</li>
</ol>-->
<?php $ef_constraints = get_post_meta($post->ID, "constraints", true);
if(!empty($ef_constraints)){ ?>
<h3><?php _e("Constraints","egyptfoss"); ?></h3>
<p class="expand-text-large">
<?php echo nl2br($ef_constraints); ?>
</p>
<?php } ?>
<!--<ol>
	<li>none</li>
</ol>-->
        </div>
      </div>
    </div><!-- #primary -->
    <div class="col-md-3 side-bar">
      <h3><?php _e('Request Info','egyptfoss') ?></h3>
          <ul class="basic-info-box list-group">
              <?php $ef_target = get_term_name_by_lang(get_post_meta($post->ID, "target_bussiness_relationship", true), pll_current_language());
              if(!empty($ef_target)) { ?>
            <li class="list-group-item">
              <strong><?php _e("Target Relationship","egyptfoss"); ?>: </strong>
              <?php echo ucwords($ef_target);  ?>
            </li>
            <?php } ?>
            <?php $ef_theme = get_term_name_by_lang(get_post_meta($post->ID, "theme", true), pll_current_language());
            if(!empty($ef_theme)) { ?>
            <li class="list-group-item">
              <strong><?php _e("Theme","egyptfoss"); ?>: </strong>
            <?php echo ucwords($ef_theme);  ?>
            </li>
            <?php } ?>
            <?php
              if (!empty($acf_technologies)) {
            ?>
            <li class="list-group-item">
              <strong><?php _e("Technologies","egyptfoss"); ?>: </strong>
              <?php $technology_spans_arr = explode(',', $technology_spans);
                  foreach($technology_spans_arr as $technology_spans){ ?>
                    <span class="interest-badge"><?php echo $technology_spans; ?></span>
              <?php } ?>
            </li>
              <?php } ?>
            <?php
              if ( ! empty( $acf_interest ) ) { ?>
            <li class="list-group-item">
              <strong><?php _e("Related interests","egyptfoss"); ?>: </strong>
              <?php $interest_spans_arr = explode(',', $interest_spans);
              foreach($interest_spans_arr as $interest_span){ ?>
              <span class="interest-badge"><?php echo $interest_span; ?></span>
              <?php } ?>
            </li>
              <?php } ?>
          <?php $ef_duedate = get_post_meta($post->ID, "deadline", true);
          if(!empty($ef_duedate)){ ?>
            <li class="list-group-item">
              <strong><?php _e("Due Date","egyptfoss"); ?>: </strong>
          <?php echo mysql2date('d F Y', $ef_duedate);  ?>
            </li>
          <?php } ?>
          </ul>
    </div>
	</div>
</div>

<?php get_footer();?>
