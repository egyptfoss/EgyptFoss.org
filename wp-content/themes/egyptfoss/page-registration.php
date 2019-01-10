<?php
/*
 Template Name: Registration Page
*/
 ?>
<?php
get_header(); 




$sidebar_id = get_meta_option('custom_sidebar', $post->ID);
$sidebar_position = get_meta_option('sidebar_position_meta_box', $post->ID);
$sidebar_class = 'col-lg-8 col-md-8 col-sm-8';
$comm = $post->comment_status;

	if( $sidebar_position == 'left' ) { 
	$sidebar_class = 'col-lg-9 col-md-9 col-sm-8 col-lg-push-3 col-md-push-3 col-sm-push-4';
	 }
	if( $sidebar_position == 'right' ) { 
	$sidebar_class = 'col-lg-9 col-md-9 col-sm-8';
	 }
	if( $sidebar_position == 'full' ) {
	$sidebar_class = 'col-lg-8 col-md-8 col-sm-8';
	 }  
?>
   
   
   
<section id="content">	
			
			<!-- Page Heading -->
			<section class="section page-heading">
				
				<div class="row">
					<div class="col-lg-8 col-md-8 col-sm-8">
						
						<h1><?php echo esc_html(get_the_title()); ?></h1>
						
						
						
						<?php if(get_option('sense_show_breadcrumb') == 'show') { ?>
						<?php candidat_the_breadcrumbs(); ?>
						<?php } ?>
	
					</div>

				</div>
				
			</section>
			<!-- Page Heading -->
	
		<!-- Section -->
		<section class="section full-width-bg gray-bg ashraf">
			
			<div class="row">
			
				<div class="<?php echo esc_attr($sidebar_class); ?>">
				
					<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
					<?php the_content(); ?>
				<?php endwhile; ?>	

				</div>
			
				
				<!-- Sidebar -->
			    <?php 
				if( $sidebar_position != 'full' ) {
					if( $sidebar_position == 'left' ) { ?>
					<div class="col-lg-3 col-md-3 col-sm-4 col-lg-pull-9 col-md-pull-9 col-sm-pull-8 sidebar">
					<?php } if( $sidebar_position == 'right' ) { ?>
					<div class="col-lg-3 col-md-3 col-sm-4 sidebar">
					<?php } ?>
					
					<?php candidat_mm_sidebar('blog',$sidebar_id);?>
					</div>
				<?php } ?>

			</div>
			<div class="row">

		</section>
		<!-- /Section -->
		
	</section>

<?php get_footer(); ?> 