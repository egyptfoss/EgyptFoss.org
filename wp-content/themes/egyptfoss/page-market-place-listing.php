<?php
/**
 * Template Name: Marketplace Listing.
 *
 * @package egyptfoss
 */

$getParams = $_GET;
get_header(); ?>
	<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-12 no-padding">
	 				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	 				<a href="#" class="open-related btn btn-outline active rfloat">Related Documents <i class="fa fa-angle-down"></i></a>
	 				<div class="related-docs-container">
	 				   <div class="related-panel">
	 				     <ul class="clearfix">
	 				         <li>
	 				             <div class="file-icon">
	 				                 <i class="fa fa-file"></i>
	 				             </div>
	 				             <div class="file-name">
	 				                 <a href="#">How to provide service that sell</a>
	 				                 <br>
	 				                 <small class="post-date"><i class="fa fa-clock-o"></i> 15 Jul, 2016  <i class="fa fa-user"></i> <a href="#">Ashraf Kotb</a></small>
	 				             </div>
	 				         </li>
	 				            <li>
	 				             <div class="file-icon">
	 				                 <i class="fa fa-file"></i>
	 				             </div>
	 				             <div class="file-name">
	 				                 <a href="#">Top rated market service report</a>
	 				                 <br>
	 				                 <small class="post-date"><i class="fa fa-clock-o"></i> 15 Jul, 2016  <i class="fa fa-user"></i> <a href="#">Ashraf Kotb</a></small>
	 				             </div>
	 				         </li>
	 				            <li>
	 				             <div class="file-icon">
	 				                 <i class="fa fa-file"></i>
	 				             </div>
	 				             <div class="file-name">
	 				                 <a href="#">Web Development Prices</a>
	 				                 <br>
	 				                 <small class="post-date"><i class="fa fa-clock-o"></i> 15 Jul, 2016  <i class="fa fa-user"></i> <a href="#">Ashraf Kotb</a></small>
	 				             </div>
	 				         </li>
	 				            <li>
	 				             <div class="file-icon">
	 				                 <i class="fa fa-file"></i>
	 				             </div>
	 				             <div class="file-name">
	 				                 <a href="#">How to provide service that sell</a>
	 				                 <br>
	 				                 <small class="post-date"><i class="fa fa-clock-o"></i> 15 Jul, 2016  <i class="fa fa-user"></i> <a href="#">Ashraf Kotb</a></small>
	 				             </div>
	 				         </li>
	 				         <li class="text-center"><a href="#" class="see-more-link">View All</a></li>
	 				     </ul>
	 				   </div>
	 				</div>
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->

<div class="container">
	<div class="row">
  	        <div class="col-md-3">
            <div class="side-menu">
            <ul class="categories-list">
               <li class="active">
               	<a href="#">Web Development</a>
               </li>
               <li>
               	<a href="#">SEO& Content</a>
               </li>
               <li>
               	<a href="#">Graphic Design</a>
               </li>
               <li>
               	<a href="#">Web Developer</a>
               </li>
               <li><a href="#">Website Builders & CMS</a></li>
               <li><a href="#">User Testing</a></li>
            </ul>
            </div>

        </div>
	  	<div id="primary" class="content-area col-md-9">
	  	<div class="row filter-bar">
	  		<div class="col-md-12">
	  			<a href="#" class="btn btn-primary rfloat"><i class="fa fa-plus"></i> Suggest Service</a>
	  		</div>
	  	</div>
<div class="row">
    <div class="col-md-12">
       <section class="filter-nav">
         <div class="row form-group">
            <div class="col-md-2">
                <select name="" id="" class="form-control">
                    <option value="">Type</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="" id="" class="form-control">
                    <option value="">Sub Type</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="" id="" class="form-control">
                    <option value="">Interests</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="" id="" class="form-control">
                    <option value="">Technologies</option>
                </select>
            </div>
            <button class="btn btn-link reset-filters rfloat"><i class="fa fa-remove"></i> Reset </button>
        </div>
       </section>
    </div>
