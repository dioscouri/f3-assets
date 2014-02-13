<?php 
$f3 = \Base::instance();
$global_app_name = $f3->get('APP_NAME');

switch ($global_app_name) 
{
    case "admin":
        // register event listener
        \Dsc\System::instance()->getDispatcher()->addListener(\Assets\Listener::instance());

        // register all the routes
        $f3->route('GET|POST /admin/assets', '\Assets\Admin\Controllers\Assets->display');
        $f3->route('GET|POST /admin/assets/@page', '\Assets\Admin\Controllers\Assets->display');
        $f3->route('GET|POST /admin/assets/delete', '\Assets\Admin\Controllers\Assets->delete');
        $f3->route('GET /admin/asset', '\Assets\Admin\Controllers\Asset->create');
        $f3->route('POST /admin/asset', '\Assets\Admin\Controllers\Asset->add');
        $f3->route('GET /admin/asset/@id', '\Assets\Admin\Controllers\Asset->read');
        $f3->route('GET /admin/asset/@id/edit', '\Assets\Admin\Controllers\Asset->edit');
        $f3->route('POST /admin/asset/@id', '\Assets\Admin\Controllers\Asset->update');
        $f3->route('DELETE /admin/asset/@id', '\Assets\Admin\Controllers\Asset->delete');
        $f3->route('GET /admin/asset/@id/delete', '\Assets\Admin\Controllers\Asset->delete');
        $f3->route('GET /admin/asset/rethumb/@id', '\Assets\Admin\Controllers\Asset->rebuildThumb');
        // upload handlers
        $f3->route('POST /admin/asset/handleTraditional', '\Assets\Admin\Controllers\Asset->handleTraditional');
        $f3->route('POST|DELETE /admin/asset/handleS3', '\Assets\Admin\Controllers\Asset->handleS3');
        // element routes
        $f3->route('GET|POST /admin/assets/element/@id', '\Assets\Admin\Controllers\Assets->element');
        $f3->route('GET|POST /admin/assets/element/@id/@page', '\Assets\Admin\Controllers\Assets->element');
        $f3->route('GET|POST /admin/assets/element/image/@id', '\Assets\Admin\Controllers\Assets->elementImage');
        $f3->route('GET|POST /admin/assets/element/image/@id/@page', '\Assets\Admin\Controllers\Assets->elementImage');
        
        // append this app's UI folder to the path
        $ui = $f3->get('UI');
        $ui .= ";" . $f3->get('PATH_ROOT') . "vendor/dioscouri/f3-assets/src/Assets/Admin/Views/";
        $f3->set('UI', $ui);
        
        // TODO set some app-specific settings, if desired
                
        break;
    case "site":
        $f3->route('GET /asset/thumb/@slug', '\Assets\Site\Controllers\Asset->thumb'); // TODO Make this support dimensions, e.g. /asset/thumb/@slug/@width/@height 
        $f3->route('GET /asset/@slug', '\Assets\Site\Controllers\Asset->read');
        
        // append this app's UI folder to the path
        $ui = $f3->get('UI');
        $ui .= ";" . $f3->get('PATH_ROOT') . "vendor/dioscouri/f3-assets/src/Assets/Site/Views/";
        $f3->set('UI', $ui);
                
        // TODO set some app-specific settings, if desired
        break;
}
?>