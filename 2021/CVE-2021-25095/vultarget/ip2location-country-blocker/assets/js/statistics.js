jQuery(document).ready(function($){
	$('#btn-purge').on('click', function(e) {
		if (!confirm('WARNING: All data will be permanently deleted from the storage. Are you sure you want to proceed with the deletion?')) {
			e.preventDefault();
		}
	});
});