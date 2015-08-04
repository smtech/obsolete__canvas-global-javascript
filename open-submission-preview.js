function stmarks_openSubmissionPreviewWhenLoaded() {
	// open the document preivew (if it's there)
	$('#preview_frame').contents().find('.content-box').find('.col-xs-5.align-right').find('a')[0].click();
	
}

function stmarks_openSubmissionPreview() {
	// not sure why I need to have the second delay -- but if I don't the document downloads, rather than previews
	$(document).ready( function() {
		$('#preview_frame').load(window.setTimeout(stmarks_openSubmissionPreviewWhenLoaded, 1000));
	});

	// open the rubric, if it's there
	$('.assess_submission_link.rubric').click();
}