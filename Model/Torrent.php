<?php
App::uses('ConnectionManager', 'Model');

class Torrent extends TorrentTrackerAppModel {

	public $name = 'Torrent';

	public $useTable = false;

	private $uploaddir;

	private $torrentdir;

	public function __construct(){
		
		$this->uploaddir = APP.'Plugin'.DS.'TorrentTracker'.DS.'webroot'.DS.'files'.DS.'uploads'.DS;

		$this->torrentdir = APP.'Plugin'.DS.'TorrentTracker'.DS.'webroot'.DS.'files'.DS.'torrents'.DS;
	}

	public function createtorrent($file = null) {
		
		//Load PHP tracker
		$this->loadPhpTracker();

		//Get datasource
		$ds = ConnectionManager::getDataSource('default')->config;

		// implementing PHPTracker_Config_Interface.
		$config = new PHPTracker_Config_Simple( array(
		    // Persistense object implementing PHPTracker_Persistence_Interface.
		    // We use MySQL here. The object is initialized with its own config.
		    'persistence' => new PHPTracker_Persistence_Mysql(
		        new PHPTracker_Config_Simple( array(
		            'db_host'       => 'localhost',
		            'db_user'       => $ds['login'],
		            'db_password'   => $ds['password'],
		            'db_name'       => $ds['database']
		        ) )
		    ),
		    // List of public announce URLs on your server.
		    'announce'  => array(
		    	Router::url(array('plugin'=>'torrent_tracker','controller'=>'torrents','action'=>'announce'),true)
		    )
		) );

		// Core class managing creating the file.
		$core = new PHPTracker_Core( $config );

		// The first parameters is a path (can be absolute) of the file,
		// the second is the piece size in bytes.
		$torrentfile = $core->createTorrent( $this->uploaddir.$file, 524288 );
		
		//Torrentpath
		$torrentpath = $this->torrentdir;
		if( ! file_exists($torrentpath)){
			mkdir($torrentpath);
		}

		//Save the file to the torrents dir of the plugin
		return(file_put_contents($torrentpath.$file.'.torrent', $torrentfile));
	}

	public function announce(){

		//Load PHP tracker
		$this->loadPhpTracker();

		//Get datasource
		$ds = ConnectionManager::getDataSource('default')->config;

		// Creating a simple config object. You can replace this with your object
		// implementing PHPTracker_Config_Interface.
		$config = new PHPTracker_Config_Simple( array(
		    // Persistense object implementing PHPTracker_Persistence_Interface.
		    // We use MySQL here. The object is initialized with its own config.
		    'persistence' => new PHPTracker_Persistence_Mysql(
		        new PHPTracker_Config_Simple( array(
		            'db_host'       => 'localhost',
		            'db_user'       => $ds['login'],
		            'db_password'   => $ds['password'],
		            'db_name'       => $ds['database']
		        ) )
		    ),
		    // The IP address of the connecting client.
		    'ip'        => $_SERVER['REMOTE_ADDR'],
		    // Interval of the next announcement in seconds - sent back to the client.
		    'interval'  => 30,
		) );

		// Core class managing creating the file.
		$core = new PHPTracker_Core( $config );

		// We take the parameters the client is sending and initialize a config
		// object with them. Again, you can implement your own Config class to do this.
		$get = new PHPTracker_Config_Simple( $_GET );

		// We simply send back the results of the announce method to the client.
		return $core->announce( $get );
	}


	private function loadPhpTracker(){
		// Registering autoloader, essential to use the library.
		require(APP.'Plugin'.DS.'TorrentTracker'.DS.'Vendor'.DS.'PHPTracker'.DS.'Autoloader.php');
		PHPTracker_Autoloader::register();
	}

}