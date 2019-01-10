<?php
/**
 * Template Name: List Datasets.
 *
 * @package egyptfoss
 */

$getParams = $_GET;
get_header(); ?>
	<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-12">
            <h1 class="entry-title"> <?php echo _n("Open Dataset","Open Datasets",2,"egyptfoss"); ?> </h1>
            <?php 
                // this variable is used in related documents templates
                $section_slug = 'open-dataset';
                include( locate_template( 'template-parts/content-related_documents.php' ) );
            ?>
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->
<?php   

    // Load publishers
    $publishers = ef_load_open_dataset_publishers();

    $current_theme = -1;
    //check query string
    if($_GET['theme'] != null){
        if($_GET['theme'] != "all")
        {
            //get id of selected category
            $current_theme = ef_return_taxonomy_id_by_name(html_entity_decode( $_GET['theme'] ),'theme');
            set_query_var('ef_listing_dataset_theme_id', $current_theme);
        }
    }
    
    $current_type = -1;
    if($_GET['type'] != null){
        if($_GET['type'] != "all")
        {
            //get id of selected category
            $current_type = ef_return_taxonomy_id_by_name($_GET['type'],'dataset_type');
            set_query_var('ef_listing_dataset_type_id', $current_type);
        }
    }
    
    $current_license = -1;
    if($_GET['license'] != null){
        if($_GET['license'] != "all")
        {
            //get id of selected category
            $current_license = ef_return_taxonomy_id_by_name($_GET['license'],'datasets_license');
            set_query_var('ef_listing_dataset_license_id', $current_license);
        }
    }
    
    $current_format = -1;
    if($_GET['format'] != null){
        if($_GET['format'] != "all")
        {
            //get id of selected category
            if(in_array(strtolower($_GET['format']), $extensions))
            {
                $current_format = $_GET['format'];
                set_query_var('ef_listing_dataset_format', $_GET['format']);
            }
        }
    }
    
    $current_publisher = -1;
    if($_GET['publisher'] != null){
        if($_GET['publisher'] != "all")
        {
          $current_publisher = $_GET['publisher'];
          set_query_var('ef_listing_dataset_publisher', $current_publisher);
        }
    }
    
    $lang = pll_current_language();
    $args = array(
        "post_status" => "publish",
        "post_type" => "open_dataset",
        "current_lang" => $lang,
        "foriegn_lang" => ($lang == "ar")?"en":"ar",
        "offset" => 0,
        "theme_id" => $current_theme,
        "type_id" => $current_type,
        "license_id" => $current_license,
        "format" => $current_format,
        "publisher" => $current_publisher
    );
    
    $count = ef_count_open_dataset($args);    
