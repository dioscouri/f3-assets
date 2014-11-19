<?php 
namespace Assets\Models;

class Assets extends \Dsc\Mongo\Collections\Assets 
{
	var $__resource = null;
	var $__storage = null;
	
	public function setStorage($type = 'gridfs') {

		//TODO check for a default from settings or config?
		
		switch (strtolower($type)) {
			case "amazons3":
				$storage = new \Assets\Models\Storage\AmazonS3;
				break;
			case "cloudfiles":
				$storage = new \Assets\Models\Storage\CloudFiles;
				break;
			case "gridfs":
			default:
				$storage = new \Assets\Models\Storage\GridFs;
		}
		 $this->__storage = $storage;
		 
		return $this;
	}
	
	private  function store() {
		//store this asset
		$this->__storage->store($this);
	
	}
	
	public  function add() {
		
	}
	
	protected function beforeSave()
	{	
		//TRY TO DO THE STORAGE OF THE RESOURCE IN The STORAGE, else FAIL.
		$this->store();
		
		
		
		if (empty($this->type)) {
			$this->type = $this->__type;
		}
	
		if (empty($this->md5 ) )
		{
			if (!empty($this->{'s3.ETag'})) {
				$this->md5 = str_replace('"', '', $this->{'s3.ETag'} );
			}
			elseif (!empty($this->filename) && file_exists($this->filename)) {
				$this->md5 = md5_file( $this->filename );
			}
			else {
				$this->md5 = md5( $this->slug );
			}
		}
		
		
		
		 
		return parent::beforeSave();
	}
	
	
	
	
}

//
//