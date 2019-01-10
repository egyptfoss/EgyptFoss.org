<?php
// get all inputs required for email
$template_inputs = unserialize(get_query_var('template_inputs'));
set_query_var('template_inputs', serialize(array()));
$language = '';
if(isset($template_inputs["language"]))
{
  $language = $template_inputs["language"];
}
?>
<table style="width:95%;margin:auto;<?php if ($language == "ar") {
              echo 'direction:rtl';
            } ?>" cellspacing="0">
    <tr>
<?php
include(locate_template("mail-templates/message-body.php"));
?>
</tr>
</table>