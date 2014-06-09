<?php
namespace Assets\Admin;

/**
 * Group class is used to keep track of a group of routes with similar aspects (the same controller, the same f3-app and etc)
 */
class Routes extends \Dsc\Routes\Group
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Initializes all routes for this group
     * NOTE: This method should be overriden by every group
     */
    public function initialize()
    {
        $this->setDefaults(array(
            'namespace' => '\Assets\Admin\Controllers',
            'url_prefix' => '/admin'
        ));
        
        $this->addSettingsRoutes();

        $this->addCrudGroup('Assets', 'Asset');
        
        $this->add('/asset/rethumb/@id', 'GET', array(
            'controller' => 'Asset',
            'action' => 'rebuildThumb'
        ));
        
        $this->add('/asset/handleUrl', 'POST', array(
            'controller' => 'Asset',
            'action' => 'handleUrl'
        ));
        
        $this->add('/asset/handleUrlS3', 'POST', array(
            'controller' => 'Asset',
            'action' => 'handleUrlS3'
        ));
        
        $this->add('/asset/handleTraditional', 'POST', array(
            'controller' => 'Asset',
            'action' => 'handleTraditional'
        ));
        
        $this->add('/asset/handleS3', array(
            'POST',
            'DELETE'
        ), array(
            'controller' => 'Asset',
            'action' => 'handleS3'
        ));
        
        $this->add('/asset/handleS3/@id', 'DELETE', array(
            'controller' => 'Asset',
            'action' => 'handleS3'
        ));
        
        $this->add( '/asset/moveToS3/@id', 'GET', array(
        	'controller' => 'Asset',
        	'action'	=> 'moveToS3',
        ) );
        
        $this->add( '/assets/moveToS3', 'POST', array(
        		'controller' => 'Assets',
        		'action'	=> 'moveToS3',
        ) );
        
        // element routes
        $this->add('/assets/element/@id', array(
            'GET',
            'POST'
        ), array(
            'controller' => 'Assets',
            'action' => 'element'
        ));
        
        $this->add('/assets/element/@id/page/@page', array(
            'GET',
            'POST'
        ), array(
            'controller' => 'Assets',
            'action' => 'element'
        ));
        
        $this->add('/assets/element/image/@id', array(
            'GET',
            'POST'
        ), array(
            'controller' => 'Assets',
            'action' => 'elementImage'
        ));
        
        $this->add('/assets/element/image/@id/page/@page', array(
            'GET',
            'POST'
        ), array(
            'controller' => 'Assets',
            'action' => 'elementImage'
        ));
    }
}