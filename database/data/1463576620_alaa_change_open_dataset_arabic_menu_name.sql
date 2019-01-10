SET @NavPageId = (SELECT ID FROM wpRuvF8_posts where post_name = 'ar-open_dataset' and post_type = 'nav_menu_item'  limit 1);
update  `wpRuvF8_posts` set post_title = 'البيانات المفتوحة' where ID = (select @NavPageId);
