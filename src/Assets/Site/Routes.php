<?php
namespace Assets\Site;

class Routes extends \Dsc\Routes\Group
{

    public function initialize()
    {
        $f3 = \Base::instance();
        
        $this->setDefaults(array(
            'namespace' => '\Assets\Site\Controllers',
            'url_prefix' => '/asset'
        ));
        
        // TODO Make these both support dimensions, e.g. /asset/thumb/@slug/@width/@height
        
        if ($f3->get('CACHE') && !$f3->get('DEBUG'))
        {
            $f3->route( 'GET /asset/thumb/@slug', '\Assets\Site\Controllers\Asset->thumb', 3600*24 );
            $f3->route( 'GET /asset/@slug', '\Assets\Site\Controllers\Asset->read', 3600*24 );
        }
        else
        {
            $this->add('/thumb/@slug', 'GET|HEAD', array(
                'controller' => 'Asset',
                'action' => 'thumb'
            ));
            
            $this->add('/@slug', 'GET|HEAD', array(
                'controller' => 'Asset',
                'action' => 'read'
            ));
            $this->add('/@slug/@height/@width', 'GET|HEAD', array(
            		'controller' => 'Asset',
            		'action' => 'read'
            ));
           $this->add('/thumb/@slug/@height/@width', 'GET|HEAD', array(
                'controller' => 'Asset',
                'action' => 'thumb'
            ));
        }        
    }
}