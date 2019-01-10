<?php
/**
 * Template Name: Listing Spaces.
 *
 * @package egyptfoss
 */
get_header(); ?>

<header class="page-header">
	<div class="container">
	 	<div class="row">
	 		<div class="col-md-12">
	 				<h1>
					My Shared Docs and Spaces
					</h1>
	 		</div>
	 	</div>
	</div>
</header><!-- .entry-header -->

<div class="container">
	<div class="row">
    <div class="col-md-3 docs-sidebar">
			<ul class="side-nav-items">
				<li class="active">
				<a href="#"><i class="fa fa-folder"></i> My Docs</a>
				</li>
				<li>
				<a href="#"><i class="fa fa-users"></i> Shared</a>
				</li>
			</ul>
    </div>
    <div  class="spaces-listing col-md-9">
			<div class="nano">
				<div class="nano-content">
					<div class="document-row clearfix">
						<div class="icon">
							<i class="fa fa-folder"></i>
						</div>
						<div class="name">
							<h4><a href="#">My Designs Space</a></h4>
						</div>
						<div class="options">
							<a href="#"><i class="fa fa-pencil"></i> Rename</a>
							<a href="#"><i class="fa fa-user-plus"></i>Invite</a>
						</div>
					</div>
					<div class="document-row clearfix">
						<div class="icon">
							<i class="fa fa-folder"></i>
						</div>
						<div class="name">
							<h4><a href="#">My Designs Space</a></h4>
						</div>
						<div class="options">
							<a href="#" data-toggle="modal" data-target=".rename-space"><i class="fa fa-pencil"></i> Rename</a>
							<a href="#" data-toggle="modal" data-target="#invite"><i class="fa fa-user-plus"></i>Invite</a>
						</div>
						<!--- Invite Modal -->
						<div class="modal fade" id="invite" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Sharing Settings</h4>
        </div>
        <div class="modal-body">
					<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#invite-users">Users</a></li>
	  <li><a data-toggle="tab" href="#invite-groups">Groups</a></li>
	</ul>

	<div class="tab-content invitation-box">
	  <div id="invite-users" class="tab-pane fade in active">
			<div class="user-row add-form">
		  	<div class="avatar">
		  		<div class="empty-avatar">
		  			<i class="fa fa-user-plus"></i>
		  		</div>
		  	</div>
				<div class="user-name">
					<input type="text" name="name" value="" class="form-control input-sm" placeholder="Type user email here">
				</div>
				<div class="user-role">
					<select class="form-control input-sm" name="">
						<option value="">Editor</option>
						<option value="">Owner</option>
					</select>
				</div>
				<div class="actions">
					<a href="#"><i class="fa fa-plus"></i></a>
				</div>
		  </div>
	  <div class="user-row">
	  	<div class="avatar">
	  		<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="" />
	  	</div>
			<div class="user-name">Ahmed Badran</div>
			<div class="user-role">Editor</div>
			<div class="actions"><a href="#" class="remove-icon"><i class="fa fa-remove"></i></a></div>
	  </div>
		<div class="user-row">
			<div class="avatar">
				<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="" />
			</div>
			<div class="user-name">Ahmed Badran</div>
			<div class="user-role">Editor</div>
			<div class="actions"><a href="#" class="remove-icon"><i class="fa fa-remove"></i></a></div>
		</div>
		<div class="user-row">
			<div class="avatar">
				<img src="<?php echo get_template_directory_uri(); ?>/demo-assets/user-avatar.jpg" alt="" />
			</div>
			<div class="user-name">Ahmed Badran</div>
			<div class="user-role">Editor</div>
			<div class="actions"><a href="#" class="remove-icon"><i class="fa fa-remove"></i></a></div>
		</div>
	  </div>
	  <div id="invite-groups" class="tab-pane fade">
	    <form class="form">
	    	<div class="form-group row">
					<div class="col-md-12">
							<label for="" class="label">Type</label>
							<label class="radio-inline">
		  <input type="radio" name="individual" id="inlineRadio1" value="option1"> Individual
		</label>
		<label class="radio-inline">
		  <input type="radio" name="entity" id="inlineRadio2" value="option2"> Entity
		</label>
					</div>
	    	</div>
				<div class="form-group row">
					<div class="col-md-12">
						<label for="" class="label">Sub Type</label>
						<select name="" id="" class="form-control input-sm">
							<option value="">Business Owner</option>
						</select>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<label for="" class="label">Interests</label>
						<input type="text" class="form-control input-sm">
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<label for="" class="label">Technologies</label>
						<input type="text" class="form-control input-sm">
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<label for="" class="label">Industry</label>
						<input type="text" class="form-control input-sm">
					</div>
				</div>
	    </form>
	  </div>

	</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary" name="button">Save</button>
        </div>
      </div>
    </div>
  </div>
						<!--- End --->
					</div>
					<div class="document-row clearfix">
						<div class="icon">
							<i class="fa fa-file"></i>
						</div>
						<div class="name">
							<h4><a href="#" data-toggle="modal" data-target="#view-document">eSpace Logo new</a></h4>
						</div>
						<div class="options">
							<a href="#" data-toggle="modal" data-target="#rename-space"><i class="fa fa-pencil"></i> Rename</a>
							<a href="#"><i class="fa fa-user-plus"></i>Invite</a>
						</div>
							<!-- Rename Modal --->
						<div class="modal fade" id="rename-space" tabindex="-1" role="dialog">
					  <div class="modal-dialog modal-sm">
					    <div class="modal-content">
								<div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					        <h4 class="modal-title">Rename</h4>
					      </div>
					      <div class="modal-body">
									<div class="row form-group">
										<div class="col-md-12">
										<input class="form-control" type="text" name="name" value="My Designs Space" autofocus="true">
										</div>
									</div>
									<div class="row form-group text-right">
										<div class="col-md-12">
											<button type="button" class="btn btn-light" name="button">Cancel</button>
											<button type="button" class="btn btn-primary" name="button">Save</button>
										</div>
									</div>
					      </div>
					    </div>
					  </div>
					</div>
						<!-- Rename Modal End --->
					</div>
				</div>
			</div>
    </div>
    </div><!-- #primary -->
	</div>
</div>

<?php get_footer();?>
