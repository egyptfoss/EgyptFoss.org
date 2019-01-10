<?php
/**
 * Template Name: Products Home.
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
  	        <div class="col-md-3">
            <div class="side-menu">
                <ul class="categories-list industry-list">
                    <li class="<?php if (!isset($getParams['industry'])) { ?> active <?php } ?>">
                        <a href="" class="filter-class leftFilter" data-id="" onclick="return false;"><?php _e('All Products', 'egyptfoss'); ?>

                        </a>
                    </li>
                    <?php
                    $industries = get_terms('industry', array('hide_empty' => 0));
                    foreach ($industries as $industry) {
                      ?>
                      <li class="<?php if ($getParams['industry'] == $industry->slug) { ?> active <?php } ?>">
                          <a href="" data-slug="<?php echo $industry->slug  ?>" class="filter-class leftFilter" data-id="<?php echo $industry->term_taxonomy_id ?>" onclick="return false;"
                             <?php if ($getParams['industry'] == $industry->slug) { ?> selected="selected" <?php } ?>
                             ><?php echo $industry->name ?>

                          </a>

                      </li>
                    <?php } ?>
                </ul>
            </div>

        </div>
	  	<div id="primary" class="content-area col-md-9">
	  	<div class="row">
<section class="featured-group">
	<div class="group-header">
		<h4 itemprop="name"><?php _e('Top MIT Products','egyptfoss') ?></h4>
		<a href="#" class="rfloat"><?php _e('See more','egyptfoss') ?></a>
	</div>
	<div class="group-content">
		<ul class="default-carousel" itemscope itemtype="http://schema.org/ItemList">
			<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/google-chrome.png" alt="Chrome">
				</div>
				<h5 itemprop="name"><a href="#">Chrome</a></h5>
				<small itemprop="offeredBy" class="product-author">
					Google, Inc
				</small>
			</li>
							<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/google-chrome.png" alt="Chrome">
				</div>
				<h5 itemprop="name"><a href="#">Chrome</a></h5>
				<small itemprop="offeredBy" class="product-author">
					Google, Inc
				</small>
			</li>
							<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/google-chrome.png" alt="Chrome">
				</div>
				<h5 itemprop="name"><a href="#">Chrome</a></h5>
				<small itemprop="offeredBy" class="product-author">
					Google, Inc
				</small>
			</li>
							<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/google-chrome.png" alt="Chrome">
				</div>
				<h5 itemprop="name"><a href="#">Chrome</a></h5>
				<small itemprop="offeredBy" class="product-author">
					Google, Inc
				</small>
			</li>
							<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/google-chrome.png" alt="Chrome">
				</div>
				<h5 itemprop="name"><a href="#">Chrome</a></h5>
				<small itemprop="offeredBy" class="product-author">
					Google, Inc
				</small>
			</li>
					<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/dropbox.png" alt="Dropbox">
				</div>
				<h5 itemprop="name"><a href="#">Dropbox</a></h5>
				<small itemprop="offeredBy" class="product-author">
					Dropbox, Inc
				</small>
			</li>
						<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/opoff.png" alt="Dropbox">
				</div>
				<h5 itemprop="name"><a href="#">Open Office</a></h5>
				<small itemprop="offeredBy" class="product-author">
					OpenOffice, Inc
				</small>
			</li>
			<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/inkscape.png" alt="Dropbox">
				</div>
				<h5 itemprop="name"><a href="#">Inscape</a></h5>
				<small itemprop="offeredBy" class="product-author">
					Inkscape, Inc
				</small>
			</li>
			<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/opoff.png" alt="Dropbox">
				</div>
				<h5 itemprop="name"><a href="#">Open Office</a></h5>
				<small itemprop="offeredBy" class="product-author">
					OpenOffice, Inc
				</small>
			</li>
						<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/inkscape.png" alt="Dropbox">
				</div>
				<h5 itemprop="name"><a href="#">Inscape</a></h5>
				<small itemprop="offeredBy" class="product-author">
					Inkscape, Inc
				</small>
			</li>
		</ul>
	</div>
</section>

 	<section class="featured-group">
	<div class="group-header">
		<h4 itemprop="name"><?php _e('Top MIT Products','egyptfoss') ?></h4>
		<a href="#" class="rfloat"><?php _e('See more','egyptfoss') ?></a>
	</div>
	<div class="group-content">
		<ul class="default-carousel" itemscope itemtype="http://schema.org/ItemList">
			<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/google-chrome.png" alt="Chrome">
				</div>
				<h5 itemprop="name"><a href="#">Chrome</a></h5>
				<small itemprop="offeredBy" class="product-author">
					Google, Inc
				</small>
			</li>
					<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/dropbox.png" alt="Dropbox">
				</div>
				<h5 itemprop="name"><a href="#">Dropbox</a></h5>
				<small itemprop="offeredBy" class="product-author">
					Dropbox, Inc
				</small>
			</li>
						<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/opoff.png" alt="Dropbox">
				</div>
				<h5 itemprop="name"><a href="#">Open Office</a></h5>
				<small itemprop="offeredBy" class="product-author">
					OpenOffice, Inc
				</small>
			</li>
			<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/inkscape.png" alt="Dropbox">
				</div>
				<h5 itemprop="name"><a href="#">Inscape</a></h5>
				<small itemprop="offeredBy" class="product-author">
					Inkscape, Inc
				</small>
			</li>
			<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/opoff.png" alt="Dropbox">
				</div>
				<h5 itemprop="name"><a href="#">Open Office</a></h5>
				<small itemprop="offeredBy" class="product-author">
					OpenOffice, Inc
				</small>
			</li>
						<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
					<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/inkscape.png" alt="Dropbox">
				</div>
				<h5 itemprop="name"><a href="#">Inscape</a></h5>
				<small itemprop="offeredBy" class="product-author">
					Inkscape, Inc
				</small>
			</li>
		</ul>
	</div>
</section>

	  	</div>
	</div><!-- #primary -->

	</div>
</div>

<?php get_footer();?>
