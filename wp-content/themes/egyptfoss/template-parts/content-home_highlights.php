<?php 
$args = array(
    "current_lang" => pll_current_language()
);
$highlights = ef_listing_homepage($args);
$index = 0;
?>
<?php if(sizeof($highlights) > 0) { ?>
<section class="home-highlights">
    <?php 
    foreach($highlights as $highlight) {
        if($index == 0) { ?>
	<article class="featured--article item">
			<a href="./<?php echo $highlight->post_type;?>/" class="article-tag"><?php echo _nx("News","News",5,"indefinite","egyptfoss"); ?></a>
                        <?php echo get_the_post_thumbnail( $highlight->ID, 'news-featured', array( 'class' => 'highlight-img' )); ?>
				<div class="overlay">
                                    <div class="caption">
                                        <h2>
                                            <a href="<?php echo get_permalink($highlight->ID) ;?>">
                                                <?php echo $highlight->post_title ;?>
                                        </a>
                                        </h2>
                                        <small class="post-date">
                                            <i class="fa fa-clock-o"></i>
                                            <?php echo mysql2date('d F Y', $highlight->post_date); ?>
                                        </small>
                                    </div>
				</div>
	</article>
        <div class="grid-box">
        <?php $index++; } else {
          $postTypeTitle = __(str_replace("_", " ",$highlight->post_type),"egyptfoss");
          if($highlight->post_type == "success_story")
          {
            $highlight->post_type = 'success-stories';
          }else if ($highlight->post_type == "expert_thought")
          {
            $highlight->post_type = 'expert-thoughts';
          }else if ($highlight->post_type == "news")
          {
            $postTypeTitle = _nx("News","News",5,"indefinite","egyptfoss");
          }
        ?>
	<article class="box--25 item">
        <a href="./<?php echo $highlight->post_type;?>/" class="article-tag"><?php _e($postTypeTitle,"egyptfoss"); ?></a>
            <?php echo get_the_post_thumbnail( $highlight->ID, 'news-thumbnail', array( 'class' => 'highlight-img' )); ?>
        <div class="overlay">
                <div class="caption">
                    <h4>
                    <a href="<?php echo get_permalink($highlight->ID) ;?>">
                            <?php echo $highlight->post_title ;?>
                    </a>
                    </h4>
                    <small class="post-date">
                                            <i class="fa fa-clock-o"></i>
                                            <?php echo mysql2date('d F Y', $highlight->post_date); ?>
                                        </small>
                </div>
            </div>
	</article>
    <?php }} ?>
	</div>
</section>
<?php } ?>