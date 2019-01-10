SET @eventNavPageId = (SELECT ID FROM wpRuvF8_posts where post_name = 'events_ar' and post_type = 'nav_menu_item'  limit 1);
update  `wpRuvF8_posts` set post_title = 'الفعاليات' where ID = (select @eventNavPageId);

SET @eventNavPageId = (SELECT ID FROM wpRuvF8_posts where post_title = 'الخرائط' and post_type = 'nav_menu_item'  limit 1);
update  `wpRuvF8_posts` set post_title = 'الخريطة' where ID = (select @eventNavPageId);

SET @eventNavPageId = (SELECT ID FROM wpRuvF8_posts where post_name = '%d8%a7%d9%84%d8%a7%d8%ae%d8%a8%d8%a7%d8%b1' and post_type = 'nav_menu_item'  limit 1);
update  `wpRuvF8_posts` set post_title = 'الأخبار' where ID = (select @eventNavPageId);

SET @eventNavPageId = (SELECT ID FROM wpRuvF8_posts where post_title = 'Maps' and post_type = 'nav_menu_item'  limit 1);
update  `wpRuvF8_posts` set post_title = 'FOSS Map' where ID = (select @eventNavPageId);

SET @main_nav = (SELECT term_id FROM wpRuvF8_terms where name = 'main_navigation' limit 1);
SET @nav_menu_tax_en = (SELECT term_id FROM wpRuvF8_term_taxonomy where term_id = (select @main_nav) and taxonomy = 'nav_menu' limit 1);

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','Home','','publish','closed','closed','','','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'0','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @main_nav_ar = (SELECT term_id FROM wpRuvF8_terms where name = 'main_navigation_ar' limit 1);
SET @nav_menu_tax_ar = (SELECT term_id FROM wpRuvF8_term_taxonomy where term_id = (select @main_nav_ar) and taxonomy = 'nav_menu' limit 1);

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','الرئيسية','','publish','closed','closed','','','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'0','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