?>
<div class="container">
	<div class="row">
  <div class="col-md-3">
  	     <div class="side-menu">
          <ul class="categories-list industry-list">              
           <li <?php echo ($current_theme == -1)?'class="active"':''; ?>><a href="" onclick="return false;" data-slug="all" class="trigger_click" data-id="-1"><?php _e("All","egyptfoss"); ?></a></li>
           <?php $terms_data = get_terms('theme', array('hide_empty' => 0)); 
            if(pll_current_language() == "ar")
            {
              $sorted_ind = array();
              foreach ($terms_data as $key => $row)
              {

                  $sorted_ind[$key] = $row->name_ar;
              }
              array_multisort($sorted_ind, SORT_ASC, $terms_data);
            }
           foreach ($terms_data as $term_data) {?>
                    <li <?php echo ($current_theme == $term_data->term_id)?'class="active"':''; ?>><a href="" data-slug="<?php echo rawurlencode($term_data->name); ?>" class="trigger_click" data-id="<?php echo $term_data->term_id; ?>"><?php echo $term_data->name; ?></a></li>
            <?php } ?>
          </ul>
        </div>
    </div>
    <div id="primary" class="content-area col-md-9">
      <div class="row">
        <div class="filter-btns">
          <div class="col-md-12">
            <?php if ( !is_user_logged_in() ) { ?>
              <a href="<?php echo home_url( pll_current_language().'/login/?redirected=addopendataset&redirect_to='.get_current_lang_page_by_template("template-add-open-dataset.php") ); ?>" class="btn btn-primary rfloat"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") .' '._n("Open Dataset","Open Datasets",0, "egyptfoss"); ?></a>
            <?php } else if (current_user_can('add_new_ef_posts')) { ?>
              <a href="<?php echo get_current_lang_page_by_template("template-add-open-dataset.php"); ?>" class="btn btn-primary rfloat"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") .' '._n("Open Dataset","Open Datasets",0, "egyptfoss"); ?></a>
            <?php } else { ?>
              <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
              <a href="javascript:void(0)" class="btn btn-primary disabled rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") .' '._n("Open Dataset","Open Datasets",0, "egyptfoss"); ?></a>
            <?php } ?>
          </div>
        </div>
        <div class="ef-results-meta" <?php echo !$count?'style="display:none;"':''; ?>>
            <?php
              printf( 
                  '%s <span class="ef-results-count">%s</span> '.
                  '%s <span class="ef-total-count">%s</span> %s '.
                  '<span class="ef-category" %s>'.
                  '%s <span class="ef-category-name">"%s"</span>'.
                  '</span>',
                  __( 'Showing', 'egyptfoss' ),
                  (constant("ef_open_dataset_per_page") > $count )?$count:constant("ef_open_dataset_per_page"),
                  __('of', 'egyptfoss'),
                  $count,
                  __('results', 'egyptfoss'),
                  ($current_theme == -1)?'style="display:none;"':'',
                  __( 'From', 'egyptfoss' ),
                  ($current_theme != -1)?html_entity_decode($_GET['theme']):$current_theme
              );
            ?>
        </div>
      </div>
    <div class="row filter-bar">
    <div class="col-md-12">
    	  <section class="filter-nav" id="filter-products">
                    <div class="form-group row">
                    <div class="col-md-2 type-filter">
                        <select id="type" class="form-control technologies custom-select2 topFilters"style="width:100%;" data-placeholder=<?php _e("Type","egyptfoss"); ?>>
                            <option hidden="hidden"></option>
                             <optgroup>
                                <?php $terms_data = get_terms('dataset_type', array('hide_empty' => 0)); 
                                    foreach ($terms_data as $term_data) {?>
                                        <option <?php echo ($current_type == $term_data->term_id)?'selected="selected"':''; ?> data-slug="<?php echo $term_data->slug  ?>" value="<?php echo $term_data->term_id ?>"><?php echo $term_data->name; ?></option>
                                <?php } ?>
                             </optgroup>

                        </select>
                    </div>
                    <div class="col-md-2 type-filter">
                      <select id="license" class="form-control technologies custom-select2 topFilters"style="width:100%;" data-placeholder=<?php _e("License","egyptfoss"); ?>>
                          <option hidden="hidden"></option>
                          <optgroup>   
                              <?php $terms_data = get_terms('datasets_license', array('hide_empty' => 1)); 
                                foreach ($terms_data as $term_data) {
                                  if( $term_data->slug == 'other' ) {
                                    $other_term = $term_data->name;
                                    $other_term_id = $term_data->term_id;
                                    if( pll_current_language() == 'ar' && $term_data->name_ar ) {
                                      $other_term = $term_data->name_ar;
                                    }
                                    continue;
                                  }
                                  ?>
                                    <option <?php echo ($current_license == $term_data->term_id)?'selected="selected"':''; ?> data-slug="<?php echo $term_data->slug  ?>" value="<?php echo $term_data->term_id ?>"><?php echo $term_data->name; ?></option>
                              <?php } ?>
                              <?php if( isset( $other_term ) ):?>
                                <option value="<?php echo $other_term_id; ?>" <?php selected($current_license, $other_term_id); ?> data-slug="other"><?php echo $other_term; ?></option>
                              <?php endif; ?>
                          </optgroup>
                      </select>
                    </div>
                    <div class="col-md-2 type-filter">
                      <select id="format" class="form-control technologies custom-select2 topFilters"style="width:100%;" data-placeholder=<?php _e("Format","egyptfoss"); ?>>
                        <option hidden="hidden"></option>
                        <optgroup>
                            <?php
                                 foreach ($extensions as $extension) {?>
                            <option <?php echo (strtolower($current_format) == $extension)?'selected="selected"':''; ?> data-slug="<?php echo $extension  ?>" value="<?php echo $extension ?>"><?php echo strtoupper($extension); ?></option>
                             <?php } ?>
                        </optgroup>
                      </select>
                    </div>
                    <div class="col-md-2 type-filter">
                      <select id="publisher" class="form-control technologies custom-select2 topFilters"style="width:100%;" data-placeholder=<?php _e("Publisher","egyptfoss"); ?>>
                        <option hidden="hidden"></option>
                        <optgroup>   
                            <?php 
                              foreach ($publishers as $publisher) {?>
                                  <option <?php echo ($current_publisher == $publisher)?'selected="selected"':''; ?> data-slug="<?php echo rawurlencode(trim($publisher));  ?>" value="<?php echo rawurlencode(trim($publisher)); ?>"><?php echo $publisher; ?></option>
                          <?php } ?>
                        </optgroup>                          
                      </select>
                    </div>                        

                <div class="col-md-2 rfloat">
                            <button class="btn btn-link reset-filters rfloat"><i class="fa fa-remove"></i> <?php _e('Reset', 'egyptfoss') ?> </button>
                        </div>
                    </div>
                </section>
    </div>
     </div>
	  	<div class="row">
        <div class="clear"></div>
        <?php set_query_var('ef_listing_datasets_offset', $_POST['offset']); ?>
            <div class="loading-overlay hidden">
                <div class="spinner">
                    <div class="double-bounce1"></div>
                    <div class="double-bounce2"></div>
                </div>
            </div>   
            <div class="col-md-12" id="load_datasets_by_ajax_container">
            <?php get_template_part('template-parts/content', 'listing_datasets'); ?>
          </div><?php
        ?>
        <div class="pagination-row clearfix view-more">
              <a href="javascript:void(0);" onclick="return false;" class="btn btn-load-more" id="load_more_listing_datasets" data-offset="<?php echo constant('ef_open_dataset_per_page'); ?>" data-count=<?php echo $count ; ?>>
              <?php _e("Load more...", "egyptfoss"); ?>
            </a>
            <i class="fa fa-circle-o-notch fa-spin hidden ef-product-list-spinner"></i>
        </div>
	  	</div>
	</div><!-- #primary -->

	</div>
</div>

<?php get_footer();?>
