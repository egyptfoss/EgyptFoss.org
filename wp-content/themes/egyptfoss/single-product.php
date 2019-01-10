<?php
/**
 * Template Name: Single Product
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package egyptfoss
 */

get_header();

$request_center_taxs = array("technology"=>array(),"interest"=>array());
$acf_industry = get_field('industry', get_the_ID());
$industry = (is_array($acf_industry)) ? $acf_industry[0] : $acf_industry;
$industry = get_term($industry, 'industry');
$acf_technologies = get_field('technology', get_the_ID(), $format_value = true);
$acf_interest = get_field('interest', get_the_ID(), $format_value = true);
$technology_spans = "";
$interest_spans = "";

foreach ($acf_technologies as $technology_id) {
  $technology = get_term($technology_id, 'technology');
  array_push($request_center_taxs["technology"], $technology->slug);
  $technology_spans .= "<span class='keyword-tag'>" . __("$technology->name", "egyptfoss") . " </span>";
}

foreach ($acf_interest as $keyword_id) {
  $keyword = get_term($keyword_id, 'interest');
  array_push($request_center_taxs["interest"], $keyword->slug);
  $interest_spans .= "<span class='keyword-tag'>" . __("$keyword->name", "egyptfoss") . "</span>";
}

//$request_center_taxs["industry"] = $industry->slug;

//list of contributors
global $wpdb;

$sql = "select user_id,sum(contributions_count) as contributions_count,updated_at from
(
(select {$wpdb->prefix}posts.post_author as user_id, 1 as contributions_count,{$wpdb->prefix}posts.post_date as updated_at
FROM {$wpdb->prefix}posts
where {$wpdb->prefix}posts.ID = ".  get_the_ID().")
union
(
select {$wpdb->prefix}posts_history.user_id,count(*) as contributions_count,max({$wpdb->prefix}posts_history.updated_at) as updated_at
FROM {$wpdb->prefix}posts_history
where {$wpdb->prefix}posts_history.post_id = ".  get_the_ID()."
group by {$wpdb->prefix}posts_history.user_id
)) as contri
group by user_id
order by contributions_count desc,updated_at desc";

$list_contributors = $wpdb->get_results($sql);
$recent_update = strtotime( $list_contributors[0]->updated_at );
foreach($list_contributors as $list_contributor){
  $curDate = strtotime($list_contributor->updated_at);
  if ($curDate > $recent_update) {
     $recent_update = $curDate;
  }
}
$recent_update = date( 'Y-m-d', $recent_update );
?>

<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12" vocab="http://schema.org/" typeof="Product">
       <div class="product-icon">
       <?php
        $img_id = get_field('_thumbnail_id', $product_id, $format_value = true);
        if ( ! empty( $img_id ) && @ get_class($img_id) != "WP_Error" ) {
          
          //$img_location = get_the_guid($img_id) ;
          echo get_the_post_thumbnail( $product_id, 'thumbnail',array('alt'=>get_the_title(), "property" => "image") );
          ?>
          	<!-- <img src="<?php echo $img_location ?>"  alt="<?php the_title();?>" /> -->

        <?php }
        else { // displays default image //
          ?><img src="<?php echo get_template_directory_uri(); ?>/img/no-product-icon.png" class="title-icon empty" alt="<?php the_title();?>" property="image"/>
        <?php } ?>
       </div>
       <h1 class="entry-title" property="name"><?php the_title(); ?></h1>
        <?php
        $product_id = get_the_ID();
        $product_meta = get_post_meta($product_id);
        ?>
        <br>
        <div class="product-short-desc" property="description">
          <?php
          $description = nl2br(get_post_meta($product_id, $key = 'description', true));
          ?>
        </div>
      </div>
    </div>
  </div>
