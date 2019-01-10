<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package egyptfoss
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
<div class="container">
	<div class="row">
		<div class="col-md-12">
							<div class="page-content">
				<div class="empty-box">
					<img src="<?php echo get_template_directory_uri(); ?>/img/500.svg" alt="error" class="error-icon">
					 <h1 class="entry-title color-primary"><?php esc_html_e( 'Internal Server Error.', 'egyptfoss' ); ?></h1>

<div class="error-page-search">
	<h3><?php esc_html_e( 'We are currently down.', 'egyptfoss' ); ?></h3>

					</div>

				</div>
				</div><!-- .page-content -->
		</div>
	</div>
</div>
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
