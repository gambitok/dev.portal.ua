<?php
/**
 * XLS parsing uses php-excel-reader from http://code.google.com/p/php-excel-reader/
 */
	header('Content-Type: text/plain');
	if (isset($argv[1])){
		$Filepath = $argv[1];
	}
	elseif (isset($_GET['File']))
	{
		$Filepath = $_GET['File'];
	}
	else
	{
		if (php_sapi_name() == 'cli')
		{
			echo 'Please specify filename as the first argument'.PHP_EOL;
		}
		else
		{
			echo 'Please specify filename as a HTTP GET parameter "File", e.g., "/test.php?File=test.xlsx"';
		}
		exit;
	}

	// Excel reader from http://code.google.com/p/php-excel-reader/
	require('php-excel-reader/excel_reader2.php');
	require('SpreadsheetReader.php');

	date_default_timezone_set('UTC'); $krs=0;
	try{
		$Spreadsheet = new SpreadsheetReader($Filepath);
		$Sheets = $Spreadsheet -> Sheets();
		print_r($Sheets);

		foreach ($Sheets as $Index => $Name){
			echo '---------------------------------'.PHP_EOL;
			echo '*** Sheet '.$Name.' ***'.PHP_EOL;
			echo '---------------------------------'.PHP_EOL;

			$Spreadsheet -> ChangeSheet($Index);
			foreach ($Spreadsheet as $Key => $Row){
				echo $Key.': ';
				if ($Row){
					foreach($Row as $rw){
						print(iconv("UTF-8", "Windows-1251",$rw)." | ");
					}
				}
				print "\n";
				$krs+=1;
				if ($krs==50){break;}
			}
			echo '---------------------------------'.PHP_EOL;
			echo '*** End of sheet '.$Name.' ***'.PHP_EOL;
			echo '---------------------------------'.PHP_EOL;
		}
		
	}
	catch (Exception $E)
	{
		echo $E -> getMessage();
	}
?>
