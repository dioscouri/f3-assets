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

	//METHOD for queueing assets to be uploaded to CDN by queuer
	
	public static function gridfsToCDN($asset_id) {
		try {
			//get the current asset
			$asset = (new \Assets\Models\Assets)->setState('filter.slug', $asset_id)->getItem();
			if(!empty($asset->id)) {
				//if we new the servers full URL we could  just serve the asset url to  get object
				$cdn = new static;
				
				$url = \Base::instance()->get('cdn.siteURL'). '/asset/'. $asset->slug;
				
				$cdn->createObject($asset->filename, $url);
				$asset->set('url', $cdn->getObjectUrl());
				
				$thumbnailPath = '/thumbs' .$asset->filename;
				$thumbUrl = \Base::instance()->get('cdn.siteURL'). '/asset/thumb/'. $asset->slug;
				
				$cdn->createObject($thumbnailPath, $thumbUrl);
				$asset->set('thumb', $cdn->getObjectUrl());
				
				$asset->set('storage', 'cloudfiles');
				$asset->save();
					
					
				//store the data from the original asset
				$oldData = $asset->cast();
				unset($oldData['_id']);
				unset($oldData['length']);
				unset($oldData['chunkSize']);
					
				//MD5 needs to change
				$oldData['md5'] = md5($oldData['title'] . uniqid());
				//save grid data
					
				//DELETE THIS ASSET
				$asset->remove();
					
				//MAKE NEW ASSET WITH NEW ID BUT SAME SLUG
				$model = new \Assets\Models\Assets;
				$model->bind( $oldData );
				$model->set('storage', 'cloudfiles');
				$model->save();
			}
		} catch (\Exception $e) {
			echo $e->getMessage(); die();
		}
	
	}
	
	
	//METHOD for queueing assets to be uploaded to CDN by queuer
	
	public static function gridfsToCDNThumbs($asset_id) {
		try {
			//get the current asset
			$asset = (new \Assets\Models\Assets)->setState('filter.slug', $asset_id)->getItem();
			
			if(!empty($asset->slug)) {
				//if we new the servers full URL we could  just serve the asset url to  get object
				$cdn = new static;
	
				
	
				$thumbnailPath = '/thumbs/' .$asset->filename;
				$thumbnailPath = str_replace('//', '/', $thumbnailPath);
				$thumbUrl = \Base::instance()->get('cdn.siteURL'). '/asset/thumb/'. $asset->slug;
	
				$cdn->createObject($thumbnailPath, $thumbUrl);
		
				$asset->save();
					
					
				//store the data from the original asset
				$oldData = $asset->cast();
				unset($oldData['_id']);
				unset($oldData['length']);
				unset($oldData['chunckSize']);
				unset($oldData['thumb']);
					
				//MD5 needs to change
				$oldData['md5'] = md5($oldData['title'] . uniqid());
				//save grid data
					
				//DELETE THIS ASSET
				$asset->remove();
					
				//MAKE NEW ASSET WITH NEW ID BUT SAME SLUG
				$model = new \Assets\Models\Assets;
					
				$model->bind( $oldData );
				$model->set('thumb', $cdn->getObjectUrl());
				$model->set('storage', 'cloudfiles');
				$model->save();
			}else {
			echo 'no product';
		}
			
		} catch (\Exception $e) {
			echo $e->getMessage(); die();
		} 
	
	}
	
	
}








?>