<?php
/**
 * Single News
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package egyptfoss
 */

if($post->post_status != "publish")
{
  //Not Found
  include( get_query_template( '404' ) );
  exit;  
}

//check if loggedIn user and subscriber
if (is_user_logged_in() && !current_user_can('add_new_ef_posts')) {
  wp_redirect(home_url('/?status=403'));
  exit;
}

get_header(); 

// get quiz_id
$quiz_id = get_post_meta(get_the_ID(), "quiz_id", true);
$pageTitle = $post->post_title;
if($quiz_id)
{ 
  global $wpdb;
  $sql = "select quiz_name from {$wpdb->prefix}mlw_quizzes where quiz_id = {$quiz_id}";
  $quiz_name = $wpdb->get_col($sql);
  if($quiz_name[0] != NULL)
  {
    $pageTitle = $quiz_name[0];
  }
}
?>
	<header class="page-header">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h1 itemprop="headline"><?php echo $pageTitle; ?></h1>
				</div>
			</div>
		</div>
	</header>
<?php 
// retrieve shortcode of quiz
$content = get_the_content();
$short_code = apply_filters('the_content', $content);
?>
<div class="container">
  <div class="row">
    <div class="content-area col-md-12">
      <?php if(get_current_user_id() > 0 ) {
        echo do_shortcode($short_code);
      }else {
        //show quiz data
        if($quiz_id)
        {           
          ?>
          
          <div class="empty-state-msg">
            <i class="fa fa-3x fa-lock"></i>
            <br>
            <h3><?php _e("Please log in","egyptfoss"); ?></h3>
            <p><?php echo __("to take this quiz and test your FOSS knowledge","egyptfoss"); ?></p>
            <p><a href="<?php echo home_url( pll_current_language()."/login/?redirected=takequiz&redirect_to=".  get_permalink(get_the_ID() ) ) ?>" class="btn btn-primary"><?php _e("Log In","egyptfoss"); ?></a></p>
          </div>
        <?php
        }else {
          include( get_query_template( '404' ) );
          exit;  
        }
      }
      ?>
    </div>
  </div>
</div>

<?php 
get_footer();
