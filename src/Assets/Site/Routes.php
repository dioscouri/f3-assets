<?php
namespace Assets\Site;

class Routes extends \Dsc\Routes\Group
{

    public function initialize()
    {
        $this->setDefaults(array(
            'namespace' => '\Assets\Site\Controllers',
            'url_prefix' => '/asset'
        ));
        
        // TODO Make these both support dimensions, e.g. /asset/thumb/@slug/@width/@height
        $this->add('/thumb/@slug', 'GET', array(
            'controller' => 'Asset',
            'action' => 'thumb'
        ));
        
        $this->add('/@slug', 'GET', array(
            'controller' => 'Asset',
            'action' => 'read'
        ));
    }
}