</div>
<div class="row">
    <div class="services-grid">
        <div class="service-card">
           <div class="inner">
               <div class="service-cover">
                   <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/service_cover.jpg" alt="">
                   <span class="provider-type-label indv">
                      <i class="fa fa-user"></i> Individual
                   </span>
               </div>
               <div class="card-content">
                  <h4>
                     <a href="#">
                      Ruby On Rail Development / Wordpress Theme Customiz...
                     </a>
                  </h4>
                  <small>Offered by <a href="#">Zaki Ibrahim</a></small>
               </div>
               <div class="card-footer clearfix">
                   <div class="rating-stars lfloat">
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star-o"></i>
                       <i class="fa fa-star-o"></i>
                       <span class="rating-count" title="Rated by 22 customer">(22)</span>
                   </div>
                   <a href="#" class="rfloat small-link">
                   <i></i>
                   More Servcies
                   </a>
               </div>
           </div>
       </div>
        <div class="service-card">
           <div class="inner">
               <div class="service-cover">
                   <img src="<?php echo get_template_directory_uri(); ?>/img/empty_service_cover.png" alt="">
                   <span class="provider-type-label entity">
                      <i class="fa fa-building-o"></i> Entity
                   </span>
               </div>
               <div class="card-content">
                  <h4>
                     <a href="#">
                      Ruby On Rail Development / Wordpress Theme Customiz...
                     </a>
                  </h4>
                  <small>Offered by <a href="#">eSpace Technologies</a></small>
               </div>
               <div class="card-footer clearfix">
                   <div class="rating-stars lfloat">
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star-o"></i>
                       <i class="fa fa-star-o"></i>
                       <span class="rating-count" title="Rated by 22 customer">(22)</span>
                   </div>
                   <a href="#" class="rfloat small-link">
                   <i></i>
                   More Servcies
                   </a>
               </div>
           </div>
       </div>
               <div class="service-card">
           <div class="inner">
               <div class="service-cover">
                   <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/mobile_development.jpg" alt="">
                   <span class="provider-type-label entity">
                      <i class="fa fa-building-o"></i> Entity
                   </span>
               </div>
               <div class="card-content">
                  <h4>
                     <a href="#">
                      Creating Class A Mobile Apps with Ionic and HTML5
                     </a>
                  </h4>
                  <small>Offered by <a href="#">eSpace Technologies</a></small>
               </div>
               <div class="card-footer clearfix">
                   <div class="rating-stars lfloat">
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star-o"></i>
                       <i class="fa fa-star-o"></i>
                       <span class="rating-count" title="Rated by 22 customer">(22)</span>
                   </div>
                   <a href="#" class="rfloat small-link">
                   <i></i>
                   More Servcies
                   </a>
               </div>
           </div>
       </div>
               <div class="service-card">
           <div class="inner">
               <div class="service-cover">
                   <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/service_cover.jpg" alt="">
                   <span class="provider-type-label indv">
                      <i class="fa fa-user"></i> Individual
                   </span>
               </div>
               <div class="card-content">
                  <h4>
                     <a href="#">
                      Ruby On Rail Development / Wordpress Theme Customiz...
                     </a>
                  </h4>
                  <small>Offered by <a href="#">Zaki Ibrahim</a></small>
               </div>
               <div class="card-footer clearfix">
                   <div class="rating-stars lfloat">
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star-o"></i>
                       <i class="fa fa-star-o"></i>
                       <span class="rating-count" title="Rated by 22 customer">(22)</span>
                   </div>
                   <a href="#" class="rfloat small-link">
                   <i></i>
                   More Servcies
                   </a>
               </div>
           </div>
       </div>
        <div class="service-card">
           <div class="inner">
               <div class="service-cover">
                   <img src="<?php echo get_template_directory_uri(); ?>/img/empty_service_cover.png" alt="">
                   <span class="provider-type-label entity">
                      <i class="fa fa-building-o"></i> Entity
                   </span>
               </div>
               <div class="card-content">
                  <h4>
                     <a href="#">
                      Ruby On Rail Development / Wordpress Theme Customiz...
                     </a>
                  </h4>
                  <small>Offered by <a href="#">eSpace Technologies</a></small>
               </div>
               <div class="card-footer clearfix">
                   <div class="rating-stars lfloat">
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star-o"></i>
                       <i class="fa fa-star-o"></i>
                       <span class="rating-count" title="Rated by 22 customer">(22)</span>
                   </div>
                   <a href="#" class="rfloat small-link">
                   <i></i>
                   More Servcies
                   </a>
               </div>
           </div>
       </div>
               <div class="service-card">
           <div class="inner">
               <div class="service-cover">
                   <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/mobile_development.jpg" alt="">
                   <span class="provider-type-label entity">
                      <i class="fa fa-building-o"></i> Entity
                   </span>
               </div>
               <div class="card-content">
                  <h4>
                     <a href="#">
                      Creating Class A Mobile Apps with Ionic and HTML5
                     </a>
                  </h4>
                  <small>Offered by <a href="#">eSpace Technologies</a></small>
               </div>
               <div class="card-footer clearfix">
                   <div class="rating-stars lfloat">
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star"></i>
                       <i class="fa fa-star-o"></i>
                       <i class="fa fa-star-o"></i>
                       <span class="rating-count" title="Rated by 22 customer">(22)</span>
                   </div>
                   <a href="#" class="rfloat small-link">
                   <i></i>
                   More Servcies
                   </a>
               </div>
           </div>
       </div>
       <div class="row text-center">
           <button type="button" class="btn btn-primary loadmore-btn" id="load" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Please wait...">Load more...</button>
       </div>
       <!-- Empty State - Remove class hidden to show -->
       <div class="row empty-state hidden">
           <div class="empty-state-msg">
               <img src="<?php echo get_template_directory_uri(); ?>/img/service_icon.svg" width="64" alt="No Services">
               <br>
               <h3>There are no products in this category</h3>
           </div>
       </div>
       <!-- Empty State End -->
    </div>
</div>
	</div><!-- #primary -->

	</div>
</div>

<?php get_footer();?>
