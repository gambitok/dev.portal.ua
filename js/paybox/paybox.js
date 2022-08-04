var errs = [];
errs[0] = "Помилка індексу";
errs[1] = "Занадто короткий запит для пошуку";

function loadPayboxList() {
	JsHttpRequest.query($rcapi,{ 'w': 'loadPayboxList'},
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#paybox_range").empty();
		$("#paybox_range").html(result["content"]);
	}}, true);
}

function newPayboxCard() {
	JsHttpRequest.query($rcapi,{ 'w': 'newPayboxCard'},
	function (result, errors){ if (errors) {alert(errors);} if (result){
		showPayboxCard(result["paybox_id"]);
	}}, true);
}

function showPayboxCard(paybox_id, paybox_name) {
	if (paybox_id<=0 || paybox_id=="") {toastr["error"](errs[0]);}
	if (paybox_id>0) {
		JsHttpRequest.query($rcapi,{ 'w': 'showPayboxCard', 'paybox_id':paybox_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#PayboxCard").modal("show");
			$("#PayboxCardBody").html(result["content"]);
			$("#PayboxCardLabel").html(paybox_name + " (ID:"+paybox_id+")");
			$("#Paybox_tabs").tab();
			var elem = document.querySelector("#in_use");
			if (elem){ new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function savePayboxGeneralInfo() {
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
			if (paybox_id.length>0 && doc_type_id.length>0) {
				JsHttpRequest.query($rcapi,{'w':'savePayboxGeneralInfo', 'paybox_id':paybox_id, 'name':name, 'full_name':full_name, 'firm_id':firm_id, 'doc_type_id':doc_type_id, 'in_use':in_use},
				function (result, errors){ if (errors) {alert(errors);} if (result){
					if (result["answer"] == 1) {
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#PayboxCard").modal("hide");
						loadPayboxList();
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadPayboxWorkersSaldo(paybox_id) {
	if (paybox_id<=0 || paybox_id=="") {toastr["error"](errs[0]);}
	if (paybox_id>0) {
		JsHttpRequest.query($rcapi,{ 'w': 'loadPayboxWorkersSaldo', 'paybox_id':paybox_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#paybox_saldo_place").html(result["content"]);
            $("#Paybox_tabs").tab();
		}}, true);
	}
}

function showPayboxWorkerSaldoJournal(paybox_id, user_id, cash_id) {
	if (paybox_id<=0 || paybox_id=="") {toastr["error"](errs[0]);}
	if (paybox_id>0) {
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showPayboxWorkerSaldoJournal', 'paybox_id':paybox_id, 'user_id':user_id, 'cash_id':cash_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#FormModalBody").html(result["content"]);
		}}, true);
	}
}

function loadPayboxWorkers(paybox_id) {
	if (paybox_id<=0 || paybox_id=="") {toastr["error"](errs[0]);}
	if (paybox_id>0) {
		JsHttpRequest.query($rcapi,{ 'w': 'loadPayboxWorkers', 'paybox_id':paybox_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#paybox_workers_place").html(result["content"]);
            $("#Paybox_tabs").tab();
		}}, true);
	}
}

function showPayboxWorkerForm(paybox_id, s_id) {
	if (paybox_id<=0 || paybox_id=="") {toastr["error"](errs[0]);}
	if (paybox_id>0) {
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showPayboxWorkerForm', 'paybox_id':paybox_id, 's_id':s_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalBody").html(result["content"]);
            var elem = document.querySelector("#default");if (elem){ new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function dropPaybox(paybox_id) {
	swal({
		title: "Видалити касу " + paybox_id + " ?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (paybox_id.length > 0) {
				JsHttpRequest.query($rcapi,{ 'w':'dropPaybox', 'paybox_id':paybox_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){
					if (result["answer"] == 1) {
						swal("Видалено!", "", "success");
						$("#PayboxCard").modal("hide");
						loadPayboxList();
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function dropPayboxWorker(paybox_id, s_id) {
	swal({
		title: "Видалити користувача з каси?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (paybox_id.length > 0) {
				JsHttpRequest.query($rcapi,{ 'w':'dropPayboxWorker', 'paybox_id':paybox_id, 's_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){
					if (result["answer"] == 1) {
						swal("Видалено!", "", "success");
						loadPayboxWorkers(paybox_id);
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function savePayboxWorkerForm(paybox_id, s_id) {
    let paybox_worker_name = $("#worker_id option:selected").html();
	swal({
		title: "Закріпити користувача  \""+paybox_worker_name+"\" за касою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let worker_id = $("#worker_id option:selected").val();
			if (paybox_id.length > 0) {
				JsHttpRequest.query($rcapi,{ 'w':'savePayboxWorkerForm', 'paybox_id':paybox_id, 's_id':s_id, 'worker_id':worker_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){
					if (result["answer"] == 1){
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadPayboxWorkers(paybox_id);
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function showPayboxClientList(client_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showPayboxClientList', 'client_id':client_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#FormModalWindow2").modal("show");
        $("#FormModalBody2").html(result["content"]);
        $("#FormModalLabel2").html("Контрагенти");
	}}, true);
}

function setPayboxClient(id, name) {
	$("#firm_id").val(id);
	$("#firm_name").val(name);
	$("#FormModalWindow2").modal("hide");
    $("#FormModalBody2").html("");
    $("#FormModalLabel2").html("");
}

/*
* PAYBOX CONVERT
* */

function loadPayboxConvertRange() {
	let paybox_id_select = $("#paybox_id_select option:selected").val();
	let date_start_select = $("#date_start").val();
	let date_end_select = $("#date_end").val();
	if (paybox_id_select === "0" && date_start_select === "" && date_end_select === "") {
		toastr["error"]("Спочатку виберіть хоч одне поле!");
	} else {
		JsHttpRequest.query($rcapi,{ 'w': 'loadPayboxConvertRange', 'paybox_id_select':paybox_id_select, 'date_start_select':date_start_select, 'date_end_select':date_end_select},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				let dt = $("#datatable");
				dt.DataTable().destroy();
				$("#paybox_convert_range").html(result["content"]);
				dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
			}}, true);
	}
}

function changePayboxConvertSumm() {
	let paybox_id = $("#paybox_id option:selected").val();
	let cash_id_from = $("#cash_id_from option:selected").val();
	let cash_id_to = $("#cash_id_to option:selected").val();
	let kours_to = $("#kours_to").val();
	let price_from = $("#price_from").val();

	JsHttpRequest.query($rcapi,{ 'w': 'changePayboxConvertSumm', 'paybox_id':paybox_id, 'cash_id_from':cash_id_from, 'price_from':price_from, 'cash_id_to':cash_id_to, 'kours_to':kours_to},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let price_from_max = result["price_from_max"];
			let kours_from = result["kours_from"];
			let price_to = result["price_to"];
			let kours_to_err = result["kours_to_err"];

			if (parseFloat(price_from) > parseFloat(price_from_max)) {
				$("#price_from").val(price_from_max);
				price_from = price_from_max;
			}

			$("#price_from_max").text(price_from_max);
			$("#kours_from").text(kours_from);
			$("#price_to").val(price_to);

			if (kours_to_err > 0) {
				$("#kours_to_err").removeClass("none");
			} else {
				$("#kours_to_err").addClass("none");
			}

			if (parseFloat(price_to) > 0) {
				$("#save_btn").prop("disabled", false);
			} else {
				$("#save_btn").prop("disabled", true);
			}

		}}, true);
}

function savePayboxConvert() {
	let paybox_id = $("#paybox_id option:selected").val();
	let cash_id_from = $("#cash_id_from option:selected").val();
	let cash_id_to = $("#cash_id_to option:selected").val();
	let kours_to = $("#kours_to").val();
	let price_from = $("#price_from").val();
	let price_to = $("#price_to").val();
	let note = $("#note").val();
	JsHttpRequest.query($rcapi,{ 'w':'savePayboxConvert', 'paybox_id':paybox_id, 'cash_id_from':cash_id_from, 'cash_id_to':cash_id_to, 'kours_to':kours_to, 'price_from':price_from, 'price_to':price_to, 'note':note},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
				clearConvertForm();
				loadPayboxConvertRange();
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

function showCashListSelected() {
	let sel = $("#cash_id_from option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'showCashListSelected', 'sel':sel},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#cash_id_to").html(result.content);
		}}, true);
}

function clearConvertForm() {
	$("#paybox_id").val("0");
	$("#cash_id_from").val("0");
	$("#cash_id_to").val("0");
	$("#price_from").val(0);
	$("#kours_to").val(0);
	$("#price_to").val(0);
	$("#note").val("");
	$("#price_from_max").val("");
	$("#kours_from").val("");
	showCashListSelected();
}

function saveCbTpoint() {
	let prro_id = $('input[name="cash_registers"]:checked').val();
	JsHttpRequest.query($rcapi,{ 'w': 'saveCbTpoint', 'prro_id':prro_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			alert('done!');
		}}, true);
}

function createXReport(prro_id) {
	JsHttpRequest.query($rcapi,{ 'w':'createXReport', 'prro_id':prro_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				$("#XOrderModal").modal("show");
				$("#XOrderBody").html(result["error"]);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

function showCheckReport(prro_id, check_id) {
	JsHttpRequest.query($rcapi,{ 'w':'showCheckReport', 'prro_id':prro_id, 'check_id':check_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				$("#XOrderModal").modal("show");
				$("#XOrderBody").html(result["error"]);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

function openServiceReceipt(prro_id, doc_type_id) {
	$("#ServiceModal").modal("show");
	$("#service_prro_id").val(prro_id);
	$("#service_doc_type_id").val(doc_type_id);
}

function createServiceReceipt() {
	let prro_id = $("#service_prro_id").val();
	let doc_type_id = $("#service_doc_type_id").val();
	let payment_type_id = $('input[name="payment_type_id"]:checked').val();
	let sum = $("#service_sum").val();

	JsHttpRequest.query($rcapi,{ 'w':'createServiceReceipt', 'prro_id':prro_id, 'sum':sum, 'doc_type_id':doc_type_id, 'payment_type_id':payment_type_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				swal("Зміни внесенно!", result["error"], "success");
				$("#ServiceModal").modal("hide");
				$("#service_sum").val("0")
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}