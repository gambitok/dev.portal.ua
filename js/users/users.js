var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

function changeDeliveryTime(){
	var value1 = $("#time_from_del").val();
	var value2 = $("#time_to_del").val();
	if(value1!==0 || value2!==0) 
    	$("#giveout_time").val('з '+value1+' по '+value2);
	else
		$("#giveout_time").val('');
}

function loadUsersList(){
	JsHttpRequest.query($rcapi,{ 'w': 'showUsersList'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#users_range").empty();
		$("#users_range").html(result["content"]);
	}}, true);
}

function newUsersCard(){
	JsHttpRequest.query($rcapi,{ 'w': 'newUsersCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		showUsersCard(result["users_id"]);
	}}, true);
}

var barcode_settings = {barWidth: 1,barHeight: 50,moduleSize: 5,showHRI: true,addQuietZone: true,marginHRI: 5,bgColor: "#FFFFFF",color: "#000000",fontSize: 14,output: "css",posX: 0,posY: 0};

function showUsersCard(users_id){
	if (users_id<=0 || users_id===""){toastr["error"](errs[0]);}
	if (users_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showUsersCard', 'users_id':users_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#UsersCard").modal('show');
			$("#UsersCardBody").html(result["content"]);
			$("#UsersCardLabel").html($("#users_name").val()+" (ID:"+$("#users_id").val()+")");
			$('#users_tabs').tab();
			$("#country_id").select2({placeholder: "Виберіть країну",dropdownParent: $("#UsersCard")});
		}}, true);
	}
}

function saveUsersGeneralInfo(){
	swal({
		title: "Зберегти зміни?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var users_id=$("#users_id").val();
			var name=$("#users_name").val();
			var post=$("#users_post").val();
			var tpoint_id=$("#users_tpoint_id option:selected").val();
			var role_id=$("#users_role_id option:selected").val();
			var phone2=$("#users_phone2").val();
			var login=$("#users_login").val();
			var pass=$("#users_pass").val();
			var email=$("#users_email").val();
			var status=$("#users_status option:selected").val();
			
			if (users_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveUsersGeneralInfo','users_id':users_id,'name':name,'post':post,'tpoint_id':tpoint_id,'role_id':role_id,'phone2':phone2,'login':login,'pass':pass,'status':status,'email':email},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						loadUsersList();
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}

function loadUsersAccess(users_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadUsersAccess', 'users_id':users_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("user_access_place").innerHTML=result["content"];
			$('#users_tabs').tab();
		}}, true);
	}
}

function loadUsersAccessCredit(users_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadUsersAccessCredit', 'users_id':users_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("user_access_credit").innerHTML=result["content"];
			$('#users_tabs').tab();
		}}, true);
	}
}

function loadUsersAccessTime(users_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadUsersAccessTime', 'users_id':users_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("user_access_time").innerHTML=result["content"];
			$('#users_tabs').tab();
			var elem = document.querySelector('#file_access');if (elem){ var dflt = new Switchery(elem, { color: '#1AB394' });}
			var elem2 = document.querySelector('#file_access_time');if (elem2){ var dflt = new Switchery(elem2, { color: '#1AB394' });}
		}}, true);
	}
}

