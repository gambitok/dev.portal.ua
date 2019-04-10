var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

function changeDeliveryTime(){
	let time_from_ = $("#time_from_del").val();
	let time_to = $("#time_to_del").val();
	if(time_from_!==0 || time_to!==0)
    $("#giveout_time").val('з '+time_from_+' по '+time_to);
		else $("#giveout_time").val('');
}

function loadTpointList(){
	JsHttpRequest.query($rcapi,{ 'w': 'showTpointList'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		let tpoint_range=$("#tpoint_range");
        tpoint_range.empty();
        tpoint_range.html(result.content);
	}}, true);
}

function newTpointCard(){
	JsHttpRequest.query($rcapi,{ 'w': 'newTpointCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		let tpoint_id=result["tpoint_id"];
		showTpointCard(tpoint_id);
	}}, true);
}

var barcode_settings = {barWidth: 1,barHeight: 50,moduleSize: 5,showHRI: true,addQuietZone: true,marginHRI: 5,bgColor: "#FFFFFF",color: "#000000",fontSize: 14,output: "css",posX: 0,posY: 0};

function showTpointCard(tpoint_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showTpointCard', 'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let tpoint_card=$("#TpointCard");
            tpoint_card.modal("show");
            $("#TpointCardBody").html(result.content);
            $("#TpointCardLabel").html($("#tpoint_name").val()+" (ID:"+$("#tpoint_id").val()+")");
			$("#tpoint_tabs").tab();
			$("#country_id").select2({placeholder: "Виберіть країну",dropdownParent: tpoint_card});
			$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: tpoint_card});
			$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: tpoint_card});
			$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: tpoint_card});
		}}, true);
	}
}

function saveTpointGeneralInfo(){
	swal({
		title: "Зберегти зміни у розділі \"Загальна інформація\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var tpoint_id=$("#tpoint_id").val();
			var name=$("#tpoint_name").val();
			var full_name=$("#tpoint_full_name").val();
			var address=$("#address").val();
			var chief=$("#chief option:selected").val();
			var country_id=$("#country_id option:selected").val();
			var state_id=$("#state_id option:selected").val();
			var region_id=$("#region_id option:selected").val();
			var city_id=$("#city_id option:selected").val(); 
		
			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveTpointGeneralInfo','tpoint_id':tpoint_id,'name':name,'full_name':full_name,'address':address,'chief':chief,'country_id':country_id,'state_id':state_id,'region_id':region_id,'city_id':city_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#TpointCard").modal('hide');
						loadTpointList();
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}

function deleteTpoint() {
	swal({
		title: "Видалити торгову точку?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var tpoint_id=$("#tpoint_id").val();
			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'deleteTpoint','tpoint_id':tpoint_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadTpointList();
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
	let country_id=$("#country_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientStateSelectList', 'country_id':country_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("state_id").innerHTML=result["content"];
		$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#TpointCard")});
	}}, true);
}

function loadRegionSelectList(){
	let state_id=$("#state_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientRegionSelectList', 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("region_id").innerHTML=result["content"];
		$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#TpointCard")});
	}}, true);
}

function loadCitySelectList(){
	let region_id=$("#region_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientCitySelectList', 'region_id':region_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("city_id").innerHTML=result["content"];
		$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#TpointCard")});
	}}, true);
}

function loadTpointStorage(tpoint_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadTpointStorage', 'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#storage_place").html(result.content);
			$('#tpoint_tabs').tab();
			$('#datatable_storage').DataTable( {searching: true,"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}} );
		}}, true);
	}
}

