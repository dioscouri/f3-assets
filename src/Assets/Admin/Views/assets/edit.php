<script src="./Assets/fineuploader/all.fineuploader.js"></script>
<link rel="stylesheet" href="./Assets/fineuploader/fineuploader.css" type="text/css" />

<?php 
if( $item->isImage()) {
	echo $this->renderLayout('Assets/Admin/Views::assets/image.php');	
} else {
	echo $this->renderLayout('Assets/Admin/Views::assets/default_edit.php');
}
 ?>