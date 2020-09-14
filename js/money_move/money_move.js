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
    if($("#sale_invoice_id")){
		e=null;
	}
    else e=null; 
});

function printMoneyMove(move_id){
	if (move_id=="" || move_id==0){toastr["error"](errs[0]);}
	if (move_id>0){
		window.open("/MoneyMove/printMMv/"+move_id,"_blank","printWindow");
	}
}

function showMoneyMoveForm(){
	JsHttpRequest.query($rcapi,{ 'w': 'showMoneyMoveForm'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
		$("#FormModalBody").html(result["content"]);
		$("#FormModalLabel").html("Нове переміщення грошей");
		numberOnlyPlace("summ");
	}}, true);
}

function viewMoneyMove(move_id){
	JsHttpRequest.query($rcapi,{ 'w': 'viewMoneyMove', 'move_id':move_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
        $("#FormModalBody").html(result["content"]);
        $("#FormModalLabel").html("Переміщення грошей: ПГ-"+move_id);
	}}, true);
}

function saveMoneyMove(){
    let paybox_id_from=$("#paybox_id_from option:selected").val();
    let paybox_id_to=$("#paybox_id_to option:selected").val();
    let user_id_to=$("#user_id_to option:selected").val();
    let balans_id_from=$("#balans_id_from option:selected").val();
    let summ=$("#summ_incash").val();
    let max_summ=$("#balans_id_from option:selected").attr('max-saldo');
	console.log("summ="+summ+"; max_summ="+max_summ);
	console.log("paybox_id_from="+paybox_id_from+">0 && paybox_id_to="+paybox_id_to+">0 && user_id_to="+user_id_to+">0 && balans_id_from="+balans_id_from+">0");
	if (paybox_id_from<=0 || paybox_id_to<=0 || user_id_to<=0 || balans_id_from<=0){toastr["error"](errs[0]);}
	if (paybox_id_from>0 && paybox_id_to>0 && user_id_to>0 && balans_id_from>0){
		if (parseFloat(summ)>parseFloat(max_summ)){toastr["error"]("Введіть коректну суму для переміщення");}
		if (parseFloat(summ)<=parseFloat(max_summ)){
			JsHttpRequest.query($rcapi,{ 'w': 'saveMoneyMove', 'paybox_id_from':paybox_id_from, 'paybox_id_to':paybox_id_to, 'user_id_to':user_id_to, 'balans_id_from':balans_id_from, 'summ':summ},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					$("#FormModalWindow").modal("hide");
                    $("#FormModalBody").html("");
                    $("#FormModalLabel").html("");
					document.location.reload();
				} else { toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function acceptMoneyMove(){
	let move_id=$("#move_id").val();
	if (move_id<=0 || move_id<=""){toastr["error"](errs[0]);}
	if (move_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'acceptMoneyMove', 'move_id':move_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Виконано!", "Переміщення грошей успішно завершено!", "success");
				$("#FormModalWindow").modal("hide");
                $("#FormModalBody").html("");
                $("#FormModalLabel").html("");
				setTimeout(function(){ document.location.reload();},1000);
			} else { toastr["error"](result["error"]); }
		}}, true);
	}
}

function getPayBoxBalans(){
    let paybox_id=$("#paybox_id option:selected").val();
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getPayBoxBalans', 'paybox_id':paybox_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#paybox_balans").html(result["content"]);
		}}, true);
	}
}

function getPayboxUserCashSaldoList(){
    let paybox_id=$("#paybox_id_from option:selected").val();
    let user_id=$("#user_id").val();
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getPayboxUserCashSaldoList', 'paybox_id':paybox_id, 'user_id':user_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#balans_id_from").html(result["content"]);
		}}, true);
	}
}

function getPayboxResiverList(){
    let paybox_id=$("#paybox_id_from option:selected").val();
    let balans_id_from=$("#balans_id_from option:selected").val();
    let user_id=$("#user_id").val();
	if (balans_id_from<=0 || balans_id_from==""){toastr["error"](errs[0]);}
	if (balans_id_from>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getPayboxResiverList', 'paybox_id':paybox_id, 'balans_id_from':balans_id_from, 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#paybox_id_to").html(result["content"]);
		}}, true);
	}
}

function getPayboxManagerList(){
    let paybox_id_to=$("#paybox_id_to option:selected").val();
    let balans_id_from=$("#balans_id_from option:selected").val();
	if (paybox_id_to<=0 || paybox_id_to==""){toastr["error"](errs[0]);}
	if (paybox_id_to>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getPayboxManagerList', 'paybox_id':paybox_id_to, 'balans_id_from':balans_id_from},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#user_id_to").html(result["content"]);
		}}, true);
	}
}

function loadCashBoxList(client_id,document_type_id,seller_id){
	JsHttpRequest.query($rcapi,{ 'w': 'loadMoneyMoveCashBoxList', 'client_id':client_id, 'document_type_id':document_type_id, 'seller_id':seller_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#paybox_id").html(result["content"]);
	}}, true);
}
