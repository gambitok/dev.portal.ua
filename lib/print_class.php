<?php

class media_print {

	function print_doc($data,$size){ $tOp=$_REQUEST["tOp"];if ($tOp==""){$tOp="I";}$tOpPath="";if ($tOp=="F"){$tOpPath=RD."/uploads/print/";}
		include("MPDF56/mpdf.php"); if ($size==""){$size="A4";}
		$mpdf = new mPDF('windows-1251', $size , '12', '', 10, 5, 5, 5, 0, 3);
		$mpdf->charset_in = 'windows-1251'; /*не забываем про русский*/
		$stylesheet = file_get_contents(RD.'/css/bootstrap.css');$mpdf->WriteHTML($stylesheet, 1);
		$stylesheet = file_get_contents(RD.'/css/sb-admin-print.css');$mpdf->WriteHTML($stylesheet, 1);
		$stylesheet = file_get_contents(RD.'/css/morris.css');$mpdf->WriteHTML($stylesheet, 1);
		$stylesheet = file_get_contents(RD.'/css/font-awesome.css');$mpdf->WriteHTML($stylesheet, 1);
		$stylesheet = file_get_contents('https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css');$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->list_indent_first_level = 0;
		$mpdf->WriteHTML($data, 2);
		session_start();
		$mpdf->setFooter(''.iconv("windows-1251","utf-8","PORTAL.MYPARTS.PRO || Видрук від: ").date("Y-m-d H:i:s").'||{PAGENO}');
		$mpdf->Output($tOpPath.'MypartsReport-'.date("Y-m-d-H-i-s").'.pdf', $tOp);
		exit;
	}

	function print_document($data,$size){ $tOp=$_REQUEST["tOp"];if ($tOp==""){$tOp="I";}$tOpPath="";if ($tOp=="F"){$tOpPath=RD."/uploads/print/";}
		include("MPDF56/mpdf.php"); if ($size==""){$size="A4";}
		$mpdf = new mPDF('windows-1251', $size , '12', '', 2, 2, 10, 10, 2, 2);
		$mpdf->charset_in = 'windows-1251'; /*не забываем про русский*/
		//styles
		$stylesheet = file_get_contents(RD.'/css/bootstrap.css');$mpdf->WriteHTML($stylesheet, 1);
		$stylesheet = file_get_contents(RD.'/css/sb-admin-print.css');$mpdf->WriteHTML($stylesheet, 1);
		$stylesheet = file_get_contents(RD.'/css/morris.css');$mpdf->WriteHTML($stylesheet, 1);
		$stylesheet = file_get_contents(RD.'/css/font-awesome.css');$mpdf->WriteHTML($stylesheet, 1);
		$stylesheet = file_get_contents(RD.'/css/style.css');$mpdf->WriteHTML($stylesheet, 1);
		$stylesheet = file_get_contents('https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css');
		$mpdf->WriteHTML($stylesheet, 1);//
		$mpdf->list_indent_first_level = 0;//
		$mpdf->SetDisplayMode('fullpage');//
		$mpdf->WriteHTML($data, 2);
		$mpdf->SetHTMLFooter( "" ); 
		session_start();
		//$mpdf->setFooter('abc.helios-therm.com.ua - '.iconv("windows-1251","utf-8",$_SESSION["user_name"]).', '.iconv("windows-1251","utf-8",$_SESSION["user_post"]).'||{PAGENO}');
		$mpdf->Output($tOpPath.'MypartsPDF-'.date("Y-m-d-H-i-s").'.pdf', $tOp);
		exit;
	}

}
