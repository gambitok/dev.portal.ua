var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";


$(document).ready(function() {
	$('#flClientCategoryTree') .on('changed.jstree', function (e, data) {
		var i, j, r = [];
		for(i = 0, j = data.selected.length; i < j; i++) {
		  r.push(data.instance.get_node(data.selected[i]).text);
		}
		$('#flClientCategoryTree_result').val('' + r.join(', '));
		var i, j, r = [];
		for(i = 0, j = data.selected.length; i < j; i++) {
		  r.push(data.instance.get_node(data.selected[i]).id);
		}
		$('#flClientCategoryTree_id').val(''+r.join(', '));
		
	}).jstree({
		'core' : {'check_callback' : true},
		'plugins' : [ 'types', 'dnd',"sort" ],
		'types' : {'default' : {'icon' : 'fa fa-folder'},}
	});
});

function ShowCheckAll() {$('#checkAll').change(function () {$('input:checkbox').prop('checked', $(this).prop('checked'));});}

function filterClientsList(){
	var client_id=$("#filClientId").val();
	var client_name=$("#filClientName").val();
	var phone=$("#filPhone").val();
	var email=$("#filEmail").val();
	var state_id=$("#filState option:selected").val();
	
	$("#clients_range").empty();
	JsHttpRequest.query($rcapi,{ 'w': 'filterClientsList', 'client_id':client_id, 'client_name':client_name, 'phone':phone, 'email':email, 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#clients_range").html(result["content"]);
		toastr["info"]("Виконано!");
	}}, true);
}

function ClearClientSearch(){
	$("#filClientId").val(""); $("#filClientName").val("");
	$("#filPhone").val("");	$("#filEmail").val("");
	$("#filState option:selected").val(0);
	filterClientsList();
}


function newClientCard(){
	JsHttpRequest.query($rcapi,{ 'w': 'newClientCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		var client_id=result["client_id"];
		if(client_id==0) {
			checkEmptyClients(client_id);
		} else {
			showClientCard(client_id);
		}
//		filterClientsList();
	}}, true);
}

function checkEmptyClients(client_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'checkEmptyClients'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		if(!result.content) {
			showClientCard(client_id);
		} else {
			$("#ClientEmpty").modal('show');
			$("#ClientEmptyBody").html(result.content);
		}
	}}, true);
}



var barcode_settings = {barWidth: 1,barHeight: 50,moduleSize: 5,showHRI: true,addQuietZone: true,marginHRI: 5,bgColor: "#FFFFFF",color: "#000000",fontSize: 14,output: "css",posX: 0,posY: 0};

function showClientCard(client_id){
//	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
//	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showClientCard', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#ClientCard").modal('show');
			document.getElementById("ClientCardBody").innerHTML=result["content"];
			document.getElementById("ClientCardLabel").innerHTML=$("#client_name").val()+" (ID:"+$("#client_id").val()+")";
			$('#client_tabs').tab();
			$("#comment_info").markdown({autofocus:false,savable:false});
			$("#country_id").select2({placeholder: "Виберіть країну",dropdownParent: $("#ClientCard")});
			$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#ClientCard")});
			$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#ClientCard")});
			$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#ClientCard")})
			.on('select2:close', function() {var el = $(this);
				if(el.val()==="NEW") { var newval = prompt("Введіть нове значення: ");
				  if(newval !== null) { 
				  	var region_id=$("#region_id option:selected").val();
				  	JsHttpRequest.query($rcapi,{ 'w': 'addNewCity', 'region_id':region_id, 'name':newval}, 
					function (result, errors){ if (errors) {alert(errors);} if (result){  
					  	el.append('<option id="'+result["id"]+'">'+newval+'</option>').val(newval);
					}}, true);
				  }
				}
			  });
			$('.i-checks').iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
		}}, true);
//	}
}

function showClientRetailList(press_btn){
	
	var status=$('#input_done').val();
	if (press_btn) {
		if (status=="true") status=true; else status=false;
		if (status){
			$('#input_done').val('false');
			$('#toggle_done').html("<i class='fa fa-eye-slash'></i>");		
		}
		else  {
			$('#input_done').val('true');
			$('#toggle_done').html("<i class='fa fa-eye'></i>");
		}	
	} else {
		if (status=="true") status=false; else status=true;
	}
	

	var prevRange=$("#clients_range").html();
	JsHttpRequest.query($rcapi,{ 'w': 'showClientRetailList', 'status':status}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		if (prevRange.length != result["content"].length){
			$("#clients_range").empty();
			$("#clients_range").html(result["content"]);
		}
	}}, true);
}

