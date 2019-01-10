<?php
/**
 * Single Event Meta (Venue) Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/modules/meta/venue.php
 *
 * @package TribeEventsCalendar
 */

if ( ! tribe_get_venue_id() ) {
	return;
}

$phone   = tribe_get_phone();
$website = tribe_get_venue_website_link();

?>

<div class="tribe-events-meta-group tribe-events-meta-group-venue" style="width: 100% !important;">
	<h3 class="tribe-events-single-section-title"> <?php esc_html_e( tribe_get_venue_label_singular(), 'egyptfoss' ) ?> </h3>
	<dl>
		<?php do_action( 'tribe_events_single_meta_venue_section_start' ) ?>

		<dd class="tribe-venue"> <?php echo tribe_get_venue() ?> </dd>

		<?php //if ( tribe_address_exists() ) : ?>
			<dd class="tribe-venue-location">
				<address class="tribe-events-address">
					<?php echo tribe_get_full_address(); ?>
                                        <?php $venue_gmap = get_post_meta(tribe_get_venue_id(), 'gmap', true);?>
					<?php //if ( tribe_show_google_map_link() ) : ?>
                                                <?php if($venue_gmap != "") { ?>
                                                
                                                <a target="_blank" class="tribe-events-gmap" title="Click to view a Google Map" href="http://maps.google.com/maps?q=<?php echo $venue_gmap['lat']; ?>,<?php echo $venue_gmap['lng']; ?>">+ Google Map</a>
						<?php //echo tribe_get_map_link_html(); ?>
                                                <?php } ?>
					<?php //endif; ?>
				</address>
			</dd>
		<?php// endif; ?>

		<?php if ( ! empty( $phone ) ): ?>
			<dt> <?php esc_html_e( 'Phone', 'egyptfoss' ) ?>: </dt>
			<dd class="tribe-venue-tel"> <?php echo $phone ?> </dd>
		<?php endif ?>

		<?php if ( ! empty( $website ) ): ?>
			<dt> <?php esc_html_e( 'Website', 'egyptfoss' ) ?>: </dt>
			<dd class="url"> <?php echo $website ?> </dd>
		<?php endif ?>

		<?php do_action( 'tribe_events_single_meta_venue_section_end' ) ?>
	</dl>
</div>
