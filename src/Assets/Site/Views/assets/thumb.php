<?php
//get the last-modified-date of this very file
//$lastModified=$flash->old("last_modified");
//get a unique hash of this file (etag)
//$etagFile = $flash->old("md5");
//get the HTTP_IF_MODIFIED_SINCE header if set
//$ifModifiedSince=(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
//get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
//$etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

//set last-modified header
//header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
//set etag-header
//header("Etag: $etagFile");
//make sure caching is turned on
header('Cache-Control: public');
// set content type header
header( 'Content-type: image/jpeg' );

//check if page has changed. If not, send 304 and exit
if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$lastModified || $etagHeader == $etagFile)
{
       header("HTTP/1.1 304 Not Modified");
       exit;
}

// display the binary data
echo $flash->old('bin');
?>