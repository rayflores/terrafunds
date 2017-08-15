jQuery(document).ready(function($){
			$( '.tf-color-picker' ).wpColorPicker();
			
			var calc  = $('.tf_admin_form').calx();
            var sheet = calc.calx('getSheet');
			
			
			
			// tax-table-2
			sheet.getCell('K28').setFormula($('.FK28').val());
			sheet.getCell('O28').setFormula($('.FO28').val());
			sheet.getCell('K29').setFormula($('.FK29').val());
			sheet.getCell('O29').setFormula($('.FO29').val());
			sheet.getCell('K30').setFormula($('.FK30').val());
			sheet.getCell('O30').setFormula($('.FO30').val());
			sheet.getCell('K31').setFormula($('.FK31').val());
			sheet.getCell('O31').setFormula($('.FO31').val());
			sheet.getCell('K32').setFormula($('.FK32').val());
			sheet.getCell('O32').setFormula($('.FO32').val());
			sheet.getCell('K33').setFormula($('.FK33').val());
			sheet.getCell('O33').setFormula($('.FO33').val());		
			sheet.getCell('K34').setFormula($('.FK34').val());
			sheet.getCell('O34').setFormula($('.FO34').val()); 
			
			// tax-table-3
			sheet.getCell('B41').setFormula($('.FQ73').val());
			sheet.getCell('B42').setFormula($('.FQ74').val());
			sheet.getCell('J41').setFormula($('.FJ41').val());
			sheet.getCell('J42').setFormula($('.FJ42').val());
			sheet.getCell('F43').setFormula($('.FF43').val());
			sheet.getCell('G43').setFormula($('.FG43').val());
			sheet.getCell('J43').setFormula($('.FJ43').val());
			sheet.getCell('C46').setFormula($('.FC46').val());
			sheet.getCell('D46').setFormula($('.FD46').val());
			sheet.getCell('E46').setFormula($('.FE46').val());
			sheet.getCell('C47').setFormula($('.FC47').val());
			sheet.getCell('D47').setFormula($('.FD47').val());
			sheet.getCell('E47').setFormula($('.FE47').val());
			sheet.getCell('C48').setFormula($('.FC48').val());
			sheet.getCell('D48').setFormula($('.FD48').val());
			sheet.getCell('E48').setFormula($('.FE48').val());
			sheet.getCell('C49').setFormula($('.FC49').val());
			sheet.getCell('D49').setFormula($('.FD49').val());
			sheet.getCell('E49').setFormula($('.FE49').val());
			sheet.getCell('C50').setFormula($('.FC50').val());
			sheet.getCell('D50').setFormula($('.FD50').val());
			sheet.getCell('E50').setFormula($('.FE50').val());
			sheet.getCell('C51').setFormula($('.FC51').val());
			sheet.getCell('D51').setFormula($('.FD51').val());
			sheet.getCell('E51').setFormula($('.FE51').val());
			sheet.getCell('C52').setFormula($('.FC52').val());
			sheet.getCell('D52').setFormula($('.FD52').val());
			sheet.getCell('E52').setFormula($('.FE52').val());
			sheet.getCell('B53').setFormula($('.FB53').val());			
			sheet.getCell('C53').setFormula($('.FC53').val());
			sheet.getCell('D53').setFormula($('.FD53').val());
			sheet.getCell('E53').setFormula($('.FE53').val());
			sheet.getCell('B54').setFormula($('.FB54').val());
			sheet.getCell('C54').setFormula($('.FC54').val());
			sheet.getCell('D54').setFormula($('.FD54').val());
			sheet.getCell('E54').setFormula($('.FE54').val());
			sheet.getCell('C55').setFormula($('.FC55').val());
			sheet.getCell('D55').setFormula($('.FD55').val());
			sheet.getCell('E55').setFormula($('.FE55').val());
			
			// tax-table-4
			sheet.getCell('G59').setFormula($('.FG59').val());
			sheet.getCell('H59').setFormula($('.FH59').val());
			sheet.getCell('G60').setFormula($('.FG60').val());
			sheet.getCell('H60').setFormula($('.FH60').val());
			sheet.getCell('I60').setFormula($('.FI60').val());
			sheet.getCell('G61').setFormula($('.FG61').val());
			sheet.getCell('H61').setFormula($('.FH61').val());
			sheet.getCell('G62').setFormula($('.FG62').val());
			sheet.getCell('H62').setFormula($('.FH62').val());
			sheet.getCell('G63').setFormula($('.FG63').val());
			sheet.getCell('H63').setFormula($('.FH63').val());
			sheet.getCell('G64').setFormula($('.FG64').val());
			sheet.getCell('H64').setFormula($('.FH64').val());
			sheet.getCell('B65').setFormula($('.FB65').val());
			sheet.getCell('G65').setFormula($('.FG65').val());
			sheet.getCell('H65').setFormula($('.FH65').val());
			sheet.getCell('B66').setFormula($('.FB66').val());
			sheet.getCell('G66').setFormula($('.FG66').val());
			sheet.getCell('H66').setFormula($('.FH66').val());
			sheet.getCell('G67').setFormula($('.FG67').val());
			sheet.getCell('H67').setFormula($('.FH67').val());
			sheet.getCell('M73').setFormula($('.FM73').val());
			sheet.getCell('Q73').setFormula($('.FQ73').val());
			sheet.getCell('S73').setFormula($('.FS73').val());
			sheet.getCell('V73').setFormula($('.FV73').val());
			sheet.getCell('X73').setFormula($('.FX73').val());
			sheet.getCell('Z73').setFormula($('.FZ73').val());
			sheet.getCell('AC73').setFormula($('.FAC73').val());
			sheet.getCell('AH73').setFormula($('.FAH73').val());
			sheet.getCell('M74').setFormula($('.FM74').val());
			sheet.getCell('O74').setFormula($('.FO74').val());
			sheet.getCell('Q74').setFormula($('.FQ74').val());
			sheet.getCell('S74').setFormula($('.FS74').val());
			sheet.getCell('V74').setFormula($('.FV74').val());
			sheet.getCell('X74').setFormula($('.FX74').val());
			sheet.getCell('Z74').setFormula($('.FZ74').val());
			sheet.getCell('AC74').setFormula($('.FAC74').val());
			sheet.getCell('AH74').setFormula($('.FAH74').val());
			sheet.getCell('M75').setFormula($('.FM75').val());
			sheet.getCell('O75').setFormula($('.FO75').val());
			sheet.getCell('Q75').setFormula($('.FQ75').val());
			sheet.getCell('S75').setFormula($('.FS75').val());
			sheet.getCell('V75').setFormula($('.FV75').val());
			sheet.getCell('X75').setFormula($('.FX75').val());
			sheet.getCell('Z75').setFormula($('.FZ75').val());
			sheet.getCell('AC75').setFormula($('.FAC75').val());
			sheet.getCell('AH75').setFormula($('.FAH75').val());
			sheet.getCell('M76').setFormula($('.FM76').val());
			sheet.getCell('O76').setFormula($('.FO76').val());
			sheet.getCell('Q76').setFormula($('.FQ76').val());
			sheet.getCell('S76').setFormula($('.FS76').val());
			sheet.getCell('V76').setFormula($('.FV76').val());
			sheet.getCell('X76').setFormula($('.FX76').val());
			sheet.getCell('Z76').setFormula($('.FZ76').val());
			sheet.getCell('AC76').setFormula($('.FAC76').val());
			sheet.getCell('AH76').setFormula($('.FAH76').val());
			sheet.getCell('S77').setFormula($('.FS77').val());
			sheet.getCell('V77').setFormula($('.FV77').val());
			sheet.getCell('X77').setFormula($('.FX77').val());
			sheet.getCell('Z77').setFormula($('.FZ77').val());
			sheet.getCell('AC77').setFormula($('.FAC77').val());
			sheet.getCell('AH77').setFormula($('.FAH77').val());
			
			sheet.getCell('M81').setFormula($('.FM81').val());
			sheet.getCell('T81').setFormula($('.FT81').val());
			sheet.getCell('AA81').setFormula($('.FAA81').val());
			sheet.getCell('AK81').setFormula($('.FAK81').val());
			sheet.getCell('M82').setFormula($('.FM82').val());
			sheet.getCell('T82').setFormula($('.FT82').val());
			sheet.getCell('AA82').setFormula($('.FAA82').val());
			sheet.getCell('AK82').setFormula($('.FAK82').val());
			sheet.getCell('M83').setFormula($('.FM83').val());
			sheet.getCell('T83').setFormula($('.FT83').val());
			sheet.getCell('AA83').setFormula($('.FAA83').val());
			sheet.getCell('AK83').setFormula($('.FAK83').val());
			sheet.getCell('M84').setFormula($('.FM84').val());
			sheet.getCell('T84').setFormula($('.FT84').val());
			sheet.getCell('AA84').setFormula($('.FAA84').val());
			sheet.getCell('AK84').setFormula($('.FAK84').val());
			sheet.getCell('M85').setFormula($('.FM85').val());
			sheet.getCell('T85').setFormula($('.FT85').val());
			sheet.getCell('AA85').setFormula($('.FAA85').val());
			sheet.getCell('AK85').setFormula($('.FAK85').val());

			sheet.getCell('T87').setFormula($('.FT87').val());
			sheet.getCell('AA87').setFormula($('.FAA87').val());
			sheet.getCell('AK87').setFormula($('.FAK87').val());
			sheet.getCell('T88').setFormula($('.FT88').val());
			sheet.getCell('AA88').setFormula($('.FAA88').val());
			sheet.getCell('AK88').setFormula($('.FAK88').val());
			sheet.getCell('T89').setFormula($('.FT89').val());
			sheet.getCell('AA89').setFormula($('.FAA89').val());
			sheet.getCell('AK89').setFormula($('.FAK89').val());
			sheet.getCell('AA91').setFormula($('.FAA91').val());
			sheet.getCell('AK91').setFormula($('.FAK91').val());
			sheet.getCell('AA92').setFormula($('.FAA92').val());
			sheet.getCell('AK92').setFormula($('.FAK92').val());
			sheet.getCell('AA93').setFormula($('.FAA93').val());
			sheet.getCell('AK93').setFormula($('.FAK93').val());
			sheet.getCell('AK95').setFormula($('.FAK95').val());
			sheet.getCell('AA95').setFormula($('.FAA95').val());
			sheet.getCell('AK96').setFormula($('.FAK96').val());
			sheet.getCell('AA96').setFormula($('.FAA96').val());
			sheet.getCell('AK97').setFormula($('.FAK97').val());
			sheet.getCell('AA97').setFormula($('.FAA97').val());
			
			sheet.getCell('M99').setFormula($('.FM99').val());
			sheet.getCell('T99').setFormula($('.FT99').val());
			sheet.getCell('AA99').setFormula($('.FAA99').val());
			sheet.getCell('AK99').setFormula($('.FAK99').val());
			sheet.getCell('M100').setFormula($('.FM100').val());
			sheet.getCell('T100').setFormula($('.FT100').val());
			sheet.getCell('AA100').setFormula($('.FAA100').val());
			sheet.getCell('AK100').setFormula($('.FAK100').val());
			sheet.getCell('M101').setFormula($('.FM101').val());
			sheet.getCell('T101').setFormula($('.FT101').val());
			sheet.getCell('AA101').setFormula($('.FAA101').val());
			sheet.getCell('AK101').setFormula($('.FAK101').val());
			
			$('.tf_admin_form').calx();
			
			$(document).on('change','.prov_sel, .donate_sel, .contrib_sel', function(e){
				$('.tf_admin_form').calx('calculate');
			});
			$(document).on('keyup','.d10, .d12, .d14, .d22', function(e){
				$('.tf_admin_form').calx('calculate');
			});
			
			/**!
 * wp-color-picker-alpha
 *
 * Overwrite Automattic Iris for enabled Alpha Channel in wpColorPicker
 * Only run in input and is defined data alpha in true
 *
 * Version: 1.2.2
 * https://github.com/23r9i0/wp-color-picker-alpha
 * Copyright (c) 2015 Sergio P.A. (23r9i0).
 * Licensed under the GPLv2 license.
 */
!function(t){var o="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAAHnlligAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAHJJREFUeNpi+P///4EDBxiAGMgCCCAGFB5AADGCRBgYDh48CCRZIJS9vT2QBAggFBkmBiSAogxFBiCAoHogAKIKAlBUYTELAiAmEtABEECk20G6BOmuIl0CIMBQ/IEMkO0myiSSraaaBhZcbkUOs0HuBwDplz5uFJ3Z4gAAAABJRU5ErkJggg==",i='<a tabindex="0" class="wp-color-result" />',e='<div class="wp-picker-holder" />',r='<div class="wp-picker-container" />',a='<input type="button" class="button button-small hidden" />';Color.fn.toString=function(){if(this._alpha<1)return this.toCSS("rgba",this._alpha).replace(/\s+/g,"");var t=parseInt(this._color,10).toString(16);return this.error?"":(t.length<6&&(t=("00000"+t).substr(-6)),"#"+t)},t.widget("wp.wpColorPicker",t.wp.wpColorPicker,{_create:function(){if(t.support.iris){var n=this,s=n.element;t.extend(n.options,s.data()),n.close=t.proxy(n.close,n),n.initialValue=s.val(),s.addClass("wp-color-picker").hide().wrap(r),n.wrap=s.parent(),n.toggler=t(i).insertBefore(s).css({backgroundColor:n.initialValue}).attr("title",wpColorPickerL10n.pick).attr("data-current",wpColorPickerL10n.current),n.pickerContainer=t(e).insertAfter(s),n.button=t(a),n.options.defaultColor?n.button.addClass("wp-picker-default").val(wpColorPickerL10n.defaultString):n.button.addClass("wp-picker-clear").val(wpColorPickerL10n.clear),s.wrap('<span class="wp-picker-input-wrap" />').after(n.button),s.iris({target:n.pickerContainer,hide:n.options.hide,width:n.options.width,mode:n.options.mode,palettes:n.options.palettes,change:function(i,e){n.options.alpha?(n.toggler.css({"background-image":"url("+o+")"}).html("<span />"),n.toggler.find("span").css({width:"100%",height:"100%",position:"absolute",top:0,left:0,"border-top-left-radius":"3px","border-bottom-left-radius":"3px",background:e.color.toString()})):n.toggler.css({backgroundColor:e.color.toString()}),t.isFunction(n.options.change)&&n.options.change.call(this,i,e)}}),s.val(n.initialValue),n._addListeners(),n.options.hide||n.toggler.click()}},_addListeners:function(){var o=this;o.wrap.on("click.wpcolorpicker",function(t){t.stopPropagation()}),o.toggler.on("click",function(){o.toggler.hasClass("wp-picker-open")?o.close():o.open()}),o.element.on("change",function(i){(""===t(this).val()||o.element.hasClass("iris-error"))&&(o.options.alpha?(o.toggler.removeAttr("style"),o.toggler.find("span").css("backgroundColor","")):o.toggler.css("backgroundColor",""),t.isFunction(o.options.clear)&&o.options.clear.call(this,i))}),o.toggler.on("keyup",function(t){13!==t.keyCode&&32!==t.keyCode||(t.preventDefault(),o.toggler.trigger("click").next().focus())}),o.button.on("click",function(i){t(this).hasClass("wp-picker-clear")?(o.element.val(""),o.options.alpha?(o.toggler.removeAttr("style"),o.toggler.find("span").css("backgroundColor","")):o.toggler.css("backgroundColor",""),t.isFunction(o.options.clear)&&o.options.clear.call(this,i)):t(this).hasClass("wp-picker-default")&&o.element.val(o.options.defaultColor).change()})}}),t.widget("a8c.iris",t.a8c.iris,{_create:function(){if(this._super(),this.options.alpha=this.element.data("alpha")||!1,this.element.is(":input")||(this.options.alpha=!1),"undefined"!=typeof this.options.alpha&&this.options.alpha){var o=this,i=o.element,e='<div class="iris-strip iris-slider iris-alpha-slider"><div class="iris-slider-offset iris-slider-offset-alpha"></div></div>',r=t(e).appendTo(o.picker.find(".iris-picker-inner")),a=r.find(".iris-slider-offset-alpha"),n={aContainer:r,aSlider:a};"undefined"!=typeof i.data("custom-width")?o.options.customWidth=parseInt(i.data("custom-width"))||0:o.options.customWidth=100,o.options.defaultWidth=i.width(),(o._color._alpha<1||-1!=o._color.toString().indexOf("rgb"))&&i.width(parseInt(o.options.defaultWidth+o.options.customWidth)),t.each(n,function(t,i){o.controls[t]=i}),o.controls.square.css({"margin-right":"0"});var s=o.picker.width()-o.controls.square.width()-20,l=s/6,c=s/2-l;t.each(["aContainer","strip"],function(t,i){o.controls[i].width(c).css({"margin-left":l+"px"})}),o._initControls(),o._change()}},_initControls:function(){if(this._super(),this.options.alpha){var t=this,o=t.controls;o.aSlider.slider({orientation:"vertical",min:0,max:100,step:1,value:parseInt(100*t._color._alpha),slide:function(o,i){t._color._alpha=parseFloat(i.value/100),t._change.apply(t,arguments)}})}},_change:function(){this._super();var t=this,i=t.element;if(this.options.alpha){var e=t.controls,r=parseInt(100*t._color._alpha),a=t._color.toRgb(),n=["rgb("+a.r+","+a.g+","+a.b+") 0%","rgba("+a.r+","+a.g+","+a.b+", 0) 100%"],s=t.options.defaultWidth,l=t.options.customWidth,c=t.picker.closest(".wp-picker-container").find(".wp-color-result");e.aContainer.css({background:"linear-gradient(to bottom, "+n.join(", ")+"), url("+o+")"}),c.hasClass("wp-picker-open")&&(e.aSlider.slider("value",r),t._color._alpha<1?(e.strip.attr("style",e.strip.attr("style").replace(/rgba\(([0-9]+,)(\s+)?([0-9]+,)(\s+)?([0-9]+)(,(\s+)?[0-9\.]+)\)/g,"rgb($1$3$5)")),i.width(parseInt(s+l))):i.width(s))}var p=i.data("reset-alpha")||!1;p&&t.picker.find(".iris-palette-container").on("click.palette",".iris-palette",function(){t._color._alpha=1,t.active="external",t._change()})},_addInputListeners:function(t){var o=this,i=100,e=function(i){var e=new Color(t.val()),r=t.val();t.removeClass("iris-error"),e.error?""!==r&&t.addClass("iris-error"):e.toString()!==o._color.toString()&&("keyup"===i.type&&r.match(/^[0-9a-fA-F]{3}$/)||o._setOption("color",e.toString()))};t.on("change",e).on("keyup",o._debounce(e,i)),o.options.hide&&t.on("focus",function(){o.show()})}})}(jQuery),jQuery(document).ready(function(t){t(".color-picker").wpColorPicker()});

});
