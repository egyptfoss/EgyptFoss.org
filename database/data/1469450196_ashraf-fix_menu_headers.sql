SET @main_nav = (SELECT term_id FROM wpRuvF8_terms where name = 'main_navigation' limit 1);
SET @nav_menu_tax_en = (SELECT term_id FROM wpRuvF8_term_taxonomy where term_id = (select @main_nav) and taxonomy = 'nav_menu' limit 1);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'news' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_en);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'events' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_en);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'products' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_en);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'fosspedia' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_en);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'open-dataset' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_en);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'success-story' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_en);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'fossmap' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_en);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'request-center' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_en);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'collaboration-center' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_en);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'News','','publish','closed','closed','','news','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'1','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/news/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'Events','','publish','closed','closed','','events','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'2','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/events/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'Products','','publish','closed','closed','','products','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'3','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/products/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'FOSSPedia','','publish','closed','closed','','fosspedia','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'4','nav_menu_item','','0');
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
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'Open Data','','publish','closed','closed','','open-dataset','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'5','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/open-datasets/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'Stories','','publish','closed','closed','','success-story','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'6','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/success-stories/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'FOSS Map','','publish','closed','closed','','fossmap','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'7','nav_menu_item','','0');
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
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'Request Center','','publish','closed','closed','','request-center','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'8','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/request-center/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'Collaboration Center','','publish','closed','closed','','collaboration-center','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'9','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/collaboration-center/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

# arabic menu modifications
SET @main_nav_ar = (SELECT term_id FROM wpRuvF8_terms where name = 'main_navigation_ar' limit 1);
SET @nav_menu_tax_ar = (SELECT term_id FROM wpRuvF8_term_taxonomy where term_id = (select @main_nav_ar) and taxonomy = 'nav_menu' limit 1);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'news-ar' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_ar);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'events-ar' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_ar);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'products-ar' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_ar);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'success-story-ar' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_ar);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'open-dataset-ar' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_ar);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'fosspedia-ar' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_ar);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'fossmap-ar' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_ar);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'request-center-ar' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_ar);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @itemId = (SELECT ID FROM wpRuvF8_posts where post_name = 'collaboration-center-ar' and post_type = 'nav_menu_item' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @itemId) and term_taxonomy_id = (select @nav_menu_tax_ar);
delete from `wpRuvF8_postmeta` where post_id = (select @itemId);
delete from `wpRuvF8_posts` where ID = (select @itemId);

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'الأخبار','','publish','closed','closed','','news-ar','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'1','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/news/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'الفعاليات','','publish','closed','closed','','events-ar','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'2','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/events/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'المنتجات','','publish','closed','closed','','products-ar','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'3','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/products/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'مركز المعرفة','','publish','closed','closed','','fosspedia-ar','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'4','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/wiki/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'البيانات المفتوحة','','publish','closed','closed','','open-dataset-ar','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'5','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/open-datasets/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'قصص النجاح','','publish','closed','closed','','success-story-ar','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'6','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/success-stories/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'الخريطة','','publish','closed','closed','','fossmap-ar','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'7','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/maps/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'مركز الطلبات','','publish','closed','closed','','request-center-ar','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'8','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/request-center/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-07-24 17:33:00','2016-07-24 17:33:00','',
'مركز المشاركة','','publish','closed','closed','','collaboration-center-ar','','','2016-07-24 17:33:00','2016-07-24 17:33:00','','0',
concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'9','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/collaboration-center/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');
