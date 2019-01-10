<?php

function ef_listing_homepage($args = array())
{
    global $wpdb;
    $results = array();
    $sql = "select distinct posts.ID,posts.post_title,posts.guid, posts.post_type,
                news_description.meta_value as description,news_homepage.meta_value as home_featured,
                posts.post_date
                from {$wpdb->prefix}posts as posts
                join {$wpdb->prefix}postmeta as news_language on news_language.post_id = posts.ID
                join {$wpdb->prefix}postmeta as news_homepage on news_homepage.post_id = posts.ID
                join {$wpdb->prefix}postmeta as news_description on news_description.post_id = posts.ID
                where posts.post_type = 'news'
                and posts.post_status = 'publish'
                and news_language.meta_key = 'language'
                and news_homepage.meta_key = 'is_news_featured_homepage'
                and news_description.meta_key = 'description'
                and news_language.meta_value like '%{$args['current_lang']}%'
                order by news_homepage.meta_value desc, posts.post_date desc
                Limit 3;";
            
    $top_section = $wpdb->get_results($sql);
    if(sizeof($top_section) >= 1)
    {
      $results[] = $top_section[0];
    }
    
    //find latest success story & expert_thoughts
    $sql = "select distinct posts.ID,posts.post_title,posts.guid, posts.post_type, posts.post_date 
          from {$wpdb->prefix}posts as posts 
          join {$wpdb->prefix}postmeta as news_language on news_language.post_id = posts.ID 
          join {$wpdb->prefix}postmeta as news_homepage on news_homepage.post_id = posts.ID
          where posts.post_type = 'news' and posts.post_status = 'publish' 
          and news_language.meta_key = 'language' 
          and news_language.meta_value like '%{$args['current_lang']}%' 
          and news_homepage.meta_key = 'is_news_featured_homepage'
          and news_homepage.meta_value = 0
          ORDER BY posts.post_date DESC LIMIT 0,1";
    $latest_news = $wpdb->get_results($sql);
    foreach($latest_news as $result) {
      $results[] = $result;
    }
    
    //find latest success story & expert_thoughts
    $sql = "select distinct posts.ID,posts.post_title,posts.guid, posts.post_type, posts.post_date 
          from {$wpdb->prefix}posts as posts 
          join {$wpdb->prefix}postmeta as news_language on news_language.post_id = posts.ID 
          where posts.post_type = 'success_story' and posts.post_status = 'publish' 
          and news_language.meta_key = 'language' 
          and news_language.meta_value like '%{$args['current_lang']}%' 
          ORDER BY RAND() LIMIT 0,2";
    $top_section_success_experts = $wpdb->get_results($sql);
    foreach($top_section_success_experts as $result) {
      $results[] = $result;
    }
    
    //fill results array to count to 5 from topsections
    for($i = 1; $i < sizeof($top_section); $i++)
    {
      if(sizeof($results) >= 3)
        break;
      $results[] = $top_section[$i];
    }
    
    return $results;
}

function ef_listing_homepage_events($args = array())
{
    global $wpdb;
        
    $sql = "select distinct posts.ID,posts.post_title, DATE_FORMAT(eventdate.meta_value, '%b') as start_date_month,
                DAY(eventdate.meta_value) as start_date_day,eventtype.meta_value as event_type,
                venue_post.post_title as venue_name,organizer_post.post_title as organizer_name
                from $wpdb->posts as posts
                join $wpdb->postmeta as eventdate on eventdate.post_id = posts.ID
                join $wpdb->postmeta as event_end_date on event_end_date.post_id = posts.ID
                join $wpdb->postmeta as eventtype on eventtype.post_id = posts.ID
                join $wpdb->postmeta as venue on venue.post_id = posts.ID 
                join $wpdb->posts as venue_post on venue.meta_value = venue_post.ID
                join $wpdb->postmeta as organizer on organizer.post_id = posts.ID 
                join $wpdb->posts as organizer_post on organizer.meta_value = organizer_post.ID
                where posts.post_type = 'tribe_events'
                and posts.post_status = 'publish'
                and DATE(event_end_date.meta_value) >= CURDATE()
                and eventdate.meta_key = '_EventStartDate'
                and event_end_date.meta_key = '_EventEndDate'
                and eventtype.meta_key = 'event_type'
                and venue.meta_key = '_EventVenueID'
                and organizer.meta_key = '_EventOrganizerID'
                order by eventdate.meta_value asc
                Limit 8; ";              
    $events = $wpdb->get_results($sql);
    return $events;
}

