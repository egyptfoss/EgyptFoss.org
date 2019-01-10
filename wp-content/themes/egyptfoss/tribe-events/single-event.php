<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/single-event.php
 *
 * @package TribeEventsCalendar
 *
 */
include( ABSPATH . 'system_data.php' );
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_singular = tribe_get_event_label_singular();
$events_label_plural = tribe_get_event_label_plural();

$event_id = get_the_ID();
$event = get_post($event_id);

$lang = get_locale();

$request_center_taxs = array("technology"=>array(),"interest"=>array());
$acf_technologies = get_field('technology', get_the_ID(), $format_value = true);
$acf_interest = get_field('interest', get_the_ID(), $format_value = true);
$technology_spans = array();
$interest_spans = array();

foreach ($acf_technologies as $technology_id) {
  $technology = get_term($technology_id, 'technology');
  array_push($request_center_taxs["technology"], $technology->slug);
  array_push ($technology_spans,__("$technology->name", "egyptfoss"));
}
$technology_spans = join(", ", $technology_spans);
  
foreach ($acf_interest as $keyword_id) {
  $keyword = get_term($keyword_id, 'interest');
  array_push($request_center_taxs["interest"], $keyword->slug);
  array_push ($interest_spans,__("$keyword->name", "egyptfoss"));
}
$interest_spans = join(", ", $interest_spans);
?>

<div id="tribe-events-content" class="tribe-events-single" vocab="http://schema.org/" typeof="Event">
	<div class="row"> 
       <?php
    $ef_messages = getMessageBySession("ef_event_messages");
    if(isset($ef_messages['success'])) { ?>
      <div class="alert alert-success">
      <?php foreach ($ef_messages['success'] as $success) { ?>
        <i class="fa fa-check"></i> <?php echo $success; ?>
      <?php } ?>
      </div>
      <div class="clearfix"></div>
    <?php } ?>

		<div class="col-md-9">
			<!-- Notices -->
			<?php tribe_the_notices() ?>
		</div>
 	<div class="col-md-12 share-product">
      <div class="share-profile rfloat"><?php getRequestCenterAddLink($request_center_taxs) ?></div>
       <?php if($event->post_status == 'publish'){ ?>
 			<div class="share-profile rfloat"><a class="btn btn-light"><i class="fa fa-share"></i> <?php _e('Share','egyptfoss') ?>
			<div class="share-box">
			<?php echo do_shortcode('[Sassy_Social_Share]');?>
		</div>
		</a>
		</div>
    <?php } ?>
    <?php if (is_user_logged_in() ) { ?>
      <?php if (current_user_can('add_new_ef_posts') && ($event->post_status == 'pending') && ($event->post_author == get_current_user_id()) ) { ?>
        <a href="<?php echo get_current_lang_page_by_template("template-edit-event.php")."?pid=".get_the_ID() ?>" class="btn btn-light rfloat"><i class="fa fa-pencil"></i> <?php echo ucwords(__('Edit','egyptfoss')); ?></a>
      <?php } else if($event->post_status != 'pending') { ?>
        <?php //hide edit button if published ?>
      <?php } else { ?>
        <!-- Subscriber, Contributor users should be able to view (Edit Event) -->
        <a href="javascript:void(0)" class="btn btn-light rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><i class="fa fa-pencil"></i> <?php echo ucwords(__('Edit','egyptfoss')); ?></a>
      <?php } ?>
    <?php } ?>

    <div class="lfloat">
    	             <span class="added-by news-author">
<?php echo get_avatar( $event->post_author, 32 ); ?>

<?php if(bp_core_get_username($event->post_author) != '') { ?>
    <a href="<?php echo home_url()."/members/".bp_core_get_username($event->post_author)."/about/" ?>">
        <?php echo bp_core_get_user_displayname($event->post_author); ?>
    </a>
<?php } else { echo bp_core_get_user_displayname($event->post_author); } ?>
</span>
    </div>

 	</div>
 </div>
