var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

$(document).ready(function() {
	$("#flClientCategoryTree") .on('changed.jstree', function (e, data) {
		var i, j, r = [];
		for(i = 0, j = data.selected.length; i < j; i++) {
	  		r.push(data.instance.get_node(data.selected[i]).text);
		}
		$('#flClientCategoryTree_result').val('' + r.join(', '));
		var i, j, r = [];
		for(i = 0, j = data.selected.length; i < j; i++) {
	  		r.push(data.instance.get_node(data.selected[i]).id);
		}
		$("#flClientCategoryTree_id").val(''+r.join(', '));
	}).jstree({
		'core' : {'check_callback' : true},
		'plugins' : [ 'types', 'dnd',"sort" ],
		'types' : {'default' : {'icon' : 'fa fa-folder'},}
	});
});

function ShowCheckAll() {$("#checkAll").change(function () {$("input:checkbox").prop("checked", $(this).prop("checked"));});}

function filterClientsList(){
	let client_id=$("#filClientId").val();
    let client_name=$("#filClientName").val();
    let phone=$("#filPhone").val();
    let email=$("#filEmail").val();
    let state_id=$("#filState option:selected").val();
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
        let client_id=result["client_id"];
		if (client_id==0) {
			checkEmptyClients(client_id);
		} else {
			showClientCard(client_id);
		}
	}}, true);
}

function checkEmptyClients(client_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'checkEmptyClients'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		if(!result.content) {
			showClientCard(client_id);
		} else {
			$("#ClientEmpty").modal("show");
			$("#ClientEmptyBody").html(result.content);
		}
	}}, true);
}

function showClientCard(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showClientCard', 'client_id':client_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){
		let client_card = $("#ClientCard");
		client_card.modal("show");
		$("#ClientCardBody").html(result["content"]);
		$("#ClientCardLabel").html($("#client_name").val()+" (ID:"+$("#client_id").val()+")");
		$("#client_tabs").tab();
		$("#comment_info").markdown({autofocus:false,savable:false});
		$("#country_id").select2({placeholder: "Виберіть країну",dropdownParent: client_card});
		$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: client_card});
		$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: client_card});
		$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: client_card}).on('select2:close', function() {var el = $(this);
			if(el.val()==="NEW") { var newval = prompt("Введіть нове значення: ");
			  if(newval !== null) {
				let region_id=$("#region_id option:selected").val();
				JsHttpRequest.query($rcapi,{ 'w': 'addNewCity', 'region_id':region_id, 'name':newval},
				function (result, errors){ if (errors) {alert(errors);} if (result){
					el.append('<option id="'+result["id"]+'">'+newval+'</option>').val(newval);
				}}, true);
			  }
			}
		  });
		$(".i-checks").iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
	}}, true);
}

function showClientRetailList(press_btn){
	var status=$("#input_done").val();
	if (press_btn) {
		status==="true" ? status=true : status=false;
		if (status){
            $("#input_done").val("false");
			$("#toggle_done").html("<i class='fa fa-eye-slash'></i>");
		} else  {
            $("#input_done").val("true");
			$("#toggle_done").html("<i class='fa fa-eye'></i>");
		}	
	} else {
        status==="true" ? status=false : status=true;
    }
	var prevRange=$("#clients_range").html();
	JsHttpRequest.query($rcapi,{ 'w': 'showClientRetailList', 'status':status}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		if (prevRange.length != result["content"].length){
            let dt=$("#datatable");
            dt.DataTable().destroy();
            $("#clients_range").empty();
            $("#clients_range").html(result.content);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
        }
	}}, true);
}

function showClientRetailCard(user_id){
	if (user_id<=0 || user_id==""){toastr["error"](errs[0]);}
	if (user_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showClientRetailCard', 'user_id':user_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			let client_card = $("#ClientCard");
			client_card.modal("show");
            $("#ClientCardBody").html(result["content"]);
            $("#ClientCardLabel").html($("#user_name").val()+" (ID:"+$("#user_id").val()+")");
			$("#country_id").select2({placeholder: "Виберіть країну",dropdownParent: client_card});
			$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: client_card});
			$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: client_card});
			$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: client_card})
		}}, true);
	}
}

function newClientRetailCard(){
	JsHttpRequest.query($rcapi,{ 'w': 'newClientRetailCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
        let user_id=result["user_id"];
		showClientRetailCard(user_id);
	}}, true);
}

