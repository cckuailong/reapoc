jQuery(document).ready(function() {
	//find status
	var $status = jQuery('#pmpro_updates_status');
	var $row = 1;
	var $count = 0;
	var $title = document.title;
	var $cycles = ['|','/','-','\\'];
	
	//start updates and update status
	if($status && $status.length > 0)
	{
		$status.html($status.html() + '\n' + 'JavaScript Loaded. Starting updates.\n');

		function pmpro_updates()
		{
			jQuery.ajax({
				url: ajaxurl,type:'GET', timeout: 30000,
				dataType: 'html',
				data: 'action=pmpro_updates',
				error: function(xml){
					alert('Error with update. Try refreshing.');				
				},
				success: function(responseHTML){
					if (responseHTML.indexOf('[error]') > -1)
					{
						alert('Error while running update: ' + responseHTML + ' Try refreshing. If this error occurs again, seek help on the PMPro member forums.');
						document.title = $title;
					}
					else if(responseHTML.indexOf('[done]') > -1)
					{
						$status.html($status.html() + '\nDone!');
						document.title = '! ' + $title;
						jQuery('#pmpro_updates_intro').html('All updates are complete.');
						location.reload(1);
					}
					else
					{
						$count++;
						re = /\[.*\]/;
						progress = re.exec(responseHTML);
						if(progress && progress.length > 0)
							jQuery('#pmpro_updates_progress').html(progress + ' ' + parseInt(eval(progress[0].replace(/\[|\]/ig, ''))*100) + '%');
						$status.html($status.html() + responseHTML.replace(re, ''));						
						document.title = $cycles[$count%4] + ' ' + $title;
						$update_timer = setTimeout(function() { pmpro_updates();}, 200);
					}

					//scroll the text area unless the mouse is over it
					if (jQuery('#status:hover').length != 0) {						
						$status.scrollTop($status[0].scrollHeight - $status.height());						
					}
				}
			});
		}

		var $update_timer = setTimeout(function() { pmpro_updates();}, 200);
	}
});