<div class="row">
	<div class="col-md-8">
			<div class="tribe-events-schedule tribe-clearfix">
          <?php if (tribe_get_start_date($event->ID, true, "Y-m-d") == tribe_get_end_date($event->ID, true, "Y-m-d")): ?>
            <h2>
                <span class="event-lbl"><?php _e("From","egyptfoss") ?></span>
                <?php echo tribe_get_start_date($event->ID,true,"l, F d, g:i A "); ?> 
                <span class="event-lbl"><?php _e("To","egyptfoss") ?></span> 
                <?php echo tribe_get_end_date($event->ID,true,"l, F d, g:i A "); ?> 
            </h2>
          <?php else: ?>
            <h2>
                <span class="event-lbl"><?php _e("From","egyptfoss") ?></span> 
                <?php echo tribe_get_start_date($event->ID,true,"l, F d, g:i A "); ?>
                <br>
                <span class="event-lbl"><?php _e("To","egyptfoss") ?></span>
                <?php echo tribe_get_end_date($event->ID,true,"l, F d, g:i A "); ?>
            </h2>
          <?php endif; ?>
          <meta property="startDate" content="<?php echo tribe_get_start_date($event->ID,true,"c"); ?>">
          <meta property="endDate" content="<?php echo tribe_get_end_date($event->ID,true,"c"); ?>">
		<?php //echo tribe_events_event_schedule_details( $event_id, '<h2>', '</h2>' ); ?>

		<?php if ( tribe_get_cost() ) { ?>
			<span class="tribe-events-cost"><i class="fa fa-ticket"></i> <?php 
                        if(tribe_get_cost( null, true ) == "Free")
                         _e("Free of Charge","egyptfoss");
                        else
                        echo tribe_get_cost( null, true ); ?></span>
                <?php }else { ?>
                        <span class="tribe-events-cost"><i class="fa fa-ticket"></i> <?php _e("Free of Charge","egyptfoss"); ?></span>
                <?php } ?>
		<?php

						// Event Website
				$website = tribe_get_event_website_link();
		if ( ! empty( $website ) ) : ?>
		<span class="event-website">
					<i class="fa fa-globe"></i>
			<span class="tribe-events-event-url"> <?php echo $website; ?> </span>
		</span>
		<?php endif ?>

 <span class="event-type">
     <?php
    $event_type = get_field('event_type', $event_id, $format_value = true);
    $lang = get_locale();
    if($lang == 'ar') {
      $event_type = $ar_events_types[$event_type];
    }else{
      $event_type = $events_types[$event_type];  
    }
    if ( ! empty( $event_type ) ) { ?>
      <i class="fa fa-info-circle"></i>
       <?php _e("$event_type", "egyptfoss"); ?>
    <?php }
    else {?>
        <?php _e('Not Specified', 'egyptfoss'); ?>
    <?php }?>

 </span>
	</div>
				<!-- Event content -->
			<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
			<meta property="name" content="<?php the_title(); ?>">
			<meta property="url" content="<?php the_permalink(); ?>">
			<div class="tribe-events-single-event-description tribe-events-content" property="description">
				<?php the_content(); ?>
			</div>
			<div class="event-info">
		<div class="row">
			<div class="col-md-12">
						<dl>
    <?php
      $themes = get_field('theme', $event_id, $format_value = true);
      if ( ! empty( $themes ) ) { ?>
					<dt><?php _e('Theme','egyptfoss') ?></dt>
                                        <dd>
                                            
    <?php if(is_array($themes)){ $theme = get_term( $themes[0], 'theme' );}else{ $theme = get_term( $themes, 'theme' );} ?>
        <?php
             if($lang == "ar")
               {
        if($theme->name_ar == '')
                                                            echo $theme->name;
                                                        else
                                                            echo $theme->name_ar;
                                                    }else
                                                    { 
                                                        _e("$theme->name", "egyptfoss"); 
                                                    }
                                                ?>
                                            <?php }
                                            ?>
                                        </dd>
                                        <?php
                                          if (!empty($acf_technologies)) {
                                         ?>
                                          <dt><?php _e('Technology', 'egyptfoss') ?></dt>                                                
                                            <dd>
                                            <?php $technology_spans_arr = explode(',', $technology_spans);
																													foreach($technology_spans_arr as $technology_spans){
																																									 ?>
                                            <span class="technology-tag"><?php echo $technology_spans; ?></span> <?php
																													}
																																									?>
                                            </dd>
                                        <?php }
                                        ?>
                                        
                                       <?php
                                            $platforms = get_field('platform', $product_id, $format_value = true);
                                            if ( ! empty( $platforms ) ) { $i = 0; $size= sizeof($platforms);?>
                                    <dt><?php _e('Platform','egyptfoss') ?></dt>
                                    <dd>
                                              
                                                
                                                  <?php foreach ( $platforms as $platform_id) :
                                                      $i++;
                                                      $platform = get_term( $platform_id, 'platform' );
                                                      ?><span class="technology-tag"><?php _e("$platform->name", "egyptfoss");
																																																																										echo ($i < $size)? ',':''; ?></span>
                                                  <?php endforeach; ?>
                                                
                                            
                                    </dd>
                                    <?php }
                                           ?>
                                    <?php 
                                        if ( ! empty( $acf_interest ) ) { ?>
                                    <dt><?php _e('Interests','egyptfoss') ?></dt>
                                        <dd>
                  <?php $interest_spans_arr = explode(',', $interest_spans);
																													foreach($interest_spans_arr as $interest_span){
																																									 ?> <span class="technology-tag"><?php echo $interest_span; ?></span> <?php
																													}
																																									?>
                </dd>          
                                        <?php }
                                         ?>
				</dl>
			</div>
		</div>
			</div>
			<!-- .tribe-events-single-event-description -->
			<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>

	</div>
	<div class="col-md-4">
					<!-- add gmaps -->
			<div class="event-location">
						<?php 	tribe_get_template_part( 'modules/meta/map' ); ?>
			</div>
	</div>