function showTpointStorageForm(tpoint_id, s_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showTpointStorageForm', 'tpoint_id':tpoint_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			var elem = document.querySelector('#default');if (elem){ var dflt = new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function showTpointSupplStorageForm(tpoint_id, s_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showTpointSupplStorageForm', 'tpoint_id':tpoint_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function dropTpointSupplStorageForm(tpoint_id,s_id){
	swal({
		title: "Видалити склад із торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropTpointSupplStorageForm','tpoint_id':tpoint_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadTpointSupplStorage(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function dropTpointStorage(tpoint_id,s_id){
	swal({
		title: "Відкріпити склад від торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropTpointStorage','tpoint_id':tpoint_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadTpointStorage(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveTpointStorageForm(tpoint_id,s_id){
	let storage_name=$("#storage_id option:selected").html();
	swal({
		title: "Закріпити склад \""+storage_name+"\" за торговою точкою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var storage_id=$("#storage_id option:selected").val();
			var local=$("#local option:selected").val();
			var dflt=0;if (document.getElementById("default").checked){dflt=1;}
			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveTpointStorageForm','tpoint_id':tpoint_id,'s_id':s_id,'storage_id':storage_id,'local':local,'default':dflt},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadTpointStorage(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveTpointSupplStorageForm(tpoint_id,s_id){
	let storage_name=$("#storage_id option:selected").html();
	swal({
		title: "Закріпити склад \""+storage_name+"\" за торговою точкою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var storage_id=$("#storage_id option:selected").val();
			var suppl_id=$("#suppl_id option:selected").val();
			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveTpointSupplStorageForm','tpoint_id':tpoint_id,'s_id':s_id,'storage_id':storage_id,'suppl_id':suppl_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadTpointSupplStorage(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadTpointClients(tpoint_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadTpointClients', 'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("clients_place").innerHTML=result["content"];
			$('#tpoint_tabs').tab();
		}}, true);
	}
}

function showTpointClientsForm(tpoint_id, s_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showTpointClientsForm', 'tpoint_id':tpoint_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			var elem = document.querySelector('#in_use');if (elem){ var in_use = new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function showTpointClientList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showTpointClientList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
		document.getElementById("FormModalBody2").innerHTML=result["content"];
		document.getElementById("FormModalLabel2").innerHTML="Контрагенти";
		setTimeout(function() { $('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}}); }, 500);
	}}, true);
}

function setTpointClient(id,name){
	$('#client_id').val(id);
	$('#client_name').val(name);
	$("#FormModalWindow2").modal('hide');
	document.getElementById("FormModalBody2").innerHTML="";
	document.getElementById("FormModalLabel2").innerHTML="";
}

function dropTpointClients(tpoint_id,s_id){
	swal({
		title: "Відкріпити контрагента від торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropTpointClients','tpoint_id':tpoint_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadTpointStorage(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveTpointClientsForm(tpoint_id,s_id){
	let client_name=$("#client_name").val();
	swal({
		title: "Закріпити контагента \""+client_name+"\" за торговою точкою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var client_id=$("#client_id").val();
			var sale_type=$("#sale_type option:selected").val();
			var tax_credit=$("#tax_credit").val();
			var tax_inform=$("#tax_inform").val();
			var in_use=0;if (document.getElementById("in_use").checked){in_use=1;}

			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveTpointClientsForm','tpoint_id':tpoint_id,'s_id':s_id,'client_id':client_id,'sale_type':sale_type,'tax_credit':tax_credit,'tax_inform':tax_inform,'in_use':in_use},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadTpointClients(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadTpointWorkers(tpoint_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadTpointWorkers', 'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("workers_place").innerHTML=result["content"];
			$('#tpoint_tabs').tab();
		}}, true);
	}
}

function showTpointWorkersForm(tpoint_id, s_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showTpointWorkersForm', 'tpoint_id':tpoint_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#FormModalBody").html(result.content);
		}}, true);
	}
}

function dropTpointWorkers(tpoint_id,s_id){
	swal({
		title: "Відкріпити працівника від торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropTpointWorkers','tpoint_id':tpoint_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadTpointWorkers(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveTpointWorkersForm(tpoint_id,s_id){
	let worker_name=$("#worker_id option:selected").html();
	swal({
		title: "Закріпити працівника \""+worker_name+"\" за торговою точкою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var worker_id=$("#worker_id option:selected").val();
			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveTpointWorkersForm','tpoint_id':tpoint_id,'s_id':s_id,'worker_id':worker_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadTpointWorkers(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadTpointDeliveryTime(tpoint_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadTpointDeliveryTime', 'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("delivery_place").innerHTML=result["content"];
			$('#tpoint_tabs').tab();
				$('#datatable_delivery').DataTable( {
					searching: true,
					"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}
				} );
		}}, true);
	}
}

