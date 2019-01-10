SET @lastInsertedPost = (SELECT max(ID) from wpRuvF8_posts);
SET @newPostId = (select @lastInsertedPost) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-04-20 11:53:00','2016-04-20 11:53:00','','Terms of services','','publish','closed','closed','','terms-of-services','','','2016-04-20 11:53:00','2016-04-20 11:53:00','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'0','page','','0');

SET @arabicPostId = (select @newPostId) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @arabicPostId),'1','2016-04-20 11:53:00','2016-04-20 11:53:00','','شروط الخدمة','','publish','closed','closed','','ar-terms-of-services','','','2016-02-28 15:53:00','2016-02-28 15:53:00','','0',concat('http://egyptfoss.com/?page_id=',(select @arabicPostId)),'0','page','','0');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'pll_57175ccee51b8','pll_57175ccee51b8','0','pll_57175ccee51b8');
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

# privacy policy

SET @lastInsertedPost = (SELECT max(ID) from wpRuvF8_posts);
SET @newPostId = (select @lastInsertedPost) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-04-20 11:53:00','2016-04-20 11:53:00','','Privacy policy','','publish','closed','closed','','privacy-policy','','','2016-04-20 11:53:00','2016-04-20 11:53:00','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'0','page','','0');

SET @arabicPostId = (select @newPostId) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @arabicPostId),'1','2016-04-20 11:53:00','2016-04-20 11:53:00','','سياسة الخصوصية','','publish','closed','closed','','ar-privacy-policy','','','2016-02-28 15:53:00','2016-02-28 15:53:00','','0',concat('http://egyptfoss.com/?page_id=',(select @arabicPostId)),'0','page','','0');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'pll_57175c997a37a','pll_57175c997a37a','0','pll_57175c997a37a');
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

# Contact us

SET @lastInsertedPost = (SELECT max(ID) from wpRuvF8_posts);
SET @newPostId = (select @lastInsertedPost) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-04-20 11:53:00','2016-04-20 11:53:00','','Contact us','','publish','closed','closed','','contact-us','','','2016-04-20 11:53:00','2016-04-20 11:53:00','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'0','page','','0');

SET @arabicPostId = (select @newPostId) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @arabicPostId),'1','2016-04-20 11:53:00','2016-04-20 11:53:00','','إتصل بنا','','publish','closed','closed','','ar-contact-us','','','2016-02-28 15:53:00','2016-02-28 15:53:00','','0',concat('http://egyptfoss.com/?page_id=',(select @arabicPostId)),'0','page','','0');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'pll_57175c5c6d3e3','pll_57175c5c6d3e3','0','pll_57175c5c6d3e3');
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