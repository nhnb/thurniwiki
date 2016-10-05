(function() {
	"use strict";

	function newfolder(event) {
		document.getElementById("newfieldset").style.display="block";
		document.getElementById("newlegend").textContent="Neues Verzeichnis";
		document.getElementById("newform").action="?action=index";
		document.getElementById("pagenamelabel").style.display="block";
		document.getElementById("file").style.display="none";
		document.getElementById("newtype").value="folder";
	}
	
	function newfile(event) {
		document.getElementById("newfieldset").style.display="block";
		document.getElementById("newlegend").textContent="Neue Datei hochladen";
		document.getElementById("newform").action="?action=upload";
		document.getElementById("pagenamelabel").style.display="none";
		document.getElementById("file").style.display="block";
	}

	function newpage(event) {
		document.getElementById("newfieldset").style.display="block";
		document.getElementById("newlegend").textContent="Neue Seite";
		document.getElementById("newform").action="?action=index";
		document.getElementById("pagenamelabel").style.display="block";
		document.getElementById("file").style.display="none";
		document.getElementById("newtype").value="page";
	}

	document.addEventListener("DOMContentLoaded", function(event) {
		if (document.getElementById("content-editor")) {
			CKEDITOR.replace('content-editor', {
			     height: "300"
			});
		}

		var element = document.getElementById("newfolder");
		if (element) {
			element.addEventListener("click", newfolder);
		}
		element = document.getElementById("newfile");
		if (element) {
			element.addEventListener("click", newfile);
		}
		element = document.getElementById("newpage");
		if (element) {
			element.addEventListener("click", newpage);
		}
	});
}());