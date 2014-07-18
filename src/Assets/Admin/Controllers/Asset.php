<?php 
namespace Assets\Admin\Controllers;

use Aws\S3\S3Client;

class Asset extends \Admin\Controllers\BaseAuth 
{
    use \Dsc\Traits\Controllers\CrudItemCollection;

    protected $crud_item_key = 'slug';
    protected $list_route = '/admin/assets';
    protected $create_item_route = '/admin/asset/create';
    protected $get_item_route = '/admin/asset/read/{id}';    
    protected $edit_item_route = '/admin/asset/edit/{id}';
    
    public function handleS3()
    {
        $settings = \Assets\Models\Settings::fetch();
         
        if (!$settings->isS3Enabled()) {
            $response = array(
            	'error' => 'Amazon S3 is not enabled'
            );
            echo json_encode($response);
            return;
        }
                
        $app = \Base::instance();
        
        $options = array(
            'clientPrivateKey' => $app->get('aws.clientPrivateKey'),
            'serverPublicKey' => $app->get('aws.serverPublicKey'),
            'serverPrivateKey' => $app->get('aws.serverPrivateKey'),
            'expectedBucketName' => $app->get('aws.bucketname'),
            'expectedMaxSize' => $app->get('aws.maxsize'), 
            'cors_origin' => $app->get('SCHEME') . "://" . $app->get('HOST') . $app->get('BASE')
        );
        
        if (empty($options['clientPrivateKey'])
            || empty($options['serverPublicKey'])
            || empty($options['serverPrivateKey'])
            || empty($options['expectedBucketName'])
            || empty($options['expectedMaxSize'])
        ) {
            $response = array(
            	'error' => 'Invalid configuration settings'
            );
            echo json_encode($response);
            return;
        }
        
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
        	$handler->getS3Client()->deleteObject(array(
        			'Bucket' => $_REQUEST['bucket'],
        			'Key' => $_REQUEST['key']
        	));
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
                        "title" => \Joomla\String\Normalise::toSpaceSeparated( $model->inputfilter()->clean( $name ) ),
                        'filename' => $name,
                        's3' => array(
                            'bucket' => $bucket,
                            'key' => $key,                            
                            'uuid' => $uuid
                        ) + $objectInfoValues
                    );

                    if (empty($values['title'])) {
                        $values['title'] = $values['md5'];
                    }
                    