function saveClientRetailGeneralInfo(){
    let user_id=$("#user_id").val();
    let user_name=$("#user_name").val();
    let country_id=$("#country_id option:selected").val();
    let state_id=$("#state_id option:selected").val();
    let region_id=$("#region_id option:selected").val();
    let city_id=$("#city_id option:selected").val();
    let user_category=$("#user_category").val();
    let user_phone=$("#user_phone").val();
    let user_email=$("#user_email").val();
    let user_status=$("#user_status").val();
	swal({
		title: "Зберегти дані користувача \""+user_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (user_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveClientRetailGeneralInfo', 'user_id':user_id, 'user_name':user_name, 'country_id':country_id, 'state_id':state_id, 'region_id':region_id, 'city_id':city_id, 'user_category':user_category, 'user_phone':user_phone, 'user_email':user_email, 'user_status':user_status},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#ClientCard").modal("hide");
						showClientRetailList();
					} else { swal("Помилка!", result["error"], "error");}
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
						$("#ClientCard").modal("hide");
						showClientRetailList();
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function setClientRetail(client_id,client_name) {
    let user_id=$("#user_id").val();
    let user_name=$("#user_name").val();
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
						$("#FormModalWindow").modal("hide");
						$("#ClientCard").modal("hide");
						showClientRetailList();
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
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
        $("#state_id").html(result["content"]);
		$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#ClientCard")});
	}}, true);
}

function loadRegionSelectList(){
    let state_id=$("#state_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientRegionSelectList', 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#region_id").html(result["content"]);
		$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#ClientCard")});
	}}, true);
}

function loadCitySelectList(){
    let region_id=$("#region_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientCitySelectList', 'region_id':region_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("city_id").innerHTML=result["content"];
		$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#ClientCard")}).on('select2:close', function() {
			var el = $(this);
			if(el.val()==="NEW") {
				var newval = prompt("Введіть нове місто: ");
				if(newval !== null) {
                    let region_id=$("#region_id option:selected").val();
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
			$("#conditions_place").html(result["content"]);
			$("#client_tabs").tab();
			let client_card = $("#ClientCard");
			$("#cash_id").select2({placeholder: "Основна валюта",dropdownParent: client_card});
			$("#country_cash_id").select2({placeholder: "Національна валюта",dropdownParent: client_card});
			$("#price_lvl").select2({placeholder: "Прайс",dropdownParent: client_card});
			$("#price_suppl_lvl").select2({placeholder: "Прайс",dropdownParent: client_card});
			$("#credit_cash_id").select2({placeholder: "Валюта кредиту",dropdownParent: client_card});
			$("#tpoint_id").select2({placeholder: "Торгова точка",dropdownParent: client_card});
			$(".i-checks").iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
		}}, true);
	}
}

/* TRUE DETAILS*/

function loadClientDetailsList(client_id) {
    if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
    if (client_id>0){
        JsHttpRequest.query($rcapi,{ 'w': 'loadClientDetailsList', 'client_id':client_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#details_place_list").html(result["content"]);
                $("#catalogue_tabs").tab();
            }}, true);
    }
}

function loadClientDetailsCard(detail_id) {
	let client_id=$("#client_id").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientDetailsCard', 'detail_id':detail_id, 'client_id':client_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#DetailsCard").modal("show");
            $("#DetailCardBody").html(result["content"]);
            $("#details_str").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});
        }}, true);
}

