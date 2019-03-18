var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";


$(document).ready(function() {
	$(document).bind('keydown', 'ctrl+a', function(){ ShowCheckAll2();});
	$(document).bind('keydown', 'a', function(){ ShowCheckAll2();});
	$(document).bind('keydown', 'p', function(){ ShowModalAll(); });
	$(document).bind('keydown', 'f2', function(){ document.getElementById("discountStr").focus()});			
	
});

$(window).bind('beforeunload', function(e){
    if($('#sale_invoice_id')){
		//closeSaveInvoiceCard();
		e=null;
	}
    else e=null; 
});

function printMoneySpend(spend_id){
	if (spend_id=="" || spend_id==0){toastr["error"](errs[0]);}
	if (spend_id>0){
		window.open("/MoneySpend/printMMv/"+spend_id,"_blank","printWindow");
	}
}

function showMoneySpendForm(){
	JsHttpRequest.query($rcapi,{ 'w': 'showMoneySpendForm'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Нова витрата";
		numberOnlyPlace("summ");
		$('#money_spend_tabs').tab();
	}}, true);
}

function viewMoneySpend(spend_id){
	JsHttpRequest.query($rcapi,{ 'w': 'viewMoneySpend', 'spend_id':spend_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Витрата: ВГ-"+spend_id;
		$('#money_spend_tabs').tab();
	}}, true);
}

function saveMoneySpend(){
	var paybox_id_from=$("#paybox_id_from option:selected").val();
	var balans_id_from=$("#balans_id_from option:selected").val();
	var spend_type_id=$("#spend_type_id option:selected").val();
	var summ=$("#summ_incash").val();
	var comment=$("#comment").val();
	var max_summ=$("#balans_id_from option:selected").attr('max-saldo');
	console.log("summ="+summ+"; max_summ="+max_summ);
	console.log("paybox_id_from="+paybox_id_from+">0 &&  balans_id_from="+balans_id_from+">0");
	if (paybox_id_from<=0 || spend_type_id<=0 || balans_id_from<=0){toastr["error"](errs[0]);}
	if (paybox_id_from>0 && spend_type_id>0 && balans_id_from>0){
		if (parseFloat(summ)>parseFloat(max_summ)){toastr["error"]("Введіть коректну суму для переміщення");}
		if (parseFloat(summ)<=parseFloat(max_summ)){
			JsHttpRequest.query($rcapi,{ 'w': 'saveMoneySpend', 'paybox_id_from':paybox_id_from,'balans_id_from':balans_id_from, 'spend_type_id':spend_type_id,'summ':summ,'comment':comment}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){ 
				$("#save_money_spend").attr("disabled", true); //disable button
				if (result["answer"]==1){ 
					$("#FormModalWindow").modal('hide'); document.getElementById("FormModalBody").innerHTML=""; document.getElementById("FormModalLabel").innerHTML="";
					document.location.reload();
				}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}
function getPayBoxBalans(){
	var paybox_id=$("#paybox_id option:selected").val();
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getPayBoxBalans', 'paybox_id':paybox_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#paybox_balans").html(result["content"]);
		}}, true);
	}
}

function getPayboxUserCashSaldoList(){
	var paybox_id=$("#paybox_id_from option:selected").val();
	var user_id=$("#user_id").val();
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getPayboxUserCashSaldoList', 'paybox_id':paybox_id, 'user_id':user_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#balans_id_from").html(result["content"]);
		}}, true);
	}
}

function loadCashBoxList(client_id,document_type_id,seller_id){
	//client_id=$("client_id").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadMoneySpendCashBoxList', 'client_id':client_id, 'document_type_id':document_type_id,'seller_id':seller_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("paybox_id").innerHTML=result["content"];
	}}, true);
}


function loadMoneySpendCDN(spend_id){
	if (spend_id<=0 || spend_id==""){toastr["error"](errs[0]);}
	if (spend_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadMoneySpendCDN', 'spend_id':spend_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("money_spend_cdn_place").innerHTML=result["content"];
		}}, true);
	}
}

function showMoneySpendCDNUploadForm(spend_id){
	$("#cdn_spend_id").val(spend_id);
	var myDropzone2 = new Dropzone("#myDropzone2",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone2.removeAllFiles(true);
	myDropzone2.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$('#fileMoneySpendCDNUploadForm').modal('hide');
		loadMoneySpendCDN(spend_id);
	});
}

function showMoneySpendCDNDropConfirmForm(spend_id,file_id,file_name){
	if (spend_id<=0 || spend_id==""){toastr["error"](errs[0]);}
	if (spend_id>0){
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'moneySpendCDNDropFile', 'spend_id':spend_id, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadMoneySpendCDN(spend_id); toastr["info"]("Файл успішно видалено"); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}