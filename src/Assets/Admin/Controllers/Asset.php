<?php 
namespace Assets\Admin\Controllers;

use Aws\S3\S3Client;

class Asset extends \Admin\Controllers\BaseAuth 
{
    use \Dsc\Traits\Controllers\CrudItem;

    protected $list_route = '/admin/assets';
    protected $create_item_route = '/admin/asset/create';
    protected $get_item_route = '/admin/asset/read/{id}';    
    protected $edit_item_route = '/admin/asset/edit/{id}';
    
    public function handleS3()
    {
        $app = \Base::instance();
        
        $options = array(
            'clientPrivateKey' => $app->get('aws.clientPrivateKey'),
            'serverPublicKey' => $app->get('aws.serverPublicKey'),
            'serverPrivateKey' => $app->get('aws.serverPrivateKey'),
            'expectedBucketName' => $app->get('aws.bucketname'),
            'expectedMaxSize' => $app->get('aws.maxsize'), 
            'cors_origin' => 'http://dev.dioscouri.com' // TODO set this automatically
        );
        
        $handler = new \Fineuploader\S3\Handler($options);
        
        $method = $handler->getRequestMethod();
        
        // This first conditional will only ever evaluate to true in a
        // CORS environment
        if ($method == 'OPTIONS') {
            $handler->handlePreflight();
        }
        // This second conditional will only ever evaluate to true if
        // the delete file feature is enabled
        else if ($method == "DELETE") {
            $handler->handlePreflightedRequest(); // only needed in a CORS environment
            $handler->deleteObject();
        }
        // This is all you really need if not using the delete file feature
        // and not working in a CORS environment
        else if	($method == 'POST') {
            $handler->handlePreflightedRequest();
        
            // Assumes the successEndpoint has a parameter of "success" associated with it,
            // to allow the server to differentiate between a successEndpoint request
            // and other POST requests (all requests are sent to the same endpoint in this example).
            // This condition is not needed if you don't require a callback on upload success.
            if (isset($_REQUEST["success"])) {
                $response = $handler->verifyFileInS3($handler->shouldIncludeThumbnail());
                if (empty($response['error'])) {
                    // store it in the assets model
                    $bucket = $_POST["bucket"];
                    $key = $_POST["key"];
                    $uuid = $_POST["uuid"];
                    $name = $_POST["name"];

                    $pathinfo = pathinfo($name);
                    
                    $objectInfo = $handler->getObjectInfo($bucket, $key);
                    $objectInfoValues = $objectInfo->getAll();
                    $object = $handler->getS3Client()->getObject(array( 'Bucket'=>$bucket, 'Key'=>$key )); 
                    
                    $model = $this->getModel();
                    $url = $handler->getS3Client()->getObjectUrl($bucket, $key);
                    
                    $thumb = null;
                    if ( $thumb_binary_data = $model->getThumb( (string) $object['Body'], $pathinfo['extension'] )) {
                        $thumb = new \MongoBinData( $thumb_binary_data, 2 );
                    }
                    
                    $values = array(
                        'storage' => 's3',
                        'contentType' => $objectInfoValues['ContentType'],
                        'md5' => md5_file( $url ),
                        'thumb' => $thumb,
                        'url' => $url,
                        'length' => $objectInfoValues['ContentLength'],
                        'metadata' => array(
                            "title" => \Joomla\String\Normalise::toSpaceSeparated( $this->inputfilter->clean( $name ) )
                        ),
                        'details' => array(
                            'bucket' => $bucket,
                            'key' => $key,
                            'filename' => $name,
                            'uuid' => $uuid
                        ) + $objectInfoValues
                    );

                    if (empty($values['metadata']['title'])) {
                        $values['metadata']['title'] = $values['md5'];
                    }
                    $values['metadata']['slug'] = $model->generateSlug( $values );
                    
                    $mapper = $model->create( $values );
                    $response["asset_id"] = (string) $mapper->id;
                    $response["slug"] = $values['metadata']['slug'];
                }
                
                echo json_encode($response);
                
            }
            else {
                $handler->signRequest();
            }
        }        
    }
    
