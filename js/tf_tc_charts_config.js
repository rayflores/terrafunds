jQuery(document).ready(function($){
// FIRST CHART
			var text = $('.C55').text();
			var taxsavetext = text.replace('$','');
			var tax_savings = parseFloat(taxsavetext);
			if (tax_savings > 0 ){
				var cash_outlay = 1000 - tax_savings;
				var ctxone = document.getElementById("tfChartOne");
				var tfChartOne = new Chart(ctxone, {
					type:"pie",
					data:{
						labels:
							["Tax Savings","Cash Outlay"],
						datasets:[{
							data:[tax_savings,cash_outlay],
							backgroundColor:["rgb(255, 99, 132)","rgb(54, 162, 235)"]
							}
						]
					},
					options:{
						title: {
							display: true,
							text: '$1000 Investment',
						},
						responsive: false,
						showAllTooltips: true,
						legend:{
							display: false,
						}
					}
					
				});
				
			}
			function addData(){
				var text = $('.C55').text();
				var taxsavetext = text.replace('$','');
				var tax_savings = parseFloat(taxsavetext);
				if (tax_savings > 0 ){
					tfChartOne.data.datasets[0].data[0] = tax_savings;
					tfChartOne.data.datasets[0].data[1] = 1000 - tax_savings;
					tfChartOne.options.showAllTooltips = true;
					tfChartOne.options.title.display = true;
				} else {
					tfChartOne.data.datasets[0].data[0] = 0;
					tfChartOne.data.datasets[0].data[1] = 0;
					tfChartOne.options.showAllTooltips = false;
					tfChartOne.options.title.display = false;
				}	
				tfChartOne.update();
			}
			$(document).on('keyup change','.d10, .d12, .d14, .d22', function(e){
				addData();
				$("#tfChartOne").mouseover();
			});
			$( "#tf-slider-2, #tf-slider-3, #tf-slider-4, #tf-slider-5" ).on('slidechange', function( event, ui ){
				addData();
			});
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
						var elPosX = el._view.x+0.95*el._view.outerRadius*Math.cos((el._view.endAngle-el._view.startAngle)/2+el._view.startAngle);
						var elPosY = el._view.y+0.95*el._view.outerRadius*Math.sin((el._view.endAngle-el._view.startAngle)/2+el._view.startAngle);
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

			Chart.pluginService.register({
				  beforeRender: function (chart) {
					if (chart.config.options.showAllTooltips) {
						// create an array of tooltips
						// we can't use the chart tooltip because there is only one tooltip per chart
						chart.pluginTooltips = [];
						chart.config.data.datasets.forEach(function (dataset, i) {
							chart.getDatasetMeta(i).data.forEach(function (sector, j) {
								if ((sector._view.endAngle-sector._view.startAngle) > 2*Math.PI*0.02) {
									chart.pluginTooltips.push(
											new Chart.Tooltip({
										_chart: chart.chart,
										_chartInstance: chart,
										_data: chart.data,
										_options: chart.options.tooltips,
										_active: [sector]
									}, chart)
									);
								}
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
							tooltip._options.bodyFontSize = tooltip._chart.height*0.035;
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
				$("#tfChartOne").mouseover();
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
							backgroundColor:["rgb(255, 99, 132)","rgb(54, 162, 235)"]
							}
						]
					},
					options:{
						title: {
							display: true,
							text: '$5000 Investment',
						},
						responsive: false,
						showAllTooltips: true,
						legend:{
							display: false,
						}
					}
					
				});
				
			}
			function addData2(){
				var d55text = $('.D55').text();
				var taxsavetext2 = d55text.replace('$','');
				var tax_savings2 = parseFloat(taxsavetext2);
				if (tax_savings2 > 0 ){
					tfChartTwo.data.datasets[0].data[0] = tax_savings2;
					tfChartTwo.data.datasets[0].data[1] = 5000 - tax_savings2;
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
			$(document).on('keyup','.d10, .d12, .d14, .d22', function(e){
				addData2();
				$("#tfChartTwo").mouseover();
			});
			$( "#tf-slider-2, #tf-slider-3, #tf-slider-4, #tf-slider-5" ).on('slidechange', function( event, ui ){
				addData2();
			});

// END SECOND CHART
// THIRD CHART

			var e55text = $('.E55').text();
			var taxsavetext3 = e55text.replace('$','');
			var tax_savings3 = parseFloat(taxsavetext3);
			var investment = $('.E47').text();
			
			if (tax_savings3 > 0 ){
				
				var cash_outlay3 = 1000 - tax_savings3;
				var ctxthree = document.getElementById("tfChartThree");
				var tfChartThree = new Chart(ctxthree, {
					type:"pie",
					data:{
						labels:
							["Tax Savings","Cash Outlay"],
						datasets:[{
							data:[tax_savings3,cash_outlay3],
							backgroundColor:["rgb(255, 99, 132)","rgb(54, 162, 235)"]
							}
						]
					},
					options:{
						title: {
							display: true,
							text: investment + ' Investment',
						},
						responsive: false,
						showAllTooltips: true,
						legend:{
							display: false,
						}
					}
					
				});
				
			}
			function addData3(){
				var investment = $('.E47').text();
				var investment_text = investment.replace('$','');
				var investment_int = parseFloat(investement_text);
				var c55text = $('.E55').text();
				var taxsavetext3 = c55text.replace('$','');
				var tax_savings3 = parseFloat(taxsavetext3);
				
				if (tax_savings3 > 0 ){
					tfChartThree.data.datasets[0].data[0] = tax_savings3;
					tfChartThree.data.datasets[0].data[1] = investment_int - tax_savings3;
					tfChartThree.options.showAllTooltips = true;
					tfChartThree.options.title.display = true;
					tfChartThree.options.title.text = investment;
				} else {
					tfChartThree.data.datasets[0].data[0] = 0;
					tfChartThree.data.datasets[0].data[1] = 0;
					tfChartThree.options.showAllTooltips = false;
					tfChartThree.options.title.display = false;
				}	
				tfChartThree.update();
			}
			$(document).on('keyup','.d10, .d12, .d14, .d22', function(e){
				addData3();
				$("#tfChartThree").mouseover();
			});
			$( "#tf-slider-2, #tf-slider-3, #tf-slider-4, #tf-slider-5" ).on('slidechange', function( event, ui ){
				addData3();
			});
			
});