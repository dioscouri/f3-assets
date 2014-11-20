<?php 

namespace Assets\Models\CDN;

use OpenCloud\Rackspace;

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
		$this->container = $this->setContainer($name);
		
		$this->storeRegion = $this->client->objectStoreService(null, $app->get('cdn.region'));
		
		return $this;
	}
	
	public function getClient() {

		return $this->client;
	}
	
	
	public function createObject($remoteFileName,  $local) {
		$handle = fopen($local, 'r');
		$this->container->uploadObject($remoteFileName, $handle);
		return $this;
	}

	public function makeContainer($name) {
		
	}
	
	public function setContainer($name, $create = true){
		// Obtain an Object Store service object from the client.
		$objectStoreService = $this->client->objectStoreService(null, $this->region);
		
		// Create a container for your objects (also referred to as files).
		$this->container = $objectStoreService->getContainer($name);
		
		return $this;	
	}
	
	public function getContainer($name, $create = true){
		
		return $this->container;
	}
	
	public function getObject($remoteFileName) {
		$this->object = $this->container->getObject($remoteFileName);
		return $this->object;
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
		return $this->object->getPublicUrl();
		 
	}

}








?>