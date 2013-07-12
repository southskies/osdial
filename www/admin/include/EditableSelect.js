/************************************************************************************************************
	
	Copyright (C) 2010  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
	
	This file is part of OSDial.
	
	OSDial is free software: you can redistribute it and/or modify
	it under the terms of the GNU Affero General Public License as
	published by the Free Software Foundation, either version 3 of
	the License, or (at your option) any later version.
	
	OSDial is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU Affero General Public License for more details.
	
	You should have received a copy of the GNU Affero General Public
	License along with OSDial.  If not, see <http://www.gnu.org/licenses/>.
	
	************************************************************************************************************
	
	Original License Information
	
	Editable select
	Copyright (C) September 2005  DTHMLGoodies.com, Alf Magne Kalleland
	
	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.
	
	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.
	
	You should have received a copy of the GNU Lesser General Public
	License along with this library; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
	
	Dhtmlgoodies.com., hereby disclaims all copyright interest in this script
	written by Alf Magne Kalleland.
	
	Alf Magne Kalleland, 2006
	Owner of DHTMLgoodies.com
	
	************************************************************************************************************/

// Path to arrow images
var arrowImage = './images/select_arrow.gif'; // Regular arrow
var arrowImageOver = './images/select_arrow_over.gif'; // Mouse over
var arrowImageDown = './images/select_arrow_down.gif'; // Mouse down

var selectBoxIds = 0;
var currentlyOpenedOptionBox = false;
var editableSelect_activeArrow = false;
var activeOption;

function setEditableSelectImagePath(imgpath) {
	arrowImage = imgpath + '/select_arrow.gif';
	arrowImageOver = imgpath + '/select_arrow_over.gif';
	arrowImageDown = imgpath + '/select_arrow_down.gif';
}

function getEditableSelectStyles(bgColor, fgColor, borderColor) {
	if (!bgColor) bgColor = '#FFF';
	if (!fgColor) fgColor = '#000';
	if (!borderColor) borderColor = '#808080';
	var result = "<style>\n";
	result += ".selectBoxArrow { margin-top:1px; float:left; position:absolute; right:1px; }\n";
	result += ".selectBoxInput { border:0px;padding-left:0px;padding-right:0px;padding-bottom:0px;padding-top:2px;";
	result += "background-color:" + bgColor + ";color:" + fgColor + ";font-family:\"dejavu sans\";font-size:11px;";
	result += "height:16px;position:absolute;top:0px;left:0px;white-space:pre;}\n";
	result += "input.selectBoxInput:focus { outline:none;}\n";
	result += ".selectBoxValueLabel { border:0px; padding-top:3px; padding-left: 2px; background-color:" + bgColor + "; color:" + fgColor + ";";
	result += " height:16px; font-family:\"dejavu sans\"; font-size:11px; position:absolute; top:0px; left:0px; }\n";
	result += ".selectBoxSpacer { position: relative; float: left; height:20px;}\n";
	result += ".selectBox { position: absolute; z-index:1000; float: left; border:1px solid " + borderColor + "; background-color:" + bgColor + "; height:20px;}\n";
	result += ".selectBoxOptionContainer { z-index:1001; position:absolute; border:1px solid " + borderColor + ";";
	result += " border-radius: 0px 0px 6px 6px;box-shadow: rgba(0,0,1,1) 0px 0px 10px;-moz-border-radius:0px 0px 6px 6px;-webkit-border-radius:0px 0px 6px 6px;-khtml-border-radius:0px 0px 6px 6px;";
	result += " height:100px; background-color:" + bgColor + "; left:-1px; top:20px; visibility:hidden; overflow:auto; white-space:nowrap;}\n";
	result += ".selectBoxAnOption { z-index:1002; cursor:default; margin:1px; overflow:hidden; white-space:nowrap; }\n";
	result += ".selectBoxAnOptionLeft {float: left; font-family:\"dejavu sans\"; font-size:11px; cursor:default; overflow:hidden; white-space:pre; }\n";
	result += ".selectBoxAnOptionRight {float: left; font-family:\"dejavu sans\"; font-size:10px; cursor:default; overflow:hidden; white-space:nowrap; }\n";
	result += ".selectBoxAnOptionType {float: right; font-family:\"dejavu sans\"; font-size:8px; cursor:default; overflow:hidden; white-space:nowrap; }\n";
	result += ".selectBoxIframe { position:absolute; background-color:" + bgColor + "; border:0px; z-index:999; }\n";
	result += "</style>\n";
	return result;
}

function selectBox_switchImageUrl(e) {
	var numId = this.id.replace(/[^\d]/g, '');
	if (document.getElementById('selectBox' + numId).getElementsByTagName('INPUT')[0].readOnly) return;

	if (this.src.indexOf(arrowImage) >= 0) {
		this.src = this.src.replace(arrowImage, arrowImageOver);
	} else {
		this.src = this.src.replace(arrowImageOver, arrowImage);
	}
	if (!e) e = window.event;
	if (typeof(e) != "undefined") {
		if (e.stopPropagation) {
			e.stopPropagation();
		} else {
			e.cancelBubble = true;
		}
	}
}

