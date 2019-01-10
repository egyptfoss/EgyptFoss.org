<?php
global $ef_collaboration_item_roles;
$user_id = get_query_var('ef_current_user_invite_id');
$role = $ef_collaboration_item_roles[get_query_var('ef_current_user_invite_role')];
?>
<div class="user-row remove-row-<?php echo $user_id; ?>" >
	<div class="avatar"><?php echo get_avatar( $user_id, 32 ); ?></div>
	<div class="user-name">
		<a href="<?php echo home_url()."/members/".bp_core_get_username($user_id).'/about/' ?>"><?php echo bp_core_get_user_displayname($user_id); ?></a>
	</div>
	<div class="user-role"><?php echo _x("$role","indefinite","egyptfoss") ?></div>
	<div class="actions"><a href="#" onclick="removeInvitedUser('remove-row-<?php echo $user_id; ?>','<?php echo $user_id; ?>', '<?php echo get_query_var('ef_current_user_invite_role'); ?>'); return false;" id="invited_user_remove" class="remove-icon"><i class="fa fa-remove"></i></a></div>
</div>