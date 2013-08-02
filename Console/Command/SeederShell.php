<?php
App::uses('ConnectionManager', 'Model');

class SeederShell extends AppShell {
    
    public function main() {

		// Registering autoloader, essential to use the library.
		require(APP.'Plugin'.DS.'TorrentTracker'.DS.'Vendor'.DS.'PHPTracker'.DS.'Autoloader.php');
		PHPTracker_Autoloader::register();

		//Get datasource
		$ds = ConnectionManager::getDataSource('default')->config;

		$persistence = new PHPTracker_Persistence_Mysql(
		    new PHPTracker_Config_Simple( array(
		        'db_host'       => 'localhost',
	            'db_user'       => $ds['login'],
	            'db_password'   => $ds['password'],
	            'db_name'       => $ds['database']
		    ) )
		);

		// Setting up seeder peer. This will listen to connections and serve files.
		$peer = new PHPTracker_Seeder_Peer(
		    new PHPTracker_Config_Simple( array(
		        'persistence'               => $persistence,
		        // PUBLIC address of the seeder server. This will be used for announcements (ie. sent to the clients).
		        'seeder_address'            => getHostByName(getHostName()), # <--Should be local IP, CAUTION! Takes in account /etc/hosts file!
		        // Don't forget the firewall!
		        'seeder_port'               => 6881,
		        // Optional parameter for IP to open socket on if differs from external.
		        //'seeder_internal_address'   => '192.168.2.123',
		        // Number telling how many processes should be forked to listen to incoming connections.
		        'peer_forks'                => 2,
		        // If specified, gives a number of outsider seeders to make self-seeding stop.
		        // This saves you bandwidth - once your file is seeded by others, you can stop serving it.
		        // Number of seeders is permanently checked, but probably 1 is too few if you want your file to be available always.
		        'seeders_stop_seeding'      => 2,
		        // Intializing file logger with default file path (/var/log/phptracker.log).
		        'logger'  => new PHPTracker_Logger_File(),
		    )
		) );

		// We set up a seeding server which starts the seeding peer, and makes regular
		// announcements to the database adding itself to the peer list for all
		// active torrents.
		$server = new PHPTracker_Seeder_Server(
		     new PHPTracker_Config_Simple( array(
		        'persistence'           => $persistence,
		        'peer'                  => $peer,
		         // Intializing file logger with default file path (/var/log/phptracker.log).
		        'logger'  => new PHPTracker_Logger_File(),
		    )
		) );

		// Starting "detached" means that process will unrelate from terminal and run as deamon.
		// To run in terminal, you can use start().
		// Detached running requires php-posix.
		$server->startDetached();

    }

}
?>