function showTpointDeliveryForm(tpoint_id, s_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showTpointDeliveryForm', 'tpoint_id':tpoint_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function saveTpointDeliveryForm(tpoint_id,s_id){
	let storage_name=$("#storage_id option:selected").html();
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
			var time_from_del=$("#time_from_del").val();
			var time_to_del=$("#time_to_del").val();
			var delivery_days=$("#delivery_days").val();
			var giveout_time=$("#giveout_time").val();

			if (storage_id.length>0 && week_day.length>0 && time_from.length>0 && time_to.length>0 && delivery_days.length>0 && giveout_time.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveTpointDeliveryForm','tpoint_id':tpoint_id,'s_id':s_id,'storage_id':storage_id,'week_day':week_day,'time_from':time_from,'time_to':time_to,'delivery_days':delivery_days,'giveout_time':giveout_time,'time_from_del':time_from_del,'time_to_del':time_to_del},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadTpointDeliveryTime(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}else{ swal("Помилка!", "Не заповніне всі поля", "error");}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function dropTpointDelivery(tpoint_id,s_id){
	swal({
		title: "Видалити умову доставки для складу?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropTpointDelivery','tpoint_id':tpoint_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadTpointDeliveryTime(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadTpointSupplDeliveryTime(tpoint_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadTpointSupplDeliveryTime', 'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("suppl_delivery_range").innerHTML=result["content"];
			$('#tpoint_tabs').tab();
			$('#tpoint_str_tabs').tab();
			$('#datatable_suppl_delivery').DataTable( {searching: true,"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}} );
		}}, true);
	}
}

function loadTpointSupplStorage(tpoint_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadTpointSupplStorage', 'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("suppl_storage_range").innerHTML=result["content"];
			$('#tpoint_tabs').tab(); $('#tpoint_storage_tabs').tab();
			$('#datatable_suppl_storage').DataTable( {searching: true,"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}} );
		}}, true);
	}
}

function loadTpointSupplStorageSelectList(){
	var suppl_id=$("#suppl_id option:selected").val(); 
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadTpointSupplStorageSelectList', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("suppl_storage_id").innerHTML=result["content"];
		}}, true);
	}
}

function loadSupplStorageList(){
	var suppl_id=$("#suppl_id option:selected").val(); 
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadSupplStorageList', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("storage_id").innerHTML=result["content"];
		}}, true);
	}
}

