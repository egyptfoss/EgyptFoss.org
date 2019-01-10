SET @newPostId = (SELECT ID FROM wpRuvF8_posts where post_name = 'add-product' limit 1);

SET @lastInsertedPost = (SELECT max(ID) from wpRuvF8_posts);
SET @arabicPostId = (select @lastInsertedPost) + 1;

INSERT INTO `wpRuvF8_posts` VALUES((select @arabicPostId),'1','2016-01-19 15:23:00','2016-01-19 15:23:00','','أضف منتج جديد','','publish','closed','closed','','%d8%a7%d8%b6%d9%81-%d9%85%d9%86%d8%aa%d8%ac-%d8%ac%d8%af%d9%8a%d8%af','','','2016-01-19 15:23:00','2016-01-19 15:23:00','','0',concat('http://egyptfoss.com/?page_id=',(select @arabicPostId)),'0','page','','0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @arabicPostId),'_wp_page_template','page-add-product.php');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'pll_569e595fe9af0','pll_569e595fe9af0','0','pll_569e595fe9af0');
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