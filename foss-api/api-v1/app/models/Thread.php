<?php

class Thread extends BaseModel {
	protected $table = 'request_threads';

	public function addThread($data) {
		$this->request_id = $data['request_id'];
		if( isset($data['owner_id']) && !empty($data['owner_id']) ) {
			$this->owner_id = $data['owner_id'];
		} else {
			$request = Post::where('ID', '=', $this->request_id)->first();
			$this->owner_id = $request->post_author;
		}
		$this->user_id = $data['user_id'];
		$this->created_at = date('Y-m-d H:i:s');
		$this->status = 1;
		$this->updated_at = $this->created_at;
		return $this;
	}

	public function updateThread($data) {
    $this->seen_by_owner = ($this->seen_by_owner == 1) ? 1 : 0;
    $this->seen_by_user = ($this->seen_by_user == 0) ? 0 : 1;
    if ($this->responses_count == NULL) {
      $this->responses_count = 0;
    }
    if ($this->status === NULL) {
      $this->status = 1;
    }

		$this->seen_by_owner = (isset($data['seen_by_owner'])) ? $data['seen_by_owner'] : (($this->seen_by_owner == null)?: $this->seen_by_owner);
		$this->seen_by_user = (isset($data['seen_by_user'])) ? $data['seen_by_user'] : $this->seen_by_user;
		$this->responses_count = (isset($data['responses_count'])) ? ($this->responses_count + $data['responses_count']) : $this->responses_count;
		$this->status = (isset($data['status'])) ? $data['status'] : $this->status;
		$this->updated_at = (isset($data['responses_count'])) ? date('Y-m-d H:i:s') : $this->updated_at;
		return $this;
	}

}