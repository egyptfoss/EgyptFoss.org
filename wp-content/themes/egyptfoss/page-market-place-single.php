<?php
/**
 * Template Name: Marketplace Single.
 *
 * @package egyptfoss
 */
$getParams = $_GET;
get_header(); ?>
	<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-12">
	 				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->

<div class="container">
	<div class="row">
<div id="primary" class="col-md-8">
<div class="row">
    <div class="col-md-12 content-area">
        <div class="service-name">
    <h2>Speed up your WordPress Speed with Google PageSpeed in 24 hours</h2>
</div>
<div class="service-rating">
    <div class="rating-stars">
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star-o"></i>
                       <i class="fa fa-star-o"></i>
                       <span class="rating-count" title="Rated by 22 customer"><a href="#reviews" class="custom-scroll">(22)</a></span>
                   </div>
</div>
<div class="service-cover">
    <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/wordpress.jpg" alt="">
</div>
<div class="service-content">
    <h3>Description</h3>
    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. </p>

    <h3>Constraints</h3>
    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. </p>
</div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 reviews-area" id="reviews">
           <h3>Reviews (22)</h3>
	   <div class="review-panel clearfix">
          <div class="review-panel-header clearfix">
             <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" class="user-avatar lfloat" alt="">
              <div class="reviewer-identity lfloat">
                  <h3>
           <a href="#">Mostafa Fathy</a>
           </h3>
           <br>
           <small class="post-date">
               <i class="fa fa-clock-o"></i>
               16 Jul, 2016
           </small>
              </div>
                <div class="rating-stars rfloat">
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star-o"></i>
                       <i class="fa fa-star-o"></i>
                   </div>
          </div>
           <p>Great developer project delivered on time.</p>
	   </div>
	      <div class="review-panel clearfix">
          <div class="review-panel-header clearfix">
             <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" class="user-avatar lfloat" alt="">
              <div class="reviewer-identity lfloat">
                  <h3>
           <a href="#">Omar Gaber</a>
           </h3>
           <br>
           <small class="post-date">
               <i class="fa fa-clock-o"></i>
               16 Jul, 2016
           </small>
              </div>
                <div class="rating-stars rfloat">
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star-o"></i>
                       <i class="fa fa-star-o"></i>
                   </div>
          </div>
           <p>Great developer project delivered on time.</p>
	   </div>
	   <div class="view-all text-center">
	       <a href="#" class="btn btn-primary">View All Reviews</a>
	   </div>
    </div>
</div>
	</div>
	<div class="col-md-4">
   <h3>Service Info</h3>
    <ul class="list-group basic-info-box" id="info-bar">
       <li class="list-group-item">
           <div class="provider-avatar lfloat">
               <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/avatar_90.jpg" class="avatar" alt="">
           </div>
           <div class="user-name lfloat">
               <h3><a href="#">Zaki Ibrahim</a></h3>
               <br>
               <small>Software Engineer</small>
           </div>
       </li>
        <li class="list-group-item">
            <strong>Technology</strong>
            <br>
            <span class="technology-tag">PHP</span>
            <span class="technology-tag">MYSql</span>
        </li>
        <li class="list-group-item">
            <strong>Interests</strong>
            <br>
            <span class="technology-tag">Business</span>
            <span class="technology-tag">Internet</span>
            <span class="technology-tag">Marketing</span>
        </li>
        <li class="list-group-item">
            <strong>Theme</strong>
            <br>
           Education
        </li>
          <li class="list-group-item">
            <div class="request-action">
	        <a href="#" class="btn btn-primary btn-block">Request Service</a>
	    </div>
        </li>
    </ul>
	</div>
</div>
<?php get_footer();?>
