<?php
foreach ($thoughts as $thought) {
  $post = $thought->ID;
  setup_postdata($post);
  ?>
  <article class="thought-card">
    <div class="col-md-2 expert-identity">
      <div class="expert-avatar">
        <?php echo get_avatar($thought->post_author ,96 ,'' ,'',array("class"=>"avatar")); ?>
      </div>
      <h4>
        <?php if (bp_core_get_username($thought->post_author) != '') { ?>
          <a href="<?php echo home_url() . "/members/" . bp_core_get_username($thought->post_author) . "/about/" ?>">
            <?php echo bp_core_get_user_displayname($thought->post_author); ?>
          </a>
        <?php
        } else {
          echo bp_core_get_user_displayname($thought->post_author);
        }
        ?>
      </h4>
      <?php
      $user_data = get_registration_data($thought->post_author);
      global $en_sub_types;
      global $ar_sub_types;
      $sub_types = (pll_current_language() == "en")?$en_sub_types:$ar_sub_types;
      if (!empty($user_data['sub_type'])) {
        ?>

        <span class="user-type"><?php echo ((isset($user_data['sub_type']) && !empty($user_data['sub_type'])) ? $sub_types[$user_data['sub_type']] : ''); ?></span>
  <?php } ?>
    </div>
    <div class="col-md-10">
      <header class="article-title">
        <h1><a href="<?php echo get_post_permalink($thought->ID); ?>"><?php echo $thought->post_title ?></a></h1>
        <small class="post-date"><i class="fa fa-clock-o"></i> <?php echo mysql2date('j F Y', $thought->post_date) ?></small>
      </header>
  <?php if (has_post_thumbnail($post)) { ?>
        <figure>
          <img src="<?php echo get_the_post_thumbnail_url($post,'xlarge-img'); ?>" class="post-img" alt="<?php echo $thought->post_title ?>">
        </figure>
  <?php } ?>
      <p><?php echo get_the_excerpt(); ?></p>
    </div>
  </article>
<?php } ?>  