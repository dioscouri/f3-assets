<?php 
namespace Assets\Admin\Controllers;

class Assets extends \Admin\Controllers\BaseAuth 
{
    use \Dsc\Traits\Controllers\Element;
    
    protected $element_item_key = 'metadata.slug'; // returns the property used to get the value from the element object.  If you want the $item->id, this method should return "id"
    protected $element_item_title_key = 'metadata.title'; // returns the property used to get the title from the element object.  If you want the $item->title, this method should return "title"
    protected $element_url = '/admin/assets/element/{id}'; // where {id} will be replaced by the id of the element object
    protected $element_url_image = '/admin/assets/element/image/{id}'; // where {id} will be replaced by the id of the element object
    
    public function display()
    {
        \Base::instance()->set('pagetitle', 'Asset Library');
        \Base::instance()->set('subtitle', '');
        
        $model = new \Assets\Admin\Models\Assets;
        $state = $model->populateState()->setState('filter.type', true)->getState();
        \Base::instance()->set('state', $state );
        
        $list = $model->paginate();
        \Base::instance()->set('list', $list );
        
        $pagination = new \Dsc\Pagination($list['total'], $list['limit']);       
        \Base::instance()->set('pagination', $pagination );
        
        $view = new \Dsc\Template;
        echo $view->render('Assets/Admin/Views::assets/list.php');
    }
    
    public function element()
    {
        $model = new \Assets\Admin\Models\Assets;
        $state = $model->populateState()->setState('filter.type', true)->getState();
        \Base::instance()->set('state', $state );
    
        $list = $model->paginate();
        \Base::instance()->set('list', $list );
    
        $pagination = new \Dsc\Pagination($list['total'], $list['limit']);
        \Base::instance()->set('pagination', $pagination );
    
        \Base::instance()->set('select_function_name', $this->getElementSelectFunction() );
        \Base::instance()->set('elementItemKey', $this->getElementItemKey() );
        \Base::instance()->set('elementItemTitleKey', $this->getElementItemTitleKey() );
                
        $view = new \Dsc\Template;
        echo $view->setLayout('app.php')->render('Assets/Admin/Views::element/list.php');
    }
    
    public function elementImage()
    {
        $model = new \Assets\Admin\Models\Assets;
        $state = $model->populateState()->setState('filter.type', true)->setState('filter.content_type', 'image/')->getState();
        \Base::instance()->set('state', $state );
    
        $list = $model->paginate();
        \Base::instance()->set('list', $list );
    
        $pagination = new \Dsc\Pagination($list['total'], $list['limit']);
        \Base::instance()->set('pagination', $pagination );
    
        \Base::instance()->set('select_function_name', $this->getElementSelectFunction() );
        \Base::instance()->set('elementItemKey', $this->getElementItemKey() );
        \Base::instance()->set('elementItemTitleKey', $this->getElementItemTitleKey() );        
        
        $view = new \Dsc\Template;
        echo $view->setLayout('app.php')->render('Assets/Admin/Views::element/list.php');
    }
    
    public function fetchElementImage($id, $value=null, $options=array() ) 
    {
        if (!isset($options['onclick_select']))
        {
            $html_pieces[] = '<div id="' . $id . '_thumb" class="text-center">';
            $html_pieces[] = '<div class="thumbnail text-center">';
            $html_pieces[] = '<img src="' . \Base::instance()->get( 'BASE' ) . '/asset/{value}" />';
            $html_pieces[] = '</div>';
            $html_pieces[] = '</div>';
            $html = implode(" ", $html_pieces);

            $options['onclick_select'] = 'jQuery("#' . $id . '_thumb").remove(); var html = \''.$html.'\'; html = html.replace("{value}",value); jQuery("#' . $id . '_primary").after(html);';
        }
                
        if (!isset($options['onclick_reset'])) 
        {
            $options['onclick_reset'] = 'jQuery("#' . $id . '_thumb").remove();';
        }
        
        if (!isset($options['url'])) 
        {
            $f3 = \Base::instance();
            $options['url'] = $f3->get('BASE') . str_replace('{id}', $id, $this->element_url_image);
        }
        
        return $this->fetchElement($id, $value, $options );
    }
    
    protected function getElementItemTitle($value=null) 
    {
        if (empty($value)) {
            return "Select";
        }
        
        $model = new \Assets\Admin\Models\Assets;
        $model->setState('filter.slug', $value);
        
        try {
            $item = $model->getItem();
            $title = $item->{'metadata.title'}; 
            
        } catch ( \Exception $e ) {
            $title = "Invalid Item";
        }
        
        return $title;        
    }
    
    protected function getElementHtml( $html_pieces, $id, $value=null, $options=array() ) 
    {
        if (empty($value)) {
            return $html_pieces;
        }
                
        $model = new \Assets\Admin\Models\Assets;
        $model->setState('filter.slug', $value);
        
        try {
            $item = $model->getItem();
            
        } catch ( \Exception $e ) {
            return $html_pieces;
        }

        if (is_a($item, '\Dsc\Mongo\Mappers\Asset') && $item->isImage()) 
        {
            $html_pieces[] = '<div id="' . $id . '_thumb" class="text-center">';
            $html_pieces[] = '<div class="thumbnail text-center">';
            $html_pieces[] = '<img src="' . \Base::instance()->get( 'BASE' ) . '/asset/' . $item->{'metadata.slug'} . '" alt="' . $item->{'metadata.title'} . '" />';
            $html_pieces[] = '</div>';
            $html_pieces[] = '</div>';
        }        
                        
        return $html_pieces;
    }
}