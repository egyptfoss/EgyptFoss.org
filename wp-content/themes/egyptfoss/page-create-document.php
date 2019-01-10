<?php
/**
 * Template Name: Create Document.
 *
 * @package egyptfoss
 */
get_header(); ?>

<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-12">
	 				<input type="text" readonly class="doc-live-title" value="Untitled Document">
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->

<div class="container">
<div id="create-doc">
		<div class="row">
  <div  class="spaces-listing col-md-12">
  <div class="form-group">
  <label for="" class="label">Document Title</label>
  <input type="text" class="form-control doc-name" placeholder="Type Document Name">
  </div>
  <div class="editor-box">
<?php
// default settings
$content = 'This content gets loaded first.';
$editor_id = 'kv_frontend_editor';
$settings =   array(
    'wpautop' => true,
    'media_buttons' => true,
    'textarea_name' => $editor_id,
    'textarea_rows' => get_option('default_post_edit_rows', 10),
    'tabindex' => '',
    'editor_css' => '',
    'editor_class' => '',
    'teeny' => false,
    'dfw' => false,
    'tinymce' => true,
    'quicktags' => true
);
		wp_editor( $content, $editor_id, $settings = array() ); ?>
  </div>
     <div class="form-group">
 <button class="btn btn-primary rfloat">Save</button>
  </div>
    </div>
    </div><!-- #primary -->
</div>
	</div>
</div>

<?php get_footer();?>
