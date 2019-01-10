SET @enPostId = (SELECT ID from wpRuvF8_posts WHERE post_title = 'Edit Thought');
SET @arPostId = (SELECT ID from wpRuvF8_posts WHERE post_title = 'تعديل رأي');

UPDATE `wpRuvF8_posts`
SET post_name = 'edit-expert-thought'
WHERE ID = (SELECT @enPostId);

UPDATE `wpRuvF8_posts`
SET post_name = 'ar-edit-expert-thought'
WHERE ID = (SELECT @arPostId);

UPDATE `wpRuvF8_postmeta`
SET `meta_value` = 'template-edit-expert-thought.php'
WHERE `post_id` = (SELECT @enPostId) AND `meta_key` = '_wp_page_template'