<!--- INVITE MODEL -->
<div class="modal fade" id="invite-space-document" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">
            "<span id="share-item-title"></span>" <?php echo __("Sharing Settings", "egyptfoss"); ?>
        </h4>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger error-msg hide" id="error-msg">
        </div>
        <div class="alert alert-success success-msg hide" id="success-msg">
        </div>                      
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" class="invite_users_groups_tab" href="#invite-users"><?php echo __("Users", "egyptfoss"); ?></a></li>
          <li><a data-toggle="tab" class="invite_users_groups_tab" href="#invite-groups"><?php echo __("Groups", "egyptfoss"); ?></a></li>
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
                <!--<input type="text" name="user_email" id="user_email" value="" class="form-control input-sm" placeholder="<?php echo __("Type user display name here", "egyptfoss"); ?>"> -->
                <select style="width:100%" id="user_email" class="custom-select2 user_email form-control input-sm">
                   <!-- <option value="-1"><?php echo __("Type user display name here", 'egyptfoss') ?></option> -->
                </select>
              </div>
              <div class="user-role">
                <?php global $ef_collaboration_item_roles; ?>
                <select style="width:100%" id="user_roles" class="form-control" name="">
                  <?php foreach ($ef_collaboration_item_roles as $role) { ?>
                    <option value="<?php echo array_search($role, $ef_collaboration_item_roles) ?>"><?php echo _x("$role", 'indefinite', "egyptfoss"); ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="actions">
                <a href="#" id="user_email_add"><i class="fa fa-plus"></i></a>
              </div>
            </div>
            <div id="list_invited_users">

            </div>
            <div class="text-center">
              <i class='fa fa-circle-o-notch hidden fa-spin invited-users-spinner'></i> 
            </div>

          </div>
          <div id="invite-groups" class="tab-pane fade">
            <div class="loading-overlay loading_invite_groups hidden" style="width:95%">
                   <div class="spinner">
                       <div class="double-bounce1"></div>
                       <div class="double-bounce2"></div>
                   </div>
             </div>
            <div id="invite_groups_container">
             <?php include(locate_template('CollaborationCenter/template-parts/inviteModalGroupContent.php')); ?>
            </div>  
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <i class='fa fa-circle-o-notch hidden fa-spin invite-group-spinner'></i> 
        <button type="button" class="btn btn-light" name="cancel" data-dismiss="modal"><?php echo __("Cancel", "egyptfoss"); ?></button>
        <button type="button" class="btn btn-primary" name="button" id="save_invited" data-locale="<?php echo get_locale(); ?>"><?php echo __("Save", "egyptfoss"); ?></button>
      </div>
    </div>
  </div>
</div>  
<!-- END OF INVITE MODEL -->
