<?php
namespace Assets\Models\Storage;


interface StorageInterface
{
	
	public function setClient($array = array());
	
    public function store($remoteFileName, $local);
    
    public function makeContainer($name);
    public function getContainer($name, $create = true);
    
    public function deleteContainer($bool = false);
    public function deleteAsset($bool = false);
    public function getContent();
    public function getUrl();
    
}