<?
class toMoney{
	function globals_var(){
		
		
		$this->_1_2[1]="����� ";
		$this->_1_2[2]="�� ";
		
		$this->_1_19[1]="���� ";
		$this->_1_19[2]="�� ";
		$this->_1_19[3]="��� ";
		$this->_1_19[4]="������ ";
		$this->_1_19[5]="�'��� ";
		$this->_1_19[6]="����� ";
		$this->_1_19[7]="�� ";
		$this->_1_19[8]="��� ";
		$this->_1_19[9]="���'��� ";
		$this->_1_19[10]="������ ";
		$this->_1_19[11]="���������� ";
		$this->_1_19[12]="���������� ";
		$this->_1_19[13]="���������� ";
		$this->_1_19[14]="������������ ";
		$this->_1_19[15]="�'��������� ";
		$this->_1_19[16]="����������� ";
		$this->_1_19[17]="��������� ";
		$this->_1_19[18]="���������� ";
		$this->_1_19[19]="���'��������� ";

		$this->des[2]="�������� ";
		$this->des[3]="�������� ";
		$this->des[4]="����� ";
		$this->des[5]="�'������� ";
		$this->des[6]="��������� ";
		$this->des[7]="������� ";
		$this->des[8]="�������� ";
		$this->des[9]="���'������ ";

		$this->hang[1]="��� ";
		$this->hang[2]="���� ";
		$this->hang[3]="������ ";
		$this->hang[4]="��������� ";
		$this->hang[5]="�'����� ";
		$this->hang[6]="������� ";
		$this->hang[7]="����� ";
		$this->hang[8]="������ ";
		$this->hang[9]="���'����� ";

		$this->namerub[1]="������ ";
		$this->namerub[2]="����� ";
		$this->namerub[3]="������� ";

		$this->nametho[1]="������ ";
		$this->nametho[2]="������ ";
		$this->nametho[3]="����� ";

		$this->namemil[1]="���� ";
		$this->namemil[2]="����� ";
		$this->namemil[3]="����� ";

		$this->namemrd[1]="����� ";
		$this->namemrd[2]="������ ";
		$this->namemrd[3]="������ ";

		$this->kopeek[1]="������ ";
		$this->kopeek[2]="������ ";
		$this->kopeek[3]="������ ";
		
		$this->fl["�"]="�";
		$this->fl["�"]="�";
		$this->fl["�"]="�";
		$this->fl["�"]="�";
		$this->fl["�"]="�";
		$this->fl["�"]="�";
		$this->fl["�"]="�";
		$this->fl["�"]="�";
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
	  if($L==0){ $s.="������� ";}
	}
	if($L >= 1000000){
		$many=0;
		$this->semantic(intval($L / 1000000),$s1,$many,2);
		$s.=$s1.$this->namemil[$many];
		$L%=1000000;
		if($L==0) {  $s.="������� "; }
	}
	if($L >= 1000){
		$many=0; $this->semantic(intval($L / 1000),$s1,$many,1);
		$s.=$s1.$this->nametho[$many];
		$L%=1000;
		if($L==0){$s.="������� ";}
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
	else{  $s.=" 00 ������";}
	$fl=substr($s,1,1);
	$s=$this->fl["$fl"].(substr($s,2));
	return $s;
}
}
?> 