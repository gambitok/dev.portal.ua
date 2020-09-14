var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

function showKoursList(){
	$("#catalogue_range").empty();
	JsHttpRequest.query($rcapi,{ 'w': 'showKoursList'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#kours_range").html(result["content"]);
		toastr["info"]("Виконано!");
	}}, true);
}

function newKoursCard(){
	JsHttpRequest.query($rcapi,{ 'w': 'newKoursCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		let kours_id=result["kours_id"];
		showKoursCard(kours_id);
	}}, true);
}

function showKoursCard(kours_id){
	if (kours_id<=0 || kours_id===""){toastr["error"](errs[0]);}
	if (kours_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showKoursCard', 'kours_id':kours_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#KoursCard").modal('show');
			document.getElementById("KoursCardBody").innerHTML=result["content"];
		}}, true);
	}
}
function saveKoursForm(){
	swal({
		title: "Встановити новий курс валют?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Встановити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let kours_id=$("#kours_id").val();
            let kours_value=$("#kours_value").val();
            let cash_id=$("#cash_id option:selected").val();
			if (kours_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveKoursForm', 'kours_id':kours_id, 'kours_value':kours_value, 'cash_id':cash_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#KoursCard").modal("hide");document.getElementById("KoursCardBody").innerHTML="";
						showKoursList();
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}

function loadStateSelectList(){
    let country_id=$("#country_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientStateSelectList', 'country_id':country_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("state_id").innerHTML=result["content"];
		$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#KoursCard")});
	}}, true);
}

function loadRegionSelectList(){
    let state_id=$("#state_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientRegionSelectList', 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("region_id").innerHTML=result["content"];
		$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#KoursCard")});
	}}, true);
}

function loadCitySelectList(){
    let region_id=$("#region_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientCitySelectList', 'region_id':region_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("city_id").innerHTML=result["content"];
		$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#KoursCard")});
	}}, true);
}

function loadKoursStorage(kours_id){
	if (kours_id<=0 || kours_id===""){toastr["error"](errs[0]);}
	if (kours_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadKoursStorage', 'kours_id':kours_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("storage_place").innerHTML=result["content"];
			$('#kours_tabs').tab();
		}}, true);
	}
}

function showKoursStorageForm(kours_id, s_id){
	if (kours_id<=0 || kours_id==""){toastr["error"](errs[0]);}
	if (kours_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showKoursStorageForm', 'kours_id':kours_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function dropKoursStorage(kours_id,s_id){
	swal({
		title: "Відкріпити склад від торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (kours_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropKoursStorage','kours_id':kours_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadKoursStorage(kours_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveKoursStorageForm(kours_id,s_id){
    let storage_name=$("#storage_id option:selected").html();
	swal({
		title: "Закріпити склад \""+storage_name+"\" за торговою точкою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let storage_id=$("#storage_id option:selected").val();
            let local=$("#local option:selected").val();
            let delivery_days=$("#delivery_days").val();
			if (kours_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveKoursStorageForm','kours_id':kours_id,'s_id':s_id,'storage_id':storage_id,'local':local,'delivery_days':delivery_days},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadKoursStorage(kours_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadKoursClients(kours_id){
	if (kours_id<=0 || kours_id===""){toastr["error"](errs[0]);}
	if (kours_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadKoursClients', 'kours_id':kours_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("clients_place").innerHTML=result["content"];
			$("#kours_tabs").tab();
		}}, true);
	}
}

function showKoursClientsForm(kours_id, s_id){
	if (kours_id<=0 || kours_id===""){toastr["error"](errs[0]);}
	if (kours_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showKoursClientsForm', 'kours_id':kours_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			var elem = document.querySelector('#vat_use'); if (elem){ new Switchery(elem, { color: '#1AB394' }); }
		}}, true);
	}
}

function showKoursClientList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showKoursClientList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal("show");
		document.getElementById("FormModalBody2").innerHTML=result["content"];
		document.getElementById("FormModalLabel2").innerHTML="Контрагенти";
		setTimeout(function() { $("#datatable_parrent").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}}); }, 500);
	}}, true);
}

function setKoursClient(id,name){
	$("#client_id").val(id);
	$("#client_name").val(name);
	$("#FormModalWindow2").modal("hide");
	$("#FormModalBody2").html("");
	$("#FormModalLabel2").html("");
}

function dropKoursClients(kours_id,s_id){
	swal({
		title: "Відкріпити контрагента від торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (kours_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropKoursClients','kours_id':kours_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadKoursStorage(kours_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveKoursClientsForm(kours_id,s_id){
    let client_name=$("#client_name").val();
	swal({
		title: "Закріпити контагента \""+client_name+"\" за торговою точкою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let client_id=$("#client_id").val();
            let vat_use=0;if (document.getElementById("vat_use").checked){vat_use=1;}
			if (kours_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveKoursClientsForm','kours_id':kours_id,'s_id':s_id,'client_id':client_id,'vat_use':vat_use},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadKoursClients(kours_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadKoursWorkers(kours_id){
	if (kours_id<=0 || kours_id===""){toastr["error"](errs[0]);}
	if (kours_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadKoursWorkers', 'kours_id':kours_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("workers_place").innerHTML=result["content"];
			$("#kours_tabs").tab();
		}}, true);
	}
}

function showKoursWorkersForm(kours_id, s_id){
	if (kours_id<=0 || kours_id===""){toastr["error"](errs[0]);}
	if (kours_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showKoursWorkersForm', 'kours_id':kours_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function dropKoursWorkers(kours_id,s_id){
	swal({
		title: "Відкріпити працівника від торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (kours_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropKoursWorkers', 'kours_id':kours_id, 's_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadKoursWorkers(kours_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveKoursWorkersForm(kours_id,s_id){
    let worker_name=$("#worker_id option:selected").html();
	swal({
		title: "Закріпити працівника \""+worker_name+"\" за торговою точкою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let worker_id=$("#worker_id option:selected").val();
			if (kours_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveKoursWorkersForm','kours_id':kours_id,'s_id':s_id,'worker_id':worker_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadKoursWorkers(kours_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}