function dropClientDetailsCard(detail_id) {
    swal({
            title: "Видалити реквізит?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (detail_id.length>0){
                    JsHttpRequest.query($rcapi,{ 'w':'dropClientDetailsCard','detail_id':detail_id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#DetailsCard").modal("hide");
                                loadClientDetailsList();
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function preconfirmClientDetailsCard() {
    swal({
            title: "Зберегти зміни у розділі \"Реквізити\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                saveClientDetailsCard();
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function saveClientDetailsCard(){
    let detail_id=$("#detail_id").val();
    let client_id=$("#client_id").val();
    let address_jur=$("#address_jur").val();
    let address_fakt=$("#address_fakt").val();
    let edrpou=$("#edrpou").val();
    let svidotctvo=$("#svidotctvo").val();
    let vytjag=$("#vytjag").val();
    let vat=$("#vat").val();
    let mfo=$("#mfo").val();
    let bank=$("#bank").val();
    let account=$("#account").val();
    let nr_details=$("#nr_details").val();
    let not_resident=0; if (document.getElementById("not_resident").checked){not_resident=1;}else{nr_details="";}
    let buh_name=$("#buh_name").val();
    let buh_edrpou=$("#buh_edrpou").val();
    let main_details=0; if (document.getElementById("main_details").checked){main_details=1;}
    if (client_id.length>0){
        JsHttpRequest.query($rcapi,{ 'w':'saveClientDetailsCard','detail_id':detail_id,'client_id':client_id,'address_jur':address_jur,'address_fakt':address_fakt,'edrpou':edrpou,'svidotctvo':svidotctvo,'vytjag':vytjag,'vat':vat,'mfo':mfo, 'bank':bank,'account':account,'not_resident':not_resident,'nr_details':nr_details,'buh_name':buh_name,'buh_edrpou':buh_edrpou,'main_details':main_details},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                if (result["answer"]==1){
                    swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                    $("#DetailsCard").modal("hide");
                    loadClientDetailsList();
                } else { swal("Помилка!", result["error"], "error");}
            }}, true);
    }
}

/*END TRUE DETAILS*/

function loadClientDetails(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientDetails', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#details_place").html(result["content"]);
			$("#catalogue_tabs").tab();
		}}, true);
	}
}

function norResidentAcive(){
	if (document.getElementById("not_resident").checked){
		document.getElementById("nr_details").disabled="";
	} else {document.getElementById("nr_details").disabled="disabled";}
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
    let client_id=$("#client_id").val();
    let address_jur=$("#address_jur").val();
    let address_fakt=$("#address_fakt").val();
    let edrpou=$("#edrpou").val();
    let svidotctvo=$("#svidotctvo").val();
    let vytjag=$("#vytjag").val();
    let vat=$("#vat").val();
    let mfo=$("#mfo").val();
    let bank=$("#bank").val();
    let account=$("#account").val();
    let nr_details=$("#nr_details").val();
    let not_resident=0; if (document.getElementById("not_resident").checked){not_resident=1;}else{nr_details="";}
    let buh_name=$("#buh_name").val();
    let buh_edrpou=$("#buh_edrpou").val();
	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveClientDetails','client_id':client_id,'address_jur':address_jur,'address_fakt':address_fakt,'edrpou':edrpou,'svidotctvo':svidotctvo,'vytjag':vytjag,'vat':vat,'mfo':mfo, 'bank':bank,'account':account,'not_resident':not_resident,'nr_details':nr_details,'buh_name':buh_name,'buh_edrpou':buh_edrpou},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
				filterClientsList();
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function loadClientDocumentPrefix(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientDocumentPrefix', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#document_prefix_place").html(result["content"]);
			$("#catalogue_tabs").tab();
		}}, true);
	}
}

