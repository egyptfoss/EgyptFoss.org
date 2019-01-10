<?php
/**
 * Template Name: Add Product.
 *
 * @package egyptfoss
 */
if ( !is_user_logged_in()) {
  wp_redirect( home_url( '/wp-login.php?redirected=addproduct' ) ); /* this is where user will be redirected if he is not logged in.*/
  exit;
} else if (!current_user_can('add_new_ef_posts')) {
  wp_redirect( home_url() );
  exit;
} else {
if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) &&  $_POST['action'] == "add_product" && isset($_POST['postid'])) {
    $nonce = $_REQUEST['_wpnonce'];
    if ( ! wp_verify_nonce( $nonce, 'add-product' ) ) {
      wp_redirect( home_url( '/?status=403' ) );
      exit;
    }
    
    foreach (array("functionality", "prerequisites", "audience", "objectives", "description") as $parameter) {
      if (array_key_exists($parameter, $_POST)) {
        $_POST[$parameter] = strip_js_tags($_POST[$parameter]);
      }
    }

    $errorMessages = '';
    if ( trim( $_POST['product_title'] ) === '' ) {
      $errorMessages .= __('Please enter a Product Title.','egyptfoss') . "\n";
      $hasError = true;
    }
    if ( trim( $_POST['description'] ) === '' ) {
      $errorMessages .= __('Please enter the Product Description.','egyptfoss') . "\n";
      $hasError = true;
    }
    $term = term_exists($_POST['post_industry'], 'industry');
    if ($term === 0 || $term === null) {
      $errorMessages .= __('Please enter the Product category.','egyptfoss') . "\n";
      $hasError = true;
    }
    
    global $ef_product_multi_uncreated_tax;
    foreach ($ef_product_multi_uncreated_tax as $uncreated_tax) {
      if (isset($_POST['post_' . $uncreated_tax]) && !empty($_POST['post_' . $uncreated_tax])) {
        foreach ($_POST['post_' . $uncreated_tax] as $term) {
          if (!get_term_by('slug', $term, $uncreated_tax)) {
            $errorMessages .= sprintf(__("Please select already exist %s", "egyptfoss"), __($uncreated_tax, "egyptfoss")) . "\n";
            $hasError = true; 
          }
        }
      }
    }
    //check if one of the uploads not an image
    $errorScreenshots = false;
    if($_FILES["files"]) {
      foreach ($files['name'] as $key => $value) {
        if ($files['name'][$key]) {
            if($files['type'][$key] != 'image/png' && $files['type'][$key] != 'image/jpg'
                    && $files['type'][$key] != 'image/jpeg')
                $errorScreenshots = true;
        }
      }
    }
    
    if($errorScreenshots) {
        $errorMessages .= __('Please enter valid product screenshots (jpeg,jpg,png).','egyptfoss') . "\n";
        $hasError = true; 
    }

    // code below is to save the information that we entered in the form. Insert the product into the database
    $product_title = $_POST['product_title'];
    $developer = trim( $_POST['developer'] );
    $description = $_POST['description'];
    $functionality = $_POST['functionality'];
    $usage_hints = $_POST['usage_hints'];
    $references = $_POST['references'];
    $link_to_source = $_POST['link_to_source'];
    // post tags //
    $post_industry = $_POST['post_industry'];
    $post_type = $_POST['post_type'];
    $post_technology = $_POST['post_technology'];
    $post_platform = $_POST['post_platform'];
    $post_license = $_POST['post_license'];
    $post_interest = $_POST['post_interest'];

    // check if title product_name already exists in wpdb
    global $wpdb;
    $query = $wpdb->prepare('SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s', $product_title);
    $wpdb->query( $query );

    if ( $wpdb->num_rows ) {  // ---- if product already exists ---- //
    	$errorMessages .= __('Product','egyptfoss').' '.$_POST['product_title'].' '.__("already exists","egyptfoss") . "\n";
      $hasError = true;
    } else if (!$hasError) {  // ---- product not exists ... add product ---- //
        $add_product_array = array(
            'post_title' => $product_title,
            'post_type'	=> 'product',
            'post_status' => 'pending',
            'post_author' => get_current_user_id()
        );

        $product_id = wp_insert_post( $add_product_array );  // getting product id after adding the product title & type
        if ($developer !== ''){ add_post_meta($product_id, 'developer', $developer); }
        add_post_meta($product_id, 'description', $description);
        if ($functionality !== ''){ add_post_meta($product_id, 'functionality', $functionality); }
        if (!empty($usage_hints)){ add_post_meta($product_id, 'usage_hints', $usage_hints); }
        if (!empty($references)){ add_post_meta($product_id, 'references', $references); }
        if (!empty($link_to_source)){ add_post_meta($product_id, 'link_to_source', $link_to_source); }

        
        //adjust post name
        $my_post = array(
            'ID'           => $product_id,
            'post_name' => wp_unique_post_slug(str_replace(' ', '-', strtolower($product_title)), 
                    $product_id, 'publish', 'product', 0)
        );
        
        // save taxonomies: Product ID, taxonomy name, appends(true) or rewrite(false)
        $taxonomies = array('industry', 'type', 'technology', 'platform', 'license', 'interest');
        foreach ($taxonomies as $tax){
          $term_ids = wp_set_object_terms($product_id, $_POST['post_'.$tax], $tax);  // insert tax in db
          update_post_meta($product_id, $tax, getTermFromTermTaxonomy($term_ids));  // update post_meta with keys and serialized ids so the backend can see it.
        }
        
        update_post_meta($product_id, 'language', serialize(array(
          "slug" => pll_current_language(),
          "translated_id" => 0)));
        wp_set_object_terms($product_id, pll_current_language(), 'language');

        $productCreated = __("Product",'egyptfoss').' '. $product_title .' '.__("added successfully, it is now under review",'egyptfoss');

        // INSERT OUR MEDIA ATTACHMENTS, Check that the nonce is valid, and the user can edit this post.
      	// add_filter( 'upload_dir', 'product_logo_dir' );
      	// function product_logo_dir( $dirs ) {
      	// 	global $product_id ;
      	//     $dirs['subdir'] = '/product_logos/'.$product_id;
      	//     $dirs['path'] = $dirs['basedir'] .'/product_logos/'.$product_id;
      	//     $dirs['url'] = $dirs['baseurl'] .'/product_logos/'.$product_id;
      	//     return $dirs;
      	// }
        if ( isset( $_POST['product_logo_nonce'], $product_id ) && wp_verify_nonce( $_POST['product_logo_nonce'], 'product_logo' )) {
            // The nonce was valid and the user has the capabilities, it is safe to continue.
            // These files need to be included as dependencies when on the front end.
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );

            // Let WordPress handle the upload.. Remember, 'product_logo' is the name of file input in form.
            $attachment_id = media_handle_upload( 'product_logo', $product_id);
            if ( !is_wp_error( $attachment_id ) ) {
              update_post_meta($product_id, '_thumbnail_id', $attachment_id);  // update post meta with the metakey _thumbnail_id so backend see photo.
              // "The image was uploaded successfully!"
            } else {
              // "There was an error uploading the image."
            }
        } else {
          // echo "Sorry there is an error in uploading logo "; // The security check failed, show the user an error.
        }
        // ---- we must handle maximum size upload for screenshots .... //
        // remove_filter( 'upload_dir', 'product_logo_dir' );

        // -------- Insert screenshots ----- //
        // add_filter( 'upload_dir', 'product_screenshots_dir' );
        // function product_screenshots_dir( $dirs ) {
        //   global $product_id ;
        //   $dirs['subdir'] = '/products_screenshots/'.$product_id;
        //   $dirs['path'] = $dirs['basedir'] .'/products_screenshots/'.$product_id;
        //   $dirs['url'] = $dirs['baseurl'] .'/products_screenshots/'.$product_id;
        //   return $dirs;
        // }
        function my_handle_attachment($file_handler, $product_id, $set_thu=false) {
          // check to make sure its a successful upload
          if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();
          require_once(ABSPATH . "wp-admin" . '/includes/image.php');
          require_once(ABSPATH . "wp-admin" . '/includes/file.php');
          require_once(ABSPATH . "wp-admin" . '/includes/media.php');

          $attachment_id = media_handle_upload( $file_handler, $product_id );
          // $product_id = array ();
          // $array = array_push($product_id, 'fg_perm_metadata', $attachment_id);

          return $attachment_id;
          if ( is_numeric( $attach_id ) ) {
              update_post_meta( $product_id, 'fg_temp_metadata', $attach_id );  // fg_temp_metadata like the meta key inserted from backend
          }
        }
        
        if( 'POST' == $_SERVER['REQUEST_METHOD']  ) {
            if ( $_FILES ) {
                $files = $_FILES["files"];  // files is the input name
                $images_ids = array();
                foreach ($files['name'] as $key => $value) {
                    if ($files['name'][$key]) {
                        $file = array(
                        'name' => $files['name'][$key],
                        'type' => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error' => $files['error'][$key],
                        'size' => $files['size'][$key]
                        );
                        $_FILES = array ("files" => $file);
                        foreach ($_FILES as $file => $array) {
                            $newupload = my_handle_attachment($file, $product_id);
                            $images_ids = array_merge($images_ids,array($newupload));
                        }
                        
                        
                    }
                    // we must handle maximum size upload for screenshots .... //
                }
                $images_ids = implode(',', $images_ids);
                update_post_meta($product_id, 'fg_perm_metadata', $images_ids);
            }
            $isGrantedToPublish = false;
            $efb_permissions = EFBBadgesUsers::getBadgesPermByUser(get_current_user_id());   
            if($efb_permissions)
            {
              foreach ($efb_permissions as $perm) {  
                $permTypes = split("__", $perm["granted_permission"]);
                if (in_array("product", $permTypes)) {
                  $isGrantedToPublish = true;
                }
              }
            }
            if($isGrantedToPublish){
              setMessageBySession("ef_product_messages", "success", array( __("Product",'egyptfoss').' '. $_POST['product_title'] .' '.__("added successfully",'egyptfoss'))) ;
            }else{
              setMessageBySession("ef_product_messages", "success", array(__("Product",'egyptfoss').' '. $_POST['product_title'] .' '.__("added successfully, it is now under review",'egyptfoss'))) ;
            }
            wp_redirect( get_permalink( $product_id ) );
        }

		  // remove_filter( 'upload_dir', 'product_screenshots_dir' );


		// $valid_formats = array("jpg", "png", "gif", "zip", "bmp");
		// $max_file_size = 2048*100; //200 kb
		// $path = "uploads/products_screenshots"; // Upload directory
		// $count = 0;

		// if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
		// // Loop $_FILES to exeicute all files
		// 	foreach ($_FILES['files']['name'] as $f => $name) {
		// 	if ($_FILES['files']['error'][$f] == 4) {
		// 		    continue; // Skip file if any error found
		// 		}
		// 		if ($_FILES['files']['error'][$f] == 0) {
		// 		    if ($_FILES['files']['size'][$f] > $max_file_size) {
		// 		        $message[] = "$name is too large!.";
		// 		        continue; // Skip large files
		// 		    }
		// 			elseif( ! in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats) ){
		// 				$message[] = "$name is not a valid format";
		// 				continue; // Skip invalid file formats
		// 			}
		// 		    else{ // No error found! Move uploaded files
		// 		        if(move_uploaded_file($_FILES["files"]["tmp_name"][$f], $path.$name))
		// 		        $count++; // Number of successfully uploaded file
		// 		    }
		// 		}
		// 	}
		// } // end of screenshots
    } // end of else --- project is not exists and added
} // end check for errors
?>


