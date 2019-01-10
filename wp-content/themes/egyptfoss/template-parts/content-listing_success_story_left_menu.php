<?php 
    $list_success_stories = count_success_stories();
    $count = count($list_success_stories);
    $lang = pll_current_language();
    
    //Success Story Categories
    $categories = get_terms('success_story_category', array('hide_empty' => 0));
    if(pll_current_language() == "ar")
    {
      $sorted_ind = array();
      foreach ($categories as $key => $row)
      {

          $sorted_ind[$key] = $row->name_ar;
      }
      array_multisort($sorted_ind, SORT_ASC, $categories);
    }    
    $current_category = -1;
    //check query string
    if($_GET['category'] != null){
        if($_GET['category'] != "all")
        {
            //get id of selected category
            $current_category = ef_return_taxonomy_id_by_name(html_entity_decode($_GET['category']), 'success_story_category');
            set_query_var('ef_listing_success_stories_category_id', $current_category);
        }
    }
?>
<div class="side-menu">
    <ul class="categories-list industry-list">
        <li <?php echo ($current_category == -1)?'class="active"':''; ?>><a href="" onclick="return false;" data-slug="all" class="trigger_click" data-id="-1"><?php _e("All","egyptfoss"); ?></a>
            <span class="count" style="display: none;"><?php echo $count; ?></span>
        </li>
       <?php foreach($categories as $category) { 
           if($lang == "ar")
           {
               if($category->name_ar != '')
                   $category->name = $category->name_ar;
           }
        ?>   
       <li <?php echo ($current_category == $category->term_id)?'class="active"':''; ?>><a href="" onclick="return false;" data-slug="<?php echo rawurlencode($category->name); ?>" class="trigger_click" data-id="<?php echo $category->term_id; ?>"><?php echo $category->name; ?></a> 
          <span class="count" style="display: none;"><?php echo ef_get_count_per_success_story_category($list_success_stories,$category->term_id); ?></span>
       </li>
       <?php } ?>
    </ul>
</div>

