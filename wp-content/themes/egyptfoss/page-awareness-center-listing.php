<?php
/**
 * Template Name: Awareness Listing.
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
<div class="row">
  <div class="col-md-12">
    <div class="surveys-list">
        <div class="survey-item clearfix">
            <div class="survey-title">
                <h2><a href="#">
                    Annual Survey of Entrepreneurs
                </a> 
                 <small class="taken-survey">
                   <i class="fa fa-check-circle"></i> 
                   Taken
                </small></h2>
                <span class="post-date">
                    <i class="fa fa-clock-o"></i> 17 Jul, 2016
                </span>
                <span class="interest-tag">Internet</span>
                <span class="interest-tag">Telecom</span>
            </div>
            <div class="survey-stats">
                <div class="q-count">
                    <span class="num">16</span>
                    <br>
                    <small>Questions</small>
                </div>
                <div class="p-count">
                     <span class="num">25</span>
                    <br>
                    <small>Participants</small>
                </div>
            </div>
        </div>
               <div class="survey-item clearfix">
            <div class="survey-title">
                <h2><a href="#">
                    2012 Economic Census
                </a></h2>
                <span class="post-date">
                    <i class="fa fa-clock-o"></i> 17 Jul, 2016
                </span>
                <span class="interest-tag">Internet</span>
                <span class="interest-tag">Telecom</span>
            </div>
            <div class="survey-stats">
                <div class="q-count">
                    <span class="num">16</span>
                    <br>
                    <small>Questions</small>
                </div>
                <div class="p-count">
                     <span class="num">25</span>
                    <br>
                    <small>Participants</small>
                </div>
            </div>
        </div>
               <div class="survey-item clearfix">
            <div class="survey-title">
                <h2><a href="#">
                    Advance Monthly Sales for Retail and Food Services (MARTS)
                </a></h2>
                <span class="post-date">
                    <i class="fa fa-clock-o"></i> 17 Jul, 2016
                </span>
                <span class="interest-tag">Internet</span>
                <span class="interest-tag">Telecom</span>
            </div>
            <div class="survey-stats">
                <div class="q-count">
                    <span class="num">16</span>
                    <br>
                    <small>Questions</small>
                </div>
                <div class="p-count">
                     <span class="num">25</span>
                    <br>
                    <small>Participants</small>
                </div>
            </div>
        </div>
               <div class="survey-item clearfix">
            <div class="survey-title">
                <h2><a href="#">
                    Annual Retail Trade Survey (ARTS)
                </a></h2>
                <span class="post-date">
                    <i class="fa fa-clock-o"></i> 17 Jul, 2016
                </span>
                <span class="interest-tag">Internet</span>
                <span class="interest-tag">Telecom</span>
            </div>
            <div class="survey-stats">
                <div class="q-count">
                    <span class="num">16</span>
                    <br>
                    <small>Questions</small>
                </div>
                <div class="p-count">
                     <span class="num">25</span>
                    <br>
                    <small>Participants</small>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
 <div class="row text-center">
          <div class="col-md-12">
              <button type="button" class="btn btn-primary loadmore-btn" id="load" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Please wait...">Load more...</button>
          </div>
       </div>
           <!-- Empty State - Remove class hidden to show -->
       <div class="row empty-state hidden">
           <div class="empty-state-msg">
               <i class="fa fa-question-circle fa-4x"></i>
               <br>
               <h3>There are no surveys in this category</h3>
           </div>
       </div>
       <!-- Empty State End -->
	</div><!-- #primary -->

	</div>
</div>

<?php get_footer();?>
