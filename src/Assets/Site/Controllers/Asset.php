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
        $f3 = \Base::instance();
        $slug = $this->inputfilter->clean( $f3->get('PARAMS.slug'), 'PATH' );
        $id = $this->inputfilter->clean( $f3->get('PARAMS.id'), 'alnum' );
        $model = $this->getModel()
            ->setState('filter.slug', $slug)
            ->setState('filter.id', $id);
        
        try {
            $item = $model->getItem();
        } catch ( \Exception $e ) {
            \Dsc\System::instance()->addMessage( "Invalid Item: " . $e->getMessage(), 'error');
            $f3->reroute( '/' );
            return;
        }

        return $item;
    }
    
    public function read() 
    {
        $f3 = \Base::instance();
        $flash = \Dsc\Flash::instance();
        $f3->set('flash', $flash );
        
        $model = $this->getModel();
        $item = $this->getItem();
        
        if (empty($item->id)) 
        {
        	return $f3->error( 404, 'Invalid Item' );        	
        }
        
        $f3->set('model', $model );
        $f3->set('item', $item );
        
        $flash->store((array) $item->cast());

        switch ($flash->old('storage')) 
        {
            case "s3":
                $f3->reroute( $flash->old('url') );
                
                break;
            case "gridfs":
            default:
                $f3 = \Base::instance();
                
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
        
        $flash->store($thumb);
        echo $this->theme->renderView('Assets/Site/Views::assets/thumb.php');
    }
    
}