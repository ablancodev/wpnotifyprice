jQuery(document).ready(function($) {
	
	$('.wordpress-ajax-form').on('submit', function(e) {
		e.preventDefault();
 
		var $form = $(this);
 
		/*
		$.post($form.attr('action'), function(data, status){
		    alert("Data: " + data + "\nStatus: " + status);
		  });
		*/
		$.ajax({
			type:		'POST',
			url:		$form.attr('action'),
			data:		$form.serialize(),
			dataType:   'html',
			success:	function( result ) {
				$('#bootstrapModal .modal-body').html(result);
			}
		});
	});
 
});