<?php 
namespace Assets;

class Listener extends \Prefab 
{
    public function onSystemRebuildMenu( $event )
    {
        if ($model = $event->getArgument('model')) 
        {
        	$root = $event->getArgument( 'root' );
        	$assets = clone $model;
        	
        	$assets->insert(
        			array(
        					'type'	=> 'admin.nav',
        					'priority' => 70,
        					'title'	=> 'Media Assets',
        					'icon'	=> 'fa fa-picture-o',
        					'is_root' => false,
        					'tree'	=> $root,
							'base' => '/admin/asset',
        		)
        	);
        	$children = array(
        			array( 'title'=>'Library', 'route'=>'/admin/assets', 'icon'=>'fa fa-list' ),
        			array( 'title'=>'Add New', 'route'=>'/admin/asset/create', 'icon'=>'fa fa-plus' ),
        	);
	        $assets->addChildren( $children, $root );
        	            
            \Dsc\System::instance()->addMessage('Assets added its admin menu items.');
        }
        
    }
}