<?php 
class AssetsBootstrap extends \Dsc\BaseBootstrap{
	protected $dir = __DIR__;
	protected $namespace = 'Assets';
}
$app = new AssetsBootstrap();