function showClientRetailCard(user_id){
	if (user_id<=0 || user_id==""){toastr["error"](errs[0]);}
	if (user_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showClientRetailCard', 'user_id':user_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#ClientCard").modal('show');
			document.getElementById("ClientCardBody").innerHTML=result["content"];
			document.getElementById("ClientCardLabel").innerHTML=$("#user_name").val()+" (ID:"+$("#user_id").val()+")";
			$("#country_id").select2({placeholder: "Виберіть країну",dropdownParent: $("#ClientCard")});
			$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#ClientCard")});
			$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#ClientCard")});
			$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#ClientCard")})
		}}, true);
	}
}

function newClientRetailCard(){
	JsHttpRequest.query($rcapi,{ 'w': 'newClientRetailCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		var user_id=result["user_id"];
		showClientRetailCard(user_id);
	}}, true);
}

function saveClientRetailGeneralInfo(){
	var user_id=$("#user_id").val();
	var user_name=$("#user_name").val();
	var client_id=$("#client_name").val();
//	var user_city=$("#user_city").val();
		var country_id=$("#country_id option:selected").val();
		var state_id=$("#state_id option:selected").val();
		var region_id=$("#region_id option:selected").val();
		var city_id=$("#city_id option:selected").val();
	var user_category=$("#user_category").val();
	var user_phone=$("#user_phone").val();
	var user_email=$("#user_email").val();
	var user_pass=$("#user_pass").val();
	var user_status=$("#user_status").val();
	var user_data=$("#user_data").val();
	
	swal({
		title: "Зберегти дані користувача \""+user_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (user_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveClientRetailGeneralInfo','user_id':user_id, 'user_name':user_name, 'client_id':client_id, 'country_id':country_id, 'state_id':state_id, 'region_id':region_id, 'city_id':city_id, 'user_category':user_category, 'user_phone':user_phone, 'user_email':user_email, 'user_pass':user_pass, 'user_status':user_status, 'user_data':user_data},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#ClientCard").modal('hide');
						showClientRetailList();
					}
					else{ swal("Помилка!", result["error"], "error");}

				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function moveClientsRetail(user_id,client_id){
	swal({
		title: "Перемістити користувача №\""+user_id+"\" в контрагенти?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (user_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'moveClientsRetail','user_id':user_id, 'client_id':client_id },
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						$("#ClientCard").modal('hide');
						showClientRetailList();
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function setClientRetail(client_id,client_name) {
	var user_id=$("#user_id").val();
	var user_name=$("#user_name").val();
	
	swal({
		title: "Привязати користувача \""+user_name+"\" за контрагентом \""+client_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (user_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'setClientRetail','user_id':user_id, 'client_id':client_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						$("#FormModalWindow").modal('hide');
						$("#ClientCard").modal('hide');
						showClientRetailList();
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
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
		$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#ClientCard")});
	}}, true);
}
function loadRegionSelectList(){
	var state_id=$("#state_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientRegionSelectList', 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("region_id").innerHTML=result["content"];
		$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#ClientCard")});
	}}, true);
}
function loadCitySelectList(){
	var region_id=$("#region_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientCitySelectList', 'region_id':region_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("city_id").innerHTML=result["content"];
		$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#ClientCard")})
		.on('select2:close', function() {var el = $(this);
			if(el.val()==="NEW") { var newval = prompt("Введіть нове місто: ");
			  if(newval !== null) { 
				var region_id=$("#region_id option:selected").val();
				JsHttpRequest.query($rcapi,{ 'w': 'addNewCity', 'region_id':region_id, 'name':newval}, 
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					el.append('<option id="'+result["id"]+'">'+newval+'</option>').val(newval);
				}}, true);
			  }
			}
		  });
	}}, true);
}



function loadClientConditions(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientConditions', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("conditions_place").innerHTML=result["content"];
			$('#client_tabs').tab();
			$("#cash_id").select2({placeholder: "Основна валюта",dropdownParent: $("#ClientCard")});
			$("#country_cash_id").select2({placeholder: "Національна валюта",dropdownParent: $("#ClientCard")});
			$("#price_lvl").select2({placeholder: "Прайс",dropdownParent: $("#ClientCard")});
			$("#price_suppl_lvl").select2({placeholder: "Прайс",dropdownParent: $("#ClientCard")});
			$("#credit_cash_id").select2({placeholder: "Валюта кредиту",dropdownParent: $("#ClientCard")});
			$("#tpoint_id").select2({placeholder: "Торгова точка",dropdownParent: $("#ClientCard")});
			$('.i-checks').iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
		}}, true);
	}
}


function loadClientDetails(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientDetails', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("details_place").innerHTML=result["content"];
			$('#catalogue_tabs').tab();
//			$("#units").select2({placeholder: "Одиниці виміру",dropdownParent: $("#ClientCard")});
		}}, true);
	}
}

