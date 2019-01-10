<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package egyptfoss
 */

?>
      <header class="page-header">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <!--<h1 class="entry-title"><?php esc_html_e( 'Nothing Found', 'egyptfoss' ); ?></h1> -->
                            <h1 class="entry-title"><?php printf(esc_html__('Search Results for: %s', 'egyptfoss'), '<span>' . get_search_query() . '</span>'); ?></h1>
                        </div>
                    </div>
                </div>
            </header><!-- .entry-header -->
<section class="no-results not-found">
	<div class="container">
	<div class="row">
	<div class="col-md-3">
		 <?php get_template_part('template-parts/content','searchfilter');?>
	</div>
	<div class="col-md-9 content-area">
	 <div class="row">
                            <div class="col-md-12">
                                <div class="form-group ft-padding-top">
                                    <?php get_search_form(); ?>
                                </div>
                            </div>
                        </div>
		<?php
                //make sure search query is empty or special characters only
                $is_search_error = false;
                if(isset($_GET["s"]))
                {
                    $query_string = trim($_GET["s"]);
                    if(empty($query_string))
                    {
                        $is_search_error = true;
                    }else
                    {
                        $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $query_string);
                        $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $query_string);

                        if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
                          $is_search_error = true;
                        }
                    }
                }
		if ( (is_home() && current_user_can( 'publish_posts' )) || ($is_search_error) ) : ?>
                <?php if($is_search_error) { ?>
                    <div class="empty-box">
                                    <i class="fa fa-warning fa-4x"></i>
                                   <!-- <p><?php _e("Enjoy exploring EgyptFOSS platform using different keywords","egyptfoss"); ?></p> -->
                                     <p><?php _e("Please type a keyword to search","egyptfoss"); ?></p>
                    </div>                    
                <?php }else { ?>
<div class="empty-box">
		<i class="fa fa-warning fa-4x"></i>
			<p><?php printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'egyptfoss' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>
</div>
                <?php } ?>
		<?php elseif ( is_search() ) : ?>
							<div class="empty-box">
						<i class="fa fa-search fa-5x"></i>
					 <h1 class="entry-title color-primary"><?php esc_html_e( 'Nothing Found', 'egyptfoss' ); ?></h1>

<div class="error-page-search">
	<h4><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'egyptfoss' ); ?></h4>
					</div>

				</div>

			<?php


		else : ?>
<div class="empty-box">
	<i class="fa fa-warning fa-4x"></i>
	<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'egyptfoss' ); ?></p></div>
			<?php


		endif; ?>
		</div>
		</div>
	</div><!-- .page-content -->
</section><!-- .no-results -->
