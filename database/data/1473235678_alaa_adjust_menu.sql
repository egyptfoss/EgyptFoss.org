# UPDATE EXISTING MENU TITLE & POSITIONS
Update wpRuvF8_posts set post_title = 'Map',menu_order = 3 where post_title = 'FOSS Map' and post_type = 'nav_menu_item';
Update wpRuvF8_posts set menu_order = 3 where post_title = 'الخريطة' and post_type = 'nav_menu_item';

Update wpRuvF8_posts set menu_order = 4 where post_title = 'Products' and post_type = 'nav_menu_item';
Update wpRuvF8_posts set menu_order = 4 where post_title = 'المنتجات' and post_type = 'nav_menu_item';

Update wpRuvF8_posts set post_title = 'Data' where post_title = 'Open Data' and post_type = 'nav_menu_item';
Update wpRuvF8_posts set post_title = 'البيانات' where post_title = 'البيانات المفتوحة' and post_type = 'nav_menu_item';

Update wpRuvF8_posts set post_title = 'Collaboration',menu_order = 6 where post_title = 'Collaboration Center' and post_type = 'nav_menu_item';
Update wpRuvF8_posts set post_title = 'المشاركة',menu_order = 6 where post_title = 'مركز المشاركة' and post_type = 'nav_menu_item';

Update wpRuvF8_posts set menu_order = 9 where post_title = 'Request Center' and post_type = 'nav_menu_item';
Update wpRuvF8_posts set menu_order = 9 where post_title = 'مركز الطلبات' and post_type = 'nav_menu_item';

Update wpRuvF8_posts set menu_order = 11 where post_title = 'FOSSPedia' and post_type = 'nav_menu_item';
Update wpRuvF8_posts set menu_order = 9 where post_title = 'مركز المعرفة' and post_type = 'nav_menu_item';

Update wpRuvF8_posts set post_title='Success Stories',menu_order = 14 where post_title = 'Stories' and post_type = 'nav_menu_item';
Update wpRuvF8_posts set menu_order = 14 where post_title = 'قصص النجاح' and post_type = 'nav_menu_item';

# INSERT NEW ITEMS
SET @main_nav = (SELECT term_id FROM wpRuvF8_terms where name = 'main_navigation' limit 1);
SET @nav_menu_tax_en = (SELECT term_id FROM wpRuvF8_term_taxonomy where term_id = (select @main_nav) and taxonomy = 'nav_menu' limit 1);

SET @main_nav_ar = (SELECT term_id FROM wpRuvF8_terms where name = 'main_navigation_ar' limit 1);
SET @nav_menu_tax_ar = (SELECT term_id FROM wpRuvF8_term_taxonomy where term_id = (select @main_nav_ar) and taxonomy = 'nav_menu' limit 1);

# Marketplace header
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','Marketplace','','publish','closed','closed','','services','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'7','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','#');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @marketPlaceHeaderID = (select @newPostId);

# ar
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','السوق','','publish','closed','closed','','ar-services','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'7','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','#');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @marketPlaceHeaderID_Ar = (select @newPostId);

# Marketplace Services inside Marketplace
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','Marketplace Services','','publish','closed','closed','','marketplace','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'8','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/market-place');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent',(select @marketPlaceHeaderID));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

# ar
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','معرض الخدمات','','publish','closed','closed','','ar-marketplace','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'8','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/market-place');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent',(select @marketPlaceHeaderID_Ar));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

# UPDATE REQUEST CENTER inside Marketplace header
SET @requestCenterId = (SELECT ID from wpRuvF8_posts where post_title = 'Request Center' and post_type = 'nav_menu_item');
Update wpRuvF8_postmeta set meta_value = (select @marketPlaceHeaderID) where post_id = (select @requestCenterId) and meta_key = '_menu_item_menu_item_parent';

SET @requestCenterId_Ar = (SELECT ID from wpRuvF8_posts where post_title = 'مركز الطلبات' and post_type = 'nav_menu_item');
Update wpRuvF8_postmeta set meta_value = (select @marketPlaceHeaderID_Ar) where post_id = (select @requestCenterId_Ar) and meta_key = '_menu_item_menu_item_parent';


# Knowledge header
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','knowledge','','publish','closed','closed','','knowledge','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'10','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','#');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @knowledgeHeaderID = (select @newPostId);

# ar
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','المعرفة','','publish','closed','closed','','ar-knowledge','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'10','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','#');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @knowledgeHeaderID_Ar = (select @newPostId);


# UPDATE FOSSPedia inside Knowledge header
SET @fosspediaId = (SELECT ID from wpRuvF8_posts where post_title = 'FOSSPedia' and post_type = 'nav_menu_item');
Update wpRuvF8_postmeta set meta_value = (select @knowledgeHeaderID) where post_id = (select @fosspediaId) and meta_key = '_menu_item_menu_item_parent';

SET @fosspediaId_Ar = (SELECT ID from wpRuvF8_posts where post_title = 'مركز المعرفة' and post_type = 'nav_menu_item');
Update wpRuvF8_postmeta set meta_value = (select @knowledgeHeaderID_Ar) where post_id = (select @fosspediaId_Ar) and meta_key = '_menu_item_menu_item_parent';

# Awareness Center inside Knowledge header
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','Awareness Center','','publish','closed','closed','','awareness-center','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'12','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/awareness-center');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent',(select @knowledgeHeaderID));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

# Ar
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','مركز التقييم','','publish','closed','closed','','ar-awareness-center','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'12','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/awareness-center');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent',(select @knowledgeHeaderID_Ar));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

# Inspiration header
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','Inspiration','','publish','closed','closed','','thoughts','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'13','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','#');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @inspirationHeaderID = (select @newPostId);

# Ar
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','الخبرات','','publish','closed','closed','','ar-thoughts','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'13','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','#');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

SET @inspirationHeaderID_Ar = (select @newPostId);

# UPDATE Success Stories inside Inspiration header
SET @successstoriesId = (SELECT ID from wpRuvF8_posts where post_title = 'Success Stories' and post_type = 'nav_menu_item');
Update wpRuvF8_postmeta set meta_value = (select @inspirationHeaderID) where post_id = (select @successstoriesId) and meta_key = '_menu_item_menu_item_parent';

SET @successstoriesId_Ar = (SELECT ID from wpRuvF8_posts where post_title = 'قصص النجاح' and post_type = 'nav_menu_item');
Update wpRuvF8_postmeta set meta_value = (select @inspirationHeaderID_Ar) where post_id = (select @successstoriesId_Ar) and meta_key = '_menu_item_menu_item_parent';

# Expert Thoughts inside Inspiration header
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','Expert Thoughts','','publish','closed','closed','','expert-thoughts','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'15','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_en),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/en/expert-thoughts');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent',(select @inspirationHeaderID));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');

# Ar
SET @newPostId = (SELECT max(ID) from wpRuvF8_posts) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-03-01 15:47:18','2016-03-01 15:47:18','','أراء الخبراء','','publish','closed','closed','','ar-expert-thoughts','','','2016-03-01 15:47:18','2016-03-01 15:47:18','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'15','nav_menu_item','','0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @nav_menu_tax_ar),'0');

INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_url','/ar/expert-thoughts');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_xfn','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_classes','a:1:{i:0;s:0:"";}');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_target','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object','');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_object_id',(select @newPostId));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_menu_item_parent',(select @inspirationHeaderID_Ar));
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_menu_item_type','custom');