function norResidentAcive(){
	if (document.getElementById("not_resident").checked){
		document.getElementById("nr_details").disabled="";
	}else{document.getElementById("nr_details").disabled="disabled";}
}


function preconfirmClientDetails(){
	swal({
		title: "Зберегти зміни у розділі \"Реквізити\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			saveClientDetails();
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}
function saveClientDetails(){
	var client_id=$("#client_id").val();
	var address_jur=$("#address_jur").val();
	var address_fakt=$("#address_fakt").val();
	var edrpou=$("#edrpou").val();
	var svidotctvo=$("#svidotctvo").val();
	var vytjag=$("#vytjag").val();
	var vat=$("#vat").val();
	var mfo=$("#mfo").val();
	var bank=$("#bank").val();
	var account=$("#account").val();
	var nr_details=$("#nr_details").val();
	var not_resident=0; if (document.getElementById("not_resident").checked){not_resident=1;}else{nr_details="";}
	var buh_name=$("#buh_name").val();
	var buh_edrpou=$("#buh_edrpou").val();

	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveClientDetails','client_id':client_id,'address_jur':address_jur,'address_fakt':address_fakt,'edrpou':edrpou,'svidotctvo':svidotctvo,'vytjag':vytjag,'vat':vat,'mfo':mfo, 'bank':bank,'account':account,'not_resident':not_resident,'nr_details':nr_details,'buh_name':buh_name,'buh_edrpou':buh_edrpou},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
				filterClientsList();
			}
			else{ swal("Помилка!", result["error"], "error");}
			
		}}, true);
	}
}



