UPDATE `wpRuvF8_options` SET option_value = 'a:4:{s:8:"activity";i:1;s:7:"members";i:1;s:8:"settings";i:1;s:13:"notifications";i:1;}' WHERE option_name = 'bp-active-components';
INSERT INTO `wpRuvF8_options` VALUES (NULL,'_bp_db_version','10071','yes');

SET @registerId = (SELECT ID FROM wpRuvF8_posts where post_name = 'register' limit 1);
SET @membersId = (SELECT ID FROM wpRuvF8_posts where post_name = 'members' limit 1);
SET @activityId = (SELECT ID FROM wpRuvF8_posts where post_name = 'activity' limit 1);
SET @activateId = (SELECT ID FROM wpRuvF8_posts where post_name = 'activate' limit 1);

UPDATE `wpRuvF8_options` SET option_value = concat('a:4:{s:7:"members";i:',(select @membersId),';s:8:"activity";i:',(select @activityId),';s:8:"register";i:',(select @registerId),';s:8:"activate";i:',(select @activateId),';}') where option_name = 'bp-pages';
