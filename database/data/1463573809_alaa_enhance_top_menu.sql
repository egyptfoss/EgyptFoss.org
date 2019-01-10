SET @main_nav = (SELECT term_id FROM wpRuvF8_terms where name = 'main_navigation' limit 1);
SET @nav_menu_tax_en = (SELECT term_id FROM wpRuvF8_term_taxonomy where term_id = (select @main_nav) and taxonomy = 'nav_menu' limit 1);

SET @main_nav_ar = (SELECT term_id FROM wpRuvF8_terms where name = 'main_navigation_ar' limit 1);
SET @nav_menu_tax_ar = (SELECT term_id FROM wpRuvF8_term_taxonomy where term_id = (select @main_nav_ar) and taxonomy = 'nav_menu' limit 1);

# REMOVE HOME ITEM
SET @homePageId = (SELECT ID FROM wpRuvF8_posts where post_title = 'Home' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @homePageId) and term_taxonomy_id = (select @nav_menu_tax_en);
delete from `wpRuvF8_postmeta` where post_id = (select @homePageId);
delete from `wpRuvF8_posts` where ID = (select @homePageId);

SET @homePageId_ar = (SELECT ID FROM wpRuvF8_posts where post_title = 'الرئيسية' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @homePageId_ar) and term_taxonomy_id = (select @nav_menu_tax_ar);
delete from `wpRuvF8_postmeta` where post_id = (select @homePageId_ar);
delete from `wpRuvF8_posts` where ID = (select @homePageId_ar);

# ADD FOSSPEDIA
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','FOSSPedia','','publish','closed','closed','','fosspedia','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'7','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/wiki/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');


SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','مركز المعرفة','','publish','closed','closed','','fosspedia_ar','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'7','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/wiki/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

# ADD FOSS MAP
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','FOSS Map','','publish','closed','closed','','maps','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'8','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/maps/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','الخريطة','','publish','closed','closed','','maps','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'8','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/maps/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');