function checkFirmsForm(){
	var caption =$('#caption').val();
	if (caption!=""){ $('#gmanualForm').submit(); }
}

function filterGmanualList(){ 
	document.getElementById("GmanualList").innerHTML="";
	var key =document.getElementById("key").value;
	var caption =document.getElementById("SCaption").value;
	JsHttpRequest.query('content.php',{ 'w': 'showGmanualList','key':key,'caption':caption}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		$('#GmanualList').append(result["content"]);
		$('#gmanualList').fixedHeaderTable({ altClass: 'odd', footer: true });
	}}, true);
}
function lastGmanual(){ 
	if (document.getElementById("ListScroll")){	orderBy="id";asc="desc"; document.getElementById("ListScroll").innerHTML=""; spage=-1; ListScroll(); }
	else {alert("Необхідно завершити редагування картки кієнта!");}
}
function generalList(){
	if (document.getElementById("ListScroll")){	orderBy="caption";asc="asc"; document.getElementById("ListScroll").innerHTML=""; spage=-1; ListScroll(); }
	else {alert("Необхідно завершити редагування картки кієнта!");}
}
function DropGmanual(){
	var key =document.getElementById("key").value;
	var gmanualId =document.getElementById("gmanual_id").value;
	JsHttpRequest.query('content.php',{ 'w': 'DropGmanual','key':key,'gmanualId':gmanualId}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		if (result["answer"]==1){location.href='?module=1&module_page=30&file='+key;}
		if (result["answer"]!=1){alert(result["answer"]);}
	}}, true);
} 



function setModalFormData(id,caption){
	$("#GmanualItemId").val(id);
	$("#GmanualItemCaption").val(caption);
}

function sendGmanualRequest1(){
	var gkey=$("#GmanualKey").val();
	var id=$("#GmanualItemId").val();
	var caption=$("#GmanualItemCaption").val();
	JsHttpRequest.query($rcapi,{ 'w': 'sendGmanualRequest1','gkey':gkey,'id':id,'caption':caption}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		toastr[result["label"]](result["content"]);
		setTimeout("window.location.reload();", 1000);
	}}, true);
}
function checkGmanualKeyForm(){
	var id=$("#gmanual_id").val();
	var gkey=$("#gkey").val();
	var caption=$("#caption").val();
	JsHttpRequest.query($rcapi,{ 'w': 'checkGkey','gkey':gkey,'id':id}, 
	function (result, errors){ if (errors) {alert (errors);} if (result){ 
		if (result["answer"]==1){
			toastr["error"](result["content"]);
		}
		if (result["answer"]==0){ document.getElementById("GmanualForm").submit();}
	}}, true);
}