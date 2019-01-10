<?php

/**
 * Template Name: Add Resources Open DataSet
 *
 * @package egyptfoss
 */


if ( !is_user_logged_in() ) {
  $current_url = home_url( pll_current_language()."/login/?redirected=addresourcesopendataset&redirect_to=".get_current_lang_page_by_template("template-add-resources-open-dataset.php")."?did=".$_GET['did'] );
  wp_redirect( home_url( get_current_lang_page_by_template('template-login.php')."?redirected=addresourcesopendataset&redirect_to={$current_url}" ) );
  exit;
} else if (!current_user_can('add_new_ef_posts')) {
  //wp_redirect( home_url( '?action=unauthorized' ) );
  wp_redirect(home_url('/?status=403'));
  exit;
}

$dataset_id = -1;
if(is_numeric($_GET["did"]))
{
    $dataset_id = $_GET["did"];
}

if (!empty($_POST['action']) && $_POST['action'] == "add_resources_open_dataset" && isset($_POST['postid'])) {
  if($_POST['postid'] == -1 || !is_numeric($_POST['postid']))
  {
      echo "No Open dataset Found !";
      exit;
  }
    
  //check existance
  global $wpdb;
  $sql = "select ID from $wpdb->posts where  post_type='open_dataset' and (post_status='publish') and ID = %s";
  $checkExistance = $wpdb->get_col($wpdb->prepare($sql, $_POST['postid']));
  if (!$checkExistance) {
      echo "No Open dataset Found !";
      exit;
  }
  
  
  $nonce = $_REQUEST['_wpnonce'];
  if ( ! wp_verify_nonce( $nonce, 'add-resources-open-dataset' ) ) {
    wp_redirect( home_url( '/?status=403' ) );
    exit;
  }
  $open_dataset_id = ef_add_resrouces_open_dataset_front_end($_POST['postid']);
  $ef_open_dataset_messages = get_query_var("ef_open_dataset_messages");
  if(!isset($ef_open_dataset_messages['errors'])) {
    $post = get_post( $dataset_id );
    wp_redirect( site_url() .'/'. pll_current_language() .'/open-datasets/'. $post->post_name );
  }
}
get_header(); 
 ?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <h1 class="entry-title">
          <?php echo the_title(); //echo __("Add","egyptfoss").' '; echo _n("Open Dataset","Open Datasets",1,"egyptfoss").' ';  echo __("Resources","egyptfoss"); ?></h1>
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
        <form id="add_resources_open_dataset" name="add_resources_open_dataset" method="post" action="" enctype="multipart/form-data">
          <div class="required">
            <?php
            $ef_open_dataset_messages = get_query_var("ef_open_dataset_messages");
            $open_dataset_description = "";
            if(isset($ef_open_dataset_messages['errors'])) {
              $open_dataset_description = (isset($_POST['open_dataset_description']))?$_POST['open_dataset_description']:"";  
            ?>
            <div class="alert alert-danger"><?php
            foreach($ef_open_dataset_messages['errors'] as $error ) {
              echo "<i class='fa fa-warning'></i> " . $error . "<br/>";
            }
            ?>
            </div>
            <?php }
            if(isset($ef_open_dataset_messages['success'])) { ?>
            <div class="alert alert-success"><?php
              foreach($ef_open_dataset_messages['success'] as $success ) {
              echo "<i class='fa fa-check'></i> " . $success . "<br/>";
            } ?>
            </div>
            <?php }
            set_query_var("ef_open_dataset_messages", array()); ?>
          </div>
            <input type="hidden" id="names" name="names" value="">
            <input type="hidden" id="sizes" name="sizes" value="">

            <div class="form-group row">
              <div class="col-md-12 screenshot-uploader">
                <label for="open_dataset_resources" class="label"><?php _e( 'Resources', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
                <input type="file" style="visibility:hidden;" name="open_dataset_resources[]" id="open_dataset_resources" multiple="multiple">
                <?php wp_nonce_field( 'open_dataset_resources', 'open_dataset_resources_nonce' ); ?>
                <div id="open_dataset_resources_validate"></div>
                <div class="upload-hint alert alert-warning">
                    <?php echo _e("HINT:","egyptfoss"). _e("Max Allowed Size per file: 20MB","egyptfoss")._e(" & ","egyptfoss")._e("Allowed formats : ","egyptfoss").implode(',',$extensions); ?>                 </div>
              </div>
            </div>            
          <div class="form-group row">
            <div class="col-md-12">
              <label for="description" class="label">
                <?php _e('Description', 'egyptfoss'); ?> <?php _e('(required)', 'egyptfoss'); ?>
              </label>
              <textarea rows="15" class="form-control" name="open_dataset_description" id="open_dataset_description"><?php echo $open_dataset_description; ?></textarea>
              <div id="open_dataset_description_validate"></div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-12">
              <input type="submit" class="btn btn-primary rfloat" value="<?php echo __("Save","egyptfoss"); ?>" tabindex="40" id="submit" name="submit" />
            </div>
          </div>
          <input type="hidden" name="postid" value="<?php echo $dataset_id; ?>" />
          <input type="hidden" name="action" value="add_resources_open_dataset" />
          <?php wp_nonce_field( 'add-resources-open-dataset' ); ?>
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
      $("#names").val('');
      $("#sizes").val('');
      $('#open_dataset_resources').filer({
          changeInput: changeInput,
          showThumbs: true,
          theme: "dragdropbox",
          onSelect: function(e){
              var newName = $("#names").val() +"|"+e.name;
              var newSize = $("#sizes").val() +"|"+e.size;
              $("#names").val(newName);
              $("#sizes").val(newSize);
            },
            onRemove: function(e,data)
            {
                var newName = $("#names").val().replace("|"+data.name,'');
                var newSize = $("#sizes").val().replace("|"+data.size,'');
                $("#names").val(newName);
                $("#sizes").val(newSize);
            },
          templates: {
              box: template_box,
              item: template_item,
              itemAppend: template_append,
              progressBar: '<div class="bar"></div>',
              itemAppendToEnd: false,
              removeConfirmation: false,
              extensions: ['jpg', 'jpeg', 'png','gif','xlsx','xlx','csv'],
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
<?php get_footer(); 
?>
