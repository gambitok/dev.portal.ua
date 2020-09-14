var spage=0;
var orderBy="mcaption,id";
var asc="asc";

function filterManualPList(){ 
	document.getElementById("ManualScroll").innerHTML="";
	var filter =document.getElementById("SCaption").value;
	var key=document.getElementById("key").value;
	var parrKey =document.getElementById("parrKey").value;
	var parrKeyId =document.getElementById(parrKey).value;
	JsHttpRequest.query('content.php',{ 'w': 'showManualList','key':key,'filter':filter,'parrentKey':parrKey,'parrentKeyId':parrKeyId}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		$('#ManualScroll').append(result["content"]);
	}}, true); 
}

function showManualP(key,fldVal,fldText,parrentKey) {
	var manValue =document.getElementById(fldVal).value;
	var manText =document.getElementById(fldText).value;
	var parrKeyId =document.getElementById(parrentKey).value;
	JsHttpRequest.query('content.php',{ 'w': 'loadManualPData','key':key,'manValue':manValue,'manText':manText,'parrentKey':parrentKey,'parrentKeyId':parrKeyId}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		document.getElementById("AlertInfo").innerHTML=result["content"];
		document.getElementById("key").value=key;
		document.getElementById("parrKey").value=parrentKey;
		document.getElementById("val").value=fldVal;
		document.getElementById("valText").value=fldText;
		showAlertForm();
	}}, true);
}

function showManualP2(key,fldVal,fldText,parrentKey,parrentKeyVal) {
	var manValue =document.getElementById(fldVal).value;
	var manText =document.getElementById(fldText).value;
	var parrKeyId =document.getElementById(parrentKeyVal).value;
	JsHttpRequest.query('content.php',{ 'w': 'loadManualPData','key':key,'manValue':manValue,'manText':manText,'parrentKey':parrentKey,'parrentKeyId':parrKeyId}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		document.getElementById("AlertInfo").innerHTML=result["content"];
		document.getElementById("key").value=key;
		document.getElementById("parrKey").value=parrentKey;
		document.getElementById("parrKeyVal").value=parrentKeyVal;
		document.getElementById("val").value=fldVal;
		document.getElementById("valText").value=fldText;
		showAlertForm();
	}}, true);
}

function closeAlertForm() {
	document.getElementById("AlertForm").style.visibility="hidden";
	document.getElementById("AlertForm").style.position="absolute";
	document.getElementById("AlertForm").style.left="-150%";
	document.getElementById("AlertForm").style.top="0%";
	document.getElementById("AlertInfo").innerHTML="";
}

function AddManualPValue(key){
	var newValue =document.getElementById("NewCaption").value;
	var parrKey =document.getElementById("parrKey").value;
	var parrKeyVal =document.getElementById("parrKeyVal").value;
	if (parrKeyVal!=""){ var parrKeyId =document.getElementById(parrKeyVal).value;}
	if (parrKeyVal==""){ var parrKeyId =document.getElementById(parrKey).value;}
	JsHttpRequest.query('content.php',{ 'w': 'AddManualPValue','key':key,'manText':newValue,'parrentKey':parrKey,'parrentKeyId':parrKeyId}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		if(parrKeyVal==""){ showManualP(key,document.getElementById("val").value,document.getElementById("valText").value,parrKey);}
		if(parrKeyVal!=""){ showManualP2(key,document.getElementById("val").value,document.getElementById("valText").value,parrKey,parrKeyVal);}
	}}, true);
}

function setValueP(id){
	var fValue =document.getElementById("v"+id).innerHTML;
	var fldVal=document.getElementById("val").value;
	var fldText=document.getElementById("valText").value;
	document.getElementById(fldVal).value=id;
	document.getElementById(fldText).value=fValue;
	closeAlertForm();
}