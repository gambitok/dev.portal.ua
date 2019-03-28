var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

function loadStorageList(){
	$("#storage_range").empty();
	JsHttpRequest.query($rcapi,{ 'w': 'loadStorageList'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#storage_range").html(result["content"]);
	}}, true);
}

function newStorageCard(){
	JsHttpRequest.query($rcapi,{ 'w': 'newStorageCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		var storage_id=result["storage_id"];
		showStorageCard(storage_id);
	}}, true);
}

var barcode_settings = {barWidth: 1,barHeight: 50,moduleSize: 5,showHRI: true,addQuietZone: true,marginHRI: 5,bgColor: "#FFFFFF",color: "#000000",fontSize: 14,output: "css",posX: 0,posY: 0};

function deleteStorage() {
	swal({
		title: "Видалити склад?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var storage_id=$("#storage_id").val();
			if (storage_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'deleteStorage','storage_id':storage_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadStorageList();
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}

function showStorageCard(storage_id){
	if (storage_id<=0 || storage_id==""){toastr["error"](errs[0]);}
	if (storage_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showStorageCard', 'storage_id':storage_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#StorageCard").modal('show');
			document.getElementById("StorageCardBody").innerHTML=result["content"];
			document.getElementById("StorageCardLabel").innerHTML=$("#storage_name").val()+" (ID:"+$("#storage_id").val()+")";
			$('#storage_tabs').tab();
			$("#country_id").select2({placeholder: "Виберіть країну",dropdownParent: $("#StorageCard")});
			$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#StorageCard")});
			$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#StorageCard")});
			$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#StorageCard")});
		}}, true);
	}
}

function loadStateSelectList(){
	var country_id=$("#country_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadStorageStateSelectList', 'country_id':country_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("state_id").innerHTML=result["content"];
		$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#StorageCard")});
	}}, true);
}

function loadRegionSelectList(){
	var state_id=$("#state_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadStorageRegionSelectList', 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("region_id").innerHTML=result["content"];
		$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#StorageCard")});
	}}, true);
}

function loadCitySelectList(){
	var region_id=$("#region_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadStorageCitySelectList', 'region_id':region_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("city_id").innerHTML=result["content"];
		$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#StorageCard")});
	}}, true);
}

function loadStorageDetails(storage_id){
	if (storage_id<=0 || storage_id==""){toastr["error"](errs[0]);}
	if (storage_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadStorageDetails', 'storage_id':storage_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("details_place").innerHTML=result["content"];
			$('#catalogue_tabs').tab();
//			$("#units").select2({placeholder: "Одиниці виміру",dropdownParent: $("#StorageCard")});
		}}, true);
	}
}

function preconfirmStorageDetails(){
	swal({
		title: "Зберегти зміни у розділі \"Реквізити\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			saveStorageDetails();
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveStorageDetails(){
	var storage_id=$("#storage_id").val();
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
	if (storage_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveStorageDetails','storage_id':storage_id,'address_jur':address_jur,'address_fakt':address_fakt,'edrpou':edrpou,'svidotctvo':svidotctvo,'vytjag':vytjag,'vat':vat,'mfo':mfo, 'bank':bank,'account':account,'not_resident':not_resident,'nr_details':nr_details},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			}
			else{ swal("Помилка!", result["error"], "error");}
			
		}}, true);
	}
}

function loadStorageDetails(storage_id){
	if (storage_id<=0 || storage_id==""){toastr["error"](errs[0]);}
	if (storage_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadStorageDetails', 'storage_id':storage_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("details_place").innerHTML=result["content"];
			$('#catalogue_tabs').tab();
		}}, true);
	}
}

