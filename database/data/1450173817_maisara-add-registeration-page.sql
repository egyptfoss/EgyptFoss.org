SET @lastInsertedPost = (SELECT max(ID) from wpRuvF8_posts);
SET @newPostId = (select @lastInsertedPost) + 1;
INSERT INTO `wpRuvF8_posts` VALUES((select @newPostId),'1','2015-12-14 18:54:12','2015-12-14 16:54:12','','Register','','publish','closed','closed','','register','','','2015-12-14 18:54:12','2015-12-14 16:54:12','','0',concat('http://egyptfoss.com/','register/'),'0','page','','0');

# UPDATE `wpRuvF8_options` SET  option_value = concat('a:4:{s:7:\"members\";i:0;s:8:\"activity\";i:0;s:8:\"register\";i:',(select @newPostId),';s:8:\"activate\";i:0;}') WHERE `option_name` = 'bp-pages';