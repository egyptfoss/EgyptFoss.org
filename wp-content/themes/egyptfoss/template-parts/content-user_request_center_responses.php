<?php
$displayedUserId = (bp_displayed_user_id())?bp_displayed_user_id():$_POST['displayedUserID'];
$args = array("post_status" => '',
              "post_type"=>"request_center",
              "no_of_posts" => constant("ef_user_request_center_per_page"),
              "offset" => (get_query_var('user_request_center_responses_offset') ? get_query_var('user_request_center_responses_offset') : 0),
              "author" => $displayedUserId);

if (get_current_user_id() != $displayedUserId) {
  $args['post_status'] = "publish";
}
$results = get_user_request_center_responses($args);
$host = get_option("home");
foreach ($results as $post)
{
  setup_postdata($post);

?>
    <div class="profile-card">
      <div class="inner clearfix">
      <div class="product-info">
        <h4><a href="<?php echo $host.'/'.pll_current_language()."/request-center/".$post->post_name?>"> <?php  _e("$post->post_title", "egyptfoss"); ?> </a></h4>
        <div>
            <small><i class="fa fa-clock-o"></i> <?php  echo mysql2date('d F Y', $post->post_date) ; ?></small>
      </div>
      <?php if (get_post_status ($ID) == 'pending' ){?>
        <span class="pending-approval">
          <i class="fa fa-history"></i>
          <?php _e('Pending Approval','egyptfoss') ?>
        </span>
      <?php }else if(get_post_status ($ID) == 'archive' ) {?>
        <span class="archived-label">
          <i class="fa fa-archive"></i>
          <?php _e('Archived','egyptfoss') ?>
        </span>
      <?php } ?>        
      </div>
      </div>
    </div>
  <?php
 }
?>
