(function ($) {

	$('.table-tasks').dataTable({
		'bProcessing': true,
		'bServerSide': true,
		'sAjaxSource': rotarydatatables.ajaxURL + '?action=get_tasks',
		'iColumns': 5,
		"aaSorting": [[ 5, "asc" ]],
	});
	
})(jQuery);