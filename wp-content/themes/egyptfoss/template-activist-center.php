<?php
/**
 * Template Name: Activist Center
 *
 * @package egyptfoss
 */

get_header(); 

$ranked_users = ef_activist_center_top_users();
?>

<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-12">
	 				<h1>
					<?php _e("Activist Center","egyptfoss"); ?>
					</h1>
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->

<div class="container">
	<div class="row">
    <div  class="col-md-12 content-area">
<div class="single-column-content">
 <div class="row">
     <div class="col-md-12 text-center">
         <h2><?php _e("Top 10 Members","egyptfoss"); ?></h2>
         <p><?php _e("Top members based on the points earned for their contributions in EgyptFOSS","egyptfoss") ?></p> 
     </div>
 </div>
  <div class="activists-list">
      <?php if (!empty($ranked_users)):
      $rank_index = 0;
      $lang = get_locale();
      foreach($ranked_users as $user) { 
        //load user info
        $registration_data = get_registration_data($user->ID);
        if($lang == 'ar') {
          global $ar_sub_types;
          $sub_types = $ar_sub_types;
        } else {
          global $en_sub_types;
          $sub_types = $en_sub_types;
        }
        
        $type = "";
        if (!empty($registration_data['type'])) {
          if ($registration_data['type'] == "Entity") {
            $type = '<span class="account-type-icon entity"><i class="fa fa-building"></i></span> ';
          }else {
            $type = '<span class="account-type-icon person"><i class="fa fa-user"></i></span> ';
          }
        }
        
        $sub_type = "";
        if (!empty($registration_data['sub_type'])) {
          $sub_type = ((isset($registration_data['sub_type']) && !empty($registration_data['sub_type'])) ? $sub_types[$registration_data['sub_type']] : '');
        }
        
        //add rank index 
        $rank_index += 1;
        
        //load user badges
        //$badges = EFBBadgesUsers::getBadgesByUser($user->ID);
        $badges = EFBBadgesUsers::getHighRankBadgesByUser($user->ID);
        ?>
      <div class="activist-row">
          <div class="rank"><?php echo $rank_index; ?></div>
          <div class="user-avatar">
              <?php echo get_avatar( $user->ID, 32 ); ?> 
          </div>
          <div class="user-data">
              <h3><a href="<?php echo home_url()."/members/".bp_core_get_username($user->ID).'/about/' ?>"><?php echo bp_core_get_user_displayname($user->ID); ?></a></h3>
              <small class="type"><?php echo $type.' '.$sub_type; ?></small>
          </div>
          <div class="badges-cell">
           <div class="badges-wrap">
                        <?php foreach($badges as $badge)
            {
              $badge->img = EFBBadges::getBadgeSmallUrl($badge->img);
            ?>
              <span data-toggle="tooltip" title="<?php echo ($lang == "ar")?$badge->title_ar:$badge->title; ?>" class="small-badge">
                  <img src="<?php echo $badge->img ?>" alt="<?php echo ($lang == "ar")?$badge->title_ar:$badge->title; ?>" title="<?php echo ($lang == "ar")?$badge->title_ar:$badge->title; ?>">
              </span>
            <?php
            } ?>
           </div>
          </div>
          <div class="user-points"><?php echo $user->points.' '._n("Pt","Pts",$user->points,"egyptfoss"); ?></div>
      </div>
      <?php } ?>
      <?php else: ?>
          <div class="empty-state-msg">
             <?php 
              $img_name = "empty_ac";
              if(pll_current_language() == "en") {
                $img_name = "empty_ac_en";
              } ?>
             
             <img src="<?php echo get_template_directory_uri(); ?>/img/<?php echo $img_name; ?>.png" alt="No users">
             <h4><?php _e('Start contributing to join the list of Top Members','egyptfoss') ?></h4>
          </div>
      <?php endif; ?>
  </div>
</div>
    </div>
    </div><!-- #primary -->
	</div>

<?php get_footer();