<?php
/**
 *Template Name: Front Page.
 *
 * @package egyptfoss
 */

get_header(); ?>

<?php get_template_part('template-parts/content', 'home_highlights'); ?>
<?php get_template_part('template-parts/content', 'home_events'); ?>

<?php
$args = array(
    "current_lang" => pll_current_language()
);
$news = ef_listing_homepage_news($args);
$expert_thoughts = ef_listing_posts( 'expert_thought' );
$success_stories = ef_listing_posts( 'success_story', $args['current_lang'] );
?>
<?php if ( $user_ID == 0 ) {?>
<section class="join-us-box">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			<img src="<?php echo get_template_directory_uri(); ?>/img/join_icon.svg" class="join-icon lfloat" alt="Join Us">
				<h1 class="color-primary lfloat"><?php _e("Join EgyptFOSS Today...","egyptfoss"); ?>
				<br>
				<small><?php _e('Contribute and meet people with similar interests today.','egyptfoss') ?> </small>
				</h1>
				<div class="join-btn">
					<a href="./register/" class="btn btn-cat-join rfloat"><?php _e("Join Now","egyptfoss"); ?></a>
				</div>
			</div>
		</div>
	</div>
</section>
<?php } ?>
<?php if(sizeof($news) > 0) { ?>
<section class="news-list">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h2>
					<a href="./news/"><?php _e("Latest News","egyptfoss"); ?></a>
				</h2>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">

				<ul class="news-list-loop">
                                    <?php foreach($news as $new) { ?>
					<li class="item clearfix">
                                            <?php if($new->is_featured == 1) { ?>
                                           <!-- <svg width="25" height="30" class="featured-icon" viewBox="0 0 26 30.75" fill="currentColor">
                                              <defs></defs>
                                              <path id="featured-icon" class="cls-1" d="M817.647,266.793h-21.29A2.378,2.378,0,0,0,794,269.068v26.186a2.381,2.381,0,0,0,2.357,2.276,2.491,2.491,0,0,0,1.686-.671L807,288.245l8.959,8.614a2.467,2.467,0,0,0,1.686.65A2.316,2.316,0,0,0,820,295.254V269.068A2.378,2.378,0,0,0,817.647,266.793Z" transform="translate(-794 -266.781)"/>
                                              <path id="_" data-name="" class="cls-2" d="M813,275.055a0.439,0.439,0,0,0-.437-0.36l-3.922-.57-1.758-3.555a0.389,0.389,0,0,0-.766,0l-1.758,3.555-3.921.57a0.433,0.433,0,0,0-.438.36,0.614,0.614,0,0,0,.2.375l2.844,2.765-0.672,3.907a1.017,1.017,0,0,0-.015.156,0.341,0.341,0,0,0,.328.39,0.664,0.664,0,0,0,.312-0.093l3.508-1.844,3.508,1.844a0.641,0.641,0,0,0,.312.093,0.336,0.336,0,0,0,.321-0.39,1.013,1.013,0,0,0-.008-0.156l-0.672-3.907,2.836-2.765A0.587,0.587,0,0,0,813,275.055Z" transform="translate(-794 -266.781)"/>
                                            </svg> -->
                                            <?php } ?>
					<div class="thumb">

					<a href="<?php echo get_permalink($new->ID) ;?>"><?php echo get_the_post_thumbnail( $new->ID, 'news-thumbnail'); ?></a>

					</div>

					<h4><a href="<?php echo get_permalink($new->ID) ;?>"><?php echo $new->post_title; ?></a></h4>
					<small class="news-date"><i class="fa fa-clock-o"></i> <?php echo mysql2date('d F Y', $new->post_date); ?></small>
					<p>
						<?php echo wp_strip_all_tags($new->description); ?>
					</p>
					</li>
                                    <?php } ?>
				</ul>

			</div>
		</div>
	</div>
</section>
<?php } ?>
<section class="reading-section">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2><a href="success-stories"><?php _e( 'Success Stories', 'egyptfoss' ); ?></a></h2>
                <div class="articles-block">
                  <?php if( count( $success_stories ) ): ?>
                    <?php $story = $success_stories[0]; array_shift( $success_stories ); ?>
                    <div class="featured-story">
                       <div class="thumb">
                          <?php
                            if ( has_post_thumbnail( $story->ID ) ) {
                              echo get_the_post_thumbnail( $story->ID, 'news-thumbnail' );
                            }
                            else {
                              ?><img src="<?php echo get_template_directory_uri(); ?>/img/empty_article_image.svg" alt="<?php echo $story->post_title ; ?>"><?php
                            }
                          ?>
                       </div>
                       <div class="intro">
                          <h4>
                            <a href="<?php echo get_post_permalink( $story->ID ); ?>">
                              <?php echo wp_trim_words( $story->post_title, 7, '...' ); ?>
                            </a>
                          </h4>
                          <p><?php echo wp_trim_words( $story->post_content, 12, '...' ); ?></p>
                       </div>
                    </div>
                    <?php if( count( $success_stories ) > 1 ): ?>
                      <div class="others-list">
                        <?php foreach( $success_stories as $index => $story ): ?>
                          <div class="post-item">
                            <div class="thumb">
                                <?php
                                  if ( has_post_thumbnail( $story->ID ) ) {
                                    echo get_the_post_thumbnail( $story->ID, 'news-thumbnail' );
                                  }
                                  else {
                                    ?><img src="<?php echo get_template_directory_uri(); ?>/img/empty_article_image.svg" alt="<?php echo $story->post_title ; ?>"><?php
                                  }
                                ?>
                            </div>
                            <div class="title">
                                <h5>
                                  <a href="<?php echo get_post_permalink( $story->ID ); ?>">
                                    <?php echo wp_trim_words( $story->post_title, 8, '...' ); ?>
                                  </a>
                                </h5>
                                <span class="short-line">
                                    <?php echo wp_trim_words( $story->post_content, 9, '...' ); ?>
                                </span>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  <?php else: ?>
                    <center><h3><?php _e( 'No success stories are found', 'egyptfoss' ); ?></h3></center>
                  <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
              <h2><a href="expert-thoughts/"><?php _e( 'Expert Thoughts', 'egyptfoss' ); ?></a></h2>
                <div class="articles-block experts">
                  <?php if( count( $expert_thoughts ) ): ?>
                    <?php $thought = $expert_thoughts[0]; array_shift( $expert_thoughts ); ?>
                    <div class="featured-story">
                       <div class="thumb avatar">
                           <?php echo get_avatar( $thought->post_author ); ?>
                       </div>
                       <div class="intro">
                          <h4>
                            <a href="<?php echo get_post_permalink($thought->ID); ?>">
                              <?php echo wp_trim_words( $thought->post_title, 7, '...' ); ?>
                            </a>
                          </h4>
                          <div class="post-meta">
                              <a href="<?php echo home_url()."/members/". bp_core_get_username( $thought->post_author ) .'/about/' ?>">
                                <?php echo bp_core_get_user_displayname( $thought->post_author ); ?>
                              </a>
                              <span class="post_date"><i class="fa fa-clock-o"></i> <?php echo mysql2date( 'd F Y', $thought->post_date ); ?> </span>
                          </div>
                          <p><?php echo wp_trim_words( $thought->post_content, 12, '...' ); ?></p>
                       </div>
                    </div>
                    <?php if( count( $expert_thoughts ) > 1 ): ?>
                      <div class="others-list">
                        <?php foreach( $expert_thoughts as $index => $thought ): ?>
                          <div class="post-item">
                             <div class="thumb avatar">
                                 <?php echo get_avatar( $thought->post_author ); ?>
                             </div>
                             <div class="title">
                                <h5>
                                  <a href="<?php echo get_post_permalink( $thought->ID ); ?>">
                                    <?php echo wp_trim_words( $thought->post_title, 8, '...' ); ?>
                                  </a>
                                </h5>
                                <div class="post-meta">
                                  <a href="<?php echo home_url()."/members/". bp_core_get_username( $thought->post_author ) .'/about/' ?>">
                                    <?php echo bp_core_get_user_displayname( $thought->post_author ); ?>
                                  </a>
                                  <span class="post_date"><i class="fa fa-clock-o"></i> <?php echo mysql2date( 'd F Y', $thought->post_date ); ?> </span>
                                </div>
                             </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  <?php else: ?>
                    <center><h3><?php _e( 'No expert thoughts are found', 'egyptfoss' ); ?></h3></center>
                  <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="platform-channels">
    <div class="container">
           <div class="row">
           <div class="col-md-12 text-center">
               <h2><a href=""><?php _e("Platform Sections","egyptfoss"); ?></a></h2>
           </div>
       </div>
        <div class="row">
            <div class="col-md-12">
                <ul class="platform-sections clearfix">
                    <li class="small-card">
                        <div class="inner">
                           <div class="icon"><img src="<?php echo get_template_directory_uri(); ?>/img/platform-icons/news-icon.png" alt="News"></div>
                            <div class="card-text">
                                <h4><a href="news/"><?php _e("News","egyptfoss"); ?></a></h4>
                                <p class="card_hint"><?php _e("Latest local and international FOSS news.","egyptfoss"); ?></p>
                            </div>
                        </div>
                    </li>
                    <li class="small-card">
                        <div class="inner">
                           <div class="icon"><img src="<?php echo get_template_directory_uri(); ?>/img/platform-icons/tribe_events-icon.png" alt="Events"></div>
                            <div class="card-text">
                                <h4><a href="events/"><?php _e("Events","egyptfoss"); ?></a></h4>
                                <p class="card_hint"><?php _e("Upcoming local and international FOSS events and happenings.","egyptfoss"); ?></p>
                            </div>
                        </div>
                    </li>
                     <li class="small-card">
                        <div class="inner">
                           <div class="icon"><img src="<?php echo get_template_directory_uri(); ?>/img/platform-icons/location.png" alt="Map"></div>
                            <div class="card-text">
                                <h4><a href="maps/"><?php _e("Map","egyptfoss"); ?></a></h4>
                                <p class="card_hint"><?php _e("An interactive map of FOSS users, entities, and events.","egyptfoss"); ?></p>
                            </div>
                        </div>
                    </li>
                     <li class="small-card">
                        <div class="inner">
                           <div class="icon"><img src="<?php echo get_template_directory_uri(); ?>/img/platform-icons/product-icon.png" alt="Products"></div>
                            <div class="card-text">
                                <h4><a href="products/"><?php _e("Products","egyptfoss"); ?></a></h4>
                                <p class="card_hint"><?php _e("Useful FOSS products in different categories.","egyptfoss"); ?></p>
                            </div>
                        </div>
                    </li>
                     <li class="small-card">
                        <div class="inner">
                           <div class="icon"><img src="<?php echo get_template_directory_uri(); ?>/img/platform-icons/open_dataset-icon.png" alt="Open Data"></div>
                            <div class="card-text">
                                <h4><a href="open-datasets/"><?php _e("Data","egyptfoss"); ?></a></h4>
                                <p class="card_hint"><?php _e("FOSS open datasets in different categories.","egyptfoss"); ?></p>
                            </div>
                        </div>
                    </li>
                     <li class="small-card">
                        <div class="inner">
                           <div class="icon"><img src="<?php echo get_template_directory_uri(); ?>/img/platform-icons/collaboration-center-icon.png" alt="collaboration center"></div>
                            <div class="card-text">
                                <h4><a href="collaboration-center/"><?php _e("Collaboration Center","egyptfoss"); ?></a></h4>
                                <p class="card_hint"><?php _e("Create, share and publish documents together.","egyptfoss"); ?></p>
                            </div>
                        </div>
                    </li>
                     <li class="small-card">
                        <div class="inner">
                           <div class="icon"><img src="<?php echo get_template_directory_uri(); ?>/img/platform-icons/service-icon.png" alt="Services"></div>
                            <div class="card-text">
                                <h4><a href="marketplace/"><?php _e("Marketplace","egyptfoss"); ?></a></h4>
                                <p class="card_hint"><?php _e("Offer your services, find the right providers, and share your reviews.","egyptfoss"); ?></p>
                            </div>
                        </div>
                    </li>
                      <li class="small-card">
                        <div class="inner">
                           <div class="icon"><img src="<?php echo get_template_directory_uri(); ?>/img/platform-icons/request_center-icon.png" alt="Request Center"></div>
                            <div class="card-text">
                                <h4><a href="request-center/"><?php _e("Request Center","egyptfoss"); ?></a></h4>
                                <p class="card_hint"><?php _e("Submit your requests and receive responses from the community.","egyptfoss"); ?></p>
                            </div>
                        </div>
                    </li>
                    <li class="small-card">
                        <div class="inner">
                           <div class="icon"><img src="<?php echo get_template_directory_uri(); ?>/img/platform-icons/pedia-icon.png" alt="FOSSpedia"></div>
                            <div class="card-text">
                                <h4><a href="wiki/"><?php _e("FOSSPedia","egyptfoss"); ?></a></h4>
                                <p class="card_hint"><?php _e("A FOSS encyclopedia.","egyptfoss"); ?></p>
                            </div>
                        </div>
                    </li>
                    <li class="small-card">
                        <div class="inner">
                           <div class="icon"><img src="<?php echo get_template_directory_uri(); ?>/img/platform-icons/awareness.png" alt="Awareness Center"></div>
                            <div class="card-text">
                                <h4><a href="awareness-center/"><?php _e("Awareness Center","egyptfoss"); ?></a></h4>
                                <p class="card_hint"><?php _e("Measure your awareness of FOSS topics.","egyptfoss"); ?></p>
                            </div>
                        </div>
                    </li>
                     <li class="small-card">
                        <div class="inner">
                           <div class="icon"><img src="<?php echo get_template_directory_uri(); ?>/img/platform-icons/expert_thought-icon.png" alt="Experts Thoughts"></div>
                            <div class="card-text">
                                <h4><a href="expert-thoughts/"><?php _e("Expert Thoughts","egyptfoss"); ?></a></h4>
                                <p class="card_hint"><?php _e("Blog posts by FOSS experts and activists.","egyptfoss"); ?></p>
                            </div>
                        </div>
                    </li>
                     </li>
                     <li class="small-card">
                        <div class="inner">
                           <div class="icon"><img src="<?php echo get_template_directory_uri(); ?>/img/platform-icons/success_story-icon.png" alt="Sucess Stories"></div>
                            <div class="card-text">
                                <h4><a href="success-stories/"><?php _e("Success Stories","egyptfoss"); ?></a></h4>
                                <p class="card_hint"><?php _e("Lessons learned from migration to FOSS.","egyptfoss"); ?></p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section class="newsletter-widget">
    <div class="container">
           <div class="row">
           <div class="col-md-12 text-center">
               <h2><a href=""><?php _e("Newsletter", "egyptfoss"); ?></a></h2>
           </div>
       </div>
        <div class="row">
            <div class="col-md-9 col-md-offset-3">
                <script type="text/javascript">
                  //<![CDATA[
                  if (typeof newsletter_check !== "function") {
                  window.newsletter_check = function (f) {
                      var re = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-]{1,})+\.)+([a-zA-Z0-9]{2,})+$/;
                      if (!re.test(f.elements["ne"].value)) {
                          alert("<?php _e( 'The email is not correct' ); ?>");
                          return false;
                      }
                      for (var i=1; i<20; i++) {
                      if (f.elements["np" + i] && f.elements["np" + i].required && f.elements["np" + i].value == "") {
                          alert("");
                          return false;
                      }
                      }
                      if (f.elements["ny"] && !f.elements["ny"].checked) {
                          alert("<?php _e( 'You must accept the privacy statement' ); ?>");
                          return false;
                      }
                      return true;
                  }
                  }
                  //]]>
                  </script>

                <form id="newsletter-widget" class="form-group" method="post" action="<?php echo home_url(); ?>/?na=s" onsubmit="return newsletter_check(this)">
                    <!-- email -->
                    <label for="newsletter-email"><?php _e( 'Email', 'egyptfoss' ); ?></label><br>
                    <input id="newsletter-email" class="tnp-email form-control col-sm-8" type="email" name="ne" size="30" placeholder="<?php _e( 'Type Your Email', 'egyptfoss' ); ?>" required>
                    <span class="col-sm-4">
                          <input class="tnp-submit btn btn-primary" type="submit" value="<?php _e( 'Subscribe', 'egyptfoss' ); ?>"/>
                    </span>
                </form>
            </div>
        </div>
    </div>
