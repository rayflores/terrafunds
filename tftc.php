<?php
/* Plugin Name:  TerraFunds Tax Calculator
*/
class TF_Tax_Calc {  
	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	private $key = 'tf_tc_options';
	/**
 	 * Options page metabox id
 	 * @var string
 	 */
	private $subkey = 'tf_tc_simple_options';
	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = 'TerraFunds Tax Calculator';
	/**
	 * Holds an instance of the object
	 *
	 * @var Myprefix_Admin
	 **/
	private static $instance = null;
	/**
	 * Constructor
	 * @since 0.1.0
	 */
	private function __construct() {
		// Set our title
		$this->title = __( 'Tax Calc', 'wc-catc' );
		$this->sub_title = __( 'Simple Tax Calc', 'wc-catc' );
		add_shortcode('terra_calc', array($this, 'tf_tc_add_shortcode') );
		add_shortcode('terra_calc_simple', array($this, 'tf_tc_add_simple_shortcode') );
		
		add_action('wp_enqueue_scripts', array($this, 'tf_tc_enqueue_scripts') );

	}

	public function tf_tc_enqueue_scripts(){
		$options = get_option($this->key);
		$cash_outlay_bg = $options['tf_tc_options_cash_outlay_bg'];
		$tax_savings_bg = $options['tf_tc_options_tax_savings_bg'];
		$title_color = $options['tf_tc_options_chart_title_color'];
		$title_style = $options['tf_tc_options_chart_title_style'];
		$title_font = $options['tf_tc_options_chart_title_font'];
		$title_size = $options['tf_tc_options_chart_title_size'];
		$tooltip_bg = $options['tf_tc_options_tooltip_bg'];
		$bgs = array(
			'cash_outlay_bg' => $cash_outlay_bg,
			'tax_savings_bg' => $tax_savings_bg,
			'title_color' => $title_color,
			'title_style' => $title_style,
			'title_font' => $title_font,
			'title_size' => $title_size,
			'tooltip_bg' => $tooltip_bg,
		);
		wp_register_script( 'tf-tc-numeral', plugins_url('js/js/numeral.min.js', __FILE__) );
		wp_register_script( 'tf-tc-calx', plugins_url('js/jquery-calx-2.2.6.js', __FILE__) );
		wp_register_style( 'tf-slider-styles', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
		wp_register_style( 'tf-cust-styles', plugins_url('css/tf_tc_styles.css', __FILE__) );
		wp_register_script( 'tf-cust-script', plugins_url('js/tf_tc_script.js', __FILE__) );
		wp_register_script( 'tf-curform', plugins_url('js/js/jquery.formatCurrency.js',__FILE__) );
		wp_localize_script( 'tf-cust-script', 'tf_options', $bgs );
	

		wp_enqueue_style( 'tf-slider-styles' );
		wp_enqueue_style( 'tf-cust-styles' );
		wp_enqueue_script('jquery-ui-core', array('jquery') );
		wp_enqueue_script('jquery-ui-slider', array('jquery') );
		wp_enqueue_script( 'tf-tc-numeral' );
		wp_enqueue_script( 'tf-tc-calx', array('jquery') );
		wp_enqueue_script( 'tf-cust-script' );
		wp_enqueue_script( 'tf-touchpunch', plugins_url( 'js/jquery.ui.touch-punch.min.js',__FILE__ ), array('jquery','jquery-ui-slider'), false , true );
		wp_enqueue_script('chartjs-js', '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js');
		wp_enqueue_script('tftc-chartsjs', plugins_url('js/tf_tc_charts_config.js',__FILE__) );
		wp_enqueue_script( 'tf-curform', array('jquery') );
	}
	public function tf_tc_add_shortcode(){
		
		$options= get_option($this->key);

		ob_start();
		if ($options) {
		?>
		<div class="tf_calc_main">
		<form class="tf-form">
			<table class="table-responsive tf_calc_table col-md-6 col-sm-12">
			<tfoot>
				<tr>
				  <td colspan="2" class="footer">
					<div class="adv-content" style="display:none;">
						<?php echo isset($options['tf_tc_options_adv_inputs_footer']) ? $options['tf_tc_options_adv_inputs_footer'] : '* FMV means the Fair Market Value'; ?> 
					</div>
				  </td>
				</tr>
			  </tfoot>
				<tbody>
					<tr>
						<td colspan="2"class="h2 size-color"><?php echo isset($options['tf_tc_options_calculator_title']) ? $options['tf_tc_options_calculator_title'] : 'Calculator Inputs'; ?></td>
					</tr>
					<tr>
						<td valign="top" class="mobi">
							<?php echo $options['tf_tc_options_step_1_label']; ?>
						</td>
						<td class="slide-pad slider-td">
						
							<input type="hidden" data-cell="D8" name="tf_tc_options[tf_tc_options_province_selection]"  class="prov_sel" value="ON">
							<!--	<option value="BC" style="text-align:center;">BC</option>
								<option value="AB">AB</option>
								<option value="SK">SK</option>
								<option value="MB">MB</option>
								<option value="ON" selected="selected">ON</option>
								<option value="QC">QC</option>
								<option value="NS">NS</option>
							</select> -->
							<div class="tf-slider">
								<div class="tf-ticks">
									<span class="tick bc">BC</span>
									<span class="tick ab" style="left:16.5%;">AB</span>
									<span class="tick sk" style="left:33.33%;">SK</span>
									<span class="tick mb" style="left:49.5%;">MB</span>
									<span class="tick on" style="left:66%;">ON</span>
									<span class="tick qc" style="left:82.5%;">QC</span>
									<span class="tick ns" style="left:100%;">NS</span>
								</div>
								<div id="tf-slider-1"></div>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td valign="top" class="notopPad mobi">
							<?php echo $options['tf_tc_options_step_2_label']; ?>
						</td>
						<td class="slider-td slide-pad" valign="bottom">
							<input type="hidden" data-cell="D10" class="d10" value="160000" data-format="$ 0,0">
							<div class="tf-slider">
								<div id="tf-slider-2"></div>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td valign="top" class="notopPad mobi">
							<?php echo $options['tf_tc_options_step_3_label']; ?>
						</td>
						<td class="slider-td slide-pad" valign="bottom">
							<input type="hidden" data-cell="D12" class="d12" value="0" data-format="$ 0,0">
							<div class="tf-slider">
								<div id="tf-slider-3"></div>
							</div>
						</td>
						
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td valign="top" class="notopPad mobi">
							<?php echo $options['tf_tc_options_step_4_label']; ?>
						</td>
						<td class="slider-td slide-pad" valign="bottom">
							<input type="hidden" data-cell="D14" class="d14" value="0" data-format="$ 0,0">
							<div class="tf-slider">
								<div id="tf-slider-4"></div>
							</div>
							
						</td>

					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td valign="top" class="outer-label mobi">
							<?php echo $options['tf_tc_options_step_5_label']; ?>
						</td>
						<td id="radio-outer" valign="bottom">
							<input type="hidden" class="d18" data-cell="D18" name="tf_tc_options_contribute_selection" value="NO">
							<ul>
								<li class="li-1"><label for="r1">No</label>
										<input type="radio" name="tf_tc_options_contribute_selection" value="NO" checked="checked">
								</li>
								<li class="li-1"><label for="r2a">Reinvest</label> 
										<input type="radio" name="tf_tc_options_contribute_selection" value="YES - Reinvest">	
								</li>
								<li class="li-1"><label for="r2">RRSP</label> 
										<input type="radio" name="tf_tc_options_contribute_selection" value="YES - Contribute to RRSP">	
								</li>
								<li class="li-1"><label for="r3">Donate</label>
										<input type="radio" name="tf_tc_options_contribute_selection" value="YES - Donate">
								</li>
							</ul>

								<!-- <option value="NO" selected="selected">No</option>
								 <option value="YES - Contribute to RRSP">Yes - Contribute to RRSP</option>
								 <option value="YES - Donate">Yes - Donate</option> -->	

						</td>
					</tr>
					<tr>
						<td valign="top" class="outer-label mobi">
							<?php echo $options['tf_tc_options_step_6_label']; ?>
						</td>
						<td id="radio-outer" valign="bottom">
							<input type="hidden" data-cell="D20" name="tf_tc_options_capital_losses" class="d20" value="NO">
								<ul class="rtwo">
								<li><label for="r4">No</label>
										<input type="radio" name="tf_tc_options_capital_losses" value="NO" checked="checked">
								</li>
								<li><label for="r5">Yes</label> 
										<input type="radio" name="tf_tc_options_capital_losses" value="YES">	
								</li>
							</ul>
						</td>
					</tr>
						<tr>
							<td id="show-adv" class="h2 size-color" valign="top" align="center" colspan="2"><div class="fa fa-chevron-down rotate"></div>Advanced Inputs
							</td>
						</tr>
						<tr class="shownone">
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td valign="top" class="mobi">	
								<div class="adv-content" style="display:none;">
										<?php echo $options['tf_tc_options_step_7_label']; ?>
								</div>		
							</td>
							<td class="slider-td slide-pad">
								<div class="adv-content" style="display:none;">	
										<input type="hidden" data-cell="D22" class="d22" value="75" data-format="0%">
										<div class="tf-slider">
											<div id="tf-slider-5"></div>
										</div>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" class="mobi">	
								<div class="adv-content" style="display:none;">
										<?php echo isset($options['tf_tc_options_step_8_label']) ? $options['tf_tc_options_step_8_label'] : 'Maximum % of taxable income to invest'; ?>
								</div>		
							</td>	
							<td class="slider-td slide-pad">
								<div class="adv-content" style="display:none;">	
										<input type="hidden" data-cell="B17" class="b17" value="10" data-format="0%">
										<div class="tf-slider">
											<div id="tf-slider-6"></div>
										</div>
								</div>
							</td>
						</tr>
					
				</tbody>
			</table>

			<div class="chart-single col-md-6 col-sm-12" style="">
				<canvas id="tfChartThree" width="350" height="350" style="display:inline-block;"></canvas>
			</div>


		<div class="extra">
			<table class="tf-tc-results table-responsive">
				<tbody>
					<tr>
						<td colspan="4" class="h2 size-color show-summary"><div class="fa fa-chevron-down rotate2"></div><?php echo isset($options['tf_tc_options_results_title']) ? $options['tf_tc_options_results_title'] : 'Summary of Tax Savings'; ?></td>
					</tr>
					<tr>
						<td></td>
					</tr>
					<tr>
						<td></td>
					</tr>
					<tr>
						<td class="first">&nbsp;</td>
						<td valign="bottom" class="noPad text-center second"><div class="tax-content"><input type="hidden" data-cell="F43" data-formula='<?php echo $options['tf_tc_options_F43_formula']; ?>'/><label data-cell="C46" data-formula='<?php echo $options['tf_tc_options_C46_formula'];?>'></label></div></td>
						<td valign="bottom" class="noPad text-center second"><div class="tax-content"><input type="hidden" data-cell="G43" data-formula='<?php echo $options['tf_tc_options_G43_formula']; ?>'/><label data-cell="D46" data-formula='<?php echo $options['tf_tc_options_D46_formula']; ?>'></label></div></td>
						<td valign="bottom" class="noPad text-center second"><div class="tax-content">
						<label data-cell="E46" data-formula='<?php echo $options['tf_tc_options_E46_formula']; ?>' class="padR"></label></div></td>
					</tr>
					<tr>
						<td class="noPad first"><div class="tax-content">Investment</div></td>
						<td class="text-center noPad second"><div class="tax-content"><label class="C47" data-cell="C47" data-formula='<?php echo $options['tf_tc_options_C47_formula']; ?>' data-format="$0,0"></label></div></td>
						<td class="text-center noPad second"><div class="tax-content"><label data-cell="D47" data-formula='<?php echo $options['tf_tc_options_D47_formula']; ?>' data-format="$0,0"></label></div></td>
						<td class="text-center noPad second"><div class="tax-content"><label class="E47" data-cell="E47" data-formula='<?php echo $options['tf_tc_options_E47_formula']; ?>' data-format="$0,0"></label></div></td>
					</tr>
			<!--		<tr>
						<td colspan="3" class="noPad"><div class="tax-content"><hr class="sep"/></div></td>
						<td class="lPad"><div class="tax-content"><hr class="sep"/></div></td>
					</tr>
			-->		<tr class="topBorder tax-content">
						<td class="first"><div class="tax-content">Tax Rate</div></td>
						<td class="text-center second"><div class="tax-content"><label data-cell="C49" data-formula='<?php echo $options['tf_tc_options_C49_formula']; ?>' data-format="0.00%"></label></div></td>
						<td class="text-center second"><div class="tax-content"><label data-cell="D49" data-formula='<?php echo $options['tf_tc_options_D49_formula']; ?>' data-format="0.00%"></label></div></td>
						<td class="text-center second"><div class="tax-content"><label data-cell="E49" data-formula='<?php echo $options['tf_tc_options_E49_formula']; ?>' data-format="0.00%"></label></div></td>
					</tr>
			<!--		<tr>
						<td colspan="3" class="noPad"><div class="tax-content"><hr class="sep"/></div></td>
						<td class="lPad"><div class="tax-content"><hr class="sep"/></div></td>
					</tr>
			-->		<tr class="topBorder tax-content">
						<td class="first"><div class="tax-content">Tax savings</div></td>
						<td class="text-center second"><div class="tax-content"><label data-cell="C50" data-formula='<?php echo $options['tf_tc_options_C50_formula']; ?>' data-format="$0,0"></label></div></td>
						<td class="text-center second"><div class="tax-content"><label data-cell="D50" data-formula='<?php echo $options['tf_tc_options_D50_formula']; ?>' data-format="$0,0"></label></div></td>
						<td class="text-center second"><div class="tax-content"><label data-cell="E50" data-formula='<?php echo $options['tf_tc_options_E50_formula']; ?>' data-format="$0,0"></label></div></td>
					</tr>
			<!--		<tr>
						<td colspan="3" class="noPad"><div class="tax-content"><hr class="sep"/></div></td>
						<td class="lPad"><div class="tax-content"><hr class="sep"/></div></td>
					</tr>
			-->		<tr class="topBorder tax-content">
						<td class="first"><div class="tax-content">ITC savings</div></td>
						<td class="text-center second"><div class="tax-content"><label data-cell="C52" data-formula='<?php echo $options['tf_tc_options_C52_formula']; ?>' data-format="$0"></label></div></td>
						<td class="text-center second"><div class="tax-content"><label data-cell="D52" data-formula='<?php echo $options['tf_tc_options_D52_formula']; ?>' data-format="$0,0"></label></div></td>
						<td class="text-center second"><div class="tax-content"><label data-cell="E52" data-formula='<?php echo $options['tf_tc_options_E52_formula']; ?>' data-format="$0,0"></label></div></td>
					</tr>
			<!--		<tr>
						<td colspan="3" class="noPad"><div class="tax-content"><hr class="sep"/></div></td>
						<td class="lPad"><div class="tax-content"><hr class="sep"/></div></td>
					</tr>
			-->		<tr class="topBorder tax-content">
						<td class="first"><div class="tax-content"><label data-cell="B53" data-formula='<?php echo $options['tf_tc_options_B53_formula']; ?>'></label></div></td>
						<td class="text-center second"><div class="tax-content"><label data-cell="C53" data-formula='<?php echo $options['tf_tc_options_C53_formula']; ?>' data-format="$0,0"></label></div></td>
						<td class="text-center second"><div class="tax-content"><label data-cell="D53" data-formula='<?php echo $options['tf_tc_options_D53_formula']; ?>' data-format="$0,0"></label></div></td>
						<td class="text-center second"><div class="tax-content"><label data-cell="E53" data-formula='<?php echo $options['tf_tc_options_E53_formula']; ?>' data-format="$0,0"></label></div></td> 
					</tr>
			<!--		<tr>
						<td colspan="3" class="noPad"><div class="tax-content"><hr class="sep"/></div></td>
						<td class="lPad"><div class="tax-content"><hr class="sep"/></div></td>
					</tr>
			-->		<tr class="topBorder tax-content">
						<td class="first"><div class="tax-content"><label data-cell="B54" data-formula='<?php echo $options['tf_tc_options_B54_formula']; ?>'></label></div></td>
						<td class="hr text-center second"><div class="tax-content"><label data-cell="C54" data-formula='<?php echo $options['tf_tc_options_C54_formula']; ?>' data-format="($0,0)"></label></div></td>
						<td class="hr text-center second"><div class="tax-content"><label data-cell="D54" data-formula='<?php echo $options['tf_tc_options_D54_formula']; ?>' data-format="($0,0)"></label></div></td>
						<td class="hr text-center second"><div class="tax-content"><label data-cell="E54" data-formula='<?php echo $options['tf_tc_options_E54_formula']; ?>' data-format="($0,0)"></label></div></td>
					</tr>
			<!--		<tr>
						<td colspan="3" class="noPad"><div class="tax-content"><hr class="sep"/></div></td>
						<td class="lPad"><div class="tax-content"><hr class="sep"/></div></td>
					</tr>
			-->		<tr class="total-results allBold topBorder tax-content">
						<td valign="top" class="first"><div class="tax-content">Total tax savings</div></td>
						<td class="text-center second"><div class="tax-content"><label class="C55" data-cell="C55" data-formula='<?php echo $options['tf_tc_options_C55_formula']; ?>' data-format="$0,0" value=""></label></div></td>
						<td class="text-center second"><div class="tax-content"><label class="D55" data-cell="D55" data-formula='<?php echo $options['tf_tc_options_D55_formula']; ?>' data-format="$0,0"></label></div></td>
						<td class="text-center second"><div class="tax-content"><label class="E55" data-cell="E55" data-formula='<?php echo $options['tf_tc_options_E55_formula']; ?>' data-format="$0,0"></label></div></td>
						
					</tr>
				</tbody>
			</table>
		</div>

			<input type="hidden" data-cell="C48" data-formula='<?php echo $options['tf_tc_options_C48_formula']; ?>' />
			<input type="hidden" data-cell="D48" data-formula='<?php echo $options['tf_tc_options_D48_formula']; ?>' />
			<input type="hidden" data-cell="E48" data-formula='<?php echo $options['tf_tc_options_E48_formula']; ?>' />
			<input type="hidden" data-cell="C51" data-formula='<?php echo $options['tf_tc_options_C51_formula']; ?>' data-format="$0,0" />
			<input type="hidden" data-cell="D51" data-formula='<?php echo $options['tf_tc_options_D51_formula']; ?>' data-format="$0,0" />
			<input type="hidden" data-cell="E51" data-formula='<?php echo $options['tf_tc_options_E51_formula']; ?>' data-format="$0,0" />

			
			<input type="hidden" data-cell="H28" data-formula='<?php echo $options['tf_tc_options_H28']; ?>'/>
			<input type="hidden" data-cell="H29" data-formula='<?php echo $options['tf_tc_options_H29']; ?>'/>
			<input type="hidden" data-cell="H30" data-formula='<?php echo $options['tf_tc_options_H30']; ?>'/>
			<input type="hidden" data-cell="H31" data-formula='<?php echo $options['tf_tc_options_H31']; ?>'/>
			<input type="hidden" data-cell="H32" data-formula='<?php echo $options['tf_tc_options_H32']; ?>'/>
			<input type="hidden" data-cell="H33" data-formula='<?php echo $options['tf_tc_options_H33']; ?>'/>
			<input type="hidden" data-cell="H34" data-formula='<?php echo $options['tf_tc_options_H34']; ?>'/>
			
			<input type="hidden" data-cell="I28" data-formula='<?php echo $options['tf_tc_options_I28']; ?>'/>
			<input type="hidden" data-cell="I29" data-formula='<?php echo $options['tf_tc_options_I29']; ?>'/>
			<input type="hidden" data-cell="I30" data-formula='<?php echo $options['tf_tc_options_I30']; ?>'/>
			<input type="hidden" data-cell="I31" data-formula='<?php echo $options['tf_tc_options_I31']; ?>'/>
			<input type="hidden" data-cell="I32" data-formula='<?php echo $options['tf_tc_options_I32']; ?>'/>
			<input type="hidden" data-cell="I33" data-formula='<?php echo $options['tf_tc_options_I33']; ?>'/>
			<input type="hidden" data-cell="I34" data-formula='<?php echo $options['tf_tc_options_I34']; ?>'/>
			
			<input type="hidden" data-cell="J28" data-formula='<?php echo $options['tf_tc_options_J28']; ?>'/>
			<input type="hidden" data-cell="J29" data-formula='<?php echo $options['tf_tc_options_J29']; ?>'/>
			<input type="hidden" data-cell="J30" data-formula='<?php echo $options['tf_tc_options_J30']; ?>'/>
			<input type="hidden" data-cell="J31" data-formula='<?php echo $options['tf_tc_options_J31']; ?>'/>
			<input type="hidden" data-cell="J32" data-formula='<?php echo $options['tf_tc_options_J32']; ?>'/>
			<input type="hidden" data-cell="J33" data-formula='<?php echo $options['tf_tc_options_J33']; ?>'/>
			<input type="hidden" data-cell="J34" data-formula='<?php echo $options['tf_tc_options_J34']; ?>'/>
			
			<input type="hidden" data-cell="K28" data-formula='<?php echo $options['tf_tc_options_K28_formula']; ?>'/>
			<input type="hidden" data-cell="K29" data-formula='<?php echo $options['tf_tc_options_K29_formula']; ?>'/>
			<input type="hidden" data-cell="K30" data-formula='<?php echo $options['tf_tc_options_K30_formula']; ?>'/>
			<input type="hidden" data-cell="K31" data-formula='<?php echo $options['tf_tc_options_K31_formula']; ?>'/>
			<input type="hidden" data-cell="K32" data-formula='<?php echo $options['tf_tc_options_K32_formula']; ?>'/>
			<input type="hidden" data-cell="K33" data-formula='<?php echo $options['tf_tc_options_K33_formula']; ?>'/>
			<input type="hidden" data-cell="K34" data-formula='<?php echo $options['tf_tc_options_K34_formula']; ?>'/>
			
			<input type="hidden" data-cell="L28" data-formula='<?php echo $options['tf_tc_options_L28']; ?>'/>
			<input type="hidden" data-cell="L29" data-formula='<?php echo $options['tf_tc_options_L29']; ?>'/>
			<input type="hidden" data-cell="L30" data-formula='<?php echo $options['tf_tc_options_L30']; ?>'/>
			<input type="hidden" data-cell="L31" data-formula='<?php echo $options['tf_tc_options_L31']; ?>'/>
			<input type="hidden" data-cell="L32" data-formula='<?php echo $options['tf_tc_options_L32']; ?>'/>
			<input type="hidden" data-cell="L33" data-formula='<?php echo $options['tf_tc_options_L33']; ?>'/>
			<input type="hidden" data-cell="L34" data-formula='<?php echo $options['tf_tc_options_L34']; ?>'/>
			
			<input type="hidden" data-cell="M28" data-formula='<?php echo $options['tf_tc_options_M28']; ?>'/>
			<input type="hidden" data-cell="M29" data-formula='<?php echo $options['tf_tc_options_M29']; ?>'/>
			<input type="hidden" data-cell="M30" data-formula='<?php echo $options['tf_tc_options_M30']; ?>'/>
			<input type="hidden" data-cell="M31" data-formula='<?php echo $options['tf_tc_options_M31']; ?>'/>
			<input type="hidden" data-cell="M32" data-formula='<?php echo $options['tf_tc_options_M32']; ?>'/>
			<input type="hidden" data-cell="M33" data-formula='<?php echo $options['tf_tc_options_M33']; ?>'/>
			<input type="hidden" data-cell="M34" data-formula='<?php echo $options['tf_tc_options_M34']; ?>'/>
			
			<input type="hidden" data-cell="N28" data-formula='<?php echo $options['tf_tc_options_N28']; ?>'/>
			<input type="hidden" data-cell="N29" data-formula='<?php echo $options['tf_tc_options_N29']; ?>'/>
			<input type="hidden" data-cell="N30" data-formula='<?php echo $options['tf_tc_options_N30']; ?>'/>
			<input type="hidden" data-cell="N31" data-formula='<?php echo $options['tf_tc_options_N31']; ?>'/>
			<input type="hidden" data-cell="N32" data-formula='<?php echo $options['tf_tc_options_N32']; ?>'/>
			<input type="hidden" data-cell="N33" data-formula='<?php echo $options['tf_tc_options_N33']; ?>'/>
			<input type="hidden" data-cell="N34" data-formula='<?php echo $options['tf_tc_options_N34']; ?>'/>
			
			<input type="hidden" data-cell="O28" data-formula='<?php echo $options['tf_tc_options_O28_formula']; ?>'/>
			<input type="hidden" data-cell="O29" data-formula='<?php echo $options['tf_tc_options_O29_formula']; ?>'/>
			<input type="hidden" data-cell="O30" data-formula='<?php echo $options['tf_tc_options_O30_formula']; ?>'/>
			<input type="hidden" data-cell="O31" data-formula='<?php echo $options['tf_tc_options_O31_formula']; ?>'/>
			<input type="hidden" data-cell="O32" data-formula='<?php echo $options['tf_tc_options_O32_formula']; ?>'/>
			<input type="hidden" data-cell="O33" data-formula='<?php echo $options['tf_tc_options_O33_formula']; ?>'/>
			<input type="hidden" data-cell="O34" data-formula='<?php echo $options['tf_tc_options_O34_formula']; ?>'/>


			<input type="hidden" data-cell="E33" data-formula='<?php echo $options['tf_tc_options_E33_formula']; ?>'/>
			<input type="hidden" data-cell="E34" data-formula='<?php echo $options['tf_tc_options_E34_formula']; ?>'/>
			<input type="hidden" data-cell="E35" data-formula='<?php echo $options['tf_tc_options_E35_formula']; ?>' data-format="($ 0,0)"/>
			<input type="hidden" data-cell="E36" data-formula='<?php echo $options['tf_tc_options_E36_formula']; ?>'/>
			
			<input type="hidden" data-cell="J41" data-formula='<?php echo $options['tf_tc_options_J41_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="J42" data-formula='<?php echo $options['tf_tc_options_J42_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="J43" data-formula='<?php echo $options['tf_tc_options_J43_formula']; ?>' data-format="$0,0"/>
			
			<input type="hidden" data-cell="G60" data-formula='<?php echo $options['tf_tc_options_G60_formula']; ?>' />
			<input type="hidden" data-cell="G61" data-formula='<?php echo $options['tf_tc_options_G61_formula']; ?>' />
			<input type="hidden" data-cell="G62" data-formula='<?php echo $options['tf_tc_options_G62_formula']; ?>' />
			<input type="hidden" data-cell="G63" data-formula='<?php echo $options['tf_tc_options_G63_formula']; ?>' />
			<input type="hidden" data-cell="G64" data-formula='<?php echo $options['tf_tc_options_G64_formula']; ?>' />
			<input type="hidden" data-cell="G65" data-formula='<?php echo $options['tf_tc_options_G65_formula']; ?>' />
			<input type="hidden" data-cell="G66" data-formula='<?php echo $options['tf_tc_options_G66_formula']; ?>' />
			<input type="hidden" data-cell="G67" data-formula='<?php echo $options['tf_tc_options_G67_formula']; ?>' />
			
			<input type="hidden" data-cell="H60" data-formula='<?php echo $options['tf_tc_options_H60_formula']; ?>' />
			<input type="hidden" data-cell="I60" data-formula='<?php echo $options['tf_tc_options_I60_formula']; ?>' />
			<input type="hidden" data-cell="H61" data-formula='<?php echo $options['tf_tc_options_H61_formula']; ?>' />
			<input type="hidden" data-cell="H62" data-formula='<?php echo $options['tf_tc_options_H62_formula']; ?>' />
			<input type="hidden" data-cell="H63" data-formula='<?php echo $options['tf_tc_options_H63_formula']; ?>' />
			<input type="hidden" data-cell="H64" data-formula='<?php echo $options['tf_tc_options_H64_formula']; ?>' />
			<input type="hidden" data-cell="H65" data-formula='<?php echo $options['tf_tc_options_H65_formula']; ?>' />
			<input type="hidden" data-cell="H66" data-formula='<?php echo $options['tf_tc_options_H66_formula']; ?>' />
			<input type="hidden" data-cell="H67" data-formula='<?php echo $options['tf_tc_options_H67_formula']; ?>' />
			
			<input type="hidden" data-cell="M73" data-formula='<?php echo $options['tf_tc_options_M73_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="M74" data-formula='<?php echo $options['tf_tc_options_M74_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="M75" data-formula='<?php echo $options['tf_tc_options_M75_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="M76" data-formula='<?php echo $options['tf_tc_options_M76_formula']; ?>' data-format="$0,0"/>
			
			<input type="hidden" data-cell="O74" data-formula='<?php echo $options['tf_tc_options_O74_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="O75" data-formula='<?php echo $options['tf_tc_options_O75_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="O76" data-formula='<?php echo $options['tf_tc_options_O76_formula']; ?>' data-format="$0,0"/>
			
			<input type="hidden" data-cell="Q73" data-formula='<?php echo $options['tf_tc_options_Q73_formula']; ?>'/>
			<input type="hidden" data-cell="Q74" data-formula='<?php echo $options['tf_tc_options_Q74_formula']; ?>'/>
			<input type="hidden" data-cell="Q75" data-formula='<?php echo $options['tf_tc_options_Q75_formula']; ?>'/>
			<input type="hidden" data-cell="Q76" data-formula='<?php echo $options['tf_tc_options_Q76_formula']; ?>'/>

			<input type="hidden" data-cell="S73" data-formula='<?php echo $options['tf_tc_options_S73_formula']; ?>' />
			<input type="hidden" data-cell="S74" data-formula='<?php echo $options['tf_tc_options_S74_formula']; ?>' />
			<input type="hidden" data-cell="S75" data-formula='<?php echo $options['tf_tc_options_S75_formula']; ?>' />
			<input type="hidden" data-cell="S76" data-formula='<?php echo $options['tf_tc_options_S76_formula']; ?>' />
			<input type="hidden" data-cell="S77" data-formula='<?php echo $options['tf_tc_options_S77_formula']; ?>'/>
			
			<input type="hidden" data-cell="V73" data-formula='<?php echo $options['tf_tc_options_V73_formula']; ?>' />
			<input type="hidden" data-cell="V74" data-formula='<?php echo $options['tf_tc_options_V74_formula']; ?>' />
			<input type="hidden" data-cell="V75" data-formula='<?php echo $options['tf_tc_options_V75_formula']; ?>' />
			<input type="hidden" data-cell="V76" data-formula='<?php echo $options['tf_tc_options_V76_formula']; ?>' />
			<input type="hidden" data-cell="V77" data-formula='<?php echo $options['tf_tc_options_V77_formula']; ?>' />
			
			<input type="hidden" data-cell="X73" data-formula='<?php echo $options['tf_tc_options_X73_formula']; ?>' />
			<input type="hidden" data-cell="X74" data-formula='<?php echo $options['tf_tc_options_X74_formula']; ?>' />
			<input type="hidden" data-cell="X75" data-formula='<?php echo $options['tf_tc_options_X75_formula']; ?>' />
			<input type="hidden" data-cell="X76" data-formula='<?php echo $options['tf_tc_options_X76_formula']; ?>' />
			<input type="hidden" data-cell="X77" data-formula='<?php echo $options['tf_tc_options_X77_formula']; ?>' />
			
			<input type="hidden" data-cell="Z73" data-formula='<?php echo $options['tf_tc_options_Z73_formula']; ?>' />
			<input type="hidden" data-cell="Z74" data-formula='<?php echo $options['tf_tc_options_Z74_formula']; ?>' />
			<input type="hidden" data-cell="Z75" data-formula='<?php echo $options['tf_tc_options_Z75_formula']; ?>' />
			<input type="hidden" data-cell="Z76" data-formula='<?php echo $options['tf_tc_options_Z76_formula']; ?>' />
			<input type="hidden" data-cell="Z77" data-formula='<?php echo $options['tf_tc_options_Z77_formula']; ?>' />
			
			<input type="hidden" data-cell="AC73" data-formula='<?php echo $options['tf_tc_options_AC73_formula']; ?>' />
			<input type="hidden" data-cell="AC74" data-formula='<?php echo $options['tf_tc_options_AC74_formula']; ?>' />
			<input type="hidden" data-cell="AC75" data-formula='<?php echo $options['tf_tc_options_AC75_formula']; ?>' />
			<input type="hidden" data-cell="AC76" data-formula='<?php echo $options['tf_tc_options_AC76_formula']; ?>' />
			<input type="hidden" data-cell="AC77" data-formula='<?php echo $options['tf_tc_options_AC77_formula']; ?>' />
			
			<input type="hidden" data-cell="AH73" data-formula='<?php echo $options['tf_tc_options_AH73_formula']; ?>' />
			<input type="hidden" data-cell="AH74" data-formula='<?php echo $options['tf_tc_options_AH74_formula']; ?>' />
			<input type="hidden" data-cell="AH75" data-formula='<?php echo $options['tf_tc_options_AH75_formula']; ?>' />
			<input type="hidden" data-cell="AH76" data-formula='<?php echo $options['tf_tc_options_AH76_formula']; ?>' />
			<input type="hidden" data-cell="AH77" data-formula='<?php echo $options['tf_tc_options_AH77_formula']; ?>' />
			
			<input type="hidden" data-cell="M81" data-formula='<?php echo $options['tf_tc_options_M81_formula']; ?>' />
			<input type="hidden" data-cell="M82" data-formula='<?php echo $options['tf_tc_options_M82_formula']; ?>' />
			<input type="hidden" data-cell="M83" data-formula='<?php echo $options['tf_tc_options_M83_formula']; ?>' />
			<input type="hidden" data-cell="M84" data-formula='<?php echo $options['tf_tc_options_M84_formula']; ?>' />
			<input type="hidden" data-cell="M85" data-formula='<?php echo $options['tf_tc_options_M85_formula']; ?>' />
			<input type="hidden" data-cell="M99" data-formula='<?php echo $options['tf_tc_options_M99_formula']; ?>' />
			<input type="hidden" data-cell="M100" data-formula='<?php echo $options['tf_tc_options_M100_formula']; ?>' />
			<input type="hidden" data-cell="M101" data-formula='<?php echo $options['tf_tc_options_M101_formula']; ?>' />
			
			<input type="hidden" data-cell="T81" data-formula='<?php echo $options['tf_tc_options_T81_formula']; ?>' />
			<input type="hidden" data-cell="T82" data-formula='<?php echo $options['tf_tc_options_T82_formula']; ?>' />
			<input type="hidden" data-cell="T83" data-formula='<?php echo $options['tf_tc_options_T83_formula']; ?>' />
			<input type="hidden" data-cell="T84" data-formula='<?php echo $options['tf_tc_options_T84_formula']; ?>' />
			<input type="hidden" data-cell="T85" data-formula='<?php echo $options['tf_tc_options_T85_formula']; ?>' />
			<input type="hidden" data-cell="T87" data-formula='<?php echo $options['tf_tc_options_T87_formula']; ?>' />
			<input type="hidden" data-cell="T88" data-formula='<?php echo $options['tf_tc_options_T88_formula']; ?>' />
			<input type="hidden" data-cell="T89" data-formula='<?php echo $options['tf_tc_options_T89_formula']; ?>' />
			<input type="hidden" data-cell="T99" data-formula='<?php echo $options['tf_tc_options_T99_formula']; ?>' />
			<input type="hidden" data-cell="T100" data-formula='<?php echo $options['tf_tc_options_T100_formula']; ?>' />
			<input type="hidden" data-cell="T101" data-formula='<?php echo $options['tf_tc_options_T101_formula']; ?>' />
			
			<input type="hidden" data-cell="AA81" data-formula='<?php echo $options['tf_tc_options_AA81_formula']; ?>' />
			<input type="hidden" data-cell="AA82" data-formula='<?php echo $options['tf_tc_options_AA82_formula']; ?>' />
			<input type="hidden" data-cell="AA83" data-formula='<?php echo $options['tf_tc_options_AA83_formula']; ?>' />
			<input type="hidden" data-cell="AA84" data-formula='<?php echo $options['tf_tc_options_AA84_formula']; ?>' />
			<input type="hidden" data-cell="AA85" data-formula='<?php echo $options['tf_tc_options_AA85_formula']; ?>' />
			<input type="hidden" data-cell="AA87" data-formula='<?php echo $options['tf_tc_options_AA87_formula']; ?>' />
			<input type="hidden" data-cell="AA88" data-formula='<?php echo $options['tf_tc_options_AA88_formula']; ?>' />
			<input type="hidden" data-cell="AA89" data-formula='<?php echo $options['tf_tc_options_AA89_formula']; ?>' />
			<input type="hidden" data-cell="AA91" data-formula='<?php echo $options['tf_tc_options_AA91_formula']; ?>' />
			<input type="hidden" data-cell="AA92" data-formula='<?php echo $options['tf_tc_options_AA92_formula']; ?>' />
			<input type="hidden" data-cell="AA93" data-formula='<?php echo $options['tf_tc_options_AA93_formula']; ?>' />
			<input type="hidden" data-cell="AA95" data-formula='<?php echo $options['tf_tc_options_AA95_formula']; ?>' />
			<input type="hidden" data-cell="AA96" data-formula='<?php echo $options['tf_tc_options_AA96_formula']; ?>' />
			<input type="hidden" data-cell="AA97" data-formula='<?php echo $options['tf_tc_options_AA97_formula']; ?>' />
			<input type="hidden" data-cell="AA99" data-formula='<?php echo $options['tf_tc_options_AA99_formula']; ?>' />
			<input type="hidden" data-cell="AA100" data-formula='<?php echo $options['tf_tc_options_AA100_formula']; ?>' />
			<input type="hidden" data-cell="AA101" data-formula='<?php echo $options['tf_tc_options_AA101_formula']; ?>' />
			
			<input type="hidden" data-cell="AK81" data-formula='<?php echo $options['tf_tc_options_AK81_formula']; ?>' />
			<input type="hidden" data-cell="AK82" data-formula='<?php echo $options['tf_tc_options_AK82_formula']; ?>' />
			<input type="hidden" data-cell="AK83" data-formula='<?php echo $options['tf_tc_options_AK83_formula']; ?>' />
			<input type="hidden" data-cell="AK84" data-formula='<?php echo $options['tf_tc_options_AK84_formula']; ?>' />
			<input type="hidden" data-cell="AK85" data-formula='<?php echo $options['tf_tc_options_AK85_formula']; ?>' />
			<input type="hidden" data-cell="AK87" data-formula='<?php echo $options['tf_tc_options_AK87_formula']; ?>' />
			<input type="hidden" data-cell="AK88" data-formula='<?php echo $options['tf_tc_options_AK88_formula']; ?>' />
			<input type="hidden" data-cell="AK89" data-formula='<?php echo $options['tf_tc_options_AK89_formula']; ?>' />
			<input type="hidden" data-cell="AK91" data-formula='<?php echo $options['tf_tc_options_AK91_formula']; ?>' />
			<input type="hidden" data-cell="AK92" data-formula='<?php echo $options['tf_tc_options_AK92_formula']; ?>' />
			<input type="hidden" data-cell="AK93" data-formula='<?php echo $options['tf_tc_options_AK93_formula']; ?>' />
			<input type="hidden" data-cell="AK95" data-formula='<?php echo $options['tf_tc_options_AK95_formula']; ?>' />
			<input type="hidden" data-cell="AK96" data-formula='<?php echo $options['tf_tc_options_AK96_formula']; ?>' />
			<input type="hidden" data-cell="AK97" data-formula='<?php echo $options['tf_tc_options_AK97_formula']; ?>' />
			<input type="hidden" data-cell="AK99" data-formula='<?php echo $options['tf_tc_options_AK99_formula']; ?>' />
			<input type="hidden" data-cell="AK100" data-formula='<?php echo $options['tf_tc_options_AK100_formula']; ?>' />
			<input type="hidden" data-cell="AK101" data-formula='<?php echo $options['tf_tc_options_AK101_formula']; ?>' />
			
		</table>
		
		<div class="charts" style="clear:both;text-align:center">
			<canvas id="tfChartOne" width="350" height="350" style="display:none;"></canvas>
			<canvas id="tfChartTwo" width="350" height="350" style="display:none;"></canvas>

		</div>

		</form>
	<?php } else {  ?>
		<div class="tf-calc-warning">
			<p>Please ensure you have saved your Tax Calculator settings <a href="<?php echo '/wp-admin/admin.php?page=tf_tc_options'; ?>">here</a></p>
		</div>
	<?php } ?>
		


		<?php 
		return ob_get_clean();
	}
	/*
	Slim Down Version
	*/
	public function tf_tc_add_simple_shortcode(){
		
		$options= get_option($this->key);
		ob_start();
		if ($options) {
		?>
		<div class="tf_calc_simple_main">
		<form class="tf-form">
			<table class="table-responsive tf_calc_table_simple">
			<tfoot>
				<tr>
				  <td colspan="2" class="footer">
					<div class="adv-content" style="display:none;">
						<?php echo isset($options['tf_tc_options_adv_inputs_footer']) ? $options['tf_tc_options_adv_inputs_footer'] : '* FMV means the Fair Market Value'; ?> 
					</div>
				  </td>
				</tr>
			  </tfoot>
				<tbody>
					<tr>
						<td colspan="2"class="h2 size-color medium smedium"><?php echo isset($options['tf_tc_options_calculator_title']) ? $options['tf_tc_options_calculator_title'] : 'Calculator Inputs'; ?></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" class="outer-label light">
							<?php echo $options['tf_tc_options_step_1_label']; ?>
						</td>
						<td class="slide-pad slider-td">
						
							<input type="hidden" data-cell="D8" name="tf_tc_options[tf_tc_options_province_selection]"  class="prov_sel" value="ON">
							<!--	<option value="BC" style="text-align:center;">BC</option>
								<option value="AB">AB</option>
								<option value="SK">SK</option>
								<option value="MB">MB</option>
								<option value="ON" selected="selected">ON</option>
								<option value="QC">QC</option>
								<option value="NS">NS</option>
							</select> -->
							<div class="tf-slider">
								<div class="tf-ticks">
									<span class="tick light bc">BC</span>
									<span class="tick light ab" style="left:16%;">AB</span>
									<span class="tick light sk" style="left:33%;">SK</span>
									<span class="tick light mb" style="left:49%;">MB</span>
									<span class="tick light on" style="left:65.6667%;">ON</span>
									<span class="tick light qc" style="left:82.3337%;">QC</span>
									<span class="tick light ns" style="left:100%;">NS</span>
								</div>
								<div id="tf-slider-1"></div>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>

							<input type="hidden" data-cell="D10" class="d10" value="500000" data-format="$ 0,0">

					

							<input type="hidden" data-cell="D12" class="d12" value="0" data-format="$ 0,0">


							<input type="hidden" data-cell="D14" class="d14" value="0" data-format="$ 0,0">

					<tr>
						<td valign="top" class="outer-label  light">
							<?php echo $options['tf_tc_options_step_5_label']; ?>
						</td>
						<td id="radio-outer" valign="bottom">
							<input type="hidden" class="d18 light" data-cell="D18" name="tf_tc_options_contribute_selection" value="NO">
							<ul>
								<li class="li-1"><label for="r1" class="light">No</label>
										<input type="radio" name="tf_tc_options_contribute_selection" value="NO" checked="checked">
								</li>
								<li class="li-1"><label for="r2a" class="light">Reinvest</label> 
										<input type="radio" name="tf_tc_options_contribute_selection" value="YES - Reinvest">	
								</li>
								<li class="li-1"><label for="r2" class="light">RRSP</label> 
										<input type="radio" name="tf_tc_options_contribute_selection" value="YES - Contribute to RRSP">	
								</li>
								<li class="li-1"><label for="r3" class="light">Donate</label>
										<input type="radio" name="tf_tc_options_contribute_selection" value="YES - Donate">
								</li>
							</ul>

								<!-- <option value="NO" selected="selected">No</option>
								 <option value="YES - Contribute to RRSP">Yes - Contribute to RRSP</option>
								 <option value="YES - Donate">Yes - Donate</option> -->	

						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" class="outer-label light">
							<?php echo $options['tf_tc_options_step_6_label']; ?>
						</td>
						<td id="radio-outer" valign="bottom" class="light">
							<input type="hidden" data-cell="D20" name="tf_tc_options_capital_losses" class="d20" value="NO">
								<ul class="rtwo">
								<li><label for="r4" class="light">No</label>
										<input type="radio" name="tf_tc_options_capital_losses" value="NO" checked="checked">
								</li>
								<li><label for="r5" class="light">Yes</label> 
										<input type="radio" name="tf_tc_options_capital_losses" value="YES">	
								</li>
							</ul>
						</td>
					</tr>

										<input type="hidden" data-cell="D22" class="d22" value="75" data-format="0%">


										<input type="hidden" data-cell="B17" class="b17" value="10" data-format="0%">

					
				</tbody>
			</table>

			
		

			<div class="chart-single-simple" style="">
				<canvas id="tfChartOne" width="350" height="350" style="display:inline-block;"></canvas>
				<div class="more-options-simple medium">
					<div class="p-simple">
						<div class="medium-small">FOR MORE OPTIONS</div>
					</div>
					
					<div class="uabb-module-content uabb-button-wrap uabb-creative-button-wrap uabb-button-width-custom uabb-creative-button-width-custom uabb-button-center uabb-creative-button-center uabb-button-reponsive-center uabb-creative-button-reponsive-center uabb-button-has-icon uabb-creative-button-has-icon">
						<a href="/tax-savings-calculator/" target="_self" class="uabb-button uabb-creative-button uabb-creative-transparent-btn  uabb-transparent-fade-btn simple-calc-button " role="button">

											<i class="uabb-button-icon uabb-creative-button-icon uabb-button-icon-before uabb-creative-button-icon-before fa ua-icon ua-icon-calculator"></i>
											<span class="uabb-button-text uabb-creative-button-text medium-white">GO TO CALCULATOR</span>
							
						
						</a>
					</div>
				</div>
			</div>
			
			<table class="tf-tc-results-simple table-responsive">
				<tbody>
					<tr>
						<td colspan="2" class="h2 size-color simple-title medium smedium">
							<?php echo isset($options['tf_tc_options_results_title']) ? $options['tf_tc_options_results_title'] : 'Summary of Tax Savings'; ?>
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="noPad first sBold">Investment</td>
						<td class="text-center noPad second"><label class="C47 sBold" data-cell="C47" data-formula='<?php echo $options['tf_tc_options_C47_formula']; ?>' data-format="$0,0"></label></td>
						
					</tr>
			<!--		<tr>
						<td colspan="3" class="noPad"><div class="tax-content"><hr class="sep"/></div></td>
						<td class="lPad"><div class="tax-content"><hr class="sep"/></div></td>
					</tr>
			-->		<tr class="topBorder">
						<td class="first light">Tax Rate (maximum)</td>
						<td class="text-center second"><label data-cell="C49" data-formula='<?php echo $options['tf_tc_options_C49_formula']; ?>' data-format="0.00%" class="nBold light"></label></td>
						
					</tr>
			<!--		<tr>
						<td colspan="3" class="noPad"><div class="tax-content"><hr class="sep"/></div></td>
						<td class="lPad"><div class="tax-content"><hr class="sep"/></div></td>
					</tr>
			-->		<tr class="topBorder">
						<td class="first light">Tax savings</td>
						<td class="text-center second"><label data-cell="C50" data-formula='<?php echo $options['tf_tc_options_C50_formula']; ?>' data-format="$0,0" class="nBold light"></label></td>
						
					</tr>
			<!--		<tr>
						<td colspan="3" class="noPad"><div class="tax-content"><hr class="sep"/></div></td>
						<td class="lPad"><div class="tax-content"><hr class="sep"/></div></td>
					</tr>
			-->		<tr class="topBorder">
						<td class="first light">ITC savings</td>
						<td class="text-center second"><label data-cell="C52" data-formula='<?php echo $options['tf_tc_options_C52_formula']; ?>' data-format="$0" class="nBold light"></label></td>
						
					</tr>
			<!--		<tr>
						<td colspan="3" class="noPad"><div class="tax-content"><hr class="sep"/></div></td>
						<td class="lPad"><div class="tax-content"><hr class="sep"/></div></td>
					</tr>
			-->		<tr class="topBorder">
						<td class="first light"><label class="light" data-cell="B53" data-formula='<?php echo $options['tf_tc_options_B53_formula']; ?>'></label></td>
						<td class="text-center second"><label data-cell="C53" data-formula='<?php echo $options['tf_tc_options_C53_formula']; ?>' data-format="$0,0" class="nBold light"></label></td>
						
					</tr>
			<!--		<tr>
						<td colspan="3" class="noPad"><div class="tax-content"><hr class="sep"/></div></td>
						<td class="lPad"><div class="tax-content"><hr class="sep"/></div></td>
					</tr>
			-->		<tr class="topBorder">
						<td class="first"><label data-cell="B54" data-formula='<?php echo $options['tf_tc_options_B54_formula']; ?>' class="nBold light"></label></td>
						<td class="hr text-center second"><label data-cell="C54" data-formula='<?php echo $options['tf_tc_options_C54_formula']; ?>' data-format="($0,0)" class="nBold light"></label></td>
						
					</tr>
			<!--		<tr>
						<td colspan="3" class="noPad"><div class="tax-content"><hr class="sep"/></div></td>
						<td class="lPad"><div class="tax-content"><hr class="sep"/></div></td>
					</tr>
			-->		<tr class="total-results allBold topBorder">
						<td valign="top" class="first sBold">Total tax savings</td>
						<td class="text-center second"><label class="C55 sBold" data-cell="C55" data-formula='<?php echo $options['tf_tc_options_C55_formula']; ?>' data-format="$0,0" value=""></label></td>
						
						
					</tr>
				</tbody>
			</table>

			<input type="hidden" data-cell="C48" data-formula='<?php echo $options['tf_tc_options_C48_formula']; ?>' />
			<input type="hidden" data-cell="D48" data-formula='<?php echo $options['tf_tc_options_D48_formula']; ?>' />
			<input type="hidden" data-cell="E48" data-formula='<?php echo $options['tf_tc_options_E48_formula']; ?>' />
			<input type="hidden" data-cell="C51" data-formula='<?php echo $options['tf_tc_options_C51_formula']; ?>' data-format="$0,0" />
			<input type="hidden" data-cell="D51" data-formula='<?php echo $options['tf_tc_options_D51_formula']; ?>' data-format="$0,0" />
			<input type="hidden" data-cell="E51" data-formula='<?php echo $options['tf_tc_options_E51_formula']; ?>' data-format="$0,0" />

			
			<input type="hidden" data-cell="H28" data-formula='<?php echo $options['tf_tc_options_H28']; ?>'/>
			<input type="hidden" data-cell="H29" data-formula='<?php echo $options['tf_tc_options_H29']; ?>'/>
			<input type="hidden" data-cell="H30" data-formula='<?php echo $options['tf_tc_options_H30']; ?>'/>
			<input type="hidden" data-cell="H31" data-formula='<?php echo $options['tf_tc_options_H31']; ?>'/>
			<input type="hidden" data-cell="H32" data-formula='<?php echo $options['tf_tc_options_H32']; ?>'/>
			<input type="hidden" data-cell="H33" data-formula='<?php echo $options['tf_tc_options_H33']; ?>'/>
			<input type="hidden" data-cell="H34" data-formula='<?php echo $options['tf_tc_options_H34']; ?>'/>
			
			<input type="hidden" data-cell="I28" data-formula='<?php echo $options['tf_tc_options_I28']; ?>'/>
			<input type="hidden" data-cell="I29" data-formula='<?php echo $options['tf_tc_options_I29']; ?>'/>
			<input type="hidden" data-cell="I30" data-formula='<?php echo $options['tf_tc_options_I30']; ?>'/>
			<input type="hidden" data-cell="I31" data-formula='<?php echo $options['tf_tc_options_I31']; ?>'/>
			<input type="hidden" data-cell="I32" data-formula='<?php echo $options['tf_tc_options_I32']; ?>'/>
			<input type="hidden" data-cell="I33" data-formula='<?php echo $options['tf_tc_options_I33']; ?>'/>
			<input type="hidden" data-cell="I34" data-formula='<?php echo $options['tf_tc_options_I34']; ?>'/>
			
			<input type="hidden" data-cell="J28" data-formula='<?php echo $options['tf_tc_options_J28']; ?>'/>
			<input type="hidden" data-cell="J29" data-formula='<?php echo $options['tf_tc_options_J29']; ?>'/>
			<input type="hidden" data-cell="J30" data-formula='<?php echo $options['tf_tc_options_J30']; ?>'/>
			<input type="hidden" data-cell="J31" data-formula='<?php echo $options['tf_tc_options_J31']; ?>'/>
			<input type="hidden" data-cell="J32" data-formula='<?php echo $options['tf_tc_options_J32']; ?>'/>
			<input type="hidden" data-cell="J33" data-formula='<?php echo $options['tf_tc_options_J33']; ?>'/>
			<input type="hidden" data-cell="J34" data-formula='<?php echo $options['tf_tc_options_J34']; ?>'/>
			
			<input type="hidden" data-cell="K28" data-formula='<?php echo $options['tf_tc_options_K28_formula']; ?>'/>
			<input type="hidden" data-cell="K29" data-formula='<?php echo $options['tf_tc_options_K29_formula']; ?>'/>
			<input type="hidden" data-cell="K30" data-formula='<?php echo $options['tf_tc_options_K30_formula']; ?>'/>
			<input type="hidden" data-cell="K31" data-formula='<?php echo $options['tf_tc_options_K31_formula']; ?>'/>
			<input type="hidden" data-cell="K32" data-formula='<?php echo $options['tf_tc_options_K32_formula']; ?>'/>
			<input type="hidden" data-cell="K33" data-formula='<?php echo $options['tf_tc_options_K33_formula']; ?>'/>
			<input type="hidden" data-cell="K34" data-formula='<?php echo $options['tf_tc_options_K34_formula']; ?>'/>
			
			<input type="hidden" data-cell="L28" data-formula='<?php echo $options['tf_tc_options_L28']; ?>'/>
			<input type="hidden" data-cell="L29" data-formula='<?php echo $options['tf_tc_options_L29']; ?>'/>
			<input type="hidden" data-cell="L30" data-formula='<?php echo $options['tf_tc_options_L30']; ?>'/>
			<input type="hidden" data-cell="L31" data-formula='<?php echo $options['tf_tc_options_L31']; ?>'/>
			<input type="hidden" data-cell="L32" data-formula='<?php echo $options['tf_tc_options_L32']; ?>'/>
			<input type="hidden" data-cell="L33" data-formula='<?php echo $options['tf_tc_options_L33']; ?>'/>
			<input type="hidden" data-cell="L34" data-formula='<?php echo $options['tf_tc_options_L34']; ?>'/>
			
			<input type="hidden" data-cell="M28" data-formula='<?php echo $options['tf_tc_options_M28']; ?>'/>
			<input type="hidden" data-cell="M29" data-formula='<?php echo $options['tf_tc_options_M29']; ?>'/>
			<input type="hidden" data-cell="M30" data-formula='<?php echo $options['tf_tc_options_M30']; ?>'/>
			<input type="hidden" data-cell="M31" data-formula='<?php echo $options['tf_tc_options_M31']; ?>'/>
			<input type="hidden" data-cell="M32" data-formula='<?php echo $options['tf_tc_options_M32']; ?>'/>
			<input type="hidden" data-cell="M33" data-formula='<?php echo $options['tf_tc_options_M33']; ?>'/>
			<input type="hidden" data-cell="M34" data-formula='<?php echo $options['tf_tc_options_M34']; ?>'/>
			
			<input type="hidden" data-cell="N28" data-formula='<?php echo $options['tf_tc_options_N28']; ?>'/>
			<input type="hidden" data-cell="N29" data-formula='<?php echo $options['tf_tc_options_N29']; ?>'/>
			<input type="hidden" data-cell="N30" data-formula='<?php echo $options['tf_tc_options_N30']; ?>'/>
			<input type="hidden" data-cell="N31" data-formula='<?php echo $options['tf_tc_options_N31']; ?>'/>
			<input type="hidden" data-cell="N32" data-formula='<?php echo $options['tf_tc_options_N32']; ?>'/>
			<input type="hidden" data-cell="N33" data-formula='<?php echo $options['tf_tc_options_N33']; ?>'/>
			<input type="hidden" data-cell="N34" data-formula='<?php echo $options['tf_tc_options_N34']; ?>'/>
			
			<input type="hidden" data-cell="O28" data-formula='<?php echo $options['tf_tc_options_O28_formula']; ?>'/>
			<input type="hidden" data-cell="O29" data-formula='<?php echo $options['tf_tc_options_O29_formula']; ?>'/>
			<input type="hidden" data-cell="O30" data-formula='<?php echo $options['tf_tc_options_O30_formula']; ?>'/>
			<input type="hidden" data-cell="O31" data-formula='<?php echo $options['tf_tc_options_O31_formula']; ?>'/>
			<input type="hidden" data-cell="O32" data-formula='<?php echo $options['tf_tc_options_O32_formula']; ?>'/>
			<input type="hidden" data-cell="O33" data-formula='<?php echo $options['tf_tc_options_O33_formula']; ?>'/>
			<input type="hidden" data-cell="O34" data-formula='<?php echo $options['tf_tc_options_O34_formula']; ?>'/>


			<input type="hidden" data-cell="E33" data-formula='<?php echo $options['tf_tc_options_E33_formula']; ?>'/>
			<input type="hidden" data-cell="E34" data-formula='<?php echo $options['tf_tc_options_E34_formula']; ?>'/>
			<input type="hidden" data-cell="E35" data-formula='<?php echo $options['tf_tc_options_E35_formula']; ?>' data-format="($ 0,0)"/>
			<input type="hidden" data-cell="E36" data-formula='<?php echo $options['tf_tc_options_E36_formula']; ?>'/>
			
			<input type="hidden" data-cell="J41" data-formula='<?php echo $options['tf_tc_options_J41_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="J42" data-formula='<?php echo $options['tf_tc_options_J42_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="J43" data-formula='<?php echo $options['tf_tc_options_J43_formula']; ?>' data-format="$0,0"/>
			
			<input type="hidden" data-cell="G60" data-formula='<?php echo $options['tf_tc_options_G60_formula']; ?>' />
			<input type="hidden" data-cell="G61" data-formula='<?php echo $options['tf_tc_options_G61_formula']; ?>' />
			<input type="hidden" data-cell="G62" data-formula='<?php echo $options['tf_tc_options_G62_formula']; ?>' />
			<input type="hidden" data-cell="G63" data-formula='<?php echo $options['tf_tc_options_G63_formula']; ?>' />
			<input type="hidden" data-cell="G64" data-formula='<?php echo $options['tf_tc_options_G64_formula']; ?>' />
			<input type="hidden" data-cell="G65" data-formula='<?php echo $options['tf_tc_options_G65_formula']; ?>' />
			<input type="hidden" data-cell="G66" data-formula='<?php echo $options['tf_tc_options_G66_formula']; ?>' />
			<input type="hidden" data-cell="G67" data-formula='<?php echo $options['tf_tc_options_G67_formula']; ?>' />
			
			<input type="hidden" data-cell="H60" data-formula='<?php echo $options['tf_tc_options_H60_formula']; ?>' />
			<input type="hidden" data-cell="I60" data-formula='<?php echo $options['tf_tc_options_I60_formula']; ?>' />
			<input type="hidden" data-cell="H61" data-formula='<?php echo $options['tf_tc_options_H61_formula']; ?>' />
			<input type="hidden" data-cell="H62" data-formula='<?php echo $options['tf_tc_options_H62_formula']; ?>' />
			<input type="hidden" data-cell="H63" data-formula='<?php echo $options['tf_tc_options_H63_formula']; ?>' />
			<input type="hidden" data-cell="H64" data-formula='<?php echo $options['tf_tc_options_H64_formula']; ?>' />
			<input type="hidden" data-cell="H65" data-formula='<?php echo $options['tf_tc_options_H65_formula']; ?>' />
			<input type="hidden" data-cell="H66" data-formula='<?php echo $options['tf_tc_options_H66_formula']; ?>' />
			<input type="hidden" data-cell="H67" data-formula='<?php echo $options['tf_tc_options_H67_formula']; ?>' />
			
			<input type="hidden" data-cell="M73" data-formula='<?php echo $options['tf_tc_options_M73_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="M74" data-formula='<?php echo $options['tf_tc_options_M74_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="M75" data-formula='<?php echo $options['tf_tc_options_M75_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="M76" data-formula='<?php echo $options['tf_tc_options_M76_formula']; ?>' data-format="$0,0"/>
			
			<input type="hidden" data-cell="O74" data-formula='<?php echo $options['tf_tc_options_O74_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="O75" data-formula='<?php echo $options['tf_tc_options_O75_formula']; ?>' data-format="$0,0"/>
			<input type="hidden" data-cell="O76" data-formula='<?php echo $options['tf_tc_options_O76_formula']; ?>' data-format="$0,0"/>
			
			<input type="hidden" data-cell="Q73" data-formula='<?php echo $options['tf_tc_options_Q73_formula']; ?>'/>
			<input type="hidden" data-cell="Q74" data-formula='<?php echo $options['tf_tc_options_Q74_formula']; ?>'/>
			<input type="hidden" data-cell="Q75" data-formula='<?php echo $options['tf_tc_options_Q75_formula']; ?>'/>
			<input type="hidden" data-cell="Q76" data-formula='<?php echo $options['tf_tc_options_Q76_formula']; ?>'/>

			<input type="hidden" data-cell="S73" data-formula='<?php echo $options['tf_tc_options_S73_formula']; ?>' />
			<input type="hidden" data-cell="S74" data-formula='<?php echo $options['tf_tc_options_S74_formula']; ?>' />
			<input type="hidden" data-cell="S75" data-formula='<?php echo $options['tf_tc_options_S75_formula']; ?>' />
			<input type="hidden" data-cell="S76" data-formula='<?php echo $options['tf_tc_options_S76_formula']; ?>' />
			<input type="hidden" data-cell="S77" data-formula='<?php echo $options['tf_tc_options_S77_formula']; ?>'/>
			
			<input type="hidden" data-cell="V73" data-formula='<?php echo $options['tf_tc_options_V73_formula']; ?>' />
			<input type="hidden" data-cell="V74" data-formula='<?php echo $options['tf_tc_options_V74_formula']; ?>' />
			<input type="hidden" data-cell="V75" data-formula='<?php echo $options['tf_tc_options_V75_formula']; ?>' />
			<input type="hidden" data-cell="V76" data-formula='<?php echo $options['tf_tc_options_V76_formula']; ?>' />
			<input type="hidden" data-cell="V77" data-formula='<?php echo $options['tf_tc_options_V77_formula']; ?>' />
			
			<input type="hidden" data-cell="X73" data-formula='<?php echo $options['tf_tc_options_X73_formula']; ?>' />
			<input type="hidden" data-cell="X74" data-formula='<?php echo $options['tf_tc_options_X74_formula']; ?>' />
			<input type="hidden" data-cell="X75" data-formula='<?php echo $options['tf_tc_options_X75_formula']; ?>' />
			<input type="hidden" data-cell="X76" data-formula='<?php echo $options['tf_tc_options_X76_formula']; ?>' />
			<input type="hidden" data-cell="X77" data-formula='<?php echo $options['tf_tc_options_X77_formula']; ?>' />
			
			<input type="hidden" data-cell="Z73" data-formula='<?php echo $options['tf_tc_options_Z73_formula']; ?>' />
			<input type="hidden" data-cell="Z74" data-formula='<?php echo $options['tf_tc_options_Z74_formula']; ?>' />
			<input type="hidden" data-cell="Z75" data-formula='<?php echo $options['tf_tc_options_Z75_formula']; ?>' />
			<input type="hidden" data-cell="Z76" data-formula='<?php echo $options['tf_tc_options_Z76_formula']; ?>' />
			<input type="hidden" data-cell="Z77" data-formula='<?php echo $options['tf_tc_options_Z77_formula']; ?>' />
			
			<input type="hidden" data-cell="AC73" data-formula='<?php echo $options['tf_tc_options_AC73_formula']; ?>' />
			<input type="hidden" data-cell="AC74" data-formula='<?php echo $options['tf_tc_options_AC74_formula']; ?>' />
			<input type="hidden" data-cell="AC75" data-formula='<?php echo $options['tf_tc_options_AC75_formula']; ?>' />
			<input type="hidden" data-cell="AC76" data-formula='<?php echo $options['tf_tc_options_AC76_formula']; ?>' />
			<input type="hidden" data-cell="AC77" data-formula='<?php echo $options['tf_tc_options_AC77_formula']; ?>' />
			
			<input type="hidden" data-cell="AH73" data-formula='<?php echo $options['tf_tc_options_AH73_formula']; ?>' />
			<input type="hidden" data-cell="AH74" data-formula='<?php echo $options['tf_tc_options_AH74_formula']; ?>' />
			<input type="hidden" data-cell="AH75" data-formula='<?php echo $options['tf_tc_options_AH75_formula']; ?>' />
			<input type="hidden" data-cell="AH76" data-formula='<?php echo $options['tf_tc_options_AH76_formula']; ?>' />
			<input type="hidden" data-cell="AH77" data-formula='<?php echo $options['tf_tc_options_AH77_formula']; ?>' />
			
			<input type="hidden" data-cell="M81" data-formula='<?php echo $options['tf_tc_options_M81_formula']; ?>' />
			<input type="hidden" data-cell="M82" data-formula='<?php echo $options['tf_tc_options_M82_formula']; ?>' />
			<input type="hidden" data-cell="M83" data-formula='<?php echo $options['tf_tc_options_M83_formula']; ?>' />
			<input type="hidden" data-cell="M84" data-formula='<?php echo $options['tf_tc_options_M84_formula']; ?>' />
			<input type="hidden" data-cell="M85" data-formula='<?php echo $options['tf_tc_options_M85_formula']; ?>' />
			<input type="hidden" data-cell="M99" data-formula='<?php echo $options['tf_tc_options_M99_formula']; ?>' />
			<input type="hidden" data-cell="M100" data-formula='<?php echo $options['tf_tc_options_M100_formula']; ?>' />
			<input type="hidden" data-cell="M101" data-formula='<?php echo $options['tf_tc_options_M101_formula']; ?>' />
			
			<input type="hidden" data-cell="T81" data-formula='<?php echo $options['tf_tc_options_T81_formula']; ?>' />
			<input type="hidden" data-cell="T82" data-formula='<?php echo $options['tf_tc_options_T82_formula']; ?>' />
			<input type="hidden" data-cell="T83" data-formula='<?php echo $options['tf_tc_options_T83_formula']; ?>' />
			<input type="hidden" data-cell="T84" data-formula='<?php echo $options['tf_tc_options_T84_formula']; ?>' />
			<input type="hidden" data-cell="T85" data-formula='<?php echo $options['tf_tc_options_T85_formula']; ?>' />
			<input type="hidden" data-cell="T87" data-formula='<?php echo $options['tf_tc_options_T87_formula']; ?>' />
			<input type="hidden" data-cell="T88" data-formula='<?php echo $options['tf_tc_options_T88_formula']; ?>' />
			<input type="hidden" data-cell="T89" data-formula='<?php echo $options['tf_tc_options_T89_formula']; ?>' />
			<input type="hidden" data-cell="T99" data-formula='<?php echo $options['tf_tc_options_T99_formula']; ?>' />
			<input type="hidden" data-cell="T100" data-formula='<?php echo $options['tf_tc_options_T100_formula']; ?>' />
			<input type="hidden" data-cell="T101" data-formula='<?php echo $options['tf_tc_options_T101_formula']; ?>' />
			
			<input type="hidden" data-cell="AA81" data-formula='<?php echo $options['tf_tc_options_AA81_formula']; ?>' />
			<input type="hidden" data-cell="AA82" data-formula='<?php echo $options['tf_tc_options_AA82_formula']; ?>' />
			<input type="hidden" data-cell="AA83" data-formula='<?php echo $options['tf_tc_options_AA83_formula']; ?>' />
			<input type="hidden" data-cell="AA84" data-formula='<?php echo $options['tf_tc_options_AA84_formula']; ?>' />
			<input type="hidden" data-cell="AA85" data-formula='<?php echo $options['tf_tc_options_AA85_formula']; ?>' />
			<input type="hidden" data-cell="AA87" data-formula='<?php echo $options['tf_tc_options_AA87_formula']; ?>' />
			<input type="hidden" data-cell="AA88" data-formula='<?php echo $options['tf_tc_options_AA88_formula']; ?>' />
			<input type="hidden" data-cell="AA89" data-formula='<?php echo $options['tf_tc_options_AA89_formula']; ?>' />
			<input type="hidden" data-cell="AA91" data-formula='<?php echo $options['tf_tc_options_AA91_formula']; ?>' />
			<input type="hidden" data-cell="AA92" data-formula='<?php echo $options['tf_tc_options_AA92_formula']; ?>' />
			<input type="hidden" data-cell="AA93" data-formula='<?php echo $options['tf_tc_options_AA93_formula']; ?>' />
			<input type="hidden" data-cell="AA95" data-formula='<?php echo $options['tf_tc_options_AA95_formula']; ?>' />
			<input type="hidden" data-cell="AA96" data-formula='<?php echo $options['tf_tc_options_AA96_formula']; ?>' />
			<input type="hidden" data-cell="AA97" data-formula='<?php echo $options['tf_tc_options_AA97_formula']; ?>' />
			<input type="hidden" data-cell="AA99" data-formula='<?php echo $options['tf_tc_options_AA99_formula']; ?>' />
			<input type="hidden" data-cell="AA100" data-formula='<?php echo $options['tf_tc_options_AA100_formula']; ?>' />
			<input type="hidden" data-cell="AA101" data-formula='<?php echo $options['tf_tc_options_AA101_formula']; ?>' />
			
			<input type="hidden" data-cell="AK81" data-formula='<?php echo $options['tf_tc_options_AK81_formula']; ?>' />
			<input type="hidden" data-cell="AK82" data-formula='<?php echo $options['tf_tc_options_AK82_formula']; ?>' />
			<input type="hidden" data-cell="AK83" data-formula='<?php echo $options['tf_tc_options_AK83_formula']; ?>' />
			<input type="hidden" data-cell="AK84" data-formula='<?php echo $options['tf_tc_options_AK84_formula']; ?>' />
			<input type="hidden" data-cell="AK85" data-formula='<?php echo $options['tf_tc_options_AK85_formula']; ?>' />
			<input type="hidden" data-cell="AK87" data-formula='<?php echo $options['tf_tc_options_AK87_formula']; ?>' />
			<input type="hidden" data-cell="AK88" data-formula='<?php echo $options['tf_tc_options_AK88_formula']; ?>' />
			<input type="hidden" data-cell="AK89" data-formula='<?php echo $options['tf_tc_options_AK89_formula']; ?>' />
			<input type="hidden" data-cell="AK91" data-formula='<?php echo $options['tf_tc_options_AK91_formula']; ?>' />
			<input type="hidden" data-cell="AK92" data-formula='<?php echo $options['tf_tc_options_AK92_formula']; ?>' />
			<input type="hidden" data-cell="AK93" data-formula='<?php echo $options['tf_tc_options_AK93_formula']; ?>' />
			<input type="hidden" data-cell="AK95" data-formula='<?php echo $options['tf_tc_options_AK95_formula']; ?>' />
			<input type="hidden" data-cell="AK96" data-formula='<?php echo $options['tf_tc_options_AK96_formula']; ?>' />
			<input type="hidden" data-cell="AK97" data-formula='<?php echo $options['tf_tc_options_AK97_formula']; ?>' />
			<input type="hidden" data-cell="AK99" data-formula='<?php echo $options['tf_tc_options_AK99_formula']; ?>' />
			<input type="hidden" data-cell="AK100" data-formula='<?php echo $options['tf_tc_options_AK100_formula']; ?>' />
			<input type="hidden" data-cell="AK101" data-formula='<?php echo $options['tf_tc_options_AK101_formula']; ?>' />
			
		</table>

		<div class="charts" style="clear:both;text-align:center">
			<canvas id="tfChartTwo" width="350" height="350" style="display:none;"></canvas>
			<canvas id="tfChartThree" width="350" height="350" style="display:none;"></canvas>

		</div>

		</form>
	<?php } else {  ?>
		<div class="tf-calc-warning">
			<p>Please ensure you have saved your Tax Calculator settings <a href="<?php echo '/wp-admin/admin.php?page=tf_tc_options'; ?>">here</a></p>
		</div>
	<?php } ?>
		


		<?php 
		return ob_get_clean();
		
	}
/**
	 * Returns the running object
	 *
	 * @return Myprefix_Admin
	 **/
	public static function get_instance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}
		/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		global $my_admin_page;
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		// enqueue admin scripts/styles
		
