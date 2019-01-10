SET @productIds = (SELECT ID from wpRuvF8_posts where post_name = 'products' and post_type = 'page' limit 1);
delete from `wpRuvF8_postmeta` where post_id = (select @productIds);
delete from `wpRuvF8_term_relationships` where object_id = (select @productIds);
delete from `wpRuvF8_posts` where ID = (select @productIds);

SET @productIds = (SELECT ID from wpRuvF8_posts where post_name = 'ar-products' and post_type = 'page' limit 1);
delete from `wpRuvF8_postmeta` where post_id = (select @productIds);
delete from `wpRuvF8_term_relationships` where object_id = (select @productIds);
delete from `wpRuvF8_posts` where ID = (select @productIds);