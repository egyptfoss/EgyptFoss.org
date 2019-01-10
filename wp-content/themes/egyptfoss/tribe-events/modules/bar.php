<?php
/**
 * Events Navigation Bar Module Template
 * Renders our events navigation bar used across our views
 *
 * $filters and $views variables are loaded in and coming from
 * the show funcion in: lib/Bar.php
 *
 * @package TribeEventsCalendar
 *
 */
?>

<?php

$filters = tribe_events_get_filters();
$views   = tribe_events_get_views();

$current_url = tribe_events_get_current_filter_url();
?>
	<div class="add-btn-bar row">
	<div class="col-md-12">
			<?php if ( !is_user_logged_in() ) { ?>
		<a href="<?php echo home_url( pll_current_language().'/login/?redirected=addevent&redirect_to='.get_current_lang_page_by_template("page-add-event.php") ); ?>" class="btn btn-primary rfloat"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") .' '._nx("Event","Events",1, "indefinite", "egyptfoss"); ?></a>
	<?php } else if (current_user_can('add_new_ef_posts')) { ?>
		<a href="<?php echo get_current_lang_page_by_template("page-add-event.php"); ?>" class="btn btn-primary rfloat"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") .' '._nx("Event","Events",1, "indefinite", "egyptfoss"); ?></a>
	<?php } else { ?>
		<!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
		<a href="javascript:void(0)" class="btn btn-primary disabled rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") .' '._nx("Event","Events",1, "indefinite", "egyptfoss"); ?></a>
	<?php } ?>
	</div>
</div>
<?php do_action( 'tribe_events_bar_before_template' ) ?>
<div id="tribe-events-bar">

	<form id="tribe-bar-form" class="tribe-clearfix" name="tribe-bar-form" method="post" action="<?php echo esc_attr( $current_url ); ?>">

		<!-- Mobile Filters Toggle -->

		<div id="tribe-bar-collapse-toggle" <?php if ( count( $views ) == 1 ) { ?> class="tribe-bar-collapse-toggle-full-width"<?php } ?>>
			<?php printf( esc_html__( 'Find %s', 'the-events-calendar' ), tribe_get_event_label_plural() ); ?><span class="tribe-bar-toggle-arrow"></span>
		</div>

		<!-- Views -->
		<?php if ( count( $views ) > 1 ) { ?>
			<div id="tribe-bar-views">

				<!-- .tribe-bar-views-inner -->
			</div><!-- .tribe-bar-views -->
		<?php } // if ( count( $views ) > 1 ) ?>

		<?php if ( ! empty( $filters ) ) { ?>
			<div class="tribe-bar-filters">
				<div class="tribe-bar-filters-inner tribe-clearfix">
					<?php foreach ( $filters as $key=>$filter ) { ?>
						<div class="<?php echo esc_attr( $filter['name'] ) ?>-filter">
							<!--<label class="label-<?php echo esc_attr( $filter['name'] ) ?>" for="<?php echo esc_attr( $filter['name'] ) ?>"><?//php echo $filter['caption'] ?></label>-->
							<?php
              if($key == "tribe-bar-date")
              {
                $value = date('Y-m');
                if( get_query_var( 'eventDate' ) ) {
                  $value = get_query_var('eventDate');
                }
              ?>
              <input type="text" data-date-language="ru" data-date-orientation="bottom" readonly name="tribe-bar-date" style="position: relative;" id="tribe-bar-date" value="<?php echo $value; ?>"  placeholder="<?php esc_attr__( 'Date', 'the-events-calendar' )  ?>"><input type="hidden" name="tribe-bar-date-day" id="tribe-bar-date-day" class="tribe-no-param" value="">
              <?php } else if($key == "tribe-bar-search") {
                $value = '';
                if( !empty( $_GET['tribe-bar-search'] ) ) {
                  $value = $_GET['tribe-bar-search'];
                }
              ?>
              <input type="text" name="tribe-bar-search" id="tribe-bar-search" value="<?php echo $value; ?>" placeholder="<?php echo __( 'Event Name', 'egyptfoss' ); ?>">
              <?php } else {
              	echo $filter['html'] ;
              }
                ?>
						</div>
          <?php } ?>
					<div class="tribe-bar-submit">
						<input class="tribe-events-button tribe-no-param btn btn-primary" type="submit" name="submit-bar" value="<?php echo __( 'Search', 'egyptfoss' ); ?>" />
          </div>
					<!-- .tribe-bar-submit -->
				</div>
				<!-- .tribe-bar-filters-inner -->
			</div><!-- .tribe-bar-filters -->
		<?php } // if ( !empty( $filters ) ) ?>

	</form>
	<!-- #tribe-bar-form -->

</div><!-- #tribe-events-bar -->

<?php if(pll_current_language()== "ar") { ?>
<style>
    .datepicker {
    right: auto;
    direction: rtl;
}

.datepicker:before {
content: '';
display: inline-block;
border-left: 7px solid transparent;
border-right: 7px solid transparent;
border-bottom: 7px solid #ccc;
border-bottom-color: rgba(0, 0, 0, 0.2);
position: absolute;
top: -7px;
left: 190px;  /** I made a change here */
}

.datepicker:after {
content: '';
display: inline-block;
border-left: 6px solid transparent;
border-right: 6px solid transparent;
border-bottom: 6px solid #ffffff;
position: absolute;
top: -6px;
left: 191px;  /** I made a change here */
}

</style>
<?php } ?>
<?php
do_action( 'tribe_events_bar_after_template' );
