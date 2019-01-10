<?php
/**
 * Template used for maps embedded within single events and venues.
 * Override this template in your own theme by creating a file at:
 *
 *     [your-theme]/tribe-events/modules/map.php
 *
 * @var $index
 * @var $width
 * @var $height
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$style = apply_filters( 'tribe_events_embedded_map_style', "height: $height; width: $width", $index );
?>

<?php $venue_gmap = get_post_meta(tribe_get_venue_id(), 'gmap', true);?>
        <?php //if ( tribe_show_google_map_link() ) : ?>
                <?php if($venue_gmap != "") { ?>
                     <input type="hidden" id="lat" name="lat" value="<?php echo $venue_gmap['lat']; ?>">
                     <input type="hidden" id="lng" name="lng" value="<?php echo $venue_gmap['lng']; ?>">
                     <div id="map_canvas"  style="<?php esc_attr_e( $style ) ?>"></div>
        <?php } ?>
<?php //endif; ?>