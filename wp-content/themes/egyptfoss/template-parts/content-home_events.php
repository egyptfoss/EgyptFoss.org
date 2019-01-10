<?php 
include( ABSPATH . 'system_data.php' );
$args = array(
    "current_lang" => pll_current_language()
);

if($args['current_lang'] == 'ar') {
  global $ar_events_types;
  $types = $ar_events_types;
} else {
  global $events_types;
  $types = $events_types;
}
$events = ef_listing_homepage_events($args);
$months = ef_return_arabic_months();
?>
<section class="upcoming-events clearfix">
<div class="container">
	<div class="row">
	<div class="col-md-12">

            <?php if(sizeof($events) > 0) { ?>
	<div class="events-list--upcoming lfloat owl-carousel">
            
            <?php foreach($events as $event) { ?>
		<div class="item--event item clearfix">
                    <div class="day-square front-card lfloat"><span class="month"><?php 
                        $en_month = $event->start_date_month;
                        if(pll_current_language()== "en")
                            echo $event->start_date_month;
                        else echo $months[$en_month];?>
                        </span><span class="day"><?php echo $event->start_date_day ; ?></span>
                    </div>
                    <div class="event-data lfloat">
                        <?php
                          $event_start_date = tribe_get_start_date($event->ID,true,"g:i A");
                          $event_start_fulldate = tribe_get_start_date($event->ID,true,"Y-m-d");
                          $event_end_date = tribe_get_end_date($event->ID,true,"g:i A ");
                          $event_end_fulldate = tribe_get_end_date($event->ID,true,"Y-m-d");
                        ?>
                        <h4><a href="<?php echo get_permalink($event->ID) ;?>"><?php echo $event->post_title ; ?></a></h4>
                        <!--	<strong class="event-label"><?php //_e("Type","egyptfoss"); ?></strong><?php// echo ': ' . $types[$event->event_type] . ' - ' ; ?>-->
                        <!--  <strong class="event-label"><?php// _e("Venue","egyptfoss"); ?></strong><?php //echo ': ' . $event->venue_name ; ?>-->
                        <?php
                        if (tribe_get_start_date($event->ID, true, "Y-m-d") == tribe_get_end_date($event->ID, true, "Y-m-d")) {?>
                            <strong><?php _e("From", "egyptfoss") ?>: </strong>
                            <?php echo $event_start_date; ?> 
                            <strong><?php _e("To", "egyptfoss") ?>: </strong>
                            <?php echo $event_end_date;
                        }
                        else{ ?>
                            <strong><?php _e("From", "egyptfoss") ?>: </strong>
                            <?php echo $event_start_fulldate; ?> 
                            <strong><?php _e("To", "egyptfoss") ?>: </strong>
                            <?php echo $event_end_fulldate;
                        }?>
                    </div>
		</div>
            <?php } ?>
	</div>
           <?php  }else { ?>
            <h3 class="no-events-front"><?php _e("There are no upcoming events for the next year. If you know any, please share with us.","egyptfoss"); ?></h3>
            <?php } ?>
	</div>
</div>
</div>
</section>
