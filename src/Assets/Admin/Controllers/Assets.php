<?php 
namespace Assets\Admin\Controllers;

class Assets extends \Admin\Controllers\BaseAuth 
{
	
    use \Dsc\Traits\Controllers\AdminList;
	use \Dsc\Traits\Controllers\Element;
    
    protected $list_route = '/admin/assets';
	protected $element_item_key = 'slug'; // returns the property used to get the value from the element object.  If you want the $item->id, this method should return "id"
    protected $element_item_title_key = 'title'; // returns the property used to get the title from the element object.  If you want the $item->title, this method should return "title"
    protected $element_url = '/admin/assets/element/{id}'; // where {id} will be replaced by the id of the element object
    protected $element_url_image = '/admin/assets/element/image/{id}'; // where {id} will be replaced by the id of the element object
    
    public function getModel(){
    	$model = new \Assets\Admin\Models\Assets;
    	return $model;
    }
    
    public function index()
    {
        \Base::instance()->set('pagetitle', 'Asset Library');
        \Base::instance()->set('subtitle', '');
        
        $model = $this->getModel();
        $state = $model->emptyState()->populateState()->getState();
        \Base::instance()->set('state', $state );
        \Base::instance()->set( 'paginated', $model->paginate() );

        echo \Dsc\System::instance()->get('theme')->renderTheme('Assets/Admin/Views::assets/list.php');
    }
    
    public function element()
    {
        $model = $this->getModel();
        $state = $model->populateState()->getState();
        \Base::instance()->set('state', $state );
        
        $id = \Base::instance()->get('PARAMS.id');
    
        \Base::instance()->set('paginated', $model->paginate());
        \Base::instance()->set('select_function_name', $this->getElementSelectFunction() . '_' . $this->inputfilter->clean( $id ) );
        \Base::instance()->set('elementItemKey', $this->getElementItemKey() );
        \Base::instance()->set('elementItemTitleKey', $this->getElementItemTitleKey() );

        echo \Dsc\System::instance()->get('theme')->setVariant('app')->renderTheme('Assets/Admin/Views::element/list.php');
    }
    
    public function elementImage()
    {
        $model = $this->getModel();
        $state = $model->populateState()->setState('filter.content_type', 'image/')->getState();
        \Base::instance()->set('state', $state );
    
        $id = \Base::instance()->get('PARAMS.id');
        
        \Base::instance()->set('paginated', $model->paginate());
        \Base::instance()->set('select_function_name', $this->getElementSelectFunction() . '_' . $this->inputfilter->clean( $id ) );
        \Base::instance()->set('elementItemKey', $this->getElementItemKey() );
        \Base::instance()->set('elementItemTitleKey', $this->getElementItemTitleKey() );        

        echo \Dsc\System::instance()->get('theme')->setVariant('app')->renderTheme('Assets/Admin/Views::element/list.php');
    }
    
    public function fetchElementImage($id, $value=null, $options=array() ) 
    {
        if (!isset($options['onclick_select']))
        {
            $html_pieces[] = '<div id="' . $id . '_thumb" class="text-center">';
            $html_pieces[] = '<div class="thumbnail text-center">';
            $html_pieces[] = '<img src="' . \Base::instance()->get( 'BASE' ) . '/asset/thumb/{value}" />';
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
        
        $model = $this->getModel();
        $model->setState('filter.slug', $value);
        $item = $model->getItem();
        
        if (!empty($item->{'title'})) {
            $title = $item->{'title'};
        }
        else {
            $title = "Invalid Item";
        }
        
        return $title;        
    }
    
    protected function getElementHtml( $html_pieces, $id, $value=null, $options=array() ) 
    {
        if (empty($value)) {
            return $html_pieces;
        }
                
        $model = $this->getModel();
        $model->setState('filter.slug', $value);
        
        try {
            $item = $model->getItem();
            if (empty($item->id)) {
            	throw new \Exception('Invalid Item');
            }
            
        } catch ( \Exception $e ) {
            return $html_pieces;
        }

        if ($item->isImage()) 
        {
            $html_pieces[] = '<div id="' . $id . '_thumb" class="text-center">';
            $html_pieces[] = '<div class="thumbnail text-center">';
            $html_pieces[] = '<img src="' . \Base::instance()->get( 'BASE' ) . '/asset/thumb/' . $item->{'slug'} . '" alt="' . $item->{'title'} . '" />';
            $html_pieces[] = '</div>';
            $html_pieces[] = '</div>';
        }        
                        
        return $html_pieces;
    }
}