function showClientDocumentPrefixForm(client_id, prefix_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showClientDocumentPrefixForm', 'client_id':client_id, 'prefix_id':prefix_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalBody").html(result["content"]);
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
				JsHttpRequest.query($rcapi,{ 'w':'dropClientDocumentPrefix', 'client_id':client_id, 'prefix_id':prefix_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadClientDocumentPrefix(client_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveClientDocumentPrefixForm(client_id,prefix_id){
    let prefix=$("#prefix").val();
	swal({
		title: "Зберегти префікс документа Користувача \""+prefix+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let doc_type_id=$("#doc_type_id option:selected").val();
			if (client_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveClientDocumentPrefixForm','client_id':client_id,'prefix_id':prefix_id,'doc_type_id':doc_type_id,'prefix':prefix},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadClientDocumentPrefix(client_id);
					} else { swal("Помилка!", result["error"], "error");}
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
            $("#contacts_place").html(result["content"]);
			$("#catalogue_tabs").tab();
		}}, true);
	}
}

function showClientContactForm(client_id, contact_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showClientContactForm', 'client_id':client_id, 'contact_id':contact_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalBody").html(result["content"]);
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
				JsHttpRequest.query($rcapi,{ 'w':'dropClientContact', 'client_id':client_id, 'contact_id':contact_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadClientContacts(client_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveClientContactForm(client_id,contact_id){
    let contact_name=$("#contact_name").val();
	swal({
		title: "Зберегти зміни контакту \""+contact_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let contact_post=$("#contact_post").val();
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
					} else { swal("Помилка!", result["error"], "error");}
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
    let client_id=$("#client_id").val();
    let org_type=$("#org_type option:selected").val();
    let client_name=$("#client_name").val();
    let client_full_name=$("#client_full_name").val();
    let phone=$("#phone").val();
    let email=$("#email").val();
    let parrent_id=$("#parrent_id").val();
    let country_id=$("#country_id option:selected").val();
    let state_id=$("#state_id option:selected").val();
    let region_id=$("#region_id option:selected").val();
    let city_id=$("#city_id option:selected").val();
    let c_category_kol=$("#c_category_kol").val();
    let user_category=$("#user_category").val();
	var c_category=[]; var cc="";
	for (var i=1;i<=c_category_kol;i++){var cc=0;if(document.getElementById("c_category_"+i).checked) { cc=$("#c_category_"+i).val(); }c_category[i]=cc;}

	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveClientGeneralInfo', 'client_id':client_id, 'org_type':org_type, 'name':client_name, 'full_name':client_full_name, 'phone':phone, 'email':email, 'parrent_id':parrent_id, 'country_id':country_id, 'state_id':state_id, 'region_id':region_id, 'city_id':city_id, 'c_category_kol':c_category_kol, 'c_category':c_category, 'user_category':user_category},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
				// $("#ClientCard").modal("hide");
				// $("#ClientCard").on('hidden.bs.modal', function () {
				// 	let client_input = $("#client_id").val();
				// 	if (client_input=="0") {
				// 		showClientCard(result["client_id"]);
				// 	}
				// });
                showClientCard(result["client_id"]);
				//var art=$("#catalogue_art").val();
				//if (art.length>0){
				//	catalogue_client_search();
				//}
			} else { swal("Помилка!", result["error"], "error");}
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
    let cash_id=$("#cash_id option:selected").val();
    let country_cash_id=$("#country_cash_id option:selected").val();
    let price_lvl=$("#price_lvl option:selected").val();
    let margin_price_lvl=$("#margin_price_lvl").val();
    let price_suppl_lvl=$("#price_suppl_lvl option:selected").val();
    let margin_price_suppl_lvl=$("#margin_price_suppl_lvl").val();
    let markup_min=$("#markup_min").val();
    let tpoint_id=$("#tpoint_id option:selected").val();
    let payment_delay=$("#payment_delay").val();
    let credit_limit=$("#credit_limit").val();
    let credit_cash_id=$("#credit_cash_id option:selected").val();
    let credit_return=$("#credit_return").val();
    let client_vat=0; if (document.getElementById("client_vat").checked){client_vat=1;}
    let doc_type_id=$("#doc_type_id option:selected").val();
    let rounding_price=$("#rounding_price option:selected").val();
    let detail_id=$("#client_details option:selected").val();
	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveClientConditions', 'client_id':client_id, 'cash_id':cash_id, 'country_cash_id':country_cash_id, 'price_lvl':price_lvl, 'margin_price_lvl':margin_price_lvl, 'price_suppl_lvl':price_suppl_lvl, 'margin_price_suppl_lvl':margin_price_suppl_lvl, 'markup_min':markup_min, 'tpoint_id':tpoint_id, 'client_vat':client_vat, 'payment_delay':payment_delay, 'credit_limit':credit_limit, 'credit_cash_id':credit_cash_id, 'credit_return':credit_return, 'doc_type_id':doc_type_id, 'rounding_price':rounding_price, 'detail_id':detail_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                loadClientConditions(client_id);
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function showClientsParrentTree(client_id,parrent_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showClientsParrentTree', 'client_id':client_id, 'parrent_id':parrent_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html("Контрагенти");
			$("#datatable_parrent").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
		}}, true);
	}
}

function setClientParrent(id,name){
	$("#parrent_id").val(id);
	$("#parrent_name").val(name);
	$("#FormModalWindow").modal("hide");
    $("#FormModalBody").html("");
    $("#FormModalLabel").html("");
}

function unlinkClientsParrent(client_id){
	swal({
		title: "Відв`язати контагента від \""+$("#parrent_name").val()+"\"?",text: "Внесені Вами зміни вплинуть на роботу Контагента",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'unlinkClientsParrent', 'client_id':client_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					$("#parrent_id").val("0");
					$("#parrent_name").val("");
					swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
				} else { toastr["error"](result["error"]); }
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
				} else { toastr["error"](result["error"]); }
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
            $("#users_place").html(result["content"]);
		}}, true);
	}
}

function showClientUserForm(client_id, user_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showClientUserForm', 'client_id':client_id, 'user_id':user_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalBody").html(result["content"]);
			$(".i-checks").iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
		}}, true);
	}
}