<?php get_header(); ?>
<header class="page-header">
<div class="container">
  <div class="row">
    <div class="col-md-7">
      <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    </div>
    <div class="col-md-5 hidden-xs">
      <?php if (function_exists('template_breadcrumbs')) template_breadcrumbs(); ?>
    </div>
  </div>
</div>
</header><!-- .entry-header -->

<div class="container">
	<div class="row">
	  <div id="primary" class="content-area col-md-12">
		  <main id="main" class="site-main" role="main">

<body>
  <div id="content" role="main">
    <div class="new-coupon-form">
      <form id="add_product" name="add_product" method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field( 'add-product' ); ?>
        <div class="required">
          <?php
            global $productCreated, $errorMessages;
            if ( $productCreated ) { ?>
              <div class="alert alert-success"><i class="fa fa-check"></i> <?php echo $productCreated; ?></div>
              <div class="clearfix"></div>
              <?php }
            if ( $errorMessages != '') { ?>
              <div class="alert alert-danger">
                <i class="fa fa-warning"></i>
                <?php echo $errorMessages; ?>
              </div>
              <div class="clearfix"></div>
          <?php } ?>
        </div>
        <?php include(locate_template('template-parts/add-form-intro.php')); ?>
        <div class="form-group row">
        	<div class="col-md-12">
            <label for="product_title" class="label">
              <?php _e( 'Title', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
            </label>
            <input class="form-control" type="text" id="product_title" value="" name="product_title" />
        	</div>
        </div>

        <div class="form-group row">
          <div class="col-md-12">
            <label for="description" class="label">
              <?php _e( 'Description', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
            </label>
            <textarea class="form-control" name="description" id="description" rows="3" placeholder="<?php _e( 'Type product description here', 'egyptfoss' ); ?>"></textarea>
          </div>
        </div>

        <div class="form-group row">
            <div class="col-md-12">
              <?php
              	/*Retrieving the image*/
              	// $attachment = get_post_meta($product_id, 'product_logo');
              	// if($attachment[0]!=''){
              	// 	echo wp_get_attachment_link($attachment[0], 'product_logo', false, false);
              	// }
              ?>
              <label for="" class="label">
                <?php _e( 'Product Logo', 'egyptfoss' ); ?>
              </label>
                <div class="input-group image-preview">
                        <input type="text" class="form-control image-preview-filename" disabled="disabled"> <!-- don't give a name === doesn't send on POST/GET -->
                        <span class="input-group-btn">
                                <!-- image-preview-clear button -->
                                <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                                        <span class="icons icon-cancel"></span> <?php _e('Clear','egyptfoss'); ?>
                                </button>
                                <!-- image-preview-input -->
                                <div class="btn btn-default image-preview-input">
                                        <span class="icons icon-folder-open"></span>
                                        <span class="image-preview-input-title"><?php _e("Browse","egyptfoss"); ?></span>
                                        <input type="file" accept="image/png, image/jpeg, image/gif" name="product_logo" id="product_logo" title="<?php _e( 'please enter correct image type', 'egyptfoss' ); ?>"/>
                                        <?php wp_nonce_field( 'product_logo', 'product_logo_nonce' ); ?>
                                </div>
                        </span>
                </div>
                <div id="product_image_error"></div>
            </div>
        </div>

        <div class="form-group row">
          <div class="col-md-12 screenshot-uploader">
            <label for="product_title" class="label"><?php _e( 'Product Screenshots', 'egyptfoss' ); ?></label>
            <input type="file" style="visibility:hidden;" name="files[]" id="filer_input" multiple="multiple" accept="image/*">
            <?php wp_nonce_field( 'product_screenshots', 'product_screenshots_nonce' ); ?>
          </div>
        </div>

        <div class="form-group row">
        	<div class="col-md-12">
  					<label for="developer" class="label">
  						<?php _e( 'Developer', 'egyptfoss' ); ?>
  					</label>
            <input class="form-control" type="text" id="developer" value="" name="developer" />
        	</div>
        </div>

        <div class="form-group row">
          <div class="col-md-12">
            <label for="functionality" class="label"><?php _e( 'Functionality', 'egyptfoss' ); ?></label>
            <textarea class="form-control" name="functionality" id="functionality" rows="3" placeholder="<?php _e( 'Type product functionality here', 'egyptfoss' ); ?>"></textarea>
          </div>
        </div>

        <div class="form-group row">
        	<div class="col-md-12">
            <?php
              $taxonomy = 'industry';
              $tax_terms = get_terms($taxonomy, array('hide_empty' => false));
            ?>
            <label for="post_industry" class="label"><?php _e( 'Category', 'egyptfoss' ); ?></label>
            <select class="form-control" id="post_industry" name="post_industry" style="width:100%; ">
              <optgroup>
                <option value="" selected disabled><?php _e( 'Select', 'egyptfoss' ); ?></option>
                <?php
                  $industries = get_terms( 'industry', array( 'hide_empty' => 0, 'parent' => 0 ) );
                  
                  foreach ($industries as $key => $industry) {
                    printf(
                      '<option value="%1$s" %2$s disabled>%3$s</option>',       
                      $industry->slug,
                      selected( $industry->slug, $industryFromGet ),
                      __( $industry->name, 'egyptfoss' )
                    );

                    // get child
                    $subterms = get_terms( industry, array( 'parent' => $industry->term_id, 'hide_empty' => false ) );

                    foreach ( $subterms as $subterm ) {
                      printf(
                        '<option value="%1$s" %2$s>%3$s</option>',       
                        $subterm->slug,
                        selected( $subterm->slug, $industryFromGet ),
                        '&nbsp;&nbsp;&nbsp;&nbsp;— '. __( $subterm->name, 'egyptfoss' )
                      );
                    }
                  }
                ?>
              </optgroup>
            </select>
        	</div>
        </div>

        <div class="form-group row">
          <div class="col-md-12">
            <label for="usage_hints" class="label"><?php _e( 'Usage hints', 'egyptfoss' ); ?></label>
            <textarea class="form-control" name="usage_hints" id="usage_hints" rows="3" placeholder="<?php _e( 'Type product usage hints here', 'egyptfoss' ); ?>"></textarea>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-12">
            <label for="references" class="label"><?php _e( 'References', 'egyptfoss' ); ?></label>
            <textarea class="form-control" name="references" id="references" rows="3" placeholder="<?php _e( 'Type product references here', 'egyptfoss' ); ?>"></textarea>
          </div>
        </div>

        <div class="form-group row">
        	<div class="col-md-12">
            <label for="link_to_source" class="label"><?php _e( 'Link to source', 'egyptfoss' ); ?></label>
            <input class="form-control" type="text" id="link_to_source" value="" name="link_to_source" placeholder="<?php _e( 'http://www.example.com', 'egyptfoss' ); ?>"/>
        	</div>
        </div>

       <?php
            global $ef_multi_add_edit_product_taxs;
            foreach($ef_multi_add_edit_product_taxs as $taxonomy)
            {
            ?>
            <div class="form-group row string post_<?php echo $taxonomy ?>">
                <div class="col-md-12">
                    <label for="post_<?php echo $taxonomy ?>" class="label"><?php _e(ucfirst($taxonomy), 'egyptfoss' ); ?></label>
                    <select 
                        <?php
                        global $ef_product_multi_uncreated_tax;
                        if(!in_array($taxonomy, $ef_product_multi_uncreated_tax) ){ ?>data-tags="true" <?php } ?>
                        class="add-product-tax form-control L-validate_taxonomy" 
                        id="post_<?php echo $taxonomy ?>" 
                        name="post_<?php echo $taxonomy ?>[]" 
                        data-placeholder="<?php _e( 'Select', 'egyptfoss' ); ?>" 
                        style="width:100%; visibility: hidden;" 
                        multiple="multiple"> 
                        <optgroup>
                            <?php
                                $post_taxonomies = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );
                                foreach ($post_taxonomies as $post_tax) {
                                    echo("<option value='".$post_tax->slug."'>");
                                    _e("$post_tax->name", "egyptfoss");
                                    echo ("</option>");
                                }
                            ?>
                    </optgroup>
                    </select>
                </div>
            </div>
            <?php } ?>

        <div class="form-group row">
          <div class="col-md-12">
            <input type="submit" class="btn btn-primary rfloat" value="<?php _e("Add","egyptfoss") ?>" tabindex="40" id="submit" name="submit" />
          </div>
        </div>
        <input type="hidden" name="postid" value="<?php echo $post_to_edit->ID; ?>" />
        <input type="hidden" name="action" value="add_product" />
      </form>
    </div><!-- .entry-content -->
  </div><!-- #post-## -->
</div><!-- #content -->
</main><!-- #main -->
	</div><!-- #primary -->
	</div>
</div>
<script>
  jQuery(document).ready(function ($) {
      $.getScript( "<?php echo get_template_directory_uri();?>/js/filer-template.js", function() {
      $('#filer_input').filer({
          changeInput: changeInput,
          showThumbs: true,
          theme: "dragdropbox",
          templates: {
              box: template_box,
              item: template_item,
              itemAppend: template_append,
              progressBar: '<div class="bar"></div>',
              itemAppendToEnd: false,
              removeConfirmation: false,
              extensions: ['jpg', 'jpeg', 'png'],
              _selectors: {
                  list: '.jFiler-items-list',
                  item: '.jFiler-item',
                  progressBar: '.bar',
                  remove: '.jFiler-item-trash-action'
              }
          },
          dragDrop: {
              dragEnter: null,
              dragLeave: null,
              drop: null,
          },
          captions: {
              button: "Choose Files",
              feedback: "Choose files To Upload",
              feedback2: "files were chosen",
              drop: "Drop file here to Upload",
              removeConfirmation: "<?php echo _e("Are you sure you want to delete this?", "buddypress"); ?>",
              errors: {
                  filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
                  filesType: "Only Images are allowed to be uploaded.",
                  filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
                  filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
              }
          },        
          addMore: true
      });
      });
  });
</script>
<?php get_footer(); ?>
<?php } ?> <?php /* user is logged in */ ?>
