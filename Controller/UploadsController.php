<?php
class UploadsController extends AppController {

	public $uses = array('TorrentTracker.Torrent','TorrentTracker.Peer');

	private $uploaddir;

	private $torrentdir;

	function beforeFilter(){
		$this->uploaddir = APP.'Plugin'.DS.'TorrentTracker'.DS.'webroot'.DS.'files'.DS.'uploads'.DS;
		$this->torrentdir = APP.'Plugin'.DS.'TorrentTracker'.DS.'webroot'.DS.'files'.DS.'torrents'.DS;
	}

	function index(){
		$this->redirect('uploadfile');
	}

	function uploadfile(){
		
	}

	function ajaxupload(){

		//Init upload handler from Vendor:
		#error_reporting(E_ALL | E_STRICT);
		require(APP.'Plugin'.DS.'TorrentTracker'.DS.'Vendor'.DS.'jqueryfileupload'.DS.'UploadHandler.php');

		//Capture output, this way we do not have to touch the UploadHandler object
		ob_start();

		//Handle upload
		if(!file_exists($this->uploaddir)){
			mkdir($this->uploaddir);
		}

		//Upload to plugin upload dir
		$options['upload_dir'] = $this->uploaddir;		
		
		$upload_handler = new UploadHandler($options); 
 		
 		//Get output
		$json_output = ob_get_contents();

		//Clear buffer
		ob_end_clean(); 

		//Convert output to PHP object, we need filename
		$output =  json_decode($json_output);

		//Create torrent of file
		$this->Torrent->createtorrent($output->files[0]->name);

		//Append torrentlink
		$output->files[0]->torrentlink = Router::url('/torrent_tracker/files/torrents/'.$output->files[0]->name.'.torrent',true);

		//Encode with torrentlink for json output
		$json_output = json_encode($output);
	
		//Do not render view. The UploadHandler will respond
		echo $json_output; //Echo the json output for the file upload plugin

		//Do not render view
		$this->autoRender = false;
	}

	/**
	 * Ajax function to list files + torrent
	 * @return [type] [description]
	 */
	function ajaxlistfiles(){

		//Glob all files in uploaddir
		$files = array();
		foreach(glob($this->uploaddir.'*') as $file){

			//Strip path
			$filename = str_replace($this->uploaddir,'',$file);

			$files[$filename]['filename']=$filename;
			$files[$filename]['filesize']=filesize($file) / 1024 / 1024; //Mb
			$files[$filename]['modified']=filemtime($file); //Mb

			//Check torrent
			if( file_exists( $this->torrentdir.$filename.'.torrent' )){

				//Add torrent file
				$files[$filename]['torrent']=$filename.'.torrent';

				//Check database table phptracker_peers for torrent peers:
				if($torrent = $this->Torrent->findByName($filename)){
					if($peers = $this->Peer->findAllByInfoHash($torrent['Torrent']['info_hash'])){
						$files[$filename]['peers']=$peers;
					}
				}
			}
		}

		$this->set('files',$files);

		$this->layout = 'ajax';
	}

	/**
	 * Ajax delete file + torrent
	 * @return [type] [description]
	 */
	function ajaxdeletefile($filename = null){

		$filename = base64_decode(urldecode($filename));

		//Delete uploaddir
		if(file_exists($this->uploaddir.$filename)){
			unlink($this->uploaddir.$filename);
		}

		//Delete torrent
		if(file_exists($this->torrentdir.$filename.'.torrent')){
			unlink($this->torrentdir.$filename.'.torrent');
		}

		//Delete from DB
		$this->Torrent->deleteAll(array('name'=>$filename));

		$this->autoRender = false; //Dont render view. Complete action will update screen
	}


}
?>