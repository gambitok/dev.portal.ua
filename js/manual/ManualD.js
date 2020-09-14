var spage=0;
var orderBy="mcaption,id";
var asc="asc";

function filterManualDList(){ 
	document.getElementById("ManualDScroll").innerHTML="";
	var filter =document.getElementById("SCaptionD").value;
	var key=document.getElementById("keyD").value;
	JsHttpRequest.query('content.php',{ 'w': 'showManualDList','key':key,'filter':filter}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		$('#ManualDScroll').append(result["content"]);
	}}, true); 
}

function showManualD(key,fldVal,fldText,fldDesc) {
	var manValue =document.getElementById(fldVal).value;
	var manText =document.getElementById(fldText).value;
	var manDesc =document.getElementById(fldDesc).value;
	JsHttpRequest.query('content.php',{ 'w': 'loadManualDData','key':key,'manValue':manValue,'manText':manText}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		document.getElementById("AlertInfo").innerHTML=result["content"];
		document.getElementById("keyD").value=key;
		document.getElementById("valD").value=fldVal;
		document.getElementById("valDText").value=fldText;
		document.getElementById("valDDesc").value=fldDesc;
		showAlertForm();
	}}, true);
}

function addManualD(){
	var key=document.getElementById("keyD").value;
	var fldVal=document.getElementById("valD").value;
	var fldText=document.getElementById("valDText").value;
	var fldDesc=document.getElementById("valDDesc").value;
	
	var newCaption =document.getElementById("ManualDCaptionIn").value;
	var newDesc=CKEDITOR.instances['ManualDDescIn'].getData();
	
	JsHttpRequest.query('content.php',{ 'w': 'AddManualD','key':key,'caption':newCaption,'desc':newDesc}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		if (result["answer"]=="ok"){
			document.getElementById("SCaptionD").value="";
			filterManualDList();hideManualDForm();		
		}
	}}, true);
}

function setValueD(id){
	var fldVal=document.getElementById("valD").value;
	var fldText=document.getElementById("valDText").value;
	var fldDesc=document.getElementById("valDDesc").value;
	JsHttpRequest.query('content.php',{ 'w': 'setValueD','id':id}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		document.getElementById(fldVal).value=id;
		document.getElementById(fldText).value=result["caption"];
		CKEDITOR.instances[fldDesc].setData( '<p>' + result["desc"] + '</p>' );
	}}, true);
	closeAlertForm();
}

function showManualDForm(){
	document.getElementById("manualDEditB").style.visibility="hidden";
		document.getElementById("manualDAddB").style.visibility="visible";
	document.getElementById("ManualDCaptionIn").value="";
	document.getElementById("ManualDForm").className="visibleDoc";
	document.getElementById("ManualDDescPlace").innerHTML="<textarea id=\"ManualDDescIn\"></textarea>";
	var hEd = CKEDITOR.instances['ManualDDescIn'];
    if (hEd) { CKEDITOR.remove(hEd);}
	CKEDITOR.replace( 'ManualDDescIn',{
		filebrowserBrowseUrl : 'ckfinder/ckfinder.html',
		filebrowserImageBrowseUrl : 'ckfinder/ckfinder.html?Type=Images',
		filebrowserUploadUrl : 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		filebrowserImageUploadUrl : 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images'
	});
}

function hideManualDForm(){
	document.getElementById("ManualDCaptionIn").value="";
	document.getElementById("ManualDDescIn").value="";
	var hEd = CKEDITOR.instances['ManualDDescIn'];
    if (hEd) { CKEDITOR.remove(hEd);}
	document.getElementById("ManualDDescPlace").innerHTML="";
	document.getElementById("ManualDForm").className="hiddenDoc";
}

function editManualDForm(id){
	JsHttpRequest.query('content.php',{ 'w': 'getManualDInfo','id':id}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		document.getElementById("manualDEditB").style.visibility="visible";
		document.getElementById("manualDAddB").style.visibility="hidden";
		
		document.getElementById("ManualDIdIn").value=id;
		document.getElementById("ManualDCaptionIn").value=result["caption"];
		document.getElementById("ManualDForm").className="visibleDoc";
		document.getElementById("ManualDDescPlace").innerHTML="<textarea id=\"ManualDDescIn\">"+result["desc"]+"</textarea>";
		var hEd = CKEDITOR.instances['ManualDDescIn'];
    	if (hEd) { CKEDITOR.remove(hEd);}
		CKEDITOR.replace( 'ManualDDescIn',{
			filebrowserBrowseUrl : 'ckfinder/ckfinder.html',
			filebrowserImageBrowseUrl : 'ckfinder/ckfinder.html?Type=Images',
			filebrowserUploadUrl : 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
			filebrowserImageUploadUrl : 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images'
		});
	}}, true);
}

function saveManualD(){
	var id=document.getElementById("ManualDIdIn").value;
	var key=document.getElementById("keyD").value;
	var fldVal=document.getElementById("valD").value;
	var fldText=document.getElementById("valDText").value;
	var fldDesc=document.getElementById("valDDesc").value;
	
	var newCaption =document.getElementById("ManualDCaptionIn").value;
	var newDesc=CKEDITOR.instances['ManualDDescIn'].getData();
	
	JsHttpRequest.query('content.php',{ 'w': 'saveManualD','id':id,'key':key,'caption':newCaption,'desc':newDesc}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		if (result["answer"]=="ok"){
			document.getElementById("SCaptionD").value="";
			filterManualDList();hideManualDForm();		
		}
	}}, true);
}