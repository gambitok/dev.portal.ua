var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

function loadPayboxList(){
	JsHttpRequest.query($rcapi,{ 'w': 'loadPayboxList'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#paybox_range").empty();
		$("#paybox_range").html(result["content"]);
	}}, true);
}

function newPayboxCard(){
	JsHttpRequest.query($rcapi,{ 'w': 'newPayboxCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		showPayboxCard(result["paybox_id"]);
	}}, true);
}

function showPayboxCard(paybox_id,paybox_name){
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showPayboxCard', 'paybox_id':paybox_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#PayboxCard").modal("show");
			$("#PayboxCardBody").html(result["content"]);
			$("#PayboxCardLabel").html(paybox_name+" (ID:"+paybox_id+")");
			$("#Paybox_tabs").tab();
			var elem = document.querySelector("#in_use");if (elem){ new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function savePayboxGeneralInfo(){
	swal({
		title: "Зберегти зміни у розділі \"Загальна інформація\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let paybox_id=$("#paybox_id").val();
            let name=$("#paybox_name").val();
            let full_name=$("#paybox_full_name").val();
            let firm_id=$("#firm_id").val();
            let doc_type_id=$("#doc_type_id option:selected").val();
            let in_use=0;if (document.getElementById("in_use").checked){in_use=1;}
			if (paybox_id.length>0 && doc_type_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'savePayboxGeneralInfo','paybox_id':paybox_id,'name':name,'full_name':full_name,'firm_id':firm_id,'doc_type_id':doc_type_id,'in_use':in_use},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#PayboxCard").modal("hide");
						loadPayboxList();
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}

function loadPayboxWorkersSaldo(paybox_id){
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadPayboxWorkersSaldo', 'paybox_id':paybox_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#paybox_saldo_place").html(result["content"]);
            $("#Paybox_tabs").tab();
		}}, true);
	}
}
 
function showPayboxWorkerSaldoJournal(paybox_id, user_id, cash_id){
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showPayboxWorkerSaldoJournal', 'paybox_id':paybox_id, 'user_id':user_id, 'cash_id':cash_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalBody").html(result["content"]);
		}}, true);
	}
}

function loadPayboxWorkers(paybox_id){
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadPayboxWorkers', 'paybox_id':paybox_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#paybox_workers_place").html(result["content"]);
            $("#Paybox_tabs").tab();
		}}, true);
	}
}

function showPayboxWorkerForm(paybox_id, s_id){
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showPayboxWorkerForm', 'paybox_id':paybox_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#FormModalBody").html(result["content"]);
            var elem = document.querySelector("#default");if (elem){ new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function dropPaybox(paybox_id){
	swal({
		title: "Видалити касу "+paybox_id+" ?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (paybox_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropPaybox', 'paybox_id':paybox_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						$("#PayboxCard").modal("hide");
						loadPayboxList();
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function dropPayboxWorker(paybox_id,s_id){
	swal({
		title: "Видалити користувача з каси?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (paybox_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropPayboxWorker','paybox_id':paybox_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadPayboxWorkers(paybox_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function savePayboxWorkerForm(paybox_id,s_id){
    let paybox_worker_name=$("#worker_id option:selected").html();
	swal({
		title: "Закріпити користувача  \""+paybox_worker_name+"\" за касою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let worker_id=$("#worker_id option:selected").val();
			if (paybox_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'savePayboxWorkerForm', 'paybox_id':paybox_id, 's_id':s_id, 'worker_id':worker_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadPayboxWorkers(paybox_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function showPayboxClientList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showPayboxClientList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal("show");
        $("#FormModalBody2").html(result["content"]);
        $("#FormModalLabel2").html("Контрагенти");
	}}, true);
}

function setPayboxClient(id,name){
	$("#firm_id").val(id);
	$("#firm_name").val(name);
	$("#FormModalWindow2").modal("hide");
    $("#FormModalBody2").html("");
    $("#FormModalLabel2").html("");
}
