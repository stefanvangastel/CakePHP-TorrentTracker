<?php
//Determine max filesize before upload
$maxfilesize = min(rtrim(ini_get('post_max_size'),'M'),rtrim(ini_get('upload_max_filesize'),'M'));
$maxfilesize = ($maxfilesize)*1024*1024;
?>

<h2>Files</h2>
<div id="files">Loading filelist...</div>

<!-- The fileinput-button span is used to style the file input field as button -->
<span class="btn btn-success fileinput-button">
    <i class="icon-plus icon-white"></i>
    <span>Upload files (max <?php echo round($maxfilesize/1024/1024,2)." Mb"; ?> / file)...</span>
    <!-- The file input field used as target for the file upload widget -->
    <input id="fileupload" type="file" name="files[]" multiple>
</span>
<br>
<br>
<!-- The global progress bar -->
<div id="progress" class="progress progress-success progress-striped">
    <div class="bar"></div>
</div>
<!-- The container for the uploaded files -->
<div id="files" class="files"></div>



<h2>Seed server</h2>
<div id="seed_server">
<?php

if(stristr(`ps -ef | grep seed`, 'TorrentTracker.seeder')){
	echo '<font color="green"><h3>Running</h3></font>';
	echo '<pre>';
		$result = explode("\n",`ps -ef | grep TorrentTracker.seeder`);

		unset($result[count($result) - 1]);
		unset($result[count($result) - 1]);
		unset($result[count($result) - 1]);

		$result = implode("\n", $result);

		echo $result;
	echo '</pre>';
}else{
	echo '<font color="red"><h3>Not running</h3></font>';
}

?>
</div>


<?php
//Load CSS
$this->start('css');
	echo $this->Html->css('/torrent_tracker/css/jquery.fileupload-ui.css');
$this->end();

//Load script
$this->start('script');
	//The jQuery UI widget factory, can be omitted if jQuery UI is already included 
	echo $this->Html->script('/torrent_tracker/js/vendor/jquery.ui.widget.js');
	//The Iframe Transport is required for browsers without support for XHR file uploads
	echo $this->Html->script('/torrent_tracker/js/jquery.iframe-transport.js');
	//The basic File Upload plugin
	echo $this->Html->script('/torrent_tracker/js/jquery.fileupload.js');

	?>
	<script>
	function showfiles(){
		<?php
		echo $this->Js->request(
	        array('action' => 'ajaxlistfiles'),
	        array(
	        	'async' => true, 
	        	'update' => '#files'
	        	)
	    );
		?>
	}
	showfiles(); //Init

	/*Auto refresh function*/
	var refreshId = setInterval(function(){showfiles();}, 10000);

	//binds to onchange event of your input field
	if (typeof(FileReader) != "undefined") { //HTML 5 support check

		$('#fileupload').bind('change', function() {

		  //this.files[0].size gets the size of your file.
		  if(this.files[0].size > <?php echo $maxfilesize; ?>){	  	  	
		  	alert('File to big, max <?php echo round($maxfilesize/1024/1024,2)." Mb"; ?> allowed');
		  	$('#fileupload').die();
		  	return false;
		  }

		});

	}

	/*jslint unparam: true */
	/*global window, $ */
	$(function () {
	    'use strict';
	    // Change this to the location of your server-side upload handler:
	    var url = '<?php echo $this->Html->url(array("plugin"=>"torrent_tracker","controller"=>"uploads","action"=>"ajaxupload"));?>';
	    $('#fileupload').fileupload({
	        url: url,
	        dataType: 'json',
	        done: function (e, data) {
	            showfiles(); //Refresh list
	            var progress = 0;
	            window.setTimeout($('#progress .bar').css(
	                'width',
	                progress + '%'
	            ),800);
	        },
	        progressall: function (e, data) {
	            var progress = parseInt(data.loaded / data.total * 100, 10);
	            $('#progress .bar').css(
	                'width',
	                progress + '%'
	            );
	        }
	    }).prop('disabled', !$.support.fileInput)
	        .parent().addClass($.support.fileInput ? undefined : 'disabled');
	});
	
	</script>
<?php
$this->end();
?>