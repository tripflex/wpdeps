
jQuery(function($) {

	// Add Toggle Link
	$('.plugins tr.dependencies-active').each(function(){
		var id = $(this).attr('id');
		$(this).prev().find('.row-actions').append(' | <a href="#' + id + '" class="dependencies-toggle" data-toggle-text="Hide Dependencies">Show Dependencies</a>');
	});

	// Handle Toggle Link
	$('a.dependencies-toggle').on('click', function(e){
		var href = $(this).attr('href');
		var text = $(this).text();
		var toggle_text = $(this).attr('data-toggle-text');
		$(href).toggle();
		$(this).attr('data-toggle-text', text).text(toggle_text);
		e.preventDefault();
	});

});
