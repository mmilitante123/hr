@extends('shared._public')

@section('title', 'Payroll: Payroll Template Parameter')

@section('styles')

@endsection

@section('content')

<!-- header -->
<div class="header--title_container">
	<div class="uk-container uk-container-center">
		<div class="container-title">
			<h1 class="page-title"><span class="uk-icon uk-icon-file-text"></span> Payroll Template Parameter </a></h1>
		</div>
	</div>
</div>

<!-- list payroll setup -->
<div class="uk-container uk-container-center">
	<div class="uk-grid main">
		<div class="uk-width-1-4">
			<ul class="uk-nav uk-nav-side uk-nav-parent-icon" data-uk-nav>
				<li><a href="{{ url('payroll/taxexemption') }}">Tax Exemption</a></li>
				<li><a href="{{ url('payroll/taxtable') }}">Tax Table</a></li>
				<li><a href="{{ url('payroll/annualtaxtable') }}">Annual Tax Table</a></li>
				<li><a href="{{ url('payroll/ssstable') }}">SSS Table</a></li>
				<li><a href="{{ url('payroll/pagibigtable') }}">Pagibig Table</a></li>
				<li><a href="{{ url('payroll/philhealthtable') }}">Philhealth Table</a></li>
				<li><a href="{{ url('payroll/paymentdisbursement') }}">Payment Disbursement</a></li>
				<li class="uk-parent"><a href="#">Payroll Details</a>
					<ul class="uk-nav-sub">
						<li><a href="{{ url('payroll/earnings') }}">Earnings</a></li>
						<li><a href="{{ url('payroll/deductions') }}">Deductions</a></li>
					</ul>
				</li>
				<li><a href="{{ url('payroll/payrollmode') }}">Payroll Mode</a></li>
				<li><a href="{{ url('payroll/payrollperiod') }}">Payroll Period</a></li>
				<li><a href="{{ url('payroll/payrollgroup') }}">Payroll Group</a></li>
				<li class="uk-active"><a href="{{ url('payroll/payrolltemplate') }}">Payroll Template Parameter</a></li>
				<li><a href="{{ url('payroll/payrollsignatory') }}">Payroll Signatory</a></li>
				<li><a href="{{ url('payroll/overtimeparamenter') }}">Overtime Parameter</a></li>
				<li><a href="{{ url('payroll/wageorder') }}">Wage Order</a></li>
			</ul>
		</div> <!-- payroll parameter list -->

		<div class="uk-width-3-4" >
			<main>
				<article class="uk-article">
					<div class="clearfix"></div>
					<div class="uk-panel template-parameter">
    					<form class="uk-form uk-form-horizontal" method="post" action="{{ url('payroll/payrolltemplate') }}" >
    						{{csrf_field()}}
    						<div class="uk-grid">
								<div class="uk-width-1-4">
									<div class="uk-form-row">
										<label class="uk-form-label">Amount Exempted</label>
									</div>
								</div>
								<div class="uk-width-3-4">
									<div class="uk-form-row">
										<div class="uk-form-controls">
											<input type="text" class="uk-form-width-medium" name="exempted_amt" value="{{ $payroll_templates->exempted_amt or null }}">
										</div>
									</div>
								</div>
							</div>
							<div class="uk-grid">
								<div class="uk-width-1-4">
									<div class="uk-form-row">
										<label class="uk-form-label">Take Home Net Pay</label>
									</div>
								</div>
								<div class="uk-width-3-4">
									<div class="uk-form-row">
										<div class="uk-form-controls">
										@if  ($payroll_templates != null && $payroll_templates->net_takehome_flag == 'fixed' )
											<input class="uk-form-width-mini" name="net_takehome_flag" id="fixed_flag" value="fixed" type="radio" checked="checked" >
											<span class="tmp-parameter_netpay-desc" >In Fixed Amount </span>
											<input class="uk-form-width-medium" name="net_takehome_amt" id="fixed_amt" type="text"  value="{{$payroll_templates->net_takehome_amt or null}}">
										@elseif ($payroll_templates == null)
											<input class="uk-form-width-mini" name="net_takehome_flag" id="fixed_flag" value="fixed" type="radio" checked="checked" >
											<span class="tmp-parameter_netpay-desc" >In Fixed Amount </span>
											<input class="uk-form-width-medium" name="net_takehome_amt" id="fixed_amt" type="text"  value="{{$payroll_templates->net_takehome_amt or null}}">
										@else
											<input class="uk-form-width-mini" name="net_takehome_flag" id="fixed_flag" value="fixed" type="radio" >
											<span class="tmp-parameter_netpay-desc" >In Fixed Amount </span>
											<input class="uk-form-width-medium" name="net_takehome_amt" id="fixed_amt" type="text">
										@endif
										</div>
									</div>
									<div class="uk-form-row">
										<div class="uk-form-controls">
										@if  ($payroll_templates != null && $payroll_templates->net_takehome_flag == 'percent')
											<input class="uk-form-width-mini" name="net_takehome_flag" id="percent_flag" value="percent" type="radio" checked="checked">
											<span class="tmp-parameter_netpay-desc" >Percentage </span>
											<input class="uk-form-width-medium" name="net_takehome_amt" id="percent_amt" type="text" value="{{$payroll_templates->net_takehome_amt or null}}">
										@else
											<input class="uk-form-width-mini" name="net_takehome_flag" id="percent_flag" value="percent" type="radio" >
											<span class="tmp-parameter_netpay-desc" >Percentage </span>
											<input class="uk-form-width-medium" name="net_takehome_amt" id="percent_amt" type="text">
										@endif
										</div>
									</div>
								</div>
							</div>

							<div class="uk-grid">
								<div class="uk-width-1-4">
									<div class="uk-form-row">
										<label class="uk-form-label">Anualized Income</label>
									</div>
								</div>
								<div class="uk-width-3-4">
									<div class="uk-form-row">
										<div class="uk-form-controls">
											<div style="">
												@if($payroll_templates != null && $payroll_templates->annualize_income_mo != null)
													<label>{{ Form::checkbox("notapplicable", "", false, ['id' => 'notapplicable']) }} Not Applicable </label><br>
												@else
													<label>{{ Form::checkbox("notapplicable", "", true, ['id' => 'notapplicable']) }} Not Applicable </label><br>
												@endif
											</div>
										</div>
										<div class="uk-grid uk-form-controls">
											<div class="uk-width-medium-1-4 annualized-income">
												@if  ($payroll_templates != null && $payroll_templates->annualize_income_mo == 'jan')
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="jan" type="radio" checked="checked"> Jan</label><br>
												@elseif($payroll_templates == null)
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="jan" type="radio" checked="checked"> Jan</label><br>
												@else
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="jan" type="radio" > Jan</label><br>
												@endif
												@if  ( $payroll_templates != null && $payroll_templates->annualize_income_mo == 'feb')
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="feb" type="radio" checked="checked"> Feb</label><br>
												@else
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="feb" type="radio" > Feb</label><br>
												@endif
										        @if  ($payroll_templates != null && $payroll_templates->annualize_income_mo == 'mar')
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="mar" type="radio" checked="checked"> Mar</label><br>
												@else
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="mar" type="radio" > Mar</label><br>
												@endif
										    </div>
										<div class="uk-width-medium-1-4 annualized-income">
										          @if  ($payroll_templates != null && $payroll_templates->annualize_income_mo == 'apr')
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="apr" type="radio" checked="checked"> Apr</label><br>
												@else
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="apr" type="radio" > Apr</label><br>
												@endif
										          @if  ($payroll_templates != null && $payroll_templates->annualize_income_mo == 'may')
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="may" type="radio" checked="checked"> May</label><br>
												@else
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="may" type="radio" > May</label><br>
												@endif
										        @if  ($payroll_templates != null && $payroll_templates->annualize_income_mo == 'jun')
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="jun" type="radio" checked="checked"> Jun</label><br>
												@else
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="jun" type="radio" > Jun</label><br>
												@endif
										    </div>
										<div class="uk-width-medium-1-4 annualized-income">
												@if  ($payroll_templates != null && $payroll_templates->annualize_income_mo == 'jul')
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="jul" type="radio" checked="checked"> Jul</label><br>
												@else
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="jul" type="radio" > Jul</label><br>
												@endif
												@if  ($payroll_templates != null && $payroll_templates->annualize_income_mo == 'aug')
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="aug" type="radio" checked="checked"> Aug</label><br>
												@else
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="aug" type="radio" > Aug</label><br>
												@endif
										    	@if  ($payroll_templates != null && $payroll_templates->annualize_income_mo == 'sep')
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="sep" type="radio" checked="checked"> Sept</label><br>
												@else
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="sep" type="radio" > Sept</label><br>
												@endif

										    </div>
										<div class="uk-width-medium-1-4 annualized-income">
												@if  ($payroll_templates != null && $payroll_templates->annualize_income_mo == 'oct')
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="oct" type="radio" checked="checked"> Oct</label><br>
												@else
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="oct" type="radio" > Oct</label><br>
												@endif
												@if  ($payroll_templates != null && $payroll_templates->annualize_income_mo == 'nov')
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="nov" type="radio" checked="checked"> Nov</label><br>
												@else
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="nov" type="radio" > Nov</label><br>
												@endif
										        @if  ($payroll_templates != null && $payroll_templates->annualize_income_mo == 'dec')
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="dec" type="radio" checked="checked"> Dec</label><br>
												@else
													<label><input class="uk-form-width-mini" name="annualize_income_mo" value="dec" type="radio" > Dec</label><br>
												@endif
										    </div>
										    <p class="uk-form-help-block annualized-income"><i>Please click applicable month</i><p>
										</div><hr>
									</div>
								</div>
							</div>

							<div class="uk-grid">
								<div class="uk-width-1-4">
									<div class="uk-form-row">
										<label class="uk-form-label">Deduct Late From</label>
									</div>
								</div>
								<div class="uk-width-3-4">
									<div class="uk-form-row">
										<div class="uk-form-controls">
											<label>
											@if  ($payroll_templates != null && $payroll_templates->ded_late_flag == 'B')
												<input class="uk-form-width-mini" name="ded_late_flag"  value="B" type="radio" placeholder="" checked="checked">
											@elseif($payroll_templates == null)
												<input class="uk-form-width-mini" name="ded_late_flag"  value="B" type="radio" placeholder="" checked="checked">
											@else
											<input class="uk-form-width-mini" name="ded_late_flag"  value="B" type="radio" placeholder="">
											@endif
											Basic</label>
											<label>
											@if  ($payroll_templates != null && $payroll_templates->ded_late_flag == 'E')
												<input class="uk-form-width-mini" name="ded_late_flag"  value="E" type="radio" placeholder="" checked="checked">
											@else
											<input class="uk-form-width-mini" name="ded_late_flag"  value="E" type="radio" placeholder="">
											@endif
											Ecola</label>
											@if  ($payroll_templates != null && $payroll_templates->ded_late_flag == 'A')
												<input class="uk-form-width-mini" name="ded_late_flag"  value="A" type="radio" placeholder="" checked="checked">
											@else
											<input class="uk-form-width-mini" name="ded_late_flag"  value="A" type="radio" placeholder="">
											@endif
											Allowance</label>
									</div>
									</div>
								</div>
							</div>

							<div class="uk-grid">
								<div class="uk-width-1-4">
									<div class="uk-form-row">
										<label class="uk-form-label">Deduct Under-time From</label>
									</div>
								</div>
								<div class="uk-width-3-4">
									<div class="uk-form-row">
										<div class="uk-form-controls">

											@if  ($payroll_templates != null && $payroll_templates->ded_utime_flag == 'B')
												<input class="uk-form-width-mini" name="ded_utime_flag"  value="B" type="radio" placeholder="" checked="checked">
											@elseif($payroll_templates == null)
												<input class="uk-form-width-mini" name="ded_utime_flag"  value="B" type="radio" placeholder="" checked="checked">
											@else
											<input class="uk-form-width-mini" name="ded_utime_flag"  value="B" type="radio" placeholder="">
											@endif
											Basic</label>
											<label>
											@if  ($payroll_templates != null && $payroll_templates->ded_utime_flag == 'E')
												<input class="uk-form-width-mini" name="ded_utime_flag"  value="E" type="radio" placeholder="" checked="checked">
											@else
											<input class="uk-form-width-mini" name="ded_utime_flag"  value="E" type="radio" placeholder="">
											@endif
											Ecola</label>
											@if  ($payroll_templates != null && $payroll_templates->ded_utime_flag == 'A')
												<input class="uk-form-width-mini" name="ded_utime_flag"  value="A" type="radio" placeholder="" checked="checked">
											@else
											<input class="uk-form-width-mini" name="ded_utime_flag"  value="A" type="radio" placeholder="">
											@endif
											Allowance</label>
										</div>
									</div>
								</div>
							</div>

							<div class="uk-grid">
								<div class="uk-width-1-4">
									<div class="uk-form-row">
										<label class="uk-form-label">Deduct Absent From</label>
									</div>
								</div>
								<div class="uk-width-3-4">
									<div class="uk-form-row">
										<div class="uk-form-controls">
											@if  ($payroll_templates != null && $payroll_templates->ded_absent_flag == 'B')
												<input class="uk-form-width-mini" name="ded_absent_flag"  value="B" type="radio" placeholder="" checked="checked">
											@elseif($payroll_templates == null)
												<input class="uk-form-width-mini" name="ded_absent_flag"  value="B" type="radio" placeholder="" checked="checked">
											@else
											<input class="uk-form-width-mini" name="ded_absent_flag"  value="B" type="radio" placeholder="">
											@endif
											Basic</label>
											<label>
											@if  ($payroll_templates != null && $payroll_templates->ded_absent_flag == 'E')
												<input class="uk-form-width-mini" name="ded_absent_flag"  value="E" type="radio" placeholder="" checked="checked">
											@else
											<input class="uk-form-width-mini" name="ded_absent_flag"  value="E" type="radio" placeholder="">
											@endif
											Ecola</label>
											@if  ($payroll_templates != null && $payroll_templates->ded_absent_flag == 'A')
												<input class="uk-form-width-mini" name="ded_absent_flag"  value="A" type="radio" placeholder="" checked="checked">
											@else
											<input class="uk-form-width-mini" name="ded_absent_flag"  value="A" type="radio" placeholder="">
											@endif
											Allowance</label>

										</div>
									</div>
								</div>
							</div>

							<div class="uk-grid">
								<div class="uk-width-1-4">
									<div class="uk-form-row">
										<label class="uk-form-label">Accumulated minutes to deduct late</label>
									</div>
								</div>
								<div class="uk-width-3-4">
									<div class="uk-form-row">
										<div class="uk-form-controls">
											<input type="text" name="min_ded_late" class="uk-form-width-medium" placeholder="" value={{$payroll_templates->min_ded_late or null}}>
										</div>
									</div>
								</div>
							</div>

							<div class="uk-grid">
								<div class="uk-width-1-4">
									<div class="uk-form-row">
										<label class="uk-form-label">Daily Time Records (DTR)</label>
									</div>
								</div>
								<div class="uk-width-3-4">
									<div class="uk-form-row">
											<div class="uk-form-controls">
											@if  ($payroll_templates != null && $payroll_templates->with_dtr_flag == 'No')
											    <label><input  class="uk-form-width-mini" name="with_dtr_flag" value="No" type="radio"  checked="checked" > No DTR</label>
											@elseif($payroll_templates == null)
											    <label><input  class="uk-form-width-mini" name="with_dtr_flag" value="No" type="radio"  checked="checked" > No DTR</label>
											@else
												<label><input  class="uk-form-width-mini" name="with_dtr_flag" value="No" type="radio" > No DTR</label>
											@endif

											@if  ($payroll_templates != null && $payroll_templates->with_dtr_flag == 'Yes')
												<label><input class="uk-form-width-mini"  name="with_dtr_flag" value="Yes" type="radio" checked="checked"> Use DTR</label>
											@else
												<label><input class="uk-form-width-mini"  name="with_dtr_flag" value="Yes" type="radio" > Use DTR</label>
											@endif

											@if  ($payroll_templates != null && $payroll_templates->with_dtr_flag == 'TCS')
											    <label><input  class="uk-form-width-mini" name="with_dtr_flag" value="TCS" type="radio"  checked="checked"> Use Time Card Summary</label>
											@else
												<label><input  class="uk-form-width-mini" name="with_dtr_flag" value="TCS" type="radio"  > Use Time Card Summary</label>
											@endif

											</div>
									</div>
								</div>
							</div>
							<!-- 20161020 updated by Melvin Militante; Reason: choices for sss, hdmf, and philhealth schedule of deduction should pertain to cut off (payroll run) -->
							<div class="uk-grid">
								<div class="uk-width-1-4">
									<div class="uk-form-row">
										<label class="uk-form-label">SSS Schedule of Deduction</label>
									</div>
								</div>
								<div class="uk-width-3-4">
									<div class="uk-form-row">
											<div class="uk-form-controls">
											@if ($payroll_templates != null)
											     {{ Form::select('sss_sched', ['1' => '1st cut off', '2' => '2nd cut off','5'=>'Every pay run'] , $payroll_templates->sss_sched, array('class'=>'uk-form-width-medium')) }}
											 @else
												 {{ Form::select('sss_sched', ['1' => '1st cut off', '2' => '2nd cut off','5'=>'Every pay run'] , '', array('class'=>'uk-form-width-medium')) }}
											 @endif
											</div>
									</div>
								</div>
							</div>

							<div class="uk-grid">
								<div class="uk-width-1-4">
									<div class="uk-form-row">
										<label class="uk-form-label">HDMF Schedule of Deduction</label>
									</div>
								</div>
								<div class="uk-width-3-4">
									<div class="uk-form-row">
											<div class="uk-form-controls">
												@if ($payroll_templates != null)
											     {{ Form::select('pagibig_sched', ['1' => '1st cut off', '2' => '2nd cut off','5'=>'Every pay run'] , $payroll_templates->pagibig_sched, array('class'=>'uk-form-width-medium')) }}
											 @else
												 {{ Form::select('pagibig_sched', ['1' => '1st cut off', '2' => '2nd cut off','5'=>'Every pay run'] , '', array('class'=>'uk-form-width-medium')) }}
											 @endif

											</div>
									</div>
								</div>
							</div>

							<div class="uk-grid">
								<div class="uk-width-1-4">
									<div class="uk-form-row">
										<label class="uk-form-label">Philhealth Schedule of Deduction</label>
									</div>
								</div>
								<div class="uk-width-3-4">
									<div class="uk-form-row">
											<div class="uk-form-controls">
												@if ($payroll_templates != null)
											     {{ Form::select('philhealth_sched', ['1' => '1st cut off', '2' => '2nd cut off','5'=>'Every pay run'] , $payroll_templates->philhealth_sched, array('class'=>'uk-form-width-medium')) }}
											 @else
												 {{ Form::select('philhealth_sched', ['1' => '1st cut off', '2' => '2nd cut off','5'=>'Every pay run'] , '', array('class'=>'uk-form-width-medium')) }}
											 @endif


												</select>
											</div>
									</div>
								</div>
							</div>
							<!-- 20161020 end of update -->
							<div class="uk-modal-footer uk-text-right form-buttons">
								<button class="uk-button btn-save" type="submit"><span class="uk-icon uk-icon-edit"></span> Save</button>
								<a href="{{url('payroll/taxexemption')}}" class="uk-button uk-modal-close btn-cancel"><span class="uk-icon uk-icon-times-circle"></span> Cancel</a>
							</div>
						</form>
					</div>
				</article>
				<div class="clearfix"></div>
			</main>
		</div>
	</div> <!-- grid -->
