<?php

class AccessToken extends BaseModel {
	protected $table = 'accesstokens';

	public function addToken($token, $user_id, $date) {
		$this->access_token = $token;
		$this->user_id = $user_id;
		$this->created_at = $date;
    return $this;
	}
}