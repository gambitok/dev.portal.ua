var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

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
            $("#SupplCardBody").html(result["content"]);
            $("#SupplCardLabel").html(result["header"]);
			$('#suppl_tabs').tab();
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
        $("#state_id").html(result.content);
		$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#SupplCard")});
	}}, true);
}

function loadRegionSelectList(){
	var state_id=$("#state_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientRegionSelectList', 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#region_id").html(result.content);
		$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#SupplCard")});
	}}, true);
}

function loadCitySelectList(){
	var region_id=$("#region_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientCitySelectList', 'region_id':region_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#city_id").html(result.content);
		$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#SupplCard")});
	}}, true);
}

function loadSupplVat(suppl_id){
	if (suppl_id<=0 || suppl_id===""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadSupplVat', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#vat_place").html(result["content"]);
            var elem1 = document.querySelector('#price_in_vat');if (elem1){ var price_in_vat = new Switchery(elem1, { color: '#1AB394' });}
			var elem2 = document.querySelector('#show_in_vat');if (elem2){ var show_in_vat = new Switchery(elem2, { color: '#1AB394' });}
			var elem3 = document.querySelector('#price_add_vat');if (elem3){ var price_add_vat = new Switchery(elem3, { color: '#1AB394' });}
			elem1.addEventListener('change', function() {
				if (elem1.checked){
				  //show_in_vat.disable();show_in_vat.setPosition(false);
				  //price_add_vat.disable();price_add_vat.setPosition(false);
				}else{
					//show_in_vat.enable();
					//price_add_vat.enable();
				}
			});
			$("#suppl_tabs").tab();
		}}, true);
	}
}

function saveSupplVat(suppl_id){
	swal({
		title: "Зберегти зміни у розділі \"Умови роботи з ПДВ\"?",text: "Внесені Вами зміни вплинуть на роботу магазину",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var price_in_vat=0; if (document.getElementById("price_in_vat").checked){price_in_vat=1;}
			var show_in_vat=0; if (document.getElementById("show_in_vat").checked && document.getElementById("show_in_vat").disabled==false){show_in_vat=1;}
			var price_add_vat=0; if (document.getElementById("price_add_vat").checked && document.getElementById("price_add_vat").disabled==false){price_add_vat=1;}

			if (suppl_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveSupplVat','suppl_id':suppl_id,'price_in_vat':price_in_vat,'show_in_vat':show_in_vat,'price_add_vat':price_add_vat},
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

function changePriceInVat(){
	if (document.getElementById("price_in_vat").checked){
		//var elem2 = document.querySelector('#show_in_vat');if (elem2){ var show_in_vat = new Switchery(elem2);show_in_vat.disable();}
		//var elem3 = document.querySelector('#price_add_vat');if (elem2){ var price_add_vat = new Switchery(elem3);price_add_vat.disable();}
		
	}else{
		//var elem2 = document.querySelector('#show_in_vat');if (elem2){ var show_in_vat = new Switchery(elem2);show_in_vat.enabled();}
		//var elem3 = document.querySelector('#price_add_vat');if (elem2){ var price_add_vat = new Switchery(elem3);price_add_vat.enabled();}
	}
	return true;
}

function loadSupplPrice(suppl_id){
	if (suppl_id<=0 || suppl_id===""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadSupplPrice', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#price_place").html(result.content);
            $("#suppl_tabs").tab();
		}}, true);
	}
}

function showPriceUploadForm(suppl_id){
	$("#fileSupplCsvUploadForm").modal('show');
	$("#csv_suppl_id").val(suppl_id);
	var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$('#fileSupplCsvUploadForm').modal('hide');
		loadSupplPrice(suppl_id);
	});
}

function finishSupplPriceImport(suppl_id){
	var start_row=parseInt($("#start_row").val());
	var kol_cols=parseInt($("#kol_cols").val());
	var cash=parseInt($("#import_cash option:selected").val());
	var kours_usd=parseFloat($("#import_kours_usd").val().replace(",", "."));
	var kours_eur=parseFloat($("#import_kours_eur").val().replace(",", "."));
	//var cls_kol=3;
	if (start_row<0 || start_row.length<=0){ swal("Помилка!", "Не вказано початковий ряд зчитування", "error");}
	if (start_row>=0){ 
		var cols=[];var cl=0; var cls_sel=0;
		for (var i=1;i<=kol_cols;i++){
			cl=$("#clm-"+i+" option:selected").val();
			if (cl>0 || cl!=""){cls_sel+=1; cols[i]=cl;}
		}
		if (cls_sel<3){swal("Помилка!", "Не вказані усі значення колонок", "error");}
		else{
			$("#waveSpinner_place").html(waveSpinner);
			JsHttpRequest.query($rcapi,{ 'w':'finishSupplPriceImport','suppl_id':suppl_id,'start_row':start_row,'kol_cols':kol_cols,'cash':cash,'kours_usd':kours_usd,'kours_eur':kours_eur,'cols':cols},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Імпорт даних завершено!", "", "success");
					loadSupplPrice(suppl_id);
				}
				else{ swal("Помилка!", result["error"], "error");}
			}}, true);
		}
	}
}