function loadClientDocumentPrefix(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientDocumentPrefix', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("document_prefix_place").innerHTML=result["content"];
			$('#catalogue_tabs').tab();
		}}, true);
	}
}
function showClientDocumentPrefixForm(client_id, prefix_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showClientDocumentPrefixForm', 'client_id':client_id, 'prefix_id':prefix_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}
function dropClientDocumentPrefix(client_id,prefix_id,prefix_name){
	swal({
		title: "Видалити префікс документа\""+prefix_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (client_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropClientDocumentPrefix','client_id':client_id,'prefix_id':prefix_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadClientDocumentPrefix(client_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveClientDocumentPrefixForm(client_id,prefix_id){
	var prefix=$("#prefix").val();
	swal({
		title: "Зберегти префікс документа Користувача \""+prefix+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var prefix=$("#prefix").val();
			var doc_type_id=$("#doc_type_id option:selected").val();

			if (client_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveClientDocumentPrefixForm','client_id':client_id,'prefix_id':prefix_id,'doc_type_id':doc_type_id,'prefix':prefix},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadClientDocumentPrefix(client_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}


function loadClientContacts(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientContacts', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("contacts_place").innerHTML=result["content"];
			$('#catalogue_tabs').tab();
		}}, true);
	}
}

function showClientContactForm(client_id, contact_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showClientContactForm', 'client_id':client_id, 'contact_id':contact_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}


function dropClientContact(client_id,contact_id,contact_name){
	swal({
		title: "Видалити контакт \""+contact_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (client_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropClientContact','client_id':client_id,'contact_id':contact_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadClientContacts(client_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveClientContactForm(client_id,contact_id){
	var contact_name=$("#contact_name").val();
	swal({
		title: "Зберегти зміни контакту \""+contact_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var contact_name=$("#contact_name").val();
			var contact_post=$("#contact_post").val();
			var cn=$("#contact_con_kol").val();

			var con_id = []; var sotc_cont = []; var contact_value = []; 
			for (var i=1;i<=cn;i++){
				con_id[i]=$("#con_id_"+i).val();
				sotc_cont[i]=$("#sotc_cont_"+i+" option:selected").val();
				contact_value[i]=$("#contact_value_"+i).val();
			}
			if (client_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveClientContactForm','client_id':client_id,'contact_id':contact_id,'contact_name':contact_name,'contact_post':contact_post,'contact_con_kol':cn,'con_id':con_id,'sotc_cont':sotc_cont,'contact_value':contact_value},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadClientContacts(client_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}



function preconfirmClientGeneralInfo(){
	swal({
		title: "Зберегти зміни у розділі \"Загальна інформація\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			saveClientGeneralInfo();
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveClientGeneralInfo(){
	
	var client_id=$("#client_id").val();
	var org_type=$("#org_type option:selected").val();
	var client_name=$("#client_name").val();
	var client_full_name=$("#client_full_name").val();
	var phone=$("#phone").val();
	var email=$("#email").val();
	var parrent_id=$("#parrent_id").val();
	var country_id=$("#country_id option:selected").val();
	var state_id=$("#state_id option:selected").val();
	var region_id=$("#region_id option:selected").val();
	var city_id=$("#city_id option:selected").val(); 
	var c_category_kol=$("#c_category_kol").val();
	var user_category=$("#user_category").val();
	
	var c_category=[]; var cc="";
	for (var i=1;i<=c_category_kol;i++){var cc=0;if(document.getElementById("c_category_"+i).checked) { cc=$("#c_category_"+i).val(); }c_category[i]=cc;}

	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveClientGeneralInfo','client_id':client_id,'org_type':org_type,'name':client_name,'full_name':client_full_name,'phone':phone,'email':email, 'parrent_id':parrent_id, 'country_id':country_id, 'state_id':state_id, 'region_id':region_id, 'city_id':city_id, 'c_category_kol':c_category_kol, 'c_category':c_category,'user_category':user_category},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
				$("#ClientCard").modal('hide');
			
					$('#ClientCard').on('hidden.bs.modal', function () {
						// do something…
						var client_input = $("#client_id").val();
						if (client_input=="0") {
							showClientCard(result["client_id"]);
							console.log(result["client_id"]);
						}
					})
				

				//var art=$("#catalogue_art").val();
				//if (art.length>0){
				//	catalogue_client_search();
				//}
			}
			else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}



function preconfirmClientConditions(client_id){
	swal({
		title: "Зберегти зміни у розділі \"Умови\"?",text: "Внесені Вами зміни вплинуть на роботу Клієнта",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			saveClientConditions(client_id);
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveClientConditions(client_id){
	var cash_id=$("#cash_id option:selected").val();
	var country_cash_id=$("#country_cash_id option:selected").val();
	var price_lvl=$("#price_lvl option:selected").val();
	var margin_price_lvl=$("#margin_price_lvl").val();
	var price_suppl_lvl=$("#price_suppl_lvl option:selected").val();
	var margin_price_suppl_lvl=$("#margin_price_suppl_lvl").val();
	var tpoint_id=$("#tpoint_id option:selected").val();
	var payment_delay=$("#payment_delay").val();
	var credit_limit=$("#credit_limit").val();
	var credit_cash_id=$("#credit_cash_id option:selected").val();
	var credit_return=$("#credit_return").val();
	var client_vat=0; if (document.getElementById("client_vat").checked){client_vat=1;}
	var doc_type_id=$("#doc_type_id option:selected").val();

	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveClientConditions','client_id':client_id,'cash_id':cash_id,'country_cash_id':country_cash_id,'price_lvl':price_lvl,'margin_price_lvl':margin_price_lvl,'price_suppl_lvl':price_suppl_lvl,'margin_price_suppl_lvl':margin_price_suppl_lvl,'tpoint_id':tpoint_id,'client_vat':client_vat,'payment_delay':payment_delay,'credit_limit':credit_limit,'credit_cash_id':credit_cash_id,'credit_return':credit_return,'doc_type_id':doc_type_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			}
			else{ swal("Помилка!", result["error"], "error");}
			
		}}, true);
	}
}


function showClientsParrentTree(client_id,parrent_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showClientsParrentTree', 'client_id':client_id, 'parrent_id':parrent_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML="Контрагенти";
			$('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
		}}, true);
	}
}

function setClientParrent(id,name){
	$('#parrent_id').val(id);
	$('#parrent_name').val(name);
	$("#FormModalWindow").modal('hide');
	document.getElementById("FormModalBody").innerHTML="";
	document.getElementById("FormModalLabel").innerHTML="";
}

function unlinkClientsParrent(client_id){
	swal({
		title: "Відвязати контагента від \""+$('#parrent_name').val()+"\"?",text: "Внесені Вами зміни вплинуть на роботу Контагента",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'unlinkClientsParrent', 'client_id':client_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					$('#parrent_id').val("0");
					$('#parrent_name').val("");
					swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
				}
				else{ toastr["error"](result["error"]); }
			}}, true);	
		} else {
			swal("Відмінено", "Операцію анульовано.", "error");
		}
	});
}
function unlinkClientsSubclient(client_id,subclient_id){
	swal({
		title: "Відвязати контагента від \""+$('#client_full_name').val()+"\"?",text: "Внесені Вами зміни вплинуть на роботу Контагента",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'unlinkClientsSubclient', 'client_id':client_id, 'subclient_id':subclient_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
					loadClientSubclients(client_id);
				}
				else{ toastr["error"](result["error"]); }
			}}, true);	
		} else {
			swal("Відмінено", "Операцію анульовано.", "error");
		}
	});
}


function loadClientSubclients(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientSubclients', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#clients_shild_range").html(result["content"]);
		}}, true);
	}
}



