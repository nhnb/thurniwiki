(function() {
	"use strict";

	document.addEventListener("DOMContentLoaded", function(event) {
		if (document.getElementById("content-editor")) {
			CKEDITOR.replace('content-editor');
		}
	});
}());