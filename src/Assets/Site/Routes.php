<?php
namespace Assets\Site;

class Routes extends \Dsc\Routes\Group
{

    public function initialize()
    {
        $this->app->route('GET|HEAD /asset/thumb/@slug', '\Assets\Site\Controllers\Asset->thumb');
        $this->app->route('GET|HEAD /asset/@slug', '\Assets\Site\Controllers\Asset->read');
        $this->app->route('GET|HEAD /asset/thumb/@slug/@width/@height', '\Assets\Site\Controllers\Asset->thumb');
        $this->app->route('GET|HEAD /asset/@slug/@width/@height', '\Assets\Site\Controllers\Asset->read');
                
        /*
        if ($this->app->get('DEBUG') || $this->input->get('refresh', 0, 'int'))
        {
            $this->app->route('GET|HEAD /asset/thumb/@slug', '\Assets\Site\Controllers\Asset->thumb');
            $this->app->route('GET|HEAD /asset/@slug', '\Assets\Site\Controllers\Asset->read');
            $this->app->route('GET|HEAD /asset/thumb/@slug/@width/@height', '\Assets\Site\Controllers\Asset->thumb');
            $this->app->route('GET|HEAD /asset/@slug/@width/@height', '\Assets\Site\Controllers\Asset->read');
        }
        
        else
        {
            $cache_period = 3600*24*60;
            
            $this->app->route('GET|HEAD /asset/thumb/@slug', '\Assets\Site\Controllers\Asset->thumb', $cache_period);
            $this->app->route('GET|HEAD /asset/@slug', '\Assets\Site\Controllers\Asset->read', $cache_period);
            $this->app->route('GET|HEAD /asset/thumb/@slug/@width/@height', '\Assets\Site\Controllers\Asset->thumb', $cache_period);
            $this->app->route('GET|HEAD /asset/@slug/@width/@height', '\Assets\Site\Controllers\Asset->read', $cache_period);           
        }
        */
    }
}