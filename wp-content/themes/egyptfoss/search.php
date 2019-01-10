<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package egyptfoss
 */
get_header();
?>

<section>
    <main id="main" class="site-main" role="main">
        <?php 
          global $semantic_posts;
        ?>
        <?php if (sizeof($semantic_posts) > 0 ) : ?>
            <header class="page-header">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h1 class="entry-title"><?php printf(esc_html__('Search Results for: %s', 'egyptfoss'), '<span>' . get_search_query() . '</span>'); ?></h1>
                        </div>
                    </div>
                </div>
            </header><!-- .entry-header -->
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                      <?php get_template_part('template-parts/content', 'searchfilter'); ?>
                    </div>
                    <div id="primary" class="content-area col-md-9">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?php get_search_form(); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="resaults-count">
                                    <?php _e('Showing', 'egyptfoss') ?>
                                    <?php $num = sizeof($semantic_posts);
                                    if (sizeof($semantic_posts) > 0) : ?> <span id='span_numb'> <?php echo $num; ?> </span> <?php
                                    endif; ?> <?php _e('of', 'egyptfoss') ?> <?php $search_count = 0;
                                   /* $search = new WP_Query("s=$s & showposts=-1");
                                    if ($search->have_posts()) : while ($search->have_posts()) : $search->the_post();
                                            $search_count++;
                                        endwhile;
                                    endif;
                                    echo $search_count;*/ echo get_query_var('ef_total_count'); ?> <?php _e('results', 'egyptfoss') ?>
                                </div>
                            </div>
                        </div>
                        <div id="load_search_by_ajax_container" class="snippet-list">
                          <?php  foreach($semantic_posts as $single_post):
                              global $post;
                              $post = $single_post;
                      
                              /**
                               * Run the loop for the search to output the results.
                               * If you want to overload this in a child theme then include a file
                               * called content-search.php and that will be used instead.
                               */
                              get_template_part('template-parts/content', 'search');
                            endforeach;
                          ?>
                        </div>
                        <div class="pagination-row clearfix view-more collapse">
                            <a href="javascript:void(0);" onclick="return false;" class="btn btn-load-more" id="load_more_listing_search" data-offset=<?php echo constant("ef_search_per_page"); ?> data-count=<?php echo get_query_var('ef_total_count'); ?>>
                            <?php _e("Load more...", "egyptfoss"); ?>
                          </a>
                        <i class="fa fa-circle-o-notch fa-spin hidden ef-product-list-spinner"></i>
                      </div>

                  <?php else : ?>
                          <?php get_template_part('template-parts/content', 'none'); ?>
                  <?php endif; ?>
                </div>
                <div class="col-md-3 side-snippet search-sidebar collapse">
                    <div class="panel panel-default">
                        <ul class="list-group">
                          <li class="list-group-item">
                            <div class="media">
                              <div class="media-left snippet">
                                <a href="#">
                                  <img id="sidebar-thmbnail" class="media-object" src="" alt="">
                                </a>
                              </div>
                              <div class="media-body">
                                <h3 id="title" class="media-heading"></h3>
                                <span class="wikipedia-link"><i class="fa fa-external-link"></i> <a id="sidebar-wikipedia" target="_blank" href="#"><?php _e("More info on Wikipedia","egyptfoss"); ?></a></span>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item">
                               <p id="sidebar-description" class="list-group-item-text desc"></p>
                          </li>
                          <li id="sidebar-related-items-head" class="list-group-item collapse" style="display: none;">
                              <h4 class="list-group-item-heading"><?php _e("Related Links","egyptfoss") ?></h4>
                                <ul id="sidebar-related-items" class="related-items list-unstyled"></ul>
                          </li>
                        </ul>
                    </div>                      
                </div>
            </div>
        </div>
    </main><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();
