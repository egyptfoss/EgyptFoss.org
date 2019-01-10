<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package egyptfoss
 */
?>

	</div><!-- #content -->

	<footer  class="site-footer" role="footer">
<div class="container">
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12">
          		<!--<img src="<?php // echo get_template_directory_uri(); ?>/img/footer-logo.png" alt="<?php // _e('EgyptFOSS','egyptfoss') ?>" class="footer-logo"> -->
          		<div class="download-apps sm-center">
                  <h4><?php _e("Mobile Apps","egyptfoss"); ?></h4>
                  <a href="https://itunes.apple.com/us/app/egyptfoss/id1179734318" title="<?php _e("Get the mobile App","egyptfoss"); ?>" data-toggle="tooltip" data-placement="top" class="app-badges appstore-badge"></a>
                  <a href="https://play.google.com/store/apps/details?id=org.egyptfoss" data-toggle="tooltip" data-placement="top" title="<?php _e("Get the mobile App","egyptfoss"); ?>" target="_blank" class="app-badges playstore-badge"></a>
          		</div>
          </div>
       <div class="col-md-4 col-sm-12 col-xs-12 text-center">
          <div class="social-follow">
               <a href="https://www.facebook.com/EgyptFOSSOrg" target="_blank"><i class="fa fa-facebook-f"></i></a>
               <a href="https://twitter.com/EgyptFOSSOrg" target="_blank"><i class="fa fa-twitter"></i></a>
               <a href="https://www.linkedin.com/company/egyptfossorg" target="_blank"><i class="fa fa-linkedin"></i></a>
              </div>
            </div>
            <div class="col-md-4 sm-center">
            	<div class="sponsors rfloat sm-no-float">
                       <a href="http://www.mcit.gov.eg/" target="_blank" title="MCIT">
           		<img src="<?php echo get_template_directory_uri(); ?>/img/mcit_logo.png" alt="MCIT">
           	</a>

           	<a href="http://www.itida.gov.eg/" target="_blank" title="ITIDA">
           		<img src="<?php echo get_template_directory_uri(); ?>/img/itida.png" alt="ITIDA">
           	</a>
           	<a href="http://www.secc.org.eg/" target="_blank" title="SECC Egypt">
           		<img src="<?php echo get_template_directory_uri(); ?>/img/secc.png" alt="SECC">
           	</a>
           </div>
            </div>
    </div>
    <div class="row">
    	<div class="license-note clearfix">
        <img src="<?php echo get_template_directory_uri(); ?>/img/cc_logo.png" height="24" alt="Creative Common">
        <?php echo _e("This work is licensed under a","egyptfoss"); ?>
        <a href="https://creativecommons.org/licenses/by/4.0/" target="_blank"><?php echo _e("Creative Commons Attribution 4.0 International License.","egyptfoss"); ?> </a>
      </div>
    </div>
</div>
	</footer>
	<div class="bottom-bar text-center">
		<div class="container">
			<ul class="footer-links">
						<li>
              <a href="<?php echo home_url(pll_current_language()."/terms-of-services") ?>"><?php _e("Terms of Services","egyptfoss"); ?></a>
						</li>
						<li>
              <a href="<?php echo home_url(pll_current_language()."/privacy-policy") ?>"><?php echo ucwords(__("Privacy policy","egyptfoss")); ?></a>
						</li>
						<li>
                <a href="mailto:info@egyptfoss.org"><?php echo ucwords(__("Contact us","egyptfoss")); ?></a>
						</li>
            <li>
              <a href="<?php echo home_url(pll_current_language()."/feedback/add/") ?>"><?php echo ucwords(__("Submit feedback","egyptfoss")); ?></a>
            </li>
            <li>
                  <a href="<?php echo home_url( pll_current_language()."/developers" ); ?>"><?php _e('Developers', 'egyptfoss'); ?></a>
            </li>
						<li>
							<a href="<?php echo home_url(pll_current_language()."/partners") ?>"><?php echo ucwords(__("Partners","egyptfoss")); ?></a>
						</li>
      </ul>
		</div>
	</div>
	<!-- #colophon -->
</div><!-- #page -->
<?php wp_footer(); ?>
<div id="mobile-nav">
		<div class="mobile-search clearfix">
		<form action="<?php bloginfo('url'); ?>/" method="get" id="searchform">
					<button class="lfloat"><i class="fa fa-search"></i></button>
			<input type="search" placeholder="<?php _e('Search','egyptfoss')?>" value="<?php the_search_query(); ?>" name="s" id="s">
		</form>
		</div>
				<div id="mobile-menu">
									<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu','container'=> 'div', 'container_class' => 'mobile-menu', 'menu_class' => 'mobile-nav') ); ?>
				</div>
    <!--<hr> -->
    <?php include( ABSPATH . 'system_data.php' ); ?>
				<ul>
                                    <?php foreach($ef_top_nav as $nav) { ?>
                                      <li>
                                        <a href="/<?php echo pll_current_language().$nav['url']; ?>"><?php _e($nav['name'],"egyptfoss"); ?></a>
                                      </li>
                                        <?php }

                                        ?>
				</ul>
	</div>
</body>
</html>