function randString(id){
	var dataSet = $(id).attr("data-character-set").split(",");
	var possible = ""; var text = "";
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
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveClientUserForm(client_id,user_id){
	let user_name=$("#user_name").val();
	swal({
		title: "Зберегти зміни Користувача \""+user_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let user_email=$("#user_email").val();
            let user_phone=$("#user_phone").val();
            let user_pass=$("#user_pass").val();
            let user_main=0; if(document.getElementById("user_main").checked) { user_main=1;}
            let price_main=0; if(document.getElementById("user_price").checked) { price_main=1;}
            let export_main=0; if(document.getElementById("user_export").checked) { export_main=1;}
            let user_invoice=$("#user_invoice").val();
            if (client_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveClientUserForm','client_id':client_id,'user_id':user_id,'user_name':user_name,'user_email':user_email,'user_phone':user_phone,'user_pass':user_pass,'user_main':user_main,'price_main':price_main,'export_main':export_main, 'user_invoice':user_invoice},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadClientUsers(client_id);
					} else { swal("Помилка!", result["error"], "error");}
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
            $("#client_commets_place").html(result["content"]);
		}}, true);
	}
}

function saveClientComment(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
        let comment=$("#client_comment_field").val();
		if (comment.length<=0){toastr["error"]("Напишіть коментар спочатку");}
		if (comment.length>0){
			JsHttpRequest.query($rcapi,{ 'w': 'saveClientComment', 'client_id':client_id, 'comment':comment}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadClientCommets(client_id);
					$("#client_comment_field").val("");
				} else { toastr["error"](result["error"]); }
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
				if (result["answer"]==1){
					loadClientCommets(client_id);
					toastr["info"]("Запис успішно видалено");
				} else { toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function loadClientCDN(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientCDN', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#client_cdn_place").html(result["content"]);
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
		$("#fileClientsCDNUploadForm").modal("hide");
		loadClientCDN(client_id);
	});
}

function showClientsCDNDropConfirmForm(client_id,file_id,file_name){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'clientsCDNDropFile', 'client_id':client_id, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadClientCDN(client_id);
					toastr["info"]("Файл успішно видалено");
				} else { toastr["error"](result["error"]); }
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
            $("#client_details_files_place").html(result["content"]);
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
		$("#fileClientsDetailsUploadForm").modal("hide");
		viewClientsDetailsFile(client_id,file_type);
	});
}

function showClientsDetailsDropConfirmForm(client_id,file_type,file_id,file_name){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'clientsDetailsDropFile', 'client_id':client_id, 'file_type':file_type, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					viewClientsDetailsFile(client_id,file_type);
					toastr["info"]("Файл успішно видалено");
				} else { toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function showCountryManual(){
    let country_id=$("#country_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCountryManual', 'country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CountryModalWindow").modal("show");
        $("#CountryBody").html(result["content"]);
		setTimeout(function(){
			$("#datatable_country").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCountry(id,name){
	$("#country_id").val(id);
	$("#country_name").val(name);
	$("#CountryModalWindow").modal("hide");
}

function showCountryForm(country_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCountryForm', 'country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
        $("#FormModalBody").html(result["content"]);
        $("#FormModalLabel").html(result["header"]);
	}}, true);
}

function saveClientCountryForm(){
    let id=$("#form_country_id").val();
    let name=$("#form_country_name").val();
    let alfa2=$("#form_country_alfa2").val();
    let alfa3=$("#form_country_alfa3").val();
    let duty=$("#form_country_duty").val();
    let risk=$("#form_country_risk").val();
	JsHttpRequest.query($rcapi,{ 'w':'saveClientCountryForm','id':id,'name':name,'alfa2':alfa2,'alfa3':alfa3,'duty':duty,'risk':risk},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			$("#country_id").val(id);
			showCountryManual();
		} else { swal("Помилка!", result["error"], "error");}
	}}, true);
}

function showCostumsManual(){
    let costums_id=$("#costums_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsManual', 'costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CostumsModalWindow").modal("show");
        $("#CostumsBody").html(result["content"]);
		setTimeout(function(){
	  		$("#datatable_costums").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCostums(id,name){
	$("#costums_id").val(id);
	$("#costums_name").val(name);
	$("#CostumsModalWindow").modal("hide");
}

function showCostumsForm(costums_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsForm', 'costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
        $("#FormModalBody").html(result["content"]);
        $("#FormModalLabel").html(result["header"]);
	}}, true);
}

