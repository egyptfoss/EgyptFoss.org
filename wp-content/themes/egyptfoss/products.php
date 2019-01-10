<?php
  /**
   * Template Name: All Products.
   *
   */

  $args = array(
    "post_status" => "publish",
    "post_type" => "product",
    'posts_per_page' => -1
  );
  global $wpdb;
  
  $sql = "SELECT DISTINCT post.ID, post.post_title, post.guid, term.name cat FROM {$wpdb->prefix}posts as post 
        JOIN {$wpdb->prefix}postmeta AS pmeta ON post.ID = pmeta.post_id
        JOIN {$wpdb->prefix}terms AS term on pmeta.meta_value = term.term_id
        WHERE (post.post_status = 'publish' AND post.post_type = 'product' AND pmeta.meta_key = 'industry')
        ORDER BY term.name ASC";
  
  $productList = $wpdb->get_results( $sql );

?>
<html>
    <head>
      <title>All products - Egyptfoss</title>
      <style>
          * {
            font-family: 'Droid Arabic Kufi', 'Monda', Arial, Helvetica, sans-serif;
          }
          
          tr:hover {
            background-color: #efefef;
          }
      </style>
    </head>
    <body>
        <table border="1">
          <tr>
              <th>Index</th>
              <th>Category</th>
              <th>Name</th>
              <th>License</th>
              <th>Technology</th>
              <th>Interests</th>
              <th>Developer</th>
              <th>Platform</th>
              <th>Type</th>
          </tr>
       <?php

          $data = array();
          foreach($productList as $i => $post ){
            $index = $i + 1;
            echo '<tr>';
            echo "<td>{$index}</td>";
            echo "<td>{$post->cat}</td>";
            echo '<td><a href="'.$post->guid.'" title="'.$post->post_title.'" target="_blank">'.wp_trim_words($post->post_title, 6, ' ...' ).'</a></td>';


            $fields = array("license", "technology", "interest", "developer", "platform", "type");
            foreach ($fields as $field) {

              $terms_ids = get_post_meta( get_the_ID(), $field, true );

              if( is_array($terms_ids) ) {
                $names = array();
                foreach ( $terms_ids as $term_id){
                    $term = get_term( $term_id, $field );
                    $names[] = $term->name;
                }
                $value = implode(', ', $names);
              } elseif(ctype_digit($terms_ids)) {
                $term = get_term( $terms_ids, $field );
                $value = $term->name;
              } else {
                $value = $terms_ids;
              }

              echo "<td>{$value}</td>";
            }

            echo '</tr>';
          } ?>
      </table>
    </body>
</html>
