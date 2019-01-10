# Remove FossPedia and Foss Map from English Menu
SET @main_nav = (SELECT term_id FROM wpRuvF8_terms where name = 'main_navigation' limit 1);
SET @nav_menu_tax_en = (SELECT term_id FROM wpRuvF8_term_taxonomy where term_id = (select @main_nav) and taxonomy = 'nav_menu' limit 1);

SET @fosspediaPageId = (SELECT ID FROM wpRuvF8_posts where post_name = 'fosspedia' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @fosspediaPageId) and term_taxonomy_id = (select @nav_menu_tax_en);
delete from `wpRuvF8_postmeta` where post_id = (select @fosspediaPageId);
delete from `wpRuvF8_posts` where ID = (select @fosspediaPageId);


SET @fossmapPageId = (SELECT ID FROM wpRuvF8_posts where post_title = 'FOSS Map' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @fossmapPageId) and term_taxonomy_id = (select @nav_menu_tax_en);
delete from `wpRuvF8_postmeta` where post_id = (select @fossmapPageId);
delete from `wpRuvF8_posts` where ID = (select @fossmapPageId);

# Remove FossPedia and Foss Map from Arabic Menu
SET @main_nav_ar = (SELECT term_id FROM wpRuvF8_terms where name = 'main_navigation_ar' limit 1);
SET @nav_menu_tax_ar = (SELECT term_id FROM wpRuvF8_term_taxonomy where term_id = (select @main_nav_ar) and taxonomy = 'nav_menu' limit 1);

SET @fosspediaPageId_ar = (SELECT ID FROM wpRuvF8_posts where post_name = 'fosspedia_ar' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @fosspediaPageId_ar) and term_taxonomy_id = (select @nav_menu_tax_ar);
delete from `wpRuvF8_postmeta` where post_id = (select @fosspediaPageId_ar);
delete from `wpRuvF8_posts` where ID = (select @fosspediaPageId_ar);


SET @fossmapPageId_ar = (SELECT ID FROM wpRuvF8_posts where post_title = 'الخريطة' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @fossmapPageId_ar) and term_taxonomy_id = (select @nav_menu_tax_ar);
delete from `wpRuvF8_postmeta` where post_id = (select @fossmapPageId_ar);
delete from `wpRuvF8_posts` where ID = (select @fossmapPageId_ar);

# Insert new menu english and arabic
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','Success Stories','','publish','closed','closed','','success_story','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'3','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/success-stories');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','قصص النجاح','','publish','closed','closed','','ar-success_story','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'3','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/success-stories');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','Open Datasets','','publish','closed','closed','','open_dataset','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'6','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/open-datasets');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','قواعد بيانات مفتوحة','','publish','closed','closed','','ar-open_dataset','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'6','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/open-datasets');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');