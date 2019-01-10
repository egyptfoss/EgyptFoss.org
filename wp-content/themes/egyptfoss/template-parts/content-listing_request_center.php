<?php
$theme = '-1';
$target = '-1';
$type = '-1';
$months = ef_return_arabic_months();
if(get_query_var('ef_listing_request_theme_id')){
  $theme = get_query_var('ef_listing_request_theme_id');
}
if(get_query_var('ef_listing_request_type_id')){
  $type = get_query_var('ef_listing_request_type_id');
}
if(get_query_var('ef_listing_request_target_id')){
  $target = get_query_var('ef_listing_request_target_id');
}
$args = array(
    "post_status" => "publish",
    "post_type" => "request_center",
    "offset" => 0,
    "theme_id" => $theme,
    "type_id" => $type,
    "target_id" => $target
);
$lang = pll_current_language();

if( isset( $list_request_center ) && $list_request_center == 0 ) {
  $list_request_center = NULL;
}
else {
  $list_request_center = get_request_center($args);
}

if ($list_request_center){
  
  foreach ($list_request_center as $request_center) {
    $request_center_id = $request_center->ID ;
    $meta = get_post_custom($request_center_id);
    ?>
    <div class="wide-card request-card">
      <div class="wide-card-body clearfix">
        <div class="thumb-side">
          <div class="card-icon-thumbnail"> 
            <?php
              $term_id = $meta['request_center_type'];
              if ($term_id) {
                foreach ( $term_id as $key => $value ) {
                  $term = get_term( $value, 'request_center_type' );
                  $slug = $term->slug;
                }
                unset($value);
                if (!$slug){ $slug = 'default-request'; }
              } else {
                $slug = 'default-request';
              }?>
              <img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/<?php echo $slug; ?>_icon.svg" width="22">
          </div>
        </div>
        <div class="card-summary">
          <h3><a href="<?php echo get_post_permalink($request_center->ID); ?>"><?php echo get_the_title($request_center); ?></a></h3>
          <div class="request-info-meta">
              <span class="meta-type">
                      <span><i class="fa fa-crosshairs"></i> <?php _e("Target Relationship", "egyptfoss"); ?>: </span>
          <?php
          $target_bussiness_relationship = $meta['target_bussiness_relationship'];
          foreach ( $target_bussiness_relationship as $key => $value ) {
            $name = get_term_name_by_lang($value, $lang);
            if ($name) {
              ?><span><?php echo $name; ?></span><?php
            } else {
              ?><span> -- </span><?php
            }
          }
          unset($value);
          ?>
              </span>
         <span class="meta-type">
                   <span><i class="fa fa-tag"></i> <?php _e("Theme", "egyptfoss"); ?>: </span>
          <?php
          $theme = $meta['theme'];
          foreach ( $theme as $key => $value ) {
            $name = get_term_name_by_lang($value, $lang);
            if ($name) {
              ?><span><?php echo $name; ?></span><?php
            } else {
              ?><span> -- </span><?php
            }
          }
          ?>
        </span>
                 <span class="meta-type">
                 <span><i class="fa fa-clock-o"></i> <?php _e("Due Date", "egyptfoss"); ?></span>
          <?php
          $deadline = $meta['deadline'];
//          var_dump($deadline);
            foreach ( $deadline as $key => $value ) {
              if ($value) {
                if($lang == "en")
                  echo date("d M Y", strtotime($value)) ;
                else
                  echo str_replace(date("M", strtotime($value)), $months[date("M", strtotime($value))], date("d M Y", strtotime($value))) ;  
              } else {
                ?><span> -- </span><?php
              }
            }
            unset($value);
          ?>
        </span>
          </div>
          <?php
          $description = $meta['description'];
          if ($description) {
            foreach ( $description as $key => $value ) {
              ?><p><?php echo wp_trim_words( $value, 30, ' ...' ); //$value; ?></p><?php
            }
            unset($value);
          } else {
            ?><p> -- </p><?php
          }
          ?>
        </div>
      </div>
    </div>
    <?php
  }
} else{
  ?>
    <div class="empty-state-msg">
      <i class="fa fa-2x fa-folder-open"></i>
      <br>
      <?php _e("There are no Requests in Request Center", "egyptfoss"); ?>
    </div>
  <?php
}
?>
