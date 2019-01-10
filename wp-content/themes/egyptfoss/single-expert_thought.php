<?php
/**
 * Template Name: Single Thoughts.
 *
 * @package egyptfoss
 */
get_header();
global $post;
$user_data = get_registration_data($post->post_author);
global $en_sub_types;
global $ar_sub_types;
load_orm();
$thoughts = new ExpertThought();
$relatedThoughts = $thoughts->getThoughtsByExpert(array("exclude_ids"=>array($post->ID),"limit"=> 2,"post_author"=>$post->post_author));
$ef_expert_thought_messages = getMessageBySession("ef_expert_thought_messages");
$is_expert = get_user_meta( get_current_user_id(), 'is_expert',true);
?>

<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1 property="headline">
          <?php echo the_title(); ?>
        </h1>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->

<div class="container">
  <div class="row" vocab="http://schema.org/" typeof="Article">
    <div  class="col-md-12 content-area" property="articleBody">
      <?php
        if ( isset( $ef_expert_thought_messages[ 'success' ] ) ) {
              ?>
              <div class="alert alert-success">
                  <i class='fa fa-check'></i> <?php echo $ef_expert_thought_messages[ 'success' ]; ?><br/>
              </div><?php
            }
        ?>
      <div class="single-column-content">
        <div class="share-widget clearfix">
          <?php if (get_post_status() == "publish") { ?>
            <div class="share-profile rfloat">
              <a class="btn btn-light"><i class="fa fa-share"></i> <?php _e('Share', 'egyptfoss') ?>
                <div class="share-box">
                  <?php echo do_shortcode('[Sassy_Social_Share]'); ?>
                </div>
              </a>
            </div>
          <?php } ?>
        <?php if( get_current_user_id() == get_the_author_meta( 'ID' ) && $is_expert ): ?>
            <div class="share-profile rfloat">
              <a href="<?php echo get_current_lang_page_by_template("template-edit-expert-thought.php", false, null, array( get_the_ID() ) ); ?>" class="btn btn-light rfloat"><i class="fa fa-pencil"></i> <?php echo ucwords(__('Edit','egyptfoss')); ?></a>
            </div>
        <?php endif; ?>
        </div>
        <article class="thought-card full">
          <div class="col-md-2 expert-identity">
            <div class="expert-avatar">
              <!-- <img src="<?php echo get_avatar_url($post->post_author); ?>" class="avatar" alt="">-->
              <?php echo get_avatar( $post->post_author ); ?>
            </div>
            <h4>
              <?php if (bp_core_get_username($post->post_author) != '') { ?>
                <a href="<?php echo home_url() . "/members/" . bp_core_get_username($post->post_author) . "/about/" ?>" property="author">
                  <?php echo bp_core_get_user_displayname($post->post_author); ?>
                </a>
                <?php
              } else {
                echo bp_core_get_user_displayname($post->post_author);
              }
              ?>
            </h4>
            <?php
            $sub_types = (pll_current_language() == "en") ? $en_sub_types : $ar_sub_types;
            if (!empty($user_data['sub_type'])) {
              ?>
              <span class="user-type">
                <?php echo ((isset($user_data['sub_type']) && !empty($user_data['sub_type'])) ? $sub_types[$user_data['sub_type']] : ''); ?>
              </span>
              <br>
            <?php } ?>
            <small class="post-date thought-timestamp" property="datePublished">
              <?php _e("Posted on","egyptfoss"); ?>
              <br>
              <i class="fa fa-clock-o"></i>  <?php
              global $post;
              echo mysql2date('j F Y', $post->post_date)
              ?>
            </small>
          </div>
          <div class="col-md-10">
            <?php if (has_post_thumbnail($post)) { ?>
              <div class="news-img-canvas">
	<figure class="article-image" itemprop="image" property="image">
                  	<a href="<?php echo get_the_post_thumbnail_url($post); ?>" class="image-link article-intro-img">
                  		<span class="enlarge-img">
				<i class="fa fa-search-plus"></i>
			</span>
                  <img src="<?php echo get_the_post_thumbnail_url($post); ?>" class="post-img" alt="<?php the_title(); ?>">
	</a>


</figure>
</div>
            <?php } ?>
            <p property="description"><?php the_content(); ?></p>
            <?php
            $interest = get_field('interest', $post->ID, true);
            if ( ! empty( $interest ) ): ?>
                <div class="col-md-12 related-to">
                  <strong><?php _e('Related interests', 'egyptfoss'); ?></strong>
                  <?php foreach ( $interest as $keyword_id ):
                      $keyword = get_term( $keyword_id, 'interest' );
                      ?><span class="interest-badge">
                      	 <?php _e( $keyword->name, "egyptfoss" ); ?>
                      </span>
                  <?php endforeach; ?>
                </div>
            <?php endif; ?>
          </div>
        </article>
        <section class="author-box">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="col-md-2 expert-identity author-identity">
                <div class="expert-avatar">
                  <!--<img src="<?php echo get_avatar_url($post->post_author); ?>" class="avatar" alt="">-->
                    <?php echo get_avatar( $post->post_author ); ?>
                </div>
                <div class="social-presence">
                  <?php if(isset($user_data["facebook_url"]) && !empty($user_data["facebook_url"])) { ?>
                  <a href="<?php echo $user_data["facebook_url"] ?>">
                    <i class="fa fa-facebook-square"></i>
                  </a>
                  <?php } ?>
                  <?php if(isset($user_data["twitter_url"]) && !empty($user_data["twitter_url"])) { ?>
                  <a href="<?php echo $user_data["twitter_url"] ?>">
                    <i class="fa fa-twitter-square"></i>
                  </a>
                  <?php } ?>
                  <?php if(isset($user_data["linkedin_url"]) && !empty($user_data["linkedin_url"])) { ?>
                  <a href="<?php echo $user_data["linkedin_url"] ?>">
                    <i class="fa fa-linkedin-square"></i>
                  </a>
                  <?php } ?>
                  <?php if(isset($user_data["gplus_url"]) && !empty($user_data["gplus_url"])) { ?>
                  <a href="<?php echo $user_data["gplus_url"] ?>">
                    <i class="fa fa-google-plus-square"></i>
                  </a>
                  <?php } ?>
                </div>
              </div>
              <div class="col-md-10">
                <h3>
                    <?php if (bp_core_get_username($post->post_author) != '') { ?>
                    <a href="<?php echo home_url() . "/members/" . bp_core_get_username($post->post_author) . "/about/" ?>">
                    <?php echo bp_core_get_user_displayname($post->post_author); ?>
                    </a>
                    <?php
                  } else {
                    echo bp_core_get_user_displayname($post->post_author);
                  }
                  ?>
                </h3>
                <?php
                if (!empty($user_data['sub_type'])) {
                  ?>
                  <span class="user-type"><?php echo ((isset($user_data['sub_type']) && !empty($user_data['sub_type'])) ? $sub_types[$user_data['sub_type']] : ''); ?></span>
                <?php } ?> 
                <p> 
                <?php if(isset($user_data["functionality"]) && !empty($user_data["functionality"])){ ?>  
                
                <?php echo $user_data["functionality"]; ?>
                
                <?php } ?>
                </p>
                <a href="<?php echo home_url() . "/members/" . bp_core_get_username($post->post_author) . "/about/" ?>"><?php _e("View Profile","egyptfoss") ?></a>
              </div>
            </div>
          </div>
        </section>
        <?php 
        if(count($relatedThoughts) > 0){ ?>
        <section class="more-by-expert">
          <h3><?php echo ucwords(__("More thoughts by this expert","egyptfoss")) ?></h3>
          <ul>
            <?php foreach($relatedThoughts as $thought){ ?>
            <li>
              <a href="<?php echo get_post_permalink($thought->ID); ?>"><?php echo $thought->post_title ?></a>
            </li>
            <?php } ?>
          </ul>
        </section>
        <?php } ?>
        <section class="thougt-comments">
          <?php
          if (comments_open() || get_comments_number()) :
            comments_template();
          endif;
          ?>
        </section>
      </div>
    </div>
  </div><!-- #primary -->
</div>

<?php get_footer(); ?>
