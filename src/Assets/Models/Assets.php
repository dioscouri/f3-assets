<?php 
namespace Assets\Models;

class Assets extends \Dsc\Mongo\Collections\Assets 
{
	var $__resource = array();
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
	

	//setup data for putInStorage call
	public  function putBinaryInStorage() {
		
	}
	//setup data for putInStorage call
	public  function putFileInStorage() {
	
	}
	//setup data for putInStorage call
	public  function putUploadInStorage($array, $name = null) {
		try {
			if(!empty($array['tmp_name'])) { 
				$ext = pathinfo($array['name'], PATHINFO_EXTENSION);
				
				if(!empty($name)) {
					$array['name'] = $name . '.'.$ext;
				} else {
					if(!empty($this->slug)) {
						$array['name'] = $this->slug. '.'.$ext;
					}
					
				} 

				$this->setResource($array['tmp_name'], $array['name']);
			} else {
				 throw new \Exception('File is not a valid upload array');
			}
			
			return $this->putInStorage();
		} catch (\Exception $e) {
			
		}
		
		

	}
	
	public  function putInStorage() {
	$result = $this->__storage->putObjectFromAsset($this);
	
	return $result;
	}
	
	public  function setResource($file, $name,  $array = array()) {
		
		$this->__resource = array('local' => $file, 'name' => $name);
	
	}

	
	
	protected function beforeSave()
	{		
		
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