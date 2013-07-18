
<table class="table table-hover table-condensed table-rounded">
	<tr>
		<th>Filename</th>
		<th>Size</th>
		<th>Uploaded</th>
		<th>Download</th>
		<th>Torrent</th>
		<th></th>
	</tr>
	<?php
	//Webroot/files base URL:
	$filesurl = $this->Html->url('/torrent_tracker/files/',true);

	//List files
	if(!empty($files)){
		foreach($files as $filename => $props){
			echo '<tr>';
				echo '<td>' . $filename . '</td>';
				echo '<td>' . round($props['filesize'],2) . ' Mb' . '</td>';
				echo '<td>' . date('d-m-Y H:i:s',$props['modified']) . '</td>';
				echo '<td>' . $this->Html->link('<i class="icon-download"></i>',$filesurl.'/uploads/'.$filename,array('escape'=>false)) . '</td>';
				echo '<td>';
					if(isset($props['torrent'])){
						echo $this->Html->link('<i class="icon-magnet"></i>',$filesurl.'/torrents/'.$props['torrent'],array('mimetype'=>'application/bittorrent', 'target'=>'_blank','escape'=>false));
					}
				echo'</td>';
				echo '<td>' . $this->Html->link('<i class="icon-trash"></i>','#',array('escape'=>false,'onclick'=>'return false;','id'=>md5($filename)),'Delete this file and torrent, are you sure?') . '</td>';
			echo '</tr>';

			//Ajax action
			$this->Js->get('#'.md5($filename));
			
			$this->Js->event('click',
				$this->Js->request(
		       		array('action' => 'ajaxdeletefile',urlencode(base64_encode($filename))),
			        array(
			        	'async' => true, 
			          	'complete' => 'showfiles()'
			        )
		   		)
	   		);
		}
	}
	?>
</table>

<?php echo $this->Js->writeBuffer(); ?>