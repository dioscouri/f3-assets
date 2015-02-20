<?php
class AssetsBootstrap extends \Dsc\Bootstrap
{
    protected $dir = __DIR__;
    protected $namespace = 'Assets';
    
    protected function preAdmin()
    {
        $this->checkSymlink();
    }
    
    protected function preSite()
    {
        $this->checkSymlink();
    }    
    
    protected function checkSymlink()
    {
        if (!is_dir($this->app->get('PATH_ROOT') . 'public/Assets'))
        {
            $target = $this->app->get('PATH_ROOT') . 'public/Assets';
            $source = realpath( $this->app->get('PATH_ROOT') . 'vendor/dioscouri/f3-assets/src/Assets/Assets' );
            $res = symlink($source, $target);
        }
    }
    
    protected function runAdmin()
    {
        \Dsc\System::instance()->getDispatcher()->addListener(\Activity\Listener::instance());
    
        if (class_exists('\Minify\Factory'))
        {
            \Minify\Factory::registerPath($this->dir . "/src/");
    
            $files = array(
                'Assets/Assets/fineuploader/all.fineuploader.js',
            );
    
            foreach ($files as $file)
            {
                \Minify\Factory::js($file);
            }
            
            $files = array(
                'Assets/Assets/fineuploader/fineuploader.css',
            );
            
            foreach ($files as $file)
            {
                \Minify\Factory::css($file);
            }            
        }
    
        parent::runAdmin();
    }    
}

$app = new AssetsBootstrap();