    public function handleTraditional()
    {
        $app = \Base::instance();
        $files_path = $app->get('TEMP') . "files";
        $chunks_path = $app->get('TEMP') . "chunks";
        
        if (!file_exists($chunks_path)) {
            mkdir( $chunks_path, \Base::MODE, true );
        }
        
        if (!file_exists($files_path)) {
            mkdir( $files_path, \Base::MODE, true );
        }
        
        $uploader = new \Fineuploader\Traditional\Handler;
        
        // Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $uploader->allowedExtensions = array(); // all files types allowed by default
        
        // Specify max file size in bytes.
        $uploader->sizeLimit = 10 * 1024 * 1024; // default is 10 MiB
        
        // Specify the input name set in the javascript.
        $uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default
        
        // If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
        $uploader->chunksFolder = $chunks_path;

        $method = $_SERVER["REQUEST_METHOD"];
        if ($method == "POST") {
            header("Content-Type: text/plain");
        
            // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
            $result = $uploader->handleUpload( $files_path );
        
            // To return a name used for uploaded file you can use the following line.
            $result["uploadName"] = $uploader->getUploadName();
            
            $result["originalName"] = $uploader->getName();
            
            // was upload successful?
            if (!empty($result['success'])) 
            {
                // OK, we have the file in the tmp folder, let's now fire up the assets model and save it to Mongo
                $model = $this->getModel();
                $db = $model->getDb();
                $grid = $db->getGridFS( $model->getGridFSCollectionName() );
                
                // The file's location in the File System
                $filename = $result["uploadName"];
                
                $pathinfo = pathinfo($filename);
                $buffer = file_get_contents( $files_path . "/" . $filename );
                
                $originalname = $result["originalName"];
                $pathinfo_original = pathinfo($originalname);

                $thumb = null;
                if ( $thumb_binary_data = $model->getThumb( $buffer, $pathinfo['extension'] )) {
                    $thumb = new \MongoBinData( $thumb_binary_data, 2 );
                }
                
                $values = array(
                    'storage' => 'gridfs',
                    'contentType' => $model->getMimeType( $buffer ),
                    'md5' => md5_file( $files_path . "/" . $filename ),
                    'thumb' => $thumb,
                    'url' => null,
                    'metadata' => array(
                        "title" => \Joomla\String\Normalise::toSpaceSeparated( $this->inputfilter->clean( $originalname ) )
                    ),
                    'details' => array(
                        "filename" => $originalname
                     )
                );
                                
                if (empty($values['metadata']['title'])) {
                    $values['metadata']['title'] = $values['md5'];
                }
                
                $values['metadata']['slug'] = $model->generateSlug( $values );
                $values['url'] = "/asset/" . $values['metadata']['slug']; 

                // save the file
                if ($storedfile = $grid->storeFile( $files_path . "/" . $filename, $values )) 
                {
                    $mapper = $model->getMapper();
                    $mapper->load(array('_id'=>$storedfile));
                    $mapper = $model->update( $mapper, $values );
                }
                
                // $storedfile has newly stored file's Document ID
                $result["asset_id"] = (string) $storedfile;
                $result["slug"] = $mapper->{'metadata.slug'};
            } 
            
            echo json_encode($result);
        }
        else {
            header("HTTP/1.0 405 Method Not Allowed");
        }
        
    }
    
    protected function getModel() 
    {
        $model = new \Assets\Admin\Models\Assets;
        return $model; 
    }
    
    protected function getItem() 
    {
        $f3 = \Base::instance();
        $id = $this->inputfilter->clean( $f3->get('PARAMS.id'), 'alnum' );
        $model = $this->getModel()
            ->setState('filter.id', $id);

        try {
            $item = $model->getItem();
        } catch ( \Exception $e ) {
            \Dsc\System::instance()->addMessage( "Invalid Item: " . $e->getMessage(), 'error');
            $f3->reroute( $this->list_route );
            return;
        }

        return $item;
    }
    
    protected function displayCreate() 
    {
        $f3 = \Base::instance();
        $f3->set('pagetitle', 'Upload New Assets');

        $all_tags = $this->getModel()->getTags();
        \Base::instance()->set('all_tags', $all_tags );
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->renderTheme('Assets/Admin/Views::assets/create.php');
    }
    
    protected function displayEdit()
    {
        $f3 = \Base::instance();
        $f3->set('pagetitle', 'Edit Asset');

        $all_tags = $this->getModel()->getTags();
        \Base::instance()->set('all_tags', $all_tags );
        
		$view = \Dsc\System::instance()->get('theme');
		$view->event = $view->trigger( 'onDisplayAdminAssetEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
        echo $view->renderTheme('Assets/Admin/Views::assets/edit.php');
    }
    
    /**
     * This controller doesn't allow reading, only editing, so redirect to the edit method
     */
    protected function doRead(array $data, $key=null) 
    {
        $f3 = \Base::instance();
        $id = $this->getItem()->get( $this->getItemKey() );
        $route = str_replace('{id}', $id, $this->edit_item_route );
        $f3->reroute( $route );
    }
    
    protected function displayRead() {}
    
    public function rebuildThumb()
    {
        $f3 = \Base::instance();
        
    	$model = $this->getModel();
    	$db = $model->getDb();
    	$gridfs = $db->getGridFS( $model->getGridFSCollectionName() );
    	
    	$id = $this->inputfilter->clean( $f3->get('PARAMS.id'), 'alnum' );
    	$item = $this->getItem();
    	
    	if (empty($item->id)) {
    		return false;
    	}
    	
    	$length = $item->{"length"};
    	$chunkSize = $item->{"chunkSize"};
    	$chunks = ceil( $length / $chunkSize );
    	
    	$collChunkName = $model->getGridFSCollectionName() . ".chunks";
    	$collChunks = $model->getDb()->{$collChunkName};
    	
    	$buffer = null;
    	for( $i=0; $i<$chunks; $i++ )
    	{
    	    $chunk = $collChunks->findOne( array( "files_id" => $item->_id, "n" => $i ) );
    	    $buffer .= (string) $chunk["data"]->bin;
    	}    	
    	
    	$thumb = null;
    	if ( $thumb_binary_data = $model->getThumb( $buffer )) {
    	    $thumb = new \MongoBinData( $thumb_binary_data, 2 );
    	}

    	
    	$item->set( 'thumb', $thumb );
    	if ($item->save()) {
    	    \Dsc\System::instance()->addMessage('Thumb recreated.');
    	} else {
    	    \Dsc\System::instance()->addMessage('There was an error recreating the thumb.');
    	}
    	
    	$this->setRedirect( $this->list_route );
    	
    	if ($route = $this->getRedirect()) {
    	    \Base::instance()->reroute( $route );
    	}
    	    	
    	return $this;

    }
}