<?php

/**
 * Uploader.
 * 
 * @package uploader
 * @author Kerem Kayhan
 * @copyright UzakYakin(c)
 * @version 2010-10-20
 **/

require_once 'vendor/class.upload.php';

class Uploader 
{
	private $maxWidth = MAX_WIDTH;
	private $maxThumbWidth = MAX_THUMB_WIDTH;
	public $filename = "";
	
	public function __construct($maxWidth = NULL, $maxThumbWidth = NULL)
	{
		if( $maxWidth ){ $this->maxWidth = $maxWidth; }
		if( $maxThumbWidth ){ $this->maxThumbWidth = $maxThumbWidth; }
	}
	
	public function upload($image)
	{
		
		$filename = $image['name'] ;
    $file_ext = strrchr($filename, '.');
    $file_body = date('YmdHis');
		$file_full_name = $file_body."".$file_ext;
    $handle = new Upload($image);
    if ($handle->uploaded) {

      
      $handle->file_overwrite     = true;
      $handle->file_new_name_body = $file_body;

      // RESIZE FOR IMAGE
			list($width, $height, $type, $attr) = getimagesize($image['tmp_name']);
      
      if ( $width > $this->maxWidth ) {
      	
          $handle->image_resize       = true;
          $handle->image_x            = $this->maxWidth;
          $handle->image_ratio_y      = true;
      }
      $handle->process(UPLOADS);
      if ($handle->processed) {
      } else {
      }

      $orig_image = UPLOADS . $file_body.$file_ext;
      list($width, $height, $type, $attr) = getimagesize($orig_image);

      // RESIZE FOR THUMBNAIL
      
      $handle->file_overwrite     = false;
      $handle->file_new_name_body = $file_body."_thumb";
      list($width, $height, $type, $attr) = getimagesize($image['tmp_name']);
      
      if ( $width > $this->maxThumbWidth ) {
      	
          $handle->image_resize       = true;
          $handle->image_x            = $this->maxThumbWidth;
          $handle->image_ratio_y      = true;
      }        
      
      $handle->process(UPLOADS);
      
      $this->filename = $file_body.$file_ext;
      return $handle->processed; 

    }
		return false;
	}	
	
	public static function unlink($name, $extension)
	{	
		unlink(UPLOADS . $name . $extension);
		unlink(UPLOADS . $name . '_thumb' . $extension);
	}
	
	public static function getThumbnail($file_name)
	{	
		$file_name = explode('.', $file_name);
		return $file_name[0]."_thumb.".$file_name[1];
	}	
	
}

