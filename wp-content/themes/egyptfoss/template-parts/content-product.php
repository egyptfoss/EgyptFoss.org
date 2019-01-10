<div class="ef_product_set">
<?php
$productList = ef_listing_get_products_by_offest();
foreach($productList as $post ){
  $post = get_post($post);
  setup_postdata($post);
  ?> 
    <div class="product-card" itemscope itemtype="http://schema.org/Product" id="<?php the_ID() ?>">
      <div class="product-card-body clearfix">
          <div class="product-img lfloat">
              <a href="#">     
                  <?php
                  if(has_post_thumbnail(get_the_ID())){
                    the_post_thumbnail('post-thumbnail', array("alt" => get_the_title()));
                  }else{
                  ?>
                  <img src="<?php echo get_template_directory_uri()."/img/no-product-icon.png"; ?>" alt="<?php echo get_the_title(); ?>">
                  <?php } ?>
              </a>
          </div>
          <div class="product-card-info lfloat">
              <h3 class="product-name"><a href="<?php the_permalink(); ?>" itemprop="name"><?php the_title(); ?></a></h3>
              <p><?php
                $prodct_desc = get_field("description", get_the_ID());
                $pos= strpos($prodct_desc, ' ', 100);
                $newdesc = substr($prodct_desc,0,$pos );
                if($newdesc)
                {
                 echo $newdesc . " ..."; 
                }else
                {
                  echo $prodct_desc; 
                }
                
              ?></p>
              <?php
              $developer = get_field("developer", get_the_ID());
              if ($developer) {
                ?>
                <p><strong><?php _e("By", "egyptfoss"); ?></strong> <a href="<?php echo get_field("link_to_source", get_the_ID()); ?>"><?php echo $developer; ?></a></p>
              <?php }else{ ?>
                <p><strong><?php echo __("By", "egyptfoss"). " --"; ?></strong></p>
              <?php } ?>  
          </div>
      </div>
      <div class="product-meta">
          <?php
          $taxonomies = array("license", "platform", "technology");
          foreach ($taxonomies as $tax) {
            $taxElement = wp_get_post_terms(get_the_ID(), $tax);
            $taxElementCount = count($taxElement);
            if ($taxElement) {
              ?>
              <span class="meta-item">
                  <strong><?php _e(ucwords($tax), "egyptfoss"); ?></strong> 
                  <?php
                  foreach($taxElement as $key=>$element){
                    if($key < 2)
                    {
                      echo $taxElement[$key]->name;
                      if ($key < 1 && $taxElementCount != 1) {echo ", ";}
                    }
                  }
                  ?>
              </span>
            <?php }else{ ?>
              <span class="meta-item">
                  <strong><?php echo __(ucwords($tax), "egyptfoss"). " --"; ?></strong> 
              </span>
            <?php }
          } ?>
          <br>
          <?php
          $keywords = wp_get_post_terms(get_the_ID(), 'keywords');
          $keywordsCount = count($keywords);
          if ($keywords) {
            ?>
            <i class="fa fa-tags"></i>
            <?php foreach ($keywords as $key => $keyword) { 
              if($key < 2)
              {
              ?>
              <a href="#"><?php echo $keyword->name; ?></a><?php if ($key < 1 && $taxElementCount != 1) {echo ",";}
              }
              ?>
            <?php }
          } ?>
      </div>
  </div>
<?php } ?>
</div>
