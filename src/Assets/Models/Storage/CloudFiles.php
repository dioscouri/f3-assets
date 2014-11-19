<?php 

namespace Assets\Models\CDN;

use OpenCloud\Rackspace;

Class CloudFiles implements StorageInterface
{	
	var $client = null;
	var $container = null;
	var $region = 'DFW';
	
	
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
		return $this;
	}
	
	
	public function store( $remoteFileName,  $local) {
		$handle = fopen($local, 'r');
		$this->container->uploadObject($remoteFileName, $handle);
		return $this;
	}

	public function makeContainer($name) {
		
	}
	
	public function getContainer($name, $create = true){
		// Obtain an Object Store service object from the client.
		$objectStoreService = $this->client->objectStoreService(null, $this->region);
		
		// Create a container for your objects (also referred to as files).
		$this->container = $objectStoreService->getContainer($name);
		
		return $this;	
	}
	
	public function deleteContainer($bool = false){
		
	}
	public function deleteAsset($bool = false){
		
	}
	public function getContent(){
		
	}
	public function getUrl(){
		
	}

}








?>