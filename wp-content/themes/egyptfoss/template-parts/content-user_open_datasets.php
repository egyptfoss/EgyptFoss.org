<?php
$displayedUserId = (bp_displayed_user_id())?bp_displayed_user_id():$_POST['displayedUserID'];
$args = array("post_status" => '',
              "post_type"=>"open_dataset",
              "no_of_posts" => constant("ef_user_open_dataset_per_page"),
              "offset" => (get_query_var('user_open_dataset_offset') ? get_query_var('user_open_dataset_offset') : 0),
              "author" => $displayedUserId);

if (get_current_user_id() != $displayedUserId) {
  $args['post_status'] = "publish";
}
$results = get_user_open_datasets($args);
foreach ($results as $post)
{
  setup_postdata($post);

?>
    <div class="profile-card">
      <div class="inner clearfix">
      <div class="product-info">
        <h4><a href="<?php echo home_url()."/open-datasets/".$post->post_name?>"> <?php  _e("$post->post_title", "egyptfoss"); ?> </a></h4>
        <div>
            <small><i class="fa fa-clock-o"></i> <?php  echo mysql2date('d F Y', $post->post_date) ; ?></small>
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