function ef_return_arabic_months()
{
    $months = array(
        "Jan" => "يناير",
        "Feb" => "فبراير",
        "Mar" => "مارس",
        "Apr" => "أبريل",
        "May" => "مايو",
        "Jun" => "يونيو",
        "Jul" => "يوليو",
        "Aug" => "أغسطس",
        "Sep" => "سبتمبر",
        "Oct" => "أكتوبر",
        "Nov" => "نوفمبر",
        "Dec" => "ديسمبر"
    );
    return $months;
}

function ef_listing_homepage_news($args = array())
{
    global $wpdb;
        
    $sql = "select distinct posts.ID,posts.post_title,news_description.meta_value as description,news_featured.meta_value as is_featured,posts.post_date
            from $wpdb->posts as posts
            join $wpdb->postmeta as news_language on news_language.post_id = posts.ID
            join $wpdb->postmeta as news_description on news_description.post_id = posts.ID
            join $wpdb->postmeta as news_featured on news_featured.post_id = posts.ID
            where posts.post_type = 'news'
            and posts.post_status = 'publish'
            and news_language.meta_key = 'language'
            and news_featured.meta_key = 'is_featured'
            and news_language.meta_value like '%{$args['current_lang']}%'
            and news_description.meta_key = 'description'
            order by posts.post_date desc
            Limit 8; ";
               
    $news = $wpdb->get_results($sql);
    return $news;
}

function ef_listing_homepage_products($args = array())
{
    global $wpdb;
        
    $sql = "select * from 
            (select distinct posts.ID,posts.post_title,product_term.name,product_term.name_ar
                            from $wpdb->posts as posts
                            join $wpdb->postmeta as product_language on product_language.post_id = posts.ID
                            join $wpdb->postmeta as product_featured on product_featured.post_id = posts.ID
                            left join $wpdb->term_relationships as product_term_relation on product_term_relation.object_id = posts.ID
                            left join $wpdb->term_taxonomy as product_term_taxonomy on product_term_taxonomy.term_taxonomy_id = product_term_relation.term_taxonomy_id
                            left join $wpdb->terms as product_term on product_term.term_id = product_term_taxonomy.term_id
                            where posts.post_type = 'product'
                            and posts.post_status = 'publish'
                            and product_language.meta_key = 'language'
                            and product_language.meta_value like '%{$args['current_lang']}%'
                            and product_featured.meta_key = 'is_featured'
                            and product_featured.meta_value = '1'
                            and product_term_taxonomy.taxonomy = 'industry'
                            order by posts.post_date desc
                            ) products
            group by products.name 
            order by products.ID desc Limit 8";
                   
    $products = $wpdb->get_results($sql);
    return $products;
}

function ef_listing_posts( $post_type, $lang = NULL )
{
    global $wpdb;
    
    $join = $where = '';
    
    if( $lang ) {
      $join   = "join $wpdb->postmeta as post_language on post_language.post_id = posts.ID";
      $where  = "and post_language.meta_key = 'language' and post_language.meta_value like '%{$lang}%'";
    }
    
    $sql = "select distinct posts.ID,posts.post_title,posts.post_content,posts.post_date,posts.post_author
            from $wpdb->posts as posts
            $join
            where posts.post_type = '{$post_type}'
            and posts.post_status = 'publish'
            $where
            order by posts.post_date desc
            Limit 5; ";
               
    $expert_thoughts = $wpdb->get_results($sql);
    return $expert_thoughts;
}
