<?php
/**
 * Template Name: Requests Center Listing.
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
<div class="row ft-padding-top">
     <div class="col-md-12">
     	 <div class="well alert alert-dismissable text-center add-story-intro fade in">
        <div class="row">
        	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
						</button>
        </div>
        <div class="row">
    <div class="col-md-12">
    	<h1 class="color-primary">Welcome To Requests Center</h1>
    	<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type </p>
    </div>

          </div>
          <div class="row">
          	<a class="btn btn-primary" data-dismiss="alert"><?php _e("OK","egyptfoss") ?></a>
          </div>
          </div>
     </div>
</div>
	<div class="row">
  	        <div class="col-md-3">
            <div class="side-menu">
            <h3 class="hidden-xs"><?php _e('Browse By Request Type','egyptfoss') ?></h3>
            <a href="#" class="open-list visible-xs"><h3><i class="fa fa-list-ul"></i> <?php _e('Browse By Request Type','egyptfoss') ?></h3></a>
            <ul class="type-list hidden-xs collapsable-list">
               <li>
               	<a href="#"><img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/service_icon.svg" width="22" alt="<?php _e('Service Request','egyptfoss') ?>"><?php _e('Service Request','egyptfoss') ?></a>
               </li>
                <li>
               	<a href="#"><img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/business_relation_icon.svg" width="22" alt="<?php _e('Busines Releations','egyptfoss') ?>"><?php _e('Busines Releations','egyptfoss') ?></a>
               </li>
                <li>
               	<a href="#"><img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/support_icon.svg" width="22" alt="<?php _e('Support Requests','egyptfoss') ?>"><?php _e('Support Requests','egyptfoss') ?></a>
               </li>
               <li>
               	<a href="#"><img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/product_icon.svg" width="22" alt="<?php _e('Product Requests','egyptfoss') ?>"><?php _e('Products Requests','egyptfoss') ?></a>
               </li>
                <li>
               	<a href="#"><img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/resource_icon.svg" width="22" alt="<?php _e('Resources Requests','egyptfoss') ?>"><?php _e('Resources Requests','egyptfoss') ?></a>
               </li>
                <li>
               	<a href="#"><img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/data_icon.svg" width="22" alt="<?php _e('Datasets Requests','egyptfoss') ?>"><?php _e('Datasets Requests','egyptfoss') ?></a>
               </li>
            </ul>
            </div>

        </div>
	  	<div id="primary" class="content-area col-md-9">
	  	<div class="row">
	  		<div class="col-md-12">
	  			<a href="http://foss.espace.ws/en/open-datasets/add" class="btn btn-light rfloat"><i class="fa fa-plus"></i> Suggest Open Dataset</a>
	  		</div>
	  	</div>
	  	<div class="row">
       <div class="col-md-12">
       	<div class="wide-card request-card">
       		<div class="wide-card-body clearfix">
       		<div class="thumb-side">
       					<div class="card-icon-thumbnail">
       				<img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/service_icon.svg" width="22" alt="<?php _e('Service Request','egyptfoss') ?>">
       			</div>
       		</div>
       			<div class="card-summary">
       				<h3><a href="#">Looking for software company to maintain our company website</a></h3>
       				<div class="request-info-meta">

       				</div>
       				<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. </p>
       			</div>
       		</div>
       		<div class="wide-card-bottom clearfix">
       			<div class="col-md-6">
       			<strong>Target Business Relationship</strong>
       					<span>Commercial Cooperation</span>
       			</div>
       			<div class="col-md-3"><strong>Theme</strong>Security</div>
       			<div class="col-md-3"><strong><i class="fa fa-clock-o"></i> Due Date</strong>13/6/2016</div>
       		</div>
       	</div>

       	  	<div class="wide-card request-card">
       		<div class="wide-card-body clearfix">
       		<div class="thumb-side">
       					<div class="card-icon-thumbnail">
       				<img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/business_relation_icon.svg" width="22" alt="<?php _e('Business Relation Request','egyptfoss') ?>">
       			</div>
       		</div>
       			<div class="card-summary">
       				<h3><a href="#">Looking for software company to maintain our company website</a></h3>
       				<div class="request-info-meta">

       				</div>
       				<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. </p>
       			</div>
       		</div>
       		<div class="wide-card-bottom clearfix">
       			<div class="col-md-6">
       			<strong>Target Business Relationship</strong>
       					<span>Commercial Cooperation</span>
       			</div>
       			<div class="col-md-3"><strong>Theme</strong>Security</div>
       			<div class="col-md-3"><strong><i class="fa fa-clock-o"></i> Due Date</strong>13/6/2016</div>
       		</div>
       	</div>

       	  	<div class="wide-card request-card">
       		<div class="wide-card-body clearfix">
       		<div class="thumb-side">
       					<div class="card-icon-thumbnail">
       				<img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/support_icon.svg" width="22" alt="<?php _e('Support Request','egyptfoss') ?>">
       			</div>
       		</div>
       			<div class="card-summary">
       				<h3><a href="#">Looking for software company to maintain our company website</a></h3>
       				<div class="request-info-meta">

       				</div>
       				<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. </p>
       			</div>
       		</div>
       		<div class="wide-card-bottom clearfix">
       			<div class="col-md-6">
       			<strong>Target Business Relationship</strong>
       					<span>Commercial Cooperation</span>
       			</div>
       			<div class="col-md-3"><strong>Theme</strong>Security</div>
       			<div class="col-md-3"><strong><i class="fa fa-clock-o"></i> Due Date</strong>13/6/2016</div>
       		</div>
       	</div>

       	  	<div class="wide-card request-card">
       		<div class="wide-card-body clearfix">
       		<div class="thumb-side">
       					<div class="card-icon-thumbnail">
       				<img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/product_icon.svg" width="22" alt="<?php _e('Product Request','egyptfoss') ?>">
       			</div>
       		</div>
       			<div class="card-summary">
       				<h3><a href="#">Looking for software company to maintain our company website</a></h3>
       				<div class="request-info-meta">

       				</div>
       				<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. </p>
       			</div>
       		</div>
       		<div class="wide-card-bottom clearfix">
       			<div class="col-md-6">
       			<strong>Target Business Relationship</strong>
       					<span>Commercial Cooperation</span>
       			</div>
       			<div class="col-md-3"><strong>Theme</strong>Security</div>
       			<div class="col-md-3"><strong><i class="fa fa-clock-o"></i> Due Date</strong>13/6/2016</div>
       		</div>
       	</div>

       	      	  	<div class="wide-card request-card">
       		<div class="wide-card-body clearfix">
       		<div class="thumb-side">
       					<div class="card-icon-thumbnail">
       				<img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/data_icon.svg" width="22" alt="<?php _e('Data Request','egyptfoss') ?>">
       			</div>
       		</div>
       			<div class="card-summary">
       				<h3><a href="#">Looking for software company to maintain our company website</a></h3>
       				<div class="request-info-meta">

       				</div>
       				<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. </p>
       			</div>
       		</div>
       		<div class="wide-card-bottom clearfix">
       			<div class="col-md-6">
       			<strong>Target Business Relationship</strong>
       					<span>Commercial Cooperation</span>
       			</div>
       			<div class="col-md-3"><strong>Theme</strong>Security</div>
       			<div class="col-md-3"><strong><i class="fa fa-clock-o"></i> Due Date</strong>13/6/2016</div>
       		</div>
       	</div>

       	<div class="wide-card request-card">
       		<div class="wide-card-body clearfix">
       		<div class="thumb-side">
       					<div class="card-icon-thumbnail">
       				<img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/resource_icon.svg" width="22" alt="<?php _e('Resources Request','egyptfoss') ?>">
       			</div>
       		</div>
       			<div class="card-summary">
       				<h3><a href="#">Looking for software company to maintain our company website</a></h3>
       				<div class="request-info-meta">

       				</div>
       				<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. </p>
       			</div>
       		</div>
       		<div class="wide-card-bottom clearfix">
       			<div class="col-md-6">
       			<strong>Target Business Relationship</strong>
       					<span>Commercial Cooperation</span>
       			</div>
       			<div class="col-md-3"><strong>Theme</strong>Security</div>
       			<div class="col-md-3"><strong><i class="fa fa-clock-o"></i> Due Date</strong>13/6/2016</div>
       		</div>
       	</div>

       </div>
 	  </div>
	</div><!-- #primary -->

	</div>
</div>

<?php get_footer();?>