</section>
<?php if ( !is_user_logged_in() && ( !isset($_COOKIE['skip-splash-screen']) || $_COOKIE['skip-splash-screen'] != '1' ) ) { ?>
  <!-- Modal -->
  <div class="modal fade" id="spash-screen" tabindex="-1" role="dialog" aria-labelledby="spash-screen" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <button type="button" class="close dismiss-spash-screen" data-dismiss="modal" aria-label="Close" cname="skip-splash-screen">
            <span aria-hidden="true">&times;</span>
          </button>
          <a href="<?php echo get_site_url(); ?>" class="site-logo">
            <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="<?php bloginfo('name'); ?>" class="foss-logo" />
          </a>
          <h1><?php _ex("Welcome to EgyptFOSS", "splash-screen", "egyptfoss"); ?></h1>
          <p>
            <?php printf( __('Here!, You\'ll find a content specially developed for Serving Free and Open Source Software community in Egypt. Please, <a href="%s" target="_blank">Register</a> or <a href="%s" target="_blank">Log in</a> to join our community and to enjoy using our services or to do the following:', 'egyptfoss'), get_register_page_current_lang(), get_current_lang_page_by_template('template-login.php') );?>
          </p>
          <ul>
            <li><?php printf( __('Adding <a href="%s" target="_blank">News</a>, <a href="%s" target="_blank">Events</a>, <a href="%s" target="_blank">Open Data Sets</a>, and <a href="%s" target="_blank">FOSS Products</a> or Write Articles on our <a href="%s" target="_blank">FOSSpedia</a>', 'egyptfoss'), 'news/', 'events/', 'open-datasets/', 'products/', 'wiki/' ); ?>.</li>
            <li><?php printf( __('Creating your Freelancer or Company profile and pin your location to <a href="%s" target="_blank">FOSS Map</a>', 'egyptfoss'), get_current_lang_page_by_template('page-add-user-location.php') ); ?>.</li>
            <li><?php printf( __('Growing your business by Adding your services to our <a href="%s" target="_blank">Services Market</a>', 'egyptfoss'), 'marketplace/' ); ?>.</li>
            <li><?php printf( __('Evaluate your FOSS Knowledge using our <a href="%s" target="_blank">Awareness Center</a>', 'egyptfoss'), 'awareness-center' ); ?>.</li>
            <li><?php printf( __('Ask for Help or For services to get done using our <a href="%s" target="_blank">Request Center</a>', 'egyptfoss'), 'request-center' ); ?>.</li>
          </ul>
          <p><?php _e( 'You\'ll be honoured by badges for you contributions on EgyptFOSS platform', 'egyptfoss' ); ?>.</p>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
<?php get_footer(); ?>
