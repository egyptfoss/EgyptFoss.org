<?php
/**
 * Single Event Meta (Organizer) Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/modules/meta/details.php
 *
 * @package TribeEventsCalendar
 */

$organizer_ids = tribe_get_organizer_ids();
$multiple = count( $organizer_ids ) > 1;

$phone = tribe_get_organizer_phone();
$email = tribe_get_organizer_email();
$website = tribe_get_organizer_website_link();
?>

<div class="tribe-events-meta-group tribe-events-meta-group-organizer" style="width: 100% !important;">
	<h3 class="tribe-events-single-section-title"><?php esc_html_e( tribe_get_organizer_label( ! $multiple ), 'egyptfoss' ); ?></h3>
	<dl>
		<?php
		do_action( 'tribe_events_single_meta_organizer_section_start' );

		foreach ( $organizer_ids as $organizer ) {
			if ( ! $organizer ) {
				continue;
			}

			?>
			<dd class="tribe-organizer">
				<?php echo tribe_get_organizer( $organizer ) ?>
			</dd>
			<?php
		}

		if ( ! $multiple ) { // only show organizer details if there is one
			if ( ! empty( $phone ) ) {
				?>
				<dt>
					<?php esc_html_e( 'Phone', 'egyptfoss' ) ?>:
				</dt>
				<dd class="tribe-organizer-tel">
					<?php echo esc_html( $phone ); ?>
				</dd>
				<?php
			}//end if

			if ( ! empty( $email ) ) {
				?>
				<dt>
					<?php esc_html_e( 'Email', 'egyptfoss' ) ?>:
				</dt>
				<dd class="tribe-organizer-email">
                                    <a href="mailto:<?php echo esc_html( $email ); ?>"><?php echo esc_html( $email ); ?></a>
				</dd>
				<?php
			}//end if

			if ( ! empty( $website ) ) {
				?>
				<dt>
					<?php esc_html_e( 'Website', 'egyptfoss' ) ?>:
				</dt>
				<dd class="tribe-organizer-url">
					<?php echo $website; ?>
				</dd>
				<?php
			}//end if
		}//end if

		do_action( 'tribe_events_single_meta_organizer_section_end' );
		?>
	</dl>
</div>
