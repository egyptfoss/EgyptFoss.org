<?php
/**
 * Template Name: Featured Products.
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
               <h3><?php _e('Browse','egyptfoss') ?></h3>
               <ul class="categories-list industry-list">
               	<li><a href="">All Products</a></li>
               	<li><a href="">Featured</a></li>
               </ul>
                <h3 class="find-cat-title"><?php _e("Find by Category", 'egyptfoss'); ?></h3>
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
	  	 <?php
                    get_template_part('template-parts/content', 'product-filter');
                    ?>
	  	<div class="row">
	  				<div class="product-card featured-card" itemscope="" itemtype="http://schema.org/Product" id="345">
	  				<svg width="25" height="30" class="featured-icon" viewBox="0 0 26 30.75" fill="currentColor">
  <defs>

  </defs>
  <path id="featured-icon" class="cls-1" d="M817.647,266.793h-21.29A2.378,2.378,0,0,0,794,269.068v26.186a2.381,2.381,0,0,0,2.357,2.276,2.491,2.491,0,0,0,1.686-.671L807,288.245l8.959,8.614a2.467,2.467,0,0,0,1.686.65A2.316,2.316,0,0,0,820,295.254V269.068A2.378,2.378,0,0,0,817.647,266.793Z" transform="translate(-794 -266.781)"/>
  <path id="_" data-name="" class="cls-2" d="M813,275.055a0.439,0.439,0,0,0-.437-0.36l-3.922-.57-1.758-3.555a0.389,0.389,0,0,0-.766,0l-1.758,3.555-3.921.57a0.433,0.433,0,0,0-.438.36,0.614,0.614,0,0,0,.2.375l2.844,2.765-0.672,3.907a1.017,1.017,0,0,0-.015.156,0.341,0.341,0,0,0,.328.39,0.664,0.664,0,0,0,.312-0.093l3.508-1.844,3.508,1.844a0.641,0.641,0,0,0,.312.093,0.336,0.336,0,0,0,.321-0.39,1.013,1.013,0,0,0-.008-0.156l-0.672-3.907,2.836-2.765A0.587,0.587,0,0,0,813,275.055Z" transform="translate(-794 -266.781)"/>
</svg>
      <div class="product-card-body clearfix">
          <div class="product-img lfloat">
            <a href="#">
             <img src="<?php echo get_template_directory_uri()?>/demo-assets/google-chrome.png"  alt="Google Chrome"></a>
          </div>
          <div class="product-card-info lfloat">
              <h3 class="product-name"><a href="#" itemprop="name">Google Chrome</a></h3>
              <p>Convert video between all key formats: DVD, AVI (DivX, XviD), MP4 (inc. Sony PSP and Apple ...</p>
              <p><strong>By</strong> Google</p>
          </div>
      </div>
      <div class="product-meta">
                        <span class="meta-item">
                  <strong>License</strong> GPL, MIT License</span>
                  <span class="category-label"><i class="fa fa-folder"></i> <strong>Category</strong> <a href="#">Internet</a></span>
      </div>
  </div>
  	<div class="product-card featured-card" itemscope="" itemtype="http://schema.org/Product" id="345">
	  				<svg width="25" height="30" class="featured-icon" viewBox="0 0 26 30.75" fill="currentColor">
  <defs>

  </defs>
  <path id="featured-icon" class="cls-1" d="M817.647,266.793h-21.29A2.378,2.378,0,0,0,794,269.068v26.186a2.381,2.381,0,0,0,2.357,2.276,2.491,2.491,0,0,0,1.686-.671L807,288.245l8.959,8.614a2.467,2.467,0,0,0,1.686.65A2.316,2.316,0,0,0,820,295.254V269.068A2.378,2.378,0,0,0,817.647,266.793Z" transform="translate(-794 -266.781)"/>
  <path id="_" data-name="" class="cls-2" d="M813,275.055a0.439,0.439,0,0,0-.437-0.36l-3.922-.57-1.758-3.555a0.389,0.389,0,0,0-.766,0l-1.758,3.555-3.921.57a0.433,0.433,0,0,0-.438.36,0.614,0.614,0,0,0,.2.375l2.844,2.765-0.672,3.907a1.017,1.017,0,0,0-.015.156,0.341,0.341,0,0,0,.328.39,0.664,0.664,0,0,0,.312-0.093l3.508-1.844,3.508,1.844a0.641,0.641,0,0,0,.312.093,0.336,0.336,0,0,0,.321-0.39,1.013,1.013,0,0,0-.008-0.156l-0.672-3.907,2.836-2.765A0.587,0.587,0,0,0,813,275.055Z" transform="translate(-794 -266.781)"/>
</svg>
      <div class="product-card-body clearfix">
          <div class="product-img lfloat">
            <a href="#">
             <img src="<?php echo get_template_directory_uri()?>/demo-assets/google-chrome.png"  alt="Google Chrome"></a>
          </div>
          <div class="product-card-info lfloat">
              <h3 class="product-name"><a href="#" itemprop="name">Google Chrome</a></h3>
              <p>Convert video between all key formats: DVD, AVI (DivX, XviD), MP4 (inc. Sony PSP and Apple ...</p>
              <p><strong>By</strong> Google</p>
          </div>
      </div>
      <div class="product-meta">
                        <span class="meta-item">
                  <strong>License</strong> GPL, MIT License</span>
                  <span class="category-label"><i class="fa fa-folder"></i> <strong>Category</strong> <a href="#">Internet</a></span>
      </div>
  </div>
	  	</div>
	</div><!-- #primary -->

	</div>
</div>

<?php get_footer();?>
