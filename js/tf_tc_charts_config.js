jQuery(document).ready(function($){
	var cash_bg = tf_options.cash_outlay_bg;
	var tax_bg = tf_options.tax_savings_bg;
	var title_font = tf_options.title_font;
	var title_color = tf_options.title_color;
	var title_style = tf_options.title_style;
	var title_size = parseInt(tf_options.title_size);
	var tooltip_bg = tf_options.tooltip_bg;
	
	Chart.Tooltip.positioners.outer = function(elements) {
				if (!elements.length) {
					return false;
				}

				var i, len;
				var x = 0;
				var y = 0;

				for (i = 0, len = elements.length; i < len; ++i) {
					var el = elements[i];
					if (el && el.hasValue()) {
						var elPosX = el._view.x+0.90*el._view.outerRadius*Math.cos((el._view.endAngle-el._view.startAngle)/3+el._view.startAngle);
						var elPosY = el._view.y+0.60*el._view.outerRadius*Math.sin((el._view.endAngle-el._view.startAngle)/3+el._view.startAngle);
						if (x < elPosX) {
							x = elPosX;
						}
						if (y < elPosY) {
							y = elPosY;
						}
					}
				}

				return {
					x: Math.round(x),
					y: Math.round(y)
				};
			},

			Chart.plugins.register({
				  beforeRender: function (chart) {
					if (chart.config.options.showAllTooltips) {
						// create an array of tooltips
						// we can't use the chart tooltip because there is only one tooltip per chart
						chart.pluginTooltips = [];
						chart.config.data.datasets.forEach(function (dataset, i) {
							chart.getDatasetMeta(i).data.forEach(function (sector, j) {
									chart.pluginTooltips.push(
											new Chart.Tooltip({
										_chart: chart.chart,
										_chartInstance: chart,
										_data: chart.data,
										_options: chart.options.tooltips,
										_active: [sector]
									}, chart)
									);
							});
						});

						// turn off normal tooltips
						chart.options.tooltips.enabled = false;
					}
				},
				  afterDraw: function (chart, easing) {
					if (chart.config.options.showAllTooltips) {
						// we don't want the permanent tooltips to animate, so don't do anything till the animation runs atleast once
						if (!chart.allTooltipsOnce) {
							if (easing !== 1)
								return;
							chart.allTooltipsOnce = true;
						}

						// turn on tooltips
						chart.options.tooltips.enabled = true;
						Chart.helpers.each(chart.pluginTooltips, function (tooltip) {
							tooltip.initialize();
							tooltip._options.position = "outer";
							tooltip._options.displayColors = false;
							tooltip._options.backgroundColor = tooltip_bg;
							tooltip._options.bodyFontSize = tooltip._chart.height*0.039;
							tooltip._options.yPadding = tooltip._options.bodyFontSize*0.30;
							tooltip._options.xPadding = tooltip._options.bodyFontSize*0.30;
							tooltip._options.caretSize = tooltip._options.bodyFontSize*0.5;
							tooltip._options.cornerRadius = tooltip._options.bodyFontSize*0.50;
							
							tooltip.update();
							
							// we don't actually need this since we are not animating tooltips
							tooltip.pivot();
							tooltip.transition(easing).draw();
						});
						chart.options.tooltips.enabled = false;
					}
				  }
				});
// FIRST CHART
			var text = $('.C55').text();
			var taxsavetext = text.replace('$','');
			var tax_savetext = taxsavetext.replace(',','');
			var tax_savings = parseFloat(tax_savetext);
			if (tax_savings > 0 ){
				var cash_outlay = 1000 - tax_savings;
				var ctxone = document.getElementById("tfChartOne").getContext("2d");
				var tfChartOne = new Chart(ctxone, {
					type:"pie",
					data:{
						labels:
							["Tax Savings","Cash Outlay"],
						datasets:[{
							data:[tax_savings,cash_outlay],
							backgroundColor:[tax_bg,cash_bg]
							}
						]
					},
					options:{
						title: {
							display: true,
							fontFamily: "'"+title_font+"'",
							fontColor: title_color,
							fontStyle: title_style,
							padding: 10,
							fontSize: title_size,
							text: '$1,000 Investment'
						},
						responsive: false,
						showAllTooltips: true,
						legend:{
							display: false,
						},
						animation: {
							animateScale: true,
							animateRotate: true
						},
						tooltips:{
							enabled: false,
							callbacks:{
								 label: function (tooltipItem, data){
									 var num = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
									return data.labels[tooltipItem.index]+": $"+ num.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
								}
							}
						}
					}
					
				});
				
			}
			function addData(){
				var text = $('.C55').text();
				var taxsavetext = text.replace('$','');
				var tax_savetext = taxsavetext.replace(',','');
				var tax_savings = parseFloat(tax_savetext);
				if (tax_savings > 0 && tax_savings < 1000 ){
					tfChartOne.data.datasets[0].data[0] = tax_savings;
					tfChartOne.data.datasets[0].data[1] = 1000 - tax_savings;
					tfChartOne.options.showAllTooltips = true;
					tfChartOne.options.title.display = true;
				} else if ( tax_savings > 0 && tax_savings > 1000 ){
					tfChartOne.data.datasets[0].data[0] = tax_savings;
					tfChartOne.data.datasets[0].data[1] = 0;
					tfChartOne.options.showAllTooltips = true;
					tfChartOne.options.title.display = true;
				}
				
				else {
					tfChartOne.data.datasets[0].data[0] = 0;
					tfChartOne.data.datasets[0].data[1] = 0;
					tfChartOne.options.showAllTooltips = false;
					tfChartOne.options.title.display = false;
				}	
				tfChartOne.update();
			}
			$(document).on('change','.d10, .d12, .d14, .d22', function(e){
				addData();
			});
			$(document).on('change','.prov_sel, .d18, .d20', function(e){
				addData();
			});
			$( "#tf-slider-2, #tf-slider-3, #tf-slider-4, #tf-slider-5" ).on('slidechange', function( event, ui ){
				addData();
			});
		

// END FIRST CHART 

// SECOND CHART 
			var d55text = $('.D55').text();
			var taxsavetext2 = d55text.replace('$','');
			var taxsavetext_2 = taxsavetext2.replace(',','');
			var tax_savings2 = parseFloat(taxsavetext_2);
			if (tax_savings2 > 0 ){
				var cash_outlay2 = 5000 - tax_savings2;
				var ctxtwo = document.getElementById("tfChartTwo");
				var tfChartTwo = new Chart(ctxtwo, {
					type:"pie",
					data:{
						labels:
							["Tax Savings","Cash Outlay"],
						datasets:[{
							data:[tax_savings2,cash_outlay2],
							backgroundColor:[tax_bg,cash_bg]
							}
						]
					},
					options:{
						title: {
							display: true,
							fontFamily: "'"+title_font+"'",
							fontColor: title_color,
							fontStyle: title_style,
							padding: 10,
							fontSize: title_size,
							text: '$5,000 Investment',
						},
						responsive: false,
						showAllTooltips: true,
						legend:{
							display: false,
						},
						animation: {
							animateScale: true,
							animateRotate: true
						},
						tooltips:{
							enabled: false,
							callbacks:{
								 label: function (tooltipItem, data){
									 var num = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
									return data.labels[tooltipItem.index]+": $"+num.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
								}
							}
						}
					}
					
				});
				
			}
			function addData2(){
				var d55text = $('.D55').text();
				var taxsavetext2 = d55text.replace('$','');
				var tax_savetext2 = taxsavetext2.replace(',','');
				var tax_savings2 = parseFloat(tax_savetext2);
				if (tax_savings2 > 0 && tax_savings2 < 5000 ){
					tfChartTwo.data.datasets[0].data[0] = tax_savings2;
					tfChartTwo.data.datasets[0].data[1] = 5000 - tax_savings2;
					tfChartTwo.options.showAllTooltips = true;
					tfChartTwo.options.title.display = true;
				} else if ( tax_savings2 > 0 && tax_savings2 > 5000 ){
					tfChartTwo.data.datasets[0].data[0] = tax_savings2;
					tfChartTwo.data.datasets[0].data[1] = 0;
					tfChartTwo.options.showAllTooltips = true;
					tfChartTwo.options.title.display = true;
				} else {
					tfChartTwo.data.datasets[0].data[0] = 0;
					tfChartTwo.data.datasets[0].data[1] = 0;
					tfChartTwo.options.showAllTooltips = false;
					tfChartTwo.options.title.display = false;
				}	
				tfChartTwo.update();
			}
			$(document).on('change','.d10, .d12, .d14, .d22', function(e){
				addData2();
			});
			$(document).on('change','.prov_sel, .d18, .d20', function(e){
				addData2();
			});
			$( "#tf-slider-2, #tf-slider-3, #tf-slider-4, #tf-slider-5" ).on('slidechange', function( event, ui ){
				addData2();
			});
			

// END SECOND CHART
// THIRD CHART

			var e55text = $('.E55').text();
			var taxsavetext3 = e55text.replace('$','');
			var taxsavetext_3 = taxsavetext3.replace(',','');
			var tax_savings3 = parseFloat(taxsavetext_3);
			var investment = $('.E47').text();
			var investment_text = investment.replace('$','');
			var investment_text3 = investment_text.replace(',','');
			var investment_int = parseFloat(investment_text3);
			
			if (tax_savings3 > 0 ){
				var cash_outlay3 = investment_int - tax_savings3;
				var ctxthree = document.getElementById("tfChartThree");
				var tfChartThree = new Chart(ctxthree, {
					type:"pie",
					data:{
						labels:
							["Tax Savings","Cash Outlay"],
						datasets:[{
							data:[tax_savings3,cash_outlay3],
							backgroundColor:[tax_bg,cash_bg]
							}
						]
					},
					options:{
						title: {
							display: true,
							fontFamily: "'"+title_font+"'",
							fontColor: title_color,
							fontStyle: title_style,
							padding: 10,
							fontSize: title_size,
							text: investment + ' Investment',
						},
						responsive: false,
						showAllTooltips: true,
						legend:{
							display: false,
						},
						animation: {
							animateScale: true,
							animateRotate: true
						},
						tooltips:{
							enabled: false,
							callbacks:{
								 label: function (tooltipItem, data){
									var num = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
									return data.labels[tooltipItem.index]+": $"+num.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
								}
							}
						}
					}
					
				});
				
			}
			function addData3(){
				var investment = $('.E47').text();
				var investment_text = investment.replace('$','');
				var investment_text3 = investment_text.replace(',','');
				var investment_int = parseFloat(investment_text3);
				var e55text = $('.E55').text();
				var taxsavetext3 = e55text.replace('$','');
				var taxsave_text3 = taxsavetext3.replace(',','');
				var tax_savings3 = parseFloat(taxsave_text3);
				
				
				if (tax_savings3 > 0 && tax_savings3 < investment_int ){
					tfChartThree.data.datasets[0].data[0] = tax_savings3;
					tfChartThree.data.datasets[0].data[1] = investment_int - tax_savings3;
					tfChartThree.options.showAllTooltips = true;
					tfChartThree.options.title.display = true;
					tfChartThree.options.title.text = investment + " Investment";
				} else if ( tax_savings3 > 0 && tax_savings3 > investment_int ){
					tfChartThree.data.datasets[0].data[0] = tax_savings3;
					tfChartThree.data.datasets[0].data[1] = 0;
					tfChartThree.options.showAllTooltips = true;
					tfChartThree.options.title.display = true;
				} else {
					tfChartThree.data.datasets[0].data[0] = 0;
					tfChartThree.data.datasets[0].data[1] = 0;
					tfChartThree.options.showAllTooltips = false;
					tfChartThree.options.title.display = false;
				}	
				tfChartThree.update();
			}
			$(document).on('change','.d10, .d12, .d14, .d22', function(e){
				addData3();
			});
			$(document).on('change','.prov_sel, .d18, .d20', function(e){
				addData3();
			});
			$( "#tf-slider-2, #tf-slider-3, #tf-slider-4, #tf-slider-5" ).on('slidechange', function( event, ui ){
				addData3();
			});
			
});