</header>
<!-- .entry-header -->
<div class="container">
  <div class="row">

     <?php
    $ef_messages = getMessageBySession("ef_product_messages");
    if(isset($ef_messages['success'])) { ?>
      <div class="alert alert-success">
      <?php foreach ($ef_messages['success'] as $success) { ?>
        <i class="fa fa-check"></i> <?php echo $success; ?>
      <?php } ?>
      </div>
      <div class="clearfix"></div>
    <?php } ?>

        <?php $edit_success_edit = getMessageBySession("edit-product");
            if(!empty($edit_success_edit))
            {?>
            <div class="alert alert-success"><?php
            echo "<i class='fa fa-check'></i>" . $edit_success_edit['success'] . "<br/>";
            ?>
            </div>
            <?php } ?>
 </div>
 <div class="row">
     <div class="col-md-12">
             	      <div class="share-product clearfix">
          <div class="share-profile rfloat">
            <?php getRequestCenterAddLink($request_center_taxs) ?>
          </div>
        <?php if(get_post_status() == "publish"){ ?>
        <div class="share-profile rfloat">
          <a class="btn btn-light"><i class="fa fa-share"></i> <?php _e('Share','egyptfoss') ?>
            <div class="share-box">
              <?php echo do_shortcode('[Sassy_Social_Share]');?>
            </div>
          </a>
        </div>
        <?php } ?>
        <?php
        $canEdit = userCanEditProduct(get_post_status(),get_the_author_meta( 'ID' ));
        ?>
               
     <?php if ( !is_user_logged_in() && get_post_status() == "publish" ) { ?>
        <a href="<?php echo home_url( pll_current_language()."/login/?redirected=editproduct&redirect_to=".get_current_lang_page_by_template("template-edit-product.php")."?pid=".get_the_ID() ); ?>" class="btn btn-light rfloat"><i class="fa fa-pencil"></i> <?php echo ucwords(__('Edit','egyptfoss')); ?></a>
      <?php } else if (current_user_can('perform_direct_ef_actions')) { ?>
        <?php if ( $canEdit ) { ?>
          <a href="<?php echo get_current_lang_page_by_template("template-edit-product.php")."?pid=".get_the_ID() ?>" class="btn btn-light rfloat"><i class="fa fa-pencil"></i> <?php echo ucwords(__('Edit','egyptfoss')); ?></a>
        <?php } ?>
      <?php } else if(get_post_status() == "publish") { ?>
        <!-- Subscriber, Contributor users should be able to view (Edit Product) -->
        <a href="javascript:void(0)" class="btn btn-light rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><i class="fa fa-pencil"></i> <?php echo ucwords(__('Edit','egyptfoss')); ?></a>
      <?php } ?>

    </div>
     </div>
 </div>
  <div class="row">
    <div id="primary" class="content-area col-md-8">
      <section class="about-product">
        <h3><?php _e('Product Description', 'egyptfoss'); ?></h3>
        <?php if ( ! empty( $description ) ) {?>
        <p><?php echo html_entity_decode( $description ); ?></p>
        <?php }
        else{
          _e('Not Specified', 'egyptfoss');
        }
        ?>
      </section>
      <section class="product-info clearfix">
        <h3><?php _e('Product Info', 'egyptfoss'); ?></h3>
        <dl>
          <dt><?php _e('Name', 'egyptfoss'); ?></dt>
          <dd><?php echo get_the_title(); ?></dd>

          <dt><?php _e('Developer', 'egyptfoss'); ?></dt>
          <?php
            $developer = get_post_meta($product_id, $key = 'developer', true);
            if ( ! empty( $developer ) ) { ?>
              <dd><?php _e($developer, 'egyptfoss'); ?></dd>
            <?php }
            else {?>
              <dd><?php _e('Not Specified', 'egyptfoss'); ?></dd>
            <?php }?>

          <dt><?php _e('Functionality', 'egyptfoss'); ?></dt>
          <?php
            $functionality = get_post_meta($product_id, $key = 'functionality', true);
            if ( ! empty( $functionality ) ) { ?>
              <dd><?php echo nl2br($functionality); ?></dd>
            <?php }
            else {?>
              <dd><?php _e('Not Specified', 'egyptfoss'); ?></dd>
            <?php }?>

          <dt><?php _e('Category', 'egyptfoss'); ?></dt>
          <?php
            if ( ! empty( $acf_industry ) ) {
              $industry_name = ((pll_current_language() == 'ar' && $industry->name_ar != '' && $industry->name_ar != null) ? $industry->name_ar : $industry->name);
              
              if( $industry->parent ) {
                $parent = get_term( $industry->parent, 'industry' );
                $parent_name = ((pll_current_language() == 'ar' && $parent->name_ar != '' && $parent->name_ar != null) ? $parent->name_ar : $parent->name);
                $industry_name = $parent_name . " -> " . $industry_name;
              }
              ?>
              <dd><span class="keyword-tag"> <?php echo $industry_name; ?>  </span> </dd>
            <?php }
            else {?>
              <dd><?php _e('Not Specified', 'egyptfoss'); ?></dd>
            <?php }?>

          <dt><?php _e('Usage hints', 'egyptfoss'); ?></dt>
          <?php
          $usage_hints = nl2br(get_post_meta($product_id, $key = 'usage_hints', true));
          if ( ! empty( $usage_hints ) ) { ?>
            <dd><?php _e($usage_hints, 'egyptfoss') ; ?></dd>
          <?php }
          else {?>
            <dd><?php _e('Not Specified', 'egyptfoss'); ?></dd>
          <?php }?>

          <dt><?php _e('References', 'egyptfoss'); ?></dt>
          <?php
            $references = nl2br(get_post_meta($product_id, $key = 'references', true));
            if ( ! empty( $references ) ) { ?>
              <dd><?php _e($references, 'egyptfoss'); ?></dd>
            <?php }
            else {?>
              <dd><?php _e('Not Specified', 'egyptfoss'); ?></dd>
            <?php }?>

          <dt><?php _e('Source', 'egyptfoss'); ?></dt>
            <?php
            $link_to_source = get_post_meta($product_id, $key = 'link_to_source', true);
            if ( ! empty( $link_to_source ) ) { ?>
          <dd><a target="blank" href="<?php echo $link_to_source;?>"><?php _e($link_to_source, 'egyptfoss') ; ?></a></dd>
            <?php }
            else {?>
              <dd><?php _e('Not Specified', 'egyptfoss'); ?></dd>
            <?php }?>

          <dt><?php _e('Type', 'egyptfoss'); ?></dt>
          <?php
          $types = get_field('type',$product_id);
            if ( ! empty( $types ) ) {
              ?> <dd> <?php
                foreach( $types as $type ) {
                  $type = get_term( $type, 'type' ); ?>
                      <span class="keyword-tag">
                      <?php echo ((pll_current_language() == 'ar' && $type->name_ar != '' && $type->name_ar != null)?_e("$type->name_ar", "egyptfoss"):_e("$type->name", "egyptfoss")); ?>
                      </span> 
                  <?php 
                }
              ?> </dd> <?php
            }
            else {?>
              <dd><?php _e('Not Specified', 'egyptfoss'); ?></dd>
            <?php }?>

              <dt><?php echo (pluralize(__('Technology', 'egyptfoss'),true)); ?></dt>
          <?php

            if ( ! empty( $acf_technologies ) ) { ?>
                <dd>
                  <?php echo $technology_spans ?>
                </dd>
            <?php }
            else {?>
              <dd><?php _e('Not Specified', 'egyptfoss'); ?></dd>
            <?php }?>

          <dt><?php echo pluralize(__('Platform', 'egyptfoss'),true); ?></dt>
          <?php
            $platforms = get_field('platform', $product_id, $format_value = true);
            if ( ! empty( $platforms ) ) { ?>
                <dd>
                  <?php foreach ( $platforms as $platform_id) :
                      $platform = get_term( $platform_id, 'platform' );
                      ?><span class="keyword-tag"> <?php _e("$platform->name", "egyptfoss"); ?> </span>
                  <?php endforeach; ?>
                </dd>
            <?php }
            else {?>
              <dd><?php _e('Not Specified', 'egyptfoss'); ?></dd>
            <?php }?>

          <dt><?php echo pluralize(__('License', 'egyptfoss'),true); ?></dt>
          <?php
            $licenses = get_field('license', $product_id, $format_value = true);
            if ( ! empty( $licenses ) ) { ?>
                <dd>
                  <?php foreach ( $licenses as $license_id) :
                      $license = get_term( $license_id, 'license' ); ?>
                      <span class="keyword-tag">
                      <?php echo ((pll_current_language() == 'ar' && !empty( $license->name_ar))?$license->name_ar:$license->name); ?>
                      </span> 
                  <?php endforeach; ?>
                </dd>
            <?php }
            else {?>
              <dd><?php _e('Not Specified', 'egyptfoss'); ?></dd>
            <?php }?>

          <dt><?php _e('Interests', 'egyptfoss'); ?></dt>
            <?php

            if ( ! empty( $acf_interest ) ) { ?>
                <dd>
                  <?php echo $interest_spans ?>
                </dd>
            <?php }
            else {?>
              <dd><?php _e('Not Specified', 'egyptfoss'); ?></dd>
            <?php }?>

        </dl>
      </section>
        <div class="row">
  	<div class="col-md-12 comments-section">
  		<?php comments_template( '', true ); ?>
  	</div>
  </div>
    </div>
    <div class="col-md-4">
     <div class="product-images">
     	  <h3><?php _e( 'Screenshots', 'egyptfoss' ); ?></h3>
      <div id="product-images" class="owl-carousel owl-theme">
        <?php
          $screenshots = get_field('fg_perm_metadata', $product_id, $format_value = true);
          if ( ! empty( $screenshots ) ) {
            $screenshots_ids = explode(",", $screenshots);
            foreach ( $screenshots_ids as $screenshot_id) :
              $screenshot_location = get_the_guid($screenshot_id);
              ?><div class="item">
              <a href="<?php echo $screenshot_location ?>" class="image-link">
              	<img src="<?php echo $screenshot_location ?>"  alt="<?php the_title(); ?>">
              </a>
              </div>
            <?php endforeach;
          }
          else {
            ?><div class="item text-center">
            <img src="<?php echo get_template_directory_uri(); ?>/img/no-product-screen.png" alt="<?php _e('No screenshots for this product', 'egyptfoss'); ?>">
            <?php _e('No screenshots for this product', 'egyptfoss'); ?>
            </div> <?php }
        ?>
      </div>
     </div>
        <?php if(count($list_contributors) > 0) { ?>
					<h3><?php echo sprintf(_n("%s Contributor","%s Contributors",count($list_contributors),"egyptfoss"),count($list_contributors)); ?></h3>
      <small class="date"><i class="fa fa-clock-o"></i> <?php _e("Last Update","egyptfoss"); ?> <?php echo mysql2date('d F Y', $recent_update); ?></small>
      <div class="nano product-contributers">
     		<div class="nano-content">
     					<div class="panel panel-default">
     			 		<ul id="members-list" class="item-list contributors-list">
        <?php foreach($list_contributors as $contributor) { ?>
         <li class="clearfix"><a href="<?php echo home_url()."/members/".bp_core_get_username($contributor->user_id).'/about/' ?>">
                 <?php echo get_avatar( $contributor->user_id, 32, null, null, array( 'class' => array( 'avatar', 'img-circle' ) ) );
                echo bp_core_get_user_displayname($contributor->user_id);
                        ?>
             </a>
         <small class="date rfloat"><i class="fa fa-pencil-square"></i> <?php echo sprintf(_n("%s update","%s updates",$contributor->contributions_count,"egyptfoss"),$contributor->contributions_count); ?></small>
         </li>
        <?php } ?>
			</ul>
     		</div>
     		</div>
      </div>
        <?php } ?>
    </div>
<!-- #primary -->
  </div>
</div>

<?php
get_footer();
