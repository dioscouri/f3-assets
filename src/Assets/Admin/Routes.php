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
		
		$this->add( '/assets', array('GET', 'POST'), array(
								'controller' => 'Assets',
								'action' => 'display'
								));

		$this->add( '/assets/@page', array('GET', 'POST'), array(
				'controller' => 'Assets',
				'action' => 'display'
		));

		$this->add( '/assets/delete', array('GET', 'POST'), array(
				'controller' => 'Assets',
				'action' => 'delete'
		));

		$this->add( '/asset', 'GET', array(
				'controller' => 'Asset',
				'action' => 'create'
		));

		$this->add( '/asset', 'POST', array(
				'controller' => 'Asset',
				'action' => 'add'
		));

		$this->add( '/asset/@id', 'GET', array(
				'controller' => 'Asset',
				'action' => 'read'
		));

		$this->add( '/asset/@id/edit', 'GET', array(
				'controller' => 'Asset',
				'action' => 'edit'
		));

		$this->add( '/asset/@id', 'POST', array(
				'controller' => 'Asset',
				'action' => 'update'
		));

		$this->add( '/asset/@id', 'DELETE', array(
				'controller' => 'Asset',
				'action' => 'delete'
		));

		$this->add( '/asset/@id/delete', 'GET', array(
				'controller' => 'Asset',
				'action' => 'delete'
		));

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