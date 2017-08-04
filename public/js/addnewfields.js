var newfield = 0;

function addnewfields(btn)
{
	newfield++;

	var divElement = document.getElementById("keyvalues");

	var formGroupElement = document.createElement("div");
	formGroupElement.setAttribute("class", "form-group");
	formGroupElement.setAttribute("id", "formgroup" + newfield);

	var textKeyElement = document.createElement("input");	
	textKeyElement.setAttribute("type", "text");
	textKeyElement.setAttribute("placeholder", "Add new key");
	textKeyElement.setAttribute("class", "form-control edit key");
	textKeyElement.setAttribute("name", "newid" + newfield + "[]");

	var textValueElement = document.createElement("input");	
	textValueElement.setAttribute("type", "text");
	textValueElement.setAttribute("placeholder", "Add new value");
	textValueElement.setAttribute("class", "form-control edit value");
	textValueElement.setAttribute("name", "newid" + newfield + "[]");

	var removeElement = document.createElement("i");
	removeElement.setAttribute("class", "fa fa-times");
	removeElement.setAttribute("onclick", "removeFormElement(" + "'formgroup" + newfield + "')");

	formGroupElement.appendChild(textKeyElement);
	formGroupElement.appendChild(textValueElement);
	formGroupElement.appendChild(removeElement);

	divElement.appendChild(formGroupElement);	

}

function removeFormElement(elementToRemove)
{
	//alert(elementToRemove);
	var divElement = document.getElementById("keyvalues");
	var formGroupElement = document.getElementById(elementToRemove);
	divElement.removeChild(formGroupElement);
}

function removeUpdateDataElement(elementToRemove)
{
	var formElement = document.getElementById("updateData");
	var formGroupElement = document.getElementById(elementToRemove);
	formElement.removeChild(formGroupElement);
}
