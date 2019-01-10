# Generated by DBDiff
# On 09/29/2016 01:32:02 pm

# Update Marketplace page title
SET @marketplacePageId = (SELECT ID FROM wpRuvF8_posts where post_name = 'market-place' and post_type = 'page'  limit 1);
update  `wpRuvF8_posts` set post_title = 'Services Market' where ID = (select @marketplacePageId);

# Update Marketplace Menu item title & URL
SET @marketplaceNavId = (SELECT ID FROM wpRuvF8_posts where post_name = 'marketplace' and post_type = 'nav_menu_item'  limit 1);
SET @marketplaceArNavId = (SELECT ID FROM wpRuvF8_posts where post_name = 'ar-marketplace' and post_type = 'nav_menu_item'  limit 1);
UPDATE `wpRuvF8_posts` set post_title = 'Services Market' where ID = (select @marketplaceNavId);
UPDATE `wpRuvF8_postmeta` SET meta_value = '/en/marketplace' WHERE post_id = (select @marketplaceNavId) AND meta_key = '_menu_item_url';
UPDATE `wpRuvF8_postmeta` SET meta_value = '/ar/marketplace' WHERE post_id = (select @marketplaceArNavId) AND meta_key = '_menu_item_url';

# Delete option to init the rewrite rules
DELETE from wpRuvF8_options  where option_name ='ef_custom_rewrite_rules_loaded';

# Update Expert thoughts arabic menu title
SET @expertArNavId = (SELECT ID FROM wpRuvF8_posts where post_name = 'ar-expert-thoughts' and post_type = 'nav_menu_item'  limit 1);
UPDATE `wpRuvF8_posts` set post_title = 'مدونة الخبراء' where ID = (select @expertArNavId);

# Update adding Expert thoughts page title
SET @newThoughtPageId = (SELECT ID FROM wpRuvF8_posts where post_name = 'add-expert-thought' and post_type = 'page'  limit 1);
SET @newThoughtArPageId = (SELECT ID FROM wpRuvF8_posts where post_name = 'ar-add-expert-thought' and post_type = 'page'  limit 1);
UPDATE `wpRuvF8_posts` set post_title = 'New Post' where ID = (select @newThoughtPageId);
UPDATE `wpRuvF8_posts` set post_title = 'مقال جديد' where ID = (select @newThoughtArPageId);