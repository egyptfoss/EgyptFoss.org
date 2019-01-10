<?php

$terms_custom_fields = array(
  'industry',
  'type',
  'theme',
  'datasets_license',
  'dataset_type',
  'success_story_category',
  'target_bussiness_relationship',
  'request_center_type',
  'news_category',
  'quiz_categories',
  'service_category',
  'license',
  'platform'
  );
function taxonomy_custom_fields($tag) {
  if($_GET["action"] == "edit")
  {
  ?> 
  <script>
    jQuery(document).ready(function ($) {
      $(".term-name-wrap").after('<tr class="form-field form-required term-name-wrap">'+
        '<th scope="row"><label for="name">Name ar</label></th>'+
        '<td><input type="text" aria-required="true" size="40" value="<?php echo $tag->name_ar; ?>" id="name" name="nameAr">'+
        '<p class="description">The name is how it appears on your site in arabic.</p>'+
        '</tr>');
    });
  </script>
  <?php }else{ ?>
  <script>
    jQuery(document).ready(function ($) {
      $(".term-name-wrap").after('<div class="form-field term-name_ar-wrap">'+
        '<label for="tag-nameAr">Name ar</label>'+
        '<input type="text" size="40" value="" id="tag-nameAr" name="nameAr">'+
        '<p>The name is how it appears on your site in arabic.</p>'+
        '</div> ');
    });
  </script>
  <?php
  }
}

foreach($terms_custom_fields as $terms_custom_field)
{
    add_action($terms_custom_field.'_edit_form_fields', 'taxonomy_custom_fields', 10, 2);
    add_action($terms_custom_field.'_add_form_fields', 'taxonomy_custom_fields', 10, 2);
}

function save_taxonomy_custom_fields($term_id) {
  if (isset($_POST['nameAr'])) {
    global $wpdb;
    $nameAr = sanitize_text_field($_POST['nameAr']);
    $sql = "update {$wpdb->prefix}terms set name_ar = '{$nameAr}' where term_id={$term_id}";
    $wpdb->query($sql);
  }
} 

foreach($terms_custom_fields as $terms_custom_field)
{
    add_action('edited_'.$terms_custom_field, 'save_taxonomy_custom_fields', 10, 2);
    add_action('create_'.$terms_custom_field, 'save_taxonomy_custom_fields', 10, 2);
}

add_filter( 'get_terms', 'ef_get_terms',10,3 );
function ef_get_terms($terms, $taxonomies, $args)
{
    global $terms_custom_fields;
    foreach($terms_custom_fields as $terms_custom_field)
    {
        if(in_array($terms_custom_field, $taxonomies))
        {
          foreach ($terms as $term)
          {
            if(pll_current_language()=="ar" && $term->name_ar && $term->taxonomy == $terms_custom_field)
            {
              $term->name = $term->name_ar;
            }
          }
        }
    }
    
    return $terms;
}

function add_post_tag_columns($columns){
   $columns = array_slice($columns, 0, 2, true) +
    array("name_ar" => "Name ar") +
    array_slice($columns, 2, count($columns) - 1, true) ;
    return $columns;
}

foreach($terms_custom_fields as $terms_custom_field)
{
    add_filter('manage_edit-'.$terms_custom_field.'_columns', 'add_post_tag_columns');
}
 
function add_post_tag_column_content($content, $custom_column, $term_id){
  $term = get_term($term_id);
  $content .= $term->name_ar;
  return $content;
}

foreach($terms_custom_fields as $terms_custom_field)
{
    add_filter('manage_'.$terms_custom_field.'_custom_column', 'add_post_tag_column_content',10,3);
}
function enableOrderByForCustomFields($orderby, $args, $taxonomies){
  // taxonomies english only
  $arrTax = array('license','interest','technology','platform');
  $lang = pll_current_language();
  if($args["orderby"] == "name_ar"){
    return "t.".$args["orderby"];
  } else if(($args["orderby"] == "name" || $args["orderby"] == "t.name") && $lang == "ar"
          && !in_array($taxonomies[0], $arrTax))
  {
    return "t.name_ar";
  }
  return $orderby;
}
add_filter( 'get_terms_orderby', 'enableOrderByForCustomFields',10,3 );