function saveClientCostumsForm(){
    let id=$("#form_costums_id").val();
    let name=$("#form_costums_name").val();
    let preferential_rate=$("#form_costums_preferential_rate").val();
    let full_rate=$("#form_costums_full_rate").val();
    let type_declaration=$("#form_costums_type_declaration").val();
    let sertification=$("#form_costums_sertification").val();
    let gos_standart=$("#form_costums_gos_standart").val();
	JsHttpRequest.query($rcapi,{ 'w':'saveClientCostumsForm','id':id,'name':name,'preferential_rate':preferential_rate,'full_rate':full_rate,'type_declaration':type_declaration,'sertification':sertification,'gos_standart':gos_standart},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			$("#costums_id").val(id);
			showCostumsManual();
		} else { swal("Помилка!", result["error"], "error");}
	}}, true);
}

function loadClientStorage(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientStorage', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#storage_place").html(result["content"]);
		}}, true);
	}
}

function showClientStorageForm(client_id,storage_id){
	JsHttpRequest.query($rcapi,{ 'w':'showClientStorageForm', 'client_id':client_id, 'storage_id':storage_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		let modal_window = $("#FormModalWindow");
		modal_window.modal("show");
		$("#FormModalBody").html(result["content"]);
		$("#FormModalLabel").html(result["header"]);
		$("#storage_country_id").select2({placeholder: "Виберіть країну",dropdownParent: modal_window});
		$("#storage_state_id").select2({placeholder: "Виберіть область",dropdownParent: modal_window});
		$("#storage_region_id").select2({placeholder: "Виберіть регіон",dropdownParent: modal_window});
		$("#storage_city_id").select2({placeholder: "Виберіть населений пунк",dropdownParent: modal_window});
	}}, true);
}

function saveClientStorageForm(client_id,storage_id){
    let name=$("#storage_name").val();
    let email=$("#storage_email").val();
    let phone=$("#storage_phone").val();
    let contact_person=$("#storage_contact_person").val();
    let country=$("#storage_country_id option:selected").val();
    let state=$("#storage_state_id option:selected").val();
    let region=$("#storage_region_id option:selected").val();
    let city=$("#storage_city_id option:selected").val();
    let client_visible=0;if (document.getElementById("client_visible").checked){client_visible=1;}
	JsHttpRequest.query($rcapi,{ 'w':'saveClientStorageForm','client_id':client_id,'storage_id':storage_id,'name':name,'email':email,'phone':phone,'contact_person':contact_person,'country':country,'state':state,'region':region,'city':city,'client_visible':client_visible},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			$("#FormModalWindow").modal('hide');
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			loadClientStorage(client_id);
		} else { swal("Помилка!", result["error"], "error");}
	}}, true);
}

function loadStorageStateSelectList(){
	let country_id=$("#storage_country_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientStateSelectList', 'country_id':country_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#storage_state_id").html(result["content"]);
        $("#storage_state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#FormModalWindow")});
	}}, true);
}

function loadStorageRegionSelectList(){
    let state_id=$("#storage_state_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientRegionSelectList', 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#storage_region_id").html(result["content"]);
        $("#storage_region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#FormModalWindow")});
	}}, true);
}

function loadStorageCitySelectList(){
    let region_id=$("#storage_region_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientCitySelectList', 'region_id':region_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#storage_city_id").html(result["content"]);
		$("#storage_city_id").select2({placeholder: "Виберіть район",dropdownParent: $("#FormModalWindow")});
	}}, true);
}

function loadClientSupplConditions(client_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientSupplConditions', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#suppl_conditions_place").html(result["content"]);
			$("#client_tabs").tab();
			var elem1 = document.querySelector("#return_goods");if (elem1){ new Switchery(elem1, { color: '#1AB394' });}
			var elem2 = document.querySelector("#prepayment");if (elem2){ new Switchery(elem2, { color: '#1AB394' });}
			let prepay_all = $("#prepay_all");
            prepay_all.iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
            prepay_all.on('ifUnchecked', function(){
				$("#prepay_summ").removeAttr("readonly");
			});
            prepay_all.on('ifChecked', function(){
				$("#prepay_summ").attr("readonly", true);
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
			//if ((prepay_persent<0 || prepay_persent>100) && er===0){}
			if (client_id.length>0 && er===0){
				JsHttpRequest.query($rcapi,{ 'w':'saveClientSupplConditions','client_id':client_id,'prepayment':prepayment,'prepay_all':prepay_all,'prepay_summ':prepay_summ,'prepay_type':prepay_type,'prepay_persent':prepay_persent},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
					} else { swal("Помилка!", result["error"], "error");}
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
	return true;
}

function changePrepayType(){
	let prepay_type=$("#prepay_type option:selected").val();
	if (prepay_type==66){
		$("#prepay_persent").removeAttr('readonly');
	}
	if (prepay_type==65){
		$("#prepay_persent").attr('readonly', true);
	}
	return true;
}

function showClientGeneralSaldoForm(client_id){
	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showClientGeneralSaldoForm', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html(result["header"]);
			$("#saldo_data_start").datepicker({format: "yyyy-mm-dd",autoclose:true});
			$("#saldo_data_end").datepicker({format: "yyyy-mm-dd",autoclose:true});
		}}, true);
	}
}

