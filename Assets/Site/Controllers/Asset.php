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
        
        $f3->set('model', $model );
        $f3->set('item', $item );
        
        if (method_exists($item, 'cast')) {
            $item_data = $item->cast();
        } else {
            $item_data = \Joomla\Utilities\ArrayHelper::fromObject($item);
        }
        $flash->store($item_data);

        switch ($flash->old('storage')) 
        {
            case "s3":
                $f3->reroute( $flash->old('url') );
                
                break;
            case "gridfs":
            default:
                $f3 = \Base::instance();
                $f3->set('pagetitle', 'View Asset');
                
                $view = new \Dsc\Template;
                echo $view->renderLayout('Assets/View.php');
                
                break;
        }
    }
    
}