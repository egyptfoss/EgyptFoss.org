SET @lastInsertedPost = (SELECT max(ID) from wpRuvF8_posts);
SET @newPostId = (select @lastInsertedPost) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-04-12 11:53:00','2016-04-12 11:53:00','','','','publish','closed','closed','','import-semantic','','','2016-04-07 11:53:00','2016-04-07 11:53:00','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId)),'0','page','','0');
INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_wp_page_template','semantic-save-posts.php');
