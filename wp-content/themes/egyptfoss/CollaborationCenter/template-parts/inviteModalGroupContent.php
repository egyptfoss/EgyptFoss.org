<?php if($item) { ?>
<form class="form" id="invite_grp_form">
  <div class="form-group row">
    <div class="col-md-12">
      <?php
      if (pll_current_language() == 'ar') {
        global $ar_sub_types;
        $sub_types = $ar_sub_types;
      } else {
        global $en_sub_types;
        $sub_types = $en_sub_types;
      }
      $selected = $item->getAnonPermissionByType("type");
      ?>
      <label for="" class="label"><?php _e("Type", "egyptfoss") ?></label>
        <select name="share_type" id="share_type" class="custom-select2 form-control share_type" style="width:100%">
          <option value=""><?php _e('Select', 'egyptfoss'); ?></option>
          <option value="Individual" <?php if ($selected == "Individual") { ?>selected<?php } ?>><?php _e('Individual', 'egyptfoss'); ?></option>
          <option value="Entity" <?php if ($selected == "Entity") { ?>selected<?php } ?>><?php _e('Entity', 'egyptfoss'); ?></option>
        </select>
   
         </div>
  </div>
  <div class="form-group row">
    <div class="col-md-12">
      <label for="" class="label"><?php _e('Account sub type', 'egyptfoss'); ?></label>
      <select name="sub_type" id="sub_type" class="custom-select2 form-control input-sm share_sub_type" style="width:100%">

        <option class="Individual Entity Event" value=""><?php _e('Select', 'egyptfoss'); ?></option>
        <?php
        $selected = $item->getAnonPermissionByType("sub_type");
        $account_sub_type_labels = array();
        global $account_sub_types;
        foreach ($account_sub_types as $sub => $t) {
          $account_sub_type_labels = array_merge($account_sub_type_labels, array($sub => $sub_types[$sub]));
        }
        asort($account_sub_type_labels);
        foreach ($account_sub_type_labels as $sub => $label) {
          if ($selected == $sub) {
            echo("<option class='" . $account_sub_types[$sub] . "' value='" . $sub . "' selected>");
          }else
          {
            echo("<option class='" . $account_sub_types[$sub] . "' value='" . $sub . "' >"); 
          }
          echo($label);
          echo("</option>");
        }
        ?>
      </select>     
    </div>
  </div>

  <div class="form-group row">
    <div class="col-md-12">
      <label for="" class="label"><?php _e("Interests", "egyptfoss") ?></label>
      <select 
        class="add-product-tax form-control input-sm L-validate_taxonomy" 
        id="share_interests" 
        name="share_interests[]" 
        data-placeholder="<?php _e('Select', 'egyptfoss'); ?>" 
        style="width:100% ;visibility: hidden;" 
        multiple="multiple">
        <optgroup>
          <?php
          $post_taxonomies = get_terms("interest", array('hide_empty' => 0));
          $selected = $item->getTaxPermissionIdsByName("interest");
          foreach ($post_taxonomies as $post_tax) {
            ?>
            <option value="<?php echo $post_tax->term_id ?>" 
                      <?php if (in_array($post_tax->term_taxonomy_id, $selected)) { ?>selected<?php } ?>>
            <?php echo $post_tax->name ?>
            </option>
<?php } ?>
        </optgroup>
      </select>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-md-12">
      <label for="" class="label"><?php _e("Technologies", "egyptfoss") ?></label>
      <select 
        class="add-product-tax form-control input-sm L-validate_taxonomy" 
        id="share_technologies" 
        name="share_technologies[]" 
        data-placeholder="<?php _e('Select', 'egyptfoss'); ?>" 
        style="width:100% ;visibility: hidden;" 
        multiple="multiple">

        <optgroup>
          <?php
          $post_taxonomies = get_terms("technology", array('hide_empty' => 0));
          $selected = $item->getTaxPermissionIdsByName("technology");
          foreach ($post_taxonomies as $post_tax) {
            ?>
            <option value="<?php echo $post_tax->term_id ?>" 
            <?php if (in_array($post_tax->term_taxonomy_id, $selected)) { ?>selected<?php } ?>>
            <?php echo $post_tax->name ?>
            </option>
<?php } ?>
        </optgroup>
      </select>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-md-12">
      <label for="" class="label"><?php echo _e("Theme", "egyptfoss") ?></label>
      <select 
        class="custom-select2 form-control input-sm " 
        id="share_industry" 
        name="share_industry" 
        style="width:100% ;">
        <option value=""><?php echo __("Select", 'egyptfoss') ?></option>
        <optgroup>

          <?php
          $post_taxonomies = get_terms("theme", array('hide_empty' => 0));
          $selected = $item->getTaxPermissionIdsByName("theme");
          foreach ($post_taxonomies as $post_tax) {
            ?>
            <option value="<?php echo $post_tax->term_id ?>" 
            <?php if (in_array($post_tax->term_taxonomy_id, $selected)) { ?>selected<?php } ?>>
            <?php echo (pll_current_language() == "ar")?($post_tax->name_ar)?$post_tax->name_ar:$post_tax->name:$post_tax->name; ?>
            </option>
<?php } ?>
        </optgroup>
      </select>
    </div>
  </div>
  <?php wp_nonce_field( 'invite_group' ); ?>
</form>
<?php } ?>
