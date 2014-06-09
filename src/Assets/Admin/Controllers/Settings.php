<?php 
namespace Assets\Admin\Controllers;

class Settings extends \Admin\Controllers\BaseAuth 
{
	use \Dsc\Traits\Controllers\Settings;
	
	protected $layout_link = 'Assets/Admin/Views::settings/default.php';
	protected $settings_route = '/admin/assets/settings';
    
    public function beforeRoute()
    {
        if(!class_exists('imagick')) {
    	 	\Dsc\System::instance()->addMessage( "ImageMagic is required for Assets to function correctly", 'warning');
    	}
    }

    protected function getModel()
    {
        $model = new \Assets\Models\Settings;
        return $model;
    }
}