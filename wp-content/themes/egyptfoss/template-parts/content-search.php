<?php 

  global $post, $ar_sub_types, $en_sub_types; 
  
  // prepare post types
  $meta_post_types    = array( 'news', 'product', 'open_dataset', 'request_center' );
  $content_post_types = array( 'tribe_events', 'success_story', 'service', 'expert_thought' );
  $not_post_types     = array( 'collaboration-center', 'pedia' );
  $user_post_types    = array( 'Entity', 'Individual'  );
  
  // prepare post types with titles
  $post_types = array(
    'news'                  =>  __('News', 'egyptfoss'),
    'tribe_events'          =>  __('Events', 'egyptfoss'),
    'product'               =>  __('Products', 'egyptfoss'),
    'open_dataset'          =>  __('Open Datasets', 'egyptfoss'),
    'collaboration-center'  =>  __('Collaboration Center', 'egyptfoss'),
    'service'               =>  __('Services', 'egyptfoss'),
    'request_center'        =>  __('Request Center', 'egyptfoss'),
    'pedia'                 =>  __('FOSSpedia', 'egyptfoss'),
    'success_story'         =>  __('Success Stories', 'egyptfoss'),
    'expert_thought'        =>  __('Expert Thoughts', 'egyptfoss'),
    'Entity'                =>  __('Entities', 'egyptfoss'),
    'Individual'            =>  __('Individuals', 'egyptfoss'),
  );
  
  $post_type = $post->post_type;
  
  if ( in_array( $post_type, $meta_post_types ) ) {
      $desc = wp_trim_words(htmlspecialchars_decode( get_post_meta( $post->ID, 'description', TRUE ) ), 30, ' ...' );
  }
  else if( in_array( $post_type, $content_post_types ) || in_array( $post_type, $not_post_types ) ) {
      $desc = wp_trim_words(htmlspecialchars_decode( $post->post_content ), 30, ' ...' );
  }
  else if( in_array( $post_type, $user_post_types ) ) { 
      $registration_data = get_registration_data( $post->ID );
      if( get_locale() == 'ar' ) {
        $sub_types = $ar_sub_types;
      } else {
        $sub_types = $en_sub_types;
      }
      if (!empty($registration_data['type'])) {
        if ($registration_data['type'] == "Entity") {
          $desc = '<span class="account-type-icon entity"><i class="fa fa-building"></i></span> ';
        }else {
          $desc = '<span class="account-type-icon person"><i class="fa fa-user"></i></span> ';
        }
      }
      if (!empty($registration_data['sub_type'])) {
        $desc .= '<span>'.((isset($registration_data['sub_type']) && !empty($registration_data['sub_type'])) ? $sub_types[$registration_data['sub_type']] : '').'</span>';
      }
  }
  
if( $post_type == 'tribe_events' ) {
  $item_class = 'event';
  $day = tribe_get_start_date( $post->ID, true, 'd' );
  $month = tribe_get_start_date( $post->ID, true, 'M' );
  $thumb = '<div class="day-square"><span class="month">'.$month.' </span><span class="day">'.$day.'</span> </div>';
}
else {
  if( has_post_thumbnail( $post->ID ) ) {
    $thumb = get_the_post_thumbnail( $post->ID, 'post-thumbnail', array( "alt" => $post->post_title ) );
    $item_class = ( $post_type == 'product' )?'product':'article';
  }else{
    $item_class = 'no-thumb';
  }
}
?>
<div class="search-snippet-card <?php echo $item_class; ?>" style="margin-bottom: 0px;padding: 10px 20px 10px 20px;">
    <?php if( in_array( $post->post_type, $not_post_types ) ): ?>
    <h3 class="snippet-title" style="margin-bottom: 0px;font-size: 1.2em;"><a href="<?php echo $post->guid; ?>" rel="bookmark"><?php echo urldecode( $post->post_title ); ?></a></h3>
    <?php elseif ( in_array( $post->post_type, $user_post_types ) ): ?>
      <?php printf( '<h3 class="snippet-title" style="margin-bottom: 0px;font-size: 1.2em;">%s <a href="%s" rel="bookmark">%s</a></h3>', get_avatar( $post->ID, 32 ), esc_url( $_SERVER['HTTP_HOST']."/".pll_current_language()."/members/".bp_core_get_username($post->ID).'/about/' ), bp_core_get_user_displayname( $post->ID ) ); ?>
    <?php else: ?>
      <?php echo sprintf( '<h3 class="snippet-title" style="margin-bottom: 0px;font-size: 1.2em;"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ) . urldecode( get_the_title() ) . '</a></h3>'; ?>
    <?php endif; ?>
   
    <div class="snippet-body">
        <?php if( !empty( $thumb ) ): ?>
          <!--<div class="snippet-thumb"><?php echo $thumb; ?></div>-->
        <?php endif; ?>
        <div class="snippet-summary" style="padding: 0px;"><p style="margin-bottom: 6px;font-size: 14px;"><?php echo $desc; ?></p></div>
    </div>
    <?php if( !isset( $_GET['type'] ) || $_GET['type'] == 'all' ): ?>
      <div class="snippet-type">
          <?php if( $post_type == 'Individual' ):
            echo '<i class="fa fa-university"></i>';
          elseif( $post_type == 'Entity' ):
            echo '<i class="fa fa-users"></i>';
          else: ?>
            <img src="<?php echo get_template_directory_uri(); ?>/img/platform-icons/<?php echo $post_type; ?>-icon.png">
          <?php endif; ?>
          <span><?php _e( 'In', 'egyptfoss' ); ?></span>
          <?php echo '<a href="'.get_bloginfo('url').'/?s='.$_GET['s'].'&type='.$post_type.'">'.$post_types[ $post_type ].'</a>'; ?>
      </div>
    <?php endif; ?>
</div>