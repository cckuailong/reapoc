/*
 evol.colorpicker 3.1.0
 ColorPicker widget for jQuery UI

 https://github.com/evoluteur/colorpicker
 (c) 2015 Olivier Giulieri

 * Depends:
 *	jquery.ui.core.js
 *	jquery.ui.widget.js
 */

(function( $, undefined ) {

var _idx=0,
	ua=window.navigator.userAgent,
	isIE=ua.indexOf("MSIE ")>0,
	_ie=isIE?'-ie':'',
	isMoz=isIE?false:/mozilla/.test(ua.toLowerCase()) && !/webkit/.test(ua.toLowerCase()),
	history=[],
	baseThemeColors=['ffffff','000000','eeece1','1f497d','4f81bd','c0504d','9bbb59','8064a2','4bacc6','f79646'],
	subThemeColors=['f2f2f2','7f7f7f','ddd9c3','c6d9f0','dbe5f1','f2dcdb','ebf1dd','e5e0ec','dbeef3','fdeada',
		'd8d8d8','595959','c4bd97','8db3e2','b8cce4','e5b9b7','d7e3bc','ccc1d9','b7dde8','fbd5b5',
		'bfbfbf','3f3f3f','938953','548dd4','95b3d7','d99694','c3d69b','b2a2c7','92cddc','fac08f',
		'a5a5a5','262626','494429','17365d','366092','953734','76923c','5f497a','31859b','e36c09',
		'7f7f7f','0c0c0c','1d1b10','0f243e','244061','632423','4f6128','3f3151','205867','974806'],
	standardColors=['c00000','ff0000','ffc000','ffff00','92d050','00b050','00b0f0','0070c0','002060','7030a0'],
	webColors=[
		['003366','336699','3366cc','003399','000099','0000cc','000066'],
		['006666','006699','0099cc','0066cc','0033cc','0000ff','3333ff','333399'],
		['669999','009999','33cccc','00ccff','0099ff','0066ff','3366ff','3333cc','666699'],
		['339966','00cc99','00ffcc','00ffff','33ccff','3399ff','6699ff','6666ff','6600ff','6600cc'],
		['339933','00cc66','00ff99','66ffcc','66ffff','66ccff','99ccff','9999ff','9966ff','9933ff','9900ff'],
		['006600','00cc00','00ff00','66ff99','99ffcc','ccffff','ccccff','cc99ff','cc66ff','cc33ff','cc00ff','9900cc'],
		['003300','009933','33cc33','66ff66','99ff99','ccffcc','ffffff','ffccff','ff99ff','ff66ff','ff00ff','cc00cc','660066'],
		['333300','009900','66ff33','99ff66','ccff99','ffffcc','ffcccc','ff99cc','ff66cc','ff33cc','cc0099','993399'],
		['336600','669900','99ff33','ccff66','ffff99','ffcc99','ff9999','ff6699','ff3399','cc3399','990099'],
		['666633','99cc00','ccff33','ffff66','ffcc66','ff9966','ff6666','ff0066','d60094','993366'],
		['a58800','cccc00','ffff00','ffcc00','ff9933','ff6600','ff0033','cc0066','660033'],
		['996633','cc9900','ff9900','cc6600','ff3300','ff0000','cc0000','990033'],
		['663300','996600','cc3300','993300','990000','800000','993333']
	],
	transColor='#0000ffff',
	int2Hex=function(i){
		var h=i.toString(16);
		if(h.length==1){
			h='0'+h;
		}
		return h;
	},
	st2Hex=function(s){
		return int2Hex(Number(s));
	},
	int2Hex3=function(i){
		var h=int2Hex(i);
		return h+h+h;
	},
	toHex3=function(c){
		if(c.length>10){ // IE9
			var p1=1+c.indexOf('('),
				p2=c.indexOf(')'),
				cs=c.substring(p1,p2).split(',');
			return ['#',st2Hex(cs[0]),st2Hex(cs[1]),st2Hex(cs[2])].join('');
		}else{
			return c;
		}
	};

$.widget( "evol.ecolorpicker", {

	version: '3.1.0',
	
	options: {
		color: null, // example:'#31859B'
		showOn: 'both', // possible values: 'focus','button','both'
		displayIndicator: false,
		transparentColor: false,
                displayPointer: true,
		history: true,
		defaultPalette: 'theme', // possible values: 'theme', 'web'
		strings: 'Theme Colors,Standard Colors,Web Colors,Theme Colors,Back to Palette,History,No history yet.'
	},

	_create: function() {
		var that=this;
		this._paletteIdx=this.options.defaultPalette=='theme'?1:2;
		this._id='evo-cp'+_idx++;
		this._enabled=true;
		switch(this.element.get(0).tagName){
			case 'INPUT':
				var color=this.options.color,
					e=this.element,
					css=((this.options.showOn==='focus')?'':'evo-pointer ')+'evo-colorind'+(isMoz?'-ff':_ie),
					style='';
				this._isPopup=true;
				this._palette=null;
				if(color!==null){
					e.val(color);
				}else{
					var v=e.val();
					if(v!==''){
						color=this.options.color=v;
					}
				}
				if(color===transColor){
					css+=' evo-transparent';
				}else{
					style=(color!==null)?('background-color:'+color):'';
				}
				e.addClass('colourPicker '+this._id)
					.wrap('<div style="width:'+(this.element.width()+32)+'px;'+
						(isIE?'margin-bottom:-21px;':'')+
						(isMoz?'padding:1px 0;':'')+
						'"></div>')
					.after('<div class="'+css+'" style="'+style+'"></div>')
					.on('keyup onpaste', function(evt){
						var c=$(this).val();
						if(c!=that.options.color){
							that._setValue(c, true);
						}
					});
				var showOn=this.options.showOn;
				if(showOn==='both' || showOn==='focus'){
					e.on('focus', function(){
						that.showPalette();
					});
				}
				if(showOn==='both' || showOn==='button'){
					e.next().on('click', function(evt){
						evt.stopPropagation();
						that.showPalette();
					});
				}
				break;
			default:
				this._isPopup=false;
				this._palette=this.element.html(this._paletteHTML())
					.attr('aria-haspopup','true');
				this._bindColors();
		}
		if(color && this.options.history){
			this._add2History(color);
		}
	},

	_paletteHTML: function() {
		var pIdx=this._paletteIdx=Math.abs(this._paletteIdx),
			opts=this.options,
			labels=opts.strings.split(',');

		var h='<div class="evo-pop'+_ie+' ui-widget ui-widget-content ui-corner-all"'+
			(this._isPopup?' style="position:absolute"':'')+'>'+
			// palette
			'<span>'+this['_paletteHTML'+pIdx]()+'</span>'+
			// links
			'<div class="evo-more"><a href="javascript:void(0)">'+labels[1+pIdx]+'</a>';
		if(opts.history){
			h+='<a href="javascript:void(0)" class="evo-hist">'+labels[5]+'</a>';
		}
		h+='</div>';
		// indicator
		if(opts.displayIndicator){
			h+=this._colorIndHTML(this.options.color)+this._colorIndHTML('');
		}
		h+='</div>';
		return h;
	},

	_colorIndHTML: function(c) {
		var css=isIE?'evo-colorbox-ie ':'',
			style='';

		if(c){
			if(c===transColor){
				css+='evo-transparent';
			}else{
				style='background-color:'+c;
			}
		}else{
			style='display:none';
		}
		return '<div class="evo-color" style="float:left">'+
			'<div style="'+style+'" class="'+css+'"></div><span>'+ // class="evo-colortxt-ie"
			(c?c:'')+'</span></div>';
	},

	_paletteHTML1: function() {
		var opts=this.options,
			labels=opts.strings.split(','),
			oTD='<td style="background-color:#',
			cTD=isIE?'"><div style="width:2px;"></div></td>':'"><span/></td>',
			oTRTH='<tr><th colspan="10" class="ui-widget-content">';

		// base theme colors
		var h='<table class="evo-palette'+_ie+'">'+oTRTH+labels[0]+'</th></tr><tr>';
		for(var i=0;i<10;i++){ 
			h+=oTD+baseThemeColors[i]+cTD;
		}
		h+='</tr>';
		if(!isIE){
			h+='<tr><th colspan="10"></th></tr>';
		}
		h+='<tr class="top">';
		// theme colors
		for(i=0;i<10;i++){ 
			h+=oTD+subThemeColors[i]+cTD;
		}
		for(var r=1;r<4;r++){
			h+='</tr><tr class="in">';
			for(i=0;i<10;i++){ 
				h+=oTD+subThemeColors[r*10+i]+cTD;
			}
		}
		h+='</tr><tr class="bottom">';
		for(i=40;i<50;i++){ 
			h+=oTD+subThemeColors[i]+cTD;
		}
		h+='</tr>'+oTRTH;
		// transparent color
		if(opts.transparentColor){
			h+='<div class="evo-transparent evo-tr-box"></div>';
		}
		h+=labels[1]+'</th></tr><tr>';
		// standard colors
		for(i=0;i<10;i++){ 
			h+=oTD+standardColors[i]+cTD;
		}
		h+='</tr></table>';
		return h; 
	},

	_paletteHTML2: function() {
		var i, iMax,
			oTD='<td style="background-color:#',
			cTD=isIE?'"><div style="width:5px;"></div></td>':'"><span/></td>',
			oTableTR='<table class="evo-palette2'+_ie+'"><tr>',
			cTableTR='</tr></table>';

		var h='<div class="evo-palcenter">';
		// hexagon colors
		for(var r=0,rMax=webColors.length;r<rMax;r++){
			h+=oTableTR;
			var cs=webColors[r];
			for(i=0,iMax=cs.length;i<iMax;i++){ 
				h+=oTD+cs[i]+cTD;
			}
			h+=cTableTR;
		}
		h+='<div class="evo-sep"/>';
		// gray scale colors
		var h2='';
		h+=oTableTR;
		for(i=255;i>10;i-=10){
			h+=oTD+int2Hex3(i)+cTD;
			i-=10;
			h2+=oTD+int2Hex3(i)+cTD;
		}
		h+=cTableTR+oTableTR+h2+cTableTR+'</div>';
		return h;
	},

	_switchPalette: function(link) {
		if(this._enabled){
			var idx, 
				content, 
				label,
				labels=this.options.strings.split(',');
			if($(link).hasClass('evo-hist')){
				// history
				var h=['<table class="evo-palette"><tr><th class="ui-widget-content">',
					labels[5], '</th></tr></tr></table>',
					'<div class="evo-cHist">'];
				if(history.length===0){
					h.push('<p>&nbsp;',labels[6],'</p>');
				}else{
					for(var i=history.length-1;i>-1;i--){
						if(history[i].length===9){
							h.push('<div class="evo-transparent"></div>');
						}else{
							h.push('<div style="background-color:',history[i],'"></div>');
						}
					}
				}
				h.push('</div>');
				idx=-this._paletteIdx;
				content=h.join('');
				label=labels[4];
			}else{
				// palette
				if(this._paletteIdx<0){
					idx=-this._paletteIdx;
					this._palette.find('.evo-hist').show();
				}else{
					idx=(this._paletteIdx==2)?1:2;
				}
				content=this['_paletteHTML'+idx]();
				label=labels[idx+1];
				this._paletteIdx=idx;
			}
			this._paletteIdx=idx;
			var e=this._palette.find('.evo-more')
				.prev().html(content).end()
				.children().eq(0).html(label);
			if(idx<0){
				e.next().hide();
			}
		}
	},

	showPalette: function() {
		if(this._enabled){
			$('.colourPicker').not('.'+this._id).ecolorpicker('hidePalette');
			if(this._palette===null){
				this._palette=this.element.next()
					.after(this._paletteHTML()).next()
					.on('click',function(evt){
						evt.stopPropagation();
					});
				this._bindColors();
				var that=this;
				$(document.body).on('click.'+this._id,function(evt){
					if(evt.target!=that.element.get(0)){
						that.hidePalette();
					}
				});
			}
		}
		return this;
	},

	hidePalette: function() {
		if(this._isPopup && this._palette){
			$(document.body).off('click.'+this._id);
			var that=this;
			this._palette.off('mouseover click', 'td,.evo-transparent')
				.fadeOut(function(){
					that._palette.remove();
					that._palette=that._cTxt=null;
				})
				.find('.evo-more a').off('click');
		}
		return this;
	},

	_bindColors: function() {
		var that=this,
			opts=this.options,
			es=this._palette.find('div.evo-color'),
			sel=opts.history?'td,.evo-cHist>div':'td';

		if(opts.transparentColor){
			sel+=',.evo-transparent';
		}
		this._cTxt1=es.eq(0).children().eq(0);
		this._cTxt2=es.eq(1).children().eq(0);
		this._palette
			.on('click', sel, function(evt){
				if(that._enabled){
					var $this=$(this);
					that._setValue($this.hasClass('evo-transparent')?transColor:toHex3($this.attr('style').substring(17)));
				}
			})
			.on('mouseover', sel, function(evt){
				if(that._enabled){
					var $this=$(this),
						c=$this.hasClass('evo-transparent')?transColor:toHex3($this.attr('style').substring(17));
					if(that.options.displayIndicator){
						that._setColorInd(c,2);
					}
					that.element.trigger('mouseover.color', c);
				}
			})
			.find('.evo-more a').on('click', function(){
				that._switchPalette(this);
			});
	},

	val: function(value) {
		if (typeof value=='undefined') {
			return this.options.color;
		}else{
			this._setValue(value);
			return this;
		}
	},

	_setValue: function(c, noHide) {
		c = c.replace(/ /g,'');
		this.options.color=c;
		if(this._isPopup){
			if(!noHide){
				this.hidePalette();
			}
			this._setBoxColor(this.element.val(c).next(), c);
		}else{
			this._setColorInd(c,1);
		}
		if(this.options.history && this._paletteIdx>0){
			this._add2History(c);
		}
		this.element.trigger('change.color', c);
	},

	_setColorInd: function(c, idx) {
		var $box=this['_cTxt'+idx];
		this._setBoxColor($box, c);
		$box.next().html(c);
	},

	_setBoxColor: function($box, c) {
		if(c===transColor){
			$box.addClass('evo-transparent')
				.removeAttr('style');
		}else{
			$box.removeClass('evo-transparent')
				.attr('style','background-color:'+c);
		}
	},

	_setOption: function(key, value) {
		if(key=='color'){
			this._setValue(value, true);
		}else{
			this.options[key]=value;
		}
	},

	_add2History: function(c) {
		var iMax=history.length;
		// skip color if already in history
		for(var i=0;i<iMax;i++){
			if(c==history[i]){
				return;
			}
		}
		// limit of 28 colors in history
		if(iMax>27){
			history.shift();
		}
		// add to history
		history.push(c);
	},

	enable: function() {
		var e=this.element;
		if(this._isPopup){
			e.removeAttr('disabled');
		}else{
			e.css({
				'opacity': '1', 
				'pointer-events': 'auto'
			});
		}
		if(this.options.showOn!=='focus'){
			this.element.next().addClass('evo-pointer');
		}
		e.removeAttr('aria-disabled');
		this._enabled=true;
		return this;
	},

	disable: function() {
		var e=this.element;
		if(this._isPopup){
			e.attr('disabled', 'disabled');
		}else{
			this.hidePalette();
			e.css({
				'opacity': '0.3', 
				'pointer-events': 'none'
			});
		}
		if(this.options.showOn!=='focus'){
			this.element.next().removeClass('evo-pointer');
		}
		e.attr('aria-disabled','true');
		this._enabled=false;
		return this;
	},

	isDisabled: function() {
		return !this._enabled;
	},

	destroy: function() {
		$(document.body).off('click.'+this._id);
		if(this._palette){
			this._palette.off('mouseover click', 'td,.evo-cHist>div,.evo-transparent')
				.find('.evo-more a').off('click');
			if(this._isPopup){
				this._palette.remove();
			}
			this._palette=this._cTxt=null;
		}
		if(this._isPopup){
			this.element
				.next().off('click').remove()
				.end().off('focus').unwrap();
		}
		this.element.removeClass('colourPicker '+this.id).empty();
		$.Widget.prototype.destroy.call(this);
	}

});

})(jQuery);
