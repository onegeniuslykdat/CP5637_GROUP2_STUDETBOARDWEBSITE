jQuery(document).ready(function ($) {
	$('#epta-template').on('click', function (event) {
        $('#epta-template option:not(:first)').attr('disabled', 'disabled');
	});
});


