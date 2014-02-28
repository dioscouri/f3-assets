<?php 
namespace Assets;

class Listener extends \Prefab 
{
    public function onSystemRebuildMenu( $event )
    {
        if ($mapper = $event->getArgument('mapper')) 
        {
            $mapper->reset();
            $mapper->priority = 40;
            $mapper->id = 'f3-assets';
            $mapper->title = 'Media Assets';
            $mapper->route = '';
            $mapper->icon = 'fa fa-picture-o';
            $mapper->children = array(
                    json_decode(json_encode(array( 'title'=>'Library', 'route'=>'/admin/assets', 'icon'=>'fa fa-list' )))
                    ,json_decode(json_encode(array( 'title'=>'Add New', 'route'=>'/admin/asset/create', 'icon'=>'fa fa-plus' )))
            );
            $mapper->save();
            
            \Dsc\System::instance()->addMessage('Assets added its admin menu items.');
        }
        
    }
}