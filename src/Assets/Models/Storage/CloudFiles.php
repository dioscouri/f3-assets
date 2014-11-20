<?php 

namespace Assets\Models\Storage;

use \OpenCloud\Rackspace;

Class CloudFiles implements StorageInterface
{	
	var $client = null;
	var $container = null;
	var $region = 'DFW';
	var $object = null;
	var $storeRegion =  null;
	
	function __construct() {
		$this->setClient();
	}
	
	public function setClient($array = array()) {
		$app =  \Base::instance();
		
		if(empty($array['username']) || empty($array['apiKey'])) {
			$array['username']= $app->get('cdn.username');
			$array['apiKey']= $app->get('cdn.apikey');
		} 
		
		$this->client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, $array);
		// 
		
		$this->storeRegion = $this->client->objectStoreService(null, $app->get('cdn.region'));
		
		$this->setContainer($app->get('cdn.container'));
		

		
		return $this;
	}
	
	public function getClient() {

		return $this->client;
	}
	
	public function createObject($remoteFileName,  $local) {
		$handle = fopen($local, 'r');
		
		$this->object = $this->container->uploadObject($remoteFileName, $handle);
		return $this;
	}
	
	public function deleteObject($remoteFileName){}
	public function updateObject($remoteFileName){}
	
	public function makeContainer($name) {
		
	}
	public function createContainer($name){}
	public function deleteContainer($bool = false){}
	
	public function setContainer($name, $create = true){
		
		
		// Create a container for your objects (also referred to as files).
		$this->container = $this->storeRegion->getContainer($name);
			
		
		return $this;	
	}
	
	public function getContainer($name, $create = true){
		
		return $this->container;
	}
	
	public function getObject($remoteFileName) {
		if(empty($this->object)) {
			$this->object = $this->container->getObject($remoteFileName);
		}
		
		return $this->object;
	}
	
	public function putObjectFromAsset($asset) {
		
		try {
			$upload = $asset->__resource;
			
			$this->createObject( $upload['name'], $upload['local']);
			
			$asset->url = $this->getObjectUrl();
			$asset->storage = 'cloudfiles';
			
		} catch (\Exception $e) {
			echo $e->getMessage(); 
			die();
		}
		
		return $asset;
	}
	
	/*
	 * Returns a stream
	 * https://developer.rackspace.com/docs/cloud-files/getting-started/?lang=php
	 */
	
    public function getObjectContents($remoteFileName = null) {
    	if(!empty($remoteFileName)) {
    		$this->getObject($remoteFileName);
    	}
    	return $this->object->getContent();
    	
    }
    
    
    /*
     * Returns a URL string
    * 
    */
	public function getObjectUrl($remoteFileName = null){
		if(!empty($remoteFileName)) {
			$this->getObject($remoteFileName);
		}
		return (string) $this->object->getPublicUrl();
		 
	}

	
	
	
}








?>