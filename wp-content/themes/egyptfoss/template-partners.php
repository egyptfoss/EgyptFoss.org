<?php
/**
 * Template Name: Partners Archive Page.
 *
 * @package egyptfoss
 */

 get_header(); ?>
 	<header class="page-header">
 	<div class="container">
 	 	<div class="row">
 	 		<div class="col-md-12">
 	 				<?php echo ucwords(the_title( '<h1 class="entry-title">', '</h1>',false )); ?>
 	 	</div>
 	</div>
 </header><!-- .entry-header -->

<div class="container">
	<div class="row">
    <div class="col-md-12 content-area">
			<div class="single-column-content">
			  <div class="row">
		      <div class="col-md-12 text-center">
	         <?php the_content(); ?>
		      </div>
			  </div>
			  <br><br>
			  <div class="row partners-list">
          <?php
          $args = array( 'numberposts' => -1, 'post_type' => 'partner' );
          $partners = get_posts( $args );

     			foreach( $partners as $partner ):
            $partner_name = $partner->post_title;
            $partner_link = get_field('link', $partner->ID);
            ?>
            <div class="col-md-3">
  			  		<a href="<?php echo $partner_link; ?>" class="text-center partner-item" rel="nofollow" target="_blank">
  			  			<div class="parnter-img">
                  <?php echo get_the_post_thumbnail( $partner->ID, 'full',array('alt'=>$partner_name, 'img-responsive center-block') ); ?>
  			  			</div>
  			  			<h4><?php echo $partner_name; ?></h4>
  			  		</a>
  			  	</div>
            <?php
          endforeach; // End of the loop.
 			    ?>
			  </div>
  	  </div>
    </div>
  </div>
</div>
 <?php get_footer();?>
