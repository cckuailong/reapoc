<?php
/** no direct access **/
defined('MECEXEC') or die();

do_action('mec_start_skin', $this->id);
do_action('mec_monthly_skin_head');

$skins = (isset($this->skin_options['skins']) and !empty( $this->skin_options['skins'] )) ? $this->skin_options['skins'] : array('dayGridMonth');
// var_dump($this->skin_options['skins']);
?>
<div class="mec-general-calendar">
	<div id='loading' style="display: none">loading...</div>
	<div id="calendar"></div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
		var calendarEl = document.getElementById("calendar");

		var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
			initialDate: '<?php echo $this->get_start_date()[0].'-'.$this->get_start_date()[1].'-'.$this->get_start_date()[2]; ?>',
			editable: false,
			selectable: false,
			businessHours: false,
			customButtons: {
				listPerMonth: {
					text: 'List (Per Month)',
					click: function() {
						calendar.changeView('listMonth');
					}
				},
				listPerWeek: {
					text: 'List (Per Week)',
					click: function() {
						calendar.changeView('listWeek');
					}
				},
				listPerYear: {
					text: 'List (Per Year)',
					click: function() {
						calendar.changeView('listYear');
					}
				},
				listPerDay: {
					text: 'List (Per Day)',
					click: function() {
						calendar.changeView('listDay');
					}
				}
			},
            headerToolbar: {
                left: 'prevYear,prev,next,nextYear today',
                center: 'title',
                right: '<?php echo implode(",", $skins); ?>'
            },
			// eventDidMount: function(info) {
			// 	var tooltip = new Tooltip(info.el, {
			// 		title: info.event.extendedProps.description,
			// 		placement: 'top',
			// 		trigger: 'hover',
			// 		container: 'body'
			// 	});
			// },
			dayMaxEvents: true,
			timeZone: <?php echo get_option('gmt_offset'); ?>,
			events: {
				url: '<?php echo get_rest_url(); ?>mec/v1/events',
				method: 'GET',
				startParam: 'startParam',
				endParam: 'endParam',
				failure: function() {
					alert('there was an error while fetching events!');
				},
			},
			forceEventDuration: true,
			loading: function(bool) {
				document.getElementById('loading').style.display =
				bool ? 'block' : 'none';
			}
		});
		
		calendar.render();
		// calendar.updateSize()
	});	
</script>