<?php
/**
 * Template Name: Events.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package egyptfoss
 */
get_header(); ?>
	<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-7">
	 				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	 				<?php post_type_archive_title(); ?>
	 		</div>
	 		<div class="col-md-5 hidden-xs">
	 			<?php if (function_exists('template_breadcrumbs')) template_breadcrumbs(); ?>
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->

<div class="container">
	<div class="row">
	  	<div id="primary" class="content-area col-md-12">
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

<?php get_footer();?>
