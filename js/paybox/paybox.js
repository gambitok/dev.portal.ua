var errs=[];
errs[0]="������� �������";
errs[1]="������� �������� ����� ��� ������";

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
		var paybox_id=result["paybox_id"];
		showPayboxCard(paybox_id);
	}}, true);
}

function showPayboxCard(paybox_id,paybox_name){
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showPayboxCard', 'paybox_id':paybox_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#PayboxCard").modal('show');
			document.getElementById("PayboxCardBody").innerHTML=result["content"];
			document.getElementById("PayboxCardLabel").innerHTML=paybox_name+" (ID:"+paybox_id+")";
			$('#Paybox_tabs').tab();
			var elem = document.querySelector('#in_use');if (elem){ var in_use = new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function savePayboxGeneralInfo(){
	swal({
		title: "�������� ���� � ����� \"�������� ����������\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���, ��������!", cancelButtonText: "³������!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var paybox_id=$("#paybox_id").val();
			var name=$("#paybox_name").val();
			var full_name=$("#paybox_full_name").val();
			var firm_id=$("#firm_id").val();
			var doc_type_id=$("#doc_type_id  option:selected").val();
			var in_use=0;if (document.getElementById("in_use").checked){in_use=1;}
			
			if (paybox_id.length>0 && doc_type_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'savePayboxGeneralInfo','paybox_id':paybox_id,'name':name,'full_name':full_name,'firm_id':firm_id,'doc_type_id':doc_type_id,'in_use':in_use},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("���������!", "������ ���� ���� ������ ��������.", "success");
						$("#PayboxCard").modal("hide");
						loadPayboxList();
					}
					else{ swal("�������!", result["error"], "error");}
				}}, true);
			} else console.log("���� �� ���");
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});	
}

function loadPayboxWorkersSaldo(paybox_id){
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadPayboxWorkersSaldo', 'paybox_id':paybox_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("paybox_saldo_place").innerHTML=result["content"];
			$('#Paybox_tabs').tab();
		}}, true);
	}
}
 
function showPayboxWorkerSaldoJournal(paybox_id, user_id, cash_id){
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showPayboxWorkerSaldoJournal', 'paybox_id':paybox_id, 'user_id':user_id, 'cash_id':cash_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function loadPayboxWorkers(paybox_id){
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadPayboxWorkers', 'paybox_id':paybox_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("paybox_workers_place").innerHTML=result["content"];
			$('#Paybox_tabs').tab();
		}}, true);
	}
}

function showPayboxWorkerForm(paybox_id, s_id){
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showPayboxWorkerForm', 'paybox_id':paybox_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			var elem = document.querySelector('#default');if (elem){ var dflt = new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function dropPaybox(paybox_id){
	swal({
		title: "�������� ���� "+paybox_id+" ?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���", cancelButtonText: "³������", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (paybox_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropPaybox','paybox_id':paybox_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("��������!", "", "success");
						$("#PayboxCard").modal("hide");
						loadPayboxList();
					}
					else{ swal("�������!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});
}

function dropPayboxWorker(paybox_id,s_id){
	swal({
		title: "�������� ����������� � ����?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���", cancelButtonText: "³������", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (paybox_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropPayboxWorker','paybox_id':paybox_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("��������!", "", "success");
						loadPayboxWorkers(paybox_id);
					}
					else{ swal("�������!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});
}

function savePayboxWorkerForm(paybox_id,s_id){
	var paybox_worker_name=$("#worker_id option:selected").html();
	swal({
		title: "�������� �����������  \""+paybox_worker_name+"\" �� �����?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���", cancelButtonText: "³������", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var worker_id=$("#worker_id option:selected").val();
			if (paybox_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'savePayboxWorkerForm','paybox_id':paybox_id,'s_id':s_id,'worker_id':worker_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("���������!", "������ ���� ���� ������ ��������.", "success");
						$("#FormModalWindow").modal("hide");
						loadPayboxWorkers(paybox_id);
					}
					else{ swal("�������!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});
}

function showPayboxClientList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showPayboxClientList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
		document.getElementById("FormModalBody2").innerHTML=result["content"];
		document.getElementById("FormModalLabel2").innerHTML="�����������";
	}}, true);
}

function setPayboxClient(id,name){
	$('#firm_id').val(id);
	$('#firm_name').val(name);
	$("#FormModalWindow2").modal('hide');
	document.getElementById("FormModalBody2").innerHTML="";
	document.getElementById("FormModalLabel2").innerHTML="";
}