		add_action( 'admin_enqueue_scripts', array($this, 'tf_tc_admin_enqueue_scripts') );
	}
	public function tf_tc_admin_enqueue_scripts($hook){
		global $my_admin_page, $pagenow;
		$options = get_option($this->key);
		$options_page = array( 'tf_tc_options','tf_tc_simple_options');
		$the_page = isset($_GET['page']) ? $_GET['page'] : '';
		

		if ( in_array($the_page,$options_page )) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'tf-tc-numeral', plugins_url('js/js/numeral.min.js', __FILE__), array('jquery'), false, true );
			wp_enqueue_script( 'tf-tc-calx', plugins_url('js/jquery-calx-2.2.6.js', __FILE__), array('jquery'), false, true );
			wp_enqueue_script( 'tf-cust-script', plugins_url('js/tf_tc_admin_script.js', __FILE__), array('jquery', 'wp-color-picker'), false, true );
		}

	}
	public function tf_tc_get_url_by_shortcode($shortcode) {
		global $wpdb;

		$url = '';

		$sql = 'SELECT ID
			FROM ' . $wpdb->posts . '
			WHERE
				post_type = "page"
				AND post_status="publish"
				AND post_content LIKE "%' . $shortcode . '%"';

		if ($id = $wpdb->get_var($sql)) {
			$url = get_permalink($id);
		}

		return $url;
	}
	
	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
		register_setting( $this->subkey, $this->subkey );
		//main
		add_settings_section(
			'tf_tc_options_section', 
			__( 'TerraFunds Tax Calculator Settings', 'wc-catc' ), 
			array( $this, 'tf_tc_options_section_callback'), 
			$this->key
		);
		add_settings_section(
			'tf_tc_options_simple_section',
			__( 'TerraFunds Simple Tax Calculator Settings', 'wc-catc'),
			array( $this, 'tf_tc_simple_options_section_callback'),
			$this->subkey
		);
		
	}
	/**
	 * Register our field to WP
	 * @since  0.1.0
	 */
	public function tf_tc_options_color_field_render(  ) {  

		$options = get_option( $this->key );
		$val = ( isset( $options['tf_tc_options_message_bg_color'] ) ) ? $options['tf_tc_options_message_bg_color'] : '';
		?>
		<input type="text" name="tf_tc_options[tf_tc_options_message_bg_color]" id="tf_tc_options_color_field" class="wp-color-picker-field" value="<?php echo $val; ?>"/>
		<?php

	}
	/**
	 * Register our settings description to WP
	 * @since  0.1.0
	 */
	public function tf_tc_options_section_callback(  ) { 
		$shortcode = '[terra_calc]';
		echo '<p>Adjust the labels and constants for the calculator.</p>';
		echo '<p>edit the style of the front-end display <a href="/wp-admin/plugin-editor.php?file=terrafunds-tax-calculator/css/tf_tc_styles.css&plugin=terrafunds-tax-calculator/tftc.php">here</a>, make sure to save changes first.</p><p>View Page with Tax Calculator here: <a href="' . $this->tf_tc_get_url_by_shortcode($shortcode) .'" target="_blank">View Page</a></p>';

	}
	/**
	 * Register our simple settings description to WP
	 * @since  0.1.0
	 */
	public function tf_tc_simple_options_section_callback(  ) { 
		$shortcode = '[terra_calc_simple]';
		echo '<p>Adjust the labels and constants for the calculator.</p>';
		echo '<p>edit the style of the front-end display <a href="/wp-admin/plugin-editor.php?file=terrafunds-tax-calculator/css/tf_tc_styles.css&plugin=terrafunds-tax-calculator/tftc.php">here</a>, make sure to save changes first.</p><p>View Page with Simple Tax Calculator here: <a href="' . $this->tf_tc_get_url_by_shortcode($shortcode) .'" target="_blank">View Page</a></p>';

	}
		/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		global $my_admin_page;
		$my_admin_page = add_menu_page( $this->title, $this->title, 'manage_options', $this->key, array( $this, 'admin_page_display' ) );
		add_submenu_page( $this->key, $this->sub_title, $this->sub_title, 'manage_options', $this->subkey, array( $this, 'simple_admin_page_display' ) );
	}
	
	/**
	 * Admin page markup.
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		$options = get_option($this->key);
		// echo '<pre>';
		// print_r($options);
		// echo '</pre>';
		if(isset($_POST['example_plugin_reset'])) {
				check_admin_referer('example-plugin-reset', 'example-plugin-reset-nonce');
				delete_option($this->key);
		?>
				<div class="updated">
					<p><?php _e('All options have been restored to their default values.', 'example-plugin'); ?></p>
				</div>
		<?php } ?>
		<style>
		td { border: 1px solid #ccc; }
		.submit input#submit.button-primary { position:fixed;bottom:5%;right:10%;}
		</style>
		<div class="wrap tf_tc-options-page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

				<form action='options.php' method='post' class="tf_admin_form" id="calc_form">
					<?php settings_fields( $this->key ); ?>
					<?php do_settings_sections( $this->key ); ?>
					<div>
						<label>Label For Calculator Inputs<br/>
							<input type="text" name="tf_tc_options[tf_tc_options_calculator_title]" value="<?php echo  isset($options['tf_tc_options_calculator_title']) ? $options['tf_tc_options_calculator_title'] : 'Calculator Inputs'; ?>"/>
						</label>
						<label>Label For Input Options<br/>
							<input type="text" name="tf_tc_options[tf_tc_options_slim_calculator_title]" value="<?php echo  isset($options['tf_tc_options_slim_calculator_title']) ? $options['tf_tc_options_slim_calculator_title'] : 'Input Options'; ?>"/>
						</label>
					</div>
					<div>
						<label>Label For Results Inputs<br/>
							<input type="text" name="tf_tc_options[tf_tc_options_results_title]" value="<?php echo isset($options['tf_tc_options_results_title']) ? $options['tf_tc_options_results_title'] : 'Total Tax Savings'; ?>"/>
						</label>
					</div>
					<div>
						<label>Color for Cash Outlay Background<br/>
							<input type="text" class="tf-color-picker" data-alpha="true" data-default-color="rgba(18,171,66,1)" name="tf_tc_options[tf_tc_options_cash_outlay_bg]" value="<?php echo isset($options['tf_tc_options_cash_outlay_bg']) ? $options['tf_tc_options_cash_outlay_bg'] : 'rgba(18,171,66,1)'; ?>"/>
						</label>
					</div>
					<div>
						<label>Color for Tax Savings Background<br/>
							<input type="text"  class="tf-color-picker" data-alpha="true" data-default-color="rgba(76,76,76,1)" name="tf_tc_options[tf_tc_options_tax_savings_bg]" value="<?php echo isset($options['tf_tc_options_tax_savings_bg']) ? $options['tf_tc_options_tax_savings_bg'] : 'rgba(76,76,76,1)'; ?>"/>
						</label>
					</div>
					<div>
						<label>Chart Title Size<br/>
							<input type="text" name="tf_tc_options[tf_tc_options_chart_title_size]" value="<?php echo isset($options['tf_tc_options_chart_title_size']) ? $options['tf_tc_options_chart_title_size'] : '14'; ?>"/>
						</label>
					<br/>
						<label>Chart Title Style<br/>
							<input type="text" name="tf_tc_options[tf_tc_options_chart_title_style]" value="<?php echo isset($options['tf_tc_options_chart_title_style']) ? $options['tf_tc_options_chart_title_style'] : 'bold'; ?>"/>
						</label>
					<br/>
						<label>Chart Title Font<br/>
							<input type="text" name="tf_tc_options[tf_tc_options_chart_title_font]" value="<?php echo isset($options['tf_tc_options_chart_title_font']) ? $options['tf_tc_options_chart_title_font'] : 'Lato'; ?>"/>
						</label>
					<br/>
						<label>Chart Title Color<br/>
							<input type="text" class="tf-color-picker" data-alpha="true" data-default-color="rgba(34,34,34,1)" name="tf_tc_options[tf_tc_options_chart_title_color]" value="<?php echo isset($options['tf_tc_options_chart_title_font']) ? $options['tf_tc_options_chart_title_color'] : 'rgba(34,34,34,1)'; ?>"/>
						</label>
					<br/>
						<label>Tooltip Background<br/>
							<input type="text" class="tf-color-picker" data-alpha="true" data-default-color="rgba(247,105,10,1)" name="tf_tc_options[tf_tc_options_tooltip_bg]" value="<?php echo isset($options['tf_tc_options_tooltip_bg']) ? $options['tf_tc_options_tooltip_bg'] : 'rgba(247,105,10,1)'; ?>"/>
						</label>
					</div>
					<table class="form-table tax-table-1"style="width:40%">
					<tr>
						<th scope="row">Front-end labels for steps</th>
					</tr>
						
						<tr colspan="2">
							<td>
								<label>B8:  Step 1 Label</label>
								<input type="text" name="tf_tc_options[tf_tc_options_step_1_label]" value="<?php echo isset($options['tf_tc_options_step_1_label']) ? $options['tf_tc_options_step_1_label'] : 'Step 1: Your Province'; ?>"/>
							</td>
							<td><label>D8</label>
								<select data-cell="D8" name="tf_tc_options[tf_tc_options_D8]" style="width:100%;text-align:center;text-align-last:center;" class="prov_sel">
									<?php if ('' !== $options['tf_tc_options_d8_select_1']) { ?>
											<option value='<?php echo isset($options[$this->key . '_d8_select_1']) ? $options[$this->key . '_d8_select_1'] : 'BC'; ?>' selected="selected"><?php echo isset($options[$this->key . '_d8_select_1']) ? $options[$this->key . '_d8_select_1'] : 'BC'; ?></option>
									<?php }
										  if ('' !== $options['tf_tc_options_d8_select_2']){ ?>
											<option value="<?php echo isset($options[$this->key . '_d8_select_2']) ? $options[$this->key . '_d8_select_2'] : 'AB'; ?>"><?php echo isset($options[$this->key . '_d8_select_2']) ? $options[$this->key . '_d8_select_2'] : 'AB'; ?></option>
									<?php }
										  if ('' !== $options['tf_tc_options_d8_select_3']){ ?>
											<option value="<?php echo isset($options[$this->key . '_d8_select_3']) ? $options[$this->key . '_d8_select_3'] : 'SK'; ?>"><?php echo isset($options[$this->key . '_d8_select_3']) ? $options[$this->key . '_d8_select_3'] : 'SK'; ?></option>
									<?php }
										  if ('' !== $options['tf_tc_options_d8_select_4']){ ?>
											<option value="<?php echo isset($options[$this->key . '_d8_select_4']) ? $options[$this->key . '_d8_select_4'] : 'MB'; ?>"><?php echo isset($options[$this->key . '_d8_select_4']) ? $options[$this->key . '_d8_select_4'] : 'MB'; ?></option>
									<?php }
										  if ('' !== $options['tf_tc_options_d8_select_5']){ ?>
											<option value="<?php echo isset($options[$this->key . '_d8_select_5']) ? $options[$this->key . '_d8_select_5'] : 'ON'; ?>"><?php echo isset($options[$this->key . '_d8_select_5']) ? $options[$this->key . '_d8_select_5'] : 'ON'; ?></option>
									<?php }
										  if ('' !== $options['tf_tc_options_d8_select_6']){ ?>
											<option value="<?php echo isset($options[$this->key . '_d8_select_6']) ? $options[$this->key . '_d8_select_6'] : 'QC'; ?>"><?php echo isset($options[$this->key . '_d8_select_6']) ? $options[$this->key . '_d8_select_6'] : 'QC'; ?></option>
									<?php }
										  if ('' !== $options['tf_tc_options_d8_select_7']){ ?>
											<option value="<?php echo isset($options[$this->key . '_d8_select_7']) ? $options[$this->key . '_d8_select_7'] : 'NS'; ?>"><?php echo isset($options[$this->key . '_d8_select_7']) ? $options[$this->key . '_d8_select_7'] : 'NS'; ?></option>
									<?php }
										  if ('' !== $options['tf_tc_options_d8_select_8']){ ?>
											<option value="<?php echo isset($options[$this->key . '_d8_select_8']) ? $options[$this->key . '_d8_select_8'] : ''; ?>"><?php echo isset($options[$this->key . '_d8_select_8']) ? $options[$this->key . '_d8_select_8'] : ''; ?></option>
									<?php }
										  if ('' !== $options['tf_tc_options_d8_select_9']){ ?>
											<option value="<?php echo isset($options[$this->key . '_d8_select_9']) ? $options[$this->key . '_d8_select_9'] : ''; ?>"><?php echo isset($options[$this->key . '_d8_select_9']) ? $options[$this->key . '_d8_select_9'] : ''; ?></option>
									<?php }
										  if ('' !== $options['tf_tc_options_d8_select_10']){ ?>
											<option value="<?php echo isset($options[$this->key . '_d8_select_10']) ? $options[$this->key . '_d8_select_10'] : ''; ?>"><?php echo isset($options[$this->key . '_d8_select_10']) ? $options[$this->key . '_d8_select_10'] : ''; ?></option>
									<?php } ?>
								</select>
							</td>
							<td>
								<input type="text" class="H5" name="tf_tc_options[tf_tc_options_d8_select_1]" value='<?php echo isset($options[$this->key . '_d8_select_1']) ? $options[$this->key . '_d8_select_1'] : 'BC'; ?>'/>
							</td>
							<td>
								<input type="text" class="I5" name="tf_tc_options[tf_tc_options_d8_select_2]" value='<?php echo isset($options[$this->key . '_d8_select_2']) ? $options[$this->key . '_d8_select_2'] : 'AB'; ?>'/>
							</td>							
							<td>
								<input type="text" class="J5" name="tf_tc_options[tf_tc_options_d8_select_3]" value='<?php echo isset($options[$this->key . '_d8_select_3']) ? $options[$this->key . '_d8_select_3'] : 'SK'; ?>'/>
							</td>
							<td>
								<input type="text" class="K5" name="tf_tc_options[tf_tc_options_d8_select_4]" value='<?php echo isset($options['tf_tc_options_d8_select_4']) ? $options[$this->key . '_d8_select_4'] : 'MB'; ?>'/>
							</td>
							<td>
								<input type="text" class="L5" name="tf_tc_options[tf_tc_options_d8_select_5]" value='<?php echo isset($options['tf_tc_options_d8_select_5']) ? $options[$this->key . '_d8_select_5'] : 'ON'; ?>'/>
							</td>
							<td>
								<input type="text" class="M5" name="tf_tc_options[tf_tc_options_d8_select_6]" value='<?php echo isset($options[$this->key . '_d8_select_6']) ? $options[$this->key . '_d8_select_6'] : 'QC'; ?>'/>
							</td>
							<td>
								<input type="text" class="N5" name="tf_tc_options[tf_tc_options_d8_select_7]" value='<?php echo isset($options[$this->key . '_d8_select_7']) ? $options[$this->key . '_d8_select_7'] : 'NS'; ?>'/>
							</td>
							<td>
								<input type="text" class="O5" name="tf_tc_options[tf_tc_options_d8_select_8]" value='<?php echo isset($options[$this->key . '_d8_select_8']) ? $options[$this->key . '_d8_select_8'] : ''; ?>'/>
							</td>
							<td>
								<input type="text" class="P5" name="tf_tc_options[tf_tc_options_d8_select_9]" value='<?php echo isset($options[$this->key . '_d8_select_9']) ? $options[$this->key . '_d8_select_9'] : ''; ?>'/>
							</td>
							<td>
								<input type="text" class="Q5" name="tf_tc_options[tf_tc_options_d8_select_10]" value='<?php echo isset($options[$this->key . '_d8_select_10']) ? $options[$this->key . '_d8_select_10'] : ''; ?>'/>
							</td>
						</tr>
						<tr>
							<td>
								<label>B10:  Step 2 Label</label>
								<input type="text" name="tf_tc_options[tf_tc_options_step_2_label]" value="<?php echo isset($options['tf_tc_options_step_2_label']) ? $options['tf_tc_options_step_2_label'] : 'Step 2: Income (salary, bonuses)'; ?>"/>
							</td>
							<td><label>D10</label><br/>
							<input type="text" data-cell="D10" class="d10" value="150000" data-format="$ 0,0">
						</td>
						</tr>
						<tr>
							<td>
								<label>B12:  Step 3 Label</label>
								<input type="text" name="tf_tc_options[tf_tc_options_step_3_label]" value="<?php echo isset($options['tf_tc_options_step_3_label']) ? $options['tf_tc_options_step_3_label'] : 'Step 3: Capital Gains'; ?>"/>
							</td>
							<td><label>D12</label><br/>
							<input type="text" data-cell="D12" class="d12" value="0" data-format="$ 0,0">
						</td>
						</tr>
						<tr>
							<td>
								<label> B14:  Step 4 Label</label>
								<input type="text" name="tf_tc_options[tf_tc_options_step_4_label]" value="<?php echo isset($options['tf_tc_options_step_4_label']) ? $options['tf_tc_options_step_4_label'] : 'Step 4: Deductions: RRSP & other '; ?>"/>
							</td>
							<td><label>D14</label><br/>
							<input type="text" data-cell="D14" class="d14" value="0" data-format="$ 0,0">
						</td>
						</tr>
						<tr>
							<td><label>B17: Step 7 : Input maximum % of income to invest in LP. If hidden set default to 10%</label>
							<input class="FB17" name="tf_tc_options[tf_tc_options_B17_formula]" data-cell="B17" value="<?php echo isset($options['tf_tc_options_B17_formula']) ? $options['tf_tc_options_B17_formula'] : '0.10' ?>" />
							</td>
							
						</tr>
						<tr>
							<td>
								<label>B18: Step 5 Label</label>
								<input type="text" name="tf_tc_options[tf_tc_options_step_5_label]" value="<?php echo isset($options['tf_tc_options_step_5_label']) ? $options['tf_tc_options_step_5_label'] : 'Step 5: Optional: Contribute Terra investment to RRSP or Donate'; ?>"/>
							</td>
							<td><label>D18</label>
								<select class="d18" data-cell="D18" name="tf_tc_options[tf_tc_options_contribute_selection]" style="width:100%;text-align:center;text-align-last:center;">
							<?php 
								if ('' !== $options[$this->key . '_contribute_select_1']){ ?>
									<option value="<?php echo isset($options[$this->key . '_contribute_select_1']) ? $options[$this->key . '_contribute_select_1'] : 'NO'; ?>" selected="selected"><?php echo $options[$this->key . '_contribute_select_1']; ?></option>
							<?php }
								if ('' !== $options[$this->key . '_contribute_select_2']){ ?>
									<option value="<?php echo isset($options[$this->key . '_contribute_select_2']) ? $options[$this->key . '_contribute_select_2'] : 'YES - Reinvest'; ?>"><?php echo $options[$this->key . '_contribute_select_2']; ?></option>
							<?php }
								if ('' !== $options[$this->key . '_contribute_select_3']){ ?>
									<option value="<?php echo isset($options[$this->key . '_contribute_select_3']) ? $options[$this->key . '_contribute_select_3'] : 'YES - Contribute to RRSP'; ?>"><?php echo $options[$this->key . '_contribute_select_3']; ?></option>
							<?php }
								if ('' !== $options[$this->key . '_contribute_select_4']){ ?>
									<option value="<?php echo isset($options[$this->key . '_contribute_select_4']) ? $options[$this->key . '_contribute_select_4'] : 'YES - Donate'; ?>"><?php echo $options[$this->key . '_contribute_select_4']; ?></option>
							<?php } ?>

								</select>	
							</td>
							<td>
								<input type="text" class="H6" value="<?php echo isset($options[$this->key . '_contribute_select_1']) ? $options[$this->key . '_contribute_select_1'] : 'NO'; ?>" name="tf_tc_options[tf_tc_options_contribute_select_1]"/>
							</td>
							<td>
								<input type="text" class="I6" value="<?php echo isset($options[$this->key . '_contribute_select_2']) ? $options[$this->key . '_contribute_select_2'] : 'YES - Reinvest'; ?>" name="tf_tc_options[tf_tc_options_contribute_select_2]"/>
							</td>
							<td>
								<input type="text" class="J6" value="<?php echo isset($options[$this->key . '_contribute_select_3']) ? $options[$this->key . '_contribute_select_3'] : 'YES - Contribute to RRSP'; ?>" name="tf_tc_options[tf_tc_options_contribute_select_3]"/>
							</td>
							<td>
								<input type="text" class="K6" value="<?php echo isset($options[$this->key . '_contribute_select_4']) ? $options[$this->key . '_contribute_select_4'] : 'YES - Donate'; ?>" name="tf_tc_options[tf_tc_options_contribute_select_4]"/>
							</td>
						</tr>
						<tr>
							<td>
								<label>B20:  Step 6 Label</label>
								<input type="text" name="tf_tc_options[tf_tc_options_step_6_label]" value="<?php echo isset($options['tf_tc_options_step_6_label']) ? $options['tf_tc_options_step_6_label'] : 'Step 6: Capital Losses available for RRSP contribution or Donation'; ?>"/>
							</td>
							<td><label>D20</label>
								<select data-cell="D20" name="tf_tc_options[tf_tc_options_contribute_selection]" style="width:100%;text-align:center;text-align-last:center;">
									<option value="NO">No</option>
									<option value="YES">Yes</option>
								</select>	
							</td>
						</tr>
						<tr>
							<td>
								<label>D22:  Step 7 Label</label>
								
								<input type="text" name="tf_tc_options[tf_tc_options_step_7_label]" value="<?php echo isset($options['tf_tc_options_step_7_label']) ? $options['tf_tc_options_step_7_label'] : 'Step 7: Optional: FMV of Terra investment in Step 5'; ?>"/>
							</td>
							<td><label>D22</label><br/>
								<input type="text" data-cell="D22" class="d22" value="75" data-format="0%">
							</td>
						</tr>
<tr>
							<td>
								<label>D23:  Step 8 Label</label>
								
								<input type="text" name="tf_tc_options[tf_tc_options_step_8_label]" value="<?php echo isset($options['tf_tc_options_step_8_label']) ? $options['tf_tc_options_step_8_label'] : 'Maximum % of taxable income to invest'; ?>"/>
							</td>
							<td>
							</td>
						</tr>								
					</table>
					<!-- Table 1 working -->
					<table class="form-table tax-table-2">
					<tr valign="top">
						<th scope="row" colspan="9">Table used for calculating the Donation tax credits used for taxable income ≤$202,800 and taxable income ≥$202,801. Note that the $202,800 threshold will change each year and needs to be updated.</th>
					</tr>
					<tr colspan="15">
						<td></td>
						<td></td>
						<td></td>					
						<td>Taxable Income</td>
	<td><label>H26</label><br/><input type="text" data-cell="H26" name="tf_tc_options[tf_tc_options_H26]" value="<?php echo isset($options['tf_tc_options_H26']) ? $options['tf_tc_options_H26'] : '202800'; ?>"/></td>
						<td></td>
						<td></td>
						<td></td>
						<td><label>L26</label><br/><input type="text" data-cell="L26" name="tf_tc_options[tf_tc_options_L26]" value="<?php echo isset($options['tf_tc_options_L26']) ? $options['tf_tc_options_L26'] : '202801'; ?>"/></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr colspan="15">
						<td></td>
						<td></td>
						<td></td>
						<td>Province</td>
						<td>Federal Tax</td>
						<td>Provincial Donation Tax Credit >$200</td>
						<td>Surtax</td>
						<td>Max Donation tax credit for taxable income ≤$202,800</td>
						<td>Federal Tax</td>
						<td>Provincial Donation Tax Credit >$200</td>
						<td>Surtax</td>
						<td>Max Donation tax credit for taxable income ≥$202,801</td>

					</tr>
					<tr colspan="12">
						<td></td>
						<td></td>
						<td></td>
						<td><label>G28</label><br/><input type="text" data-cell="G28" name="tf_tc_options[tf_tc_options_G28]" value="<?php echo isset($options['tf_tc_options_G28']) ? $options['tf_tc_options_G28'] : 'BC'; ?>"/></td>
						<td><label>H28</label><br/><input class="bc_fed_tax_1" type="text" name="tf_tc_options[tf_tc_options_H28]" value="<?php echo isset($options['tf_tc_options_H28']) ? $options['tf_tc_options_H28'] : '0.29'; ?>" data-cell="H28" /></td>
						<td><label>I28</label><br/><input type="text" name="tf_tc_options[tf_tc_options_I28]" value="<?php echo isset($options['tf_tc_options_I28']) ? $options['tf_tc_options_I28'] : '0.1470'; ?>" data-cell="I28" /></td>
						<td><label>J28</label><br/><input type="text" name="tf_tc_options[tf_tc_options_J28]" value="<?php echo isset($options['tf_tc_options_J28']) ? $options['tf_tc_options_J28'] : '0'; ?>" data-cell="J28" /></td>
						<td><label>K28</label><br/><input type="text" data-cell="K28"/><input type="text" name="tf_tc_options[tf_tc_options_K28_formula]"  class="FK28" value="<?php echo isset($options['tf_tc_options_K28_formula']) ? $options['tf_tc_options_K28_formula'] : 'H28+I28+J28'; ?>"/></td>
						<td><label>L28</label><br/><input type="text" name="tf_tc_options[tf_tc_options_L28]" value="<?php echo isset($options['tf_tc_options_L28']) ? $options['tf_tc_options_L28'] : '0.33'; ?>" data-cell="L28" /></td>
						<td><label>M28</label><br/><input type="text" name="tf_tc_options[tf_tc_options_M28]" value="<?php echo isset($options['tf_tc_options_M28']) ? $options['tf_tc_options_M28'] : '0.147'; ?>" data-cell="M28" /></td>
						<td><label>N28</label><br/><input type="text" name="tf_tc_options[tf_tc_options_N28]" value="<?php echo isset($options['tf_tc_options_N28']) ? $options['tf_tc_options_N28'] : '0.0'; ?>" data-cell="N28" /></td>
						<td><label>O28</label><br/><input type="text" data-cell="O28"/><input type="text" name="tf_tc_options[tf_tc_options_O28_formula]"  class="FO28" value="<?php echo isset($options['tf_tc_options_O28_formula']) ? $options['tf_tc_options_O28_formula'] : 'L28+M28+N28'; ?>"/></td>
					</tr>
					<tr colspan="15">
						<td></td>
						<td></td>
						<td></td>
						<td><label>G29</label><br/><input type="text" name="tf_tc_options[tf_tc_options_G29]" value="<?php echo isset($options['tf_tc_options_G29']) ? $options['tf_tc_options_G29'] : 'AB'; ?>" data-cell="G29"/></td>
						<td><label>H29</label><br/><input type="text" name="tf_tc_options[tf_tc_options_H29]" value="<?php echo isset($options['tf_tc_options_H29']) ? $options['tf_tc_options_H29'] : '0.29'; ?>" data-cell="H29" /></td>
						<td><label>I29</label><br/><input type="text" name="tf_tc_options[tf_tc_options_I29]" value="<?php echo isset($options['tf_tc_options_I29']) ? $options['tf_tc_options_I29'] : '0.21'; ?>" data-cell="I29" /></td>
						<td><label>J29</label><br/><input type="text" name="tf_tc_options[tf_tc_options_J29]" value="<?php echo isset($options['tf_tc_options_J29']) ? $options['tf_tc_options_J29'] : '0'; ?>" data-cell="J29" /></td>
						<td><label>K29</label><br/><input type="text" data-cell="K29" /><br/><input type="text" name="tf_tc_options[tf_tc_options_K29_formula]"  class="FK29" value="<?php echo isset($options['tf_tc_options_K29_formula']) ? $options['tf_tc_options_K29_formula'] : 'H29+I29+J29'; ?>"/></td>
						<td><label>L29</label><br/><input type="text" name="tf_tc_options[tf_tc_options_L29]" value="<?php echo isset($options['tf_tc_options_L29']) ? $options['tf_tc_options_L29'] : '0.33'; ?>" data-cell="L29" /></td>
						<td><label>M29</label><br/><input type="text" name="tf_tc_options[tf_tc_options_M29]" value="<?php echo isset($options['tf_tc_options_M29']) ? $options['tf_tc_options_M29'] : '0.21'; ?>" data-cell="M29" /></td>
						<td><label>N29</label><br/><input type="text" name="tf_tc_options[tf_tc_options_N29]" value="<?php echo isset($options['tf_tc_options_N29']) ? $options['tf_tc_options_N29'] : '0'; ?>" data-cell="N29" /></td>
						<td><label>O29</label><br/><input type="text" data-cell="O29"/><br/><input type="text" name="tf_tc_options[tf_tc_options_O29_formula]" class="FO29" value="<?php echo isset($options['tf_tc_options_O29_formula']) ? $options['tf_tc_options_O29_formula'] : 'L29+M29+N29'; ?>"/></td>
					</tr>
					<tr colspan="15">
						<td></td>
						<td></td>
						<td></td>					 
						<td><label>G30</label><br/><input type="text" name="tf_tc_options[tf_tc_options_G30]" value="<?php echo isset($options['tf_tc_options_G30']) ? $options['tf_tc_options_G30'] : 'SK'; ?>" data-cell="G30"/></td>
						<td><label>H30</label><br/><input type="text" name="tf_tc_options[tf_tc_options_H30]" value="<?php echo isset($options['tf_tc_options_H30']) ? $options['tf_tc_options_H30'] : '0.29'; ?>" data-cell="H30" /></td>
						<td><label>I30</label><br/><input type="text" name="tf_tc_options[tf_tc_options_I30]" value="<?php echo isset($options['tf_tc_options_I30']) ? $options['tf_tc_options_I30'] : '0.1475'; ?>" data-cell="I30" /></td>
						<td><label>J30</label><br/><input type="text" name="tf_tc_options[tf_tc_options_J30]" value="<?php echo isset($options['tf_tc_options_J30']) ? $options['tf_tc_options_J30'] : '0'; ?>" data-cell="J30" /></td>
						<td><label>K30</label><br/><input type="text" data-cell="K30" /><br/><input type="text" name="tf_tc_options[tf_tc_options_K30_formula]"  class="FK30" value="<?php echo isset($options['tf_tc_options_K30_formula']) ? $options['tf_tc_options_K30_formula'] : 'H30+I30+J30'; ?>"/></td>
						<td><label>L30</label><br/><input type="text" name="tf_tc_options[tf_tc_options_L30]" value="<?php echo isset($options['tf_tc_options_L30']) ? $options['tf_tc_options_L30'] : '0.33'; ?>" data-cell="L30" /></td>
						<td><label>M30</label><br/><input type="text" name="tf_tc_options[tf_tc_options_M30]" value="<?php echo isset($options['tf_tc_options_M30']) ? $options['tf_tc_options_M30'] : '0.1475'; ?>" data-cell="M30" /></td>
						<td><label>N30</label><br/><input type="text" name="tf_tc_options[tf_tc_options_N30]" value="<?php echo isset($options['tf_tc_options_N30']) ? $options['tf_tc_options_N30'] : '0'; ?>" data-cell="N30" /></td>
						<td><label>O30</label><br/><input type="text" data-formula="<?php echo $options['tf_tc_options_O30_formula']; ?>" data-cell="O30" /><br/><input type="text" name="tf_tc_options[tf_tc_options_O30_formula]"  class="FO30" value="<?php echo isset($options['tf_tc_options_O30_formula']) ? $options['tf_tc_options_O30_formula'] : 'L30+M30+N30'; ?>"/></td>
					</tr>
					<tr colspan="15">
						<td><small>Step 1: Income</small></td>
						<td><label>E33</label><br/><input  type="text" class="FE33" name="tf_tc_options[tf_tc_options_E33_formula]" value="<?php echo isset($options['tf_tc_options_E33_formula']) ? $options['tf_tc_options_E33_formula'] : 'D10'; ?>"/><br/>
						<small><input type="text" data-cell="E33" data-formula='<?php echo $options['tf_tc_options_E33_formula']; ?>'/></small></td>
						<td></td>
						<td><label>G31</label><br/><input type="text" name="tf_tc_options[tf_tc_options_G31]" value="<?php echo isset($options['tf_tc_options_G31']) ? $options['tf_tc_options_G31'] : 'MB'; ?>" data-cell="G31"/></td>
						<td><label>H31</label><br/><input type="text" name="tf_tc_options[tf_tc_options_H31]" value="<?php echo isset($options['tf_tc_options_H31']) ? $options['tf_tc_options_H31'] : '0.29'; ?>" data-cell="H31" /></td>
						<td><label>I31</label><br/><input type="text" name="tf_tc_options[tf_tc_options_I31]" value="<?php echo isset($options['tf_tc_options_I31']) ? $options['tf_tc_options_I31'] : '0.174'; ?>" data-cell="I31" /></td>
						<td><label>J31</label><br/><input type="text" name="tf_tc_options[tf_tc_options_J31]" value="<?php echo isset($options['tf_tc_options_J31']) ? $options['tf_tc_options_J31'] : '0.0'; ?>" data-cell="J31" /></td>
						<td><label>K31</label><br/><input type="text" data-cell="K31"  /><input type="text" name="tf_tc_options[tf_tc_options_K31_formula]"  class="FK31" value="<?php echo isset($options['tf_tc_options_K31_formula']) ? $options['tf_tc_options_K31_formula'] : 'H31+I31+J31'; ?>"/></td>
						<td><label>L31</label><br/><input type="text" name="tf_tc_options[tf_tc_options_L31]" value="<?php echo isset($options['tf_tc_options_L31']) ? $options['tf_tc_options_L31'] : '0.33'; ?>" data-cell="L31" /></td>
						<td><label>M31</label><br/><input type="text" name="tf_tc_options[tf_tc_options_M31]" value="<?php echo isset($options['tf_tc_options_M31']) ? $options['tf_tc_options_M31'] : '0.174'; ?>" data-cell="M31" /></td>
						<td><label>N31</label><br/><input type="text" name="tf_tc_options[tf_tc_options_N31]" value="<?php echo isset($options['tf_tc_options_N31']) ? $options['tf_tc_options_N31'] : '0.0'; ?>" data-cell="N31" /></td>
						<td><label>O31</label><br/><input type="text" data-cell="O31" /><br/><input type="text" name="tf_tc_options[tf_tc_options_O31_formula]" class="FO31" value="<?php echo isset($options['tf_tc_options_O31_formula']) ? $options['tf_tc_options_O31_formula'] : 'L31+M31+N31'; ?>"/></td>
					</tr>
					<tr colspan="15">
						<td><small>Step 2: Captial Gain</small></td>
						<td><label>E34</label><br/><input type="text" name="tf_tc_options[tf_tc_options_E34_formula]" class="FE34" value="<?php echo isset($options['tf_tc_options_E34_formula']) ? $options['tf_tc_options_E34_formula'] : 'D12*(0.5)'; ?>"/><br/>
						<small><input type="text" data-cell="E34" data-formula='<?php echo $options['tf_tc_options_E34_formula']; ?>'/></small></td>
						<td></td>
						<td><label>G32</label><br/><input type="text" name="tf_tc_options[tf_tc_options_G32]" value="<?php echo isset($options['tf_tc_options_G32']) ? $options['tf_tc_options_G32'] : 'ON'; ?>" data-cell="G32"/></td>
						<td><label>H32</label><br/><input type="text" name="tf_tc_options[tf_tc_options_H32]" value="<?php echo isset($options['tf_tc_options_H32']) ? $options['tf_tc_options_H32'] : '0.29'; ?>" data-cell="H32" /></td>
						<td><label>I32</label><br/><input type="text" name="tf_tc_options[tf_tc_options_I32]" value="<?php echo isset($options['tf_tc_options_I32']) ? $options['tf_tc_options_I32'] : '0.1116'; ?>" data-cell="I32" /></td>
						<td><label>J32</label><br/><input type="text" name="tf_tc_options[tf_tc_options_J32]" value="<?php echo isset($options['tf_tc_options_J32']) ? $options['tf_tc_options_J32'] : '0.0625'; ?>" data-cell="J32" /></td>
						<td><label>K32</label><br/><input type="text" data-cell="K32" /><br/><input type="text" name="tf_tc_options[tf_tc_options_K32_formula]"  class="FK32" value="<?php echo isset($options['tf_tc_options_K32_formula']) ? $options['tf_tc_options_K32_formula'] : 'H32+I32+J32'; ?>"/></td>
						<td><label>L32</label><br/><input type="text" name="tf_tc_options[tf_tc_options_L32]" value="<?php echo isset($options['tf_tc_options_L32']) ? $options['tf_tc_options_L32'] : '0.33'; ?>" data-cell="L32" /></td>
						<td><label>M32</label><br/><input type="text" name="tf_tc_options[tf_tc_options_M32]" value="<?php echo isset($options['tf_tc_options_M32']) ? $options['tf_tc_options_M32'] : '0.1116'; ?>" data-cell="M32" /></td>
						<td><label>N32</label><br/><input type="text" name="tf_tc_options[tf_tc_options_N32]" value="<?php echo isset($options['tf_tc_options_N32']) ? $options['tf_tc_options_N32'] : '0.0625'; ?>" data-cell="N32" /></td>
						<td><label>O32</label><br/><input type="text" data-cell="O32" /><br/><input type="text" name="tf_tc_options[tf_tc_options_O32_formula]"  class="FO32" value="<?php echo isset($options['tf_tc_options_O32_formula']) ? $options['tf_tc_options_O32_formula'] : 'L32+M32+N32'; ?>"/></td>
					</tr>
					<tr colspan="15">
						<td><small>Step 3: RRSP / Other Deductions</small></td>
						<td><label>E35</label><br/><input type="text" name="tf_tc_options[tf_tc_options_E35_formula]" class="FE35" value="<?php echo isset($options['tf_tc_options_E35_formula']) ? $options['tf_tc_options_E35_formula'] : 'D14*(-1)'; ?>"/><br/>
						<small><input type="text" data-cell="E35" data-formula='<?php echo $options['tf_tc_options_E35_formula']; ?>'/></small></td>
						<td></td>
						<td><label>G33</label><br/><input type="text" name="tf_tc_options[tf_tc_options_G33]" value="<?php echo isset($options['tf_tc_options_G33']) ? $options['tf_tc_options_G33'] : 'QC'; ?>" data-cell="G33"/></td>
						<td><label>H33</label><br/><input type="text" name="tf_tc_options[tf_tc_options_H33]" value="<?php echo isset($options['tf_tc_options_H33']) ? $options['tf_tc_options_H33'] : '0.2422'; ?>" data-cell="H33" /></td>
						<td><label>I33</label><br/><input type="text" name="tf_tc_options[tf_tc_options_I33]" value="<?php echo isset($options['tf_tc_options_I33']) ? $options['tf_tc_options_I33'] : '0.24'; ?>" data-cell="I33" /></td>
						<td><label>J33</label><br/><input type="text" name="tf_tc_options[tf_tc_options_J33]" value="<?php echo isset($options['tf_tc_options_J33']) ? $options['tf_tc_options_J33'] : '0'; ?>" data-cell="J33" /></td>
						<td><label>K33</label><br/><input type="text" data-cell="K33" /><br/><input type="text" name="tf_tc_options[tf_tc_options_K33_formula]"  class="FK33" value="<?php echo isset($options['tf_tc_options_K33_formula']) ? $options['tf_tc_options_K33_formula'] : 'H33+I33+J33'; ?>"/></td>
						<td><label>L33</label><br/><input type="text" name="tf_tc_options[tf_tc_options_L33]" value="<?php echo isset($options['tf_tc_options_L33']) ? $options['tf_tc_options_L33'] : '0.2756'; ?>" data-cell="L33" /></td>
						<td><label>M33</label><br/><input type="text" name="tf_tc_options[tf_tc_options_M33]" value="<?php echo isset($options['tf_tc_options_M33']) ? $options['tf_tc_options_M33'] : '0.24'; ?>" data-cell="M33" /></td>
						<td><label>N33</label><br/><input type="text" name="tf_tc_options[tf_tc_options_N33]" value="<?php echo isset($options['tf_tc_options_N33']) ? $options['tf_tc_options_N33'] : '0'; ?>" data-cell="N33" /></td>
						<td><label>O33</label><br/><input type="text" data-cell="O33" /><input type="text" name="tf_tc_options[tf_tc_options_O33_formula]"  class="FO33" value="<?php echo isset($options['tf_tc_options_O33_formula']) ? $options['tf_tc_options_O33_formula'] : 'L33+M33+N33'; ?>"/></td>
					</tr>
					<tr colspan="15">
						<td><small>Taxable Income</small></td>
						<td><label>E36</label><br/><input type="text" name="tf_tc_options[tf_tc_options_E36_formula]" class="FE36" value="<?php echo isset($options['tf_tc_options_E36_formula']) ? $options['tf_tc_options_E36_formula'] : 'E33+E34+E35'; ?>"/><br/>
						<small><input type="text" data-cell="E36" data-formula='<?php echo $options['tf_tc_options_E36_formula']; ?>'/></small></td>
						<td></td>
						<td><label>G34</label><br/><input type="text" name="tf_tc_options[tf_tc_options_G34]" value="<?php echo isset($options['tf_tc_options_G34']) ? $options['tf_tc_options_G34'] : 'NS'; ?>" data-cell="G34"/></td>
						<td><label>H34</label><br/><input type="text" name="tf_tc_options[tf_tc_options_H34]" value="<?php echo isset($options['tf_tc_options_H34']) ? $options['tf_tc_options_H34'] : '0.29'; ?>" data-cell="H34" /></td>
						<td><label>I34</label><br/><input type="text" name="tf_tc_options[tf_tc_options_I34]" value="<?php echo isset($options['tf_tc_options_I34']) ? $options['tf_tc_options_I34'] : '0.21'; ?>" data-cell="I34" /></td>
						<td><label>J34</label><br/><input type="text" name="tf_tc_options[tf_tc_options_J34]" value="<?php echo isset($options['tf_tc_options_J34']) ? $options['tf_tc_options_J34'] : '0.0'; ?>" data-cell="J34" /></td>
						<td><label>K34</label><br/><input type="text" data-cell="K34" /><br/>
						<input type="text" name="tf_tc_options[tf_tc_options_K34_formula]"  class="FK34" value="<?php echo isset($options['tf_tc_options_K34_formula']) ? $options['tf_tc_options_K34_formula'] : 'H34+I34+J34'; ?>"/></td>
						<td><label>L34</label><br/><input type="text" name="tf_tc_options[tf_tc_options_L34]" value="<?php echo isset($options['tf_tc_options_L34']) ? $options['tf_tc_options_L34'] : '0.33'; ?>" data-cell="L34" /></td>
						<td><label>M34</label><br/><input type="text" name="tf_tc_options[tf_tc_options_M34]" value="<?php echo isset($options['tf_tc_options_M34']) ? $options['tf_tc_options_M34'] : '0.21'; ?>" data-cell="M34" /></td>
						<td><label>N34</label><br/><input type="text" name="tf_tc_options[tf_tc_options_N34]" value="<?php echo isset($options['tf_tc_options_N34']) ? $options['tf_tc_options_N34'] : '0'; ?>" data-cell="N34" /></td>
						<td><label>O34</label><br/><input type="text" data-cell="O34" />
						<input type="text" name="tf_tc_options[tf_tc_options_O34_formula]"  class="FO34" value="<?php echo isset($options['tf_tc_options_O34_formula']) ? $options['tf_tc_options_O34_formula'] : 'L34+M34+N34'; ?>"/>
						</td>
					</tr>
				</table>
				<!-- Table 2 working -->
				<table class="form-table tax-table-3">
					<tr>
						<td colspan="2"><label data-cell="B41" data-formula='CONCATENATE("Taxable income subject to highest tax rate of ",Q73)'></label></td>
						<td></td>
						<td></td>
						<td>E</td>
						<td><label>J41</label><br/><input type="text" class="FJ41" name="tf_tc_options[tf_tc_options_J41_formula]" value="<?php echo isset($options['tf_tc_options_J41_formula']) ? $options['tf_tc_options_J41_formula'] : 'S73' ?>"/><br/><input type="text" data-cell="J41"/></td>
					</tr>
					<tr>
						<td colspan="2"><label data-cell="B42" data-formula='CONCATENATE("Taxable income subject to 2nd highest tax rate of ",Q73)'></label></td>
						<td></td>
						<td></td>
						<td>F</td>
						<td><label>J42</label><br/><input type="text" class="FJ42" name="tf_tc_options[tf_tc_options_J42_formula]" value="<?php echo isset($options['tf_tc_options_J42_formula']) ? $options['tf_tc_options_J42_formula'] : 'S74' ?>"/><br/><input type="text" data-cell="J42"/></td>
					</tr>
					<tr>
						<td colspan="2"><label data-formula='IF(D8="AB",CONCATENATE("Taxable income subject to tax rates of ",Q76," and ",Q75),IF(D8="ON",CONCATENATE("Taxable income subject to tax rates of ",Q76," and ",Q75),CONCATENATE("Taxable income subject to tax rate of ",Q75)))'></label></td>
						<td><label>F43</label><br/><input class="FF43" name="tf_tc_options[tf_tc_options_F43_formula]" value='<?php echo isset($options["tf_tc_options_F43_formula"]) ? $options["tf_tc_options_F43_formula"] : 'IF(S73+S74+S75+S76>=1000," ","Insufficient income")' ?>'/><br/><input type="text" data-cell="F43"/></td>
						<td><label>G43</label><br/><input type="text" class="FG43" name="tf_tc_options[tf_tc_options_G43_formula]" value='<?php echo isset($options['tf_tc_options_G43_formula']) ? $options['tf_tc_options_G43_formula'] : 'IF(S73+S74+S75+S76>=5000," ","Insufficient income")' ?>'/><br/><input data-cell="G43"/></td>
						<td>G</td>
						<td><label>J43</label><br/><input type="text" class="FJ43" name="tf_tc_options[tf_tc_options_J43_formula]" value='<?php echo isset($options['tf_tc_options_J43_formula']) ? $options['tf_tc_options_J43_formula'] : 'IF(D8="AB",S75+S76,IF(D8="ON",S75+S76,S75))' ?>'/><br/><input type="text" data-cell="J43"/></td>
					</tr>
					<tr>
						<th></th>
						<th colspan="2">Your Estimated Tax Savings</th>
					</tr>
					<tr>
						<td></td>
						<td><label>C46</label><br/><input class="FC46" name="tf_tc_options[tf_tc_options_C46_formula]" value='<?php echo isset($options["tf_tc_options_C46_formula"]) ? $options["tf_tc_options_C46_formula"] : 'IF(F43="Insufficient income","Insufficient ","Invest")' ?>'/><br/><input type="text" data-cell="C46"/></td>
						<td><label>D46</label><br/><input class="FD46" name="tf_tc_options[tf_tc_options_D46_formula]" value='<?php echo isset($options["tf_tc_options_D46_formula"]) ? $options["tf_tc_options_D46_formula"] : 'IF(G43="Insufficient income","Insufficient ","Invest")' ?>'/><br/><input type="text" data-cell="D46"/></td>
						<td><label>E46</label><br/><input class="FE46" name="tf_tc_options[tf_tc_options_E46_formula]" value='<?php echo isset($options["tf_tc_options_E46_formula"]) ? $options["tf_tc_options_E46_formula"] : 'IF(I60=" "," ","Invest")' ?>'/><br/><input type="text" data-cell="E46"/></td>
					</tr>
					<tr>
						<td>Investment</td>
						<td><label>C47</label><br/><input class="FC47" name="tf_tc_options[tf_tc_options_C47_formula]" value='<?php echo isset($options["tf_tc_options_C47_formula"]) ? $options["tf_tc_options_C47_formula"] : 'IF(F43="Insufficient income"," Income","1000")' ?>'/><br/><input type="text" data-cell="C47"/></td>
						<td><label>D47</label><br/><input class="FD47" name="tf_tc_options[tf_tc_options_D47_formula]" value='<?php echo isset($options["tf_tc_options_D47_formula"]) ? $options["tf_tc_options_D47_formula"] : 'IF(G43="Insufficient income"," Income","5000")' ?>'/><br/><input type="text" data-cell="D47"/></td>
						<td><label>E47</label><br/><input class="FE47" name="tf_tc_options[tf_tc_options_E47_formula]" value='<?php echo isset($options["tf_tc_options_E47_formula"]) ? $options["tf_tc_options_E47_formula"] : 'IF(D48<5000," ",H61)' ?>'/><br/><input type="text" data-cell="E47"/></td>
					</tr>
					<tr>
						<td>Investment - $</td>
						<td><label>C48</label><br/><input class="FC48" name="tf_tc_options[tf_tc_options_C48_formula]" value='<?php echo isset($options["tf_tc_options_C48_formula"]) ? $options["tf_tc_options_C48_formula"] : 'MAX(IF(S73+S74+S75+S76>=1000,1000,0))' ?>'/><br/><input type="text" data-cell="C48"/></td>
						<td><label>D48</label><br/><input class="FD48" name="tf_tc_options[tf_tc_options_D48_formula]" value='<?php echo isset($options["tf_tc_options_D48_formula"]) ? $options["tf_tc_options_D48_formula"] : 'MAX(IF(S73+S74+S75+S76>=5000,5000,0))' ?>'/><br/><input type="text" data-cell="D48"/></td>
						<td><label>E48</label><br/><input class="FE48" name="tf_tc_options[tf_tc_options_E48_formula]" value='<?php echo isset($options["tf_tc_options_E48_formula"]) ? $options["tf_tc_options_E48_formula"] : 'IF(G61<5000," ",IF(V77=0," ",V77))' ?>'/><br/><input type="text" data-cell="E48"/></td>
					</tr>
					<tr>
						<td>Tax Rate</td>
						<td><label>C49</label><br/><input class="FC49" name="tf_tc_options[tf_tc_options_C49_formula]" value='<?php echo isset($options["tf_tc_options_C49_formula"]) ? $options["tf_tc_options_C49_formula"] : 'IF(E36-C48>=M73,Q73,IF(E36-C48>=M74,Q74,IF(E36-C48>=M75,Q75,IF(D8="BC",0,IF(D8="SK",0,IF(D8="MB",0,IF(D8="QC",0,IF(D8="NS",0,(IF((OR(D8="AB",D8="ON")),IF(E36-C48>=M76,Q76,0)))))))))))' ?>'/><br/><input type="text" data-cell="C49"/></td>
						<td><label>D49</label><br/><input class="FD49" name="tf_tc_options[tf_tc_options_D49_formula]" value='<?php echo isset($options["tf_tc_options_D49_formula"]) ? $options["tf_tc_options_D49_formula"] : 'IF(E36-D48>=M73,Q73,IF(E36-D48>=M74,Q74,IF(E36-D48>=M75,Q75,IF(D8="BC",0,IF(D8="SK",0,IF(D8="MB",0,IF(D8="QC",0,IF(D8="NS",0,(IF((OR(D8="AB",D8="ON")),IF(E36-D48>=M76,Q76,0)))))))))))' ?>'/><br/><input type="text" data-cell="D49"/></td>
						<td><label>E49</label><br/><input class="FE49" name="tf_tc_options[tf_tc_options_E49_formula]" value='<?php echo isset($options['tf_tc_options_E49_formula']) ? $options['tf_tc_options_E49_formula'] : 'IF(D48<5000," ",IF(H61=" "," ",Z77/H61))' ?>'/><br/><input type="text" data-cell="E49"/></td>
					</tr>
					<tr>
						<td>Tax Savings</td>
						<td><label>C50</label><br/><input class="FC50" name="tf_tc_options[tf_tc_options_C50_formula]" value='<?php echo isset($options["tf_tc_options_C50_formula"]) ? $options["tf_tc_options_C50_formula"] : 'IF(C47=" ","-",C48*C49)' ?>'/><br/><input type="text" data-cell="C50"/></td>
						<td><label>D50</label><br/><input class="FD50" name="tf_tc_options[tf_tc_options_D50_formula]" value='<?php echo isset($options["tf_tc_options_D50_formula"]) ? $options["tf_tc_options_D50_formula"] : 'IF(D47=" ","-",D48*D49)' ?>'/><br/><input type="text" data-cell="D50"/></td>
						<td><label>E50</label><br/><input class="FE50" name="tf_tc_options[tf_tc_options_E50_formula]" value='<?php echo isset($options["tf_tc_options_E50_formula"]) ? $options["tf_tc_options_E50_formula"] : 'IF(D48<5000," ",IF(H61=" "," ",Z77))' ?>'/><br/><input type="text" data-cell="E50"/></td>
					</tr>
					<tr>
						<td>Your Cost - $</td>
						<td><label>C51</label><br/><input class="FC51" name="tf_tc_options[tf_tc_options_C51_formula]" value='<?php echo isset($options["tf_tc_options_C51_formula"]) ? $options["tf_tc_options_C51_formula"] : 'C48-C50' ?>'/><br/><input type="text" data-cell="C51"/></td>
						<td><label>D51</label><br/><input class="FD51" name="tf_tc_options[tf_tc_options_D51_formula]" value='<?php echo isset($options["tf_tc_options_D51_formula"]) ? $options["tf_tc_options_D51_formula"] : 'IF(G43="Insufficient income"," Income","5000")' ?>'/><br/><input type="text" data-cell="D51"/></td>
						<td><label>E51</label><br/><input class="FE51" name="tf_tc_options[tf_tc_options_E51_formula]" value='<?php echo isset($options["tf_tc_options_E51_formula"]) ? $options["tf_tc_options_E51_formula"] : 'IF(H61=" "," ",H61-H63)' ?>'/><br/><input type="text" data-cell="E51"/></td>
					</tr>
					
					
					<tr>
						<td>ITC Savings</td>
						<td><label>C52</label><br/><input class="FC52" name="tf_tc_options[tf_tc_options_C52_formula]" value='<?php echo isset($options["tf_tc_options_C52_formula"]) ? $options["tf_tc_options_C52_formula"] : 'IF(F43="Insufficient Income",0,C47*0.9*0.35*0.15*(1-C49))' ?>'/><br/><input type="text" data-cell="C52"/></td>
						<td><label>D52</label><br/><input class="FD52" name="tf_tc_options[tf_tc_options_D52_formula]" value='<?php echo isset($options["tf_tc_options_D52_formula"]) ? $options["tf_tc_options_D52_formula"] : 'IF(G43="Insufficient Income",0,D47*0.9*0.35*0.15*(1-D49))' ?>'/><br/><input type="text" data-cell="D52"/></td>
						<td><label>E52</label><br/><input class="FE52" name="tf_tc_options[tf_tc_options_E52_formula]" value='<?php echo isset($options["tf_tc_options_E52_formula"]) ? $options["tf_tc_options_E52_formula"] : 'IF(D48<5000," ",IF(E47=" "," ",E47*(0.9)*(0.35)*(0.15)*(1-E49)))' ?>'/><br/><input type="text" data-cell="E52"/></td>
					</tr>
					<tr>
						<td><label>B53</label><br/><input class="FB53" name="tf_tc_options[tf_tc_options_B53_formula]" value='<?php echo isset($options['tf_tc_options_B53_formula']) ? $options['tf_tc_options_B53_formula'] : 'IF(D18="YES - Contribute to RRSP","RRSP savings",IF(D18="YES - Donate","Donation savings ", "RRSP/Donate"))'; ?>'/><input data-cell="B53" ></td>
						<td><label>C53</label><br/><input class="FC53" name="tf_tc_options[tf_tc_options_C53_formula]" value='<?php echo isset($options["tf_tc_options_C53_formula"]) ? $options["tf_tc_options_C53_formula"] : 'IF(F43="Insufficient income","-",IF(D18="NO","-",IF(D18="YES - Contribute to RRSP",IF(F43="Insufficient income",0,1000*C49*D22),IF(AND(E36>=202801,D18="YES - Donate"),IF(D8="BC",1000*O28*D22,IF(D8="AB",1000*O29*D22,IF(D8="SK",1000*O30*D22,IF(D8="MB",1000*O31*D22,IF(D8="ON",1000*O32*D22,IF(D8="QC",1000*O33*D22,IF(D8="NS",1000*O34*D22))))))),IF(D8="BC",1000*K28*D22,IF(D8="AB",1000*K29*D22,IF(D8="SK",1000*K30*D22,IF(D8="MB",1000*K31*D22,IF(D8="ON",1000*K32*D22,IF(D8="QC",1000*K33*D22,IF(D8="NS",1000*K34*D22)))))))))))' ?>'/><br/><input type="text" data-cell="C53"/></td>
						<td><label>D53</label><br/><input class="FD53" name="tf_tc_options[tf_tc_options_D53_formula]" value='<?php echo isset($options["tf_tc_options_D53_formula"]) ? $options["tf_tc_options_D53_formula"] : 'IF(G43="Insufficient income","-",IF(D18="NO","-",IF(D18="YES - Contribute to RRSP",IF(G43="Insufficient income",0,5000*D49*D22),IF(AND(E36>=202801,D18="YES - Donate"),IF(D8="BC",5000*O28*D22,IF(D8="AB",5000*O29*D22,IF(D8="SK",5000*O30*D22,IF(D8="MB",5000*O31*D22,IF(D8="ON",5000*O32*D22,IF(D8="QC",5000*O33*D22,IF(D8="NS",5000*O34*D22))))))),IF(D8="BC",5000*K28*D22,IF(D8="AB",5000*K29*D22,IF(D8="SK",5000*K30*D22,IF(D8="MB",5000*K31*D22,IF(D8="ON",5000*K32*D22,IF(D8="QC",5000*K33*D22,IF(D8="NS",5000*K34*D22)))))))))))' ?>'/><br/><input type="text" data-cell="D53"/></td>
						<td><label>E53</label><br/><input class="FE53" name="tf_tc_options[tf_tc_options_E53_formula]" value='<?php echo isset($options["tf_tc_options_E53_formula"]) ? $options["tf_tc_options_E53_formula"] : 'IF(E36<=0," ",IF(G61<5000," ",IF(D18="NO","-",IF(D18="YES - Contribute to RRSP",IF(H59="Insufficient income",0,I60*H62*D22),IF(AND(E36>=202801,D18="YES - Donate"),IF(D8="BC",I60*O28*D22,IF(D8="AB",I60*O29*D22,IF(D8="SK",I60*O30*D22,IF(D8="MB",I60*O31*D22,IF(D8="ON",I60*O32*D22,IF(D8="QC",I60*O33*D22,IF(D8="NS",I60*O34*D22))))))),IF(D8="BC",I60*K28*D22,IF(D8="AB",I60*K29*D22,IF(D8="SK",I60*K30*D22,IF(D8="MB",I60*K31*D22,IF(D8="ON",I60*K32*D22,IF(D8="QC",I60*K33*D22,IF(D8="NS",I60*K34*D22))))))))))))' ?>'/><br/><input type="text" data-cell="E53"/></td>
					</tr>
					<tr>
						<td><label>B54</label><br/><input class="FB54" name="tf_tc_options[tf_tc_options_B54_formula]" value='<?php echo isset($options['tf_tc_options_B54_formula']) ? $options['tf_tc_options_B54_formula'] : 'IF(D18="YES - Contribute to RRSP","CG Tax on transfer",IF(D18="YES - Donate","CG Tax on Donation", "        -"))'; ?>'/><br/><input data-cell="B54" /></td>
						<td><label>C54</label><br/><input class="FC54" name="tf_tc_options[tf_tc_options_C54_formula]" value='<?php echo isset($options["tf_tc_options_C54_formula"]) ? $options["tf_tc_options_C54_formula"] : 'IF(D18="NO","-",IF(D20="YES",0,IF(D18="YES - Contribute to RRSP",IF(F43="Insufficient income","-",1000*D22*0.5*C49*-1),IF(D18="YES - Donate",IF(F43="Insufficient income","-",1000*D22*0.5*C49*-1),0))))' ?>'/><br/><input type="text" data-cell="C54"/></td>
						<td><label>D54</label><br/><input class="FD54" name="tf_tc_options[tf_tc_options_D54_formula]" value='<?php echo isset($options["tf_tc_options_D54_formula"]) ? $options["tf_tc_options_D54_formula"] : 'IF(D18="NO","-",IF(D20="YES",0,IF(D18="YES - Contribute to RRSP",IF(G43="Insufficient income","-",5000*D22*0.5*D49*-1),IF(D18="YES - Donate",IF(G43="Insufficient income","-",5000*D22*0.5*D49*-1),0))))' ?>'/><br/><input type="text" data-cell="D54"/></td>
						<td><label>E54</label><br/><input class="FE54" name="tf_tc_options[tf_tc_options_E54_formula]" value='<?php echo isset($options["tf_tc_options_E54_formula"]) ? $options["tf_tc_options_E54_formula"] : 'IF(E36<=0," ",IF(G61<5000," ",IF(D18="NO","-",IF(D18="NO"," ",IF(D20="YES",0,IF(D18="YES - Contribute to RRSP",IF(G61<5000," ",IF(H59="Insufficient income",0,I60*D22*0.5*H62*-1)),IF(D18="YES - Donate",IF(D48<5000," ",IF(H59="Insufficient income",0,I60*D22*0.5*H62*-1)),0)))))))' ?>'/><br/><input type="text" data-cell="E54"/></td>
					</tr>
					<tr>
						<td>Total Tax Savings</td>
						<td><label>C55</label><br/><input class="FC55" name="tf_tc_options[tf_tc_options_C55_formula]" value='<?php echo isset($options["tf_tc_options_C55_formula"]) ? $options["tf_tc_options_C55_formula"] : '(IF((OR(C53="-",C54="-")),C50+C52,C50+C52+C53+C54))' ?>'/><br/><input type="text" data-cell="C55"/></td>
						<td><label>D55</label><br/><input class="FD55" name="tf_tc_options[tf_tc_options_D55_formula]" value='<?php echo isset($options["tf_tc_options_D55_formula"]) ? $options["tf_tc_options_D55_formula"] : '(IF((OR(D53="-",D54="-")),D50+D52,D50+D52+D53+D54))' ?>'/><br/><input type="text" data-cell="D55"/></td>
						<td><label>E55</label><br/><input class="FE55" name="tf_tc_options[tf_tc_options_E55_formula]" value='<?php echo isset($options["tf_tc_options_E55_formula"]) ? $options["tf_tc_options_E55_formula"] : 'IF(E50=" "," ",(IF((OR(E53="-",E54>0)),E50+E52,E50+E52+E53+E54)))' ?>'/><br/><input type="text" data-cell="E55"/></td>
					</tr>
				</table>
				<!-- Table 3 working -->
				<table class="form-table tax-table-4">
					<tr>
						<td colspan="2">Your estimated tax savings:</td>
						<td></td>
						<td></td>
						<td><label>G59</label><br/><input type="text" class="FG59" value='<?php echo isset($options['tf_tc_options_G59_formula']) ? $options['tf_tc_options_G59_formula'] : 'IF(S77>=10000," ","Insufficient income")' ?>' name="tf_tc_options[tf_tc_options_G59_formula]"/><br/><input type="text" data-cell="G59"/></td>
						<td><label>H59</label><br/><input type="text" class="FH59" value='<?php echo isset($options['tf_tc_options_H59_formula']) ? $options['tf_tc_options_H59_formula'] : 'IF(G61<10000," ",IF(H61=" "," "," "))' ?>' name="tf_tc_options[tf_tc_options_H59_formula]"/><br/><input type="text" data-cell="H59"/></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td>A</td>
						<td><label>G60</label><br/><input class="FG60" name="tf_tc_options[tf_tc_options_G60_formula]" value='<?php echo isset($options['tf_tc_options_G60_formula']) ? $options['tf_tc_options_G60_formula'] : 'IF(G59="Insufficient income",0,"Invest $10,000")' ?>'/> <input type="text" data-cell="G60"/></td>
						<td><label>H60</label><br/><input class="FH60" name="tf_tc_options[tf_tc_options_H60_formula]" value='<?php echo isset($options['tf_tc_options_H60_formula']) ? $options['tf_tc_options_H60_formula'] : 'IF(I60=" "," ","Invest")' ?>'/><input type="text" data-cell="H60"/></td>
						<td><label>I60</label><br/><input class="FI60" name="tf_tc_options[tf_tc_options_I60_formula]" value='<?php echo isset($options['tf_tc_options_I60_formula']) ? $options['tf_tc_options_I60_formula'] : 'IF(G61<10000," ",H61)' ?>'/><input data-cell="I60"/></td>						
					</tr>
					<tr>
						<td>Investment - $</td>
						<td></td>
						<td></td>
						<td></td>
						<td><label>G61</label><br/><input type="text" class="FG61" name="tf_tc_options[tf_tc_options_G61_formula]"<input type="text" data-cell="G61" value="<?php echo isset($options['tf_tc_options_G61_formula']) ? $options['tf_tc_options_G61_formula'] : 'MAX(IF(S73+S74+S75+S76>=10000,10000,0))' ?>"/><input data-cell="G61"/></td>
						<td colspan="2"><label>H61</label><br/><input type="text" class="FH61" name="tf_tc_options[tf_tc_options_H61_formula]" value='<?php echo isset($options['tf_tc_options_H61_formula']) ? $options['tf_tc_options_H61_formula'] : 'IF(G61<10000," ",IF(V77=0," ",V77))' ?>' style="width:100%;"/><br/><input data-cell="H61"  style="width:100%;"/></td>						
					</tr>
					<tr>
						<td>Tax Rate - %</td>
						<td></td>
						<td></td>
						<td>B</td>
						<td><label>G62</label><br/><input type="text" class="FG62" name="tf_tc_options[tf_tc_options_G62_formula]" value='<?php echo isset($options['tf_tc_options_G62_formula']) ? $options['tf_tc_options_G62_formula'] : 'IF(G59="Insufficient income",0,IF(E36-G61>=M73,Q73,IF(E36-G61>=M74,Q74,IF(E36-G61>=M75,Q75,IF(D8="BC",0,IF(D8="SK",0,IF(D8="MB",0,IF(D8="QC",0,IF(D8="NS",0,(IF((OR(D8="AB",D8="ON")),IF(E36-G61>=M76,Q76,0))))))))))))' ?>'/><br/><input data-cell="G62"/></td>
						<td colspan="2"><label>H62</label><br/><input type="text" class="FH62" name="tf_tc_options[tf_tc_options_H62_formula]" value='<?php echo isset($options['tf_tc_options_H62_formula']) ? $options['tf_tc_options_H62_formula'] : 'IF(G61<10000," ",IF(H61=" "," ",Z77/H61))' ?>' style="width:100%"/><br/><input type="text" data-cell="H62" style="width:100%"/></td>						
					</tr>
					<tr>
						<td>Tax savings - Terra LP</td>
						<td></td>
						<td></td>
						<td>C = A x B</td>
						<td><label>G63</label><br/><input type="text" class="FG63" name="tf_tc_options[tf_tc_options_G63_formula]" value='<?php echo isset($options['tf_tc_options_G63_formula']) ? $options['tf_tc_options_G63_formula'] : 'G61*G62' ?>'/><input type="text" data-cell="G63"/></td>
						<td colspan="2"><label>H63</label><br/><input type="text" class="FH63" name="tf_tc_options[tf_tc_options_H63_formula]" value='<?php echo isset($options['tf_tc_options_H63_formula']) ? $options['tf_tc_options_H63_formula'] : 'IF(G61<10000," ",IF(H61=" "," ",Z77))' ?>' style="width:100%"/><br/><input type="text" data-cell="H63" style="width:100%"/></td>						
					</tr>
					<tr>
						<td>Your Cost - $</td>
						<td></td>
						<td></td>
						<td>A - C</td>
						<td><label>G64</label><br/><input class="FG64" name="tf_tc_options[tf_tc_options_G64_formula]" type="text" value='<?php echo isset($options['tf_tc_options_G64_formula']) ? $options['tf_tc_options_G64_formula'] : 'G61-G63' ?>'/><br/><input type="text" data-cell="G64"/></td>
						<td colspan="2"><label>H64</label><br/><input type="text" class="FH64" name="tf_tc_options[tf_tc_options_H64_formula]" value='<?php echo isset($options['tf_tc_options_H64_formula']) ? $options['tf_tc_options_H64_formula'] : 'IF(H61=" "," ",H61-H63)' ?>' style="width:100%"/><br/><input type="text" data-cell="H64" style="width:100%"/></td>
					</tr>
					<tr>
						<td><input type="text" class="FB65" name="tf_tc_options[tf_tc_options_B65_formula]" value='<?php echo isset($options['tf_tc_options_B65_formula']) ? $options['tf_tc_options_B65_formula'] : 'IF(D18="YES - Contribute to RRSP","Tax savings on transfer to RRSP",IF(D18="YES - Donate","Tax savings on Donation", "        -"))' ?>' style="width:100%"/><br/><label data-cell="B65"></label></td>
						<td></td>
						<td></td>
						<td><label data-cell="E65" data-formula='IF(D18="YES - Contribute to RRSP","D",IF(D18="YES - Donate","D", "-"))'></label></td>
						<td><label>G65</label><br/><input type="text" class="FG65" name="tf_tc_options[tf_tc_options_G65_formula]" value='<?php echo isset($options['tf_tc_options_G65_formula']) ? $options['tf_tc_options_G65_formula'] : 'IF(G59="Insufficient income","-",IF(D18="NO","-",IF(D18="YES - Contribute to RRSP",IF(G43="Insufficient income",0,10000*D49*D22),IF(AND(E36>=202801,D18="YES - Donate"),IF(D8="BC",10000*O28*D22,IF(D8="AB",10000*O29*D22,IF(D8="SK",10000*O30*D22,IF(D8="MB",10000*O31*D22,IF(D8="ON",10000*O32*D22,IF(D8="QC",10000*O33*D22,IF(D8="NS",10000*O34*D22))))))),IF(D8="BC",10000*K28*D22,IF(D8="AB",10000*K29*D22,IF(D8="SK",10000*K30*D22,IF(D8="MB",10000*K31*D22,IF(D8="ON",10000*K32*D22,IF(D8="QC",10000*K33*D22,IF(D8="NS",10000*K34*D22)))))))))))' ?>'/><br/><input type="text" data-cell="G65"/></td>
						<td colspan="2"><label>H65</label><br/><input type="text" class="FH65" name="tf_tc_options[tf_tc_options_H65_formula]" value='<?php echo isset($options['tf_tc_options_H65_formula']) ? $options['tf_tc_options_H65_formula'] : 'IF(E36<=0," ",IF(G61<10000," ",IF(D18="NO","-",IF(D18="YES - Contribute to RRSP",IF(H59="Insufficient income",0,I60*H62*D22),IF(AND(E36>=202801,D18="YES - Donate"),IF(D8="BC",I60*O28*D22,IF(D8="AB",I60*O29*D22,IF(D8="SK",I60*O30*D22,IF(D8="MB",I60*O31*D22,IF(D8="ON",I60*O32*D22,IF(D8="QC",I60*O33*D22,IF(D8="NS",I60*O34*D22))))))),IF(D8="BC",I60*K28*D22,IF(D8="AB",I60*K29*D22,IF(D8="SK",I60*K30*D22,IF(D8="MB",I60*K31*D22,IF(D8="ON",I60*K32*D22,IF(D8="QC",I60*K33*D22,IF(D8="NS",I60*K34*D22))))))))))))' ?>' style="width:100%"/><br/><input type="text" data-cell="H65" style="width:100%"/></td>						
					</tr>
					<tr>
						<td><input type="text" class="FB66" name="tf_tc_options[tf_tc_options_B66_formula]" value='<?php echo isset($options['tf_tc_options_B66_formula']) ? $options['tf_tc_options_B66_formula'] :'IF(D18="YES - Contribute to RRSP","Capital gains tax on transfer to RRSP",IF(D18="YES - Donate","Capital gains tax on Donation", "        -"))'?>'/><br/><label data-cell="B66"></label></td>
						<td></td>
						<td></td>
						<td><label data-cell="E66" data-formula='IF(D18="YES - Contribute to RRSP","E",IF(D18="YES - Donate","E", "-"))'></label></td>
						<td><label>G66</label><br/><input type="text" class="FG66" name="tf_tc_options[tf_tc_options_G66_formula]" value='<?php echo isset($options['tf_tc_options_G66_formula']) ? $options['tf_tc_options_G66_formula'] : 'IF(D18="NO","-",IF(D20="YES",0,IF(D18="YES - Contribute to RRSP",IF(G59="Insufficient income","-",10000*D22*0.5*G62*-1),IF(D18="YES - Donate",IF(G59="Insufficient income","-",10000*D22*0.5*G62*-1),0))))' ?>'/><br/><input type="text" data-cell="G66"></td>
						<td colspan="2"><label>H66</label><br/><input type="text" class="FH66" name="tf_tc_options[tf_tc_options_H66_formula]" value='<?php echo isset($options['tf_tc_options_H66_formula']) ? $options['tf_tc_options_H66_formula'] : 'IF(E36<=0," ",IF(G61<10000," ",IF(D18="NO","-",IF(D18="NO"," ",IF(D20="YES",0,IF(D18="YES - Contribute to RRSP",IF(G61<10000," ",IF(H59="Insufficient income",0,I60*D22*0.5*H62*-1)),IF(D18="YES - Donate",IF(G61<10000," ",IF(H59="Insufficient income",0,I60*D22*0.5*H62*-1)),0)))))))' ?>' style="width:100%"/><br/><input type="text" data-cell="H66" style="width:100%"/></td>						
					</tr>
					<tr>
						<td>Total Tax Savings</td>
						<td></td>
						<td></td>
						<td>F = C+D+E</td>
						<td><label>G67</label><br/><input type="text" class="FG67" name="tf_tc_options[tf_tc_options_G67_formula]" value='<?php echo isset($options['tf_tc_options_G67_formula']) ? $options['tf_tc_options_G67_formula'] : 'G61*G62' ?>'/><input type="text" data-cell="G67"></td>
						<td colspan="2"><label>H67</label><br/><input type="text" class="FH67" name="tf_tc_options[tf_tc_options_H67_formula]" value='<?php echo isset($options['tf_tc_options_H67_formula']) ? $options['tf_tc_options_H67_formula'] : 'IF(G61<10000," ",IF(H61=" "," ",Z77))' ?>' style="width:100%"/><br/><input type="text" data-cell="H67" style="width:100%"/></td>						
					</tr>
					<tr>
						<th colspan="10">Tax Rates for standard income ranges (note we are only interest in the top 3 (BC, SK, MB, QC, NS) or top 4 (AB, ON) income ranges depending on the province.</th>
					</tr>
					<tr>
						<td>Province</td>
						<td></td>
						<td></td>
						<td>Income Ranges</td>
						<td></td>
						<td></td>
						<td></td>
						<td>Tax Rates for Income Ranges</td>
						<td>Income subject to high tax rates</td>
						<td>Recommended Investment</td>
						<td>% of Taxable Income</td>
						<td>tax savings of recommended investment</td>
						<td>Tax savings for income range</td>
						<td>Maximum investment</td>
					</tr>
					<tr>
						<td><label data-cell="B73" data-formula='IF(D8="ON","Ontario",IF(D8="BC","British Columbia",IF(D8="AB","Alberta",IF(D8="SK","Saskatchewan",IF(D8="MB","Manitoba",IF(D8="QC","Québec",IF(D8="NS","Nova Scotia")))))))'></label></td>
						<td></td>
						<td></td>
						<td>&geq;</td>
						<td><label>M73</label><br/><input class="FM73" value='<?php echo isset($options["tf_tc_options_M73_formula"]) ? $options["tf_tc_options_M73_formula"] : 'IF(D8="AB",303901,IF(D8="BC",202801,IF(D8="SK",202801,IF(D8="MB",202801,IF(D8="ON",220001,IF(D8="QC",202801,IF(D8="NS",202801)))))))' ?>' name="tf_tc_options[tf_tc_options_M73_formula]"/><br/><input type="text" data-cell="M73"/></td>
						<td></td>
						<td></td>
						<td><label>Q73</label><br/><input class="FQ73" name="tf_tc_options[tf_tc_options_Q73_formula]" value='<?php echo isset($options['tf_tc_options_Q73_formula']) ? $options['tf_tc_options_Q73_formula'] : 'IF(D8="ON",0.5353,IF(D8="BC",0.477,IF(D8="AB",0.48,IF(D8="SK",0.4775,IF(D8="MB",0.504,IF(D8="QC",0.5331,IF(D8="NS",0.54)))))))' ?>'/><input type="text" data-cell="Q73"/></td>
						<td><label>S73</label><br/><input class="FS73" name="tf_tc_options[tf_tc_options_S73_formula]" value='<?php echo isset($options['tf_tc_options_S73_formula']) ? $options['tf_tc_options_S73_formula'] : 'IF(E36<M73,0,E36-M73)' ?>'/><input type="text" data-cell="S73"/></td>
						<td><label>V73</label><br/><input class="FV73" name="tf_tc_options[tf_tc_options_V73_formula]" value='<?php echo isset($options['tf_tc_options_V73_formula']) ? $options['tf_tc_options_V73_formula'] : 'IF(AH73>=(E36*B17),E36*B17,AH73)' ?>'/><input type="text" data-cell="V73"/></td>
						<td><label>X73</label><br/><input class="FX73" name="tf_tc_options[tf_tc_options_X73_formula]" value='<?php echo isset($options['tf_tc_options_X73_formula']) ? $options['tf_tc_options_X73_formula'] : 'IF(AH73>=(E36*B17),E36*B17,AH73)' ?>'/><input type="text" data-cell="X73"/></td>
						<td><label>Z73</label><br/><input class="FZ73" name="tf_tc_options[tf_tc_options_Z73_formula]" value='<?php echo isset($options['tf_tc_options_Z73_formula']) ? $options['tf_tc_options_Z73_formula'] : 'V73*Q73' ?>'/><input type="text" data-cell="Z73"/></td>
						<td><label>AC73</label><br/><input class="FAC73" name="tf_tc_options[tf_tc_options_AC73_formula]" value='<?php echo isset($options['tf_tc_options_AC73_formula']) ? $options['tf_tc_options_AC73_formula'] : 'S73*Q73' ?>'/><input type="text" data-cell="AC73"/></td>
						<td><label>AH73</label><br/><input class="FAH73" name="tf_tc_options[tf_tc_options_AH73_formula]" value='<?php echo isset($options['tf_tc_options_AH73_formula']) ? $options['tf_tc_options_AH73_formula'] : 'S73' ?>'/><input type="text" data-cell="AH73"/></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td>between</td>
						<td><label>M74</label><br/><input class="FM74" value='<?php echo isset($options["tf_tc_options_M74_formula"]) ? $options["tf_tc_options_M74_formula"] : 'IF(D8="AB",202801,IF(D8="BC",142354,IF(D8="SK",142354,IF(D8="MB",142354,IF(D8="QC",142354,IF(D8="NS",150001,202801))))))' ?>' name="tf_tc_options[tf_tc_options_M74_formula]"/><br/><input type="text" data-cell="M74"/></td>
						<td>and</td>
						<td><label>O74</label><br/><input class="FO74" value='<?php echo isset($options["tf_tc_options_O74_formula"]) ? $options["tf_tc_options_O74_formula"] : 'IF(D8="AB",303900,IF(D8="BC",202800,IF(D8="SK",202800,IF(D8="MB",202800,IF(D8="ON",220000,IF(D8="QC",202800,IF(D8="NS",202800,220000)))))))' ?>' name="tf_tc_options[tf_tc_options_O74_formula]"/><br/><input type="text" data-cell="O74"/></td>
						<td><label>Q74</label><br/><input class="FQ74" name="tf_tc_options[tf_tc_options_Q74_formula]" value='<?php echo isset($options['tf_tc_options_Q74_formula']) ? $options['tf_tc_options_Q74_formula'] : 'IF(D8="ON",0.5197,IF(D8="BC",0.437,IF(D8="AB",0.47,IF(D8="SK",0.4375,IF(D8="MB",0.464,IF(D8="QC",0.4997,IF(D8="NS",0.5)))))))' ?>'/><input type="text" data-cell="Q74"/></td>
						<td><label>S74</label><br/><input class="FS74" name="tf_tc_options[tf_tc_options_S74_formula]" value='<?php echo isset($options['tf_tc_options_S74_formula']) ? $options['tf_tc_options_S74_formula'] : 'IF(E36<M74,0,IF(E36<=M73,E36-M74,IF(E36>M73,M73-M74)))' ?>'/><input type="text" data-cell="S74"/></td>
						<td><label>V74</label><br/><input class="FV74" name="tf_tc_options[tf_tc_options_V74_formula]" value='<?php echo isset($options['tf_tc_options_V74_formula']) ? $options['tf_tc_options_V74_formula'] : 'IF(AH74+V73>=(E36*B17),E36*B17-V73,AH74)' ?>'/><input type="text" data-cell="V74"/></td>
						<td><label>X74</label><br/><input class="FX74" name="tf_tc_options[tf_tc_options_X74_formula]" value='<?php echo isset($options['tf_tc_options_X74_formula']) ? $options['tf_tc_options_X74_formula'] : 'IF(E36>0,V74/E36,0)' ?>'/><input type="text" data-cell="X74"/></td>
						<td><label>Z74</label><br/><input class="FZ74" name="tf_tc_options[tf_tc_options_Z74_formula]" value='<?php echo isset($options['tf_tc_options_Z74_formula']) ? $options['tf_tc_options_Z74_formula'] : 'V74*Q74' ?>'/><input type="text" data-cell="Z74"/></td>
						<td><label>AC74</label><br/><input class="FAC74" name="tf_tc_options[tf_tc_options_AC74_formula]" value='<?php echo isset($options['tf_tc_options_AC74_formula']) ? $options['tf_tc_options_AC74_formula'] : 'S74*Q74' ?>'/><input type="text" data-cell="AC74"/></td>
						<td><label>AH74</label><br/><input class="FAH74" name="tf_tc_options[tf_tc_options_AH74_formula]" value='<?php echo isset($options['tf_tc_options_AH74_formula']) ? $options['tf_tc_options_AH74_formula'] : 'S74' ?>'/><input type="text" data-cell="AH74"/></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td>between</td>
						<td><label>M75</label><br/><input class="FM75" value='<?php echo isset($options['tf_tc_options_M75_formula']) ? $options['tf_tc_options_M75_formula'] : 'IF(D8="BC",108461,IF(D8="AB",150001,IF(D8="ON",150001,IF(D8="SK",129215,IF(D8="MB",110000,IF(D8="QC",103915,IF(D8="NS",143252,202801)))))))' ?>' name="tf_tc_options[tf_tc_options_M75_formula]"/><br/><input type="text" data-cell="M75"/></td>
						<td>and</td>
						<td><label>O75</label><br/><input class="FO75" value='<?php echo isset($options["tf_tc_options_O75_formula"]) ? $options["tf_tc_options_O75_formula"] : 'IF(D8="BC",142353,IF(D8="SK",142353,IF(D8="MB",142353,IF(D8="QC",142353,IF(D8="NS",150000,202800)))))' ?>' name="tf_tc_options[tf_tc_options_O75_formula]"/><br/><input type="text" data-cell="O75"/></td>
						<td><label>Q75</label><br/><input class="FQ75" name="tf_tc_options[tf_tc_options_Q75_formula]" value='<?php echo isset($options['tf_tc_options_Q75_formula']) ? $options['tf_tc_options_Q75_formula'] : 'IF(D8="ON",0.4797,IF(D8="BC",0.407,IF(D8="AB",0.42,IF(D8="SK",0.4075,IF(D8="MB",0.434,IF(D8="QC",0.4746,IF(D8="NS",0.465)))))))' ?>'/><input type="text" data-cell="Q75"/></td>
						<td><label>S75</label><br/><input class="FS75" name="tf_tc_options[tf_tc_options_S75_formula]" value='<?php echo isset($options['tf_tc_options_S75_formula']) ? $options['tf_tc_options_S75_formula'] : 'IF(E36<M75,0,IF(E36<=M74,E36-M75,IF(E36>M74,M74-M75)))' ?>'/><input type="text" data-cell="S75"/></td>
						<td><label>V75</label><br/><input class="FV75" name="tf_tc_options[tf_tc_options_V75_formula]" value='<?php echo isset($options['tf_tc_options_V75_formula']) ? $options['tf_tc_options_V75_formula'] : 'IF(AH75+V74+V73>=(E36*B17),E36*B17-V74-V73,AH75)' ?>'/><input type="text" data-cell="V75"/></td>
						<td><label>X75</label><br/><input class="FX75" name="tf_tc_options[tf_tc_options_X75_formula]" value='<?php echo isset($options['tf_tc_options_X75_formula']) ? $options['tf_tc_options_X75_formula'] : 'IF(E36>0,V75/E36,0)' ?>'/><input type="text" data-cell="X75"/></td>
						<td><label>Z75</label><br/><input class="FZ75" name="tf_tc_options[tf_tc_options_Z75_formula]" value='<?php echo isset($options['tf_tc_options_Z75_formula']) ? $options['tf_tc_options_Z75_formula'] : 'V75*Q75' ?>'/><input type="text" data-cell="Z75"/></td>
						<td><label>AC75</label><br/><input class="FAC75" name="tf_tc_options[tf_tc_options_AC75_formula]" value='<?php echo isset($options['tf_tc_options_AC75_formula']) ? $options['tf_tc_options_AC75_formula'] : 'S75*Q75' ?>'/><input type="text" data-cell="AC75"/></td>
						<td><label>AH75</label><br/><input class="FAH75" name="tf_tc_options[tf_tc_options_AH75_formula]" value='<?php echo isset($options['tf_tc_options_AH75_formula']) ? $options['tf_tc_options_AH75_formula'] : 'S75' ?>'/><input type="text" data-cell="AH75"/></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td><label data-cell="J76" data-formula='IF(D8="AB","between",IF(D8="ON","between"," "))'></label></td>
						<td><label>M76</label><input type="text" class="FM76" name="tf_tc_options[tf_tc_options_M76_formula]" value='<?php echo isset($options['tf_tc_options_M76_formula']) ? $options['tf_tc_options_M76_formula'] : 'IF(D8="AB",142354,IF(D8="ON",142354,0))' ?>'/><br/><input type="text" data-cell="M76"/></td>
						<td></td>
						<td><label>O76</label><input type="text" class="FO76" name="tf_tc_options[tf_tc_options_O76_formula]" value='<?php echo isset($options['tf_tc_options_O76_formula']) ? $options['tf_tc_options_O76_formula'] : 'IF(D8="AB",150000,IF(D8="ON",150000,0))' ?>'/><br/><input type="text" data-cell="O76"/></td>
						<td><label>Q76</label><input type="text" class="FQ76" name="tf_tc_options[tf_tc_options_Q76_formula]" value='<?php echo isset($options['tf_tc_options_Q76_formula']) ? $options['tf_tc_options_Q76_formula'] : 'IF(D8="AB",0.41,IF(D8="ON",0.4641," "))' ?>'/><br/><input type="text" data-cell="Q76"/></td>
						<td><label>S76</label><br/><input type="text" class="FS76" name="tf_tc_options[tf_tc_options_S76_formula]" value='<?php echo isset($options['tf_tc_options_S76_formula']) ? $options['tf_tc_options_S76_formula'] : 'IF(D8="BC",0,IF(D8="SK",0,IF(D8="MB",0,IF(D8="QC",0,IF(D8="NS",0,IF(E36<M76,0,IF(E36<M75,E36-M76,IF(E36>M75,M75-M76))))))))' ?>'/><br/><input type="text" data-cell="S76"/></td>
						<td><label>V76</label><br/><input type="text" class="FV76" name="tf_tc_options[tf_tc_options_V76_formula]" value='<?php echo isset($options['tf_tc_options_V76_formula']) ? $options['tf_tc_options_V76_formula'] : 'IF((AH76+V75+V74+V73)>=(E36*B17),E36*B17-V75-V74-V73,AH76)' ?>'/><br/><input type="text" data-cell="V76"/></td>
						<td><label>X76</label><br/><input type="text" class="FX76" name="tf_tc_options[tf_tc_options_X76_formula]" value='<?php echo isset($options['tf_tc_options_X76_formula']) ? $options['tf_tc_options_X76_formula'] : 'IF((AH76+V75+V74+V73)>=(E36*B17),E36*B17-V75-V74-V73,AH76)' ?>'/><br/><input type="text" data-cell="X76"/></td>
						<td><label>Z76</label><br/><input type="text" class="FZ76" name="tf_tc_options[tf_tc_options_Z76_formula]" value='<?php echo isset($options['tf_tc_options_Z76_formula']) ? $options['tf_tc_options_Z76_formula'] : 'IF(Q76=" ",0,V76*Q76)' ?>'/><br/><input type="text" data-cell="Z76"/></td>
						<td><label>AC76</label><br/><input type="text" class="FAC76" name="tf_tc_options[tf_tc_options_AC76_formula]" value='<?php echo isset($options['tf_tc_options_AC76_formula']) ? $options['tf_tc_options_AC76_formula'] : 'IF(D8="BC",0,IF(D8="SK",0,IF(D8="MB",0,IF(D8="QC",0,IF(D8="NS",0,S76*Q76)))))' ?>'/><br/><input type="text" data-cell="AC76" data-formula='<?php echo $options['tf_tc_options_AC76_formula']; ?>'/></td>
						<td><label>AH76</label><br/><input type="text" class="FAH76" name="tf_tc_options[tf_tc_options_AH76_formula]" value='<?php echo isset($options['tf_tc_options_AH76_formula']) ? $options['tf_tc_options_AH76_formula'] : 'S76' ?>'/><br/><input type="text" data-cell="AH76"/></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td>Totals</td>
						<td><label>S77</label><br/><input type="text" class="FS77" name="tf_tc_options[tf_tc_options_S77_formula]" value='<?php echo isset($options['tf_tc_options_S77_formula']) ? $options['tf_tc_options_S77_formula'] : 'S73+S74+S75+S76' ?>'/><br/><input type="text" data-cell="S77"/></td>
						<td><label>V77</label><br/><input type="text" class="FV77" name="tf_tc_options[tf_tc_options_V77_formula]" value='<?php echo isset($options['tf_tc_options_V77_formula']) ? $options['tf_tc_options_V77_formula'] : 'V73+V74+V75+V76' ?>'/><br/><input type="text" data-cell="V77"/></td>
						<td><label>X77</label><br/><input type="text" class="FX77" name="tf_tc_options[tf_tc_options_X77_formula]" value='<?php echo isset($options['tf_tc_options_X77_formula']) ? $options['tf_tc_options_X77_formula'] : 'X73+X74+X75+X76' ?>'/><br/><input type="text" data-cell="X77"/></td>
						<td><label>Z77</label><br/><input type="text" class="FZ77" name="tf_tc_options[tf_tc_options_Z77_formula]" value='<?php echo isset($options['tf_tc_options_Z77_formula']) ? $options['tf_tc_options_Z77_formula'] : 'Z73+Z74+Z75+Z76' ?>'/><br/><input type="text" data-cell="Z77"/></td>
						<td><label>AC77</label><br/><input type="text" class="FAC77" name="tf_tc_options[tf_tc_options_AC77_formula]" value='<?php echo isset($options['tf_tc_options_AC77_formula']) ? $options['tf_tc_options_AC77_formula'] : 'AC73+AC74+AC75+AC76' ?>'/><br/><input type="text" data-cell="AC77"/></td>
						<td><label>AH77</label><br/><input type="text" class="FAH77" name="tf_tc_options[tf_tc_options_AH77_formula]" value='<?php echo isset($options['tf_tc_options_AH77_formula']) ? $options['tf_tc_options_AH77_formula'] : 'AH73+AH74+AH75+AH76' ?>'/><br/><input type="text" data-cell="AH77"/></td>
					</tr>
				</table>
				<!-- Table 4 Working --> 
				<table class="form-table tax-table-5">
					<tr>
						<td>Calculations showing the applicable tax rates by income bracket for the province selected in input field above</td>
					</tr>
					<tr>
						<td>Income subject to tax & Tax savings:</td>
						<td>Calculations</td>
						<td>Recommended Tax Savings on income over <br/><label data-cell="M80" data-formula="M73-1"></label></td>
						<td>Alternative 1 Tax Savings on income over <br/><label data-cell="T80" data-formula="M74-1"></label></td>
						<td>Alternative 2 Tax Savings on income over <br/><label data-cell="AA80" data-formula="M75-1"/></td>
						<td><label data-cell="AK80" data-formula='IF(D8="ON","Alternative 3 Tax savings on income over ",IF(D8="AB","Alternative 3 Tax savings on income over ","NA "))'></label></td>
					</tr>
				<tr>
						<td>Taxable Income</td>
						<td>A</td>
						<td><label>M81</label><br/><input type="text" class="FM81" name="tf_tc_options[tf_tc_options_M81_formula]" value='<?php echo isset($options['tf_tc_options_M81_formula']) ? $options['tf_tc_options_M81_formula'] : 'E36' ?>'/><br/><input data-cell="M81"/></td>
						<td><label>T81</label><br/><input type="text" class="FT81" name="tf_tc_options[tf_tc_options_T81_formula]" value='<?php echo isset($options['tf_tc_options_T81_formula']) ? $options['tf_tc_options_T81_formula'] : 'E36' ?>'/><br/><input data-cell="T81"/></td>
						<td><label>AA81</label><br/><input type="text" class="FAA81" name="tf_tc_options[tf_tc_options_AA81_formula]" value='<?php echo isset($options['tf_tc_options_AA81_formula']) ? $options['tf_tc_options_AA81_formula'] : 'E36' ?>'/><br/><input data-cell="AA81"/></td>
						<td><label>AK81</label><br/><input type="text" class="FAK81" name="tf_tc_options[tf_tc_options_AK81_formula]" value='<?php echo isset($options['tf_tc_options_AK81_formula']) ? $options['tf_tc_options_AK81_formula'] : 'E36' ?>'/><br/><input data-cell="AK81"/></td>
					</tr>
						<tr>
						<td>Less: starting income taxed at highest tax rate:</td>
						<td></td>
						<td><label>M82</label><br/><input type="text" class="FM82" name="tf_tc_options[tf_tc_options_M82_formula]" value='<?php echo isset($options['tf_tc_options_M82_formula']) ? $options['tf_tc_options_M82_formula'] : 'M73*(-1)' ?>'/><br/><input data-cell="M82"/></td>
						<td><label>T82</label><br/><input type="text" class="FT82" name="tf_tc_options[tf_tc_options_T82_formula]" value='<?php echo isset($options['tf_tc_options_T82_formula']) ? $options['tf_tc_options_T82_formula'] : 'M73*(-1)' ?>'/><br/><input data-cell="T82"/></td>
						<td><label>AA82</label><br/><input type="text" class="FAA82" name="tf_tc_options[tf_tc_options_AA82_formula]" value='<?php echo isset($options['tf_tc_options_AA82_formula']) ? $options['tf_tc_options_AA82_formula'] : 'M73*(-1)' ?>'/><br/><input data-cell="AA82"/></td>
						<td><label>AK82</label><br/><input type="text" class="FAK82" name="tf_tc_options[tf_tc_options_AK82_formula]" value='<?php echo isset($options['tf_tc_options_AK82_formula']) ? $options['tf_tc_options_AK82_formula'] : 'M73*(-1)' ?>'/><br/><input data-cell="AK82"/></td>
					</tr>
					<tr>
						<td>Income subject to <input type="text" data-cell="B83" data-formula="Q73" /> tax rate</td>
						<td>B</td>
						<td><label>M83</label><br/><input type="text" class="FM83" name="tf_tc_options[tf_tc_options_M83_formula]" value='<?php echo isset($options['tf_tc_options_M83_formula']) ? $options['tf_tc_options_M83_formula'] : 'IF(M81<M82*-1,0,M81+M82)' ?>'/><br/><input data-cell="M83"/></td>
						<td><label>T83</label><br/><input type="text" class="FT83" name="tf_tc_options[tf_tc_options_T83_formula]" value='<?php echo isset($options['tf_tc_options_T83_formula']) ? $options['tf_tc_options_T83_formula'] : 'IF(T81<T82*-1,0,T81+T82)' ?>'/><br/><input data-cell="T83"/></td>
						<td><label>AA83</label><br/><input type="text" class="FAA83" name="tf_tc_options[tf_tc_options_AA83_formula]" value='<?php echo isset($options['tf_tc_options_AA83_formula']) ? $options['tf_tc_options_AA83_formula'] : 'IF(AA81<AA82*-1,0,AA81+AA82)' ?>'/><br/><input data-cell="AA83"/></td>
						<td><label>AK83</label><br/><input type="text" class="FAK83" name="tf_tc_options[tf_tc_options_AK83_formula]" value='<?php echo isset($options['tf_tc_options_AK83_formula']) ? $options['tf_tc_options_AK83_formula'] : 'IF(AK81<AK82*-1,0,AK81+AK82)' ?>'/><br/><input data-cell="AK83"/></td>
					</tr>
					<tr>
						<td>Tax rate</td>
						<td>C</td>
						<td><label>M84</label><br/><input type="text" class="FM84" name="tf_tc_options[tf_tc_options_M84_formula]" value='<?php echo isset($options['tf_tc_options_M84_formula']) ? $options['tf_tc_options_M84_formula'] : 'IF(M83<=0,0,Q73)' ?>'/><br/><input data-cell="M84"/></td>
						<td><label>T84</label><br/><input type="text" class="FT84" name="tf_tc_options[tf_tc_options_T84_formula]" value='<?php echo isset($options['tf_tc_options_T84_formula']) ? $options['tf_tc_options_T84_formula'] : 'IF(T83<=0,0,Q73)' ?>'/><br/><input data-cell="T84"/></td>
						<td><label>AA84</label><br/><input type="text" class="FAA84" name="tf_tc_options[tf_tc_options_AA84_formula]" value='<?php echo isset($options['tf_tc_options_AA84_formula']) ? $options['tf_tc_options_AA84_formula'] : 'IF(AA83<=0,0,Q73)' ?>'/><br/><input data-cell="AA84"/></td>
						<td><label>AK84</label><br/><input type="text" class="FAK84" name="tf_tc_options[tf_tc_options_AK84_formula]" value='<?php echo isset($options['tf_tc_options_AK84_formula']) ? $options['tf_tc_options_AK84_formula'] : 'IF(AK83<=0,0,Q73)' ?>'/><br/><input data-cell="AK84"/></td>
					</tr>
					<tr>
						<td>Available tax savings at <input type="text" data-cell="B85" data-formula="Q73"/> tax rate</td>
						<td>D = B x C</td>
						<td><label>M85</label><br/><input type="text" class="FM85" name="tf_tc_options[tf_tc_options_M85_formula]" value='<?php echo isset($options['tf_tc_options_M85_formula']) ? $options['tf_tc_options_M85_formula'] : 'M83*M84' ?>'/><br/><input data-cell="M85"/></td>
						<td><label>T85</label><br/><input type="text" class="FT85" name="tf_tc_options[tf_tc_options_T85_formula]" value='<?php echo isset($options['tf_tc_options_T85_formula']) ? $options['tf_tc_options_T85_formula'] : 'T83*T84' ?>'/><br/><input data-cell="T85"/></td>
						<td><label>AA85</label><br/><input type="text" class="FAA85" name="tf_tc_options[tf_tc_options_AA85_formula]" value='<?php echo isset($options['tf_tc_options_AA85_formula']) ? $options['tf_tc_options_AA85_formula'] : 'AA83*AA84' ?>'/><br/><input data-cell="AA85"/></td>
						<td><label>AK85</label><br/><input type="text" class="FAK85" name="tf_tc_options[tf_tc_options_AK85_formula]" value='<?php echo isset($options['tf_tc_options_AK85_formula']) ? $options['tf_tc_options_AK85_formula'] : 'AK83*AK84' ?>'/><br/><input data-cell="AK85"/></td>
					</tr>
					<tr>
						<td>Income subject to <input type="text" data-cell="B87" data-formula="Q74"/> tax rate</td>
						<td>E</td>
						<td>N/A</td>
						<td><label>T87</label><br/><input type="text" class="FT87" name="tf_tc_options[tf_tc_options_T87_formula]" value='<?php echo isset($options['tf_tc_options_T87_formula']) ? $options['tf_tc_options_T87_formula'] : 'IF(T81<M74,0,IF(T81<M73,T81-M74,IF(T81>M73,T82*(-1)-M74)))' ?>'/><br/><input data-cell="T87"/></td>
						<td><label>AA87</label><br/><input type="text" class="FAA87" name="tf_tc_options[tf_tc_options_AA87_formula]" value='<?php echo isset($options['tf_tc_options_AA87_formula']) ? $options['tf_tc_options_AA87_formula'] : 'IF(AA81<M74,0,IF(AA81<M73,AA81-M74,IF(AA81>M73,M73-M74)))' ?>'/><br/><input data-cell="AA87"/></td>
						<td><label>AK87</label><br/><input type="text" class="FAK87" name="tf_tc_options[tf_tc_options_AK87_formula]" value='<?php echo isset($options['tf_tc_options_AK87_formula']) ? $options['tf_tc_options_AK87_formula'] : 'IF(AK81<M74,0,IF(AK81<M73,AK81-M74,IF(AK81>M73,M73-M74)))' ?>'/><br/><input data-cell="AK87" data-formula='<? echo $options['tf_tc_options_AK87_formula']; ?>'/></td>
					</tr>
					<tr>
						<td>Tax rate</td>
						<td>F</td>
						<td>N/A</td>
						<td><label>T88</label><br/><input type="text" class="FT88" name="tf_tc_options[tf_tc_options_T88_formula]" value='<?php echo isset($options['tf_tc_options_T88_formula']) ? $options['tf_tc_options_T88_formula'] : 'IF(T87<=0,0,Q74)' ?>'/><br/><input data-cell="T88"/></td>
						<td><label>AA88</label><br/><input type="text" class="FAA88" name="tf_tc_options[tf_tc_options_AA88_formula]" value='<?php echo isset($options['tf_tc_options_AA88_formula']) ? $options['tf_tc_options_AA88_formula'] : 'IF(AA87<=0,0,Q74)' ?>'/><br/><input data-cell="AA88"/></td>
						<td><label>AK88</label><br/><input type="text" class="FAK88" name="tf_tc_options[tf_tc_options_AK88_formula]" value='<?php echo isset($options['tf_tc_options_AK88_formula']) ? $options['tf_tc_options_AK88_formula'] : 'IF(AK87<=0,0,Q74)' ?>'/><br/><input data-cell="AK88"/></td>
					</tr>
				<tr>
						<td>Available tax savings at <input type="text" data-cell="B89" data-formula="Q74"/> tax rate</td>
						<td>G = E x F</td>
						<td>N/A</td>
						<td><label>T89</label><br/><input type="text" class="FT89" name="tf_tc_options[tf_tc_options_T89_formula]" value='<?php echo isset($options['tf_tc_options_T89_formula']) ? $options['tf_tc_options_T89_formula'] : 'T87*T88' ?>'/><br/><input data-cell="T89"/></td>
						<td><label>AA89</label><br/><input type="text" class="FAA89" name="tf_tc_options[tf_tc_options_AA89_formula]" value='<?php echo isset($options['tf_tc_options_AA89_formula']) ? $options['tf_tc_options_AA89_formula'] : 'AA87*AA88' ?>'/><br/><input data-cell="AA89"/></td>
						<td><label>AK89</label><br/><input type="text" class="FAK89" name="tf_tc_options[tf_tc_options_AK89_formula]" value='<?php echo isset($options['tf_tc_options_AK89_formula']) ? $options['tf_tc_options_AK89_formula'] : 'AK87*AK88' ?>'/><br/><input data-cell="AK89"/></td>
					</tr>
					<tr>
						<td>Inccome subject to <input type="text" data-cell="B91" data-formula="Q75"/> tax rate</td>
						<td>H</td>
						<td>N/A</td>
						<td>N/A</td>
						<td><label>AA91</label><br/><input type="text" class="FAA91" name="tf_tc_options[tf_tc_options_AA91_formula]" value='<?php echo isset($options['tf_tc_options_AA91_formula']) ? $options['tf_tc_options_AA91_formula'] : 'IF(AA81<M75,0,IF(AA81<M74,AA81-M75,IF(AA81>M74,M74-M75)))' ?>'/><br/><input data-cell="AA91"/></td>
						<td><label>AK91</label><br/><input type="text" class="FAK91" name="tf_tc_options[tf_tc_options_AK91_formula]" value='<?php echo isset($options['tf_tc_options_AK91_formula']) ? $options['tf_tc_options_AK91_formula'] : 'IF(AK81<M75,0,IF(AK81<M74,AK81-M75,IF(AK81>M74,M74-M75)))' ?>'/><br/><input data-cell="AK91"/></td>
					</tr>
						<tr>
						<td>Tax rate</td>
						<td>I</td>
						<td>N/A</td>
						<td>N/A</td>
						<td><label>AA92</label><br/><input type="text" class="FAA92" name="tf_tc_options[tf_tc_options_AA92_formula]" value='<?php echo isset($options['tf_tc_options_AA92_formula']) ? $options['tf_tc_options_AA92_formula'] : 'IF(AA91<=0,0,Q75)' ?>'/><br/><input data-cell="AA92"/></td>
						<td><label>AK92</label><br/><input type="text" class="FAK92" name="tf_tc_options[tf_tc_options_AK92_formula]" value='<?php echo isset($options['tf_tc_options_AK92_formula']) ? $options['tf_tc_options_AK92_formula'] : 'IF(AK91<=0,0,Q75)' ?>'/><br/><input data-cell="AK92"/></td>
					</tr>
					<tr>
						<td>Available tax savings at <input type="text" data-cell="B93" data-formula="Q75"/> tax rate</td>
						<td>J = H x I</td>
						<td>N/A</td>
						<td>N/A</td>
						<td><label>AA93</label><br/><input type="text" class="FAA93" name="tf_tc_options[tf_tc_options_AA93_formula]" value='<?php echo isset($options['tf_tc_options_AA93_formula']) ? $options['tf_tc_options_AA93_formula'] : 'AA91*AA92' ?>'/><br/><input data-cell="AA93"/></td>
						<td><label>AK93</label><br/><input type="text" class="FAK93" name="tf_tc_options[tf_tc_options_AK93_formula]" value='<?php echo isset($options['tf_tc_options_AK93_formula']) ? $options['tf_tc_options_AK93_formula'] : 'AK91*AK92' ?>'/><br/><input data-cell="AK93"/></td>
					</tr>
					<tr>
						<td>Inccome subject to <input type="text" data-cell="B95" data-formula="Q76"/> tax rate</td>
						<td>H</td>
						<td>N/A</td>
						<td>N/A</td>
						<td><label>AA95</label><br/><input type="text" class="FAA95" name="tf_tc_options[tf_tc_options_AA95_formula]" value='<?php echo isset($options['tf_tc_options_AA95_formula']) ? $options['tf_tc_options_AA95_formula'] : 'IF(AA81<M76,0,IF(AA81<M75,AA81-M76,IF(AA81>M75,M75-M76)))' ?>'/><br/><input data-cell="AA95"/></td>
						<td><label>AK95</label><br/><input type="text" class="FAK95" name="tf_tc_options[tf_tc_options_AK95_formula]" value='<?php echo isset($options['tf_tc_options_AK95_formula']) ? $options['tf_tc_options_AK95_formula'] : 'IF(AK81<M76,0,IF(AK81<M75,AK81-M76,IF(AK81>M75,M75-M76)))' ?>'/><br/><input data-cell="AK95"/></td>
					</tr>
					<tr>
						<td>Tax rate</td>
						<td>I</td>
						<td>N/A</td>
						<td>N/A</td>
						<td><label>AA96</label><br/><input type="text" class="FAA96" name="tf_tc_options[tf_tc_options_AA96_formula]" value='<?php echo isset($options['tf_tc_options_AA96_formula']) ? $options['tf_tc_options_AA96_formula'] : 'IF(AA95<=0,0,IF(Q76=" ",0," "))' ?>'/><br/><input data-cell="AA96"/></td>
						<td><label>AK96</label><br/><input type="text" class="FAK96" name="tf_tc_options[tf_tc_options_AK96_formula]" value='<?php echo isset($options['tf_tc_options_AK96_formula']) ? $options['tf_tc_options_AK96_formula'] : 'IF(AK95<=0,0,IF(Q76=" ",0," "))' ?>'/><br/><input data-cell="AK96"/></td>
					</tr>
					<tr>
						<td>Available tax savings at <input type="text" data-cell="B97" data-formula="Q75"/> tax rate</td>
						<td>J = H x I</td>
						<td>N/A</td>
						<td>N/A</td>
						<td><label>AA97</label><br/><input type="text" class="FAA97" name="tf_tc_options[tf_tc_options_AA97_formula]" value='<?php echo isset($options['tf_tc_options_AA97_formula']) ? $options['tf_tc_options_AA97_formula'] : 'AA95*AA96' ?>'/><br/><input data-cell="AA97"/></td>
						<td><label>AK97</label><br/><input type="text" class="FAK97" name="tf_tc_options[tf_tc_options_AK97_formula]" value='<?php echo isset($options['tf_tc_options_AK97_formula']) ? $options['tf_tc_options_AK97_formula'] : 'AK95*AK96' ?>'/><br/><input data-cell="AK97"/></td>
					</tr>
					<tr>
						<td>Total Available tax savings - $</td>
						<td>K = D + G + J</td>
						<td><label>M99</label><br/><input type="text" class="FM99" name="tf_tc_options[tf_tc_options_M99_formula]" value='<?php echo isset($options['tf_tc_options_M99_formula']) ? $options['tf_tc_options_M99_formula'] : 'M85' ?>'/><br/><input type="text" data-cell="M99"/></td>
						<td><label>T99</label><br/><input type="text" class="FT99" name="tf_tc_options[tf_tc_options_T99_formula]" value='<?php echo isset($options['tf_tc_options_T99_formula']) ? $options['tf_tc_options_T99_formula'] : 'T85+T89' ?>'/><br/><input type="text" data-cell="T99"/></td>
						<td><label>AA99</label><br/><input type="text" class="FAA99" name="tf_tc_options[tf_tc_options_AA99_formula]" value='<?php echo isset($options['tf_tc_options_AA99_formula']) ? $options['tf_tc_options_AA99_formula'] : 'AA85+AA89+AA93' ?>'/><br/><input type="text" data-cell="AA99"/></td>
						<td><label>AK99</label><br/><input type="text" class="FAK99" name="tf_tc_options[tf_tc_options_AK99_formula]" value='<?php echo isset($options['tf_tc_options_AK99_formula']) ? $options['tf_tc_options_AK99_formula'] : 'AK85+AK8+AK93+AK97' ?>'/><br/><input type="text" data-cell="AK99"/></td>
					</tr>
					<tr>
						<td>Total tax savings - %</td>
						<td>L = K / M</td>
						<td><label>M100</label><br/><input type="text" class="FM100" name="tf_tc_options[tf_tc_options_M100_formula]" value='<?php echo isset($options['tf_tc_options_M100_formula']) ? $options['tf_tc_options_M100_formula'] : 'IF(M99<=0,0,M99/M101)' ?>'/><br/><input type="text" data-cell="M100"/></td>
						<td><label>T100</label><br/><input type="text" class="FT100" name="tf_tc_options[tf_tc_options_T100_formula]" value='<?php echo isset($options['tf_tc_options_T100_formula']) ? $options['tf_tc_options_T100_formula'] : 'IF(T99<=0,0,T99/T101)' ?>'/><br/><input type="text" data-cell="T100"/></td>
						<td><label>AA100</label><br/><input type="text" class="FAA100" name="tf_tc_options[tf_tc_options_AA100_formula]" value='<?php echo isset($options['tf_tc_options_AA100_formula']) ? $options['tf_tc_options_AA100_formula'] : 'IF(AA99<=0,0,AA99/AA101)' ?>'/><br/><input type="text" data-cell="AA100"/></td>
						<td><label>AK100</label><br/><input type="text" class="FAK100" name="tf_tc_options[tf_tc_options_AK100_formula]" value='<?php echo isset($options['tf_tc_options_AK100_formula']) ? $options['tf_tc_options_AK100_formula'] : 'IF(AK99<=0,0,AK99/AK101)' ?>'/><br/><input type="text" data-cell="AK100"/></td>
					</tr>
				<tr>
						<td>Recommended Terra Flow-Through LP investment</td>
						<td>M = B + E + H</td>
						<td><label>M101</label><br/><input type="text" class="FM101" name="tf_tc_options[tf_tc_options_M101_formula]" value='<?php echo isset($options['tf_tc_options_M101_formula']) ? $options['tf_tc_options_M101_formula'] : 'M83' ?>'/><br/><input type="text" data-cell="M101"/></td>
						<td><label>T101</label><br/><input type="text" class="FT101" name="tf_tc_options[tf_tc_options_T101_formula]" value='<?php echo isset($options['tf_tc_options_T101_formula']) ? $options['tf_tc_options_T101_formula'] : 'T83+T87' ?>'/><br/><input type="text" data-cell="T101"/></td>
						<td><label>AA101</label><br/><input type="text" class="FAA101" name="tf_tc_options[tf_tc_options_AA101_formula]" value='<?php echo isset($options['tf_tc_options_AA101_formula']) ? $options['tf_tc_options_AA101_formula'] : 'AA83+AA87+AA91' ?>'/><br/><input type="text" data-cell="AA101"/></td>
						<td><label>AK101</label><br/><input type="text" class="FAK101" name="tf_tc_options[tf_tc_options_AK101_formula]" value='<?php echo isset($options['tf_tc_options_AK101_formula']) ? $options['tf_tc_options_AK101_formula'] : 'AK83+AK87+AK91+AK95' ?>'/><br/><input type="text" data-cell="AK101"/></td>
					</tr>
					<!--	-->
					
				</table> 

					<?php submit_button(); ?>
				</form>
				<div id="example-plugin-reset" style="clear: both;">
				<form method="post" action="">
					<?php wp_nonce_field('example-plugin-reset', 'example-plugin-reset-nonce'); ?>
					<label style="font-weight:normal;">
						<?php printf(__('Do you wish to <strong>completely reset</strong> the default options for', 'example-plugin')); ?> <?php echo $this->title ?>? </label>
					<input class="button-primary" type="submit" name="example_plugin_reset" value="Restore Defaults" />
				</form>
			</div>
				
		</div>
		<?php
	}
	public function simple_admin_page_display(){
		$subkey = $this->subkey;
		$options = get_option($subkey);
		if(isset($_POST['simple_plugin_reset'])) {
				check_admin_referer('simple-plugin-reset', 'simple-plugin-reset-nonce');
				delete_option($subkey);
		?>
				<div class="updated">
					<p><?php _e('All options have been restored to their default values.', 'simple-plugin'); ?></p>
				</div>
		<?php } ?>
		<style>
		td { border: 1px solid #ccc; }
		.submit input#submit.button-primary { position:fixed;bottom:5%;right:10%;}
		</style>

		<div class="wrap tf_tc-simple-options-page <?php echo $subkey; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
		
		<form action='options.php' method='post' class="tf_admin_form_simple"  id="calc_form">
					<?php settings_fields( $subkey ); ?>
					<?php do_settings_sections( $subkey ); ?>
					<div>
						<label>Label For Input Options<br/>
							<input type="text" name="tf_tc_options[tf_tc_options_slim_calculator_title]" value="<?php echo  isset($options[$subkey . '_calculator_title']) ? $options[$subkey . '_calculator_title'] : 'Input Options'; ?>"/>
						</label>
					</div>
			<table>
				<tbody>
					<tr>
						<td><label data-cell="A2">Investment</label></td>
						<td><input type="text" data-cell="B2" name="tf_tc_simple_options_investment" value="<?php echo isset($options[$subkey . '_investment']) ? $options[$subkey . '_investment'] : '1000'; ?>" data-format="$0,0"/></td>
					</tr>
					<tr>
						<td><label data-cell="A3">FMV of RRSP Transfer or Donation</label></td>
						<td><input type="text" data-cell="B3" name="tf_tc_simple_options_donation" value="<?php echo isset($options[$subkey . '_donation']) ? $options[$subkey . '_donation'] : '75'; ?>" data-format="0%"/></td>
					</tr>
					<tr>
						<td><label data-cell="A4">Mining Tax Credit (Fed)</label></td>
						<td><input type="text" data-cell="B4" name="tf_tc_simple_options_credit_fed" value="<?php echo isset($options[$subkey . '_credit_fed']) ? $options[$subkey . '_credit_fed'] : '15'; ?>" data-format="0%"/></td>
					</tr>
					<tr>
						<td><label data-cell="A5">Mining - % of Investments</label></td>
						<td><input type="text" data-cell="B5" name="tf_tc_simple_options_mining" value="<?php echo isset($options[$subkey . '_mining']) ? $options[$subkey . '_mining'] : '35'; ?>" data-format="0%"/></td>
					</tr>
					<tr>
						<td><label data-cell="A6">Province</label></td>
						<td><input type="text" data-cell="B6" name="tf_tc_simple_options_province_bc" value="<?php echo isset($options[$subkey . '_province_bc']) ? $options[$subkey . '_province_bc'] : 'BC'; ?>"/></td>
						<td><input type="text" data-cell="C6" name="tf_tc_simple_options_province_ab" value="<?php echo isset($options[$subkey . '_province_ab']) ? $options[$subkey . '_province_ab'] : 'AB'; ?>"/></td>
						<td><input type="text" data-cell="D6" name="tf_tc_simple_options_province_sk" value="<?php echo isset($options[$subkey . '_province_sk']) ? $options[$subkey . '_province_sk'] : 'SK'; ?>"/></td>
						<td><input type="text" data-cell="E6" name="tf_tc_simple_options_province_mb" value="<?php echo isset($options[$subkey . '_province_mb']) ? $options[$subkey . '_province_mb'] : 'MB'; ?>"/></td>
						<td><input type="text" data-cell="F6" name="tf_tc_simple_options_province_on" value="<?php echo isset($options[$subkey . '_province_on']) ? $options[$subkey . '_province_on'] : 'ON'; ?>"/></td>
						<td><input type="text" data-cell="G6" name="tf_tc_simple_options_province_qc" value="<?php echo isset($options[$subkey . '_province_qc']) ? $options[$subkey . '_province_qc'] : 'QC'; ?>"/></td>
						<td><input type="text" data-cell="H6" name="tf_tc_simple_options_province_ns" value="<?php echo isset($options[$subkey . '_province_ns']) ? $options[$subkey . '_province_ns'] : 'NS'; ?>"/></td>
					</tr>
					<tr>
						<td><label data-cell="A7">Tax Rate (minimum)</label></td>
						<td><input type="text" data-cell="B7" name="tf_tc_simple_options_taxratemin_bc" value="<?php echo isset($options[$subkey . '_taxratemin_bc']) ? $options[$subkey . '_taxratemin_bc'] : '47.7'; ?>" data-format="0.00%"/></td>
						<td><input type="text" data-cell="C7" name="tf_tc_simple_options_taxratemin_ab" value="<?php echo isset($options[$subkey . '_taxratemin_ab']) ? $options[$subkey . '_taxratemin_ab'] : '48'; ?>" data-format="0.00%"/></td>
						<td><input type="text" data-cell="D7" name="tf_tc_simple_options_taxratemin_sk" value="<?php echo isset($options[$subkey . '_taxratemin_sk']) ? $options[$subkey . '_taxratemin_sk'] : '47.75'; ?>" data-format="0.00%"/></td>
						<td><input type="text" data-cell="E7" name="tf_tc_simple_options_taxratemin_mb" value="<?php echo isset($options[$subkey . '_taxratemin_mb']) ? $options[$subkey . '_taxratemin_mb'] : '50.40'; ?>" data-format="0.00%"/></td>
						<td><input type="text" data-cell="F7" name="tf_tc_simple_options_taxratemin_on" value="<?php echo isset($options[$subkey . '_taxratemin_on']) ? $options[$subkey . '_taxratemin_on'] : '53.53'; ?>" data-format="0.00%"/></td>
						<td><input type="text" data-cell="G7" name="tf_tc_simple_options_taxratemin_qc" value="<?php echo isset($options[$subkey . '_taxratemin_qc']) ? $options[$subkey . '_taxratemin_qc'] : '53.31'; ?>" data-format="0.00%"/></td>
						<td><input type="text" data-cell="H7" name="tf_tc_simple_options_taxratemin_ns" value="<?php echo isset($options[$subkey . '_taxratemin_ns']) ? $options[$subkey . '_taxratemin_ns'] : '54'; ?>" data-format="0.00%"/></td>
					</tr>
					<tr>
						<td><label data-cell="A8">Donation tax credit (maximum)</label></td>
						<td><input type="text" data-cell="B8" name="tf_tc_simple_options_donationtaxcredit_bc" value="<?php echo isset($options[$subkey . '_donationtaxcredit_bc']) ? $options[$subkey . '_donationtaxcredit_bc'] : '47.7'; ?>" data-format="0.00%"/></td>
						<td><input type="text" data-cell="C8" name="tf_tc_simple_options_donationtaxcredit_ab" value="<?php echo isset($options[$subkey . '_donationtaxcredit_ab']) ? $options[$subkey . '_donationtaxcredit_ab'] : '54'; ?>" data-format="0.00%"/></td>
						<td><input type="text" data-cell="D8" name="tf_tc_simple_options_donationtaxcredit_sk" value="<?php echo isset($options[$subkey . '_donationtaxcredit_sk']) ? $options[$subkey . '_donationtaxcredit_sk'] : '47.75'; ?>" data-format="0.00%"/></td>
						<td><input type="text" data-cell="E8" name="tf_tc_simple_options_donationtaxcredit_mb" value="<?php echo isset($options[$subkey . '_donationtaxcredit_mb']) ? $options[$subkey . '_donationtaxcredit_mb'] : '50.40'; ?>" data-format="0.00%"/></td>
						<td><input type="text" data-cell="F8" name="tf_tc_simple_options_donationtaxcredit_on" value="<?php echo isset($options[$subkey . '_donationtaxcredit_on']) ? $options[$subkey . '_donationtaxcredit_on'] : '50.41'; ?>" data-format="0.00%"/></td>
						<td><input type="text" data-cell="G8" name="tf_tc_simple_options_donationtaxcredit_qc" value="<?php echo isset($options[$subkey . '_donationtaxcredit_qc']) ? $options[$subkey . '_donationtaxcredit_qc'] : '51.56'; ?>" data-format="0.00%"/></td>
						<td><input type="text" data-cell="H8" name="tf_tc_simple_options_donationtaxcredit_ns" value="<?php echo isset($options[$subkey . '_donationtaxcredit_ns']) ? $options[$subkey . '_donationtaxcredit_ns'] : '54'; ?>" data-format="0.00%"/></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">Terra LP</td>
						<td></td>
						<td colspan="2">LP + RRSP</td>
						<td></td>
						<td colspan="2">LP + Donation</td>
						<td></td>
						<td colspan="2">LP + RRSP + Cap Loss</td>
						<td></td>
						<td colspan="2">LP + Donation + Cap Loss</td>
					</tr>
					<tr>
						<td></td>
						<td>Tax Savings</td>
						<td>Cash Outlay</td>
						<td></td>
						<td>Tax Savings</td>
						<td>Cash Outlay</td>
						<td></td>
						<td>Tax Savings</td>
						<td>Cash Outlay</td>
						<td></td>
						<td>Tax Savings</td>
						<td>Cash Outlay</td>
						<td></td>
						<td>Tax Savings</td>
						<td>Cash Outlay</td>
					</tr>
					<tr>
						<td>BC</td>
						<td>
							<input type="text" data-cell="B12" name="tf_tc_simple_options_terra_taxsavings_bc" data-formula="<?php echo isset($options[$subkey . '_terra_taxsavings_bc_formula']) ? $options[$subkey . '_terra_taxsavings_bc_formula'] : 'B2*B7+B2*0.9*B5*B4*(1-B7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FB12" name="tf_tc_simple_options_terra_taxsavings_bc_formula" value="<?php echo isset($options[$subkey . '_terra_taxsavings_bc_formula']) ? $options[$subkey . '_terra_taxsavings_bc_formula'] : 'B2*B7+B2*0.9*B5*B4*(1-B7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="C12" name="tf_tc_simple_options_terra_cashoutlay_bc" data-formula="<?php echo isset($options[$subkey . '_terra_cashoutlay_bc_formula']) ? $options[$subkey . '_terra_cashoutlay_bc_formula'] : 'B2-B12'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FC12" name="tf_tc_simple_options_terra_cashoutlay_bc_formula" value="<?php echo isset($options[$subkey . '_terra_cashoutlay_bc_formula']) ? $options[$subkey . '_terra_cashoutlay_bc_formula'] : 'B2-B12'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="E12" name="tf_tc_simple_options_lprrsp_taxsavings_bc" data-formula="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_bc_formula']) ? $options[$subkey . '_lprrsp_taxsavings_bc_formula'] : 'B12+(B3*B2*B7)-(B3*B2*0.5*B7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FE12" name="tf_tc_simple_options_lprrsp_taxsavings_bc_formula" value="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_bc_formula']) ? $options[$subkey . '_lprrsp_taxsavings_bc_formula'] : 'B12+(B3*B2*B7)-(B3*B2*0.5*B7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="F12" name="tf_tc_simple_options_lprrsp_cashoutlay_bc" data-formula="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_bc_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_bc_formula'] : 'B2-E12'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FF12" name="tf_tc_simple_options_lprrsp_cashoutlay_bc_formula" value="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_bc_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_bc_formula'] : 'B2-E12'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="H12" name="tf_tc_simple_options_lpdonation_taxsavings_bc" data-formula="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_bc_formula']) ? $options[$subkey . '_lpdonation_taxsavings_bc_formula'] : 'B12+(B3*B2*B8)-(B3*B2*0.5*B7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FH12" name="tf_tc_simple_options_lpdonation_taxsavings_bc_formula" value="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_bc_formula']) ? $options[$subkey . '_lpdonation_taxsavings_bc_formula'] : 'B12+(B3*B2*B8)-(B3*B2*0.5*B7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="I12" name="tf_tc_simple_options_lpdonation_cashoutlay_bc" data-formula="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_bc_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_bc_formula'] : 'B2-H12'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FI12" name="tf_tc_simple_options_lpdonation_cashoutlay_bc_formula" value="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_bc_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_bc_formula'] : 'B2-H12'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="K12" name="tf_tc_simple_options_lprrspcaploss_taxsavings_bc" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_bc_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_bc_formula'] : 'B12+(B3*B2*B7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FK12" name="tf_tc_simple_options_lprrspcaploss_taxsavings_bc_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_bc_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_bc_formula'] : 'B12+(B3*B2*B7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="L12" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_bc" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_bc_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_bc_formula'] : 'B2-K12'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FL12" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_bc_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_bc_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_bc_formula'] : 'B2-K12'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="N12" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_bc" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_bc_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_bc_formula'] : 'B12+(B3*B2*B8)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FN12" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_bc_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_bc_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_bc_formula'] : 'B12+(B3*B2*B8)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="O12" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_bc" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_bc_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_bc_formula'] : 'B2-N12'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FO12" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_bc_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_bc_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_bc_formula'] : 'B2-N12'; ?>"/>
						</td>
					</tr>
					<tr>
						<td>AB</td>
						<td>
							<input type="text" data-cell="B13" name="tf_tc_simple_options_terra_taxsavings_ab" data-formula="<?php echo isset($options[$subkey . '_terra_taxsavings_ab_formula']) ? $options[$subkey . '_terra_taxsavings_ab_formula'] : 'B2*C7+B2*0.9*B5*B4*(1-C7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FB13" name="tf_tc_simple_options_terra_taxsavings_ab_formula" value="<?php echo isset($options[$subkey . '_terra_taxsavings_ab_formula']) ? $options[$subkey . '_terra_taxsavings_ab_formula'] : 'B2*C7+B2*0.9*B5*B4*(1-C7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="C13" name="tf_tc_simple_options_terra_cashoutlay_ab" data-formula="<?php echo isset($options[$subkey . '_terra_cashoutlay_ab_formula']) ? $options[$subkey . '_terra_cashoutlay_ab_formula'] : 'B2-B13'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FC13" name="tf_tc_simple_options_terra_cashoutlay_ab_formula" value="<?php echo isset($options[$subkey . '_terra_cashoutlay_ab_formula']) ? $options[$subkey . '_terra_cashoutlay_ab_formula'] : 'B2-B13'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="E13" name="tf_tc_simple_options_lprrsp_taxsavings_ab" data-formula="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_ab_formula']) ? $options[$subkey . '_lprrsp_taxsavings_ab_formula'] : 'B13+(B3*B2*C7)-(B3*B2*0.5*C7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FE13" name="tf_tc_simple_options_lprrsp_taxsavings_ab_formula" value="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_ab_formula']) ? $options[$subkey . '_lprrsp_taxsavings_ab_formula'] : 'B13+(B3*B2*C7)-(B3*B2*0.5*C7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="F13" name="tf_tc_simple_options_lprrsp_cashoutlay_ab" data-formula="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_ab_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_ab_formula'] : 'B2-E13'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FF13" name="tf_tc_simple_options_lprrsp_cashoutlay_ab_formula" value="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_ab_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_ab_formula'] : 'B2-E13'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="H13" name="tf_tc_simple_options_lpdonation_taxsavings_ab" data-formula="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_ab_formula']) ? $options[$subkey . '_lpdonation_taxsavings_ab_formula'] : 'B13+(B3*B2*C8)-(B3*B2*0.5*C7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FH13" name="tf_tc_simple_options_lpdonation_taxsavings_ab_formula" value="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_ab_formula']) ? $options[$subkey . '_lpdonation_taxsavings_ab_formula'] : 'B13+(B3*B2*C8)-(B3*B2*0.5*C7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="I13" name="tf_tc_simple_options_lpdonation_cashoutlay_ab" data-formula="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_ab_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_ab_formula'] : 'B2-H13'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FI13" name="tf_tc_simple_options_lpdonation_cashoutlay_ab_formula" value="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_ab_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_ab_formula'] : 'B2-H13'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="K13" name="tf_tc_simple_options_lprrspcaploss_taxsavings_ab" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_ab_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_ab_formula'] : 'B13+(B3*B2*C7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FK13" name="tf_tc_simple_options_lprrspcaploss_taxsavings_ab_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_ab_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_ab_formula'] : 'B13+(B3*B2*C7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="L13" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_ab" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_ab_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_ab_formula'] : 'B2-K13'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FL13" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_ab_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_ab_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_ab_formula'] : 'B2-K13'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="N13" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_ab" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_ab_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_ab_formula'] : 'B13+(B3*B2*C8)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FN13" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_ab_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_ab_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_ab_formula'] : 'B13+(B3*B2*C8)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="O13" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_ab" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_ab_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_ab_formula'] : 'B2-N13'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FO13" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_ab_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_ab_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_ab_formula'] : 'B2-N13'; ?>"/>
						</td>
					</tr>
					<tr>
						<td>SK</td>
						<td>
							<input type="text" data-cell="B14" name="tf_tc_simple_options_terra_taxsavings_sk" data-formula="<?php echo isset($options[$subkey . '_terra_taxsavings_sk_formula']) ? $options[$subkey . '_terra_taxsavings_sk_formula'] : 'B2*D7+B2*0.9*B5*B4*(1-D7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FB14" name="tf_tc_simple_options_terra_taxsavings_sk_formula" value="<?php echo isset($options[$subkey . '_terra_taxsavings_sk_formula']) ? $options[$subkey . '_terra_taxsavings_sk_formula'] : 'B2*D7+B2*0.9*B5*B4*(1-D7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="C14" name="tf_tc_simple_options_terra_cashoutlay_sk" data-formula="<?php echo isset($options[$subkey . '_terra_cashoutlay_sk_formula']) ? $options[$subkey . '_terra_cashoutlay_sk_formula'] : 'B2-B14'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FC14" name="tf_tc_simple_options_terra_cashoutlay_sk_formula" value="<?php echo isset($options[$subkey . '_terra_cashoutlay_sk_formula']) ? $options[$subkey . '_terra_cashoutlay_sk_formula'] : 'B2-B14'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="E14" name="tf_tc_simple_options_lprrsp_taxsavings_sk" data-formula="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_sk_formula']) ? $options[$subkey . '_lprrsp_taxsavings_sk_formula'] : 'B14+(B3*B2*D7)-(B3*B2*0.5*D7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FE14" name="tf_tc_simple_options_lprrsp_taxsavings_sk_formula" value="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_sk_formula']) ? $options[$subkey . '_lprrsp_taxsavings_sk_formula'] : 'B14+(B3*B2*D7)-(B3*B2*0.5*D7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="F14" name="tf_tc_simple_options_lprrsp_cashoutlay_sk" data-formula="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_sk_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_sk_formula'] : 'B2-E14'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FF14" name="tf_tc_simple_options_lprrsp_cashoutlay_sk_formula" value="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_sk_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_sk_formula'] : 'B2-E14'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="H14" name="tf_tc_simple_options_lpdonation_taxsavings_sk" data-formula="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_sk_formula']) ? $options[$subkey . '_lpdonation_taxsavings_sk_formula'] : 'B14+(B3*B2*D8)-(B3*B2*0.5*D7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FH14" name="tf_tc_simple_options_lpdonation_taxsavings_sk_formula" value="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_sk_formula']) ? $options[$subkey . '_lpdonation_taxsavings_sk_formula'] : 'B14+(B3*B2*D8)-(B3*B2*0.5*D7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="I14" name="tf_tc_simple_options_lpdonation_cashoutlay_sk" data-formula="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_sk_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_sk_formula'] : 'B2-H14'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FI14" name="tf_tc_simple_options_lpdonation_cashoutlay_sk_formula" value="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_sk_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_sk_formula'] : 'B2-H14'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="K14" name="tf_tc_simple_options_lprrspcaploss_taxsavings_sk" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_sk_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_sk_formula'] : 'B14+(B3*B2*D7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FK14" name="tf_tc_simple_options_lprrspcaploss_taxsavings_sk_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_sk_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_sk_formula'] : 'B14+(B3*B2*D7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="L14" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_sk" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_sk_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_sk_formula'] : 'B2-K14'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FL14" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_sk_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_sk_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_sk_formula'] : 'B2-K14'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="N14" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_sk" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_sk_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_sk_formula'] : 'B14+(B3*B2*D8)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FN14" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_sk_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_sk_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_sk_formula'] : 'B14+(B3*B2*D8)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="O14" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_sk" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_sk_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_sk_formula'] : 'B2-N14'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FO14" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_sk_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_sk_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_sk_formula'] : 'B2-N14'; ?>"/>
						</td>
					</tr>
					<tr>
						<td>MB</td>
						<td>
							<input type="text" data-cell="B15" name="tf_tc_simple_options_terra_taxsavings_mb" data-formula="<?php echo isset($options[$subkey . '_terra_taxsavings_mb_formula']) ? $options[$subkey . '_terra_taxsavings_mb_formula'] : 'B2*E7+B2*0.9*B5*B4*(1-E7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FB15" name="tf_tc_simple_options_terra_taxsavings_mb_formula" value="<?php echo isset($options[$subkey . '_terra_taxsavings_mb_formula']) ? $options[$subkey . '_terra_taxsavings_mb_formula'] : 'B2*E7+B2*0.9*B5*B4*(1-E7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="C15" name="tf_tc_simple_options_terra_cashoutlay_mb" data-formula="<?php echo isset($options[$subkey . '_terra_cashoutlay_mb_formula']) ? $options[$subkey . '_terra_cashoutlay_mb_formula'] : 'B2-B15'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FC15" name="tf_tc_simple_options_terra_cashoutlay_mb_formula" value="<?php echo isset($options[$subkey . '_terra_cashoutlay_mb_formula']) ? $options[$subkey . '_terra_cashoutlay_mb_formula'] : 'B2-B15'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="E15" name="tf_tc_simple_options_lprrsp_taxsavings_mb" data-formula="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_mb_formula']) ? $options[$subkey . '_lprrsp_taxsavings_mb_formula'] : 'B15+(B3*B2*E7)-(B3*B2*0.5*E7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FE15" name="tf_tc_simple_options_lprrsp_taxsavings_mb_formula" value="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_mb_formula']) ? $options[$subkey . '_lprrsp_taxsavings_mb_formula'] : 'B15+(B3*B2*E7)-(B3*B2*0.5*E7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="F15" name="tf_tc_simple_options_lprrsp_cashoutlay_mb" data-formula="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_mb_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_mb_formula'] : 'B2-E15'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FF15" name="tf_tc_simple_options_lprrsp_cashoutlay_mb_formula" value="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_mb_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_mb_formula'] : 'B2-E15'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="H15" name="tf_tc_simple_options_lpdonation_taxsavings_mb" data-formula="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_mb_formula']) ? $options[$subkey . '_lpdonation_taxsavings_mb_formula'] : 'B15+(B3*B2*E8)-(B3*B2*0.5*E7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FH15" name="tf_tc_simple_options_lpdonation_taxsavings_mb_formula" value="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_mb_formula']) ? $options[$subkey . '_lpdonation_taxsavings_mb_formula'] : 'B15+(B3*B2*E8)-(B3*B2*0.5*E7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="I15" name="tf_tc_simple_options_lpdonation_cashoutlay_mb" data-formula="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_mb_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_mb_formula'] : 'B2-H15'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FI15" name="tf_tc_simple_options_lpdonation_cashoutlay_mb_formula" value="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_mb_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_mb_formula'] : 'B2-H15'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="K15" name="tf_tc_simple_options_lprrspcaploss_taxsavings_mb" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_mb_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_mb_formula'] : 'B15+(B3*B2*E7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FK15" name="tf_tc_simple_options_lprrspcaploss_taxsavings_mb_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_mb_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_mb_formula'] : 'B15+(B3*B2*E7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="L15" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_mb" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_mb_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_mb_formula'] : 'B2-K15'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FL15" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_mb_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_mb_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_mb_formula'] : 'B2-K15'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="N15" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_mb" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_mb_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_mb_formula'] : 'B15+(B3*B2*E8)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FN15" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_mb_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_mb_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_mb_formula'] : 'B15+(B3*B2*E8)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="O15" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_mb" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_mb_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_mb_formula'] : 'B2-N15'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FO15" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_mb_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_mb_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_mb_formula'] : 'B2-N15'; ?>"/>
						</td>
					</tr>
					<tr>
						<td>ON</td>
						<td>
							<input type="text" data-cell="B16" name="tf_tc_simple_options_terra_taxsavings_on" data-formula="<?php echo isset($options[$subkey . '_terra_taxsavings_on_formula']) ? $options[$subkey . '_terra_taxsavings_on_formula'] : 'B2*F7+B2*0.9*B5*B4*(1-F7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FB16" name="tf_tc_simple_options_terra_taxsavings_on_formula" value="<?php echo isset($options[$subkey . '_terra_taxsavings_on_formula']) ? $options[$subkey . '_terra_taxsavings_on_formula'] : 'B2*F7+B2*0.9*B5*B4*(1-F7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="C16" name="tf_tc_simple_options_terra_cashoutlay_on" data-formula="<?php echo isset($options[$subkey . '_terra_cashoutlay_on_formula']) ? $options[$subkey . '_terra_cashoutlay_on_formula'] : 'B2-B16'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FC16" name="tf_tc_simple_options_terra_cashoutlay_on_formula" value="<?php echo isset($options[$subkey . '_terra_cashoutlay_on_formula']) ? $options[$subkey . '_terra_cashoutlay_on_formula'] : 'B2-B16'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="E16" name="tf_tc_simple_options_lprrsp_taxsavings_on" data-formula="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_on_formula']) ? $options[$subkey . '_lprrsp_taxsavings_on_formula'] : 'B16+(B3*B2*F7)-(B3*B2*0.5*F7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FE16" name="tf_tc_simple_options_lprrsp_taxsavings_on_formula" value="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_on_formula']) ? $options[$subkey . '_lprrsp_taxsavings_on_formula'] : 'B16+(B3*B2*F7)-(B3*B2*0.5*F7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="F16" name="tf_tc_simple_options_lprrsp_cashoutlay_on" data-formula="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_on_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_on_formula'] : 'B2-E16'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FF16" name="tf_tc_simple_options_lprrsp_cashoutlay_on_formula" value="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_on_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_on_formula'] : 'B2-E16'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="H16" name="tf_tc_simple_options_lpdonation_taxsavings_on" data-formula="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_on_formula']) ? $options[$subkey . '_lpdonation_taxsavings_on_formula'] : 'B16+(B3*B2*F8)-(B3*B2*0.5*F7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FH16" name="tf_tc_simple_options_lpdonation_taxsavings_on_formula" value="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_on_formula']) ? $options[$subkey . '_lpdonation_taxsavings_on_formula'] : 'B16+(B3*B2*F8)-(B3*B2*0.5*F7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="I16" name="tf_tc_simple_options_lpdonation_cashoutlay_on" data-formula="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_on_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_on_formula'] : 'B2-H16'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FI16" name="tf_tc_simple_options_lpdonation_cashoutlay_on_formula" value="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_on_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_on_formula'] : 'B2-H16'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="K16" name="tf_tc_simple_options_lprrspcaploss_taxsavings_on" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_on_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_on_formula'] : 'B16+(B3*B2*F7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FK16" name="tf_tc_simple_options_lprrspcaploss_taxsavings_on_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_on_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_on_formula'] : 'B16+(B3*B2*F7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="L16" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_on" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_on_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_on_formula'] : 'B2-K16'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FL16" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_on_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_on_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_on_formula'] : 'B2-K16'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="N16" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_on" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_on_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_on_formula'] : 'B16+(B3*B2*F8)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FN16" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_on_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_on_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_on_formula'] : 'B16+(B3*B2*F8)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="O16" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_on" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_on_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_on_formula'] : 'B2-N16'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FO16" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_on_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_on_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_on_formula'] : 'B2-N16'; ?>"/>
						</td>
					</tr>
					<tr>
						<td>QC</td>
						<td>
							<input type="text" data-cell="B17" name="tf_tc_simple_options_terra_taxsavings_qc" data-formula="<?php echo isset($options[$subkey . '_terra_taxsavings_qc_formula']) ? $options[$subkey . '_terra_taxsavings_qc_formula'] : 'B2*G7+B2*0.9*B5*B4*(1-G7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FB17" name="tf_tc_simple_options_terra_taxsavings_qc_formula" value="<?php echo isset($options[$subkey . '_terra_taxsavings_qc_formula']) ? $options[$subkey . '_terra_taxsavings_qc_formula'] : 'B2*G7+B2*0.9*B5*B4*(1-G7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="C17" name="tf_tc_simple_options_terra_cashoutlay_qc" data-formula="<?php echo isset($options[$subkey . '_terra_cashoutlay_qc_formula']) ? $options[$subkey . '_terra_cashoutlay_qc_formula'] : 'B2-B17'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FC17" name="tf_tc_simple_options_terra_cashoutlay_qc_formula" value="<?php echo isset($options[$subkey . '_terra_cashoutlay_qc_formula']) ? $options[$subkey . '_terra_cashoutlay_qc_formula'] : 'B2-B17'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="E17" name="tf_tc_simple_options_lprrsp_taxsavings_qc" data-formula="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_qc_formula']) ? $options[$subkey . '_lprrsp_taxsavings_qc_formula'] : 'B17+(B3*B2*G7)-(B3*B2*0.5*G7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FE17" name="tf_tc_simple_options_lprrsp_taxsavings_qc_formula" value="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_qc_formula']) ? $options[$subkey . '_lprrsp_taxsavings_qc_formula'] : 'B17+(B3*B2*G7)-(B3*B2*0.5*G7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="F17" name="tf_tc_simple_options_lprrsp_cashoutlay_qc" data-formula="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_qc_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_qc_formula'] : 'B2-E17'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FF17" name="tf_tc_simple_options_lprrsp_cashoutlay_qc_formula" value="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_qc_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_qc_formula'] : 'B2-E17'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="H17" name="tf_tc_simple_options_lpdonation_taxsavings_qc" data-formula="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_qc_formula']) ? $options[$subkey . '_lpdonation_taxsavings_qc_formula'] : 'B17+(B3*B2*G8)-(B3*B2*0.5*G7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FH17" name="tf_tc_simple_options_lpdonation_taxsavings_qc_formula" value="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_qc_formula']) ? $options[$subkey . '_lpdonation_taxsavings_qc_formula'] : 'B17+(B3*B2*G8)-(B3*B2*0.5*G7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="I17" name="tf_tc_simple_options_lpdonation_cashoutlay_qc" data-formula="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_qc_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_qc_formula'] : 'B2-H17'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FI17" name="tf_tc_simple_options_lpdonation_cashoutlay_qc_formula" value="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_qc_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_qc_formula'] : 'B2-H17'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="K17" name="tf_tc_simple_options_lprrspcaploss_taxsavings_qc" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_qc_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_qc_formula'] : 'B17+(B3*B2*G7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FK17" name="tf_tc_simple_options_lprrspcaploss_taxsavings_qc_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_qc_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_qc_formula'] : 'B17+(B3*B2*G7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="L17" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_qc" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_qc_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_qc_formula'] : 'B2-K17'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FL17" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_qc_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_qc_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_qc_formula'] : 'B2-K17'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="N17" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_qc" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_qc_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_qc_formula'] : 'B17+(B3*B2*G8)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FN17" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_qc_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_qc_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_qc_formula'] : 'B17+(B3*B2*G8)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="O17" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_qc" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_qc_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_qc_formula'] : 'B2-N17'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FO17" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_qc_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_qc_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_qc_formula'] : 'B2-N17'; ?>"/>
						</td>
					</tr>
					<tr>
						<td>NS</td>
						<td>
							<input type="text" data-cell="B18" name="tf_tc_simple_options_terra_taxsavings_ns" data-formula="<?php echo isset($options[$subkey . '_terra_taxsavings_ns_formula']) ? $options[$subkey . '_terra_taxsavings_ns_formula'] : 'B2*H7+B2*0.9*B5*B4*(1-H7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FB18" name="tf_tc_simple_options_terra_taxsavings_ns_formula" value="<?php echo isset($options[$subkey . '_terra_taxsavings_ns_formula']) ? $options[$subkey . '_terra_taxsavings_ns_formula'] : 'B2*H7+B2*0.9*B5*B4*(1-H7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="C18" name="tf_tc_simple_options_terra_cashoutlay_ns" data-formula="<?php echo isset($options[$subkey . '_terra_cashoutlay_ns_formula']) ? $options[$subkey . '_terra_cashoutlay_ns_formula'] : 'B2-B18'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FC18" name="tf_tc_simple_options_terra_cashoutlay_ns_formula" value="<?php echo isset($options[$subkey . '_terra_cashoutlay_ns_formula']) ? $options[$subkey . '_terra_cashoutlay_ns_formula'] : 'B2-B18'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="E18" name="tf_tc_simple_options_lprrsp_taxsavings_ns" data-formula="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_ns_formula']) ? $options[$subkey . '_lprrsp_taxsavings_ns_formula'] : 'B18+(B3*B2*H7)-(B3*B2*0.5*H7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FE18" name="tf_tc_simple_options_lprrsp_taxsavings_ns_formula" value="<?php echo isset($options[$subkey . '_lprrsp_taxsavings_ns_formula']) ? $options[$subkey . '_lprrsp_taxsavings_ns_formula'] : 'B18+(B3*B2*H7)-(B3*B2*0.5*H7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="F18" name="tf_tc_simple_options_lprrsp_cashoutlay_ns" data-formula="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_ns_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_ns_formula'] : 'B2-E18'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FF18" name="tf_tc_simple_options_lprrsp_cashoutlay_ns_formula" value="<?php echo isset($options[$subkey . '_lprrsp_cashoutlay_ns_formula']) ? $options[$subkey . '_lprrsp_cashoutlay_ns_formula'] : 'B2-E18'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="H18" name="tf_tc_simple_options_lpdonation_taxsavings_ns" data-formula="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_ns_formula']) ? $options[$subkey . '_lpdonation_taxsavings_ns_formula'] : 'B18+(B3*B2*H8)-(B3*B2*0.5*H7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FH18" name="tf_tc_simple_options_lpdonation_taxsavings_ns_formula" value="<?php echo isset($options[$subkey . '_lpdonation_taxsavings_ns_formula']) ? $options[$subkey . '_lpdonation_taxsavings_ns_formula'] : 'B18+(B3*B2*H8)-(B3*B2*0.5*H7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="I18" name="tf_tc_simple_options_lpdonation_cashoutlay_ns" data-formula="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_ns_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_ns_formula'] : 'B2-H18'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FI18" name="tf_tc_simple_options_lpdonation_cashoutlay_ns_formula" value="<?php echo isset($options[$subkey . '_lpdonation_cashoutlay_ns_formula']) ? $options[$subkey . '_lpdonation_cashoutlay_ns_formula'] : 'B2-H18'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="K18" name="tf_tc_simple_options_lprrspcaploss_taxsavings_ns" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_ns_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_ns_formula'] : 'B18+(B3*B2*H7)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FK18" name="tf_tc_simple_options_lprrspcaploss_taxsavings_ns_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_taxsavings_ns_formula']) ? $options[$subkey . '_lprrspcaploss_taxsavings_ns_formula'] : 'B18+(B3*B2*H7)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="L18" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_ns" data-formula="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_ns_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_ns_formula'] : 'B2-K18'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FL18" name="tf_tc_simple_options_lprrspcaploss_cashoutlay_ns_formula" value="<?php echo isset($options[$subkey . '_lprrspcaploss_cashoutlay_ns_formula']) ? $options[$subkey . '_lprrspcaploss_cashoutlay_ns_formula'] : 'B2-K18'; ?>"/>
						</td>
						<td>
						</td>
						<td>
							<input type="text" data-cell="N18" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_ns" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_ns_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_ns_formula'] : 'B18+(B3*B2*H8)'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FN18" name="tf_tc_simple_options_lpdonationcaploss_taxsavings_ns_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_taxsavings_ns_formula']) ? $options[$subkey . '_lpdonationcaploss_taxsavings_ns_formula'] : 'B18+(B3*B2*H8)'; ?>"/>
						</td>
						<td>
							<input type="text" data-cell="O18" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_ns" data-formula="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_ns_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_ns_formula'] : 'B2-N18'; ?>" data-format="$0,0"/>
							<br/>
							<input type="text" data-cell="FO18" name="tf_tc_simple_options_lpdonationcaploss_cashoutlay_ns_formula" value="<?php echo isset($options[$subkey . '_lpdonationcaploss_cashoutlay_ns_formula']) ? $options[$subkey . '_lpdonationcaploss_cashoutlay_ns_formula'] : 'B2-N18'; ?>"/>
						</td>
					</tr>
					</tbody>
					</table> 

					<?php submit_button(); ?>
				</form>
				<div id="simple-plugin-reset" style="clear: both;">
				<form method="post" action="">
					<?php wp_nonce_field('simple-plugin-reset', 'simple-plugin-reset-nonce'); ?>
					<label style="font-weight:normal;">
						<?php printf(__('Do you wish to <strong>completely reset</strong> the default options for', 'simple-plugin')); ?> <?php echo $this->sub_title ?>? </label>
					<input class="button-primary" type="submit" name="simple_plugin_reset" value="Restore Defaults" />
				</form>
			</div>
				
		</div>
		<?php 
	}
	
	public function reset_options() {
		delete_option( $this->key );
	}
}
/**
 * Helper function to get/return the TF_Tax_Calc object
 * @since  0.1.0
 * @return TF_Tax_Calc object
 */
function tf_tax_calc() {
	return TF_Tax_Calc::get_instance();
}

// fire it up!
tf_tax_calc();
