SET @eventNavPageId = (SELECT ID FROM wpRuvF8_posts where post_name = 'fosspedia_ar' and post_type = 'nav_menu_item'  limit 1);
update  `wpRuvF8_posts` set post_title = 'مركز المعرفة' where ID = (select @eventNavPageId);