function loadSupplIndex(suppl_id){
	if (suppl_id<=0 || suppl_id===""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadSupplIndex', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#index_place").html(result.content);
			$("#suppl_tabs").tab();
		}}, true);
	}
}

function showIndexUploadForm(suppl_id){
	$("#fileSupplIndexUploadForm").modal('show');
	$("#csv_index_suppl_id").val(suppl_id);
	var myDropzone4 = new Dropzone("#myDropzone4",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone4.removeAllFiles(true);
	myDropzone4.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$('#fileSupplIndexUploadForm').modal('hide');
		loadSupplIndex(suppl_id);
	});
}

function finishSupplIndexImport(suppl_id){
	var start_row=parseInt($("#start_row").val());
	var kol_cols=parseInt($("#kol_cols").val());
	//var cls_kol=3;
	if (start_row<0 || start_row.length<=0){ swal("Помилка!", "Не вказано початковий ряд зчитування", "error");}
	if (start_row>=0){ 
		var cols=[];var cl=0; var cls_sel=0;
		for (var i=1;i<=kol_cols;i++){
			cl=$("#clm-"+i+" option:selected").val();
			if (cl>0 || cl!=""){cls_sel+=1; cols[i]=cl;}
		}
		if (cls_sel<3){swal("Помилка!", "Не вказані усі значення колонок", "error");}
		else{
			$("#waveSpinner_place").html(waveSpinner);
			JsHttpRequest.query($rcapi,{ 'w':'finishSupplIndexImport','suppl_id':suppl_id,'start_row':start_row,'kol_cols':kol_cols,'cols':cols},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Імпорт даних завершено!", "", "success");
					loadSupplIndex(suppl_id);
				}
				else{ swal("Помилка!", result["error"], "error");}
			}}, true);
		}
	}
}

function loadSupplOrderInfo(suppl_id){
	if (suppl_id<=0 || suppl_id===""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadSupplOrderInfo', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#info_suppl_place").html(result.content);
            $('#suppl_tabs').tab();
			$("#suppl_text_info").markdown({autofocus:false,savable:false})
			initSample();
		}}, true);
	}
}

function saveSupplOrderInfo(suppl_id){
	swal({
		title: "Зберегти зміни у розділі \"Алгоритм роботи\"?",text: "Внесені Вами зміни вподальшому вплинуть на роботу магазину і ваше життя :)",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			//var info=$("#suppl_text_info").val();
			var info = CKEDITOR.instances.editor.getData();
			if (suppl_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveSupplOrderInfo','suppl_id':suppl_id,'info':info},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені. Амінь", "success");
					}
					else{ swal("Помилка!", result["error"], "error");}
					
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadSupplCoopList() {
	JsHttpRequest.query($rcapi,{ 'w': 'loadSupplCoopList'}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$('#datatable').DataTable().destroy();
			$("#suppl_range").html(result["content"]);
			$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}}, true);
}

function showSupplCoopCard(suppl_id){
	if (suppl_id<=0 || suppl_id==""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showSupplCoopCard', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#SupplCard").modal('show');
            $("#SupplCardBody").html(result["content"]);
            $("#SupplCardLabel").html(result["header"]);
			$("#country_id").select2({placeholder: "Виберіть країну",dropdownParent: $("#SupplCard")});
			$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#SupplCard")});
			$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#SupplCard")});
			$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#SupplCard")});
			$('#suppl_tabs').tab();
		}}, true);
	}
}

function saveSupplCoop(suppl_id) {
	swal({
		title: "Зберегти зміни у розділі \"Запити на співпрацю\"?",text: "",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (suppl_id.length>0){
				var company=$("#suppl_company").val();
				var name=$("#suppl_name").val();
				var phone=$("#suppl_phone").val();
				var email=$("#suppl_email").val();
				var city_id=$("#city_id option:selected").val(); 
				var comment=$("#suppl_comment").val();
				var status=$("#suppl_status option:selected").val();

				JsHttpRequest.query($rcapi,{ 'w':'saveSupplCoop','suppl_id':suppl_id,'company':company,'name':name,'phone':phone,'email':email,'city_id':city_id,'comment':comment,'status':status},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#SupplCard").modal("hide");
						loadSupplCoopList();
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}
