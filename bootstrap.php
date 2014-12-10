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
}

$app = new AssetsBootstrap();