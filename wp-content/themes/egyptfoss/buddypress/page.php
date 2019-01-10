<?php
/**
 * Template Name: Full Width Template.
 *
 * @package egyptfoss
 */
if ( get_the_title() == 'Members' ) {
	include( get_query_template( '404' ) );
	exit;
} else {
	get_header(); ?>
		<header class="page-header">
		<div class="container">
		 	<div class="row">
		 		<div class="col-md-7">
	                            <h1 class="entry-title"><?php _e(get_the_title(),"egyptfoss"); ?> </h1>
		 		</div>
		 		<div class="col-md-5 hidden-xs">
		 			<?php if (function_exists('template_breadcrumbs')) template_breadcrumbs(); ?>
		 		</div>
		 	</div>
		</div>
		</header><!-- .entry-header -->
	<div class="container">
		<div class="row">
		  	<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

				<?php
				while ( have_posts() ) : the_post();

					get_template_part( 'template-parts/content', 'page' );

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;

				endwhile; // End of the loop.
				?>
			</main><!-- #main -->
		</div><!-- #primary -->
		</div>
	</div>
<?php } ?>
<?php get_footer();?>
