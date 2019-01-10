<?php
$displayedUserId = (bp_displayed_user_id())?bp_displayed_user_id():$_POST['displayedUserID'];
$args = array("post_status" => '',
              "post_type"=>"expert_thought",
              "no_of_posts" => constant("ef_user_expert_thought_per_page"),
              "offset" => (get_query_var('user_expert_thought_offset') ? get_query_var('user_expert_thought_offset') : 0),
              "author" => $displayedUserId);

if (get_current_user_id() != $displayedUserId) {
  $args['post_status'] = "publish";
}
$results = get_user_expert_thoughts($args);
foreach ($results as $post)
{
  setup_postdata($post);

?>
    <div class="profile-card">
      <div class="inner clearfix">
      	     <div class="card-thumb">
        <?php
        $img_id = get_field('_thumbnail_id', get_the_ID(), $format_value = true);
        if ( ! empty( $img_id ) && @ get_class($img_id) != "WP_Error" ) {
          //$img_location = get_the_guid($img_id) ;
          echo get_the_post_thumbnail( get_the_ID(), 'news-thumbnail-small' );
        }
        else { // displays default image //
          ?><img src="<?php echo get_template_directory_uri(); ?>/img/no-product-icon.png" alt="<?php echo get_the_title(); ?>"><?php
        }
        ?>
      </div>
      <div class="product-info">
        <h4><a href="<?php echo home_url()."/expert-thoughts/".$post->post_name?>"> <?php  _e("$post->post_title", "egyptfoss"); ?> </a></h4>
        <div>
            <small><i class="fa fa-clock-o"></i> <?php  echo __("Created at", "egyptfoss"). " : ".  date('Y-m-d',strtotime($post->post_date)) ; ?></small>
      </div>
      <?php if (get_post_status ($ID) == 'pending' ):?>
<span class="pending-approval">
<i class="fa fa-history"></i>
<?php _e('Pending Approval','egyptfoss') ?>
</span>
<?php endif;?>
      </div>
      </div>
    </div>
  <?php
 }
?>
