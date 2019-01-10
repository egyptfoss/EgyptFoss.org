<form class="" action="<?php echo home_url(); ?>" method="get" id="searchform_inner">
    <div class="input-group">
        <input type="search" value="<?php the_search_query(); ?>" name="s" id="s_inner" class="form-control result-search" placeholder="<?php _e('Search Site', 'egyptfoss') ?>">
        <?php if( isset( $_GET['type'] ) ): ?>
            <input type="hidden" value="<?php echo $_GET['type']; ?>" name="type">
        <?php endif; ?>
        <span class="input-group-btn">
            <button class="btn btn-primary"><?php _e("Search", "egyptfoss"); ?></button>
        </span>
    </div>
    <!-- check empty and special characters -->
    <?php 
        /*$error_msg = '';
        if(isset($_GET["s"]))
        {
            $query_string = trim($_GET["s"]);
            if(empty($query_string))
            {
                $error_msg = __("Search","egyptfoss").' '.__("required","egyptfoss");
            }else
            {
                $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $query_string);
                $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $query_string);

                if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
                  $error_msg = __("Search","egyptfoss"). ' ' . __("must at least contain one letter",'egyptfoss');
                }
            }
        }else
        {
            $error_msg = __("Search","egyptfoss").' '.__("required","egyptfoss");
        }*/
        $error_msg = "";
    ?>
    <div id="s_inner_validate_hidden">
        <?php if(!empty($error_msg)) { ?>
        <label for="s_inner" class="error" id="s_inner-error"><?php echo $error_msg; ?></label>
        <?php } ?>
    </div>
</form>
