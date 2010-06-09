<?php

//$loader->import ('saf.MailForm');

//$form = new MailForm;

//$client_information =& $form->addWidget ('textarea', 'client_information');
//$submit =& $form->addWidget ('submit', 'submit');
//$submit->setValues ('Create Invoice');

//if ($form->invalid ($cgi)) {
//	$form->setValues ($cgi);
//	echo $form->show ();
//} else {
//	$form->setValues ($cgi);
//	$vars = $form->getValues ();
//	$vars = make_obj ($vars);

loader_import ('ext.fpdf');

global $cgi;

class PDF extends FPDF {
	var $B;
	var $I;
	var $U;
	var $HREF;

	function PDF($orientation='P',$unit='mm',$format='A4') {
	    //Call parent constructor
	    $this->FPDF($orientation,$unit,$format);
	    //Initialization
	    $this->B=0;
	    $this->I=0;
	    $this->U=0;
	    $this->HREF='';
	}

	function WriteHTML($html) {
	    //HTML parser
	    $html=str_replace("\n",' ',$html);
	    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	    foreach($a as $i=>$e) {
	        if($i%2==0) {
	            //Text
	            if($this->HREF)
	                $this->PutLink($this->HREF,$e);
	            else
	                $this->Write(5,$e);
	        } else {
	            //Tag
	            if($e{0}=='/')
	                $this->CloseTag(strtoupper(substr($e,1)));
	            else {
	                //Extract properties
	                $a2=split(' ',$e);
	                $tag=strtoupper(array_shift($a2));
	                $prop=array();
	                foreach($a2 as $v)
	                    if(preg_match('/^([^=]*)=["\']?([^"\']*)["\']?$/',$v,$a3))
	                        $prop[strtoupper($a3[1])]=$a3[2];
	                $this->OpenTag($tag,$prop);
	            }
	        }
	    }
	}

	function OpenTag($tag,$prop) {
	    //Opening tag
	    if($tag=='B' or $tag=='I' or $tag=='U')
	        $this->SetStyle($tag,true);
	    if($tag=='A')
	        $this->HREF=$prop['HREF'];
	    if($tag=='BR')
	        $this->Ln(5);
	}

	function CloseTag($tag) {
	    //Closing tag
	    if($tag=='B' or $tag=='I' or $tag=='U')
	        $this->SetStyle($tag,false);
	    if($tag=='A')
	        $this->HREF='';
	}

	function SetStyle($tag,$enable) {
	    //Modify style and select corresponding font
	    $this->$tag+=($enable ? 1 : -1);
	    $style='';
	    foreach(array('B','I','U') as $s)
	        if($this->$s>0)
	            $style.=$s;
	    $this->SetFont('',$style);
	}

	function PutLink($URL,$txt) {
	    //Put a hyperlink
	    $this->SetTextColor(0,0,225);
	    //$this->SetStyle('U',true);
	    $this->Write(5,$txt,$URL);
	    //$this->SetStyle('U',false);
	    $this->SetTextColor(0);
	}

	function Header () {
		$this->Image ('inc/app/siteinvoice/pix/header.jpg', 15, 10, 180);
	}