function selectBox_showOptions(e) {
	if (editableSelect_activeArrow && editableSelect_activeArrow != this) editableSelect_activeArrow.src = arrowImage;
	editableSelect_activeArrow = this;

	var numId = this.id.replace(/[^\d]/g, '');
	if (document.getElementById('selectBox' + numId).getElementsByTagName('INPUT')[0].readOnly) return;

	var optionDiv = document.getElementById('selectBoxOptions' + numId);
	if (optionDiv.style.display == 'inline') {
		if (navigator.userAgent.indexOf('MSIE') >= 0) document.getElementById('selectBoxIframe' + numId).style.display = 'none';
		optionDiv.style.display = 'none';
		this.src = arrowImageOver;
	} else {
		if (navigator.userAgent.indexOf('MSIE') >= 0) document.getElementById('selectBoxIframe' + numId).style.display = 'inline';
		optionDiv.style.display = 'inline';
		optionDiv.style.zIndex = 1000;
		this.src = arrowImageDown;
		if (currentlyOpenedOptionBox && currentlyOpenedOptionBox != optionDiv) currentlyOpenedOptionBox.style.display = 'none';
		currentlyOpenedOptionBox = optionDiv;

		var curvalue = document.getElementById('selectBox' + numId).getElementsByTagName('INPUT')[0].value;
		for (var no = 0; no < optionDiv.children.length; no++) {
			var curopt = document.getElementById(numId + 'optionValue' + no);
			var curbox = document.getElementById(numId + 'optionBox' + no);
			if (curopt.innerHTML == curvalue) {
				optionDiv.scrollTop = curbox.offsetTop;
				curbox.style.fontWeight = 'bold';
				if (activeOption) {
					activeOption.style.backgroundColor = '';
					activeOption.style.color = '';
					//activeOption.style.fontWeight='normal';
				}
				activeOption = curbox;
				curbox.style.backgroundColor = '#316AC5';
				curbox.style.color = '#FFF';
			} else {
				if (typeof(curbox.style) != "undefined") curbox.style.fontWeight = 'normal';
			}
		}
	}
	if (!e) e = window.event;
	if (typeof(e) != "undefined") {
		if (e.stopPropagation) {
			e.stopPropagation();
		} else {
			e.cancelBubble = true;
		}
	}
}

function selectOptionValue() {
	var optIds = this.id.replace(/[^\d]+/g, '|').split('|');

	var textInput = document.getElementById('selectBox' + optIds[0]).getElementsByTagName('INPUT')[0];
	textInput.value = document.getElementById(optIds[0] + 'optionValue' + optIds[1]).innerHTML;

	var valueLabelDiv = document.getElementById('selectBoxValueLabel' + optIds[0]);
	valueLabelDiv.innerHTML = document.getElementById(optIds[0] + 'optionBox' + optIds[1]).innerHTML;
	valueLabelDiv.style.width = document.getElementById(optIds[0] + 'optionBox' + optIds[1]).offsetWidth - document.getElementById('arrowSelectBox' + optIds[0]).offsetWidth - 3 + 'px';
	valueLabelDiv.style.fontWeight = 'normal';
	valueLabelDiv.style.display = 'block';
	valueLabelDiv.style.opacity = '1.0';

	document.getElementById('selectBoxOptions' + optIds[0]).style.display = 'none';
	document.getElementById('arrowSelectBox' + optIds[0]).src = arrowImageOver;
	if (navigator.userAgent.indexOf('MSIE') >= 0) document.getElementById('selectBoxIframe' + optIds[0]).style.display = 'none';

	if (activeOption) {
		activeOption.style.backgroundColor = '';
		activeOption.style.color = '';
		activeOption.style.fontWeight = 'normal';
	}
	activeOption = undefined;
}

function highlightSelectBoxOption() {
	if (this.style.backgroundColor == '#316AC5') {
		this.style.backgroundColor = '';
		this.style.color = '';
	} else {
		this.style.backgroundColor = '#316AC5';
		this.style.color = '#FFF';
	}

	if (activeOption) {
		activeOption.style.backgroundColor = '';
		activeOption.style.color = '';
	}
	activeOption = this;
}

function selectBox_open(sbobj) {
	var numId = sbobj.parentNode.id.replace(/[^\d]/g, '');
	var sbopt = document.getElementById('selectBoxOptions' + numId);
	if (sbopt && sbopt.style && sbopt.style.display && sbopt.style.display != 'inline') document.getElementById('arrowSelectBox' + numId)['onclick']();
}

