jQuery(document).ready(function($){
			
	
			$('.show-summary').click(function() {
				$('tr td:nth-child(4)').toggleClass('bg');
				$('td.lPad').toggleClass('bg');
				$('.tax-content').toggle('slow');
				$('.rotate2').toggleClass('down');
			});
			$('#show-adv').click(function(){ 
				$('.adv-content').toggle('slow');
				$('.rotate').toggleClass('down');
			});
		
			//var options =  tf_options;
			
			$('.tf-form').calx();
			
			$( "#tf-slider-1" ).slider({
			  range: "max",
			  min: 1,
			  max: 7,
			  step: 1,
			  value: 5,
			  labels: ['BC','AB','SK','MB','ON','QC','NS'],
			  slide: function( event, ui ) {
				  if (ui.value == 1){
					  $( ".prov_sel" ).val( 'BC' );
				  }if (ui.value == 2){
					  $( ".prov_sel" ).val( 'AB' );
				  }if (ui.value == 3){
					  $( ".prov_sel" ).val( 'SK' );
				  }if (ui.value == 4){
					  $( ".prov_sel" ).val( 'MB' );
				  }if (ui.value == 5){
					  $( ".prov_sel" ).val( 'ON' );
				  }if (ui.value == 6){
					  $( ".prov_sel" ).val( 'QC' );
				  }if (ui.value == 7){
					  $( ".prov_sel" ).val( 'NS' );
				  }
			  }
			});
			var initialValue2 = 160000;

			var sliderTooltip2 = function(event, ui) {
				var curValue2 = ui.value || initialValue2;
				var tooltip2 = '<div class="tooltip"><div class="tooltip-inner">$' + curValue2.toFixed().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</div></div>';

				$('#tf-slider-2 .ui-slider-handle').html(tooltip2);
			}
			$( "#tf-slider-2" ).slider({
			  range: "max",
			  min: 0,
			  max: 1000000,
			  step: 5000,
			  value: initialValue2,
			  create: sliderTooltip2,
			  slide: function( event, ui ) {
				$( ".d10" ).val( ui.value );
				var curValue2 = ui.value;
				var tooltip2 = '<div class="tooltip"><div class="tooltip-inner">$' + curValue2.toFixed().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</div></div>';

				$('#tf-slider-2 .ui-slider-handle').html(tooltip2);
				
			  }
			});
			var initialValue3 = 0;

			var sliderTooltip3 = function(event, ui) {
				var curValue3 = ui.value || initialValue3;
				var tooltip3 = '<div class="tooltip"><div class="tooltip-inner">$' + curValue3.toFixed().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</div></div>';

				$('#tf-slider-3 .ui-slider-handle').html(tooltip3);
			}
			$( "#tf-slider-3" ).slider({
			  range: "max",
			  min: 0,
			  max: 1000000,
			  step: 1000,
			  value: initialValue3,
			  create: sliderTooltip3,
			  slide: function( event, ui ) {
				$( ".d12" ).val( ui.value );
				var curValue3 = ui.value;
				var tooltip3 = '<div class="tooltip"><div class="tooltip-inner">$' + curValue3.toFixed().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</div></div>';

				$('#tf-slider-3 .ui-slider-handle').html(tooltip3);
				
			  }
			});
			var initialValue4 = 0;

			var sliderTooltip4 = function(event, ui) {
				var curValue4 = ui.value || initialValue4;
				var tooltip4 = '<div class="tooltip"><div class="tooltip-inner">$' + curValue4.toFixed().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</div></div>';

				$('#tf-slider-4 .ui-slider-handle').html(tooltip4);
			}
			$( "#tf-slider-4" ).slider({
			  range: "max",
			  min: 0,
			  max: 1000000,
			  step: 1000,
			  value: initialValue4,
			  create: sliderTooltip4,
			  slide: function( event, ui ) {
				$( ".d14" ).val( ui.value );
				var curValue4 = ui.value;
				var tooltip4 = '<div class="tooltip"><div class="tooltip-inner">$' + curValue4.toFixed().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</div></div>';

				$('#tf-slider-4 .ui-slider-handle').html(tooltip4);
			  }
			});
			var initialValue5 = 75;

			var sliderTooltip5 = function(event, ui) {
				var curValue5 = ui.value || initialValue5;
				var tooltip5 = '<div class="tooltip"><div class="tooltip-inner">' + curValue5 + '%</div></div>';

				$('#tf-slider-5 .ui-slider-handle').html(tooltip5);
			}
			$( "#tf-slider-5" ).slider({
			  range: "max",
			  min: 0,
			  max: 100,
			  step: 1,
			  value: 75,
			  create: sliderTooltip5,
			  slide: function( event, ui ) {
				$( ".d22" ).val( ui.value );
				var curValue5 = ui.value;
				var tooltip5 = '<div class="tooltip"><div class="tooltip-inner">' + curValue5 + '%</div></div>';

				$('#tf-slider-5 .ui-slider-handle').html(tooltip5);
				}
			});
			// B17
			var initialValue6 = 10;

			var sliderTooltip6 = function(event, ui) {
				var curValue6 = ui.value || initialValue6;
				var tooltip6 = '<div class="tooltip"><div class="tooltip-inner">' + curValue6 + '%</div></div>';

				$('#tf-slider-6 .ui-slider-handle').html(tooltip6);
			}
			$( "#tf-slider-6" ).slider({
			  range: "max",
			  min: 10,
			  max: 25,
			  step: 1,
			  value: 10,
			  create: sliderTooltip6,
			  slide: function( event, ui ) {
				$( ".b17" ).val( ui.value );
				var curValue6 = ui.value;
				var tooltip6 = '<div class="tooltip"><div class="tooltip-inner">' + curValue6 + '%</div></div>';

				$('#tf-slider-6 .ui-slider-handle').html(tooltip6);
				}
			});
			// D10
			$('.d10').on('change', function() {
				var d10 = $(this).val();
				$( ".tf-form" ).calx( 'setValue', 'D10', d10 );
				$( '#tf-slider-2').slider('value', d10 );
			});
			$('#tf-slider-2').on('slidechange', function( event, ui) {
				//console.log(ui.value);
				$( ".tf-form" ).calx( 'setValue', 'D10', ui.value );
			});
			
			// D12
			$('.d12').on('change', function() {
				var d12 = $(this).val();
				$( ".tf-form" ).calx( 'setValue', 'D12', d12 );
				$( '#tf-slider-3').slider('value', d12 );
			});
			$('#tf-slider-3').on('slidechange', function( event, ui) {
				//console.log(ui.value);
				$( ".tf-form" ).calx( 'setValue', 'D12', ui.value );
			});
			
			// D14
			$('.d14').on('change', function() {
				var d14 = $(this).val();
				$( ".tf-form" ).calx( 'setValue', 'D14', d14 );
				$( '#tf-slider-4').slider('value', d14 );
			});
			$('#tf-slider-4').on('slidechange', function( event, ui) {
				//console.log(ui.value);
				$( ".tf-form" ).calx( 'setValue', 'D14', ui.value );
			});
			// D18
			$('input[name=tf_tc_options_contribute_selection]').on('change',function() {
				var d18 = $( 'input[name=tf_tc_options_contribute_selection]:checked' ).val();
				//console.log(d18);
				$(".tf-form").calx( 'setValue', 'D18', d18 );
			});
			// D20
			$('input[name=tf_tc_options_capital_losses]').on('change',function() {
				var d20 = $( 'input[name=tf_tc_options_capital_losses]:checked' ).val();
				//console.log(d18);
				$(".tf-form").calx( 'setValue', 'D20', d20 );
			});
			// D22
			$('.d22').on('change', function() {
				var d22 = $(this).val();
				$( ".tf-form" ).calx( 'setValue', 'D22', d22 );
				$( '#tf-slider-5').slider('value', d22 );
			});
			$('#tf-slider-5').on('slidechange', function( event, ui) {
				//console.log(ui.value);
				$( ".tf-form" ).calx( 'setValue', 'D22', ui.value );
			});
			$('#tf-slider-6').on('slidechange', function( event, ui) {
				//console.log(ui.value);
				$( ".tf-form" ).calx( 'setValue', 'B17', ui.value );
			});
			$('#tf-slider-1').on('slidechange', function ( event, ui ){
				var d8 = 'ON';
				if (ui.value == 1){
					  d8 = 'BC';
				  }
				if (ui.value == 2){
					  d8 = 'AB';
				  }
				if (ui.value == 3){
					  d8 = 'SK';
				  }
				if (ui.value == 4){
					  d8 = 'MB';
				  }
				if (ui.value == 5){
					  d8 = 'ON';
				  }
				if (ui.value == 6){
					  d8 = 'QC';
				  }
				if (ui.value == 7){
					  d8 = 'NS';
				  }
				$('.tf-form').calx( 'setValue', 'D8', d8 )
			});
			
			
			// let's use the entire sheet here
			var calc = $('.tf-form').calx();
			var sheet = calc.calx('getSheet');
			
			$(document).on('change','.prov_sel, .donate_sel, .contrib_sel', function(e){
				$('.tf-form').calx('calculate');
			});
			$(document).on('keyup','.d10, .d12, .d14, .d22', function(e){
				$('.tf-form').calx('calculate');
			});

			$('.tf-form').calx('calculate');
			
	/* Mobile Detection */
	var isMobile = {
		Android: function() {
			return navigator.userAgent.match(/Android/i);
		},
		BlackBerry: function() {
			return navigator.userAgent.match(/BlackBerry/i);
		},
		iOS: function() {
			return navigator.userAgent.match(/iPhone|iPad|iPod/i);
		},
		Opera: function() {
			return navigator.userAgent.match(/Opera Mini/i);
		},
		Windows: function() {
			return navigator.userAgent.match(/IEMobile/i);
		},
		any: function() {
			return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
		}
	};
	// use if(isMobile.any()) { it is mobile }			
	/* end Mobile Detection */	

	if ( isMobile.any() ){
		//simple
		$('table.tf-tc-results-simple').insertAfter($('canvas#tfChartOne'));
		// main
		$('canvas#tfChartThree').css('width','300');
		$('canvas#tfChartThree').css('height','300');
	}
		
});