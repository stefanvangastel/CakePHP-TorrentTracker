CakePHP-TorrentTracker
======================

Standalone Torrent tracker/server plugin for CakePHP including fileupload, seedserver, announce url and torrent creator. 

Uses:

* https://github.com/blueimp/jQuery-File-Upload
* https://github.com/tcz/PHPTracker

More README following. Dev still in progress but code is working! 

Load plugin in bootstrap and visit: http://<name>/torrent_tracker/uploads/ to upload files. Torrents will be created on the fly.

To start seeding server run from APP_DIR (NIX server required): $ Console/cake TorrentTracker.Seeder
