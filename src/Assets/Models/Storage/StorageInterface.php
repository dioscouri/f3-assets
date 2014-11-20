<?php
namespace Assets\Models\Storage;


interface StorageInterface
{
	
	public function setClient($array = array());
	public function getClient();
	
    public function createObject($remoteFileName, $local);
    public function deleteObject($remoteFileName);
    public function updateObject($remoteFileName);
    
    public function getObject($remoteFileName);
    public function getObjectContents($remoteFileName = null);
    public function getObjectUrl($remoteFileName = null);
    
    public function createContainer($name);
    public function getContainer($name, $create = true);
    public function deleteContainer($bool = false);
    
    
    
}