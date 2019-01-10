SET @lastInsertedPost = (SELECT max(ID) from wpRuvF8_posts);
SET @newPostId = (select @lastInsertedPost) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-10-04 13:40:00','2016-10-04 13:40:00','','View Revision','','publish','closed','closed','','view-revision','','','2016-06-09 11:53:00','2016-05-04 11:53:00','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'0','page','','0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_wp_page_template','CollaborationCenter/template-single-revision.php');

SET @arabicPostId = (select @newPostId) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @arabicPostId),'1','2016-10-04 13:40:00','2016-10-04 13:40:00','','View Revision Ar','','publish','closed','closed','','view-revision-ar','','','2016-10-04 13:40:00','2016-10-04 13:40:00','','0',concat('http://egyptfoss.com/?page_id=',(select @arabicPostId)),'0','page','','0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @arabicPostId),'_wp_page_template','CollaborationCenter/template-single-revision.php');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'pll_57f39574da41e','pll_57f39574da41e','0','pll_57f39574da41e');
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

DELETE from wpRuvF8_options  where option_name ='ef_custom_rewrite_rules_loaded';