function showClientSupplGeneralSaldoForm(client_id){
    if (client_id.length>0){
        JsHttpRequest.query($rcapi,{ 'w': 'showClientSupplGeneralSaldoForm', 'client_id':client_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#FormModalWindow").modal("show");
                $("#FormModalBody").html(result["content"]);
                $("#FormModalLabel").html(result["header"]);
                $("#saldo_data_start").datepicker({format: "yyyy-mm-dd",autoclose:true});
                $("#saldo_data_end").datepicker({format: "yyyy-mm-dd",autoclose:true});
            }}, true);
    }
}

function filterClientGeneralSaldoForm(client_id){
	if (client_id.length>0){
        let from=$("#saldo_data_start").val();
        let to=$("#saldo_data_end").val();
		JsHttpRequest.query($rcapi,{ 'w': 'filterClientGeneralSaldoForm', 'client_id':client_id, 'from':from, 'to':to},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#client_saldo_list_range").html(result["range"]);
			$("#client_saldo_start").html(result["saldo_start"]);
			$("#client_saldo_end").html(result["saldo_end"]);
			$("#client_saldo_data_start").html(result["saldo_data_start"]);
			$("#client_saldo_data_end").html(result["saldo_data_end"]);
		}}, true);
	}
}

function viewJpayMoneyPay(pay_id){
	JsHttpRequest.query($rcapi,{ 'w': 'viewJpayMoneyPay', 'pay_id':pay_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal("show");
        $("#FormModalBody2").html(result["content"]);
        $("#FormModalLabel2").html("Оплата накладної");
		numberOnlyPlace("sale_invoice_kredit");
		numberOnlyPlace("cash_kours");
	}}, true);
}

function getSaleInvoceProlog() {
    let client_id=$("#client_id").val();
    let date_search=$("#data_search").val();
	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getSaleInvoceProlog', 'client_id':client_id, 'date_search':date_search},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html("Пролонгація документів");
		}}, true);
	}
}

function checkSaleInvoceProlog() {
    let client_id=$("#client_id").val();
    let date_start=$("#data_search").val();
    let date_new=$("#data_pay").val();
	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'checkSaleInvoceProlog', 'client_id':client_id, 'date_start':date_start, 'date_new':date_new},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
			$("#FormModalBody").html(result["content"]);
			$("#FormModalLabel").html("Пролонгація документів");
		}}, true);
	}
}

function getSaleInvocePrologHistory() {
    let client_id=$("#client_id").val();
	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getSaleInvocePrologHistory', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html("Історія пролонгація документів");
            $("#datatable_prolog").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
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
            let client_id=$("#client_id").val();
            let date_start=$("#data_search").val();
            let date_new=$("#data_pay").val();
			if (client_id.length>0 && date_new.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'editSaleInvoceProlog', 'client_id':client_id, 'date_start':date_start, 'date_new':date_new},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						getSaleInvoceProlog();
					} else { swal("Помилка!", result["error"], "error");}
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
			$("#suppl_mandate").html(result.content[0]);
			$("#suppl_basis").html(result.content[1]);
		}}, true);
	}
}

function loadClientSupplMandate(client_id) {
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientSupplMandate', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#suppl_mandate").html(result.content);
			document.getElementById("suppl_mandate").style.padding="20px";
            $("#suppl_basis").html("");
        }}, true);
	}
}

function loadClientSupplBasis(client_id) {
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClientSupplBasis', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#suppl_mandate").html("");
			document.getElementById("suppl_mandate").style.padding="0";
            $("#suppl_basis").html(result.content);
        }}, true);
	}
}

