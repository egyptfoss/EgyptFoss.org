<?php
$displayedUserId = (bp_displayed_user_id())?bp_displayed_user_id():$_POST['displayedUserID'];
$args = array("post_status" => '',
              "post_type"=>"fosspedia",
              "no_of_posts" => constant("ef_user_fosspedia_per_page"),
              "offset" => (get_query_var('user_fosspedia_offset') ? get_query_var('user_fosspedia_offset') : 0),
              "author" => $displayedUserId);
         
if (get_current_user_id() != $displayedUserId) {
  $args['post_status'] = "publish";
}
$results = get_user_fosspedia($args);
foreach ($results as $post)
{
  setup_postdata($post);
 
?>
    <div class="profile-card">
      <div class="inner clearfix">
      <div class="product-info">
        <h4><a href="<?php echo esc_url( $_SERVER['HTTP_HOST'].$post->page_url );?>"> <?php  echo str_replace("_"," ",$post->post_title); ?> </a></h4>
        <div>
            <small><i class="fa fa-clock-o"></i> <?php  echo __("Created at", "egyptfoss"). " : ".  date('Y-m-d',strtotime($post->post_date)) ; ?></small>
      </div>
      </div>
      </div>
    </div>
  <?php
 }
?>
