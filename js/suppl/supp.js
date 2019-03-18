var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";


$(document).ready(function() {
});

function loadSupplList(){
	JsHttpRequest.query($rcapi,{ 'w': 'showSupplList'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#suppl_range").empty();
		$("#suppl_range").html(result["content"]);
	}}, true);
}

var barcode_settings = {barWidth: 1,barHeight: 50,moduleSize: 5,showHRI: true,addQuietZone: true,marginHRI: 5,bgColor: "#FFFFFF",color: "#000000",fontSize: 14,output: "css",posX: 0,posY: 0};

function showSupplCard(suppl_id){
	if (suppl_id<=0 || suppl_id==""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showSupplCard', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#SupplCard").modal('show');
			document.getElementById("SupplCardBody").innerHTML=result["content"];
			document.getElementById("SupplCardLabel").innerHTML=$("#suppl_name").val()+" (ID:"+$("#suppl_id").val()+")";
			$('#suppl_tabs').tab();
			$("#country_id").select2({placeholder: "Виберіть країну",dropdownParent: $("#SupplCard")});
			$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#SupplCard")});
			$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#SupplCard")});
			$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#SupplCard")});
		}}, true);
	}
}
function saveSupplGeneralInfo(){
	swal({
		title: "Зберегти зміни у розділі \"Загальна інформація\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var suppl_id=$("#suppl_id").val();
			var name=$("#suppl_name").val();
			var full_name=$("#suppl_full_name").val();
			var address=$("#address").val();
			var chief=$("#chief option:selected").val();
			var country_id=$("#country_id option:selected").val();
			var state_id=$("#state_id option:selected").val();
			var region_id=$("#region_id option:selected").val();
			var city_id=$("#city_id option:selected").val(); 
		
			if (suppl_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveSupplGeneralInfo','suppl_id':suppl_id,'name':name,'full_name':full_name,'address':address,'chief':chief,'country_id':country_id,'state_id':state_id,'region_id':region_id,'city_id':city_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						loadSupplList();
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}
function loadStateSelectList(){
	var country_id=$("#country_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientStateSelectList', 'country_id':country_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("state_id").innerHTML=result["content"];
		$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#SupplCard")});
	}}, true);
}
function loadRegionSelectList(){
	var state_id=$("#state_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientRegionSelectList', 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("region_id").innerHTML=result["content"];
		$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#SupplCard")});
	}}, true);
}
function loadCitySelectList(){
	var region_id=$("#region_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientCitySelectList', 'region_id':region_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("city_id").innerHTML=result["content"];
		$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#SupplCard")});
	}}, true);
}

function loadSupplStorage(suppl_id){
	if (suppl_id<=0 || suppl_id==""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadSupplStorage', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("storage_place").innerHTML=result["content"];
			$('#suppl_tabs').tab();
		}}, true);
	}
}