                    $model->insert( $values );
                    $response["asset_id"] = (string) $model->get('id');
                    $response["slug"] = $model->{'slug'};
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
                $grid = $model->collectionGridFS();
                
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
           			"title" => \Joomla\String\Normalise::toSpaceSeparated( $model->inputfilter()->clean( $originalname ) ),
                    "filename" => $originalname,
                );
                                
                if (empty($values['title'])) {
                    $values['title'] = $values['md5'];
                }
                // save the file
                if ($storedfile = $grid->storeFile( $files_path . "/" . $filename, $values )) 
                {
                	$model->load(array('_id'=>$storedfile));
                	$model->bind( $values );
	                $model->{'slug'} = $model->generateSlug();
     	            $model->save();
                }
                // $storedfile has newly stored file's Document ID
                $result["asset_id"] = (string) $storedfile;
                $result["slug"] = $model->{'slug'};
            } 
            
            echo json_encode($result);
        }
        else {
            header("HTTP/1.0 405 Method Not Allowed");
        }
        
    }
    
    public function handleUrl()
    {
        $f3 = \Base::instance();
        $url = $this->input->get( 'upload_url', null, 'default' );

        $custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'assets.handleUrl.redirect' );
        $redirect = $custom_redirect ? $custom_redirect : $this->create_item_route;
        
        if (!empty($url)) {
            try {
                $result = \Assets\Admin\Models\Assets::createFromUrl( $url );
                if (!empty($result['error'])) {
                	throw new \Exception( $result['message'] );
                }
            }
            catch (\Exception $e) {
                \Dsc\System::instance()->addMessage( $e->getMessage(), 'error');
                $f3->reroute( $redirect );
                return;            	
            }            
        }
        
        \Dsc\System::instance()->addMessage( "Uploaded.  Edit here: <a href='./admin/asset/edit/".$result["asset_id"]."'>./admin/asset/edit/".$result["asset_id"]."</a>" );
        $f3->reroute( $redirect );
        return;        
    }
    
    public function handleUrlS3()
    {
        $f3 = \Base::instance();
        $url = $this->input->get( 'upload_url', null, 'default' );
    
        $custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'assets.handleUrl.redirect' );
        $redirect = $custom_redirect ? $custom_redirect : $this->create_item_route;
    
        if (!empty($url)) {
            try {
                $asset = \Assets\Admin\Models\Assets::createFromUrlToS3( $url );
            }
            catch (\Exception $e) {
                \Dsc\System::instance()->addMessage( $e->getMessage(), 'error');
                $f3->reroute( $redirect );
                return;
            }
        }
    
        \Dsc\System::instance()->addMessage( "Uploaded.  Edit here: <a href='./admin/asset/edit/" . $asset->id . "'>./admin/asset/edit/" . $asset->id . "</a>" );
        $f3->reroute( $redirect );
        return;
    }
    
    protected function getModel() 
    {
        $model = new \Assets\Admin\Models\Assets;
        return $model; 
    }
    
    protected function getItem() 
    {
        $f3 = \Base::instance();
        $model = $this->getModel();
        $id = $model->inputfilter()->clean( $f3->get('PARAMS.id'), 'string' );
        $model->setState('filter.slug', $id);

        try {
            $item = $model->getItem();
        } catch ( \Exception $e ) {
            \Dsc\System::instance()->addMessage( "Invalid Item: " . $e->getMessage(), 'error');
            $f3->reroute( $this->list_route );
            return;
        }

        return $item;
    }
    
    public function rotate() {
    	$asset = $this->getItem();
    	$length = $asset->length;
    	$chunkSize =$asset->chunkSize;
    	$chunks = ceil( $length / $chunkSize );
    	 
    	$collChunkName = $asset->collectionNameGridFS() . ".chunks";
    	$collChunks = $asset->getDb()->{$collChunkName};
    	$binImagedata = null;
    	for( $i=0; $i<$chunks; $i++ )
    	{
    		$chunk = $collChunks->findOne( array( "files_id" => $asset->_id, "n" => $i ) );
    		$binImagedata .=  $chunk["data"]->bin;
    	}
    	
    	$dscImage = New \Dsc\Image(\imagecreatefromstring($binImagedata));
    	
    	$dscImage->rotate($this->app->get('PARAMS.degrees'), null, false);
    	$buffer = $dscImage->toBuffer();
    	
    	$asset->replace($buffer);
    	$this->app->reroute('/admin/asset/edit/'.$this->app->get('PARAMS.id'));
    	
    }
    
    protected function displayCreate() 
    {
        $f3 = \Base::instance();

        $all_tags = $this->getModel()->getTags();
        \Base::instance()->set('all_tags', $all_tags );
        
        $this->app->set('meta.title', 'Upload New Assets');
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->renderTheme('Assets/Admin/Views::assets/create.php');
    }
    
    protected function displayEdit()
    {
        $f3 = \Base::instance();

        $all_tags = $this->getModel()->getTags();
        \Base::instance()->set('all_tags', $all_tags );

        $this->app->set('meta.title', 'Edit Asset');
        
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
    
    /**
     * 
     * @return \Assets\Admin\Controllers\Asset
     */
    public function rebuildThumb()
    {
        $custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'asset.rethumb.redirect' );
        $redirect = $custom_redirect ? $custom_redirect : $this->list_route;
        
    	$item = $this->getItem();    	
    	if (empty($item->id)) 
    	{
    	    \Dsc\System::addMessage('There was an error recreating the thumb', 'error');
    	    \Dsc\System::addMessage('Invalid Item', 'error');
    		$this->app->reroute( $redirect );
    	}
    	
    	try {
    	    $item->rebuildThumb();
    	    \Dsc\System::addMessage('Thumb recreated', 'success');
    	}
    	catch (\Exception $e) {
    	    \Dsc\System::addMessage('There was an error recreating the thumb.', 'error');
    	    \Dsc\System::addMessage($e->getMessage(), 'error');
    	}
    	 
    	$this->app->reroute( $redirect );
    }
    
    /**
     * 
     * @throws \Exception
     */
    public function moveToS3()
    {
        $settings = \Assets\Models\Settings::fetch();
        
    	$f3 = \Base::instance();
    	
    	$model = $this->getModel();
    	$id = $model->inputfilter()->clean( $f3->get('PARAMS.id'), 'alnum' );
    	$item = $this->getItem();

    	try{
    	    if (!$settings->isS3Enabled()) {
    	        throw new \Exception('Amazon S3 is not enabled');
    	    }
    	        	    
    		$item->moveToS3();
    		\Dsc\System::instance()->addMessage('This asset was successfully uploaded to Amazon S3.');
    		
    		$route = str_replace('{id}', $id, $this->edit_item_route );
    		$f3->reroute( $route );    		
    		
    	} catch( \Exception $e ){
    		\Dsc\System::instance()->addMessage('This assets upload failed.', 'error');
    		\Dsc\System::addMessage( $e->$e->getMessage(), 'error' );
    	}
    	
    	\Base::instance()->reroute( $this->list_route );
    }
}