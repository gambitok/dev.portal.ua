<?
class toMoney{
	function globals_var(){
		
		
		$this->_1_2[1]="оќдна ";
		$this->_1_2[2]="дв≥ ";
		
		$this->_1_19[1]="одна ";
		$this->_1_19[2]="дв≥ ";
		$this->_1_19[3]="три ";
		$this->_1_19[4]="чотири ";
		$this->_1_19[5]="п'€ть ";
		$this->_1_19[6]="ш≥сть ";
		$this->_1_19[7]="с≥м ";
		$this->_1_19[8]="в≥с≥м ";
		$this->_1_19[9]="дев'€ть ";
		$this->_1_19[10]="дес€ть ";
		$this->_1_19[11]="одинадц€ть ";
		$this->_1_19[12]="дванадц€ть ";
		$this->_1_19[13]="тринадц€ть ";
		$this->_1_19[14]="чотирнадц€ть ";
		$this->_1_19[15]="п'€тнадц€ть ";
		$this->_1_19[16]="ш≥стнадц€ть ";
		$this->_1_19[17]="с≥мнадц€ть ";
		$this->_1_19[18]="в≥с≥мнадц€ть ";
		$this->_1_19[19]="дев'€тнадц€ть ";

		$this->des[2]="двадц€ть ";
		$this->des[3]="тридц€ть ";
		$this->des[4]="сорок ";
		$this->des[5]="п'€тдес€т ";
		$this->des[6]="ш≥стдес€т ";
		$this->des[7]="с≥мдес€т ";
		$this->des[8]="в≥с≥мдес€т ";
		$this->des[9]="дев'€носто ";

		$this->hang[1]="сто ";
		$this->hang[2]="дв≥ст≥ ";
		$this->hang[3]="триста ";
		$this->hang[4]="чотириста ";
		$this->hang[5]="п'€тсот ";
		$this->hang[6]="ш≥стсот ";
		$this->hang[7]="с≥мсот ";
		$this->hang[8]="в≥с≥мсот ";
		$this->hang[9]="дев'€тсот ";

		$this->namerub[1]="гривн€ ";
		$this->namerub[2]="гривн≥ ";
		$this->namerub[3]="гривень ";

		$this->nametho[1]="тис€ча ";
		$this->nametho[2]="тис€ч≥ ";
		$this->nametho[3]="тис€ч ";

		$this->namemil[1]="м≥л≥он ";
		$this->namemil[2]="м≥л≥она ";
		$this->namemil[3]="м≥л≥он≥в ";

		$this->namemrd[1]="м≥л≥ард ";
		$this->namemrd[2]="м≥л≥арда ";
		$this->namemrd[3]="м≥л≥ард≥в ";

		$this->kopeek[1]="коп≥йка ";
		$this->kopeek[2]="коп≥йки ";
		$this->kopeek[3]="коп≥йок ";
		
		$this->fl["с"]="—";
		$this->fl["с"]="—";
		$this->fl["т"]="“";
		$this->fl["ч"]="„";
		$this->fl["п"]="ѕ";
		$this->fl["ш"]="Ў";
		$this->fl["в"]="¬";
		$this->fl["д"]="ƒ";
	}

function semantic($i,&$words,&$many,$f){
	$words="";
	$fl=0;
	if($i >= 100){
		$jkl = intval($i / 100);
		$words.=$this->hang[$jkl];
		$i%=100;
	}
	if($i >= 20){
		$jkl = intval($i / 10);
		$words.=$this->des[$jkl];
		$i%=10;
		$fl=1;
	}
switch($i){
  case 1: $many=1; break;
  case 2:
  case 3:
  case 4: $many=2; break;
  default: $many=3; break;
}
if($i){
  if($i < 3 && $f == 1)
   $words.=$this->_1_2[$i];
  else
   $words.=$this->_1_19[$i];
}
}
function num2str($L){
	$this->globals_var();
	$s=" ";$s1=" "; 
	$kop=intval(( intval(round($L*100)) - intval($L)*100 ));
	$L=intval($L);
	if($L>=1000000000){
	  $many=0;
	  $this->semantic(intval($L / 1000000000),$s1,$many,3);
	  $s.=$s1.$this->namemrd[$many];
	  $L%=1000000000;
	  if($L==0){ $s.="гривень ";}
	}
	if($L >= 1000000){
		$many=0;
		$this->semantic(intval($L / 1000000),$s1,$many,2);
		$s.=$s1.$this->namemil[$many];
		$L%=1000000;
		if($L==0) {  $s.="гривень "; }
	}
	if($L >= 1000){
		$many=0; $this->semantic(intval($L / 1000),$s1,$many,1);
		$s.=$s1.$this->nametho[$many];
		$L%=1000;
		if($L==0){$s.="гривень ";}
	}
	if($L != 0){
		$many=0;$this->semantic($L,$s1,$many,0);
		$s.=$s1.$this->namerub[$many];
	}
	if($kop > 0){
		$many=0;$this->semantic($kop,$s1,$many,1);
		//$s.=$s1.$this->kopeek[$many];
		$s.=$kop." ".$this->kopeek[$many];
	}
	else{  $s.=" 00 коп≥йок";}
	$fl=substr($s,1,1);
	$s=$this->fl["$fl"].(substr($s,2));
	return $s;
}
}
?> 