function selectBox_close(sbobj) {
	var numId = sbobj.parentNode.id.replace(/[^\d]/g, '');
	var sbopt = document.getElementById('selectBoxOptions' + numId);
	if (sbopt && sbopt.style && sbopt.style.display && sbopt.style.display == 'inline') document.getElementById('arrowSelectBox' + numId)['onclick']();
}

function createEditableSelect(dest) {
	dest.className = 'selectBoxInput';

	var options = dest.getAttribute('selectBoxOptions').split(';');
	var labels = new Array();
	if (dest.getAttribute('selectBoxLabels')) labels = dest.getAttribute('selectBoxLabels').split(';');
	var types = new Array();
	if (dest.getAttribute('selectBoxTypes')) types = dest.getAttribute('selectBoxTypes').split(';');

	// Calculate the longest option and adjust the input size.
	var newsize = 0;
	for (var no = 0; no < options.length; no++) {
		var tmp = '';
		if (options.length == labels.length) {
			if (labels[no] == undefined) labels[no] = '';
			if (labels[no] != '') {
				tmp += '  -  ';
			}
			tmp += labels[no];
		}
		if (options.length == types.length) {
			if (types[no] == undefined) types[no] = '';
			if (types[no] != '') {
				tmp += '  -  ';
			}
		}
		tmp += options[no];
		if (tmp.length > newsize) newsize = tmp.length;
	}
	dest.size = newsize + 7;

	// Turn off broweser textbox autocompletion
	if (!dest.getAttribute('autocomplete')) dest.setAttribute('autocomplete', 'off');

	var spacer = document.createElement('DIV');
	spacer.style.styleFloat = 'left';
	spacer.style.width = dest.offsetWidth + 28 + 'px';
	spacer.id = 'selectBoxSpacer' + selectBoxIds;
	spacer.className = 'selectBoxSpacer';

	var div = document.createElement('DIV');
	div.style.styleFloat = 'left';
	div.style.width = dest.offsetWidth + 16 + 'px';
	div.id = 'selectBox' + selectBoxIds;

	var parent = dest.parentNode;
	parent.insertBefore(div, dest);
	parent.insertBefore(spacer, div);
	div.appendChild(dest);
	div.className = 'selectBox';
	div.style.zIndex = 10000 - selectBoxIds;

	var img = document.createElement('IMG');
	img.src = arrowImage;
	img.className = 'selectBoxArrow';

	img.onmouseover = selectBox_switchImageUrl;
	img.onmouseout = selectBox_switchImageUrl;
	img.onclick = selectBox_showOptions;
	img.id = 'arrowSelectBox' + selectBoxIds;

	dest.onload = function() {
		var numId2 = this.parentNode.id.replace(/[^\d]/g, '');
		var sfld = document.getElementById('selectBox' + numId2);
		var curvalue = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value;
		var sboptions = document.getElementById('selectBoxOptions' + numId2);
		var valueLabelDiv = document.getElementById('selectBoxValueLabel' + numId2);

		if (curvalue == "") {
			if (this.getAttribute('selectBoxLabel')) {
				valueLabelDiv.innerHTML = this.getAttribute('selectBoxLabel');
				valueLabelDiv.style.width = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].offsetWidth - 3 + 'px';
				valueLabelDiv.style.fontWeight = 'normal';
				valueLabelDiv.style.display = 'block';
				valueLabelDiv.style.opacity = '1.0';
				if (this.getAttribute('selectBoxForce')) {
					//this.onfocus = function() {
					//	this.blur();
					//}
					valueLabelDiv.onclick = function() {
						var valueLabelId = this.id.replace(/[^\d]/g, '');
						var arrowsel = document.getElementById('arrowSelectBox' + valueLabelId);
						arrowsel.click();
					}
				} else {
					valueLabelDiv.onclick = function() {
						var valueLabelId = this.id.replace(/[^\d]/g, '');
						var minput = document.getElementById('selectBox' + valueLabelId).getElementsByTagName('INPUT')[0];
						var sboptions = document.getElementById('selectBoxOptions' + valueLabelId);
						if (sboptions.style.display == 'inline') {
							selectBox_close(minput);
						} else {
							document.getElementById('selectBoxValueLabel' + valueLabelId).style.display = 'none';
							minput.focus();
						}
					}
				}
			}
		} else {
			document.getElementById('arrowSelectBox' + numId2).click();
			var selfound = 0;
			for (var no = 0; no < sboptions.children.length; no++) {
				var curopt = document.getElementById(numId2 + 'optionValue' + no);
				var curbox = document.getElementById(numId2 + 'optionBox' + no);
				if (curopt.innerHTML == curvalue) {
					selfound++;
					sboptions.scrollTop = curbox.offsetTop;
					if (activeOption) {
						activeOption.style.backgroundColor = '';
						activeOption.style.color = '';
						activeOption.style.fontWeight = 'normal';
					}
					activeOption = curbox;
					curbox.style.fontWeight = 'bold';
					curbox.style.backgroundColor = '#316AC5';
					curbox.style.color = '#FFF';
					valueLabelDiv.innerHTML = curbox.innerHTML;
					valueLabelDiv.style.width = curbox.offsetWidth - document.getElementById('arrowSelectBox' + numId2).offsetWidth - 3 + 'px';
					valueLabelDiv.style.fontWeight = 'normal';
					valueLabelDiv.style.display = 'block';
					valueLabelDiv.style.opacity = '1.0';
					break;
				} else {
					curbox.style.fontWeight = 'normal';
					valueLabelDiv.style.display = 'none';
				}
			}
			if (selfound == 0) {
				valueLabelDiv.style.display = 'none';
			}
			document.getElementById('arrowSelectBox' + numId2).click();
		}
	}

	//Fix to close the selectBoxOptions if the text field is clicked again
	dest.onclick = function() {
		var numId2 = this.parentNode.id.replace(/[^\d]/g, '');
		var sboptions = document.getElementById('selectBoxOptions' + numId2);
		if (sboptions.style.display == 'inline' || this.getAttribute('selectBoxForce')) selectBox_close(this);
	}

	dest.ondblclick = function() {
		var numId2 = this.parentNode.id.replace(/[^\d]/g, '');
		var sboptions = document.getElementById('selectBoxOptions' + numId2);
		if (sboptions.style.display == 'inline') {
			selectBox_close(this);
		} else {
			selectBox_open(this);
		}
	}

	// Catch TAB and close selection if open.
	dest.onkeydown = function(evt) {
		var key;
		if (evt.keyCode) {
			key = evt.keyCode;
		} else if (typeof(e.which) != 'undefined') {
			key = evt.which;
		}

		var numId2 = this.parentNode.id.replace(/[^\d]/g, '');
		var sboptions = document.getElementById('selectBoxOptions' + numId2);
		var valueLabelDiv = document.getElementById('selectBoxValueLabel' + numId2);
		if (key == 9) {
			if (!sboptions.style.display == 'inline') selectBox_open(sboptions);
			var selfound = 0;
			var curvalue = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value;
			for (var no = 0; no < sboptions.children.length; no++) {
				var curopt = document.getElementById(numId2 + 'optionValue' + no);
				var curbox = document.getElementById(numId2 + 'optionBox' + no);
				curbox.style.backgroundColor = '';
				curbox.style.color = '';
				curbox.style.fontWeight = 'normal';
				curbox = activeOption;
				var optIds;
				if (typeof(curbox) != 'undefined') {
					optIds = curbox.id.replace(/[^\d]+/g, '|').split('|');
					curopt = document.getElementById(optIds[0] + 'optionValue' + optIds[1]);
					document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value = curopt.innerHTML;
				}
				evt.cancel = true;
				evt.returnValue = false;
				if (typeof(curbox) != 'undefined') {
					sboptions.scrollTop = curbox.offsetTop;
					curbox.style.backgroundColor = '#316AC5';
					curbox.style.color = '#FFF';
					curbox.style.fontWeight = 'bold';
				}
				if (typeof(curbox) != 'undefined') {
					valueLabelDiv.innerHTML = curbox.innerHTML;
					valueLabelDiv.style.width = curbox.offsetWidth - document.getElementById('arrowSelectBox' + numId2).offsetWidth - 3 + 'px';
					valueLabelDiv.style.fontWeight = 'normal';
					valueLabelDiv.style.display = 'block';
					valueLabelDiv.style.opacity = '1.0';
				}
				if (sboptions.style.display == 'inline') selectBox_close(sboptions);
				var nodes = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].form.querySelectorAll('select:enabled, input:enabled:not([type="hidden"]), a[href]');
				for (var i = 0; i < nodes.length; i++) {
					if (nodes[i].name == document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].name) {
						var newi = i + 1;
						if (newi == nodes.length) newi = 0;
						nodes[newi].focus();
						break;
					}
				}
				break;
			}
			if (sboptions.style.display == 'inline') selectBox_close(sboptions);
			var nodes = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].form.querySelectorAll('select:enabled, input:enabled:not([type="hidden"]), a[href]');
			for (var i = 0; i < nodes.length; i++) {
				if (nodes[i].name == document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].name) {
					var newi = i + 1;
					if (newi == nodes.length) newi = 0;
					nodes[newi].focus();
					break;
				}
			}
		} else if (key == 35) {
			if (!sboptions.style.display == 'inline') selectBox_open(sboptions);
			var selfound = 0;
			var curvalue = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value;
			for (var no = 0; no < sboptions.children.length; no++) {
				var curopt = document.getElementById(numId2 + 'optionValue' + no);
				var curbox = document.getElementById(numId2 + 'optionBox' + no);
				curbox.style.backgroundColor = '';
				curbox.style.color = '';
				curbox.style.fontWeight = 'normal';
				curbox = activeOption;
				var optIds;
				if (typeof(curbox) != 'undefined') {
					optIds = curbox.id.replace(/[^\d]+/g, '|').split('|');
					curopt = document.getElementById(optIds[0] + 'optionValue' + optIds[1]);
					document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value = curopt.innerHTML;
				}
				evt.cancel = true;
				evt.returnValue = false;
				if (typeof(curbox) != 'undefined') {
					sboptions.scrollTop = curbox.offsetTop;
					curbox.style.backgroundColor = '#316AC5';
					curbox.style.color = '#FFF';
					curbox.style.fontWeight = 'bold';
				}
				if (typeof(curbox) != 'undefined') {
					valueLabelDiv.innerHTML = curbox.innerHTML;
					valueLabelDiv.style.width = curbox.offsetWidth - document.getElementById('arrowSelectBox' + numId2).offsetWidth - 3 + 'px';
					valueLabelDiv.style.fontWeight = 'normal';
					valueLabelDiv.style.display = 'block';
					valueLabelDiv.style.opacity = '1.0';
				}
				if (sboptions.style.display == 'inline') selectBox_close(sboptions);
				var nodes = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].form.querySelectorAll('select:enabled, input:enabled:not([type="hidden"]), a[href]');
				for (var i = 0; i < nodes.length; i++) {
					if (nodes[i].name == document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].name) {
						var newi = i + 1;
						if (newi == nodes.length) newi = 0;
						nodes[newi].focus();
						break;
					}
				}
				break;
			}
			if (sboptions.style.display == 'inline') selectBox_close(sboptions);
		} else if (key == 27) {
			if (!sboptions.style.display == 'inline') selectBox_open(sboptions);
			var iput = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0];
			iput.value = iput.defaultValue;
			var curvalue = iput.value;
			var selfound = 0;
			for (var no = 0; no < sboptions.children.length; no++) {
				var curopt = document.getElementById(numId2 + 'optionValue' + no);
				var curbox = document.getElementById(numId2 + 'optionBox' + no);
				if (curvalue == curopt.innerHTML) {
					selfound++;
					evt.cancel = true;
					evt.returnValue = false;
					//iput.blur();
					valueLabelDiv.innerHTML = curbox.innerHTML;
					valueLabelDiv.style.width = curbox.offsetWidth - document.getElementById('arrowSelectBox' + numId2).offsetWidth - 3 + 'px';
					valueLabelDiv.style.fontWeight = 'normal';
					valueLabelDiv.style.display = 'block';
					valueLabelDiv.style.opacity = '1.0';
					//iput.focus();
					selectBox_close(sboptions);
					break;
				}
			}
			if (selfound == 0) {
				evt.cancel = true;
				evt.returnValue = false;
				//iput.blur();
				valueLabelDiv.innerHTML = iput.value;
				valueLabelDiv.style.width = iput.offsetWidth - 3 + 'px';
				valueLabelDiv.style.fontWeight = 'normal';
				valueLabelDiv.style.display = 'block';
				valueLabelDiv.style.opacity = '1.0';
				//iput.focus();
				selectBox_close(sboptions);
			}
			if (sboptions.style.display == 'inline') selectBox_close(sboptions);
			return false;
		} else {
			if (this.getAttribute('selectBoxForce')) {
				evt.cancel = true;
				evt.returnValue = false;
				if (document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value == "") {
					if (this.getAttribute('selectBoxLabel')) {
						valueLabelDiv.innerHTML = this.getAttribute('selectBoxLabel');
						valueLabelDiv.style.width = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].offsetWidth - 3 + 'px';
						valueLabelDiv.style.fontWeight = 'normal';
						valueLabelDiv.style.display = 'block';
						valueLabelDiv.style.opacity = '1.0';
					}
				}
				return false;
			}
		}
	}

	// Open on keypress except on TAB and select the current matching option.
	dest.onkeyup = function(evt) {
		var key;
		if (evt.keyCode) {
			key = evt.keyCode;
		} else if (typeof(e.which) != 'undefined') {
			key = evt.which;
		}
		var numId2 = this.parentNode.id.replace(/[^\d]/g, '');
		var sboptions = document.getElementById('selectBoxOptions' + numId2);
		var valueLabelDiv = document.getElementById('selectBoxValueLabel' + numId2);

		if (key != 9 && key != 35) {
			selectBox_open(this);
			var selfound = 0;
			for (var no = 0; no < sboptions.children.length; no++) {
				var curvalue = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value;
				var curopt = document.getElementById(numId2 + 'optionValue' + no);
				var curbox = document.getElementById(numId2 + 'optionBox' + no);
				if (curopt.innerHTML == curvalue) {
					selfound++;
					sboptions.scrollTop = curbox.offsetTop;
					if (activeOption) {
						activeOption.style.backgroundColor = '';
						activeOption.style.color = '';
						activeOption.style.fontWeight = 'normal';
					}
					activeOption = curbox;
					curbox.style.fontWeight = 'bold';
					curbox.style.backgroundColor = '#316AC5';
					curbox.style.color = '#FFF';
					valueLabelDiv.innerHTML = curbox.innerHTML;
					valueLabelDiv.style.width = curbox.offsetWidth - document.getElementById('arrowSelectBox' + numId2).offsetWidth - 3 + 'px';
					valueLabelDiv.style.fontWeight = 'normal';
					valueLabelDiv.style.display = 'block';
					valueLabelDiv.style.opacity = '1.0';
					break;
				} else if (curvalue.length > 0 && curopt.innerHTML.substring(0, curvalue.length) == curvalue) {
					selfound++;
					sboptions.scrollTop = curbox.offsetTop;
					if (activeOption) {
						activeOption.style.backgroundColor = '';
						activeOption.style.color = '';
						activeOption.style.fontWeight = 'normal';
					}
					activeOption = curbox;
					curbox.style.fontWeight = 'bold';
					curbox.style.backgroundColor = '#316AC5';
					curbox.style.color = '#FFF';
					valueLabelDiv.innerHTML = curbox.innerHTML;
					valueLabelDiv.style.width = curbox.offsetWidth - document.getElementById('arrowSelectBox' + numId2).offsetWidth - 3 + 'px';
					valueLabelDiv.style.fontWeight = 'normal';
					valueLabelDiv.style.display = 'block';
					valueLabelDiv.style.opacity = '0.4';
					break;
				} else {
					curbox.style.fontWeight = 'normal';
					valueLabelDiv.style.display = 'none';
				}
			}
			if (selfound == 0) {
				var curopt = document.getElementById(numId2 + 'optionValue0');
				var curbox = document.getElementById(numId2 + 'optionBox0');
				//document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value=curopt.innerHTML;
				sboptions.scrollTop = curbox.offsetTop;
				activeOption = curbox;
				curbox.style.backgroundColor = '#316AC5';
				curbox.style.color = '#FFF';
				valueLabelDiv.innerHTML = curbox.innerHTML;
				valueLabelDiv.style.width = curbox.offsetWidth - document.getElementById('arrowSelectBox' + numId2).offsetWidth - 3 + 'px';
				valueLabelDiv.style.fontWeight = 'normal';
				valueLabelDiv.style.display = 'block';
				valueLabelDiv.style.opacity = '1.0';
			}
		}
		if (key == 38) {
			// Up
			if (sboptions.style.display == 'inline') {
				var curvalue = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value;
				for (var no = 0; no < sboptions.children.length; no++) {
					var curopt = document.getElementById(numId2 + 'optionValue' + no);
					var curbox = document.getElementById(numId2 + 'optionBox' + no);
					if (curopt.innerHTML == curvalue) {
						curbox.style.fontWeight = 'bold';
					}
					if (activeOption == curbox) {
						var nxtno = no - 1;
						if (nxtno < 0) {
							nxtno = sboptions.children.length - 1;
						}
						var nxtopt = document.getElementById(numId2 + 'optionValue' + nxtno);
						var nxtbox = document.getElementById(numId2 + 'optionBox' + nxtno);
						document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value = nxtopt.innerHTML;
						sboptions.scrollTop = nxtbox.offsetTop;
						if (activeOption) {
							activeOption.style.backgroundColor = '';
							activeOption.style.color = '';
							activeOption.style.fontWeight = 'normal';
						}
						activeOption = nxtbox;
						nxtbox.style.backgroundColor = '#316AC5';
						nxtbox.style.color = '#FFF';
						nxtbox.style.fontWeight = 'bold';
						valueLabelDiv.innerHTML = nxtbox.innerHTML;
						valueLabelDiv.style.width = nxtbox.offsetWidth - document.getElementById('arrowSelectBox' + numId2).offsetWidth - 3 + 'px';
						valueLabelDiv.style.fontWeight = 'normal';
						valueLabelDiv.style.display = 'block';
						valueLabelDiv.style.opacity = '1.0';
						break;
					}
				}
			}
		} else if (key == 40) {
			// Down
			if (sboptions.style.display == 'inline') {
				var curvalue = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value;
				for (var no = 0; no < sboptions.children.length; no++) {
					var curopt = document.getElementById(numId2 + 'optionValue' + no);
					var curbox = document.getElementById(numId2 + 'optionBox' + no);
					if (curopt.innerHTML == curvalue) {
						curbox.style.fontWeight = 'bold';
					}
					if (activeOption == curbox) {
						var nxtno = no + 1;
						if (nxtno + 1 > sboptions.children.length) {
							nxtno = 0;
						}
						var nxtopt = document.getElementById(numId2 + 'optionValue' + nxtno);
						var nxtbox = document.getElementById(numId2 + 'optionBox' + nxtno);
						document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value = nxtopt.innerHTML;
						sboptions.scrollTop = nxtbox.offsetTop;
						if (activeOption) {
							activeOption.style.backgroundColor = '';
							activeOption.style.color = '';
							activeOption.style.fontWeight = 'normal';
						}
						activeOption = nxtbox;
						nxtbox.style.backgroundColor = '#316AC5';
						nxtbox.style.color = '#FFF';
						nxtbox.style.fontWeight = 'bold';
						valueLabelDiv.innerHTML = nxtbox.innerHTML;
						valueLabelDiv.style.width = nxtbox.offsetWidth - document.getElementById('arrowSelectBox' + numId2).offsetWidth - 3 + 'px';
						valueLabelDiv.style.fontWeight = 'normal';
						valueLabelDiv.style.display = 'block';
						valueLabelDiv.style.opacity = '1.0';
						break;
					}
				}
			}
		} else {
			var selfound = 0;
			var curvalue = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value;
			for (var no = 0; no < sboptions.children.length; no++) {
				curvalue = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].value;
				var curopt = document.getElementById(numId2 + 'optionValue' + no);
				var curbox = document.getElementById(numId2 + 'optionBox' + no);
				if (curopt.innerHTML == curvalue) {
					selfound++;
					sboptions.scrollTop = curbox.offsetTop;
					if (activeOption) {
						activeOption.style.backgroundColor = '';
						activeOption.style.color = '';
						activeOption.style.fontWeight = 'normal';
					}
					activeOption = curbox;
					curbox.style.fontWeight = 'bold';
					curbox.style.backgroundColor = '#316AC5';
					curbox.style.color = '#FFF';
					valueLabelDiv.innerHTML = curbox.innerHTML;
					valueLabelDiv.style.width = curbox.offsetWidth - document.getElementById('arrowSelectBox' + numId2).offsetWidth - 3 + 'px';
					valueLabelDiv.style.fontWeight = 'normal';
					valueLabelDiv.style.display = 'block';
					valueLabelDiv.style.opacity = '1.0';
					break;
				} else if (curvalue.length > 0 && curopt.innerHTML.substring(0, curvalue.length) == curvalue) {
					sboptions.scrollTop = curbox.offsetTop;
					if (activeOption) {
						activeOption.style.backgroundColor = '';
						activeOption.style.color = '';
						activeOption.style.fontWeight = 'normal';
					}
					activeOption = curbox;
					curbox.style.backgroundColor = '#316AC5';
					curbox.style.color = '#FFF';
					valueLabelDiv.innerHTML = curbox.innerHTML;
					valueLabelDiv.style.width = curbox.offsetWidth - document.getElementById('arrowSelectBox' + numId2).offsetWidth - 3 + 'px';
					valueLabelDiv.style.fontWeight = 'normal';
					valueLabelDiv.style.display = 'block';
					valueLabelDiv.style.opacity = '0.4';
					break;
				} else {
					curbox.style.fontWeight = 'normal';
					if (activeOption) {
						activeOption.style.backgroundColor = '';
						activeOption.style.color = '';
					}
					activeOption = undefined;
					valueLabelDiv.style.display = 'none';
				}
			}
			if (curvalue.length == 0) {
				if (this.getAttribute('selectBoxForce')) {
					evt.cancel = true;
					evt.returnValue = false;
					if (this.getAttribute('selectBoxLabel')) {
						valueLabelDiv.innerHTML = this.getAttribute('selectBoxLabel');
						valueLabelDiv.style.width = document.getElementById('selectBox' + numId2).getElementsByTagName('INPUT')[0].offsetWidth - 3 + 'px';
						valueLabelDiv.style.fontWeight = 'normal';
						valueLabelDiv.style.display = 'block';
						valueLabelDiv.style.opacity = '1.0';
					}
					return false;
				} else {
					var curbox = document.getElementById(numId2 + 'optionBox0');
					sboptions.scrollTop = curbox.offsetTop;
					valueLabelDiv.style.display = 'none';
				}
			}
		}
	}

	div.appendChild(img);

	var optionDiv = document.createElement('DIV');

	optionDiv.onblur = function() {
		selectBox_close(this);
	};
	optionDiv.id = 'selectBoxOptions' + selectBoxIds;
	optionDiv.className = 'selectBoxOptionContainer';
	optionDiv.style.width = div.offsetWidth - 2 + 'px';

	if (navigator.userAgent.indexOf('MSIE') >= 0) {
		var iframe = document.createElement('IFRAME');
		iframe.src = 'about:blank';
		iframe.style.border = '0px';
		iframe.style.width = optionDiv.style.width;
		iframe.style.height = optionDiv.offsetHeight + 'px';
		iframe.style.display = 'none';
		iframe.id = 'selectBoxIframe' + selectBoxIds;
		div.appendChild(iframe);
	}

	var valueLabelDiv = document.createElement('DIV');
	valueLabelDiv.id = 'selectBoxValueLabel' + selectBoxIds;
	valueLabelDiv.className = 'selectBoxValueLabel';
	div.appendChild(valueLabelDiv);

	div.appendChild(optionDiv);

	if (dest.getAttribute('selectBoxForce')) {
		//dest.onfocus = function() {
		//	this.blur();
		//}
		valueLabelDiv.onclick = function() {
			var valueLabelId = this.id.replace(/[^\d]/g, '');
			var arrowsel = document.getElementById('arrowSelectBox' + valueLabelId);
			arrowsel.click();
		}
	} else {
		valueLabelDiv.onclick = function() {
			var valueLabelId = this.id.replace(/[^\d]/g, '');
			var minput = document.getElementById('selectBox' + valueLabelId).getElementsByTagName('INPUT')[0];
			var sboptions = document.getElementById('selectBoxOptions' + valueLabelId);
			if (sboptions.style.display == 'inline') {
				selectBox_close(minput);
			} else {
				document.getElementById('selectBoxValueLabel' + valueLabelId).style.display = 'none';
				minput.focus();
			}
		}
	}

	if (dest.getAttribute('selectBoxOptions')) {
		var optionsTotalHeight = 0;
		var optionsMaxWidth = 0;
		var optionArray = new Array();

		for (var no = 0; no < options.length; no++) {
			var anOption = document.createElement('DIV');
			anOption.id = selectBoxIds + 'optionBox' + no;
			anOption.className = 'selectBoxAnOption';
			anOption.onclick = selectOptionValue;
			anOption.style.width = optionDiv.style.width.replace('px', '') - 2 + 'px';
			anOption.onmouseover = highlightSelectBoxOption;

			var anOption1 = document.createElement('DIV');
			anOption1.className = 'selectBoxAnOptionLeft';
			anOption1.innerHTML = options[no];
			anOption1.id = selectBoxIds + 'optionValue' + no;
			anOption.appendChild(anOption1);

			if (options.length == labels.length) {
				var anOptionS = document.createElement('DIV');
				anOptionS.className = 'selectBoxAnOptionLeft';
				anOptionS.innerHTML = '  -  ';
				//anOptionS.innerHTML = '&nbsp;&nbsp;-&nbsp;&nbsp;';
				anOptionS.id = selectBoxIds + 'optionSep' + no;
				anOption.appendChild(anOptionS);

				var anOption2 = document.createElement('DIV');
				anOption2.className = 'selectBoxAnOptionRight';
				anOption2.innerHTML = labels[no];
				anOption2.id = selectBoxIds + 'optionLabel' + no;
				anOption.appendChild(anOption2);
			}

			if (options.length == types.length) {
				var anOptionT = document.createElement('DIV');
				anOptionT.className = 'selectBoxAnOptionType';
				anOptionT.innerHTML = types[no];
				anOptionT.id = selectBoxIds + 'optionType' + no;
				anOption.appendChild(anOptionT);
			}

			optionDiv.appendChild(anOption);
			optionsTotalHeight = optionsTotalHeight + anOption.offsetHeight;
			if (anOption1.offsetWidth > optionsMaxWidth) optionsMaxWidth = anOption1.offsetWidth;
			optionArray.push(anOption);
		}
		for (var no = 0; no < optionArray.length; no++) {
			document.getElementById(selectBoxIds + 'optionValue' + no).style.width = optionsMaxWidth + 'px';
			if (optionsTotalHeight > optionDiv.offsetHeight) optionArray[no].style.width = optionDiv.style.width.replace('px', '') - 22 + 'px';
		}
		optionDiv.style.display = 'none';
		optionDiv.style.visibility = 'visible';
	}

	var sfld = document.getElementById('selectBox' + selectBoxIds);
	var ifld = sfld.getElementsByTagName('INPUT')[0];
	sfld.disable = function() {
		this.style.opacity = '0.5';
		this.getElementsByTagName('INPUT')[0].value = this.getElementsByTagName('INPUT')[0].defaultValue;
		this.getElementsByTagName('INPUT')[0].readOnly = true;
	};
	ifld.disable = function() {
		this.parentNode.style.opacity = '0.5';
		this.value = this.defaultValue;
		this.readOnly = true;
	};
	sfld.enable = function() {
		this.style.opacity = '1';
		this.getElementsByTagName('INPUT')[0].readOnly = false;
	};
	ifld.enable = function() {
		this.parentNode.style.opacity = '1';
		this.readOnly = false;
	};
	if (ifld.disabled) {
		ifld.disabled = false;
		sfld.disable();
	}

	var selectBoxCloseAll = function(e) {
		selectBox_close(ifld);
	};
	if (document.body.addEventListener) {
		document.body.addEventListener('click', selectBoxCloseAll, false);
	} else if (document.body.attachEvent) {
		document.body.attachEvent("on" + 'click', selectBoxCloseAll);
	} else {
		document.body["on" + 'click'] = selectBoxCloseAll;
	}

	selectBoxIds = selectBoxIds + 1;
}