</div>
	<!-- Event header -->
	<div id="tribe-events-header" <?php tribe_events_the_header_attributes() ?>>
		<!-- Navigation -->
		<h3 class="tribe-events-visuallyhidden"><?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?></h3>
		<ul class="tribe-events-sub-nav">
			<li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ) ?></li>
			<li class="tribe-events-nav-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ) ?></li>
		</ul>
		<!-- .tribe-events-sub-nav -->
	</div>
	<!-- #tribe-events-header -->

	<?php while ( have_posts() ) :  the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<!-- Event featured image, but exclude link -->
			<?php echo tribe_event_featured_image( $event_id, 'full', false ); ?>
			<!-- Event meta -->
			<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
			<?php tribe_get_template_part( 'modules/meta' ); ?>
			<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
			<div class="row">
				<div class="col-md-6">
                        <?php if( get_field('audience') ): ?>
			<h3><?php _e('Audience','egyptfoss') ?></h3>
         <div class="expand-text">
         		<?php the_field('audience'); ?>
         </div>
<?php endif; ?>
				</div>
				<div class="col-md-6">
					 	<?php if( get_field('objectives') ): ?>		
			<h3><?php _e('Objectives','egyptfoss') ?></h3>
            <div class="expand-text">
            	<?php the_field('objectives'); ?>
            </div>
<?php endif; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<?php if( get_field('prerequisites') ): ?>
			<h3><?php _e('Prerequisites','egyptfoss') ?></h3>
   <div class="expand-text">
   		<?php the_field('prerequisites'); ?>
   </div>
<?php endif; ?>
				</div>
						<div class="col-md-6">
                                                    <?php if( get_field('functionality') ): ?>
			<h3><?php _e('Functionality','egyptfoss') ?></h3>
               <div class="expand-text">
               	<?php the_field('functionality'); ?>
               </div>
   
                        <?php endif; ?>
				</div>
			</div>

		</div> <!-- #post-x -->
	<?php endwhile; ?>

	<!-- Event footer -->
	<div id="tribe-events-footer">
		<!-- Navigation -->
		<h3 class="tribe-events-visuallyhidden"><?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?></h3>
		<ul class="tribe-events-sub-nav">
			<li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ) ?></li>
			<li class="tribe-events-nav-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ) ?></li>
		</ul>
		<!-- .tribe-events-sub-nav -->
	</div>
        
        		<?php if ( get_post_type() == Tribe__Events__Main::POSTTYPE && tribe_get_option( 'showComments', true ) ) comments_template() ?>

	<!-- #tribe-events-footer -->

</div><!-- #tribe-events-content -->
