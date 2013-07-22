<?php
	//Default route
	Router::connect('/torrent_tracker/', array('plugin'=>'torrent_tracker', 'controller' => 'uploads', 'action' => 'uploadfile'));
?>
