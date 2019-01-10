SET @lastInsertedPost = (SELECT max(ID) from wpRuvF8_posts);
SET @newPostId = (select @lastInsertedPost) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2016-01-19 12:03:43','2016-01-19 12:03:43','','Add Product','','publish','closed','closed','','add-product','','','2016-01-19 12:03:43','2016-01-19 12:03:43','','0',concat('http://egyptfoss.com/?page_id=',(select @newPostId),'/'),'0','page','','0');


INSERT INTO `wpRuvF8_postmeta` VALUES (NULL,(select @newPostId),'_wp_page_template','page-add-product.php');
