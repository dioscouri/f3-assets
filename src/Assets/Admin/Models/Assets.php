<?php 
namespace Assets\Models;

class Assets extends \Dsc\Mongo\Collections\Assets 
{
	
	
	public static function getStorage($type = 'gridfs') {
		 
		switch ($type) {
			case "AmazonS3":
				$storage = new \Assets\Models\Storage\AmazonS3;
				break;
			case "CloudFiles":
				$storage = new \Assets\Models\Storage\CloudFiles;
				break;
			case "gridfs":
			default:
				$storage = new \Assets\Models\Storage\GridFs;
		}
		 
		return $storage;
	}
	
	
	
	
}