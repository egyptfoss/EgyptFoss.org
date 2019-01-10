      <div class="row filter-bar">
                    <div class="col-md-7">
                        <?php
                        //          $productsCount = get_product_count();
                        ?>
                    </div>
                    <div class="col-md-5">
                        <div class="filter-btns">
                            <a  class="btn btn-default" data-toggle="collapse" data-target="#filter-products"><i class="fa fa-filter"></i> <?php _e("Filter", "egyptfoss"); ?></a>
                            <?php if ( !is_user_logged_in() ) { ?>
                              <a href="<?php echo home_url( '/wp-login.php?redirected=addproduct' ); ?>" class="btn btn-light"><i class="fa fa-plus"></i> <?php _e("Add Product", "egyptfoss"); ?></a>
                            <?php } else if (current_user_can('add_new_ef_posts')) { ?>
                              <a href="<?php echo get_current_lang_page_by_template("page-add-product.php"); ?>" class="btn btn-light"><i class="fa fa-plus"></i> <?php _e("Add Product", "egyptfoss"); ?></a>
                            <?php } else { ?>
                              <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
                              <a id="sub-tooltip" href="javascript:void(0)" class="btn btn-light" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><i class="fa fa-plus"></i> <?php _e("Add Product", "egyptfoss"); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <section class="filter-nav collapse" id="filter-products">
                   <div class="form-group row">
                   	                    <?php
                    $term_taxs = array("type", "license", "platform", "technology");
                    foreach ($term_taxs as $term_tax) {
                      ?>
                      <div class="col-md-2">
                      	                <select class="custom-select2 form-control filter-class topFilters" data-taxonomy="<?php echo $term_tax ?>" hidden="hidden" style="width:100%;">

                          <option value=""><?php echo __($term_tax,'egyptfoss') ?></option>

                              <?php
                              $terms_data = get_terms($term_tax, array('hide_empty' => 0));
                              foreach ($terms_data as $term_data) {
                                ?>
                                <option  data-slug="<?php echo $term_data->slug  ?>" value="<?php echo $term_data->term_taxonomy_id ?>"
                                    <?php if ($getParams[$term_tax] == $term_data->slug) { ?>selected="selected"<?php } ?>
                                       ><?php echo $term_data->name; ?></option>
  <?php } ?>

                    </select>
                      </div>
<?php } ?>
                  <div class="col-md-2">
                  	 <button class="btn btn-link reset-filters"><i class="fa fa-remove"></i> <?php _e('Reset', 'egyptfoss') ?> </button>
                  </div>
                   </div>
                </section>
