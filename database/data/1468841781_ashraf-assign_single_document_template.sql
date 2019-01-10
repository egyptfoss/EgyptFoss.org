
SET @PageId = (SELECT ID FROM wpRuvF8_posts where post_name = 'view-document' and post_type = 'page' limit 1);
update  `wpRuvF8_postmeta` set meta_value = 'CollaborationCenter/template-single-document.php' where post_id = (select @PageId) and meta_key = "_wp_page_template";


SET @PageId = (SELECT ID FROM wpRuvF8_posts where post_name = 'view-document-ar' and post_type = 'page' limit 1);
update  `wpRuvF8_postmeta` set meta_value = 'CollaborationCenter/template-single-document.php' where post_id = (select @PageId) and meta_key = "_wp_page_template";