function loadClientUsers(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientUsers', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("users_place").innerHTML=result["content"];
		}}, true);
	}
}

function showClientUserForm(client_id, user_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showClientUserForm', 'client_id':client_id, 'user_id':user_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			$('.i-checks').iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
		}}, true);
	}
}

function randString(id){
  var dataSet = $(id).attr('data-character-set').split(',');  
  var possible = '';  var text = '';
  if($.inArray('a-z', dataSet) >= 0){possible += 'abcdefghijklmnopqrstuvwxyz';}
  if($.inArray('A-Z', dataSet) >= 0){possible += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';}
  if($.inArray('0-9', dataSet) >= 0){possible += '0123456789';}
  if($.inArray('#', dataSet) >= 0){possible += '![]{}()%&*$#^<>~@|';}
  for(var i=0; i < $(id).attr('data-size'); i++) {text += possible.charAt(Math.floor(Math.random() * possible.length));}
  $(id).val(""+text);
  return text;
}

function dropClientUser(client_id,user_id,user_name){
	swal({
		title: "Видалити користувача \""+user_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (client_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropClientUser','client_id':client_id,'user_id':user_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadClientUsers(client_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveClientUserForm(client_id,user_id){
	var user_name=$("#user_name").val();
	swal({
		title: "Зберегти зміни Користувача \""+user_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var user_name=$("#user_name").val();
			var user_email=$("#user_email").val();
			var user_phone=$("#user_phone").val();
			var user_pass=$("#user_pass").val();
			var user_main=0; if(document.getElementById("user_main").checked) { user_main=1;}
			var price_main=0; if(document.getElementById("user_price").checked) { price_main=1;}
			var export_main=0; if(document.getElementById("user_export").checked) { export_main=1;}

			if (client_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveClientUserForm','client_id':client_id,'user_id':user_id,'user_name':user_name,'user_email':user_email,'user_phone':user_phone,'user_pass':user_pass,'user_main':user_main,'price_main':price_main,'export_main':export_main},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadClientUsers(client_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}


function loadClientCommets(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientCommets', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("client_commets_place").innerHTML=result["content"];
		}}, true);
	}
}

function saveClientComment(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		var comment=$("#client_comment_field").val();
		if (comment.length<=0){toastr["error"]("Напишіть коментар спочатку");}
		if (comment.length>0){
			JsHttpRequest.query($rcapi,{ 'w': 'saveClientComment', 'client_id':client_id, 'comment':comment}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadClientCommets(client_id); $("#client_comment_field").val(""); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}
function dropClientComment(client_id,cmt_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		
		if(confirm('Видалити запис?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'dropClientComment', 'client_id':client_id, 'cmt_id':cmt_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadClientCommets(client_id); toastr["info"]("Запис успішно видалено"); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}
function loadClientCDN(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientCDN', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("client_cdn_place").innerHTML=result["content"];
		}}, true);
	}
}

function showClientsCDNUploadForm(client_id){
	$("#cdn_client_id").val(client_id);
	var myDropzone2 = new Dropzone("#myDropzone2",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone2.removeAllFiles(true);
	myDropzone2.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$('#fileClientsCDNUploadForm').modal('hide');
		loadClientCDN(client_id);
	});
}

function showClientsCDNDropConfirmForm(client_id,file_id,file_name){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'clientsCDNDropFile', 'client_id':client_id, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadClientCDN(client_id); toastr["info"]("Файл успішно видалено"); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function viewClientsDetailsFile(client_id,file_type){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		$("#viewDetailsForm").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientsDetailsFile', 'client_id':client_id, 'file_type':file_type}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("client_details_files_place").innerHTML=result["content"];
		}}, true);
	}
}


function fileClientsDetailsUploadForm(client_id,file_type){
	$("#dtls_client_id").val(client_id);
	$("#dtls_file_type").val(file_type);
	$("#fileClientsDetailsUploadForm").modal("show");
	var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$('#fileClientsDetailsUploadForm').modal('hide');
		viewClientsDetailsFile(client_id,file_type);
	});
}


function showClientsDetailsDropConfirmForm(client_id,file_type,file_id,file_name){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'clientsDetailsDropFile', 'client_id':client_id, 'file_type':file_type, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ viewClientsDetailsFile(client_id,file_type); toastr["info"]("Файл успішно видалено"); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function showCountryManual(){
	var country_id=$("#country_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCountryManual','country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CountryModalWindow").modal('show');
		document.getElementById("CountryBody").innerHTML=result["content"];
		setTimeout(function(){
		  $('#datatable_country').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
		
	}}, true);
}


