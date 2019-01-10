<?php
/**
 * Default Events Template
 * This file is the basic wrapper template for all the views if 'Default Events Template'
 * is selected in Events -> Settings -> Template -> Events Template.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/default-template.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

get_header();
?>
	<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-12">
	 				<h1 class="entry-title" property="name">
	 				<?php
							 if ( is_single() ) {
  echo the_title();
  if(ef_tribe_is_past_event(get_the_ID())){
  ?> <span class="passed-event" data-toggle="tooltip" data-placement="bottom" title="<?php _e("This event has passed.","egyptfoss"); ?>">
	 					<i class="fa fa-exclamation-triangle"></i>
	 					<?php _e('Passed','egyptfoss') ?>
  </span><?php }
} else {
 echo __('Events','egyptfoss');
}?>
	 				<?php do_action( 'tribe_events_before_the_title' ); ?>
	 					<?php do_action( 'tribe_events_after_the_title' ); ?>
	 					
	 				</h1>
          <?php 
              // this variable is used in related documents templates
              $section_slug = 'event';
              include( locate_template( 'template-parts/content-related_documents.php' ) );
          ?>
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->
<div  class="container events-container">
	<?php tribe_events_before_html(); ?>
	<?php tribe_get_view(); ?>
	<?php tribe_events_after_html(); ?>
</div> <!-- #tribe-events-pg-template -->
<?php
get_footer();
