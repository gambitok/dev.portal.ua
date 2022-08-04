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
		showStorageCard(result["storage_id"]);
	}}, true);
}

function deleteStorage() {
	swal({
		title: "Видалити склад?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			let storage_id=$("#storage_id").val();
			if (storage_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'deleteStorage','storage_id':storage_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadStorageList();
					} else { swal("Помилка!", result["error"], "error");}
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
			let storage_card = 	$("#StorageCard");
			storage_card.modal("show");
			$("#StorageCardBody").html(result["content"]);
			$("#StorageCardLabel").html($("#storage_name").val()+" (ID:"+$("#storage_id").val()+")");
			$("#storage_tabs").tab();
			$("#country_id").select2({placeholder: "Виберіть країну",dropdownParent: storage_card});
			$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: storage_card});
			$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: storage_card});
			$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: storage_card});
		}}, true);
	}
}

function loadStateSelectList(){
    let country_id=$("#country_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadStorageStateSelectList', 'country_id':country_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("state_id").innerHTML=result["content"];
		$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#StorageCard")});
	}}, true);
}

function loadRegionSelectList(){
    let state_id=$("#state_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadStorageRegionSelectList', 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("region_id").innerHTML=result["content"];
		$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#StorageCard")});
	}}, true);
}

function loadCitySelectList(){
	let region_id=$("#region_id option:selected").val();
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
			$("#details_place").html(result["content"]);
			$("#catalogue_tabs").tab();
		}}, true);
	}
}

// function preconfirmStorageDetails(){
// 	swal({
// 		title: "Зберегти зміни у розділі \"Реквізити\"?",
// 		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
// 		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
// 	},
// 	function (isConfirm) {
// 		if (isConfirm) {
// 			saveStorageDetails();
// 		} else {
// 			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
// 		}
// 	});
// }

function saveStorageDetails(){
    let storage_id=$("#storage_id").val();
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
	if (storage_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveStorageDetails','storage_id':storage_id,'address_jur':address_jur,'address_fakt':address_fakt,'edrpou':edrpou,'svidotctvo':svidotctvo,'vytjag':vytjag,'vat':vat,'mfo':mfo, 'bank':bank,'account':account,'not_resident':not_resident,'nr_details':nr_details},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
	}
}

function showStorageDetailsForm(storage_id, param_id){
	if (storage_id<=0 || storage_id==""){toastr["error"](errs[0]);}
	if (storage_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showStorageDetailsForm', 'storage_id':storage_id, 'param_id':param_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalBody").html(result["content"]);
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
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveStorageDetailsForm(storage_id,storage_str_id){
    let param_name=$("#param_type_id option:selected").html();
	swal({
		title: "Застосувати тип зберігання на складі \""+param_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let param_id=$("#param_type_id option:selected").val();
			if (storage_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveStorageDetailsForm','storage_id':storage_id,'storage_str_id':storage_str_id,'param_id':param_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadStorageDetails(storage_id);
					} else { swal("Помилка!", result["error"], "error");}
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
			let storage_id=$("#storage_id").val();
            let name=$("#storage_name").val();
            let full_name=$("#storage_full_name").val();
            let address=$("#address").val();
            let storekeeper=$("#storekeeper").val();
            let country_id=$("#country_id option:selected").val();
            let state_id=$("#state_id option:selected").val();
            let region_id=$("#region_id option:selected").val();
            let city_id=$("#city_id option:selected").val();
            let order_by=$("#order_by option:selected").val();
			if (storage_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveStorageGeneralInfo','storage_id':storage_id,'name':name,'full_name':full_name,'address':address,'storekeeper':storekeeper,'country_id':country_id,'state_id':state_id,'region_id':region_id,'city_id':city_id,'order_by':order_by},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                        $("#StorageCard").modal("hide");
						loadStorageList();
					} else { swal("Помилка!", result["error"], "error");}
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
            $("#cells_place").html(result["content"]);
            $("#catalogue_tabs").tab();
		}}, true);
	}
}