function showTpointSupplDeliveryForm(tpoint_id, s_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showTpointSupplDeliveryForm', 'tpoint_id':tpoint_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function saveTpointSupplDeliveryForm(tpoint_id,s_id){
	let suppl_name=$("#suppl_id option:selected").html();
	let storage_name=$("#suppl_storage_id option:selected").html();
	swal({
		title: "Зберегти умову доставки для складу \""+storage_name+"\" постачальника "+suppl_name+"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var suppl_id=$("#suppl_id option:selected").val();
			var suppl_storage_id=$("#suppl_storage_id option:selected").val();
			var week_day=$("#week_day option:selected").val();
			var time_from=$("#time_from").val();
			var time_to=$("#time_to").val();
			var time_from_del=$("#time_from_del").val();
			var time_to_del=$("#time_to_del").val();
			var delivery_days=$("#delivery_days").val();
			var giveout_time=$("#giveout_time").val();

			if (suppl_id.length>0 && suppl_storage_id.length>0 && week_day.length>0 && time_from.length>0 && time_to.length>0 && delivery_days.length>0 && giveout_time.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveTpointSupplDeliveryForm','tpoint_id':tpoint_id,'s_id':s_id,'suppl_id':suppl_id,'suppl_storage_id':suppl_storage_id,'week_day':week_day,'time_from':time_from,'time_to':time_to,'delivery_days':delivery_days,'giveout_time':giveout_time,'time_from_del':time_from_del,'time_to_del':time_to_del},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadTpointSupplDeliveryTime(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}else{ swal("Помилка!", "Не заповніне всі поля", "error");}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function dropTpointSupplDelivery(tpoint_id,s_id){
	swal({
		title: "Видалити умову доставки для складу?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropTpointSupplDelivery','tpoint_id':tpoint_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadTpointDeliveryTime(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadTpointSupplFm(tpoint_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadTpointSupplFm', 'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("fm_place").innerHTML=result["content"];
			$('#tpoint_tabs').tab();
			$('#datatable_suppl_fm').DataTable( {
				searching: true,
				"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}
			} );
		}}, true);
	}
}

function showTpointSupplFmForm(tpoint_id, s_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showTpointSupplFmForm', 'tpoint_id':tpoint_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function saveTpointSupplFmForm(tpoint_id,s_id){
	let suppl_name=$("#suppl_id option:selected").html();
	let price_rating_id=$("#price_rating_id option:selected").html();
	swal({
		title: "Зберегти ціноутворення для рейтингу \""+price_rating_id+"\" постачальника "+suppl_name+"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var suppl_id=$("#suppl_id option:selected").val();
			var suppl_storage_id=$("#suppl_storage_id option:selected").val();
			var price_rating_id=$("#price_rating_id option:selected").val();
			var price_from=$("#price_from").val();
			var price_to=$("#price_to").val();
			var margin=$("#margin").val();
			var delivery=$("#delivery").val();
			var margin2=$("#margin2").val();

			if (suppl_id.length>0 && suppl_storage_id.length>0 && price_rating_id.length>0 && price_from.length>0 && price_to.length>0 && margin.length>0 && delivery.length>0 && margin2.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveTpointSupplFmForm','tpoint_id':tpoint_id,'s_id':s_id,'suppl_id':suppl_id,'suppl_storage_id':suppl_storage_id,'price_rating_id':price_rating_id,'price_from':price_from,'price_to':price_to,'margin':margin,'delivery':delivery,'margin2':margin2},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadTpointSupplFm(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}else{ swal("Помилка!", "Не заповніне всі поля", "error");}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function dropTpointSupplFm(tpoint_id,s_id){
    swal({
            title: "Видалити умову доставки для складу?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (tpoint_id.length>0){
                    JsHttpRequest.query($rcapi,{ 'w':'dropTpointSupplDelivery','tpoint_id':tpoint_id,'s_id':s_id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Відкріплено!", "", "success");
                                loadTpointDeliveryTime(tpoint_id);
                            }
                            else{ swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function loadTpointPayBox(tpoint_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadTpointPayBox', 'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("pay_box_place").innerHTML=result["content"];
			$('#tpoint_tabs').tab();
		}}, true);
	}
}

function showTpointPayBoxForm(tpoint_id, s_id){
	if (tpoint_id<=0 || tpoint_id===""){toastr["error"](errs[0]);}
	if (tpoint_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showTpointPayBoxForm', 'tpoint_id':tpoint_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			var elem = document.querySelector('#in_use');if (elem){ var in_use = new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function dropTpointPayBox(tpoint_id,s_id){
	swal({
		title: "Видалити касу від торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropTpointPayBox','tpoint_id':tpoint_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadTpointPayBox(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveTpointPayBoxForm(tpoint_id,s_id){
	let client_name=$("#client_name").val();
	swal({
		title: "Закріпити контагента \""+client_name+"\" за торговою точкою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var client_id=$("#client_id").val();
			var name=$("#name").val();
			var in_use=0;if (document.getElementById("in_use").checked){in_use=1;}

			if (tpoint_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveTpointPayBoxForm','tpoint_id':tpoint_id,'s_id':s_id,'client_id':client_id,'name':name,'in_use':in_use},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadTpointPayBox(tpoint_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

