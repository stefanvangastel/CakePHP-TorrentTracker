<?php
class TorrentsController extends AppController {

	public $uses = array('TorrentTracker.Torrent');

	function announce(){
		
		//Send the announce output
		echo $this->Torrent->announce();

		//Do not render view
		$this->autoRender = false;
	}

}
?>