<?php 
if( $item->isImage()) {
	echo $this->renderLayout('Assets/Admin/Views::assets/image.php');	
} else {
	echo $this->renderLayout('Assets/Admin/Views::assets/default_edit.php');
}
 ?>