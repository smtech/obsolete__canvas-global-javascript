
function stmarks_showCachedRubric() {
	"use strict";
	var assignment = document.location.href;
	if (!/.*\/courses\/\d+\/assignments\/\d+$/.test(assignment)) {
		return;
	}
	
	var url = 'https://skunkworks.stmarksschool.org/canvas/lti/canvashack/hacks/cache-and-show-rubrics/show-rubrics.php';
	var http = new XMLHttpRequest();
	var params = 'assignment=' + encodeURIComponent(assignment);
	http.open('POST', url, true);
	http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	http.setRequestHeader("Content-length", params.length);
	http.setRequestHeader("Connection", "close");
	http.onreadystatechange = function() {
		if (http.readyState === 4 && http.status === 200) {
			var rubrics = document.createElement('div');
			var footer = document.getElementById('module_sequence_footer');
			var tool = document.getElementById('tool_content');
			footer.parentNode.insertBefore(rubrics, footer);
			//document.getElementById('content').removeClass('padless');
			rubrics.outerHTML = http.responseText;
			rubrics.style.display = 'block';
			if (tool.src == 'about:blank') {
				tool.style.height = '0px';
				tool.parentNode.style.height = 'auto';
			}
		}
	};
	http.send(params);
}