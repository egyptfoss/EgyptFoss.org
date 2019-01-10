# Generated by DBDiff
# On 03/01/2016 03:33:31 pm
SET @main_nav = (SELECT term_id FROM wpRuvF8_terms where name = 'main_navigation' limit 1);
SET @nav_menu_tax_en = (SELECT term_id FROM wpRuvF8_term_taxonomy where term_id = (select @main_nav) and taxonomy = 'nav_menu' limit 1);

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','News','','publish','closed','closed','','news','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'1','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/news');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @productsPageId = (SELECT ID FROM wpRuvF8_posts where post_name = 'products' limit 1);
SET @newPostId = (SELECT @newPostId) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','Products','','publish','closed','closed','',(select @newPostId),'','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'2','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','page');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @productsPageId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','post_type');


SET @newPostId = (SELECT @newPostId) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','FOSSpedia','','publish','closed','closed','','fosspedia','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'3','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/wiki/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT @newPostId) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','Events','','publish','closed','closed','','events','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'4','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/events/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @mapPageId = (SELECT ID FROM wpRuvF8_posts where post_name = 'foss-map' limit 1);
SET @newPostId = (SELECT @newPostId) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','','','publish','closed','closed','',(select @newPostId),'','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'5','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','page');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @mapPageId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','post_type');


INSERT INTO `wpRuvF8_terms` VALUES (NULL,'main_navigation_ar','main_navigation_ar','0','main_navigation_ar');
SET @main_nav_ar = (select last_insert_id());
INSERT INTO `wpRuvF8_term_taxonomy` VALUES (NULL,(select @main_nav_ar),'nav_menu','','0','5');
SET @nav_menu_tax_ar = (select last_insert_id());

SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','الاخبار','','publish','closed','closed','','%d8%a7%d9%84%d8%a7%d8%ae%d8%a8%d8%a7%d8%b1','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'1','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/news');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @productsPageId = (SELECT ID FROM wpRuvF8_posts where post_name = '%d8%a7%d9%84%d9%85%d9%86%d8%aa%d8%ac%d8%a7%d8%aa' limit 1);
SET @newPostId = (SELECT @newPostId) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','','','publish','closed','closed','',(select @newPostId),'','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'2','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','page');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @productsPageId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','post_type');


SET @newPostId = (SELECT @newPostId) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','FOSSpedia','','publish','closed','closed','','fosspedia_ar','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'3','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/wiki/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @newPostId = (SELECT @newPostId) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','الحدث','','publish','closed','closed','','events_ar','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'4','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/events/');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @mapPageId = (SELECT ID FROM wpRuvF8_posts where post_name = 'ar-foss-map' limit 1);
SET @newPostId = (SELECT @newPostId) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','','','publish','closed','closed','',(select @newPostId),'','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'5','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url',' ');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','page');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @mapPageId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','post_type');

UPDATE `wpRuvF8_options` SET `option_value` = concat('a:13:{s:7:"browser";i:1;s:7:"rewrite";i:1;s:12:"hide_default";i:0;s:10:"force_lang";i:1;s:13:"redirect_lang";i:0;s:13:"media_support";i:0;s:4:"sync";a:0:{}s:10:"post_types";a:1:{i:0;s:7:"product";}s:10:"taxonomies";a:0:{}s:7:"domains";a:0:{}s:7:"version";s:6:"1.7.12";s:12:"default_lang";s:2:"en";s:9:"nav_menus";a:1:{s:9:"egyptfoss";a:1:{s:7:"primary";a:2:{s:2:"en";i:',(select @main_nav),';s:2:"ar";i:',(select @main_nav_ar),';}}}}') where option_name = 'polylang';
DELETE from `wpRuvF8_options` where option_name = "theme_mods_egyptfoss";
INSERT INTO `wpRuvF8_options` VALUES (NULL,'theme_mods_egyptfoss',concat('a:1:{s:18:"nav_menu_locations";a:1:{s:7:"primary";i:',(select @main_nav),';}}'),'yes');