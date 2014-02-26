<?php

namespace Assets\Site;

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
					'namespace' => '\Assets\Site\Controllers',
					'url_prefix' => '/asset'
				)
		);
 		// TODO Make this support dimensions, e.g. /asset/thumb/@slug/@width/@height
 		$this->add( '/thumb/@slug', 'GET', array(
								'controller' => 'Asset',
								'action' => 'thumb'
								));

		$this->add( '/@slug', 'GET', array(
				'controller' => 'Asset',
				'action' => 'read'
		));
	}
}