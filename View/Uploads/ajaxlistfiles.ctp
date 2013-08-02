
<table class="table table-hover table-condensed table-rounded table-bordered">
	<tr>
		<th>Filename</th>
		<th>Size</th>
		<th>Uploaded</th>
		<th>Download</th>
		<th>Torrent</th>
		<th>Delete</th>
		<th>Swarm info</th>
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
				echo '<td>';

					if(!empty($props['peers'])){
						
							echo '<table width="100%" class="table-hover table-condensed table-rounded table-bordered">';
								echo '<tr>';
									echo '<th></th>';
									echo '<th width="22%">Host</th>';
									echo '<th width="16%">Up</th>';
									echo '<th width="16%">Down</th>';
									echo '<th width="16%">Left</th>';
									echo '<th width="10%">Status</th>';
									echo '<th width="16%">On/offline</th>';
								echo '</tr>';

								foreach($props['peers'] as $peer){
									echo '<tr>';
										echo '<td>';

											$icon = 'user';
											$label = 'Peer';
											if($peer['Peer']['ip_address'] == ip2long(getHostByName(getHostName()))){
												//Is it this seedserver?
												$icon = 'hdd';
												$label = 'This seedserver';
											}

											echo '<i class="icon-'.$icon.'" title="'.$label.'"></i>';

										echo '</td>';

										echo '<td><abbr title="'.long2ip($peer['Peer']['ip_address']).' : '.$peer['Peer']['port'].'">' . gethostbyaddr(long2ip($peer['Peer']['ip_address'])) . '</abbr></td>';
										echo '<td>' . round($peer['Peer']['bytes_uploaded'] / 1024 / 1024 ,2).' Mb' . '</td>';
										echo '<td>' . round($peer['Peer']['bytes_downloaded'] / 1024 / 1024 ,2).' Mb' . '</td>';
										echo '<td>' . round($peer['Peer']['bytes_left'] / 1024 / 1024 ,2).' Mb' . '</td>';
										echo '<td>' . $peer['Peer']['status'] . '</td>';

										echo '<td>';

											$icon = 'ok';
											if(strtotime($peer['Peer']['expires']) < time()){
												//Is it this seedserver?
												$icon = 'remove';
											}

											echo '<i class="icon-'.$icon.'"></i>';

										echo '</td>';

									echo '</tr>';
								}
							echo '</table>';

					}else{
						echo 'No peers';
					}

				echo '</td>';
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