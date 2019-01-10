<?php
/**
 * Template Name: View Document.
 *
 * @package egyptfoss
 */
get_header(); ?>

<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-12">
	 				<h1>
					Untitled Document
					</h1>
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->

<div class="container">
<div class="row">
	<div class="col-md-12">
		<div class="ui-options-buttons text-right">
			<a href="" class="btn btn-light">
				<i class="fa fa-pencil"></i>
				Edit
			</a>
			<a href="" class="btn btn-light">
				<i class="fa fa-user-plus"></i>
				Invite
			</a>
		</div>
	</div>
</div>
<div class="row">
   	<div class="col-md-12">
   				<ul class="list-group edit-doc-list">
    <li class="list-group-item clearfix">
    <strong class="col-md-2">Document Status</strong>
    	<div class="form-group">
    	<div class="col-md-4">
    		Published
    	</div>
    	</div>
    </li>
    <li class="list-group-item clearfix">
    	<div class="form-group">
    		<strong class="col-md-2">Related to</strong>
    		<div class="col-md-4">
    		News
    		</div>
    	</div>
    </li>
    <li class="list-group-item clearfix">
    <strong class="col-md-2">Latest Revision</strong>
    <div class="col-md-7">
    	14/12/2015 - 12:30 P.M
    	<a href="#">View Revisions</a>
    </div>
    </li>
  </ul>

   	</div>
   </div>
	<div class="row">
  <div  class="spaces-listing col-md-9">
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
    </div>
        <div class="col-md-3 docs-sidebar">
<h3>Document Info</h3>
<ul class="basic-info-box list-group">
  <li class="list-group-item">
    <strong>Created</strong>
    <br>
   14/12/2015 - 12:30 P.M
  </li>
  <li class="list-group-item">
    <strong>Latest Revision</strong>
    <br>
    14/12/2015 - 12:30 P.M
    <br>
    <a href="#" data-toggle="modal" data-target="#revisions">View Revisions</a>
    						<!--- Invite Modal -->
						<div class="modal fade" id="revisions" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Revisions</h4>
        </div>
        <div class="modal-body">

	  <div class="user-row">
	  	<div class="avatar">
	  		<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="" />
	  	</div>
			<div class="user-name">Ahmed Badran</div>
			<div class="doc-status">Published</div>
			<div class="doc-date">
				<small class="date">14/12/2016 - 11:33pm</small>
			</div>
	  </div>
		<div class="user-row">
			<div class="avatar">
				<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="" />
			</div>
			<div class="user-name">Ahmed Badran</div>
			<div class="doc-status">Pending Review</div>
			<div class="doc-date">
				<small class="date">14/12/2016 - 11:33pm</small>
			</div>
		</div>
		<div class="user-row">
			<div class="avatar">
				<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="" />
			</div>
			<div class="user-name">Ahmed Badran</div>
			<div class="doc-status">Editor</div>
			<div class="doc-date">
				<small class="date">14/12/2016 - 11:33pm</small>
			</div>
		</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
						<!--- End --->
  </li>
   <li class="list-group-item">
    <strong>Status</strong>
    <br>
   <select name="" class="form-control input-sm">
   	<option value="">Published</option>
   </select>
</ul>
    </div>
    </div><!-- #primary -->
	</div>
</div>

<?php get_footer();?>
