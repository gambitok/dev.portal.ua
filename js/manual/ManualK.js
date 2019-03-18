function showManualK(key,fldVal,fldText,filter) {
	var manValue =document.getElementById(fldVal).value;
	var manText =document.getElementById(fldText).value;
	var filterValue=document.getElementById(filter).value; alert("ok");
	JsHttpRequest.query('content.php',{ 'w': 'loadManualKData','key':key,'manValue':manValue,'manText':manText,'filter':filterValue}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		document.getElementById("AlertInfo").innerHTML=result["content"];
		document.getElementById("keyK").value=key;
		document.getElementById("valK").value=fldVal;
		document.getElementById("valKText").value=fldText;
		showAlertForm();
		$('#ManualKScroll').slimscroll({width: '100%', height: '500px', size: '10px', color: '#1361b7', distance: '0px', railVisible: true, railColor: '#ccc', railOpacity: 0.3, wheelStep: 10, allowPageScroll: false, disableFadeOut: false });
	}}, true);
	
}
function setValueK(){
	var key=document.getElementById("keyK").value;
	var fldVal=document.getElementById("valK").value;
	var fldText=document.getElementById("valKText").value;
	
	
	var kol=document.getElementById("kolKonstr").value;
	var result="";var resultText="";
	var tid="";var tcap="";
	for (i=1;i<=kol;i++){
		if (document.getElementById("constructiv_"+i)){
			if (document.getElementById("constructiv_"+i).checked){
				tid=document.getElementById("constructiv_"+i).value; result+=tid+";";
				tcap=document.getElementById("contr_cap_"+i).innerHTML; resultText+=tcap+";";
			}
		}
	}
	document.getElementById(fldVal).value=result;
	document.getElementById(fldText).value=resultText;
	closeAlertForm();
}
function showManualKForm(){
	document.getElementById("ManualKCaptionIn").value="";
	document.getElementById("ManualKForm").className="visibleDoc";
	document.getElementById("ManualKDescIn").value="";
	document.getElementById("ManualKZnosFromIn").value="";
	document.getElementById("ManualKZnosToIn").value="";
}
function hideManualKForm(){
	document.getElementById("ManualKCaptionIn").value="";
	document.getElementById("ManualKDescIn").value="";
	document.getElementById("ManualKZnosFromIn").value="";
	document.getElementById("ManualKZnosToIn").value="";
	document.getElementById("ManualKForm").className="hiddenDoc";
}