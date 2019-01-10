<?php

class Response extends BaseModel {
	protected $table = 'thread_responses';

	public function addResponse($data) {
		if(isset($data['thread_id']) && !empty($data['thread_id'])) {
			$this->thread_id = $data['thread_id'];
		} else {
			$thread = Thread::where('request_id', '=', $data['request_id'])->where('user_id', '=', $data['user_id'])->first();
			if ($thread == null) {
				$thread = new Thread;
				$thread->addThread($data);
				$thread->save();
			}
			$this->thread_id = $thread->id;
		}
		$this->user_id = $data['user_id'];
		$this->message = $data['message'];
		$this->created_at = date('Y-m-d H:i:s');
		return $this;
	}

}