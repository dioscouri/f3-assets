<?php 

namespace Assets\Models\Storage;


Class GridFs implements StorageInterface
{	
	var $client = null;
	var $container = null;
		
	function __construct() {
		$this->setClient();
	}
	
	
	public function setClient($array = array()) {
		//set client to gridfs
		return $this;
	}
	
	
	public function store( $remoteFileName,  $local) {
		
		//$grid->storeFile( $file_path, $values )
		return $this;
	}

	public function makeContainer($name) {
		
	}
	
	public function getContainer($name, $create = true){		
		
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