function selectCountry(id,name){
	$("#country_id").val(id);
	$("#country_name").val(name);
	$("#CountryModalWindow").modal('hide');
}
function showCountryForm(country_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCountryForm','country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML=result["header"];
	}}, true);
}

function saveClientCountryForm(){
	var id=$("#form_country_id").val();
	var name=$("#form_country_name").val();
	var alfa2=$("#form_country_alfa2").val();
	var alfa3=$("#form_country_alfa3").val();
	var duty=$("#form_country_duty").val();
	var risk=$("#form_country_risk").val();
	
	JsHttpRequest.query($rcapi,{ 'w':'saveClientCountryForm','id':id,'name':name,'alfa2':alfa2,'alfa3':alfa3,'duty':duty,'risk':risk},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			$("#country_id").val(id);
			showCountryManual();
		}
		else{ swal("Помилка!", result["error"], "error");}
		
	}}, true);
	
}
function showCostumsManual(){
	var costums_id=$("#costums_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsManual','costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CostumsModalWindow").modal('show');
		document.getElementById("CostumsBody").innerHTML=result["content"];
		setTimeout(function(){
		  $('#datatable_costums').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
		
	}}, true);
}
function selectCostums(id,name){
	$("#costums_id").val(id);
	$("#costums_name").val(name);
	$("#CostumsModalWindow").modal('hide');
}
function showCostumsForm(costums_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsForm','costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML=result["header"];
	}}, true);
}

function saveClientCostumsForm(){
	var id=$("#form_costums_id").val();
	var name=$("#form_costums_name").val();
	var preferential_rate=$("#form_costums_preferential_rate").val();
	var full_rate=$("#form_costums_full_rate").val();
	var type_declaration=$("#form_costums_type_declaration").val();
	var sertification=$("#form_costums_sertification").val();
	var gos_standart=$("#form_costums_gos_standart").val();
	
	JsHttpRequest.query($rcapi,{ 'w':'saveClientCostumsForm','id':id,'name':name,'preferential_rate':preferential_rate,'full_rate':full_rate,'type_declaration':type_declaration,'sertification':sertification,'gos_standart':gos_standart},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			$("#costums_id").val(id);
			showCostumsManual();
		}
		else{ swal("Помилка!", result["error"], "error");}
		
	}}, true);
	
}



function loadClientStorage(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientStorage', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("storage_place").innerHTML=result["content"];
		}}, true);
	}
}

function showClientStorageForm(client_id,storage_id){
	JsHttpRequest.query($rcapi,{ 'w':'showClientStorageForm','client_id':client_id,'storage_id':storage_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML=result["header"];
		$("#storage_country_id").select2({placeholder: "Виберіть країну",dropdownParent: $("#FormModalWindow")});
		$("#storage_state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#FormModalWindow")});
		$("#storage_region_id").select2({placeholder: "Виберіть регіон",dropdownParent: $("#FormModalWindow")});
		$("#storage_city_id").select2({placeholder: "Виберіть населений пунк",dropdownParent: $("#FormModalWindow")});
	}}, true);
}
function saveClientStorageForm(client_id,storage_id){

	var name=$("#storage_name").val();
	var email=$("#storage_email").val();
	var phone=$("#storage_phone").val();
	var contact_person=$("#storage_contact_person").val();
	var country=$("#storage_country_id option:selected").val();
	var state=$("#storage_state_id option:selected").val();
	var region=$("#storage_region_id option:selected").val();
	var city=$("#storage_city_id option:selected").val();
	var client_visible=0;if (document.getElementById("client_visible").checked){client_visible=1;}

	
	JsHttpRequest.query($rcapi,{ 'w':'saveClientStorageForm','client_id':client_id,'storage_id':storage_id,'name':name,'email':email,'phone':phone,'contact_person':contact_person,'country':country,'state':state,'region':region,'city':city,'client_visible':client_visible},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			$("#FormModalWindow").modal('hide');
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			loadClientStorage(client_id);
		}
		else{ swal("Помилка!", result["error"], "error");}
		
	}}, true);
	
}

function loadStorageStateSelectList(){
	var country_id=$("#storage_country_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientStateSelectList', 'country_id':country_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("storage_state_id").innerHTML=result["content"];
		$("#storage_state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#FormModalWindow")});
	}}, true);
}
function loadStorageRegionSelectList(){
	var state_id=$("#storage_state_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientRegionSelectList', 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("storage_region_id").innerHTML=result["content"];
		$("#storage_region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#FormModalWindow")});
	}}, true);
}
function loadStorageCitySelectList(){
	var region_id=$("#storage_region_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientCitySelectList', 'region_id':region_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("storage_city_id").innerHTML=result["content"];
		$("#storage_city_id").select2({placeholder: "Виберіть район",dropdownParent: $("#FormModalWindow")});
	}}, true);
}

