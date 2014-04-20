<?php   ?>
<?php
//get the last-modified-date of this very file
$lastModified=$flash->old("metadata.last_modified.time");
//get a unique hash of this file (etag)
$etagFile = $flash->old("md5");
//get the HTTP_IF_MODIFIED_SINCE header if set
$ifModifiedSince=(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
//get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
$etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

//set last-modified header
header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
//set etag-header
header("Etag: $etagFile");
//make sure caching is turned on
header('Cache-Control: public');
// set content type header
header( 'Content-type: ' . $flash->old('contentType') );

//check if page has changed. If not, send 304 and exit
if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$lastModified || $etagHeader == $etagFile)
{
       header("HTTP/1.1 304 Not Modified");
       exit;
}

// display the binary data
$length = $flash->old("length");
$chunkSize = $flash->old("chunkSize");
$chunks = ceil( $length / $chunkSize );

$collChunkName = $model->collectionNameGridFS() . ".chunks";
$collChunks = $model->getDb()->{$collChunkName};
 
for( $i=0; $i<$chunks; $i++ )
{
    $chunk = $collChunks->findOne( array( "files_id" => $item->_id, "n" => $i ) );
    echo (string) $chunk["data"]->bin;
}
?>