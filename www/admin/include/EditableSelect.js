
function getEditableSelectStyles() {
	var result="";
	var bg = "#FFF";
	var fg = "#000";
	result += "<style>\n";
	result += ".selectBoxArrow { margin-top:1px; float:left; position:absolute; right:1px; }\n";
	result += ".selectBoxInput { border:0px; padding-left:1px; background-color:" + bg + "; color:" + fg + "; height:16px; position:absolute; top:0px; left:0px; }\n";
	result += ".selectBoxSpacer { position: relative; float: left; height:20px; }\n";
	result += ".selectBox { position: absolute; z-index:1000; float: left; border:1px solid #7f9db9; background-color:" + bg + "; height:20px; }\n";
	result += ".selectBoxOptionContainer { z-index:1001; position:absolute; border:1px solid #7f9db9; height:100px; background-color:" + bg + "; left:-1px; top:20px; visibility:hidden; overflow:auto; }\n";
	result += ".selectBoxAnOption { z-index:1002; font-family:arial; font-size:12px; cursor:default; margin:1px; overflow:hidden; white-space:nowrap; }\n";
	result += ".selectBoxIframe { position:absolute; background-color:" + bg + "; border:0px; z-index:999; }\n";
	result += "</style>\n";
	return result;
}

function setEditableSelectImagePath(imgpath) {
	arrowImage = imgpath + '/select_arrow.gif';
	arrowImageOver = imgpath + '/select_arrow_over.gif';
	arrowImageDown = imgpath + '/select_arrow_down.gif';
}

	/************************************************************************************************************
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
	var arrowImage = './images/select_arrow.gif';	// Regular arrow
	var arrowImageOver = './images/select_arrow_over.gif';	// Mouse over
	var arrowImageDown = './images/select_arrow_down.gif';	// Mouse down

	
	var selectBoxIds = 0;
	var currentlyOpenedOptionBox = false;
	var editableSelect_activeArrow = false;
	

	
	function selectBox_switchImageUrl()
	{
		if(this.src.indexOf(arrowImage)>=0){
			this.src = this.src.replace(arrowImage,arrowImageOver);	
		}else{
			this.src = this.src.replace(arrowImageOver,arrowImage);
		}
		
		
	}
	
	function selectBox_showOptions()
	{
		if(editableSelect_activeArrow && editableSelect_activeArrow!=this){
			editableSelect_activeArrow.src = arrowImage;
			
		}
		editableSelect_activeArrow = this;
		
		var numId = this.id.replace(/[^\d]/g,'');
		var optionDiv = document.getElementById('selectBoxOptions' + numId);
		if(optionDiv.style.display=='inline'){
			if(navigator.userAgent.indexOf('MSIE')>=0)
				document.getElementById('selectBoxIframe' + numId).style.display='none';
			optionDiv.style.display='none';
			this.src = arrowImageOver;	
		}else{			
			if(navigator.userAgent.indexOf('MSIE')>=0)
				document.getElementById('selectBoxIframe' + numId).style.display='inline';
			optionDiv.style.display='inline';
			optionDiv.style.zIndex = 1000;
			this.src = arrowImageDown;	
			if(currentlyOpenedOptionBox && currentlyOpenedOptionBox!=optionDiv)currentlyOpenedOptionBox.style.display='none';	
			currentlyOpenedOptionBox= optionDiv;			
		}
	}
	
	function selectOptionValue()
	{
		var parentNode = this.parentNode.parentNode;
		var textInput = parentNode.getElementsByTagName('INPUT')[0];
		textInput.value = this.innerHTML;	
		this.parentNode.style.display='none';	
		document.getElementById('arrowSelectBox' + parentNode.id.replace(/[^\d]/g,'')).src = arrowImageOver;
		
		if(navigator.userAgent.indexOf('MSIE')>=0)
			document.getElementById('selectBoxIframe' + parentNode.id.replace(/[^\d]/g,'')).style.display='none';
		
	}
	var activeOption;
	function highlightSelectBoxOption()
	{
		if(this.style.backgroundColor=='#316AC5'){
			this.style.backgroundColor='';
			this.style.color='';
		}else{
			this.style.backgroundColor='#316AC5';
			this.style.color='#FFF';			
		}	
		
		if(activeOption){
			activeOption.style.backgroundColor='';
			activeOption.style.color='';			
		}
		activeOption = this;
		
	}
	
	function createEditableSelect(dest)
	{

		dest.className='selectBoxInput';		

		var spacer = document.createElement('DIV');
		spacer.style.styleFloat = 'left';
		spacer.style.width = dest.offsetWidth + 28 + 'px';
		spacer.id = 'selectBoxSpacer' + selectBoxIds;
		spacer.className='selectBoxSpacer';

		var div = document.createElement('DIV');
		div.style.styleFloat = 'left';
		div.style.width = dest.offsetWidth + 16 + 'px';
		div.id = 'selectBox' + selectBoxIds;

		var parent = dest.parentNode;
		parent.insertBefore(div,dest);
		parent.insertBefore(spacer,div);
		div.appendChild(dest);	
		div.className='selectBox';
		div.style.zIndex = 10000 - selectBoxIds;

		var img = document.createElement('IMG');
		img.src = arrowImage;
		img.className = 'selectBoxArrow';
		
		img.onmouseover = selectBox_switchImageUrl;
		img.onmouseout = selectBox_switchImageUrl;
		img.onclick = selectBox_showOptions;
		img.id = 'arrowSelectBox' + selectBoxIds;

		//Fix to close the selectBoxOptions if the text field is clicked again
		dest.onclick = function() {
			var numId2 = this.parentNode.id.replace(/[^\d]/g,'');
			sboptions = document.getElementById('selectBoxOptions' + numId2);
			sbarrow = document.getElementById('arrowSelectBox' + numId2);
			if (this.getAttribute('selectBoxForce') || sboptions.style.display == 'inline') sbarrow['onclick']();
		}

		div.appendChild(img);

		var optionDiv = document.createElement('DIV');
		optionDiv.id = 'selectBoxOptions' + selectBoxIds;
		optionDiv.className='selectBoxOptionContainer';
		optionDiv.style.width = div.offsetWidth-2 + 'px';
		
		if(navigator.userAgent.indexOf('MSIE')>=0){
			var iframe = document.createElement('IFRAME');
			iframe.src = 'about:blank';
			iframe.style.border = '0px';
			iframe.style.width = optionDiv.style.width;
			iframe.style.height = optionDiv.offsetHeight + 'px';
			iframe.style.display='none';
			iframe.id = 'selectBoxIframe' + selectBoxIds;
			div.appendChild(iframe);
		}

		div.appendChild(optionDiv);
		
		if(dest.getAttribute('selectBoxForce')){
			dest.onfocus = function() {
				this.blur();
			}
		}

		if(dest.getAttribute('selectBoxOptions')){
			var options = dest.getAttribute('selectBoxOptions').split(';');
			var optionsTotalHeight = 0;
			var optionArray = new Array();
			for(var no=0;no<options.length;no++){
				var anOption = document.createElement('DIV');
				anOption.innerHTML = options[no];
				anOption.className='selectBoxAnOption';
				anOption.onclick = selectOptionValue;
				anOption.style.width = optionDiv.style.width.replace('px','') - 2 + 'px'; 
				anOption.onmouseover = highlightSelectBoxOption;
				optionDiv.appendChild(anOption);	
				optionsTotalHeight = optionsTotalHeight + anOption.offsetHeight;
				optionArray.push(anOption);
			}
			if(optionsTotalHeight > optionDiv.offsetHeight){				
				for(var no=0;no<optionArray.length;no++){
					optionArray[no].style.width = optionDiv.style.width.replace('px','') - 22 + 'px'; 	
				}	
			}		
			optionDiv.style.display='none';
			optionDiv.style.visibility='visible';
		}
		
		selectBoxIds = selectBoxIds + 1;
	}	