function loadClientSupplConditions(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientSupplConditions', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("suppl_conditions_place").innerHTML=result["content"];
			$('#client_tabs').tab();
			var elem1 = document.querySelector('#return_goods');if (elem1){ var return_goods = new Switchery(elem1, { color: '#1AB394' });}
			var elem2 = document.querySelector('#prepayment');if (elem2){ var prepayment = new Switchery(elem2, { color: '#1AB394' });}
			$('#prepay_all').iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
			$('#prepay_all').on('ifUnchecked', function(event){
				$("#prepay_summ").removeAttr('readonly');
			});
			$('#prepay_all').on('ifChecked', function(event){
				$("#prepay_summ").attr('readonly', true);
			});
		}}, true);
	}
}

function saveClientSupplConditions(client_id){
	swal({
		title: "Зберегти зміни у розділі \"Умови постачальників\"?",text: "Внесені Вами зміни вплинуть на роботу магазину",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var prepayment=0; if (document.getElementById("prepayment").checked){prepayment=1;}
			var prepay_all=0; if (document.getElementById("prepay_all").checked){prepay_all=1;}
			var prepay_summ=$("#prepay_summ").val();
			var prepay_type=$("#prepay_type option:selected").val();
			var prepay_persent=parseInt($("#prepay_persent").val());
			
			var er=0;
			
			if (prepay_persent<0 || prepay_persent>100){er=1; swal("Помилка!", "Відсоток передоплати не повинен бути більше 100", "error");}
			if ((prepay_persent<0 || prepay_persent>100) && er==0){
				
			}
		
			if (client_id.length>0 && er==0){
				JsHttpRequest.query($rcapi,{ 'w':'saveClientSupplConditions','client_id':client_id,'prepayment':prepayment,'prepay_all':prepay_all,'prepay_summ':prepay_summ,'prepay_type':prepay_type,'prepay_persent':prepay_persent},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
					}
					else{ swal("Помилка!", result["error"], "error");}
					
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}
function changePrepayment(){
	if (document.getElementById("prepayment").checked){
		$("#prepay_all").removeAttr('disabled');
		$("#prepay_summ").removeAttr('readonly');
		$("#prepay_type").removeAttr('readonly');
		changePrepayType();
		$("#prepay_persent").removeAttr('readonly');
	}else{
		$("#prepay_all").attr('disabled', true);
		$("#prepay_summ").attr('readonly', true);
		$("#prepay_type").attr('readonly', true);
		changePrepayType();
		$("#prepay_persent").attr('readonly', true);
		
	}
	return;
}

function changePrepayType(){
	var prepay_type=$("#prepay_type option:selected").val();
	if (prepay_type==66){
		$("#prepay_persent").removeAttr('readonly');
	}
	if (prepay_type==65){
		$("#prepay_persent").attr('readonly', true);
	}
	return;
}

function showClientGeneralSaldoForm(client_id){
	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showClientGeneralSaldoForm', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML=result["header"];
			$('#saldo_data_start').datepicker({format: "yyyy-mm-dd",autoclose:true})
			$('#saldo_data_end').datepicker({format: "yyyy-mm-dd",autoclose:true})
		}}, true);
	}
}
function filterClientGeneralSaldoForm(client_id){
	if (client_id.length>0){
		var from=$("#saldo_data_start").val();
		var to=$("#saldo_data_end").val();
		JsHttpRequest.query($rcapi,{ 'w': 'filterClientGeneralSaldoForm', 'client_id':client_id,'from':from,'to':to}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("client_saldo_list_range").innerHTML=result["range"];
			document.getElementById("client_saldo_start").innerHTML=result["saldo_start"];
			document.getElementById("client_saldo_end").innerHTML=result["saldo_end"];
			document.getElementById("client_saldo_data_start").innerHTML=result["saldo_data_start"];
			document.getElementById("client_saldo_data_end").innerHTML=result["saldo_data_end"];
			
		}}, true);
	}
}


function viewJpayMoneyPay(pay_id){
	JsHttpRequest.query($rcapi,{ 'w': 'viewJpayMoneyPay', 'pay_id':pay_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
		document.getElementById("FormModalBody2").innerHTML=result["content"];
		document.getElementById("FormModalLabel2").innerHTML="Оплата накладної";
		numberOnlyPlace("sale_invoice_kredit");
		numberOnlyPlace("cash_kours");
	}}, true);
}

function getSaleInvoceProlog() {
	var client_id=$("#client_id").val();
	var date_search=$("#data_search").val();
	console.log(date_search);
	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getSaleInvoceProlog', 'client_id':client_id,'date_search':date_search}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML="Пролонгація документів";
		}}, true);
	}
}

