var spage=0;
var orderBy="mcaption,id";
var asc="asc";

function checkPoint(obj){var s="";
	if (document.getElementById(obj)){ s=document.getElementById(obj).value; s=s.replace(",", "."); document.getElementById(obj).value=s; }
}

function showManual(key,fldVal,fldText) {
	var manValue =document.getElementById(fldVal).value;
	var manText =document.getElementById(fldText).value;
	JsHttpRequest.query($rcapi,{ 'w': 'loadManualData','key':key,'manValue':manValue,'manText':manText}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		document.getElementById("AlertModalViewHolder").innerHTML=result["content"];
		document.getElementById("key").value=key;
		document.getElementById("val").value=fldVal;
		document.getElementById("valText").value=fldText;
		$("#AlertModal").modal("show");
		$('#datatableManual').DataTable({keys: true,stateSave: true,fixedHeader: true,"lengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
	
}
function closeAlertForm() {
	$("#AlertModal").modal("hide");
	document.getElementById("AlertModalViewHolder").innerHTML=document.getElementById("hiddenModalSpin").innerHTML;
}

function AddManualValue(key){
	var newValue =document.getElementById("NewCaption").value;
	JsHttpRequest.query($rcapi,{ 'w': 'AddManualValue','key':key,'manText':newValue}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		showManual(key,document.getElementById("val").value,document.getElementById("valText").value);
	}}, true);
}
function setCheckbox(id){er=0;
	if (er==0 && !document.getElementById(id).checked){ er=1; document.getElementById(id).checked=true;}
	if (er==0 && document.getElementById(id).checked){ er=1; document.getElementById(id).checked=false;}
}
function setValue(id){
	var fValue =document.getElementById("v"+id).innerHTML;
	var fldVal=document.getElementById("val").value;
	var fldText=document.getElementById("valText").value;
	document.getElementById(fldVal).value=id;
	document.getElementById(fldText).value=fValue;
	closeAlertForm();
}

function lv_ses(){
	JsHttpRequest.query($rcapi,{ 'w': 'lv_ses'}, 
	function (result, errors){ if (errors) {} if (result){ }}, true);
}
setTimeout("lv_ses",120000);