function showStorageCellsForm(storage_id, cells_id){
	if (storage_id<=0 || storage_id==""){toastr["error"](errs[0]);}
	if (storage_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showStorageCellsForm', 'storage_id':storage_id, 'cells_id':cells_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalBody").html(result["content"]);
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
				JsHttpRequest.query($rcapi,{ 'w':'saveStorageCellsForm','storage_id':storage_id,'cells_id':cells_id,'str_kol':str_kol,'cell_param_ids':cell_param_ids,'cell_vls':cell_vls,'def_ch':def_ch},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadStorageCells(storage_id);
					} else { swal("Помилка!", result["error"], "error");}
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
				JsHttpRequest.query($rcapi,{ 'w':'dropStorageCells', 'storage_id':storage_id, 'cells_id':cells_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadStorageCells(storage_id);
					} else { swal("Помилка!", result["error"], "error");}
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
			$("#users_place").html(result["content"]);
			$("#catalogue_tabs").tab();
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
				JsHttpRequest.query($rcapi,{ 'w':'setUserStorage', 'user_id':user_id, 'storage_id':storage_id, 'status':1},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						loadStorageUsers(storage_id);
					} else { swal("Помилка!", result["error"], "error");}
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
				let status=0;
				JsHttpRequest.query($rcapi,{ 'w':'setUserStorage','user_id':user_id,'storage_id':storage_id,'status':status},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
						loadStorageUsers(storage_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);	
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadStorageCellsList() {
	let cell_id = $("#storage_cells_select option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w': 'loadStorageCellsList', 'cell_id':cell_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let dt=$("#datatable");
            dt.DataTable().destroy();
            $("#storage_cells_range").html(result.content);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
        }}, true);
}

function loadStorageAllCellList() {
    let storage_id = $("#storage_select option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w': 'loadStorageAllCellList', 'storage_id':storage_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let dt=$("#datatable");
            dt.DataTable().destroy();
            $("#storage_cells_range").html(result.content);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
        }}, true);
}

function loadStorageAllList() {
    let storage_id = $("#storage_select2 option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w': 'loadStorageAllList', 'storage_id':storage_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let dt=$("#datatable");
            dt.DataTable().destroy();
            $("#storage_cells_range").html(result.content);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
        }}, true);
}

function exportStorageCellsList() {
    let cell_id = $("#storage_cells_select option:selected").val();
    let url = "StorageCells/export/"+cell_id+"/";
    window.open(url, '_blank');
}

function exportStorageList() {
    let storage_id = $("#storage_select option:selected").val();
    let url = "StorageCells/export2/"+storage_id+"/";
    window.open(url, '_blank');
}

function exportStorageAllList() {
    let storage_id = $("#storage_select2 option:selected").val();
    let url = "StorageCells/export3/"+storage_id+"/";
    window.open(url, '_blank');
}

var waveSpinner="<div class='sk-spinner sk-spinner-wave'><div class='sk-rect1'></div><div class='sk-rect2'></div><div class='sk-rect3'></div><div class='sk-rect4'></div><div class='sk-rect5'></div></div>";

function getStorageReservDuplicates() {
	let storage_id = $("#storage_list option:selected").val();
	$("#waveSpinnerCat_place").html(waveSpinner);
	JsHttpRequest.query($rcapi,{ 'w': 'getStorageReservDuplicates', 'storage_id':storage_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let dt = $("#datatable7");
			dt.DataTable().destroy();
			$("#storage_reserv").html(result.content);
			$("#waveSpinnerCat_place").html("");
			dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}}, true);
}

function loadStorageCostRange() {
	let storage_id = $("#storage_list option:selected").val();
	let kours_id = $("#kours_list option:selected").val();
	let brand_id = $("#brands_list option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadStorageCostRange', 'storage_id':storage_id, 'kours_id':kours_id, 'brand_id':brand_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let dt = $("#datatable");
			dt.DataTable().destroy();
			$("#storage_range").html(result.content);
			dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}}, true);
}

function exportStorageCostList() {
	let storage_id = $("#storage_list option:selected").val();
	let kours_id = $("#kours_list option:selected").val();
	let brand_id = $("#brands_list option:selected").val();
	let url = "StorageCost/export/" + storage_id + "/" + kours_id + "/" + brand_id + "/";
	window.open(url, '_blank');
}