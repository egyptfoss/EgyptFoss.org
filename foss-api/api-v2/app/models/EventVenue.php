<?php

class EventVenue extends BaseModel {
	protected $table = 'events_venues';

	public function addEventVenue($data){
		$this->event_id = $data['event_id'];
		$this->venue_id = $data['venue_id'];
		$this->venue_name = $data['venue_name'];
		$this->event_start_datetime = $data['event_start_datetime'];
		$this->event_end_datetime = $data['event_end_datetime'];
		return $this;
	}
}