function showSupplStorageForm(suppl_id, s_id){
	if (suppl_id<=0 || suppl_id==""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showSupplStorageForm', 'suppl_id':suppl_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}
function dropSupplStorage(suppl_id,s_id){
	swal({
		title: "Відкріпити склад від торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (suppl_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropSupplStorage','suppl_id':suppl_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadSupplStorage(suppl_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveSupplStorageForm(suppl_id,s_id){
	var storage_name=$("#storage_id option:selected").html();
	swal({
		title: "Закріпити склад \""+storage_name+"\" за торговою точкою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var storage_id=$("#storage_id option:selected").val();
			var local=$("#local option:selected").val();

			if (suppl_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveSupplStorageForm','suppl_id':suppl_id,'s_id':s_id,'storage_id':storage_id,'local':local},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadSupplStorage(suppl_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}


function loadSupplClients(suppl_id){
	if (suppl_id<=0 || suppl_id==""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadSupplClients', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("clients_place").innerHTML=result["content"];
			$('#suppl_tabs').tab();
		}}, true);
	}
}
function showSupplClientsForm(suppl_id, s_id){
	if (suppl_id<=0 || suppl_id==""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showSupplClientsForm', 'suppl_id':suppl_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			var elem = document.querySelector('#vat_use');
			if (elem){
	            var vat_use = new Switchery(elem, { color: '#1AB394' });
			}
		}}, true);
	}
}
function showSupplClientList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showSupplClientList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
		document.getElementById("FormModalBody2").innerHTML=result["content"];
		document.getElementById("FormModalLabel2").innerHTML="Контрагенти";
		setTimeout(function() { $('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}}); }, 500);
	}}, true);
}
function setSupplClient(id,name){
	$('#client_id').val(id);
	$('#client_name').val(name);
	$("#FormModalWindow2").modal('hide');
	document.getElementById("FormModalBody2").innerHTML="";
	document.getElementById("FormModalLabel2").innerHTML="";
}

function dropSupplClients(suppl_id,s_id){
	swal({
		title: "Відкріпити контрагента від торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (suppl_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropSupplClients','suppl_id':suppl_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadSupplStorage(suppl_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveSupplClientsForm(suppl_id,s_id){
	var client_name=$("#client_name").val();
	swal({
		title: "Закріпити контагента \""+client_name+"\" за торговою точкою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var client_id=$("#client_id").val();
			var vat_use=0;if (document.getElementById("vat_use").checked){vat_use=1;}

			if (suppl_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveSupplClientsForm','suppl_id':suppl_id,'s_id':s_id,'client_id':client_id,'vat_use':vat_use},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadSupplClients(suppl_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}


function loadSupplWorkers(suppl_id){
	if (suppl_id<=0 || suppl_id==""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadSupplWorkers', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("workers_place").innerHTML=result["content"];
			$('#suppl_tabs').tab();
		}}, true);
	}
}

function showSupplWorkersForm(suppl_id, s_id){
	if (suppl_id<=0 || suppl_id==""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showSupplWorkersForm', 'suppl_id':suppl_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}
function dropSupplWorkers(suppl_id,s_id){
	swal({
		title: "Відкріпити працівника від торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (suppl_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropSupplWorkers','suppl_id':suppl_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadSupplWorkers(suppl_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveSupplWorkersForm(suppl_id,s_id){
	var worker_name=$("#worker_id option:selected").html();
	swal({
		title: "Закріпити працівника \""+worker_name+"\" за торговою точкою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var worker_id=$("#worker_id option:selected").val();

			if (suppl_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveSupplWorkersForm','suppl_id':suppl_id,'s_id':s_id,'worker_id':worker_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadSupplWorkers(suppl_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}



function loadSupplDeliveryTime(suppl_id){
	if (suppl_id<=0 || suppl_id==""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadSupplDeliveryTime', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("delivery_place").innerHTML=result["content"];
			$('#suppl_tabs').tab();
		}}, true);
	}
}

function showSupplDeliveryForm(suppl_id, s_id){
	if (suppl_id<=0 || suppl_id==""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showSupplDeliveryForm', 'suppl_id':suppl_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function saveSupplDeliveryForm(suppl_id,s_id){
	var storage_name=$("#storage_id option:selected").html();
	swal({
		title: "Зберегти умову доставки для складу \""+storage_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var storage_id=$("#storage_id option:selected").val();
			var week_day=$("#week_day option:selected").val();
			var time_from=$("#time_from").val();
			var time_to=$("#time_to").val();
			var delivery_days=$("#delivery_days").val();
			var giveout_time=$("#giveout_time").val();

			if (storage_id.length>0 && week_day.length>0 && time_from.length>0 && time_to.length>0 && delivery_days.length>0 && giveout_time.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveSupplDeliveryForm','suppl_id':suppl_id,'s_id':s_id,'storage_id':storage_id,'week_day':week_day,'time_from':time_from,'time_to':time_to,'delivery_days':delivery_days,'giveout_time':giveout_time},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadSupplDeliveryTime(suppl_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}else{ swal("Помилка!", "Не заповніне всі поля", "error");}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}
function dropSupplDelivery(suppl_id,s_id){
	swal({
		title: "Видалити умову доставки для складу?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (suppl_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropSupplDelivery','suppl_id':suppl_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadSupplDeliveryTime(suppl_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}