function saveUsersAccessTime(users_id){
	swal({
		title: "Зберегти час доступу?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Зберегти", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var access=0; if (document.getElementById("file_access").checked) {access=1;}
			var access_time=0; if (document.getElementById("file_access_time").checked) {access_time=1;}
			var time_from=$('#access_time_from').val();
			var time_to=$('#access_time_to').val();
				JsHttpRequest.query($rcapi,{ 'w':'saveUsersAccessTime','users_id':users_id,'access':access,'access_time':access_time,'time_from':time_from,'time_to':time_to},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "", "success");
						loadUsersAccessTime(users_id);
					}
					else {swal("Помилка!", result["error"], "error");}
				}}, true);	
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveUsersAccessCredit(users_id){
	swal({
		title: "Зберегти кредит?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Зберегти", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var credit=$('#users_credit').val();
				JsHttpRequest.query($rcapi,{ 'w':'saveUsersAccessCredit','users_id':users_id,'credit':credit},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "", "success");
						loadUsersAccessCredit(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);	
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function showUsersAccessItemForm(users_id, mf_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showUsersAccessItemForm', 'users_id':users_id, 'mf_id':mf_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			var elem = document.querySelector('#file_access');if (elem){ var dflt = new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function clearUsersAcсess(users_id){
	swal({
		title: "Очистити права користувача?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Очистити", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (users_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'clearUsersAcсess','users_id':users_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено права!", "", "success");
						loadUsersAccess(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveUsersAccessItemForm(users_id,mf_id){
	swal({
		title: "Зберегти права доступу?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var lvl_id=$("#lvl_id option:selected").val();
			var file_access=0;if (document.getElementById("file_access").checked){file_access=1;}
			if (users_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveUsersAccessItemForm','users_id':users_id,'mf_id':mf_id,'lvl_id':lvl_id,'file_access':file_access},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadUsersAccess(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveUsersSupplStorageForm(users_id,s_id){
	var storage_name=$("#storage_id option:selected").html();
	swal({
		title: "Закріпити склад \""+storage_name+"\" за торговою точкою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var storage_id=$("#storage_id option:selected").val();
			var suppl_id=$("#suppl_id option:selected").val();
			if (users_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveUsersSupplStorageForm','users_id':users_id,'s_id':s_id,'storage_id':storage_id,'suppl_id':suppl_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadUsersSupplStorage(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadUsersClients(users_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadUsersClients', 'users_id':users_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("clients_place").innerHTML=result["content"];
			$('#users_tabs').tab();
		}}, true);
	}
}

function showUsersClientsForm(users_id, s_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showUsersClientsForm', 'users_id':users_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			var elem = document.querySelector('#in_use');if (elem){ var in_use = new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function showUsersClientList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showUsersClientList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
		document.getElementById("FormModalBody2").innerHTML=result["content"];
		document.getElementById("FormModalLabel2").innerHTML="Контрагенти";
		setTimeout(function() { $('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}}); }, 500);
	}}, true);
}

function setUsersClient(id,name){
	$('#client_id').val(id);
	$('#client_name').val(name);
	$("#FormModalWindow2").modal('hide');
	document.getElementById("FormModalBody2").innerHTML="";
	document.getElementById("FormModalLabel2").innerHTML="";
}

function dropUsersClients(users_id,s_id){
	swal({
		title: "Відкріпити контрагента від торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (users_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropUsersClients','users_id':users_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadUsersStorage(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveUsersClientsForm(users_id,s_id){
	var client_name=$("#client_name").val();
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

			if (users_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveUsersClientsForm','users_id':users_id,'s_id':s_id,'client_id':client_id,'sale_type':sale_type,'tax_credit':tax_credit,'tax_inform':tax_inform,'in_use':in_use},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadUsersClients(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadUsersWorkers(users_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadUsersWorkers', 'users_id':users_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("workers_place").innerHTML=result["content"];
			$('#users_tabs').tab();
		}}, true);
	}
}

function showUsersWorkersForm(users_id, s_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showUsersWorkersForm', 'users_id':users_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function dropUsersWorkers(users_id,s_id){
	swal({
		title: "Відкріпити працівника від торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (users_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropUsersWorkers','users_id':users_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadUsersWorkers(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveUsersWorkersForm(users_id,s_id){
	var worker_name=$("#worker_id option:selected").html();
	swal({
		title: "Закріпити працівника \""+worker_name+"\" за торговою точкою?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var worker_id=$("#worker_id option:selected").val();

			if (users_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveUsersWorkersForm','users_id':users_id,'s_id':s_id,'worker_id':worker_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadUsersWorkers(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadUsersDeliveryTime(users_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadUsersDeliveryTime', 'users_id':users_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("delivery_place").innerHTML=result["content"];
			$('#users_tabs').tab();
				$('#datatable_delivery').DataTable( {
					searching: true,
					"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}
				} );
		}}, true);
	}
}

function showUsersDeliveryForm(users_id, s_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showUsersDeliveryForm', 'users_id':users_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function saveUsersDeliveryForm(users_id,s_id){
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
			var time_from_del=$("#time_from_del").val();
			var time_to_del=$("#time_to_del").val();
			var delivery_days=$("#delivery_days").val();
			var giveout_time=$("#giveout_time").val();

			if (storage_id.length>0 && week_day.length>0 && time_from.length>0 && time_to.length>0 && delivery_days.length>0 && giveout_time.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveUsersDeliveryForm','users_id':users_id,'s_id':s_id,'storage_id':storage_id,'week_day':week_day,'time_from':time_from,'time_to':time_to,'delivery_days':delivery_days,'giveout_time':giveout_time,'time_from_del':time_from_del,'time_to_del':time_to_del},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadUsersDeliveryTime(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}else{ swal("Помилка!", "Не заповніне всі поля", "error");}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function dropUsersDelivery(users_id,s_id){
	swal({
		title: "Видалити умову доставки для складу?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (users_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropUsersDelivery','users_id':users_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadUsersDeliveryTime(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadUsersSupplDeliveryTime(users_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadUsersSupplDeliveryTime', 'users_id':users_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("suppl_delivery_range").innerHTML=result["content"];
			$('#users_tabs').tab();
			$('#users_str_tabs').tab();
			$('#datatable_suppl_delivery').DataTable( {searching: true,"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}} );
		}}, true);
	}
}

function loadUsersSupplStorage(users_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadUsersSupplStorage', 'users_id':users_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("suppl_storage_range").innerHTML=result["content"];
			$('#users_tabs').tab(); $('#users_storage_tabs').tab();
			$('#datatable_suppl_storage').DataTable( {searching: true,"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}} );
		}}, true);
	}
}

function loadUsersSupplStorageSelectList(){
	var suppl_id=$("#suppl_id option:selected").val(); 
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadUsersSupplStorageSelectList', 'suppl_id':suppl_id}, 
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

function showUsersSupplDeliveryForm(users_id, s_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showUsersSupplDeliveryForm', 'users_id':users_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function saveUsersSupplDeliveryForm(users_id,s_id){
	var suppl_name=$("#suppl_id option:selected").html();
	var storage_name=$("#suppl_storage_id option:selected").html();
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
				JsHttpRequest.query($rcapi,{ 'w':'saveUsersSupplDeliveryForm','users_id':users_id,'s_id':s_id,'suppl_id':suppl_id,'suppl_storage_id':suppl_storage_id,'week_day':week_day,'time_from':time_from,'time_to':time_to,'delivery_days':delivery_days,'giveout_time':giveout_time,'time_from_del':time_from_del,'time_to_del':time_to_del},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadUsersSupplDeliveryTime(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}else{ swal("Помилка!", "Не заповніне всі поля", "error");}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function dropUsersSupplDelivery(users_id,s_id){
	swal({
		title: "Видалити умову доставки для складу?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (users_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropUsersSupplDelivery','users_id':users_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Відкріплено!", "", "success");
						loadUsersDeliveryTime(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadUsersSupplFm(users_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadUsersSupplFm', 'users_id':users_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("fm_place").innerHTML=result["content"];
			$('#users_tabs').tab();
			$('#datatable_suppl_fm').DataTable( {
					searching: true,
					"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}
				} );
		}}, true);
	}
}

function showUsersSupplFmForm(users_id, s_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showUsersSupplFmForm', 'users_id':users_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function saveUsersSupplFmForm(users_id,s_id){
	var suppl_name=$("#suppl_id option:selected").html();
	var storage_name=$("#suppl_storage_id option:selected").html();
	var price_rating_id=$("#price_rating_id option:selected").html();
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
				JsHttpRequest.query($rcapi,{ 'w':'saveUsersSupplFmForm','users_id':users_id,'s_id':s_id,'suppl_id':suppl_id,'suppl_storage_id':suppl_storage_id,'price_rating_id':price_rating_id,'price_from':price_from,'price_to':price_to,'margin':margin,'delivery':delivery,'margin2':margin2},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadUsersSupplFm(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}else{ swal("Помилка!", "Не заповніне всі поля", "error");}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadUsersPayBox(users_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadUsersPayBox', 'users_id':users_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("pay_box_place").innerHTML=result["content"];
			$('#users_tabs').tab();
		}}, true);
	}
}
function showUsersPayBoxForm(users_id, s_id){
	if (users_id<=0 || users_id==""){toastr["error"](errs[0]);}
	if (users_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showUsersPayBoxForm', 'users_id':users_id, 's_id':s_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			var elem = document.querySelector('#in_use');if (elem){ var in_use = new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function dropUsersPayBox(users_id,s_id){
	swal({
		title: "Видалити касу від торгової точки?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (users_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropUsersPayBox','users_id':users_id,'s_id':s_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadUsersPayBox(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveUsersPayBoxForm(users_id,s_id){
	var client_name=$("#client_name").val();
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
			if (users_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveUsersPayBoxForm','users_id':users_id,'s_id':s_id,'client_id':client_id,'name':name,'in_use':in_use},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadUsersPayBox(users_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadTrustedIPList(){ 
	JsHttpRequest.query($rcapi,{ 'w': 'showTrustedIPList'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#users_range").empty();
		$("#users_range").html(result["content"]);
	}}, true);
}

function newTrustedIPCard(){
	JsHttpRequest.query($rcapi,{ 'w': 'newTrustedIPCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		var trusted_id=result["trusted_id"];
		showTrustedIPCard(trusted_id);
	}}, true);
}

function showTrustedIPCard(trusted_id){
	if (trusted_id<=0 || trusted_id==""){toastr["error"](errs[0]);}
	if (trusted_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showTrustedIPCard', 'trusted_id':trusted_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#TrustedIPCard").modal('show');
			document.getElementById("TrustedIPCardBody").innerHTML=result["content"];
			document.getElementById("TrustedIPCardLabel").innerHTML=$("#trusted_ip").val()+" (ID:"+$("#trusted_id").val()+")";
			$('#trusted_tabs').tab();
		}}, true);
	}
}

function saveTrustedIPGeneralInfo() {
	var trusted_id = $("#trusted_id").val();
	var trusted_ip = $("#trusted_ip").val();
	var trusted_descr = $("#trusted_descr").val();
	swal({
		title: "Зберегти зміни?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (trusted_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveTrustedIPGeneralInfo','trusted_id':trusted_id,'trusted_ip':trusted_ip,'trusted_descr':trusted_descr},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						loadTrustedIPList();
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}

function dropTrustedIP(trusted_id) { console.log(trusted_id);
	swal({
		title: "Видалити IP?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (trusted_id.length>0){ 
				JsHttpRequest.query($rcapi,{ 'w':'dropTrustedIP','trusted_id':trusted_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadTrustedIPList();
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

