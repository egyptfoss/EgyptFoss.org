<?php
/**
 * Template Name: Survey Result.
 *
 * @package egyptfoss
 */

get_header(); 
?>

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
		<div id="primary" class="content-area col-md-12">
			<div class="row">
				<div class="col-md-12 text-center welcome">
					<h1 class="color-primary"><?php echo __('Welcome To the Marketplace','egyptfoss'); ?></h1>
					<p><?php echo __('Offer your services to grow your business or find the right service providers for your needs.','egyptfoss'); ?></p>
				</div>
			</div>
			<div class="row text-center mt60_px">
				<div class="col-md-4">
					<img src="<?php echo get_template_directory_uri(); ?>/img/offer_service.svg" alt="Offer Service">
					<h3>
						<?php if ( !is_user_logged_in() ) { ?>
						<a href="<?php echo home_url( pll_current_language().'/login/?redirected=respondtoservice&redirect_to='.get_current_lang_page_by_template("MarketPlace/template-add-service.php") ); ?>">
						<?php } else if (current_user_can('add_new_ef_posts')) { ?>
						<a href="<?php echo get_current_lang_page_by_template("MarketPlace/template-add-service.php"); ?>">
						<?php } else { ?>
						<!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
						<a href="javascript:void(0)" class="disabled" data-toggle="tooltip" data-placement="top" title="<?php _e('You are not authorized to perform this action. Please contact us for more information.', 'egyptfoss'); ?>">
						<?php } ?>
							<?php echo __('Offer Your Services','egyptfoss'); ?></h3>
						</a>
					<p><?php echo __('Offer your services to receive requests from interested members.','egyptfoss'); ?></p>
				</div>
				<div class="col-md-4">
					<img src="<?php echo get_template_directory_uri(); ?>/img/find_talents.svg" alt="Offer Service">
					<h3><a href="<?php echo $list_url; ?>"><?php echo __('Find the Right Providers','egyptfoss'); ?></a></h3>
					<p><?php echo __('Find the right service providers for your needs.','egyptfoss'); ?></p>
				</div>
				<div class="col-md-4">
					<img src="<?php echo get_template_directory_uri(); ?>/img/talk.svg" alt="Offer Service">
					<h3><?php echo __('Discuss and Review','egyptfoss'); ?></h3>
					<p><?php echo __('Discuss with service providers in private messages and rate the service once done.','egyptfoss'); ?></p>
				</div>
			</div>
			<div class="row text-center mt60_px">
				<div class="col-md-12">
					<h2 class="section-title-services">
						<?php echo __('Browse Services','egyptfoss'); ?>
					</h2>
				</div>
			</div>
			<div class="services-categories-home">
				<div class="row">
					<?php $categories = get_terms('service_category', array('hide_empty' => 0, 'number' => 15, 'orderby' => 'count', 'order' => 'DESC'));
					if( !empty( $categories ) && !is_wp_error( $categories ) ){ ?>
						<div class="col-md-4">
							<ul class="list-unstyled services-list">
								<?php for ($i = 0 ; $i < count($categories); $i++) {
									if($i > 0 && ($i % 5 == 0)) { ?>
											</ul>
										</div>
										<div class="col-md-4">
											<ul class="list-unstyled services-list">
									<?php }
									$category = $categories[$i];
									$category_label = ((pll_current_language() == "ar") && !empty($category->name_ar)) ? $category->name_ar : $category->name; ?>
									<li>
										<a href="<?php echo $list_url.'?category='.$category_label ?>"><?php echo $category_label; ?></a>
										<span class="inner-count">(<?php echo $category->count; ?>)</span>
									</li>
								<?php } ?>
							</ul>
						</div>
					<?php } ?>
				</div>
				<div class="row">
					<div class="col-md-12 text-center">
						<a href="<?php echo $list_url; ?>" class="btn btn-link"><?php echo __('View All Services','egyptfoss'); ?></a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="cat-box-bar">
							<div class="icon">
								<img src="<?php echo get_template_directory_uri(); ?>/img/service_icon_green.png" alt="Offer Service">
							</div>
							<div class="cat-content">
								<h2 class="color-primary"><?php echo __('Start Selling Your Services Now','egyptfoss'); ?></h2>
								<p><?php echo __('Start selling your services to grow your business.','egyptfoss'); ?></p>
							</div>
							<div class="cat-btn">
								<?php if ( !is_user_logged_in() ) { ?>
								<a href="<?php echo home_url( pll_current_language().'/login/?redirected=respondtoservice&redirect_to='.get_current_lang_page_by_template("MarketPlace/template-add-service.php") ); ?>" class="btn btn-primary btn-lg">
								<?php } else if (current_user_can('add_new_ef_posts')) { ?>
								<a href="<?php echo get_current_lang_page_by_template("MarketPlace/template-add-service.php"); ?>" class="btn btn-primary btn-lg">
								<?php } else { ?>
								<!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
								<a href="javascript:void(0)" class="btn btn-primary btn-lg disabled rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e('You are not authorized to perform this action. Please contact us for more information.', 'egyptfoss'); ?>">
								<?php } ?>
									<?php echo __('Start Now','egyptfoss'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div><!-- #primary -->

<?php get_footer();?>
