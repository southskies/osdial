/************************************************************************************************************
        
Copyright (C) 2013  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
        
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

This is a color picker addon!!!

set attribute mode=colorpicker on any text input.
to hide text input box, set hideInput=1
size will use size or style of input box, this can be overriden by setting forceWidth.

<input type=text name=color mode=colorpicker size=10><br>
<input type=text name=color2 mode=colorpicker size=25><br>
<input type=text name=color4 mode=colorpicker style='width:300px' size=10><br>
<input type=text name=color3 mode=colorpicker hideInput=1 size=10><br>
<input type=text name=color3 mode=colorpicker hideInput=1 forceWidth=300 size=10><br>


************************************************************************************************************/

var OSDCP = OSDCP || {};
OSDCP.changePickerColor = function(a, b) {
	typeof OnColorChanged !== "undefined" && OnColorChanged(a, b)
};
OSDCP.ColorPicker = function() {
	if (typeof(arrowImage) == "undefined") arrowImage = 'select_arrow.gif';
	if (typeof(arrowImageOver) == "undefined") arrowImageOver = 'select_arrow_over.gif';
	if (typeof(arrowImageDown) == "undefined") arrowImageDown = 'select_arrow_down.gif';

	var onEvent = function(target, etype, func) {
		if (target.addEventListener) {
			target.addEventListener(etype, func, false);
		} else if (target.attachEvent) {
			target.attachEvent("on" + etype, func);
		} else {
			target["on" + etype] = func;
		}
	};

	var rgb2hex = function(color) {
		if (color.substr(0, 1) != '#') {
			if (color.indexOf('rgb') >= 0) {
				var rgbre = /(.*?)rgb\((\d+), (\d+), (\d+)\)/.exec(color);
				var r = parseInt(rgbre[2]);
				var g = parseInt(rgbre[3]);
				var b = parseInt(rgbre[4]);
				var rgb = b | (g << 8) | (r << 16);
				color = rgbre[1] + '#' + new String('000000' + rgb.toString(16)).slice( - 6);
			} else {
				color = '#FFFFFF';
			}
		}
		return color.toUpperCase();
	};

	var picker;

	var Picker = function() {
		this.colornamediv = this.colorsamplediv = this.colorgriddiv = null;
		this.count = - 1;
		this.Input = [];
		this.Span = [];
		this.createPicker();
	};

	Picker.prototype = {
		stopEvent: function(e) {
			if (!e) e = window.event;
			if (e.stopPropagation) {
				e.stopPropagation();
			} else {
				e.cancelBubble = true;
				e.stopEvent && e.stopEvent();
			}
		},

		createPicker: function() {
			this.pickerdiv = document.createElement('div');
			this.pickerdiv.id = 'picker';
			this.colornamediv = document.createElement('div');
			this.colornamediv.id = 'colorname';
			this.colorsamplediv = document.createElement('div');
			this.colorsamplediv.id = 'colorsample';
			this.colorgriddiv = document.createElement('div');
			this.colorgriddiv.id = 'colorgrid';

			this.pickerActions();

			this.pickerdiv.appendChild(this.colornamediv);
			this.pickerdiv.appendChild(this.colorsamplediv);
			this.pickerdiv.appendChild(this.colorgriddiv);

			this.makeColorGrid();
			this.pickerLoadInput();

			var picker = this;
			onEvent(document.body, "click", function() {
				if (picker.count > - 1) picker.Span[picker.count].style.zIndex = 1;
				picker.hidePicker();
			});
		},

		pickerActions: function() {
			onEvent(this.colorgriddiv, 'mouseover', this.gridMouseOver);
			onEvent(this.colorgriddiv, 'click', this.gridClick);
		},

		pickerLoadInput: function() {
			var inputs = document.getElementsByTagName("input");
			var me = this;
			for (var i = 0; i < inputs.length; i++) {
				if (inputs[i].getAttribute('mode') == 'colorpicker') {
					var count = this.Input.length;
					if (inputs[i].count === undefined) {
						this.Input[count] = inputs[i];
						this.Input[count].count = count;
						var inputwidth = this.Input[count].offsetWidth;
						if (this.Input[count].getAttribute('forceWidth') > 0) inputwidth = this.Input[count].getAttribute('forceWidth');
						if (this.Input[count].getAttribute('hideInput') == '1') this.Input[count].type = 'hidden';
						this.Input[count].onchange = function() {
							me.tryBackground(me.Span[this.count], this);
						};
						this.Span[count] = document.createElement('span');
						this.Span[count].count = count;
						this.Span[count].className = 'pickerMain';
						this.Span[count].style.width = inputwidth;
						this.Span[count].onselectstart = function() {
							return false
						};
						var span2 = document.createElement('span');
						span2.style.padding = 0;
						span2.style.margin = 0;
						span2.style.width = inputwidth - 4;
						span2.style.border = '2px solid #FFF';
						span2.style.float = 'left';
						span2.style.display = 'inline-block';
						this.Span[count].appendChild(span2);
						var arrow = document.createElement('img');
						arrow.src = arrowImage;
						arrow.style.float = 'right';
						arrow.style.padding = 0;
						arrow.style.margin = 0;
						arrow.onmousedown = function() {
							this.src = arrowImageDown
						};
						arrow.onmouseover = function() {
							this.src = arrowImageOver
						};
						arrow.onmouseout = function() {
							this.src = arrowImage
						};
						arrow.onmouseup = function() {
							this.src = arrowImage
						};
						span2.appendChild(arrow);
						this.Input[count].parentNode.insertBefore(this.Span[count], this.Input[count].nextSibling);
						this.spanClick(this.Span[count]);
						this.tryBackground(this.Span[count], this.Input[count]);
					}
				}

			}
		},
		spanClick: function(el) {
			var me = this;
			onEvent(el, 'click', function(b) {
				if (me.Span[me.count] && me.Span[me.count].style && me.Span[me.count].style.zIndex && me.Span[me.count].style.zIndex > 1) {
					if (me.count > - 1) me.Span[me.count].style.zIndex = 1;
					me.hidePicker();
				} else {
					if (me.count > - 1) me.Span[me.count].style.zIndex = 1;
					me.count = el.count;
					el.appendChild(me.pickerdiv).style.display = "block";
					me.Span[me.count].style.zIndex = 20000;
				}
				me.stopEvent(b);

			});
		},

		tryBackground: function(el, inp) {
			try {
				el.style.backgroundColor = inp.value;
			} catch(c) {}
		},

		makeColorGrid: function() {
			var rowcnt = 0;
			var chex = ["00", "33", "66", "99", "CC", "FF"];
			for (var b = 0; b < 6; b++) {
				for (var r = 0; r < 3; r++) {
					for (var g = 0; g < 6; g++) {
						var rgbcode = chex[r] + chex[g] + chex[b];
						this.colorgriddiv.appendChild(this.makeColorDiv(rgbcode));
					}
				}
				this.colorgriddiv.appendChild(this.makeClearDiv());
			}
			for (var b = 0; b < 6; b++) {
				for (var r = 3; r < 6; r++) {
					for (var g = 0; g < 6; g++) {
						var rgbcode = chex[r] + chex[g] + chex[b];
						this.colorgriddiv.appendChild(this.makeColorDiv(rgbcode));
					}
				}
				this.colorgriddiv.appendChild(this.makeClearDiv());
			}
			var space = document.createElement('div');
			space.className = 'space';
			this.colorgriddiv.appendChild(space);
			this.colorgriddiv.appendChild(this.makeClearDiv());
			var nhex = ["0", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "F"];
			for (var w = 0; w < 18; w++) {
				var rgbcode = '';
				for (var i = 0; i < 6; i++) {
					rgbcode += nhex[w];
				}
				this.colorgriddiv.appendChild(this.makeColorDiv(rgbcode));
			}
			this.colorgriddiv.appendChild(this.makeClearDiv());
		},

		makeClearDiv: function() {
			var cleardiv = document.createElement('div');
			cleardiv.className = 'clear';
			return cleardiv;
		},

		makeColorDiv: function(rgbcode) {
			var colordiv = document.createElement('div');
			colordiv.style.backgroundColor = '#' + rgbcode;
			return colordiv;
		},

		gridMouseOver: function(e) {
			var el;
			if (e.target) {
				el = e.target;
			} else {
				el = e.srcElement;
			}
			if (el.id != 'colorgrid') {
				var selcolor = rgb2hex(el.style.backgroundColor);
				picker.colorsamplediv.style.backgroundColor = selcolor;
				picker.colornamediv.innerHTML = selcolor.toUpperCase();
			}
			picker.stopEvent(e);
		},

		gridClick: function(e) {
			var el;
			if (e.target) {
				el = e.target;
			} else {
				el = e.srcElement;
			}
			if (el.id != 'colorgrid') {
				var selcolor = rgb2hex(el.style.backgroundColor);
				picker.Span[picker.count].style.backgroundColor = picker.Input[picker.count].value = selcolor;
				picker.Span[picker.count].style.zIndex = 1;
				picker.hidePicker();
				OSDCP.changePickerColor(el.style.backgroundColor, picker.count)

			}
			picker.stopEvent(e);
		},
		hidePicker: function() {
			this.pickerdiv.style.display = 'none';
		},

	};
	onEvent(window, "load", function() {
		if (!picker) picker = new Picker;
	});
	return {
		refresh: function() {
			for (var b = 0; b < picker.Input.length; b++) picker.tryBackground(picker.Span[b], picker.Input[b])
		},
		reload: function() {
			picker.pickerLoadInput()
		}
	}
} ();

