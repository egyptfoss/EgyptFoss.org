<?php
$displayedUserId = (bp_displayed_user_id())?bp_displayed_user_id():$_POST['displayedUserID'];
$args = array("post_status" => '',
              "post_type"=>"tribe_events",
              "no_of_posts" => constant("ef_user_events_per_page"),
              "offset" => (get_query_var('user_event_offset') ? get_query_var('user_event_offset') : 0),
              "author" => $displayedUserId);

if (get_current_user_id() != $displayedUserId) {
  $args['post_status'] = "publish";
}
$results = get_user_events($args);
foreach ($results as $post)
{
  setup_postdata($post);

?>
<div class="profile-card user-event-card">
      <div class="inner clearfix">
      	<div class="card-thumb">
        <?php $eventStartDate = get_post_meta(get_the_ID(),'_EventStartDate',true);
        if($eventStartDate)
        {
          $date = new DateTime($eventStartDate);
									echo '<div class="day-square">';
									echo '<span class="month">';
          echo $date->format('M');
									echo '</span>';
									echo '<span class="day">';
          echo $date->format('d');
										echo '</span>';
									echo '</div>';
        }
        ?>
      </div>
      <div class="product-info">
          <h4><a href="<?php the_permalink() ?>"> <?php the_title() ?> </a></h4>
           <div class="event-date">
        <?php $eventVenue = tribe_get_venue(get_the_ID());
          echo $eventVenue;
        ?>
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
