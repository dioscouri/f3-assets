<?php
namespace Assets\Models;

class Settings extends \Dsc\Mongo\Collections\Settings
{
    public function isS3Enabled()
    {
        if (!class_exists('\Aws\S3\S3Client'))
        {
            return false;
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
        ) 
        { 
            $result = false;        	
        } 
        else 
        {
        	$result = true;
        }
        
        return $result;
    }
}