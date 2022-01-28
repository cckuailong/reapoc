<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<table class="form-table">
				<tr>
					<th scope="row">
						<label>
							<?php _e('Maps', UMS_LANG_CODE); ?>
						</label>
					</th>
					<td>
						<button id="umsCsvExportMapsBtn" class="button">
							<?php _e('Export', UMS_LANG_CODE); ?>
						</button>
						<?php echo htmlUms::ajaxfile('csv_import_file_maps', array(
							'url' => uriUms::_(array('baseUrl' => admin_url('admin-ajax.php'), 'page' => 'csv', 'action' => 'import', 'type' => 'maps', 'reqType' => 'ajax')),
							'data' => 'umsCsvImportData',
							'buttonName' => __('Import', UMS_LANG_CODE),
							'responseType' => 'json',
							'onSubmit' => 'umsCsvImportOnSubmit',
							'onComplete' => 'umsCsvImportOnComplete',
							'btn_class' => 'button',
						))?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="umsCsvExportMarkersBtn">
							<?php _e('Markers', UMS_LANG_CODE); ?>
						</label>
					</th>
					<td>
						<button id="umsCsvExportMarkersBtn" class="button">
							<?php _e('Export', UMS_LANG_CODE); ?>
						</button>
						<?php echo htmlUms::ajaxfile('csv_import_file_markers', array(
							'url' => uriUms::_(array('baseUrl' => admin_url('admin-ajax.php'), 'page' => 'csv', 'action' => 'import', 'type' => 'markers', 'reqType' => 'ajax')),
							'data' => 'umsCsvImportData',
							'buttonName' => __('Import', UMS_LANG_CODE),
							'responseType' => 'json',
							'onSubmit' => 'umsCsvImportOnSubmit',
							'onComplete' => 'umsCsvImportOnComplete',
							'btn_class' => 'button',
						))?>
					</td>
				</tr>
				<tr>
					<td colspan="2"><div id="umsCsvImportMsg"></div></td>
				</tr>
			</table>
			<h3><?php _e('CSV Options', UMS_LANG_CODE)?></h3>
			<form id="umsCsvForm">
				<table class="form-table no-border">
					<tr>
						<th scope="row">
							<label for="umsCsvExportDelimiter">
								<?php _e('Delimiter', UMS_LANG_CODE); ?>
							</label>
						</th>
						<td>
							<?php echo htmlUms::selectbox('opt_values[csv_options][delimiter]', array(
								'options' => $this->delimiters,
								'value' => !empty($this->options['delimiter']) ? $this->options['delimiter'] : ';',
								'attrs' => 'style="min-width: 150px;" id="umsCsvExportDelimiter"'))?>
						</td>
					</tr>
				</table>
				<?php echo htmlUms::hidden('page', array('value' => 'csv'))?>
				<?php echo htmlUms::hidden('action', array('value' => 'saveCsvOptions'))?>
			</form>
			<button id="umsCsvSaveBtn" class="button">
				<i class="fa fa-save"></i>
				<?php _e('Save', UMS_LANG_CODE)?>
			</button>
		</div>
	</div>
</section>