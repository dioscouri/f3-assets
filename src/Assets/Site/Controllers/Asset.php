<?php 
namespace Assets\Site\Controllers;

class Asset extends \Dsc\Controller 
{
    protected function getModel() 
    {
        $model = new \Assets\Admin\Models\Assets;
        return $model; 
    }
    
    protected function getItem() 
    {
        $slug = $this->inputfilter->clean( $this->app->get('PARAMS.slug'), 'PATH' );
        $model = $this->getModel()->setState('filter.slug', $slug);
        
        try {
            $item = $model->getItem();
        } catch ( \Exception $e ) {
            \Dsc\System::instance()->addMessage( "Invalid Item: " . $e->getMessage(), 'error');
            $this->app->reroute( '/' );
            return;
        }

        return $item;
    }
    
    public function read() 
    {
        $flash = \Dsc\Flash::instance();
        $this->app->set('flash', $flash );
        
        $model = $this->getModel();
        $item = $this->getItem();
        
        if (empty($item->id)) 
        {
        	return $this->app->error( 404, 'Invalid Item' );        	
        }
        
        $this->app->set('model', $model );
        $this->app->set('item', $item );
        
        $flash->store((array) $item->cast());
		
        $height = $this->app->get('PARAMS.height');
        $width = $this->app->get('PARAMS.width');
        
        if($height && $width) {
        	$this->app->set('height', $height);
        	$this->app->set('width', $width);
        }
        
        
        switch ($flash->old('storage')) 
        {
            case "s3":
                $this->app->reroute( $flash->old('url') );
                
                break;
            case "gridfs":
            default:
                $this->app->set('meta.title', $item->slug);
                
                $view = \Dsc\System::instance()->get('theme');
                echo $view->renderLayout('Assets/Site/Views::assets/view.php');
                
                break;
        }
    }
    
    /**
     * Displays the thumb
     */
    public function thumb()
    {
        $flash = \Dsc\Flash::instance();
        $this->app->set('flash', $flash );
        $slug = $this->inputfilter->clean( $this->app->get('PARAMS.slug'), 'PATH' );
        
        try {
            $thumb = \Dsc\Mongo\Collections\Assets::cachedThumb($slug);
            if (empty($thumb['bin'])) {
            	throw new \Exception;
            }
        } 
        catch (\Exception $e) {
            return $this->app->error( 404, 'Invalid Thumb' );
        }
        $height = $this->app->get('PARAMS.height');
        $width = $this->app->get('PARAMS.width');
        
        if($height && $width) {
        	$this->app->set('height', $height);
        	$this->app->set('width', $width);
        }
        
        $flash->store($thumb);
        echo $this->theme->renderView('Assets/Site/Views::assets/thumb.php');
    }
    
}