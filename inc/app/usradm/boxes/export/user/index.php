<?php
set_time_limit (0);
require 'PHPExcel.php';
require 'PHPExcel/Writer/Excel5.php';

$q = db_fetch_array ('select username, firstname, lastname, email, role, team, disabled, lang, company, position, website, phone, cell, home, fax, address1, address2, city, province, postal_code, country, registered from sitellite_user order by username asc');

if(!empty($q)) {

    // Set headers for output
    header('Content-Type: application/vnd.ms-excel');
	header ('Content-Disposition: attachment; filename=users-' . date ('Y-m-d') . '.xls');
	header('Cache-Control: max-age=0');

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set properties
    $objPHPExcel->getProperties()->setCreator("Sitellite CMS");
    $objPHPExcel->getProperties()->setLastModifiedBy("Sitellite CMS");
    $objPHPExcel->getProperties()->setTitle("users-" . date ('Y-m-d'));
    $objPHPExcel->getProperties()->setSubject("users-" . date ('Y-m-d'));
    $objPHPExcel->getProperties()->setDescription("Sitellite CMS users");

    // set active excel sheet to first
    $objPHPExcel->setActiveSheetIndex(0);

    // headers
    $objPHPExcel->getActiveSheet()->SetCellValue("A1","username");
    $objPHPExcel->getActiveSheet()->SetCellValue("B1","firstname");
    $objPHPExcel->getActiveSheet()->SetCellValue("C1","lastname");
    $objPHPExcel->getActiveSheet()->SetCellValue("D1","email");
    $objPHPExcel->getActiveSheet()->SetCellValue("E1","role");
    $objPHPExcel->getActiveSheet()->SetCellValue("F1","team");
    $objPHPExcel->getActiveSheet()->SetCellValue("G1","disabled");
    $objPHPExcel->getActiveSheet()->SetCellValue("H1","lang");
    $objPHPExcel->getActiveSheet()->SetCellValue("I1","company");
    $objPHPExcel->getActiveSheet()->SetCellValue("J1","position");
    $objPHPExcel->getActiveSheet()->SetCellValue("K1","website");
	$objPHPExcel->getActiveSheet()->SetCellValue("L1","phone");
	$objPHPExcel->getActiveSheet()->SetCellValue("M1","cell");
	$objPHPExcel->getActiveSheet()->SetCellValue("N1","home");
	$objPHPExcel->getActiveSheet()->SetCellValue("O1","fax");
	$objPHPExcel->getActiveSheet()->SetCellValue("P1","address1");
	$objPHPExcel->getActiveSheet()->SetCellValue("Q1","address2");
	$objPHPExcel->getActiveSheet()->SetCellValue("R1","city");
	$objPHPExcel->getActiveSheet()->SetCellValue("S1","province");
	$objPHPExcel->getActiveSheet()->SetCellValue("T1","postal_code");
	$objPHPExcel->getActiveSheet()->SetCellValue("U1","country");
	$objPHPExcel->getActiveSheet()->SetCellValue("V1","registered");

    // Bold headers
    $objPHPExcel->getActiveSheet()->getStyle('A1:V1')->getFont()->setBold(true);

	for($i=0;$i<count($q);$i++)
	{
		$excelrow = $i + 2; // excel starts at 2 (after headers) while array starts at 0
	  	$objPHPExcel->getActiveSheet()->SetCellValue("A".$excelrow,$q[$i]->username);
		$objPHPExcel->getActiveSheet()->SetCellValue("B".$excelrow,$q[$i]->firstname);
		$objPHPExcel->getActiveSheet()->SetCellValue("C".$excelrow,$q[$i]->lastname);
		$objPHPExcel->getActiveSheet()->SetCellValue("D".$excelrow,$q[$i]->email);
		$objPHPExcel->getActiveSheet()->SetCellValue("E".$excelrow,$q[$i]->role);
		$objPHPExcel->getActiveSheet()->SetCellValue("F".$excelrow,$q[$i]->team);
		$objPHPExcel->getActiveSheet()->SetCellValue("G".$excelrow,$q[$i]->disabled);
		$objPHPExcel->getActiveSheet()->SetCellValue("H".$excelrow,$q[$i]->lang);
		$objPHPExcel->getActiveSheet()->SetCellValue("I".$excelrow,$q[$i]->company);
		$objPHPExcel->getActiveSheet()->SetCellValue("J".$excelrow,$q[$i]->position);
		$objPHPExcel->getActiveSheet()->SetCellValue("K".$excelrow,$q[$i]->website);
		$objPHPExcel->getActiveSheet()->SetCellValue("L".$excelrow,$q[$i]->phone);
		$objPHPExcel->getActiveSheet()->SetCellValue("M".$excelrow,$q[$i]->cell);
		$objPHPExcel->getActiveSheet()->SetCellValue("N".$excelrow,$q[$i]->home);
		$objPHPExcel->getActiveSheet()->SetCellValue("O".$excelrow,$q[$i]->fax);
		$objPHPExcel->getActiveSheet()->SetCellValue("P".$excelrow,$q[$i]->address1);
		$objPHPExcel->getActiveSheet()->SetCellValue("Q".$excelrow,$q[$i]->address2);
		$objPHPExcel->getActiveSheet()->SetCellValue("R".$excelrow,$q[$i]->city);
		$objPHPExcel->getActiveSheet()->SetCellValue("S".$excelrow,$q[$i]->province);
		$objPHPExcel->getActiveSheet()->SetCellValue("T".$excelrow,$q[$i]->postal_code);
		$objPHPExcel->getActiveSheet()->SetCellValue("U".$excelrow,$q[$i]->country);
		$objPHPExcel->getActiveSheet()->SetCellValue("V".$excelrow,$q[$i]->registered);
    }
	
    //Set column widths
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12.5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12.5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12.5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12.5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12.5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12.5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12.5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(12.5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(12.5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(7.5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(12.5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(12.5);
	
    // Create Excel 5 file
    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
	
	// Output to browser
	$objWriter->save("php://output");
	
} else {
      echo intl_get("No records available to output");
}
exit;

?>