function showStorageDetailsForm(storage_id, param_id){
	if (storage_id<=0 || storage_id==""){toastr["error"](errs[0]);}
	if (storage_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showStorageDetailsForm', 'storage_id':storage_id, 'param_id':param_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function dropStorageDetails(storage_id,param_id,param_name){
	swal({
		title: "Видалити тип зберігання на складі\""+param_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (storage_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropStorageDetails','storage_id':storage_id,'param_id':param_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadStorageDetails(storage_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveStorageDetailsForm(storage_id,storage_str_id){
	var param_name=$("#param_type_id option:selected").html();
	swal({
		title: "Застосувати тип зберігання на складі \""+param_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var param_id=$("#param_type_id option:selected").val();
			if (storage_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveStorageDetailsForm','storage_id':storage_id,'storage_str_id':storage_str_id,'param_id':param_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadStorageDetails(storage_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveStorageGeneralInfo(){
	swal({
		title: "Зберегти зміни у розділі \"Загальна інформація\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var storage_id=$("#storage_id").val();
			var name=$("#storage_name").val();
			var full_name=$("#storage_full_name").val();
			var address=$("#address").val();
			var storekeeper=$("#storekeeper").val();
			var country_id=$("#country_id option:selected").val();
			var state_id=$("#state_id option:selected").val();
			var region_id=$("#region_id option:selected").val();
			var city_id=$("#city_id option:selected").val(); 
			var order_by=$("#order_by option:selected").val();
			if (storage_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveStorageGeneralInfo','storage_id':storage_id,'name':name,'full_name':full_name,'address':address,'storekeeper':storekeeper,'country_id':country_id,'state_id':state_id,'region_id':region_id,'city_id':city_id,'order_by':order_by},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						loadStorageList();
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}

function loadStorageCells(storage_id){
	if (storage_id<=0 || storage_id==""){toastr["error"](errs[0]);}
	if (storage_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadStorageCells', 'storage_id':storage_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("cells_place").innerHTML=result["content"];
			$('#catalogue_tabs').tab();
		}}, true);
	}
}

function showStorageCellsForm(storage_id, cells_id){
	if (storage_id<=0 || storage_id==""){toastr["error"](errs[0]);}
	if (storage_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showStorageCellsForm', 'storage_id':storage_id, 'cells_id':cells_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			var elem = document.querySelector('#def_ch');if (elem){ var dflt = new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function saveStorageCellsForm(storage_id,cells_id){
	swal({
		title: "Зберегти комірку?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (storage_id.length>0){
				var str_kol=$("#str_kol").val();
				var def_ch=0;if (document.getElementById("def_ch").checked){def_ch=1;}
				var cell_str_ids=[]; var cell_param_ids=[]; var cell_vls=[];
				for (var i=1;i<=str_kol;i++){
					var cell_str_id=$("#cell_str_id_"+i).val();cell_str_ids[i]=cell_str_id;
					var cell_param_id=$("#cell_param_id_"+i).val();cell_param_ids[i]=cell_param_id;
					var cell_vl=$("#cell_vl_"+i).val();cell_vls[i]=cell_vl;
				}
				JsHttpRequest.query($rcapi,{ 'w':'saveStorageCellsForm','storage_id':storage_id,'cells_id':cells_id,'str_kol':str_kol,'cell_str_ids':cell_str_ids,'cell_param_ids':cell_param_ids,'cell_vls':cell_vls,'def_ch':def_ch},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadStorageCells(storage_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function dropStorageCells(storage_id,cells_id,cell_name){
	swal({
		title:"Видалити  комірку зберігання на складі\""+cell_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (storage_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropStorageCells','storage_id':storage_id,'cells_id':cells_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadStorageCells(storage_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadStorageUsers(storage_id) {
	if (storage_id<=0 || storage_id==""){toastr["error"](errs[0]);}
	if (storage_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadStorageUsers', 'storage_id':storage_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("users_place").innerHTML=result["content"];
			$('#catalogue_tabs').tab();
		}}, true);
	}
} 

function setUserStorage(user_id,storage_id) {
	swal({
		title: "Назначити склад за користувачем?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
				var status=1;
				JsHttpRequest.query($rcapi,{ 'w':'setUserStorage','user_id':user_id,'storage_id':storage_id,'status':status},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						loadStorageUsers(storage_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);	
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function dropUserStorage(user_id,storage_id) {
	swal({
		title: "Видалити користувача зі склада?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
				var status=0;
				JsHttpRequest.query($rcapi,{ 'w':'setUserStorage','user_id':user_id,'storage_id':storage_id,'status':status},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
						loadStorageUsers(storage_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);	
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}