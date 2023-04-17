function addOption(selectField, value, name){
	var opt = document.createElement('option');
	opt.value = value;
	opt.innerHTML = name;
	selectField.appendChild(opt);
}

/*
	Enable the zoom on the given element (width increases)
*/

function zoomIn(element){
	element.style.width = "120px";
	element.setAttribute("onclick","zoomOut(this)");
}

/*
	Disable the zoom on the given element (width decreases)
*/
function zoomOut(element){
	element.style.width = "45px";
	element.setAttribute("onclick","zoomIn(this)");
}