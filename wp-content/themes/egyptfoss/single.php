<?php
/**
 * Single Post
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package egyptfoss
 */

get_header(); ?>
	<header class="page-header">
		<div class="container">
			<div class="row">
				<div class="col-md-7">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</div>
				<div class="col-md-5 hidden-xs">
					<?php if (function_exists('template_breadcrumbs')) template_breadcrumbs(); ?>
				</div>
			</div>
		</div>
	</header>
	<!-- .entry-header -->
	<div class="container">
		<div class="row">
			<div id="primary" class="content-area col-md-12">
				<main id="main" class="site-main" role="main">

					<?php
		while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/content', get_post_format() );

			the_post_navigation();

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

			</div>
			<!-- #primary -->
		</div>
	</div>


	<?php
get_sidebar();
get_footer();