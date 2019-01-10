<?php
/**
 * The template for displaying 403 pages (access forbidden).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_403_Page
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
								<img src="<?php echo get_template_directory_uri(); ?>/img/403.svg" alt="error" class="error-icon">
								<h1 class="entry-title color-primary"><?php _e( 'Access Denied', 'egyptfoss' ); ?></h1>
								<div class="error-page-search">
									<!--<h3><?php _e( "You don't have permission to access this page or you have signed out.", 'egyptfoss' ); ?></h3>-->
                    <h3><?php _e( "You are not authorized to perform this action.", 'egyptfoss' ); ?></h3>
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
