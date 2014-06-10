<?php 
namespace Fineuploader\S3;

use Aws\S3\S3Client;

class Handler extends \Prefab 
{
    protected $clientPrivateKey;
    protected $cors_origin;
    protected $serverPublicKey;
    protected $serverPrivateKey;
    protected $expectedBucketName;
    protected $expectedMaxSize;
    
    protected $objects = array();
    
    public function __construct($config=array()) 
    {
        foreach ($config as $key => $value ) {
            $this->$key = $value;
        }
        //\FB::log($this);
    }
    
    // This will retrieve the "intended" request method.  Normally, this is the
    // actual method of the request.  Sometimes, though, the intended request method
    // must be hidden in the parameters of the request.  For example, when attempting to
    // send a DELETE request in a cross-origin environment in IE9 or older, it is not
    // possible to send a DELETE request.  So, we send a POST with the intended method,
    // DELETE, in a "_method" parameter.
    function getRequestMethod() {
        global $HTTP_RAW_POST_DATA;
    
        // This should only evaluate to true if the Content-Type is undefined
        // or unrecognized, such as when XDomainRequest has been used to
        // send the request.
        if(isset($HTTP_RAW_POST_DATA)) {
            parse_str($HTTP_RAW_POST_DATA, $_POST);
        }
    
        if (!empty($_POST['_method'])) {
            return $_POST['_method'];
        }
    
        return $_SERVER['REQUEST_METHOD'];
    }
    
    // Only needed in cross-origin setups
    function handlePreflightedRequest() {
        // If you are relying on CORS, you will need to adjust the allowed domain here.
        header('Access-Control-Allow-Origin: ' . $this->cors_origin );
    }
    
    // Only needed in cross-origin setups
    function handlePreflight() {
        $this->handlePreflightedRequest();
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');
    }
    
    function getS3Client() {
    
        return S3Client::factory(array(
                'key' => $this->serverPublicKey,
                'secret' => $this->serverPrivateKey
        ));
    }
    
    // Only needed if the delete file feature is enabled
    function deleteObject() {
        $this->getS3Client()->deleteObject(array(
                'Bucket' => $_POST['bucket'],
                'Key' => $_POST['key']
        ));
    }
    
    function signRequest() {
        header('Content-Type: application/json');
    
        $responseBody = file_get_contents('php://input');
        $contentAsObject = json_decode($responseBody, true);
        $jsonContent = json_encode($contentAsObject);
    
        $headersStr = !empty($contentAsObject["headers"]) ? $contentAsObject["headers"] : null;
        if ($headersStr) {
            $this->signRestRequest($headersStr);
        }
        else {
            $this->signPolicy($jsonContent);
        }
    }
    
    function signRestRequest($headersStr) {
        if ($this->isValidRestRequest($headersStr)) {
            $response = array('signature' => $this->sign($headersStr));
            echo json_encode($response);
        }
        else {
            echo json_encode(array("invalid" => true));
        }
    }
    
    function isValidRestRequest($headersStr) {
        $expectedBucketName = $this->expectedBucketName;
    
        $pattern = "/\/$expectedBucketName\/.+$/";
        preg_match($pattern, $headersStr, $matches);
    \FB::log(count($matches), 'count matches');
        return count($matches) > 0;
    }
    
    function signPolicy($policyStr) {
        $policyObj = json_decode($policyStr, true);
    
        if ($this->isPolicyValid($policyObj)) {
            $encodedPolicy = base64_encode($policyStr);
            $response = array('policy' => $encodedPolicy, 'signature' => $this->sign($encodedPolicy));
            echo json_encode($response);
        }
        else {
            echo json_encode(array("invalid" => true));
        }
    }
    
    function isPolicyValid($policy) {
        $expectedMaxSize = $this->expectedMaxSize; 
        $expectedBucketName = $this->expectedBucketName;
    
        $conditions = $policy["conditions"];
        $bucket = null;
        $parsedMaxSize = null;
    
        for ($i = 0; $i < count($conditions); ++$i) {
            $condition = $conditions[$i];
    
            if (isset($condition["bucket"])) {
                $bucket = $condition["bucket"];
            }
            else if (isset($condition[0]) && $condition[0] == "content-length-range") {
                $parsedMaxSize = $condition[2];
            }
        }
    
        return $bucket == $expectedBucketName && $parsedMaxSize == (string)$expectedMaxSize;
    }
    
    function sign($stringToSign) {
    
        return base64_encode(hash_hmac(
                'sha1',
                $stringToSign,
                $this->clientPrivateKey,
                true
        ));
    }
    
    // This is not needed if you don't require a callback on upload success.
    function verifyFileInS3($includeThumbnail) {
        $expectedMaxSize = $this->expectedMaxSize;
    
        $bucket = $_POST["bucket"];
        $key = $_POST["key"];
    
        // If utilizing CORS, we return a 200 response with the error message in the body
        // to ensure Fine Uploader can parse the error message in IE9 and IE8,
        // since XDomainRequest is used on those browsers for CORS requests.  XDomainRequest
        // does not allow access to the response body for non-success responses.
        if ($this->getObjectSize($bucket, $key) > $expectedMaxSize) {
            // You can safely uncomment this next line if you are not depending on CORS
            //header("HTTP/1.0 500 Internal Server Error");
            $this->deleteObject();
            //echo json_encode(array("error" => "File is too big!"));
            $response = array("error" => "File is too big!");
        }
        else {
            $link = $this->getTempLink($bucket, $key);
            $response = array("tempLink" => $link);
    
            if ($includeThumbnail) {
                $response["thumbnailUrl"] = $link;
            }
    
           	//echo json_encode($response);
        }
        
        return $response;
    }
    
    // Provide a time-bombed public link to the file.
    function getTempLink($bucket, $key) {
        $client = $this->getS3Client();
        $url = "{$bucket}/{$key}";
        $request = $client->get($url);
    
        return $client->createPresignedUrl($request, '+15 minutes');
    }
    
    function getObjectSize($bucket, $key) {
        $objInfo = $this->getObjectInfo($bucket, $key);

        return $objInfo['ContentLength'];
    }
    
    // Return true if it's likely that the associate file is natively
    // viewable in a browser.  For simplicity, just uses the file extension
    // to make this determination, along with an array of extensions that one
    // would expect all supported browsers are able to render natively.
    function isFileViewableImage($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $viewableExtensions = array("jpeg", "jpg", "gif", "png");
    
        return in_array($ext, $viewableExtensions);
    }
    
    // Returns true if we should attempt to include a link
    // to a thumbnail in the uploadSuccess response.  In it's simplest form
    // (which is our goal here - keep it simple) we only include a link to
    // a viewable image and only if the browser is not capable of generating a client-side preview.
    function shouldIncludeThumbnail() {
        $filename = $_POST["name"];
        $isPreviewCapable = (!empty($_POST["isBrowserPreviewCapable"]) && $_POST["isBrowserPreviewCapable"] == "true") ? true : false;
        $isFileViewableImage = $this->isFileViewableImage($filename);
    
        return false;
        //return !$isPreviewCapable && $isFileViewableImage;
    }
    
    function getObjectInfo($bucket, $key) {
        if (empty($this->objects[$bucket])) 
        {
            $this->objects[$bucket] = array();
        }
        
        if (empty($this->objects[$bucket][$key])) 
        {
            $this->objects[$bucket][$key] = $this->getS3Client()->headObject(array(
                    'Bucket' => $bucket,
                    'Key' => $key
            ));            
        }

        return $this->objects[$bucket][$key];
    }
}
?>