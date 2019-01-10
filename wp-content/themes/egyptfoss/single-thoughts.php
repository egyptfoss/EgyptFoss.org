<?php
/**
 * Template Name: Single Thoughts.
 *
 * @package egyptfoss
 */
get_header(); ?>

<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-12">
	 				<h1>
					<?php echo the_title(); ?>
					</h1>
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->

<div class="container">
	<div class="row">
    <div  class="col-md-12 content-area">
<div class="single-column-content">
  <div class="share-widget clearfix">
          <?php if(get_post_status() == "publish"){ ?>
				<div class="share-profile rfloat">
					<a class="btn btn-light"><i class="fa fa-share"></i> <?php _e('Share','egyptfoss') ?>
						<div class="share-box">
							<?php echo do_shortcode('[Sassy_Social_Share]');?>
						</div>
					</a>
		</div>
      <?php } ?>
 	</div>
   <article class="thought-card full">
   <div class="col-md-2 expert-identity">
   <div class="expert-avatar">
       <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/avatar_90.jpg" class="avatar" alt="">
   </div>
   <h4>
   <a href="#">Ahmed Badran</a>
   </h4>
   <span class="user-type">Researcher</span>
   <br>
    <small class="post-date thought-timestamp">
    Posted on
    <br>
    <i class="fa fa-clock-o"></i> 26 Jul, 2016</small>
   </div>
   <div class="col-md-10">
                 <div class="news-img-canvas">
	<figure class="article-image" itemprop="image">
                  	<a href="https://placeholdit.imgix.net/~text?txtsize=33&txt=Placeholder&w=900&h=600" class="image-link article-intro-img">
                  		<span class="enlarge-img">
				<i class="fa fa-search-plus"></i>
			</span>
                 <img src="https://placeholdit.imgix.net/~text?txtsize=33&txt=Placeholder&w=400&h=300"  alt="">
	</a>


</figure>
</div>
    <p>Robin Smith is the co-founder CEO of ORIG3N, a regenerative medical company that has crowdsourced blood samples to create the world’s biggest and most diverse bio-repository. According to Smith, regenerative medicine is the next big thing in medical science — so disruptive, in fact, that he says it’s akin to the Internet in 1993. No wonder, then, that ORIG3N has already.</p>
    <p>
    <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/stats.jpg"  alt="" style="float:right;">
    Robin Smith is the co-founder CEO of ORIG3N, a regenerative medical company that has crowdsourced blood samples to create the world’s biggest and most diverse bio-repository. According to Smith, regenerative medicine is the next big thing in medical science — so disruptive, in fact, that he says it’s akin to the Internet in 1993. No wonder, then, that ORIG3N has already.</p>
    <br>
    <br>
    <p>Robin Smith is the co-founder CEO of ORIG3N, a regenerative medical company that has crowdsourced blood samples to create the world’s biggest and most diverse bio-repository. According to Smith, regenerative medicine is the next big thing in medical science — so disruptive, in fact, that he says it’s akin to the Internet in 1993. No wonder, then, that ORIG3N has already.</p>
   </div>
   </article>
   <section class="author-box">
       <div class="panel panel-default">
           <div class="panel-body">
   <div class="col-md-2 expert-identity author-identity">
   <div class="expert-avatar">
       <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/avatar_90.jpg" class="avatar" alt="">
   </div>
   <div class="social-presence">
       <a href="#">
           <i class="fa fa-facebook-square"></i>
       </a>
        <a href="#">
           <i class="fa fa-twitter-square"></i>
       </a>
       <a href="#">
           <i class="fa fa-linkedin-square"></i>
       </a>
   </div>
   </div>
          <div class="col-md-10">
   <h3>
   <a href="#">Ahmed Badran</a>
   </h3>
   <span class="user-type">Researcher</span>
        <p>Robin Smith is the co-founder CEO of ORIG3N, a regenerative medical company that has crowdsourced blood samples to create the world’s biggest and most diverse bio-repository. According to Smith</p>
         <a href="#">View Profile</a>
          </div>
           </div>
       </div>
   </section>
   <section class="more-by-expert">
       <h3>More by this expert</h3>
       <ul>
           <li>
               <a href="#">Tumblr teases plan to introduce ads on all its blogs</a>
           </li>
           <li><a href="#">
               Microsoft Pix uses AI to help your iPhone take the best pictures
           </a></li>
       </ul>
   </section>
   <section class="thougt-comments">

       <?php	if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif; ?>

   </section>
</div>
    </div>
    </div><!-- #primary -->
	</div>

<?php get_footer();?>