	function Footer () {
		$this->SetY (-15);
		$this->SetDrawColor (90, 90, 90);
		$this->Line (15, 278, 195, 278);
		$this->SetFont ('Arial', '', 9);
		$this->SetTextColor (150, 150, 150);
		$this->Cell (0, 0, chr (169) . ' ' . appconf ('company_name') . ', ' . date ('Y') . '  For further information, visit us online at ' . appconf ('company_website'), 0, 0, 'R');
		$this->SetTextColor (0);
	}
} // end PDF

	$pdf = new PDF;
	$pdf->SetFont ('Arial', '', 10);

	$pdf->Open ();
	$pdf->SetDisplayMode (150, 'single');
	$pdf->SetLeftMargin (15);
	$pdf->SetRightMargin (15);
	$pdf->SetFillColor (240, 240, 240);
	$pdf->AddPage ();

	$pdf->Cell (0, 5, ' ', 0, 1, 'C');
	$pdf->Ln ();

	// heading
	$pdf->SetFont ('Arial', '', 10);
	$pdf->SetFont ('Arial', '', 10);
	$pdf->Cell (0, 5, appconf ('company_address'), 0, 1, 'R');
	$pdf->SetLeftMargin (131.5);
	$pdf->WriteHTML ('tel: ' . appconf ('company_phone') . ', web: <a href="http://' . appconf ('company_website') . '/">' . appconf ('company_website') . '</a>');
	$pdf->SetLeftMargin (15);
	$pdf->Ln ();

	$pdf->Cell (0, 5, ' ', 0, 1, 'C');
	$pdf->Ln ();

	// client info
	$client = db_single ('select * from siteinvoice_client where id = ?', $cgi->client);
	$pdf->SetFont ('Arial', 'B', 10);
	$pdf->Cell (0, 5, 'Client Information', 0, 1);
	$pdf->SetFont ('Arial', '', 10);
	$pdf->MultiCell (0, 4, $client->name . "\n" . $client->address, 0, 1);
	$pdf->Ln ();

	/*
	$pdf->MultiCell (0, 4, 'Note: As per contract signed with 10Digit Communications November, 28th, 2001 for Support Fees for Sitellite' . "\n" . 'Content Management Website Software: http://www.winnipegfreepress2.com/sitellite, http://www.wfpauto.com, and resold licenses.', 0, 1);
	$pdf->Ln ();
	*/

	$pdf->SetFillColor (210, 225, 240);

	// table headings (date, item no, description, quantity, price, amount)
	$pdf->SetFont ('Arial', 'B', 8);
	$pdf->Cell (20, 4, 'DATE', 1, 0, 'C', 1);
	$pdf->Cell (20, 4, 'ITEM NO.', 1, 0, 'C', 1);
	$pdf->Cell (80, 4, 'DESCRIPTION', 1, 0, 'C', 1);
	$pdf->Cell (20, 4, 'QUANTITY', 1, 0, 'C', 1);
	$pdf->Cell (20, 4, 'PRICE', 1, 0, 'C', 1);
	$pdf->Cell (20, 4, 'AMOUNT', 1, 0, 'C', 1);
	$pdf->Ln ();

	// table rows
	$pdf->SetFont ('Arial', '', 8);

	foreach (array ('01', '02', '03', '04', '05', '06', '07', '08', '09', '10') as $n) {
		if (! empty ($cgi->{'price' . $n})) {
			${'price' . $n} = number_format ((float) $cgi->{'price' . $n}, 2);
		} else {
			${'price' . $n} = '';
		}
		if (! empty ($cgi->{'amt' . $n})) {
			${'amt' . $n} = number_format ((float) $cgi->{'amt' . $n}, 2);
		} else {
			${'amt' . $n} = '';
		}
	}

	$pdf->Cell (20, 4, $cgi->date01, 1, 0, 'C');
	$pdf->Cell (20, 4, $cgi->item01, 1, 0, 'L');
	$pdf->Cell (80, 4, $cgi->desc01, 1, 0, 'L');
	$pdf->Cell (20, 4, $cgi->qty01, 1, 0, 'R');
	$pdf->Cell (20, 4, $price01, 1, 0, 'R');
	$pdf->Cell (20, 4, $amt01, 1, 0, 'R');
	$pdf->Ln ();

	$pdf->Cell (20, 4, $cgi->date02, 1, 0, 'C');
	$pdf->Cell (20, 4, $cgi->item02, 1, 0, 'L');
	$pdf->Cell (80, 4, $cgi->desc02, 1, 0, 'L');
	$pdf->Cell (20, 4, $cgi->qty02, 1, 0, 'R');
	$pdf->Cell (20, 4, $price02, 1, 0, 'R');
	$pdf->Cell (20, 4, $amt02, 1, 0, 'R');
	$pdf->Ln ();

	$pdf->Cell (20, 4, $cgi->date03, 1, 0, 'C');
	$pdf->Cell (20, 4, $cgi->item03, 1, 0, 'L');
	$pdf->Cell (80, 4, $cgi->desc03, 1, 0, 'L');
	$pdf->Cell (20, 4, $cgi->qty03, 1, 0, 'R');
	$pdf->Cell (20, 4, $price03, 1, 0, 'R');
	$pdf->Cell (20, 4, $amt03, 1, 0, 'R');
	$pdf->Ln ();

	$pdf->Cell (20, 4, $cgi->date04, 1, 0, 'C');
	$pdf->Cell (20, 4, $cgi->item04, 1, 0, 'L');
	$pdf->Cell (80, 4, $cgi->desc04, 1, 0, 'L');
	$pdf->Cell (20, 4, $cgi->qty04, 1, 0, 'R');
	$pdf->Cell (20, 4, $price04, 1, 0, 'R');
	$pdf->Cell (20, 4, $amt04, 1, 0, 'R');
	$pdf->Ln ();

	$pdf->Cell (20, 4, $cgi->date05, 1, 0, 'C');
	$pdf->Cell (20, 4, $cgi->item05, 1, 0, 'L');
	$pdf->Cell (80, 4, $cgi->desc05, 1, 0, 'L');
	$pdf->Cell (20, 4, $cgi->qty05, 1, 0, 'R');
	$pdf->Cell (20, 4, $price05, 1, 0, 'R');
	$pdf->Cell (20, 4, $amt05, 1, 0, 'R');
	$pdf->Ln ();

	$pdf->Cell (20, 4, $cgi->date06, 1, 0, 'C');
	$pdf->Cell (20, 4, $cgi->item06, 1, 0, 'L');
	$pdf->Cell (80, 4, $cgi->desc06, 1, 0, 'L');
	$pdf->Cell (20, 4, $cgi->qty06, 1, 0, 'R');
	$pdf->Cell (20, 4, $price06, 1, 0, 'R');
	$pdf->Cell (20, 4, $amt06, 1, 0, 'R');
	$pdf->Ln ();

	$pdf->Cell (20, 4, $cgi->date07, 1, 0, 'C');
	$pdf->Cell (20, 4, $cgi->item07, 1, 0, 'L');
	$pdf->Cell (80, 4, $cgi->desc07, 1, 0, 'L');
	$pdf->Cell (20, 4, $cgi->qty07, 1, 0, 'R');
	$pdf->Cell (20, 4, $price07, 1, 0, 'R');
	$pdf->Cell (20, 4, $amt07, 1, 0, 'R');
	$pdf->Ln ();

	$pdf->Cell (20, 4, $cgi->date08, 1, 0, 'C');
	$pdf->Cell (20, 4, $cgi->item08, 1, 0, 'L');
	$pdf->Cell (80, 4, $cgi->desc08, 1, 0, 'L');
	$pdf->Cell (20, 4, $cgi->qty08, 1, 0, 'R');
	$pdf->Cell (20, 4, $price08, 1, 0, 'R');
	$pdf->Cell (20, 4, $amt08, 1, 0, 'R');
	$pdf->Ln ();

	$pdf->Cell (20, 4, $cgi->date09, 1, 0, 'C');
	$pdf->Cell (20, 4, $cgi->item09, 1, 0, 'L');
	$pdf->Cell (80, 4, $cgi->desc09, 1, 0, 'L');
	$pdf->Cell (20, 4, $cgi->qty09, 1, 0, 'R');
	$pdf->Cell (20, 4, $price09, 1, 0, 'R');
	$pdf->Cell (20, 4, $amt09, 1, 0, 'R');
	$pdf->Ln ();

	$pdf->Cell (20, 4, $cgi->date10, 1, 0, 'C');
	$pdf->Cell (20, 4, $cgi->item10, 1, 0, 'L');
	$pdf->Cell (80, 4, $cgi->desc10, 1, 0, 'L');
	$pdf->Cell (20, 4, $cgi->qty10, 1, 0, 'R');
	$pdf->Cell (20, 4, $price10, 1, 0, 'R');
	$pdf->Cell (20, 4, $amt10, 1, 0, 'R');
	$pdf->Ln ();

	// table bottom (subtotal, gst, pst, amount due)
	$subtotal = $cgi->amt01 + $cgi->amt02 +
				$cgi->amt03 + $cgi->amt04 +
				$cgi->amt05 + $cgi->amt06 +
				$cgi->amt07 + $cgi->amt08 +
				$cgi->amt09 + $cgi->amt10;
	$pdf->SetFont ('Arial', 'B', 8);
	$pdf->Cell (160, 4, 'SUBTOTAL', 1, 0, 'R', 1, 0);
	$pdf->SetFont ('Arial', '', 8);
	$pdf->Cell (20, 4, number_format ((float) $subtotal, 2), 1, 1, 'R');

	$total = $subtotal;

	foreach (appconf ('taxes') as $tax => $percent) {
		if ($cgi->{$tax} == 'yes') {
			$taxes = $subtotal * $percent;
		} else {
			$taxes = 0;
		}
		$pdf->SetFont ('Arial', 'B', 8);
		$pdf->Cell (160, 4, $tax, 1, 0, 'R', 1, 0);
		$pdf->SetFont ('Arial', '', 8);
		$pdf->Cell (20, 4, number_format ((float) $taxes, 2), 1, 1, 'R');

		$total += $taxes;
	}

	$pdf->SetFont ('Arial', 'B', 8);
	$pdf->Cell (160, 4, 'AMOUNT DUE', 1, 0, 'R', 1, 0);
	$pdf->SetFont ('Arial', '', 8);
	$pdf->Cell (20, 4, number_format ((float) $total, 2), 1, 1, 'R');
	$pdf->Ln ();

	// gst number
	//$pdf->Cell (0, 5, , 0, 1, 'R');
	$pdf->MultiCell (0, 5, appconf ('extra_info'), 0, 1, 'R');

	$taxes = $total - $subtotal;

	db_execute (
		'insert into siteinvoice_invoice (id, client_id, name, sent_on, status, notice, subtotal, taxes, total, currency) values (null, ?, ?, now(), "unpaid", 0, ?, ?, ?, ?)',
		$cgi->client,
		$cgi->name,
		$subtotal,
		$taxes,
		$total,
		$cgi->currency
	);
	$invoice_id = db_lastid ();

	$pdf->Output ('inc/app/siteinvoice/data/' . $invoice_id . '.pdf');
	umask (0000);
	chmod ('inc/app/siteinvoice/data/' . $invoice_id . '.pdf', 0777);

	if ($cgi->send_invoice == 'yes') {
		// get client info
		$client = db_single ('select * from siteinvoice_client where id = ?', $cgi->client);
		$client->invoice_no = $invoice_id;
		$client->total = $total;
		$client->currency = $cgi->currency;

		// send email to client
		loader_import ('ext.phpmailer');

		$mailer = new PHPMailer ();
		$mailer->isMail ();
		$mailer->From = appconf ('company_email');
		$mailer->FromName = appconf ('company_email_name');
		$mailer->Subject = 'Invoice #' . $invoice_id;
		$mailer->Body = template_simple ('email/initial.spt', $client);
		$mailer->AddAttachment ('inc/app/siteinvoice/data/' . $invoice_id . '.pdf', strtolower ($client->code) . '-' . $invoice_id . '.pdf');
		$mailer->AddAddress ($client->contact_email, $client->contact_name);
		$bcc_list = appconf ('bcc_list');
		if (! empty ($bcc_list)) {
			$bcc = appconf ('company_email') . ', ' . $bcc_list;
		} else {
			$bcc = appconf ('company_email');
		}
		$mailer->AddBCC ($bcc);
		$mailer->Send ();

		// reply on screen
		page_title ('SiteInvoice - Invoice Sent');
	} else {
		page_title ('SiteInvoice - Invoice Created');
	}

	echo '<p><a href="' . site_prefix () . '/index/siteinvoice-app">Continue</a></p>';

//}

//exit;

?>