</div> <!-- container -->






@endsection

@section('scripts')

<script type="text/javascript" language="javascript" src="{{asset('js/table.js')}}"></script>
<script type="text/javascript" class="init">
	$(document).ready(
		function() {
			$('#tax_exemption').DataTable();

			if(document.getElementById('fixed_flag').checked==true) {
				document.getElementById('percent_amt').disabled=true;
				document.getElementById('fixed_amt').disabled=false;
			}
			else {
				document.getElementById('percent_amt').disabled=false;
				document.getElementById('fixed_amt').disabled=true;
			}


			if(document.getElementById('notapplicable').checked) {
				var radios = document.getElementsByName('annualize_income_mo');
				for (var i = 0; i< radios.length;  i++){
					radios[i].disabled = true;
				}
			}
			else {
				var radios = document.getElementsByName('annualize_income_mo');
				for (var i = 0; i< radios.length;  i++){
					radios[i].disabled = false;
				}
			}

			$('#notapplicable').on('click', function() {
				if(document.getElementById('notapplicable').checked) {
					var radios = document.getElementsByName('annualize_income_mo');
					for (var i = 0; i< radios.length;  i++){
						radios[i].disabled = true;
					}
				}
				else {
					var radios = document.getElementsByName('annualize_income_mo');
					for (var i = 0; i< radios.length;  i++){
						radios[i].disabled = false;
					}
				}
			});

			$('#fixed_flag').on('click', function () {

				document.getElementById('percent_amt').disabled=true;
				document.getElementById('fixed_amt').disabled=false;
			});
			$('#percent_flag').on('click', function () {

				document.getElementById('percent_amt').disabled=false;
				document.getElementById('fixed_amt').disabled=true;
			});
		}
	);
</script>

@endsection