<?php
/**
 * Single News
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package egyptfoss
 */

$lang = pll_current_language();
get_header(); ?>
	<header class="page-header">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h1 property="headline"><?php echo $post->post_title; ?></h1>
				</div>
				<div class="col-md-5 hidden-xs">
					<?php if (function_exists('template_breadcrumbs')) template_breadcrumbs(); ?>
				</div>
			</div>
		</div>
	</header>
	<!-- .entry-header -->
	<div class="container">
		<div class="row">
       <?php
    $ef_messages = getMessageBySession("ef_story_messages");
    if(isset($ef_messages['success'])) { ?>
      <div class="alert alert-success">
      <?php foreach ($ef_messages['success'] as $success) { ?>
        <i class="fa fa-check"></i> <?php echo $success; ?>
      <?php } ?>
      </div>
      <div class="clearfix"></div>
    <?php } ?>
    </div>

             <div class="row" vocab="http://schema.org/" typeof="Article">
 	<div class="col-md-8">

   <div class="single-news-meta">
   	   <div class="news-author"> <?php echo get_avatar( $post->post_author, 32 ); ?> <span><?php if(bp_core_get_username($post->post_author) != '') { ?> <a href="<?php echo home_url()."/members/".bp_core_get_username($post->post_author) ?>/about/"> <?php echo bp_core_get_user_displayname($post->post_author); ?></a><?php } else {  ?> <?php echo bp_core_get_user_displayname($post->post_author); }?> </span>
                </div>
    	<div class="post-date" itemprop="datePublished" property="datePublished">
              <i class="fa fa-clock-o"></i>
              <?php echo mysql2date('F j, Y ', $post->post_date); ?>
            </div>
                                <span class="story-category">
                        <?php 
                        $category = get_term(get_post_meta($post->ID, 'success_story_category', true) ); 
                        if($lang == "ar")
                        {
                            if($category->name_ar != '')
                                $category->name = $category->name_ar;
                        }?>
                                    <a href="/<?php echo pll_current_language();?>/success-stories/?category=<?php echo rawurlencode($category->name); ?>"><?php  
                            echo $category->name;
                        ?></a>
                    </span>

   </div>
 	</div>
 	<div class="col-md-4">
 					<div class="share-product">
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
 	</div>
 </div>
            
		<div class="row">
			<div id="primary" class="content-area col-md-8 story">
				<main id="main" class="site-main" role="main">

					<?php
if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<div class="news-img-canvas">
	<figure class="article-image" itemprop="image" property="image">
				<?php
                $img_id = get_field('_thumbnail_id', $news_id, $format_value = true);
                if ( ! empty( $img_id ) && @ get_class($img_id) != "WP_Error" ) {
                  $img_location = get_the_guid($img_id) ;
                  ?>
                  	<a href="<?php echo $img_location; ?>" class="image-link article-intro-img">
                  		<span class="enlarge-img">
				<i class="fa fa-search-plus"></i>
			</span>
                  <?php echo get_the_post_thumbnail( $news->ID, 'full',array('alt'=>get_the_title()) ); ?>
	</a>
                  <?php
                }


                else { // displays default image //
                  ?><img src="<?php echo get_template_directory_uri(); ?>/img/empty_article_image.svg" class="no-article-image" alt="<?php echo $news->post_title ; ?>"><?php
                }
              ?>


</figure>
</div>
<article class="news-article" property="description">
	<?php echo nl2br(do_shortcode($post->post_content)); ?>
            <?php
            $interest = get_field('interest', $post->ID, $format_value = true);
            if ( ! empty( $interest ) ) { ?>
                                    <div class="col-md-12 related-to">
        <strong><?php _e('Related interests', 'egyptfoss'); ?></strong>

                
                  <?php foreach ( $interest as $keyword_id) :
                      $keyword = get_term( $keyword_id, 'interest' );
                      ?><span class="interest-badge">
                      	 <?php _e("$keyword->name", "egyptfoss"); ?>
                      </span>
        
                  <?php endforeach; ?>
                </div>
            <?php } ?>
</article>
		<?php	if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif; ?>

		<?php endwhile; // End of the loop.
		?>

			</div>
  <?php $imgs = get_field( 'fg_perm_metadata', get_the_ID() ); ?>
  <div class="col-md-4 side-latest">
      <?php if ( ! empty( $imgs ) ): ?>
          <div class="product-images">
              <h3><?php _e( 'Gallery', 'egyptfoss' ); ?></h3>
              <div id="product-images" class="owl-carousel owl-theme">
                  <?php
                      $img_ids = explode( ",", $imgs );
                      foreach ( $img_ids as $img_id ) :
                        $img_url = get_the_guid( $img_id );
                        ?>
                        <div class="item">
                          <a href="<?php echo $img_url ?>" class="image-link">
                            <img src="<?php echo $img_url ?>"  alt="<?php the_title(); ?>">
                          </a>
                        </div>
                  <?php endforeach; ?>
              </div>
          </div>
      <?php endif; ?>
			<h3><?php _e('Recent Success Stories','egyptfoss') ?></h3>
			<ul class="other-news-list">
				<?php
				global $wp_query;
				$args = array(
    'numberposts' => 5,
    'offset' => 0,
    'post_type' => 'success_story',
    'post__not_in' => array( $post->ID )
);
$postslist = get_posts( $args );
foreach ($postslist as $post) :  setup_postdata($post); ?>

  <li class="news-list-item clearfix">

  <div class="lfloat">

  	<?php if ( has_post_thumbnail() ): ?>
  	<a href="<?php the_permalink(); ?>">
<?php the_post_thumbnail('news-thumbnail-small'); ?>
</a>
<?php else: ?>
<a href="<?php the_permalink(); ?>">
<img src="<?php echo get_template_directory_uri(); ?>/img/empty_article_small.svg" class="small-thumb" />
			</a>
<?php  endif ?>
  </div>
  <div class="lfloat news-title">
  <a href="<?php the_permalink(); ?>">
 <?php echo wp_trim_words( $post->post_title, 4, ' ...' ); ?>
  </a>
  <br>
      <div class="post-date">
              <i class="fa fa-clock-o"></i>
              <?php echo mysql2date('F j, Y ', $post->post_date); ?>
            </div>
			</div>
  </li>
<?php endforeach; ?>





				</ul>
			</div>
		</div>
	</div>


	<?php
get_footer();
