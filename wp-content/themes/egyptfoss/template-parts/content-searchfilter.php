<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package egyptfoss
 */
if(isset($_GET["type"]))
{
    $current_type = $_GET["type"];
}else
{
    $current_type = "all";
}

?>
<?php $search_query = stripslashes($_GET['s']);// filter_input(INPUT_GET, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS); ?>
<ul id="results-sections" class="categories-list collapse in">
  <li <?php echo ($current_type =="all")?"class='active'":"" ?>><a href="<?php bloginfo('url'); ?>/?s=<?= $search_query?>"><i class="fa fa-search"></i> <?php _e('All Results', 'egyptfoss') ?></a></li>
  <li <?php echo ($current_type =="news")?"class='active'":"" ?>><a href="<?php bloginfo('url'); ?>/?s=<?= $search_query?>&type=news"><i class="fa fa-newspaper-o"></i> <?php _e('News', 'egyptfoss') ?></a></li>
  <li <?php echo ($current_type =="tribe_events")?"class='active'":"" ?>><a href="<?php bloginfo('url'); ?>/?s=<?= $search_query?>&type=tribe_events"><i class="fa fa-calendar"></i> <?php _e('Events', 'egyptfoss') ?></a></li>
  <li <?php echo ($current_type =="product")?"class='active'":"" ?>><a href="<?php bloginfo('url'); ?>/?s=<?= $search_query?>&type=product"><i class="fa fa-cube"></i> <?php _e('Products', 'egyptfoss') ?></a></li>
  <li <?php echo ($current_type =="open_dataset")?"class='active'":"" ?>><a href="<?php bloginfo('url'); ?>/?s=<?= $search_query?>&type=open_dataset"><i class="fa fa-database"></i> <?php _e('Open Datasets', 'egyptfoss') ?></a></li>
  <li <?php echo ($current_type =="collaboration-center")?"class='active'":"" ?>><a href="<?php bloginfo('url'); ?>/?s=<?= $search_query?>&type=collaboration-center"><i class="fa fa-folder"></i> <?php _e('Collaboration Center', 'egyptfoss') ?></a></li>
  <li <?php echo ($current_type =="service")?"class='active'":"" ?>><a href="<?php bloginfo('url'); ?>/?s=<?= $search_query?>&type=service"><i class="fa fa-briefcase"></i> <?php _e('Services', 'egyptfoss') ?></a></li>
  <li <?php echo ($current_type =="request_center")?"class='active'":"" ?>><a href="<?php bloginfo('url'); ?>/?s=<?= $search_query?>&type=request_center"><i class="fa fa-question-circle"></i> <?php _e('Request Center', 'egyptfoss') ?></a></li>
  <li <?php echo ($current_type =="pedia")?"class='active'":"" ?>><a href="<?php bloginfo('url'); ?>/?s=<?= $search_query?>&type=pedia"><i class="fa fa-lightbulb-o"></i> <?php _e('FOSSpedia', 'egyptfoss') ?></a></li>
  <li <?php echo ($current_type =="success_story")?"class='active'":"" ?>><a href="<?php bloginfo('url'); ?>/?s=<?= $search_query?>&type=success_story"><i class="fa fa-book"></i> <?php _e('Success Stories', 'egyptfoss') ?></a></li>
  <li <?php echo ($current_type =="expert_thought")?"class='active'":"" ?>><a href="<?php bloginfo('url'); ?>/?s=<?= $search_query?>&type=expert_thought"><img src="<?php echo get_template_directory_uri(); ?>/img/expert-bullet.svg" class="list-icon-small" alt=""> <?php _e('Expert Thoughts', 'egyptfoss') ?></a></li>
  <li <?php echo ($current_type =="Entity")?"class='active'":"" ?>><a href="<?php bloginfo('url'); ?>/?s=<?= $search_query?>&type=Entity"><i class="fa fa-university"></i> <?php _e('Entities', 'egyptfoss') ?></a></li>
  <li <?php echo ($current_type =="Individual")?"class='active'":"" ?>><a href="<?php bloginfo('url'); ?>/?s=<?= $search_query?>&type=Individual"><i class="fa fa-users"></i> <?php _e('Individuals', 'egyptfoss') ?></a></li>
</ul>
