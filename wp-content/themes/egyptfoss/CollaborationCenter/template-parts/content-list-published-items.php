<?php
  $current_section = '';
  
  if( isset( $_GET['section'] ) ) {
    $current_section = $_GET['section'];
  }
?>
<div  class="spaces-listing col-md-9 content-area">
  <div class="category-row clearfix">
    <select class="technologies rfloat" id="sectionFilter" style="width:300px;">
        <option value=""><?php _e("All","egyptfoss") ?></option>
        <?php 
        global $ef_sections;
        foreach ($ef_sections as $key=>$section)
        {
          $ef_sections[$key] = __($section,"egyptfoss");
        }
        asort($ef_sections);
        foreach ($ef_sections as $key=>$section)
        {
          if( $key == 'collaboration-center' ) continue;
        ?>
        <option value="<?php echo $key ?>" <?php selected( $key, $current_section ); ?>><?php _e($section,"egyptfoss") ?></option>
        <?php }
        ?>
      </select>
      <div class="ef-results-meta" <?php echo !count($items)?'style="display:none;"':''; ?>>
        <?php
          printf( 
              '%s <span class="ef-results-count">%s</span> %s '.
              '<span class="ef-category" %s> '.
              '%s <span class="ef-category-name">"%s"</span>'.
              '</span>',
              __( 'Showing', 'egyptfoss' ),
              count( $items ),
              __('results', 'egyptfoss'),
              (empty($current_section))?'style="display:none;"':'',
              __( 'From', 'egyptfoss' ),
              __( $ef_sections[$current_section], 'egyptfoss' )
          );
        ?>
      </div>
  </div>
  <div class="nano">
    <div class="nano-content" id="SpacesAndDocumentsDiv">
      <div class="loading-overlay loading_published_collaboration hidden">
                   <div class="spinner">
                       <div class="double-bounce1"></div>
                       <div class="double-bounce2"></div>
                   </div>
       </div>
      <div class="publishedItemCards">
      <?php
     include(locate_template('CollaborationCenter/template-parts/published-item-cards.php'));
     ?>
     </div>   
    </div>
  </div>
</div>