function checkSaleInvoceProlog() {
	var client_id=$("#client_id").val();
	var date_start=$("#data_search").val();
	var date_new=$("#data_pay").val();
	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'checkSaleInvoceProlog', 'client_id':client_id,'date_start':date_start,'date_new':date_new}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML="Пролонгація документів";
		}}, true);
	}
}

function getSaleInvocePrologHistory() {
	var client_id=$("#client_id").val();
	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getSaleInvocePrologHistory', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML="Історія пролонгація документів";
			$('#datatable_prolog').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}}, true);
	}
}

function editSaleInvoceProlog() {
	swal({
		title: "Зберегти зміни?",text: "Внесені Вами зміни вплинуть на роботу магазину",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var client_id=$("#client_id").val();
			var date_start=$("#data_search").val();
			var date_new=$("#data_pay").val();
			if (client_id.length>0 && date_new.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'editSaleInvoceProlog','client_id':client_id, 'date_start':date_start, 'date_new':date_new},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						getSaleInvoceProlog();
					}
					else{ swal("Помилка!", result["error"], "error");}			
				}}, true);
			} else {
				swal("Відмінено", "Спочатку введіть дату!", "error");
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadClientSupplDocuments(client_id) {
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientDocuments', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("suppl_mandate").innerHTML=result.content[0];
			document.getElementById("suppl_basis").innerHTML=result.content[1];
		}}, true);
	}
}

function loadClientSupplMandate(client_id) {
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientSupplMandate', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("suppl_mandate").innerHTML=result.content;
			document.getElementById("suppl_mandate").style.padding="20px";
			document.getElementById("suppl_basis").innerHTML="";
		}}, true);
	}
}

function loadClientSupplBasis(client_id) {
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientSupplBasis', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("suppl_mandate").innerHTML="";
			document.getElementById("suppl_mandate").style.padding="0";
			document.getElementById("suppl_basis").innerHTML=result.content;
		}}, true);
	}
}

function showClientMandateForm(client_id,mandate_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w':'showClientMandateForm','client_id':client_id,'mandate_id':mandate_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML=result["header"];
		}}, true);
	}
}

function saveClientMandateForm(client_id,mandate_id){
	var number=$("#mandate_number").val();
	var seria=$("#mandate_seria").val();
	var receiver=$("#mandate_receiver").val();
	var data_from=$("#mandate_data_from").val();
	var data_to=$("#mandate_data_to").val();

	JsHttpRequest.query($rcapi,{ 'w':'saveClientMandateForm','client_id':client_id,'mandate_id':mandate_id,'number':number,'seria':seria,'receiver':receiver,'data_from':data_from,'data_to':data_to},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			$("#FormModalWindow").modal('hide');
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			loadClientSupplDocuments(client_id);
		}
		else{ swal("Помилка!", result["error"], "error");}
		
	}}, true);
	
}

function dropClientMandate(client_id,mandate_id){
	swal({
		title: "Видалити доручення документа\""+mandate_id+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (client_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropClientMandate','client_id':client_id,'mandate_id':mandate_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadClientSupplDocuments(client_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function showClientBasisForm(client_id,basis_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w':'showClientBasisForm','client_id':client_id,'basis_id':basis_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML=result["header"];
		}}, true);
	}
}

function saveClientBasisForm(client_id,basis_id){
	var number=$("#basis_number").val();
	var data_from=$("#basis_data_from").val();
	var data_to=$("#basis_data_to").val();

	JsHttpRequest.query($rcapi,{ 'w':'saveClientBasisForm','client_id':client_id,'basis_id':basis_id,'number':number,'data_from':data_from,'data_to':data_to},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			$("#FormModalWindow").modal('hide');
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			loadClientSupplDocuments(client_id);
		}
		else{ swal("Помилка!", result["error"], "error");}
		
	}}, true);
	
}

function dropClientBasis(client_id,basis_id){
	swal({
		title: "Видалити доручення документа\""+basis_id+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (client_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropClientBasis','client_id':client_id,'basis_id':basis_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadClientSupplDocuments(client_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function printGeneralSaldoList() {
	var saldo_data_start=$("#saldo_data_start").val();
	var saldo_data_end=$("#saldo_data_end").val();
	var client_id=$("#client_id").val();
	window.open("/Clients/printCl1/"+client_id+"/"+saldo_data_start+"/"+saldo_data_end,"_blank","printWindow");
}

function showClientConditionsHistory(client_id) {
	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showClientConditionsHistory','client_id':client_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#ClientConditionsHistory").modal('show');
			$("#ClientConditionsHistoryBody").html(result.content);
		}}, true);
	}
}
