<?php
$theme = '-1';
$license = '-1';
$type = '-1';
$formats = '-1';
$publisher = '-1';
if(get_query_var('ef_listing_dataset_theme_id'))
{
    $theme = get_query_var('ef_listing_dataset_theme_id');
}

if(get_query_var('ef_listing_dataset_type_id'))
{
    $type = get_query_var('ef_listing_dataset_type_id');
}

if(get_query_var('ef_listing_dataset_license_id'))
{
    $license = get_query_var('ef_listing_dataset_license_id');
}

if(get_query_var('ef_listing_dataset_format'))
{
    $formats = get_query_var('ef_listing_dataset_format');
}

if(get_query_var('ef_listing_dataset_publisher'))
{
    $publisher = get_query_var('ef_listing_dataset_publisher');
}

$lang = pll_current_language();
$args = array(
    "post_status" => "publish",
    "post_type" => "open_dataset",
    "current_lang" => $lang,
    "foriegn_lang" => ($lang == "ar")?"en":"ar",
    "offset" => 0,
    "theme_id" => $theme,
    "type_id" => $type,
    "license_id" => $license,
    "format" => $formats,
    "publisher" => $publisher
);
$list_datasets = get_datasets($args);
$homeUrl = home_url();
if (strpos($homeUrl, $lang) === false) {
  $homeUrl = $homeUrl."/$lang";
}
if ($list_datasets){
foreach ($list_datasets as $dataset) {
  $dataset_id = $dataset->ID ;
  $meta = get_post_custom($dataset_id);
    ?>
	<div class="dataset-card clearfix">

 	<div class="card-title">
 		<h3>
 		<a href="<?php echo $homeUrl."/open-datasets/".$dataset->post_name; ?>"><?php echo $dataset->post_title; ?></a>
 		</h3>
 	</div>
 	<div class="card-summary">
          <div class="data-set-meta">
             		<span class="meta-type">
			 	<span class="type-label"><?php _e("Theme","egyptfoss");?>:</span>
                                <?php 
                                    $theme_id = get_post_meta($dataset_id, 'theme', true);
                                    $theme_name = get_term_name_by_lang($theme_id, $lang);
                                ?>
                                <a href="" onclick="return false;" data-slug="<?php echo $theme_name; ?>" class="trigger_click" data-id="<?php echo $theme_id; ?>">
 			 <?php
                            echo  $theme_name;
                ?>
                                </a>
 		</span>
 			<span class="meta-type">
 				<span class="type-label"><?php _e("Type","egyptfoss");?>:</span>
                                <?php 
                                    $type_id = get_post_meta($dataset_id, 'dataset_type', true);
                                    $type_name = get_term_name_by_lang($type_id, $lang);
                                ?>   
                                <a href="" onclick="return false;" data-slug="<?php echo $type_name; ?>" class="topFiltersInList" data-id="<?php echo $type_id; ?>">
 				<?php echo $type_name; ?>
                                </a>
 			</span>
 			<span class="meta-type">
 				<span class="type-label"><?php _e("License","egyptfoss");?>:</span>
                                <?php 
                                    $license_id = get_post_meta($dataset_id, 'datasets_license', true);
                                    $license_name = get_term_name_by_lang($license_id, $lang);
                                ?>
                                <a href="" onclick="return false;" data-slug="<?php echo $license_name; ?>" class="topFiltersInList" data-id="<?php echo $license_id; ?>">
                                <?php echo $license_name; ?>
                                </a>
 			</span>  
 			<span class="meta-type">
 				<span class="type-label"><?php _e("Publisher","egyptfoss");?>:</span>
          <?php 
              $data_publisher = trim(get_post_meta($dataset_id,'publisher',true));
          ?>
          <a href="" onclick="return false;" data-slug="<?php echo rawurlencode($data_publisher); ?>" class="topFiltersInList" data-id="<?php echo rawurlencode($data_publisher); ?>">
            <?php echo $data_publisher; ?>
          </a>
 			</span>                
          </div>

            <?php
                $description = $meta['description'];
                if ($description) {
                  foreach ( $description as $key => $value ) {
                    ?><p><?php echo wp_trim_words( $value, 30, ' ...' );//$value; ?></p><?php
                  }
                  unset($value);
                }
                else {
                  ?><p></p><?php
                }
            ?>
            <?php 
                $open_dataset_formats = get_post_meta($dataset_id,'dataset_formats',true);
                $attachments = explode('|||',$open_dataset_formats);
                global $extension_mime_types_conv;
                if ( $attachments ) {
                    $attachments = array_unique($attachments);
                    foreach ( $attachments as $attachment ) {
                      $attachment = $extension_mime_types_conv[$attachment];
                      $attachment_ext = strtoupper($attachment);
                      echo '<span class="file-type" data-format="'.$attachment.'">' . $attachment_ext . '</span> ';
                    }
                }
            ?>
 	</div>
 	</div>
    <?php
  }
} else{?>
       <div class="empty-state-msg">
                   <i class="fa fa-3x fa-database"></i>
                   <br>
                   <p>
                             <?php _e("There are no Open Datasets yet, ", "egyptfoss"); ?>
                <?php if ( !is_user_logged_in() ) {?>
                  <a href="<?php echo home_url( pll_current_language().'/login/?redirected=addopendataset&redirect_to='.get_current_lang_page_by_template("template-add-open-dataset.php") ); ?>"><?php echo __("Suggest", "egyptfoss") .' '._n("Open Dataset","Open Datasets",0, "egyptfoss"); ?></a>
                <?php } else if (current_user_can('add_new_ef_posts')) { ?>
                  <a href="<?php echo get_current_lang_page_by_template('template-add-open-dataset.php') ?> "> <?php echo __("Suggest", "egyptfoss") .' '._n("Open Dataset","Open Datasets",0, "egyptfoss"); ?></a>
                <?php } else { ?>
                  <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
                  <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><?php echo __("Suggest", "egyptfoss") .' '._n("Open Dataset","Open Datasets",0, "egyptfoss"); ?></a>
                <?php } ?>
                   </p>
               </div>
<?php } ?>
