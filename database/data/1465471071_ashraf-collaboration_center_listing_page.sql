SET @PageId = (SELECT ID FROM wpRuvF8_posts where post_name = 'listing-spaces-2' and post_type = 'page' limit 1);
delete from `wpRuvF8_term_relationships` where object_id = (select @PageId);

SET @lastInsertedPost = (SELECT max(ID) from wpRuvF8_posts);
SET @newPostId = (select @lastInsertedPost) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-06-09 11:53:00','2016-06-09 11:53:00','','Collaboration Center','','publish','closed','closed','','collaboration-center','','','2016-06-09 11:53:00','2016-05-04 11:53:00','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'0','page','','0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_wp_page_template','template-listing-items.php');

SET @arabicPostId = (select @newPostId) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @arabicPostId),'1','2016-06-09 11:53:00','2016-06-09 11:53:00','','مركز التعاون','','publish','closed','closed','','ar-collaboration-center','','','2016-06-09 15:53:00','2016-06-09 15:53:00','','0',concat('http://egyptfoss.com/?page_id=',(select @arabicPostId)),'0','page','','0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @arabicPostId),'_wp_page_template','template-listing-items.php');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'pll_57596fac99927','pll_57596fac99927','0','pll_5755415b90db8');
SET @lastInsertedID = (select last_insert_id());
INSERT INTO `wpRuvF8_term_taxonomy` VALUES (NULL,(select @lastInsertedID),'post_translations',concat('a:2:{s:2:\"ar\";i:',(select @arabicPostId),';s:2:\"en\";i:',(select @newPostId),';}'),'0','2');
SET @lastInsertedID = (select last_insert_id());
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @arabicPostId),(select @lastInsertedID),'0');
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @lastInsertedID),'0');


SET @term_ar_6 = (SELECT term_id FROM `wpRuvF8_terms` WHERE `slug` = 'ar' limit 1);
SET @term_tax_id_6 = (SELECT term_taxonomy_id from  `wpRuvF8_term_taxonomy` WHERE `term_id` =  (select @term_ar_6));
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @arabicPostId),(select @term_tax_id_6),'0');

SET @term_en_3 = (SELECT term_id FROM `wpRuvF8_terms` WHERE `slug` = 'en' limit 1);
SET @term_tax_id_3 = (SELECT term_taxonomy_id from  `wpRuvF8_term_taxonomy` WHERE `term_id` =  (select @term_en_3));
INSERT INTO `wpRuvF8_term_relationships` VALUES((select @newPostId),(select @term_tax_id_3),'0');