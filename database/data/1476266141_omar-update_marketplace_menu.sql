# Generated by DBDiff
# On 10/12/2016 12:55:41 pm

# Update Marketplace Menu item title & URL ( FossTesting )
SET @marketplaceNavId = (SELECT ID FROM wpRuvF8_posts where post_name = 'marketplace-2' and post_type = 'nav_menu_item'  limit 1);
UPDATE `wpRuvF8_posts` set post_title = 'Services Market' where ID = (select @marketplaceNavId);
UPDATE `wpRuvF8_postmeta` SET meta_value = '/en/marketplace' WHERE post_id = (select @marketplaceNavId) AND meta_key = '_menu_item_url';
