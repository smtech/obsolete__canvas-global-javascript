
function stmarks_showCachedRubric() {
	"use strict";
	var assignment = document.location.href;
	if (!/.*\/courses\/\d+\/assignments\/\d+$/.test(assignment)) {
		return;
	}
	
	function displayCache() {
		if (http.readyState === 4 && http.status === 200) {
			var rubrics = document.createElement('div');
			var tool = $('.tool_content_wrapper')[0];
			tool.parentNode.insertBefore(rubrics, tool);
			rubrics.outerHTML = http.responseText;
			rubrics.style.display = 'block';
		}
	}
	
	var url = 'https://skunkworks.stmarksschool.org/canvas/lti/canvashack/hacks/cache-and-show-rubrics/show-rubrics.php';
	var http = new XMLHttpRequest();
	var params = 'assignment=' + encodeURIComponent(assignment);
	http.open('POST', url);
	http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	//http.setRequestHeader("Content-length", params.length);
	//http.setRequestHeader("Connection", "close");
	http.addEventListener('load', displayCache);
	http.send(params);
}