<?php

namespace Assets\Admin;

/**
 * Group class is used to keep track of a group of routes with similar aspects (the same controller, the same f3-app and etc)
 */
class Routes extends \Dsc\Routes\Group{
	
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Initializes all routes for this group
	 * NOTE: This method should be overriden by every group
	 */
	public function initialize(){
		$this->setDefaults(
				array(
					'namespace' => '\Assets\Admin\Controllers',
					'url_prefix' => '/admin'
				)
		);
		
		$this->addCrudGroup( 'Assets', 'Asset');

		$this->add( '/asset/rethumb/@id', 'GET', array(
				'controller' => 'Asset',
				'action' => 'rebuildThumb'
		));

        // upload handlers
		$this->add( '/asset/handleTraditional', 'POST', array(
				'controller' => 'Asset',
				'action' => 'handleTraditional'
		));

		$this->add( '/asset/handleS3', array('POST', 'DELETE'), array(
				'controller' => 'Asset',
				'action' => 'handleS3'
		));

        // element routes
		$this->add( '/assets/element/@id', array('GET', 'POST'), array(
				'controller' => 'Assets',
				'action' => 'element'
		));

		$this->add( '/assets/element/@id/@page', array('GET', 'POST'), array(
				'controller' => 'Assets',
				'action' => 'element'
		));

		$this->add( '/assets/element/image/@id', array('GET', 'POST'), array(
				'controller' => 'Assets',
				'action' => 'elementImage'
		));

		$this->add( '/assets/element/image/@id/@page', array('GET', 'POST'), array(
				'controller' => 'Assets',
				'action' => 'elementImage'
		));
	}
}