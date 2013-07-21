# CakePHP-TorrentTracker
- - -

# Intro

Plugin born out of need to faclitate torrent-based peer-to-peer update mechanism from CakePHP based webapp.

Provides Torrent tracker/server plugin for CakePHP including fileupload, torrent creator, seedserver and announce url. Based on the PHP Tracker project.

Includes and uses:

* https://github.com/blueimp/jQuery-File-Upload
* https://github.com/tcz/PHPTracker
(Todo: make submodules)

# Requirements

This plugin requires jQuery to be loaded. jQuery File Upload uses TwitterBootstrap CSS files. These are not loaded by default but are included in plugin webroot css dir.

# Installation and Setup

(1) Check out a copy of the TorrentTracker CakePHP plugin from the repository using Git :

git clone http://github.com/stefanvangastel/CakePHP-TorrentTracker.git

or download the archive from Github : https://github.com/stefanvangastel/CakePHP-TorrentTracker/archive/master.zip

You must place the TorrentTracker CakePHP plugin within your CakePHP 2.x app/Plugin directory.

(2) Load the plugin in app/Config/bootstrap.php

// Load TorrentTracker plugin, with loading routes for short urls
CakePlugin::load('TorrentTracker', array('routes' => true));

(3) Load the SQL struct file in your db from location: app/Plugin/TorrentTracker/db/TorrentTracker_table_structure.sql

(4) Visit http://YOUR_URL/torrent_tracker/

(5) To start seeding server run from APP_DIR (NIX server required !): $ Console/cake TorrentTracker.Seeder

# Additional information

More README following. Dev still in progress but code is working! 

Torrents will be created on the fly.

