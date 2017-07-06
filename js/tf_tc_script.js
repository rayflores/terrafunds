jQuery(document).ready(function($){
			//var options =  tf_options;
			
			$('.tf-form').calx();
			
			$( "#tf-slider-2" ).slider({
			  range: "max",
			  min: 0,
			  max: 1000000,
			  step: 5000,
			  value: 160000,
			  slide: function( event, ui ) {
				$( ".d10" ).val( ui.value );
			  }
			});
			$( "#tf-slider-3" ).slider({
			  range: "max",
			  min: 0,
			  max: 1000000,
			  step: 1000,
			  value: 0,
			  slide: function( event, ui ) {
				$( ".d12" ).val( ui.value );
				
			  }
			});
			$( "#tf-slider-4" ).slider({
			  range: "max",
			  min: 0,
			  max: 1000000,
			  step: 1000,
			  value: 0,
			  slide: function( event, ui ) {
				$( ".d14" ).val( ui.value );
				
			  }
			});
			$( "#tf-slider-5" ).slider({
			  range: "max",
			  min: 0,
			  max: 100,
			  step: 5,
			  value: 75,
			  slide: function( event, ui ) {
				$( ".d22" ).val( ui.value );
				
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
			
			
			// let's use the entire sheet here
			var calc = $('.tf-form').calx();
			var sheet = calc.calx('getSheet');
			
			$(document).on('change','.prov_sel, .donate_sel, .contrib_sel', function(e){
				$('.tf-form').calx('calculate');
			});
			$(document).on('keyup','.d10, .d12, .d14, .d22', function(e){
				$('.tf-form').calx('calculate');
			});

			
});