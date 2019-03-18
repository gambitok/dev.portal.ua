var spage=0;
var orderBy="mcaption,id";
var asc="asc";

function filterManualManagerList(){ 
	document.getElementById("ManualScroll").innerHTML="";
	var filter =document.getElementById("SCaption").value;
	var bankOc =document.getElementById("bankOc").value;
	JsHttpRequest.query('content.php',{ 'w': 'showManualManagerList','filter':filter,'bankOc':bankOc}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		$('#ManualScroll').append(result["content"]);
	}}, true); 
}
function showManualManager(fldVal,fldText) {
	var manValue =document.getElementById(fldVal).value;
	var manText =document.getElementById(fldText).value;
	var bankOc=document.getElementById("bankOc").value;
	JsHttpRequest.query('content.php',{ 'w': 'loadManualManagerData','manValue':manValue,'manText':manText,'bankOc':bankOc}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		document.getElementById("AlertInfo").innerHTML=result["content"];
		document.getElementById("val").value=fldVal;
		document.getElementById("valText").value=fldText;
		showAlertForm();
	}}, true);
	
}
function AddManualManagerValue(){
	var name =document.getElementById("NewManagerName").value;
	var city =document.getElementById("NewManagerCity").value;
	var phone =document.getElementById("NewManagerPhone").value;
	var email =document.getElementById("NewManagerEmail").value;
	var persent =document.getElementById("NewManagerPersent").value;
	var bankOc =document.getElementById("bankOc").value;

	JsHttpRequest.query('content.php',{ 'w': 'AddManualManagerValue','name':name,'city':city,'phone':phone,'email':email,'persent':persent,'bankOc':bankOc}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		showManualManager(document.getElementById("val").value,document.getElementById("valText").value);
	}}, true);
}
function setManualManager(id){
	var fValue =document.getElementById("v"+id).innerHTML;
	var fldVal=document.getElementById("val").value;
	var fldText=document.getElementById("valText").value;
	document.getElementById(fldVal).value=id;
	document.getElementById(fldText).value=fValue;
	closeAlertForm();
}