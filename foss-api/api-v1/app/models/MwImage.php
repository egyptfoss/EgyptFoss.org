<?php

class MwImage extends WikiBaseModel {

    protected $connection = 'mediawiki';
    protected $table = 'image';
    protected $attributes = array(
      'img_metadata' => "",
      'img_description' => "",
      'img_user_text' => "",
    );

    public function __construct($language="en",array $attributes = array())
    {
      parent::__construct($language, $attributes);
    }
    

    public function addImage($fileData, $loggedin_user, $uploadfile) {
    $user = User::find($loggedin_user->user_id);
    $type = explode('/', $fileData['type']);
    list($width, $height, $img_type, $attr) = getimagesize($uploadfile);


    $sha1Base36 = sha1_file($uploadfile);
    if ($sha1Base36 !== false) {
      $sha1Base36 = mw_helper::wfBaseConvert($sha1Base36, 16, 36, 31);
    }
    $this->img_name = ucfirst($fileData['name']);
    $this->img_size = $fileData['size'];
    $this->img_width = $width;
    $this->img_height = $height;
    $this->img_metadata = serialize($fileData);
    $this->img_bits = 8;
    $this->img_media_type = "BITMAP";
    $this->img_major_mime = $type[0];
    $this->img_minor_mime = $type[1];
    //$this->img_description = null;
    $this->img_user = $user->ID;
    $this->img_user_text = $user->user_login;
    $this->img_timestamp = date('YmdHis');
    $this->img_sha1 = $sha1Base36;

    return $this;
  }

  
}
