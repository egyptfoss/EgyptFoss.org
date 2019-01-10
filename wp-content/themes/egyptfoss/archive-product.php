<?php
/**
 * Template Name: Products Listing.
 *
 * @package egyptfoss
 */
$getParams = $_GET;
get_header();
?>
<header class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="entry-title"><?php _e('Products', 'egyptfoss') ?></h1>
                <?php 
                    // this variable is used in related documents templates
                    $section_slug = 'product';
                    include( locate_template( 'template-parts/content-related_documents.php' ) );
                ?>
            </div>
        </div>
    </div>
</header>
<!-- .entry-header -->

<div class="container page-body">
    <section class="product-page-options"></section>
    <div class="row">
        <div class="col-md-3">
           	      <div class="side-menu">

                <ul class="categories-list industry-list">
                    <li class="<?php if (empty($_GET) || $_GET['industry'] == 'top-ten') { ?> active <?php } ?>">
                        <a href="" onclick="return false;"
                           <?php if (empty($_GET) || $_GET['industry'] == 'top-ten') { ?> selected ="selected" <?php } ?>
                           class="filter-class leftFilter" data-id="top-ten">
                               <img src="<?php echo get_template_directory_uri(); ?>/img/top-ten.png"  alt="<?php _e('Top 10 Products', 'egyptfoss'); ?>" class="list-icon"><?php _e('Top 10 Products', 'egyptfoss'); ?>
                        </a>
                    </li>
                    <li class="<?php if ((isset($getParams['industry']) && $getParams['industry'] == "featured")) { ?> active <?php } ?>">
                        <a href=""
                           <?php if ((isset($getParams['industry']) && $getParams['industry'] == "featured")) { ?> selected ="selected" <?php } ?>
                           class="filter-class leftFilter" data-id="featured" onclick="return false;" data-name="<?php _e("Editor's Choice", 'egyptfoss'); ?>">
                              <img src="<?php echo get_template_directory_uri(); ?>/img/featured-icon.png"  alt="<?php _e("Editor's Choice", 'egyptfoss'); ?>" class="list-icon">
                                <?php _e("Editor's Choice", 'egyptfoss'); ?>
                        </a>
                    </li>
                    <?php
                    $industries = get_terms('industry', array('hide_empty' => 0,'orderby'=>'name', 'parent' => 0));
                    if(pll_current_language() == "ar")
                    {
                      $sorted_ind = array();
                      foreach ($industries as $key => $row)
                      {

                          $sorted_ind[$key] = $row->name_ar;
                      }
                      array_multisort($sorted_ind, SORT_ASC, $industries);
                    }
                    //var_dump($row);
                    foreach ($industries as $industry) {
                      ?>
                      <li class="<?php if ($getParams['industry'] == $industry->slug) { ?> active <?php } ?>">
                          <a href="" data-slug="<?php echo $industry->slug ?>" class="filter-class leftFilter" data-id="<?php echo $industry->term_taxonomy_id ?>" data-name="<?php echo $industry->name; ?>" onclick="return false;"
                             <?php if ($getParams['industry'] == $industry->slug) { ?> selected="selected" <?php } ?>
                             ><?php echo $industry->name ?>

                          </a>

                      </li>
                    <?php } ?>
                </ul>
            </div>
        </div>       
        <div id="primary" class="content-area col-md-9 <?php if (!empty($_GET) && $_GET['industry'] != "top-ten") { ?> hidden <?php } ?> topTenListingProduct">

                  <div class="row filter-bar">
                    <div class="col-md-5 rfloat">
                        <div class="filter-btns">
                            <?php if (!is_user_logged_in()) { ?>
                              <a href="<?php echo home_url( pll_current_language().'/login/?redirected=addproduct&redirect_to='.get_current_lang_page_by_template("page-add-product.php") ); ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") . ' ' . _x("Product", "up", "egyptfoss"); ?></a>
                            <?php } else if (current_user_can('add_new_ef_posts')) { ?>
                              <a href="<?php echo get_current_lang_page_by_template("page-add-product.php"); ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") . ' ' . _x("Product", "up", "egyptfoss"); ?></a>
                            <?php } else { ?>
                              <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
                              <a href="javascript:void(0)" class="btn btn-primary disabled" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") . ' ' . _x("Product", "up", "egyptfoss"); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
             <div class="row">
              <div class="col-md-12">
                  <div class="loading-overlay loading-overlay_top_ten  hidden">
                    <div class="spinner">
                        <div class="double-bounce1"></div>
                        <div class="double-bounce2"></div>
                    </div>
                </div>
                <div id="load_more_top_ten_product_container">    
                    <?php
                    global $random_term_ids;
                    $random_term_ids = array();
                    get_template_part('template-parts/content','products_top_ten');
                    ?>
                </div>
              </div>
                <div class=" pagination-row clearfix">
                    <a href="javascript:void(0);" onclick="return false;" data-random="<?php echo join(',',$random_term_ids); ?>" class=" btn btn-load-more hidden" id="load_more_top_ten_product" data-offset="0" data-count="<?php echo (countTopTenTerms('industry')- count($random_term_ids)) ?>">
                        <?php _e("Load more...", "egyptfoss"); ?>
                    </a>
                    <i class="fa fa-circle-o-notch fa-spin hidden ef-top-ten-product-list-spinner"></i>
                </div>    
            </div>
        </div>
        
        <div id="primary" class="content-area col-md-9 <?php if (empty($_GET) || $_GET['industry'] == "top-ten") { ?> hidden <?php } ?> normalListingProduct">
            <main id="main" class="site-main" role="main">

                <div class="row filter-bar">
                    <div class="col-md-7">
                      <?php
                        $params = parse_url($_GET['q']);
                        parse_str($params['query'],$params);
                        $params = array_merge($params,$_GET);
                        $url_term_ids = array();
                        global $ef_product_filtered_taxs;
                        foreach ($ef_product_filtered_taxs as $tax)
                        {
                          $term = get_term_by('slug', $params[$tax], $tax);
                          if($term)
                          {
                            $url_term_ids = array_merge($url_term_ids,array($term->term_taxonomy_id));
                          }  
                        }
                        if(!empty($url_term_ids))
                        {
                          set_query_var('term_ids', $url_term_ids);
                        }else
                        {
                          set_query_var('term_ids', $_POST['term_ids']);
                        }
                        $browseBy = "";
                        if( (isset($_POST['browseProductsBy']) && $_POST['browseProductsBy'] == "featured") || $_GET['industry'] == "featured" )
                        {
                          $browseBy = "featured";
                        }  else {
                          if(!isset($_GET['industry']) && $_POST['browseProductsBy'] == "")
                          {
                            $browseBy = "featured";
                          }
                        }
                        set_query_var('ef_product_offest', $_POST['offest']);
                        $args = array(
                          "post_status" => "publish",
                          "post_type" => "product",
                          "current_lang" => pll_current_language(),
                          "foriegn_lang" => (pll_current_language() == "ar")?"en":"ar",
                          "offest" => (get_query_var("ef_product_offest") ? get_query_var("ef_product_offest") : 0),
                          "browseProductsBy" => $browseBy
                        );
                        $count = ef_listing_get_products_by_filter($args,$_POST['newUrl'], TRUE);
                      ?>
                    </div>
                    <div class="col-md-12">
                        <div class="filter-btns">
                            <?php if (!is_user_logged_in()) { ?>
                              <a href="<?php echo home_url( pll_current_language().'/login/?redirected=addproduct&redirect_to='.get_current_lang_page_by_template("page-add-product.php") ); ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") . ' ' . _x("Product", "up", "egyptfoss"); ?></a>
                            <?php } else if (current_user_can('add_new_ef_posts')) { ?>
                              <a href="<?php echo get_current_lang_page_by_template("page-add-product.php"); ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") . ' ' . _x("Product", "up", "egyptfoss"); ?></a>
                            <?php } else { ?>
                              <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
                              <a href="javascript:void(0)" class="btn btn-primary disabled" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") . ' ' . _x("Product", "up", "egyptfoss"); ?></a>
                            <?php } ?>
                        </div>
                        <div class="ef-results-meta" <?php echo !$count?'style="display:none;"':''; ?>>
                          <?php
                            $industry = -1;
                            if( isset( $_GET['industry'] ) ) {
                                if( $_GET['industry'] == 'featured' ) {
                                  $industry = __( "Editor's Choice", "egyptfoss" );
                                }
                                else {
                                  foreach ($industries as $key => $row) {
                                      if( $row->slug == $_GET['industry'] ) {
                                        if(pll_current_language() == "ar") {
                                          $industry = $row->name_ar;
                                        }
                                        else {
                                          $industry = $row->name;
                                        }
                                      }
                                  }
                                }
                            }

                            printf( 
                                '%s <span class="ef-results-count">%s</span> '.
                                '%s <span class="ef-total-count">%s</span> %s '.
                                '<span class="ef-category" %s>'.
                                '%s <span class="ef-category-name">"%s"</span>'.
                                '</span>',
                                __( 'Showing', 'egyptfoss' ),
                                (constant("ef_products_per_page") > $count )?$count:constant("ef_products_per_page"),
                                __('of', 'egyptfoss'),
                                $count,
                                __('results', 'egyptfoss'),
                                (empty($industry))?'style="display:none;"':'',
                                __( 'From', 'egyptfoss' ),
                                (!empty($industry))?$industry:''
                            );
                          ?>
                        </div>
                    </div>
                </div>

                <section class="filter-nav" id="filter-products">
                    <div class="form-group row">
                        <?php
                        $term_taxs = array("type", "license", "platform", "technology");
                        foreach ($term_taxs as $term_tax) {
                          ?>
                          <div class="col-md-2 <?php echo $term_tax."-filter" ?>">
                              <select class="custom-select2 form-control filter-class topFilters" data-taxonomy="<?php echo $term_tax ?>" hidden="hidden" style="width:100%;">

                                  <option value=""><?php echo __($term_tax, 'egyptfoss') ?></option>

                                  <?php
                                  $terms_data = get_terms($term_tax, array('hide_empty' => 0));
                                  foreach ($terms_data as $term_data) {
                                    ?>
                                    <option  data-slug="<?php echo $term_data->slug ?>" value="<?php echo $term_data->term_taxonomy_id ?>" 
                                             <?php if ($getParams[$term_tax] == $term_data->slug) { ?>selected="selected"<?php } ?>
                                             ><?php echo $term_data->name; ?></option>
                                           <?php } ?>

                              </select>  
                          </div>
                        <?php } ?>
                        <div class="col-md-2">
                            <button class="btn btn-link reset-filters rfloat"><i class="fa fa-remove"></i> <?php _e('Reset', 'egyptfoss') ?> </button>
                        </div>
                    </div>
                </section>
                <div class="loading-overlay hidden">
                    <div class="spinner">
                        <div class="double-bounce1"></div>
                        <div class="double-bounce2"></div>
                    </div>
                </div>
                <div class="row" id="load_product_by_ajax_container">
                    <?php
                    get_template_part('template-parts/content', 'product_filtered');
                    ?>
                </div>
                <div class=" pagination-row clearfix">
                    <a href="javascript:void(0);" onclick="return false;" class="hidden btn btn-load-more" id="load_more_product" data-offest="0" data-count="0">
                        <?php _e("Load more...", "egyptfoss"); ?>
                    </a>
                    <i class="fa fa-circle-o-notch fa-spin hidden ef-product-list-spinner"></i>
                </div>
                <div class="row hidden noProductsFound">
                    <?php _e("There are no products yet,", "egyptfoss"); ?>
                    <?php if (!is_user_logged_in()) { ?>
                      <a href="<?php echo home_url('/wp-login.php?redirected=addproduct'); ?>"><?php echo __("Suggest", "egyptfoss") . ' ' . _x("Product", "up", "egyptfoss"); ?></a>
                    <?php } else if (current_user_can('add_new_ef_posts')) { ?>
                      <a href="<?php echo home_url( pll_current_language().'/login/?redirected=addproduct&redirect_to='.get_current_lang_page_by_template("page-add-product.php") ); ?>"><?php echo __("Suggest", "egyptfoss") . ' ' . _x("Product", "up", "egyptfoss"); ?></a>
                    <?php } else { ?>
                      <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
                      <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><?php echo __("Suggest", "egyptfoss") . ' ' . _x("Product", "up", "egyptfoss"); ?></a>
                    <?php } ?>
                </div>
                <div class="row hidden noResultsFound">
                    <?php _e("There are no results,", "egyptfoss"); ?>
                    <?php if (!is_user_logged_in()) { ?>
                      <a href="<?php echo home_url( pll_current_language().'/login/?redirected=addproduct&redirect_to='.get_current_lang_page_by_template("page-add-product.php") ); ?>"><?php echo __("Suggest", "egyptfoss") . ' ' . _x("Product", "up", "egyptfoss"); ?></a>
                    <?php } else if (current_user_can('add_new_ef_posts')) { ?>
                      <a href="<?php echo get_current_lang_page_by_template("page-add-product.php"); ?>"><?php echo __("Suggest", "egyptfoss") . ' ' . _x("Product", "up", "egyptfoss"); ?></a>
                    <?php } else { ?>
                      <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
                      <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><?php _e("Add new product", "egyptfoss"); ?></a>
                    <?php } ?>
                </div>
                <div class="row hidden noFeaturedFound">
                    <?php
                      _e("Featured products aren't specified","egyptfoss");
                    ?>
                </div>
            </main>
        </div>
    </div>
</div>

<?php get_footer(); ?>
