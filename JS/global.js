function addOption(selectField, value, name){
	var opt = document.createElement('option');
	opt.value = value;
	opt.innerHTML = name;
	selectField.appendChild(opt);
}

function zoomIn(element){
	element.style.width = "120px";
	element.setAttribute("onclick","zoomOut(this)");
}

function zoomOut(element){
	element.style.width = "45px";
	element.setAttribute("onclick","zoomIn(this)");
}