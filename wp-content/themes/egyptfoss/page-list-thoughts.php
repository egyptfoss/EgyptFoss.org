<?php
/**
 * Template Name: Listing Thoughts.
 *
 * @package egyptfoss
 */
get_header(); ?>

<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-12">
	 				<h1>
					Experts Thoughts
					</h1>
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->

<div class="container">
	<div class="row">
    <div  class="col-md-12 content-area">
<div class="single-column-content">
   <article class="thought-card">
   <div class="col-md-2 expert-identity">
   <div class="expert-avatar">
       <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/avatar_90.jpg" class="avatar" alt="">
   </div>
   <h4>
   <a href="#">Ahmed Badran</a>
   </h4>
   <span class="user-type">Researcher</span>
   </div>
   <div class="col-md-10">
      <header class="article-title">
        <h1><a href="">Regenerative medicine today is like the internet in 1993</a></h1>
       <small class="post-date"><i class="fa fa-clock-o"></i> 26 Jul, 2016</small>
      </header>
    <p>Robin Smith is the co-founder CEO of ORIG3N, a regenerative medical company that has crowdsourced blood samples to create the world’s biggest and most diverse bio-repository. According to Smith, regenerative medicine is the next big thing in medical science — so disruptive, in fact, that he says it’s akin to the Internet in 1993. No wonder, then, that ORIG3N has already.</p>
   </div>
   </article>

      <article class="thought-card">
   <div class="col-md-2 expert-identity">
   <div class="expert-avatar">
       <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/avatar_90.jpg" class="avatar" alt="">
   </div>
   <h4>
   <a href="#">Ahmed Badran</a>
   </h4>
   <span class="user-type">Researcher</span>
   </div>
   <div class="col-md-10">
      <header class="article-title">
        <h1><a href="">Why car tech companies should look to Amazon for inspiration</a></h1>
       <small class="post-date"><i class="fa fa-clock-o"></i> 26 Jul, 2016</small>
      </header>
    <figure>
        <img src="<?php echo get_template_directory_uri(); ?>/demo-assets/dummy_image.jpg" class="post-img" alt="">
    </figure>
    <p>Computers are complicated, and one thing drivers don’t need in cars is complication. The safe operation of big, heavy, motor-powered vehicles that can travel as high speed relies heavily on an attentive driver paying attention to the control systems they use to make the car go, as well as to the world around them. Computers, whatever other advantages they have, require attention and focus to operate; figuring out how to make a computer do what you want, especially for any kind of advanced operationn, was always (and remains for many) a learning experience nearly on par with mastering a new language.</p>
   </div>
   </article>
</div>
    </div>
    </div><!-- #primary -->
	</div>

<?php get_footer();?>
