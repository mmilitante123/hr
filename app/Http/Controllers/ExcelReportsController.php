<?php

/*@author Carlo Mendoza*/

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\tbl_user_model;
use Excel;
use PHPExcel; 
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet_Drawing;
use PHPEXCEL_STYLE_ALIGNMENT;
use DB;

class ExcelReportsController extends Controller
{	
    /*
	 *	 Process the Pagibig Report
	 *
	 *   @param int $id
	 */

	public function getHdmf($id) { 
		
		$path = (public_path().'/excels/HDMF_Template.xltx');
		$workbook = new PHPExcel();
		$workbook = PHPExcel_IOFactory::load($path);
		$workbook -> setActiveSheetIndex(0);
		$workbook -> getProperties() -> setCreator("Carlo Mendoza")
									 -> setTitle("HDMF");	

		$workbook -> getActiveSheet() -> getPageSetup() -> setFitToWidth(1)
													    -> setFitToHeight(0);
		$worksheet = $workbook -> getActiveSheet();
		
		$worksheet  -> getColumnDimension('A') -> setWidth(22);
		$worksheet  -> getColumnDimension('B') -> setWidth(12);
		$worksheet  -> getColumnDimension('C') -> setWidth(13);
		$worksheet  -> getColumnDimension('D') -> setWidth(16);
		$worksheet  -> getColumnDimension('E') -> setWidth(16);
		$worksheet  -> getColumnDimension('F') -> setWidth(16);
		$worksheet  -> getColumnDimension('G') -> setWidth(16);
		$worksheet  -> getColumnDimension('H') -> setWidth(30);
		$worksheet  -> getColumnDimension('I') -> setWidth(11);
		$worksheet  -> getColumnDimension('J') -> setWidth(11);
		$worksheet  -> getColumnDimension('K') -> setWidth(14);		  

		$d_today = date("Y/m/d");
		$index = 6;
//QUERY
		$results = db::select(
				   db::raw("
							select      c.company_name,
										e.hdmf_no,
										c.address,
										c.city,
										upper(e.last_name) as last_name,
										upper(e.first_name) as first_name,
										upper(e.middle_name) as middle_name,
										TO_CHAR(p.date_to,'MM-DD-YYYY') as date_to, 
										TO_CHAR(p.date_from,'MM-DD-YYYY')as date_from,
										sum(case
											when p. entry_type = 'DB' and p.payroll_element_id = '5' 
											and e.employee_id = p.employee_id then entry_amt
											else 0
											end
										)emp_share,
										sum(case
											when p.entry_type = 'EE' and p.payroll_element_id = '11' 
											and e.employee_id = p.employee_id then entry_amt
											else 0
											end
										)comp_share

							from 		hr.tbl_employee e
							join 		hr.tbl_company  c on e.company_id = c.company_id 
							join 		hr.tbl_payroll  p on e.company_id = p.company_id
							where  		c.company_id = '$id' and e.active_flag = 'Y'
										and e.employee_id  = p.employee_id
							group by 	c.company_name, 
										c.address, 
										c.city, 
										e.last_name,
										e.first_name, 
										e.middle_name, 
										p.date_to, 
										p.date_from,
										e.hdmf_no
								")
							);

		$dbcount = count($results);

		foreach ($results as $result) {
//EXCEL POSITION					
			$acell = ('A'.$index);
			$ccell = ('C'.$index);
			$dcell = ('D'.$index);
			$ecell = ('E'.$index);
			$fcell = ('F'.$index);
			$gcell = ('G'.$index);
			$hcell = ('H'.$index);
			$icell = ('I'.$index);
			$jcell = ('J'.$index);
//QUERY RESULT
			$worksheet -> setCellValue('B2',   $result -> company_name )
					   -> setCellValue('B3',   $result -> address. '  '. $result -> city)
					   -> setCellValue($acell, $result -> hdmf_no)
					   -> setCellValue($dcell, $result -> last_name)
					   -> setCellValue($ecell, $result -> first_name)
					   -> setCellValue($gcell, $result -> middle_name)
					   -> setCellValue($hcell, $result -> date_from.'  -  '.$result -> date_to)
					   -> setCellValue($icell, $result -> emp_share)
					   -> setCellValue($jcell, $result -> comp_share);
			$worksheet -> getStyle($icell)
					   -> getNumberFormat()
					   -> setFormatCode('0.00');
			$worksheet -> getStyle($jcell)
					   -> getNumberFormat()
					   -> setFormatCode('0.00'); 
//ITERATOR				
		$index = $index + 1; 
			}

			$dyn_val 	= ($dbcount + 5);
			$dyn_header = ('A5'.':'.'K'.$dyn_val);
			$dyn_border = ('A6'.':'.'K'.$dyn_val);
			$dyn_align  = ('A6'.':'.'B'.$dyn_val);
			$dyn_align2 = ('G6'.':'.'K'.$dyn_val);
//STYLES
			$white 	  = new Styles;
			$vhcenter = new Styles;
			$black 	  = new Styles;
			$insthin  = new Styles;
//DYNAMIC STYLES
			$worksheet 					   -> getStyle($dyn_header) -> applyFromArray($black -> getBlack())
					   -> getActiveSheet() -> getStyle($dyn_border) -> applyFromArray($insthin -> getInsThin())
					   -> getActiveSheet() -> getStyle($dyn_align)  -> applyFromArray($vhcenter -> getVertHoriCenter())
					   -> getActiveSheet() -> getStyle($dyn_align2) -> applyFromArray($vhcenter -> getVertHoriCenter());

		// foreach (range('A', $workbook -> getActiveSheet() -> getHighestDataColumn()) as $col) {
  //       		$workbook -> getActiveSheet()
  //               		  -> getColumnDimension($col)
  //              			  -> setAutoSize(true);
  //  				 } 
		$objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
		// // We'll be outputting an excel file
		header('Content-type: application/vnd.ms-excel');
		// // It will be called file.xls
		header('Content-Disposition: attachment; filename="HDMF_'.$d_today.'.xls"');
		// Write file to the browser
		$objWriter -> save('php://output');
		$workbook -> disconnectWorksheets();
		unset($workbook);


		
// 		$file_type = 'xls';
// 		$data =	
// 				[
// 				['EMPLOYER ID'],
// 				['EMPLOYER NAME'],
// 				['ADDRESS'],
// 				['MEMBERSHIP PROG'],
// 				[],
// 				['Pag-IBIG ID','RTN	ACCOUNT NO','LAST NAME',
// 				'FIRST NAME','NAME EXTENSION','MIDDLE NAME',
// 				'PERIOD COVERED','EE SHARE','ER SHARE','REMARKS'],
// 				];

// 		excel::create('HDMF_'.$d_today, function($hdmf) use ($data, $id) {

// 			$hdmf -> sheet('HDMF_Sheet', function($sheet) use ($data, $id) {	

// 				$sheet -> fromArray($data);
// 				$sheet -> setStyle(array(
// 					'font' => array(
// 						'name' => 'Calibri',
// 						'size' => '11',
// 						)
// 					));
// //STYLES
// 				$white 	  = new Styles;
// 				$vhcenter = new Styles;
// 				$black 	  = new Styles;
// 				$insthin  = new Styles;

// 				$sheet -> getStyle('A1:J6') -> applyFromArray($white -> getWhite());
// 				$sheet -> getStyle('A2:A5') -> getFont() -> setSize(12);
// 				$sheet -> getStyle('A2:A5') -> getFont() -> setBold(true);
// 				$sheet -> getStyle('A7:J7') -> getFont() -> setSize(12);
// 				$sheet -> getStyle('A7:J7') -> getFont() -> setBold(true);
// 				$sheet -> getStyle('A7:J7') -> applyFromArray($vhcenter -> getVertHoriCenter());
// 				$sheet -> getStyle('A7:J7') -> applyFromArray($black -> getBlack());
// 				$sheet -> getStyle('A7:J7') -> applyFromArray($insthin -> getInsThin());				
//                 $sheet -> setWidth(array(
//                 	'A' => 23, 'B' => 20, 'C' => 20, 'D' => 20,
//                 	'E' => 20, 'F' => 20, 'G' => 23, 'H' => 12, 
//                 	'I' => 12, 'J' => 17
//                 	                    )
//                 				  );
// 				$index = 8;
// 				$results = db::select(
// 						   db::raw("
// 									select   	c.company_name,
// 												e.hdmf_no,
// 												c.address,
// 												c.city,
// 												upper(e.last_name) as last_name,
// 												upper(e.first_name) as first_name,
// 												upper(e.middle_name) as middle_name,
// 												TO_CHAR(p.date_to,'MM-DD-YYYY') as date_to, 
// 												TO_CHAR(p.date_from,'MM-DD-YYYY')as date_from,
// 												sum(case
// 													when p.entry_type = 'DB' and p.payroll_element_id = '5' 
// 																			 and e.employee_id 		  = p.employee_id then entry_amt
// 													else 0
// 												end
// 												)emp_share,
// 												sum(case
// 													when p.entry_type = 'EE' and p.payroll_element_id = '11' 
// 																	 		 and e.employee_id 	      = p.employee_id then entry_amt
// 													else 0
// 												end
// 												)comp_share
// 									from 		hr.tbl_employee e
// 									join 		hr.tbl_company  c on e.company_id = c.company_id 
// 									join 		hr.tbl_payroll  p on e.company_id = p.company_id
// 									where  		c.company_id = '$id' and e.active_flag = 'Y'

// 									group by 	c.company_name, 
// 												c.address, 
// 												c.city, 
// 												e.last_name,
// 												e.first_name, 
// 												e.middle_name, 
// 												p.date_to, 
// 												p.date_from,
// 												e.hdmf_no
// 									")
// 								   );

// 				$dbcount = count($results);

// 				foreach ($results as $result) {
// //EXCEL POSITION					
// 					$acell = ('A'.$index);
// 					$ccell = ('C'.$index);
// 					$dcell = ('D'.$index);
// 					$fcell = ('F'.$index);
// 					$gcell = ('G'.$index);
// 					$hcell = ('H'.$index);
// 					$icell = ('I'.$index);

// 					$sheet -> setCellValue('B3',   $result -> company_name );
// 					$sheet -> setCellValue('B4',   $result -> address. '  '. $result -> city);
// 					$sheet -> setCellValue($acell, $result -> hdmf_no);
// 					$sheet -> setCellValue($ccell, $result -> last_name);
// 					$sheet -> setCellValue($dcell, $result -> first_name);
// 					$sheet -> setCellValue($fcell, $result -> middle_name);
// 					$sheet -> setCellValue($gcell, $result -> date_from.'  -  '.$result -> date_to);
// 					$sheet -> setCellValue($hcell, $result -> emp_share);
// 					$sheet -> getStyle($hcell)
// 					       -> getNumberFormat()
// 					       -> setFormatCode('0.00'); 
// 					$sheet -> setCellValue($icell, $result -> comp_share);
// 					$sheet -> getStyle($icell)
// 					       -> getNumberFormat()
// 					       -> setFormatCode('0.00'); 
// 				$index = $index + 1; //ITERATOR
// 				}

// 				$dyn_val 	= (7 + $dbcount);
// 				$dyn_border = ('A8'.':'.'J'.$dyn_val);
// 				$dyn_align  = ('A8'.':'.'B'.$dyn_val);
// 				$dyn_align2 = ('G8'.':'.'J'.$dyn_val);
// 				$sheet -> getStyle($dyn_border) -> applyFromArray($black -> getBlack());
// 				$sheet -> getStyle($dyn_border) -> applyFromArray($insthin -> getInsThin());
// 				$sheet -> getStyle($dyn_align)  -> applyFromArray($vhcenter -> getVertHoriCenter());
// 				$sheet -> getStyle($dyn_align2)  -> applyFromArray($vhcenter -> getVertHoriCenter());

// 			});

// 	})->download($file_type);

}

	public function getPayroll($id) {


	$path = (public_path().'/excels/PayrollRegister_Template.xltx');
	$workbook = new PHPExcel();
	$workbook = PHPExcel_IOFactory::load($path);
	$workbook -> getProperties() -> setCreator("Carlo Mendoza");
	$workbook -> getProperties() -> setTitle("Payroll Register");	
	$workbook -> setActiveSheetIndex(0);
	$workbook -> getActiveSheet() -> getPageSetup() -> setFitToWidth(1);
	$workbook -> getActiveSheet() -> getPageSetup() -> setFitToHeight(0);

	$worksheet = $workbook -> getActiveSheet();
	
	$d_today = date("Y/m/d");
	$index = 2;

	$results = db::select(
		   	   db::raw("
		   				select 	e.employee_number,
						       (e.last_name || ', ' || e.first_name || '  ' ||e.middle_name) as employee,
-- GROSS PAY
								sum(case
							    	when p.entry_type = 'CR' and p.payroll_element_id = '1' then entry_amt
							    	else 0
						        	end
						    	)gross,
-- SSS
			   					sum(case
			   						when p.entry_type = 'DB' and p.payroll_element_id = '2' then entry_amt
			   						else 0
			   						end
			   					)sss,
-- PHILHEALTH
			   		        	sum(case 
			   		                when p.entry_type = 'DB' and p.payroll_element_id = '4' then entry_amt
			   		                else 0
			   		            	end
			   		        	)philhealth,
-- PAGIBIG HDMF
			   			    	sum(case 
			   		                when p.entry_type = 'DB' and p.payroll_element_id = '5' then entry_amt
			   		                else 0
			   		            	end
			   		        	)hdmf,
-- TAXABLE INCOME
			   					sum(case
									when p.entry_type = 'CR' and p.payroll_element_id = '1' THEN entry_amt
									else 0
									end) -
								sum(case
									when p.entry_type = 'DB'        and p.payroll_element_id = '2'
									or   p.payroll_element_id = '4' or  p.payroll_element_id = '5'
									 THEN entry_amt
									else 0
									end) 
								taxinc,
-- WITHOLDING TAX
								sum(case
									when p.entry_type ='DB' and p.payroll_element_id ='12' then entry_amt
									else 0
									end
								)withtax,

-- NETPAYNOABSENCE
								sum(case
									when p.entry_type = 'CR' and p.payroll_element_id = '1' then entry_amt
									else 0
									end) -
								sum(case
									when p.entry_type = 'DB' and p.payroll_element_id = '2' 
									or   p.payroll_element_id = '4' or p.payroll_element_id = '5' then entry_amt
									else 0
									end) -
								sum(case
									when p.entry_type ='DB' and p.payroll_element_id ='12' then entry_amt
									else 0
									end) 
								npnoabs

			   		from		hr.tbl_employee e 
					join		hr.tbl_company  c ON e.company_id = c.company_id 
					join		hr.tbl_payroll  p ON e.company_id = p.company_id
			  		where 		c.company_id  = '$id' 	
			  					and	e.active_flag = 'Y'
								and e.employee_id = p.employee_id												
			  		group by    e.employee_number, employee
			  		order by    e.employee_number
			   						")
								);


			foreach($results as $result) {
				
			$acell = ('A'.$index);
			$bcell = ('B'.$index);
			$ccell = ('C'.$index);
			$dcell = ('D'.$index);
			$ecell = ('E'.$index);
			$fcell = ('F'.$index);
			$gcell = ('G'.$index);
			$hcell = ('H'.$index);
			$icell = ('I'.$index);
			$jcell = ('J'.$index);
			$ucell = ('U'.$index);

				$worksheet -> setCellValue($acell, $result -> employee_number);
				$worksheet -> setCellValue($bcell, $result -> employee);
				$worksheet -> setCellValue($dcell, $result -> gross);
				$worksheet -> getStyle($dcell)
					   	   -> getNumberFormat()
					       -> setFormatCode('0.00'); 
				$worksheet -> setCellValue($ecell, $result -> sss);
				$worksheet -> getStyle($ecell)
					       -> getNumberFormat()
					       -> setFormatCode('0.00'); 
				$worksheet -> setCellValue($fcell, $result -> philhealth);
				$worksheet -> getStyle($fcell)
					   	   -> getNumberFormat()
					       -> setFormatCode('0.00'); 
				$worksheet -> setCellValue($gcell, $result -> hdmf);
				$worksheet -> getStyle($gcell)
					       -> getNumberFormat()
					       -> setFormatCode('0.00'); 
				$worksheet -> setCellValue($hcell, $result -> taxinc); 
				$worksheet -> getStyle($hcell)
						   -> getNumberFormat()
						   -> setFormatCode('0.00'); 
				$worksheet -> setCellValue($icell, $result -> withtax);
				$worksheet -> getStyle($icell)
						   -> getNumberFormat()
						   -> setFormatCode('0.00'); 
				$worksheet -> setCellValue($jcell, $result -> npnoabs);
				$worksheet -> getStyle($jcell)
						   -> getNumberFormat()
						   -> setFormatCode('0.00'); 
				$worksheet -> setCellValue($ucell, $result -> npnoabs);
				$worksheet -> getStyle($ucell)
						   -> getNumberFormat()
						   -> setFormatCode('0.00'); 

		$index  = $index + 1;
	}			
				$dbcount = count($results);	
				$dyn_val 	= (1 + $dbcount);
				$dyn_val2   = ($dyn_val + 1);
				$dyn_border = ('A2'.':'.'U'.$dyn_val);
				$dyn_align  = ('A2'.':'.'U'.$dyn_val);
				$dtot 		= ('D2'.':'.'D'.$dyn_val);
				$etot 		= ('E2'.':'.'E'.$dyn_val);
				$ftot 		= ('F2'.':'.'F'.$dyn_val);
				$gtot 		= ('G2'.':'.'G'.$dyn_val);
				$htot 		= ('H2'.':'.'H'.$dyn_val);
				$itot 		= ('I2'.':'.'I'.$dyn_val);
				$jtot 		= ('J2'.':'.'J'.$dyn_val);
				$utot 		= ('U2'.':'.'U'.$dyn_val);

				$vhcenter = new Styles;
				$black    = new Styles;
				$insmed   = new Styles;
				$insthin  = new Styles;

				$worksheet -> getStyle($dyn_border) -> applyFromArray($black -> getBlack());
				$worksheet -> getStyle($dyn_align) -> applyFromArray($insthin -> getInsThin());
				$worksheet -> getStyle('B'.$dyn_val2.':'.'U'.$dyn_val2) 
						   -> getFont() 
						   -> setBold(true);
				$worksheet -> getStyle('B'.$dyn_val2.':'.'U'.$dyn_val2)
						   -> getNumberFormat()
						   -> setFormatCode('0.00'); 

				$worksheet -> setCellValue('B'.$dyn_val2,'Total:  ');
				$worksheet -> setCellvalue('D'.$dyn_val2,"= SUM($dtot)");
				$worksheet -> setCellvalue('E'.$dyn_val2,"= SUM($etot)");
				$worksheet -> setCellvalue('F'.$dyn_val2,"= SUM($ftot)");
				$worksheet -> setCellvalue('G'.$dyn_val2,"= SUM($gtot)");
				$worksheet -> setCellvalue('H'.$dyn_val2,"= SUM($htot)");
				$worksheet -> setCellvalue('I'.$dyn_val2,"= SUM($itot)");
				$worksheet -> setCellvalue('J'.$dyn_val2,"= SUM($jtot)");
				$worksheet -> setCellvalue('U'.$dyn_val2,"= SUM($utot)");



		$objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
			// // We'll be outputting an excel file
		header('Content-type: application/vnd.ms-excel');
			// // It will be called file.xls
		header('Content-Disposition: attachment; filename="PayrollRegister'.$d_today.'.xls"');
			// Write file to the browser
		$objWriter -> save('php://output');
		$workbook -> disconnectWorksheets();
		unset($workbook);


// 		excel::create('PayrollRegister_'.$d_today, function($payroll) use ($id) {	

// 			$payroll -> sheet('Payroll Summary', function($sheet) use ($id) {

// 			$head = array(
// 					'EMPLOYEE NO',
// 					'EMPLOYEE NAME',
// 					'GROSS PAY',
// 					'SSS',
// 					'Philhealth',
// 					'HDMF',
// 					'Taxable Income',
// 					'Withholding Tax',
// 					'Net Pay with no Absences and Reimbursements',
// 					'Absences',
// 					'Deductions Due to Absences',
// 					'HDMF Loan',
// 					'SSS Loan',
// 					'Cash Advance',
// 					'Reimbursements',
// 					'Overtime/NSD','Other Deductions',
// 					'Total Adjustments	Notes',
// 					'Net Pay'
// 					);
	
// 			$data = array($head);
// 			$sheet -> fromArray($head, null, 'A1',false, false);
// //STYLES
// 			$vhcenter = new Styles;
// 			$black    = new Styles;
// 			$insmed   = new Styles;
// 			$insthin  = new Styles;
		
// 			$sheet -> getStyle('A1:S1') -> getFont() -> setSize(12);
// 			$sheet -> getStyle('A1:S1') -> getFont() -> setBold(true);
// 			$sheet -> getStyle('A1:S1') -> applyFromArray($vhcenter -> getVertHoriCenter());
// 			$sheet -> getStyle('A1:S1') -> applyFromArray($black -> getBlack());
// 			$sheet -> getStyle('A1:S1') -> applyFromArray($insmed -> getInsMed());
// 			$sheet->setFreeze('A1');
// 			$sheet->setFreeze('C2');

// 			$sheet -> setWidth(array(
// 					'A' => 16, 'B' => 30, 'C' => 14, 'D' => 8,  'E' => 14,
// 					'F' => 8,  'G' => 18, 'H' => 18, 'I' => 50, 'J' => 14,
// 					'K' => 30, 'L' => 14, 'M' => 14, 'N' => 16, 'O' => 19,
// 					'P' => 20, 'Q' => 20, 'R' => 28, 'S' => 16		
// 					));
// 			$sheet -> setHeight(1, 35);
// 			$sheet -> setStyle(array(
// 				'font' => array(
// 					'name' => 'Calibri',
// 					'size' => '10',)
// 									)
// 							  );

// 			$records = db::select(
// 		   			   db::raw("
// 		   						select 	e.employee_number,
// 						    	(e.last_name || ', ' || e.first_name || '  ' ||e.middle_name) as employee,

// -- GROSS PAY
// 								sum(case
// 							    	when p.entry_type = 'CR' and p.payroll_element_id = '1' then entry_amt
// 							    	else 0
// 						        end
// 						    	)gross,
// -- SSS
// 			   					sum(case
// 			   						when p.entry_type = 'DB' and p.payroll_element_id = '2' then entry_amt
// 			   						else 0
// 			   					end
// 			   					)sss,
// -- PHILHEALTH
// 			   		        	sum(case 
// 			   		                when p.entry_type = 'DB' and p.payroll_element_id = '4' then entry_amt
// 			   		                else 0
// 			   		            end
// 			   		        	)philhealth,
// -- PAGIBIG HDMF
// 			   			    	sum(case 
// 			   		                when p.entry_type = 'DB' and p.payroll_element_id = '5' then entry_amt
// 			   		                else 0
// 			   		            end
// 			   		        	)hdmf,
// -- TAXABLE INCOME
// 			   					sum(case
// 									when p.entry_type = 'CR' and p.payroll_element_id = '1' THEN entry_amt
// 									else 0
// 								end) -
// 								sum(case
// 									when p.entry_type = 'DB'        and p.payroll_element_id = '2'
// 									or   p.payroll_element_id = '4' or  p.payroll_element_id = '5'
// 									 THEN entry_amt
// 									else 0
// 								end) 
// 								taxinc,
								

// -- WITHOLDING TAX
// 								sum(case
// 									when p.entry_type ='DB' and p.payroll_element_id ='12' then entry_amt
// 									else 0
// 								end
// 								)withtax,

// -- NETPAYNOABSENCE
// 								sum(case
// 									when p.entry_type = 'CR' and p.payroll_element_id = '1' then entry_amt
// 									else 0
// 								end) -
// 								sum(case
// 									when p.entry_type = 'DB' and p.payroll_element_id = '2' 
// 									or   p.payroll_element_id = '4' or p.payroll_element_id = '5' then entry_amt
// 									else 0
// 								end) -
// 								sum(case
// 									when p.entry_type ='DB' and p.payroll_element_id ='12' then entry_amt
// 									else 0
// 								end) 
// 								npnoabs

// 			   		from		hr.tbl_employee e 

// 					join		hr.tbl_company  c ON e.company_id = c.company_id 
// 					join		hr.tbl_payroll  p ON e.company_id = p.company_id
// 			  		where 		c.company_id  = '$id' 	
// 			  					and	e.active_flag = 'Y'
// 								and e.employee_id = p.employee_id												
// 			  		group by    e.employee_number, employee
// 			  		order by    e.employee_number
// 			   						")
// 								);	

// 			$iterator = 2;	
// 			$dbcount = count($records);	
// 			$dyn_val 	= (1 + $dbcount);
// 			$dyn_val2   = ($dyn_val + 1);

// 			$dyn_border = ('A2'.':'.'S'.$dyn_val);
// 			$dyn_align  = ('A2'.':'.'S'.$dyn_val);
// 			$ctot 		= ('C2'.':'.'C'.$dyn_val);
// 			$dtot 		= ('D2'.':'.'D'.$dyn_val);
// 			$etot 		= ('E2'.':'.'E'.$dyn_val);
// 			$ftot 		= ('F2'.':'.'F'.$dyn_val);
// 			$gtot 		= ('G2'.':'.'G'.$dyn_val);
// 			$htot 		= ('H2'.':'.'H'.$dyn_val);
// 			$itot 		= ('I2'.':'.'I'.$dyn_val);
// 			$stot 		= ('S2'.':'.'S'.$dyn_val);

// 			foreach($records as $record) {

// 			$acell = ('A'.$iterator);
// 			$bcell = ('B'.$iterator);
// 			$ccell = ('C'.$iterator);
// 			$dcell = ('D'.$iterator);
// 			$ecell = ('E'.$iterator);
// 			$fcell = ('F'.$iterator);
// 			$gcell = ('G'.$iterator);
// 			$hcell = ('H'.$iterator);
// 			$icell = ('I'.$iterator);
// 			$scell = ('S'.$iterator);

// 			$sheet -> setCellValue($acell, $record -> employee_number);
// 			$sheet -> setCellValue($bcell, $record -> employee);
// 			$sheet -> setCellValue($ccell, $record -> gross);
// 			$sheet -> getStyle($ccell)
// 					       -> getNumberFormat()
// 					       -> setFormatCode('0.00'); 
// 			$sheet -> setCellValue($dcell, $record -> sss);
// 			$sheet -> getStyle($dcell)
// 					       -> getNumberFormat()
// 					       -> setFormatCode('0.00'); 
// 			$sheet -> setCellValue($ecell, $record -> philhealth);
// 			$sheet -> getStyle($ecell)
// 					       -> getNumberFormat()
// 					       -> setFormatCode('0.00'); 
// 			$sheet -> setCellValue($fcell, $record -> hdmf);
// 			$sheet -> getStyle($fcell)
// 					       -> getNumberFormat()
// 					       -> setFormatCode('0.00'); 
// 			$sheet -> setCellValue($gcell, $record -> taxinc);
// 			$sheet -> getStyle($fcell)
// 					       -> getNumberFormat()
// 					       -> setFormatCode('0.00'); 
// 			$sheet -> getStyle($bcell)
// 					       -> getNumberFormat()
// 					       -> setFormatCode('0.00'); 
// 			$sheet -> setCellValue($hcell, $record -> withtax);
// 			$sheet -> getStyle($hcell)
// 					       -> getNumberFormat()
// 					       -> setFormatCode('0.00'); 
// 			$sheet -> setCellValue($icell, $record -> npnoabs);
// 			$sheet -> getStyle($icell)
// 					       -> getNumberFormat()
// 					       -> setFormatCode('0.00'); 
// 			$sheet -> setCellValue($scell, $record -> npnoabs);
// 			$sheet -> getStyle($scell)
// 					       -> getNumberFormat()
// 					       -> setFormatCode('0.00'); 
// 		$iterator  = $iterator + 1;
// 	}

// 			$sheet -> getStyle('B'.$dyn_val2.':'.'S'.$dyn_val2) 
// 				   -> getFont() 
// 				   -> setBold(true);
// 			$sheet -> getStyle('B'.$dyn_val2.':'.'S'.$dyn_val2)
// 				   -> getNumberFormat()
// 				   -> setFormatCode('0.00'); 
// 			$sheet -> getStyle($dyn_border) -> applyFromArray($black -> getBlack());
// 			$sheet -> getStyle($dyn_align) -> applyFromArray($insthin -> getInsThin());

// 			$sheet -> setCellValue('B'.$dyn_val2,'Total:  ');
// 			$sheet -> setCellvalue('C'.$dyn_val2,"= SUM($ctot)");
// 			$sheet -> setCellvalue('D'.$dyn_val2,"= SUM($dtot)");
// 			$sheet -> setCellvalue('E'.$dyn_val2,"= SUM($etot)");
// 			$sheet -> setCellvalue('F'.$dyn_val2,"= SUM($ftot)");
// 			$sheet -> setCellvalue('G'.$dyn_val2,"= SUM($gtot)");
// 			$sheet -> setCellvalue('H'.$dyn_val2,"= SUM($htot)");
// 			$sheet -> setCellvalue('I'.$dyn_val2,"= SUM($itot)");
// 			$sheet -> setCellvalue('S'.$dyn_val2,"= SUM($stot)");

// 				});

// 			$payroll -> sheet('Payroll Notes', function($sheet) {

// 			});

// 		})->download($file_type);
	}


public function getCbcacat()
    {
  		$d_today = date("Y/m/d");
  		$file_type = 'xls';

  		excel::create('Employee-Data_'.$d_today, function($employee_data) {

        	$employee_data -> sheet('Employee Data', function($sheet) {

          	$example_employee = tbl_user_model::orderBy('user_id', 'asc') -> get();
          	$head = array(
          					array('TOTAL AMOUNT:', '', '0.00'),
          					array('TOTAL COUNT:', '', '0'),
          					array('Account Number', 'Account Type', 'Employee Name', 'Amount')
          				);

  				$sheet -> fromArray($head, null, 'A1', false, false);

          $bottom_part = $example_employee -> count() + 4;

          $sheet -> setFreeze('E4')
  					     -> setWidth('A', 20)
  					     -> setWidth('B', 20)
  					     -> setWidth('C', 40)
  					     -> setWidth('D', 20)
  					     -> setHeight('3', 35)
  					     -> setHeight($bottom_part, 25)
  					     -> mergeCells('A'. $bottom_part .':D'. $bottom_part)
       					 -> setBorder('A3:D'.$bottom_part, 'thin');

  				$sheet -> cells('A1:D3', function($cells) {
  							$cells -> setFontSize(8)
  								     -> setFontWeight('bold')
  								     -> setFontColor('#FFFFFF')
  								     -> setBackground('#FF0000');
  				});

  				$sheet -> cells('A3:D3', function($cells) {
  							$cells -> setValignment('center')
  							       -> setAlignment('center');
  				});

  				$sheet -> cells('A1:D'. $bottom_part, function($cells) {
  							$cells -> setBorder('thick', 'thick', 'thick', 'thick');
  				});

  				$sheet -> cell('A'. $bottom_part, function($cell){
  							$cell -> setFontSize(9)
  							      -> setFontWeight('bold')
  							      -> setAlignment('center')
  							      -> setValignment('center')
  							      -> setFontColor('#FFFFFF')
  							      -> setBackground('#FF0000')
  							      -> setValue('***END OF FILE***');
  				});
          // // display values at employee columns
          $counter = 0;
          foreach(range(4, $example_employee -> count() + 3) as $range) {
            $sheet -> setCellValue('C'. $range, strtoupper($example_employee -> get($counter) -> last_name . ', ' . $example_employee -> get($counter) -> first_name));
            $counter = $counter + 1;
          		}
  			});
  		}) -> download($file_type);
	}

public function getPhform($id) {
	
	$path = (public_path().'/excels/Philhealth_RF1_Template.xltx');
	$workbook = new PHPExcel();
	$workbook = PHPExcel_IOFactory::load($path);
	$workbook -> getProperties() -> setCreator("Carlo Mendoza");
	$workbook -> getProperties() -> setTitle("Payroll Register");	
	$workbook -> setActiveSheetIndex(0);
	$workbook -> getActiveSheet() -> getPageSetup() -> setFitToWidth(1);
	$workbook -> getActiveSheet() -> getPageSetup() -> setFitToHeight(0);

	$dataws = $workbook -> getSheet(0);

	
	// $file_type = 'xls';

	$dataws -> getColumnDimension('A') -> setWidth(12.71);
	$dataws -> getColumnDimension('B') -> setWidth(25.71);
	$dataws -> getColumnDimension('C') -> setWidth(13.71);
	$dataws -> getColumnDimension('D') -> setWidth(25.71);
	$dataws -> getColumnDimension('E') -> setWidth(24.71);
	$dataws -> getColumnDimension('F') -> setWidth(25.71);
	$dataws -> getColumnDimension('G') -> setWidth(16.71);
	$dataws -> getColumnDimension('H') -> setWidth(8.71);
	$dataws -> getColumnDimension('I') -> setWidth(15.71);
	$dataws -> getColumnDimension('J') -> setWidth(6.71);
	$dataws -> getColumnDimension('K') -> setWidth(10.71);
	$dataws -> getColumnDimension('L') -> setWidth(10.71);
	$dataws -> getColumnDimension('M') -> setWidth(25.71);
	$dataws -> getColumnDimension('N') -> setWidth(15.71);
	$dataws -> getColumnDimension('O') -> setWidth(25.71);
	$dataws -> getColumnDimension('P') -> setWidth(15.71);

	$dataws -> getStyle('E4:E7') -> getFill()
								 		    -> setFillType(PHPExcel_Style_Fill::FILL_SOLID)
								 		    -> getStartColor()
								 		    -> setRGB('F0E68C');
	$dataws -> getStyle('K1:K3') -> getFill()
								 		    -> setFillType(PHPExcel_Style_Fill::FILL_SOLID)
								 		    -> getStartColor()
								 		    -> setRGB('F0E68C');							 		    


// COMPANY QUERY
	$data_companies = db::select(
					db::raw("
						select 
								c.company_name,
								c.address,
								c.city,
								c.contact_no,
								p.year_trans,
								p.month_trans,
								c.sss_no,
								c.tin_no,
								u.first_name,
								u.last_name,
								sum(case 
									when p.entry_type = 'DB' and p.payroll_element_id = '5' then entry_amt
									else 0
									end
								)totalps,
								sum(case
									when p.entry_type = 'EE' and p.payroll_element_id = '11' then entry_amt
									else 0
									end
								)totales

						from 	hr.tbl_employee e 
						join 	hr.tbl_company c on e.company_id = c.company_id
						join 	hr.tbl_payroll p on e.company_id = p.company_id
						join 	hr.tbl_user u    on u.user_id = p.created_by 	 	--for COMPANY DETAILS only!!			
						where 		c.company_id = '$id'
								and e.active_flag = 'Y' 
								and e.company_id  = c.company_id 
								and e.employee_id = p.employee_id
						group by 
								c.company_name,
								c.address,
								c.city,
								c.contact_no,
								p.year_trans,
								p.month_trans,
								c.sss_no,
								c.tin_no,
								u.first_name,
								u.last_name
						       ")
					        );

	$t_stamp = date("h:i:sa");
	$d_today = date("Y/m/d");

	$dbcount = count($data_companies);

	
	foreach($data_companies as $data_company) {

		$dataws -> setCellValue('B1', $data_company -> company_name)
				-> setCellvalue('B2', $data_company -> address.', '.$data_company -> city)
				-> setCellvalue('B4', $data_company -> contact_no)
				-> setCellvalue('E2', $dbcount)
				-> setCellvalue('E3', 'PRIVATE')
				-> setCellvalue('E4', $data_company -> year_trans)
				-> setCellvalue('E5', $data_company -> month_trans)
				-> setCellvalue('E7', $d_today.' '.$t_stamp)
				-> setCellvalue('G1', $data_company -> sss_no)
				-> setCellvalue('G2', $data_company -> tin_no)
				-> setCellValue('G4', $data_company -> last_name.', '. $data_company -> first_name);
	}
//Styles
	$la = new Styles;
	$vhcenter = new Styles;

	$dataws -> getStyle('N1:N6') -> getNumberFormat()
						   		 -> setFormatCode('0.00'); 
	$dataws -> getStyle('B1:B2') -> applyFromArray($la -> leftAlign());
	$dataws -> getStyle('B3:B5') -> applyFromArray($la -> leftAlign());
	$dataws -> getStyle('E2:E7') -> applyFromArray($vhcenter -> getVertHoriCenter());
	$dataws -> getStyle('N1:N6') -> applyFromArray($vhcenter -> getVertHoriCenter());

// EMPLOYEE QUERY
	$data_employees = db::select(
			   		  db::raw("
					    select 
					    		e.employee_number,
								upper(e.last_name) as  last_name,
								upper(e.first_name) as first_name,
								e.middle_name,
								e.philhealth_no,
								e.date_birth,
								e.gender,
								TO_CHAR(p.date_from,'MM-DD-YYYY') as date_from,
								TO_CHAR(p.date_to,'MM-DD-YYYY')as date_to,
								sum(case 
									when p.entry_type = 'CR' and p.payroll_element_id = '1' 
									and e.employee_id = p.employee_id then entry_amt
									else 0
									end
								)basic,
								sum(case
									when p.entry_type = 'DB' and p.payroll_element_id = '5' 
									and e.employee_id = p.employee_id then entry_amt
									else 0
										end
								)e_share,
								sum(case
									when p.entry_type = 'EE' and p.payroll_element_id = '11'
									and e.employee_id = p.employee_id  then entry_amt
									else 0
									end
								)c_share
							from 	hr.tbl_employee e 
							join 	hr.tbl_company c on e.company_id = c.company_id
							join 	hr.tbl_payroll p on e.company_id = p.company_id
							where 	c.company_id = '$id' 
									and e.company_id = c.company_id 
									and e.active_flag = 'Y'
							group by 
									e.employee_number,
									e.last_name,
									e.first_name,
									e.middle_name,
									e.philhealth_no,
									e.date_birth,
									e.gender,
									date_from,
									date_to
						   	")
						);
//data_employees iterator
	$deiterator = 9;
	$de_count = (count($data_employees) - 1);
	$dyn_range = $de_count + $deiterator;
	foreach($data_employees as $data_employee) {

	$acell = ('A'.$deiterator);
	$bcell = ('B'.$deiterator);
	$ccell = ('C'.$deiterator);
	$dcell = ('D'.$deiterator);
	$ecell = ('E'.$deiterator);
	$fcell = ('F'.$deiterator);
	$gcell = ('G'.$deiterator);
	$hcell = ('H'.$deiterator);
	$icell = ('I'.$deiterator);
	$jcell = ('J'.$deiterator);
	$kcell = ('K'.$deiterator);
	$lcell = ('L'.$deiterator);
	$mcell = ('M'.$deiterator);
	$ncell = ('N'.$deiterator);
	$ocell = ('O'.$deiterator);

		$dataws -> setCellValue($acell, $data_employee -> employee_number)
				-> setCellvalue($bcell, $data_employee -> last_name)
				-> setCellvalue($dcell, $data_employee -> first_name)
				-> setCellvalue($ecell, $data_employee -> middle_name)
				-> setCellvalue($fcell, $data_employee -> philhealth_no)
				-> setCellvalue($gcell, $data_employee -> date_birth)
				-> setCellvalue($hcell, $data_employee -> gender)
				-> setCellvalue($icell, $data_employee -> basic)
				-> setCellvalue($kcell, $data_employee -> c_share)
				-> setCellvalue($lcell, $data_employee -> e_share)
				-> setCellvalue($ocell, $data_employee -> date_from.' - '.$data_employee -> date_to);

	$deiterator	= $deiterator + 1;			
	}

	$black = new Styles;
	$insthin = new Styles;

	$dataws -> getStyle('I9:I'.$dyn_range) -> getNumberFormat()
						   				   -> setFormatCode('0.00'); 
	$dataws -> getStyle('K9:M'.$dyn_range) -> getNumberFormat()
						   				   -> setFormatCode('0.00'); 
	$dataws -> getStyle('I9:I'.$dyn_range) -> getFill()
								 		   -> setFillType(PHPExcel_Style_Fill::FILL_SOLID)
								 		   -> getStartColor()
								 		   -> setRGB('F0E68C');
	$dataws -> getStyle('N9:O'.$dyn_range) -> getFill()
								 		   -> setFillType(PHPExcel_Style_Fill::FILL_SOLID)
								 		   -> getStartColor()
								 		   -> setRGB('F0E68C');
								 		   								 		   
	$dataws -> getStyle('A9:A'.$dyn_range) -> applyFromArray($vhcenter -> getVertHoriCenter());
	$dataws -> getStyle('C9:C'.$dyn_range) -> applyFromArray($vhcenter -> getVertHoriCenter());
	$dataws -> getStyle('F9:H'.$dyn_range) -> applyFromArray($vhcenter -> getVertHoriCenter());
	$dataws -> getStyle('O9:O'.$dyn_range) -> applyFromArray($vhcenter -> getVertHoriCenter());
	$dataws -> getStyle('A9:O'.$dyn_range) -> applyFromArray($black -> getBlack());
	$dataws -> getStyle('A9:O'.$dyn_range) -> applyFromArray($insthin -> getInsThin());

//3rd sheet
	$rfws = $workbook -> getSheet(2);

	$rfws -> getColumnDimension('A') -> setWidth(10.71);
	$rfws -> getColumnDimension('B') -> setWidth(0.25);
	$rfws -> getColumnDimension('C') -> setWidth(1.71);
	$rfws -> getColumnDimension('D') -> setWidth(1.71);
	$rfws -> getColumnDimension('E') -> setWidth(0.25);
	$rfws -> getColumnDimension('F') -> setWidth(1.71);
	$rfws -> getColumnDimension('G') -> setWidth(1.71);
	$rfws -> getColumnDimension('H') -> setWidth(1.71);
	$rfws -> getColumnDimension('I') -> setWidth(1.71);
	$rfws -> getColumnDimension('J') -> setWidth(1.71);
	$rfws -> getColumnDimension('K') -> setWidth(1.71);
	$rfws -> getColumnDimension('L') -> setWidth(1.71);
	$rfws -> getColumnDimension('M') -> setWidth(1.71);
	$rfws -> getColumnDimension('N') -> setWidth(1.71);
	$rfws -> getColumnDimension('O') -> setWidth(0.25);
	$rfws -> getColumnDimension('P') -> setWidth(2.71);
	$rfws -> getColumnDimension('Q') -> setWidth(20.71);
	$rfws -> getColumnDimension('R') -> setWidth(7.71);
	$rfws -> getColumnDimension('S') -> setWidth(12.71);
	$rfws -> getColumnDimension('T') -> setWidth(25.71);
	$rfws -> getColumnDimension('U') -> setWidth(13.71);
	$rfws -> getColumnDimension('V') -> setWidth(4.71);
	$rfws -> getColumnDimension('W') -> setWidth(12.25);
	$rfws -> getColumnDimension('X') -> setWidth(7.71);
	$rfws -> getColumnDimension('Y') -> setWidth(7.71);
	$rfws -> getColumnDimension('Z') -> setWidth(13.71);
	$rfws -> getColumnDimension('AA') -> setWidth(27.71);

	$rfws -> getRowDimension('1') -> setRowHeight(34.50);
	$rfws -> getRowDimension('2') -> setRowHeight(34.50);
	$rfws -> getRowDimension('3') -> setRowHeight(34.50);
	$rfws -> getRowDimension('4') -> setRowHeight(34.50);
	$rfws -> getRowDimension('5') -> setRowHeight(34.50);
	$rfws -> getRowDimension('6') -> setRowHeight(34.50);
	$rfws -> getRowDimension('7') -> setRowHeight(34.50);
	$rfws -> getRowDimension('8') -> setRowHeight(34.50);
	$rfws -> getRowDimension('9') -> setRowHeight(61.50);
	$rfws -> getRowDimension('6') -> setRowHeight(27.75);



	$rf1_companies = db::select(
			   		 db::raw("
			   				
							select 
									c.philhealth_no,
									c.tin_no,
									c.company_name,
									c.address,
									c.city,
									c.contact_no

							from 	hr.tbl_employee e 
							join 	hr.tbl_company c on e.company_id = c.company_id
							join 	hr.tbl_payroll p on e.company_id = p.company_id
							join 	hr.tbl_user u    on u.user_id = p.created_by
						
							where 	c.company_id = '$id' 
									and e.company_id = c.company_id and e.active_flag = 'Y'
									and e.employee_id = p.employee_id

							group by 
									c.philhealth_no,
									c.tin_no,
									c.company_name,
									c.address,
									c.city,
									c.contact_no
			   			")
					);
	$rcom_iterator = 1;
	foreach ($rf1_companies as $rf1_company) {

		$rfws -> setCellValue('Q3', $rf1_company -> philhealth_no)
			  -> setCellvalue('Q4', $rf1_company -> tin_no)
			  -> setCellvalue('Q5', $rf1_company -> company_name)
			  -> setCellvalue('Q6', $rf1_company -> address. ' '. $rf1_company -> city)
			  -> setCellvalue('Q7', $rf1_company -> contact_no);

	$rcom_iterator = $rcom_iterator + 1;
	}


	$rf1_employees = db::select(
			   		 db::raw("

			   				select 
									e.philhealth_no,
									e.last_name,
									e.suffix,
									e.first_name,
									e.middle_name,
									e.date_birth,
									e.gender,
									e.date_hired,

									sum(case
									when p.entry_type = 'DB' and p.payroll_element_id = '5' 
									and e.employee_id = p.employee_id then entry_amt
									else 0
									end
									)p_share,

									sum(case
									when p.entry_type = 'EE' and p.payroll_element_id = '11'
									and e.employee_id = p.employee_id  then entry_amt
									else 0
									end
									)e_share										

							from 	hr.tbl_employee e 

							join 	hr.tbl_company c on e.company_id = c.company_id
							join 	hr.tbl_payroll p on e.company_id = p.company_id
							join 	hr.tbl_user u    on u.user_id = p.created_by

							where 	c.company_id = '$id' 
									and e.company_id = c.company_id and e.active_flag = 'Y'

							group by 
									e.philhealth_no,
									e.last_name,
									e.suffix,
									e.first_name,
									e.middle_name,
									e.date_birth,
									e.date_hired,
									e.gender
			   			")
					);
$styles = new Styles;

	$remp_iterator = 11;
	$remp_count = count($rf1_employees);

	$footer0 = $remp_count + 10;
	$footer1 = $remp_count + 11;
	$footer2 = $remp_count + 12;
	$footer3 = $remp_count + 13;
	$footer4 = $remp_count + 14;
	$footer5 = $remp_count + 15;
	$footer6 = $remp_count + 16;

	foreach($rf1_employees as $rf1_employee) {

		$aselector = ('A'.$remp_iterator);
		$cselector = ('C'.$remp_iterator);
		$dselector = ('D'.$remp_iterator);
		$fselector = ('F'.$remp_iterator);
		$gselector = ('G'.$remp_iterator);
		$hselector = ('H'.$remp_iterator);
		$iselector = ('I'.$remp_iterator);
		$jselector = ('J'.$remp_iterator);
		$kselector = ('K'.$remp_iterator);
		$lselector = ('L'.$remp_iterator);
		$mselector = ('M'.$remp_iterator);
		$nselector = ('N'.$remp_iterator);
		$qselector = ('Q'.$remp_iterator);
		$rselector = ('R'.$remp_iterator);
		$sselector = ('S'.$remp_iterator);
		$tselector = ('T'.$remp_iterator);
		$uselector = ('U'.$remp_iterator);
		$vselector = ('V'.$remp_iterator);
		$xselector = ('X'.$remp_iterator);
		$yselector = ('Y'.$remp_iterator);
		$aaselector = ('AA'.$remp_iterator);


//PhilHealth Number
		$rfws -> setCellValue($aselector, substr($rf1_employee -> philhealth_no,0,1))
		 	  -> setCellValue($cselector, substr($rf1_employee -> philhealth_no,1,1))
			  -> setCellValue($dselector, substr($rf1_employee -> philhealth_no,2,1))
			  -> setCellValue($fselector, substr($rf1_employee -> philhealth_no,3,1))
			  -> setCellValue($gselector, substr($rf1_employee -> philhealth_no,4,1))
			  -> setCellValue($hselector, substr($rf1_employee -> philhealth_no,5,1))
			  -> setCellValue($iselector, substr($rf1_employee -> philhealth_no,6,1))
			  -> setCellValue($jselector, substr($rf1_employee -> philhealth_no,7,1))
			  -> setCellValue($kselector, substr($rf1_employee -> philhealth_no,8,1))
			  -> setCellValue($lselector, substr($rf1_employee -> philhealth_no,9,1))
			  -> setCellValue($mselector, substr($rf1_employee -> philhealth_no,10,1))
			  -> setCellValue($nselector, substr($rf1_employee -> philhealth_no,11,1));
		$rfws -> setCellValue($qselector, $rf1_employee -> last_name)
			  -> setCellValue($rselector, $rf1_employee -> suffix)
			  -> setCellValue($sselector, $rf1_employee -> first_name)
			  -> setCellValue($tselector, $rf1_employee -> middle_name)
			  -> setCellValue($uselector, $rf1_employee -> date_birth)
			  -> setCellValue($vselector, $rf1_employee -> gender)
			  -> setCellValue($xselector, $rf1_employee -> p_share)
			  -> setCellValue($yselector, $rf1_employee -> e_share);

	$remp_iterator = $remp_iterator + 1;
	}

	$style = new Styles;

	$footers = db::select(
			   db::raw("
			   	          select        TO_CHAR(p.date_from,'MM-DD-YYYY') as date_from,
										TO_CHAR(p.date_to,'MM-DD-YYYY')as date_to,
			   	          				p.date_payroll,

			   	          				sum(case
										when p.entry_type = 'DB' and p.payroll_element_id = '5'
										then entry_amt
										else 0
										end
										)pstotal,

			   							sum(case
										when p.entry_type = 'EE' and p.payroll_element_id = '11'
										then entry_amt
										else 0
										end
										)estotal,

			   	          				sum(case
										when p.entry_type = 'DB' and p.payroll_element_id = '5'
										then entry_amt
										when p.entry_type = 'EE' and p.payroll_element_id = '11'
										then entry_amt
										else 0
										end
										)grandtotal

							from 	hr.tbl_employee e 
							join 	hr.tbl_company c on e.company_id = c.company_id
							join 	hr.tbl_payroll p on e.company_id = p.company_id
							join 	hr.tbl_user u    on u.user_id = p.created_by
						
							where 	c.company_id = '$id' 
									and e.company_id = c.company_id and e.active_flag = 'Y'
									and e.employee_id = p.employee_id

							group by
									    p.date_to,
			   	          				p.date_from,
			   	          				p.date_payroll

			   	"));
		
		$rfws -> getRowDimension($footer1) -> setRowHeight(35);
		$rfws -> getRowDimension($footer2) -> setRowHeight(35);
		$rfws -> getRowDimension($footer3) -> setRowHeight(35);
		$rfws -> getRowDimension($footer4) -> setRowHeight(24);
		$rfws -> getRowDimension($footer5) -> setRowHeight(45);

		$ps = ('X11:X'.$footer0);
		$es = ('Y11:Y'.$footer0);
		$pses = ('X'.($footer1).':'.'Y'.($footer1));	


		$rfws -> mergecells('A'.($footer2).':'.'G'.($footer2))
			  -> mergecells('A'.($footer3).':'.'G'.($footer3))
			  -> mergeCells('A'.($footer1).':'.'G'.($footer1))
			  -> mergeCells('H'.($footer1).':'.'T'.($footer1))
			  -> mergeCells('A'.($footer1).':'.'G'.($footer1))
			  -> mergeCells('H'.($footer1).':'.'T'.($footer1))
			  -> mergeCells('H'.($footer2).':'.'P'.($footer2))
			  -> mergecells('R'.($footer2).':'.'S'.($footer2))
			  -> mergeCells('H'.($footer3).':'.'P'.($footer3))
			  -> mergecells('R'.($footer3).':'.'S'.($footer3))
			  -> mergeCells('U'.($footer1).':'.'W'.($footer2))
			  -> mergeCells('U'.($footer3).':'.'W'.($footer3))
			  -> mergeCells('X'.($footer1).':'.'X'.($footer2))
			  -> mergeCells('Y'.($footer1).':'.'Y'.($footer2))
			  -> mergeCells('X'.($footer3).':'.'Y'.($footer3))
			  -> mergecells('Z'.($footer1).':'.'AA'.($footer1))
			  -> mergeCells('Z'.($footer2).':'.'AA'.($footer2))
			  -> mergeCells('Z'.($footer3).':'.'AA'.($footer3))
			  -> mergeCells('A'.($footer4).':'.'AA'.($footer4))
			  -> mergeCells('A'.($footer5).':'.'R'.($footer5))
			  -> mergeCells('S'.($footer5).':'.'V'.($footer5))
			  -> mergeCells('W'.($footer5).':'.'AA'.($footer5));

		$rfws -> setCellValue('A'.$footer2, $remp_count)
			  -> setCellvalue('A'.$footer3, "Indicates Total Number \n of Employees ")
			  -> setCellvalue('H'.$footer1, 'ACKNOWLEDGEMENT RECEIPT (PAR/POR/TRANSACTION REFERENCE NUMBER')
			  -> setcellvalue('H'.$footer2, 'APPLICABLE PERIOD')
			  -> setCellValue('Q'.$footer2, 'REMITTED AMOUNT')
			  -> setCellvalue('R'.$footer2, 'ACKNOWLEDGEMENT RECEIPT')
			  -> setCellvalue('T'.$footer2, 'TRANSACTION DATE')
			  -> setcellvalue('H'.$footer3, '')
			  -> setCellValue('Q'.$footer3, '')
			  -> setCellvalue('R'.$footer3, '')
			  -> setCellvalue('T'.$footer3, '')
			  -> setCellvalue('U'.$footer1,'GRAND TOTAL (PS + ES)')
			  -> setCellvalue('U'.$footer3, '(To be accomplished at every page)')
			  -> setCellvalue('X'.$footer1,"= SUM($ps)")
			  -> setCellvalue('Y'.$footer1,"= SUM($es)")
			  -> setCellvalue('X'.$footer3,"= SUM($pses)")
			  -> setCellvalue('Z'.$footer1, "________________________________________\nSIGNATURE OVER PRINTED NAME")
			  -> setCellvalue('Z'.$footer2, "________________________________________\nOFFICIAL DESIGNATION")
			  -> setCellvalue('Z'.$footer3, "________________________________________\nDATE")
			  -> setCellValue('A'.$footer4,'UNDER PENALTY OF LAW, I HEREBY ATTEST THAT THE ABOVE INFORMATIONS PROVIDED HEREIN ARE TRUE AND CORRECT.')
			  -> setCellValue('A'.$footer5,"________________________________________\nSignature over printed name")
			  -> setCellValue('S'.$footer5,"________________________________________\nOfficial Designation")
			  -> setCellValue('W'.$footer5,"________________________________________\nDate");

		$box12 = new PHPExcel_Worksheet_Drawing;
		$box13 = new PHPExcel_Worksheet_Drawing;
		$box14 = new PHPExcel_Worksheet_Drawing;
		$box15 = new PHPExcel_Worksheet_Drawing;
		$box16 = new PHPExcel_Worksheet_Drawing;

		$box12 -> setPath(public_path('imgs/box12.png'));
		$box13 -> setPath(public_path('imgs/box13.png'));
		$box14 -> setPath(public_path('imgs/box14.png'));
		$box15 -> setPath(public_path('imgs/box15.png'));
		$box16 -> setPath(public_path('imgs/box16.png'));
		
		$box12 -> setCoordinates('A'.($footer1));
		$box13 -> setCoordinates('H'.($footer1));
		$box14 -> setCoordinates('U'.($footer1));
		$box15 -> setCoordinates('Z'.($footer1));
		$box16 -> setCoordinates('A'.($footer4));

		$box12 -> setWorkSheet($rfws);
		$box13 -> setWorkSheet($rfws);
		$box14 -> setWorkSheet($rfws);
		$box15 -> setWorkSheet($rfws);
		$box16 -> setWorkSheet($rfws);

		$rfws -> getStyle('A11'.':'.'AA'.($footer5)) -> applyFromArray($styles -> getFooter());
		$rfws -> getStyle('A11'.':'.'AA'.($footer5)) -> applyFromArray($styles -> getInsThin());
		$rfws -> getStyle('A11'.':'.'A'.($footer0)) -> applyFromArray($styles -> rightAlign());
		$rfws -> getStyle('U11'.':'.'V'.($footer0)) -> applyFromArray($styles -> getVertHoriCenter());
		$rfws -> getStyle('A'.($footer1).':'.'W'.($footer5)) -> applyFromArray($styles -> getVertHoriCenter());
		$rfws -> getStyle('A'.($footer1).':'.'W'.($footer5)) -> applyFromArray($styles -> getVertHoriCenter());
		$rfws -> getStyle('A'.($footer1).':'.'G'.($footer3)) -> applyFromArray($styles -> getWhite());

		$rfws -> getStyle('A'.($footer0).':'.'X'.($footer3)) -> getFont() -> setSize(12);
		$rfws -> getStyle('A'.($footer3)) -> applyFromArray ($styles -> ft8wraptrue());
		$rfws -> getStyle('H'.($footer2).':'.'T'.($footer2)) -> applyFromArray ($styles -> ft8wraptrue());	
		$rfws -> getStyle('Z'.($footer1).':'.'Z'.($footer3)) -> applyFromArray ($styles -> ft8wraptrue());

		$rfws -> getStyle('Z'.($footer1).':'.'Z'.($footer3)) -> applyFromArray ($styles -> ft8wraptrue());
		$rfws -> getStyle('Z'.($footer1).':'.'Z'.($footer3)) -> getAlignment() -> setVertical(PHPEXCEL_STYLE_ALIGNMENT::VERTICAL_BOTTOM);
		$rfws -> getStyle('Z'.($footer1).':'.'Z'.($footer3)) -> getAlignment() -> setHorizontal(PHPEXCEL_STYLE_ALIGNMENT::HORIZONTAL_CENTER);	
		$rfws -> getStyle('A'.($footer5).':'.'W'.($footer5)) -> applyFromArray ($styles -> ft8wraptrue());
		$rfws -> getStyle('A'.($footer5).':'.'W'.($footer5)) -> getAlignment() -> setVertical(PHPEXCEL_STYLE_ALIGNMENT::VERTICAL_BOTTOM);
		$rfws -> getStyle('A'.($footer1).':'.'W'.($footer3)) -> getAlignment() -> setHorizontal(PHPEXCEL_STYLE_ALIGNMENT::HORIZONTAL_CENTER);

		$rfws -> getStyle('H'.$footer1) -> getFont() -> setBold(true);
		$rfws -> getStyle('U'.$footer1) -> getFont() -> setBold(true);
		$rfws -> getStyle('H'.$footer1) -> getFont() -> setBold(true);
		$rfws -> getStyle('U'.$footer3) -> getFont() -> setSize(8);

		$rfws -> getStyle('H'.($footer2).':'.'T'.($footer2)) -> getFont() -> setSize(10);
		$rfws -> getStyle('X'.($footer1).':'.'Y'.($footer3)) -> applyFromArray ($style -> getVertHoriCenter());
		$rfws -> getStyle('A'.$footer4) -> getFont() -> setSize(16);
		$rfws -> getStyle('A'.$footer4) -> getFont() -> setBold(true);
		$rfws -> getStyle('A'.$footer2) -> getFont() -> setSize(28);
		$rfws -> getStyle('A'.$footer2) -> getFont() -> setBold(true);
		$rfws -> getStyle('X'.($footer1).':'.'Y'.($footer1)) -> getNumberFormat()
						   		 							 -> setFormatCode('0.00'); 
		$rfws -> getStyle('X'.$footer3) -> getNumberFormat()
						   		 		-> setFormatCode('0.00'); 

	$objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
			// // We'll be outputting an excel file
	header('Content-type: application/vnd.ms-excel');
			// // It will be called file.xls
	header('Content-Disposition: attachment; filename="Philhealth_RF1_'.$d_today.'.xls"');
			// Write file to the browser
	$objWriter -> save('php://output');
	$workbook -> disconnectWorksheets();
	unset($workbook);

	}


}