function showClientMandateForm(client_id,mandate_id){
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w':'showClientMandateForm', 'client_id':client_id, 'mandate_id':mandate_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html(result["header"]);
		}}, true);
	}
}

function saveClientMandateForm(client_id,mandate_id){
	let number=$("#mandate_number").val();
    let seria=$("#mandate_seria").val();
    let receiver=$("#mandate_receiver").val();
    let data_from=$("#mandate_data_from").val();
    let data_to=$("#mandate_data_to").val();
	JsHttpRequest.query($rcapi,{ 'w':'saveClientMandateForm', 'client_id':client_id, 'mandate_id':mandate_id, 'number':number, 'seria':seria, 'receiver':receiver, 'data_from':data_from, 'data_to':data_to},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			$("#FormModalWindow").modal("hide");
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			loadClientSupplDocuments(client_id);
		} else { swal("Помилка!", result["error"], "error");}
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
				JsHttpRequest.query($rcapi,{ 'w':'dropClientMandate', 'client_id':client_id, 'mandate_id':mandate_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadClientSupplDocuments(client_id);
					} else { swal("Помилка!", result["error"], "error");}
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
		JsHttpRequest.query($rcapi,{ 'w':'showClientBasisForm', 'client_id':client_id, 'basis_id':basis_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html(result["header"]);
		}}, true);
	}
}

function saveClientBasisForm(client_id,basis_id){
    let number=$("#basis_number").val();
    let data_from=$("#basis_data_from").val();
    let data_to=$("#basis_data_to").val();
	JsHttpRequest.query($rcapi,{ 'w':'saveClientBasisForm', 'client_id':client_id, 'basis_id':basis_id, 'number':number, 'data_from':data_from, 'data_to':data_to},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			$("#FormModalWindow").modal("hide");
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			loadClientSupplDocuments(client_id);
		} else { swal("Помилка!", result["error"], "error");}
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
				JsHttpRequest.query($rcapi,{ 'w':'dropClientBasis', 'client_id':client_id, 'basis_id':basis_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadClientSupplDocuments(client_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function printGeneralSaldoList() {
    let saldo_data_start=$("#saldo_data_start").val();
    let saldo_data_end=$("#saldo_data_end").val();
    let client_id=$("#client_id").val();
	window.open("/Clients/printCl1/"+client_id+"/"+saldo_data_start+"/"+saldo_data_end,"_blank","printWindow");
}

function showClientConditionsHistory(client_id) {
	if (client_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showClientConditionsHistory', 'client_id':client_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#ClientConditionsHistory").modal("show");
			$("#ClientConditionsHistoryBody").html(result.content);
		}}, true);
	}
}

function loadClientAddresses(client_id) {
    if (client_id<=0 || client_id===""){toastr["error"](errs[0]);}
    if (client_id>0){
        JsHttpRequest.query($rcapi,{ 'w': 'loadClientAddresses', 'client_id':client_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#client_addresses").html(result.content);
            }}, true);
    }
}

function addClientAddress(client_id) {
    let address=$("#address_new").val();
	JsHttpRequest.query($rcapi,{ 'w': 'addClientAddress', 'client_id':client_id, 'address':address},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			loadClientAddresses(client_id);
		}}, true);
}

function saveClientAddresss(address_id,client_id) {
	let address=$("#address_"+address_id).val();
    JsHttpRequest.query($rcapi,{ 'w':'saveClientAddresss', 'address_id':address_id, 'address':address},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"]==1){
                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                loadClientAddresses(client_id);
            } else { swal("Помилка!", result["error"], "error");}
        }}, true);
}

function dropClientAddresss(address_id,client_id) {
    swal({
            title: "Видалити адресу?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (address_id.length>0){
                    JsHttpRequest.query($rcapi,{ 'w':'dropClientAddresss', 'address_id':address_id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Видалено!", "", "success");
                                loadClientAddresses(client_id);
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function dropClient() {
    let client_id = $("#client_id").val();
    swal({
            title: "Видалити клієнта?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (client_id.length>0) {
                    JsHttpRequest.query($rcapi,{ 'w':'dropClient', 'client_id':client_id },
                        function (result, errors){ if (errors) {alert(errors);} if (result) {
                            if (result["answer"]==1) {
                                swal("Видалено!", result["error"], "success");
                                $("#ClientCard").modal("hide");
                            } else { swal("Помилка!", result["error"], "error"); }
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}
