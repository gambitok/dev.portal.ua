var errs = [];
errs[0] = "Помилка індексу";
errs[1] = "Занадто короткий запит для пошуку";

function loadSupplList() {
	JsHttpRequest.query($rcapi,{ 'w': 'showSupplList'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#suppl_range").empty();
		$("#suppl_range").html(result["content"]);
	}}, true);
}

// var barcode_settings = {barWidth: 1,barHeight: 50,moduleSize: 5,showHRI: true,addQuietZone: true,marginHRI: 5,bgColor: "#FFFFFF",color: "#000000",fontSize: 14,output: "css",posX: 0,posY: 0};

function showSupplCard(suppl_id) {
	if (suppl_id<=0 || suppl_id=="") {
		toastr["error"](errs[0]);
	}
	if (suppl_id>0) {
		JsHttpRequest.query($rcapi,{ 'w': 'showSupplCard', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#SupplCard").modal("show");
            $("#SupplCardBody").html(result["content"]);
            $("#SupplCardLabel").html(result["header"]);
			$("#suppl_tabs").tab();
		}}, true);
	}
}

function saveSupplGeneralInfo() {
	swal({
		title: "Зберегти зміни у розділі \"Загальна інформація\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let suppl_id = $("#suppl_id").val();
            let name = $("#suppl_name").val();
            let full_name = $("#suppl_full_name").val();
            let address = $("#address").val();
            let chief = $("#chief option:selected").val();
            let country_id = $("#country_id option:selected").val();
            let state_id = $("#state_id option:selected").val();
            let region_id = $("#region_id option:selected").val();
            let city_id = $("#city_id option:selected").val();
			if (suppl_id.length > 0) {
				JsHttpRequest.query($rcapi,{'w':'saveSupplGeneralInfo','suppl_id':suppl_id,'name':name,'full_name':full_name,'address':address,'chief':chief,'country_id':country_id,'state_id':state_id,'region_id':region_id,'city_id':city_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"] == 1) {
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						loadSupplList();
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

function loadStateSelectList(){
    let country_id = $("#country_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientStateSelectList', 'country_id':country_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#state_id").html(result.content);
		$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: $("#SupplCard")});
	}}, true);
}

function loadRegionSelectList(){
    let state_id = $("#state_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientRegionSelectList', 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#region_id").html(result.content);
		$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#SupplCard")});
	}}, true);
}

function loadCitySelectList() {
    let region_id = $("#region_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadClientCitySelectList', 'region_id':region_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#city_id").html(result.content);
		$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#SupplCard")});
	}}, true);
}

function loadSupplVat(suppl_id) {
	if (suppl_id <= 0 || suppl_id === "") {
		toastr["error"](errs[0]);
	}
	if (suppl_id > 0) {
		JsHttpRequest.query($rcapi,{ 'w': 'loadSupplVat', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#vat_place").html(result["content"]);
            let elem1 = document.querySelector("#price_in_vat");if (elem1){ var price_in_vat = new Switchery(elem1, { color: '#1AB394' });}
            let elem2 = document.querySelector("#show_in_vat");if (elem2){ var show_in_vat = new Switchery(elem2, { color: '#1AB394' });}
            let elem3 = document.querySelector("#price_add_vat");if (elem3){ var price_add_vat = new Switchery(elem3, { color: '#1AB394' });}
			elem1.addEventListener('change', function() {
				if (elem1.checked) {
				  //show_in_vat.disable();show_in_vat.setPosition(false);
				  //price_add_vat.disable();price_add_vat.setPosition(false);
				} else {
					//show_in_vat.enable();
					//price_add_vat.enable();
				}
			});
			$("#suppl_tabs").tab();
		}}, true);
	}
}

function saveSupplVat(suppl_id) {
	swal({
		title: "Зберегти зміни у розділі \"Умови роботи з ПДВ\"?",text: "Внесені Вами зміни вплинуть на роботу магазину",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let price_in_vat = 0;
            if (document.getElementById("price_in_vat").checked) {price_in_vat=1;}
            let show_in_vat = 0;
            if (document.getElementById("show_in_vat").checked && document.getElementById("show_in_vat").disabled==false) {show_in_vat=1;}
            let price_add_vat = 0;
            if (document.getElementById("price_add_vat").checked && document.getElementById("price_add_vat").disabled==false) {price_add_vat=1;}
            let return_delay = $("#return_delay").val();
			if (suppl_id.length > 0) {
				JsHttpRequest.query($rcapi,{ 'w':'saveSupplVat', 'suppl_id':suppl_id, 'price_in_vat':price_in_vat, 'show_in_vat':show_in_vat, 'price_add_vat':price_add_vat, 'return_delay':return_delay},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"] == 1) {
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
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

function changePriceInVat(){
	if (document.getElementById("price_in_vat").checked){
		//var elem2 = document.querySelector('#show_in_vat');if (elem2){ var show_in_vat = new Switchery(elem2);show_in_vat.disable();}
		//var elem3 = document.querySelector('#price_add_vat');if (elem2){ var price_add_vat = new Switchery(elem3);price_add_vat.disable();}
		
	} else {
		//var elem2 = document.querySelector('#show_in_vat');if (elem2){ var show_in_vat = new Switchery(elem2);show_in_vat.enabled();}
		//var elem3 = document.querySelector('#price_add_vat');if (elem2){ var price_add_vat = new Switchery(elem3);price_add_vat.enabled();}
	}
	return true;
}

function loadSupplPrice(suppl_id) {
	if (suppl_id <= 0 || suppl_id === "") {
		toastr["error"](errs[0]);
	}
	if (suppl_id > 0) {
		JsHttpRequest.query($rcapi,{ 'w': 'loadSupplPrice', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#price_place").html(result.content);
            $("#suppl_tabs").tab();
		}}, true);
	}
}

// PREFIXU

function loadSupplPricePrefix(suppl_id) {
    if (suppl_id <= 0 || suppl_id === "") {
    	toastr["error"](errs[0]);
    }
    if (suppl_id > 0) {
        JsHttpRequest.query($rcapi,{ 'w': 'loadSupplPricePrefix', 'suppl_id':suppl_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#prefix_suppl_place").html(result.content);
                $("#suppl_tabs").tab();
            }}, true);
    }
}

function showSupplPricePrefix(prefix_id=0) {
    JsHttpRequest.query($rcapi,{ 'w': 'showSupplPricePrefix', 'prefix_id':prefix_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#FormModalWindow").modal("show");
			$("#FormModalBody").html(result.content);
			$("#FormModalLabel").html("Редагування префіксів");
            $("#brand_select").select2();
		}}, true);
}

function saveSupplPricePrefix() {
	let prefix_id=$("#prefix_id").val();
	let suppl_id=$("#suppl_id").val();
	let suppl_brand=$("#suppl_brand").val();
	let brand_id=$("#brand_select option:selected").val();
	let prefix=$("#prefix").val();
	let return_delay=$("#return_delay").val();
	let warranty_info=$("#warranty_info").val();
    swal({
            title: "Зберегти префікс?",text: "",
            type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (prefix_id.length > 0) {
                    JsHttpRequest.query($rcapi,{ 'w':'saveSupplPricePrefix', 'prefix_id':prefix_id, 'suppl_id':suppl_id, 'suppl_brand':suppl_brand, 'brand_id':brand_id, 'prefix':prefix, 'return_delay':return_delay, 'warranty_info':warranty_info},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"] == 1) {
                                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#FormModalWindow").modal("hide");
                                loadSupplPricePrefix(suppl_id);
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

function dropSupplPricePrefix() {
    let prefix_id = $("#prefix_id").val();
    swal({
            title: "Видалити префікс?",text: "",
            type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, видалити!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (prefix_id.length > 0) {
                    JsHttpRequest.query($rcapi,{ 'w':'dropSupplPricePrefix', 'prefix_id':prefix_id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"] == 1) {
                                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#FormModalWindow").modal("hide");
                                let suppl_id = $("#suppl_id").val();
                                loadSupplPricePrefix(suppl_id);
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

function showPriceUploadForm(suppl_id) {
    $("#fileSupplCsvUploadForm").modal("show");
    $("#csv_suppl_id").val(suppl_id);
    var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
    myDropzone3.removeAllFiles(true);
    myDropzone3.on("queuecomplete", function() {
        toastr["info"]("Завантаження файлів завершено.");
        this.removeAllFiles();
        $("#fileSupplCsvUploadForm").modal("hide");
        loadSupplPrice(suppl_id);
    });
}

function finishSupplPriceImport(suppl_id) {
    let start_row=parseInt($("#start_row").val());
    let kol_cols=parseInt($("#kol_cols").val());
    let cash=parseInt($("#import_cash option:selected").val());
    let kours_usd=parseFloat($("#import_kours_usd").val().replace(",", "."));
    let kours_eur=parseFloat($("#import_kours_eur").val().replace(",", "."));
	if (start_row < 0 || start_row.length <= 0) {
		swal("Помилка!", "Не вказано початковий ряд зчитування", "error");
	}
	if (start_row >= 0) {
		var cols=[];var cl=0; var cls_sel=0;
		for (var i=1;i<=kol_cols;i++){
			cl=$("#clm-"+i+" option:selected").val();
			if (cl>0 || cl!=""){cls_sel+=1; cols[i]=cl;}
		}
		if (cls_sel<3) {
			swal("Помилка!", "Не вказані усі значення колонок", "error");
		} else {
			$("#waveSpinner_place").html(waveSpinner);
			JsHttpRequest.query($rcapi,{ 'w':'finishSupplPriceImport', 'suppl_id':suppl_id, 'start_row':start_row, 'kol_cols':kol_cols, 'cash':cash, 'kours_usd':kours_usd, 'kours_eur':kours_eur, 'cols':cols},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"] == 1) {
					swal("Імпорт даних завершено!", "", "success");
					loadSupplPrice(suppl_id);
				} else {
					swal("Помилка!", result["error"], "error");
				}
			}}, true);
		}
	}
}

function loadSupplIndex(suppl_id) {
	if (suppl_id<=0 || suppl_id===""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadSupplIndex', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#index_place").html(result.content);
			$("#suppl_tabs").tab();
		}}, true);
	}
}

function showIndexUploadForm(suppl_id) {
	$("#fileSupplIndexUploadForm").modal("show");
	$("#csv_index_suppl_id").val(suppl_id);
	var myDropzone4 = new Dropzone("#myDropzone4",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone4.removeAllFiles(true);
	myDropzone4.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$("#fileSupplIndexUploadForm").modal("hide");
		loadSupplIndex(suppl_id);
	});
}

function finishSupplIndexImport(suppl_id) {
	let start_row=parseInt($("#start_row").val());
    let kol_cols=parseInt($("#kol_cols").val());
	if (start_row < 0 || start_row.length <= 0) {
		swal("Помилка!", "Не вказано початковий ряд зчитування", "error");
	}
	if (start_row >= 0) {
		var cols=[]; var cl=0; var cls_sel=0;
		for (var i=1;i<=kol_cols;i++) {
			cl=$("#clm-"+i+" option:selected").val();
			if (cl>0 || cl!="") {cls_sel+=1; cols[i]=cl;}
		}
		if (cls_sel<3) {
			swal("Помилка!", "Не вказані усі значення колонок", "error");
		} else {
			$("#waveSpinner_place").html(waveSpinner);
			JsHttpRequest.query($rcapi,{ 'w':'finishSupplIndexImport', 'suppl_id':suppl_id, 'start_row':start_row, 'kol_cols':kol_cols, 'cols':cols},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Імпорт даних завершено!", "", "success");
					loadSupplIndex(suppl_id);
				} else {
					swal("Помилка!", result["error"], "error");
				}
			}}, true);
		}
	}
}

function loadSupplOrderInfo(suppl_id) {
	if (suppl_id<=0 || suppl_id===""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadSupplOrderInfo', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#info_suppl_place").html(result.content);
            $("#suppl_tabs").tab();
			$("#suppl_text_info").markdown({autofocus:false,savable:false})
			initSample();
		}}, true);
	}
}

function saveSupplOrderInfo(suppl_id) {
	swal({
		title: "Зберегти зміни у розділі \"Алгоритм роботи\"?",text: "Внесені Вами зміни вподальшому вплинуть на роботу магазину і ваше життя :)",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let info = CKEDITOR.instances.editor.getData();
			if (suppl_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveSupplOrderInfo', 'suppl_id':suppl_id, 'info':info},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені. Амінь", "success");
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

function loadSupplCoopList() {
	JsHttpRequest.query($rcapi,{ 'w': 'loadSupplCoopList'}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let dt=$("#datatable");
			dt.DataTable().destroy();
			$("#suppl_range").html(result["content"]);
			dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}}, true);
}

function showSupplCoopCard(suppl_id) {
	if (suppl_id<=0 || suppl_id==""){toastr["error"](errs[0]);}
	if (suppl_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showSupplCoopCard', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			let suppl_card = $("#SupplCard");
			suppl_card.modal("show");
            $("#SupplCardBody").html(result["content"]);
            $("#SupplCardLabel").html(result["header"]);
			$("#country_id").select2({placeholder: "Виберіть країну",dropdownParent: suppl_card});
			$("#state_id").select2({placeholder: "Виберіть область",dropdownParent: suppl_card});
			$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: suppl_card});
			$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: suppl_card});
			$("#suppl_tabs").tab();
		}}, true);
	}
}

function saveSupplCoop(suppl_id) {
	swal({
		title: "Зберегти зміни у розділі \"Запити на співпрацю\"?", text: "",
		type: "warning", allowOutsideClick:true,	allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (suppl_id.length>0){
				let company=$("#suppl_company").val();
                let name=$("#suppl_name").val();
                let phone=$("#suppl_phone").val();
                let email=$("#suppl_email").val();
                let city_id=$("#city_id option:selected").val();
                let comment=$("#suppl_comment").val();
                let status=$("#suppl_status option:selected").val();
				JsHttpRequest.query($rcapi,{ 'w':'saveSupplCoop', 'suppl_id':suppl_id, 'company':company, 'name':name, 'phone':phone, 'email':email, 'city_id':city_id, 'comment':comment, 'status':status},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1) {
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#SupplCard").modal("hide");
						loadSupplCoopList();
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

/*
* SUPPL IMPORT CARD
* */

function showSupplImportCard(suppl_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showSupplImportCard', 'suppl_id':suppl_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#SupplCard").modal("show");
			$("#SupplCardBody").html(result.content);
			let sign = "";
            suppl_id==="0" ? sign="Новий постачальник" : sign="Карта постачальника `"+$("#client_list option:selected").text()+"`";
			$("#SupplCardLabel").html(sign);
			$("#suppl_tabs").tab();
		}}, true);
}

function saveSupplImportGeneralInfo() {
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let suppl_id = $("#suppl_id").val();
                let email = $("#suppl_email").val();
                let start_row = $("#start_row").val();
                let client_id = $("#client_list option:selected").val();
                let file_format = $("#file_format_list option:selected").val();
                let cash_id = $("#cash_list option:selected").val();
                let delimiter = $("#delimiter").val();
                let column_article = $("#column_article").val();
                let column_brand = $("#column_brand").val();
                let column_price = $("#column_price").val();
                JsHttpRequest.query($rcapi,{'w':'saveSupplImportGeneralInfo', 'suppl_id':suppl_id, 'client_id':client_id, 'email':email, 'file_format':file_format, 'cash_id':cash_id, 'start_row':start_row, 'delimiter':delimiter, 'column_article':column_article, 'column_brand':column_brand, 'column_price':column_price},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"] == 1) {
							swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						} else {
							swal("Помилка!", result["error"], "error");
						}
					}}, true);

            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function previewSupplImport() {
    let cash_id = $("#cash_list option:selected").val();
    let start_row = $("#start_row").val();
    let column_article = $("#column_article").val();
    let column_brand = $("#column_brand").val();
    let column_price = $("#column_price").val();
    JsHttpRequest.query($rcapi,{ 'w': 'previewSupplImport', 'cash_id':cash_id, 'start_row':start_row, 'column_article':column_article, 'column_brand':column_brand, 'column_price':column_price},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let dt = $("#preview_table");
            dt.html(result.content);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
        }}, true);
}

/*=========UNKNOWN NUMBERS=========*/

function showNumbersList() {
    $("#unknown_brand").html("");
    $("#unknown_brand_id").html("");
    let suppl_id = $("#select_numbers option:selected").val();
    if (suppl_id === "0") {
    	toastr["error"]("Виберіть постачальника");
    }
    if (suppl_id !== "0") {
		$("#unknown_numbers_range").html('<div class="sk-spinner sk-spinner-wave"><div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div></div>');
		JsHttpRequest.query($rcapi,{ 'w': 'showNumbersList', 'suppl_id':suppl_id},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				let dt = $("#datatable");
				dt.DataTable().destroy();
				$("#unknown_numbers_range").html(result["content"]);
				$("#unknown_brand").html(result["select"]);
				$("#unknown_brand").select2();
				dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
			}}, true);
    }
}

function showNumbersBrandList() {
    $("#unknown_brand").html("");
    $("#unknown_brand_id").html("");
    let suppl_id = $("#select_numbers option:selected").val();
    if (suppl_id === "0") {
    	toastr["error"]("Виберіть постачальника");
    }
    if (suppl_id !== "0") {
        JsHttpRequest.query($rcapi,{ 'w': 'showNumbersBrandList', 'suppl_id':suppl_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#unknown_brand").html(result["content"]);
                $("#unknown_brand").select2();
            }}, true);
    }
}

/*
* Показати артикули
* */
function showArticlesNumbersList() {
    let suppl_id = $("#select_numbers option:selected").val();
    let suppl_brand = $("#unknown_brand option:selected").val();
    let brand_id = $("#unknown_brand_id option:selected").val();
    let prefix = $("#unknown_numbers_prefix").val();
    let limit = $("#unknown_numbers_limit").val();

    if (suppl_id === "0" || suppl_id === undefined) {toastr["error"]("Виберіть постачальника");}
    if (suppl_brand === "0" || suppl_brand === undefined) {toastr["error"]("Виберіть бренд постачальника");}
    if (brand_id === "0" || brand_id === undefined) {toastr["error"]("Виберіть бренд з каталогу");}

    if ((suppl_id !== "0" && suppl_id !== undefined) && (suppl_brand !== "0" && suppl_brand !== undefined) && (brand_id !== "0" && brand_id !== undefined)) {
		$("#unknown_numbers_range").html('<div class="sk-spinner sk-spinner-wave"><div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div></div>');
		JsHttpRequest.query($rcapi,{ 'w': 'showArticlesNumbersList', 'suppl_id':suppl_id, 'suppl_brand':suppl_brand, 'brand_id':brand_id, 'prefix':prefix, 'limit':limit},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				let dt = $("#datatable");
				dt.DataTable().destroy();
				$("#unknown_numbers_range").html(result["content"]);
				dt.DataTable({keys: true,"aaSorting": [[1,"desc"]],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
			}}, true);
    }
}

function showUnknownBrandIds() {
    let suppl_id = $("#select_numbers option:selected").val();
    let brand = $("#unknown_brand option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w': 'showUnknownBrandIds', 'suppl_id':suppl_id, 'brand':brand},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#unknown_brand_id").html(result["content"]);
            $("#unknown_brand_id").select2();
            showUnknownBrandPrefix();
        }}, true);
}

function showAllBrandIds() {
    JsHttpRequest.query($rcapi,{ 'w': 'showAllBrandIds'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#unknown_brand_id").html(result["content"]);
            $("#unknown_brand_id").select2();
        }}, true);
}

function saveSupplPrefix() {
    let suppl_id = $("#select_numbers option:selected").val();
    let suppl_brand = $("#unknown_brand option:selected").val();
    let brand_id = $("#unknown_brand_id option:selected").val();
    let prefix = $("#unknown_numbers_prefix").val();
    let return_delay = $("#return_delay").val();
    let warranty_info = $("#warranty_info").val();

    if (suppl_id === "0" || suppl_id === undefined) {toastr["error"]("Виберіть постачальника");}
    if (suppl_brand === "0" || suppl_brand === undefined) {toastr["error"]("Виберіть бренд постачальника");}
    if (brand_id === "0" || brand_id === undefined) {toastr["error"]("Виберіть бренд з каталогу");}

    if ((suppl_id !== "0" && suppl_id !== undefined) && (suppl_brand !== "0" && suppl_brand !== undefined) && (brand_id !== "0" && brand_id !== undefined)) {
        swal({
                title: "Зберегти префікс '" + prefix + "' для бренда " + suppl_brand + "?", text: "",
                type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
                confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    JsHttpRequest.query($rcapi,{ 'w':'saveSupplPrefix', 'suppl_id':suppl_id, 'suppl_brand':suppl_brand, 'brand_id':brand_id, 'prefix':prefix, 'return_delay':return_delay, 'warranty_info':warranty_info},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"] == 1) {
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                            } else {
                            	swal("Помилка!", result["error"], "error");
                            }
                        }}, true);
                } else {
                    swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
                }
            });
    }
}

function showUnknownBrandPrefix() {
    let suppl_id = $("#select_numbers option:selected").val();
    let suppl_brand = $("#unknown_brand option:selected").val();
    let brand_id = $("#unknown_brand_id option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w':'showUnknownBrandPrefix', 'suppl_id':suppl_id, 'suppl_brand':suppl_brand, 'brand_id':brand_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#unknown_numbers_prefix").val(result.prefix);
            $("#return_delay").val(result.return_delay);
            $("#warranty_info").val(result.warranty_info);
        }}, true);
}

function saveArticlesNumbersList() {
    let suppl_id = $("#select_numbers option:selected").val();
    let suppl_brand = $("#unknown_brand option:selected").val();
    let brand_id = $("#unknown_brand_id option:selected").val();
    let return_delay = $("#return_delay").val();
    let warranty_info = $("#warranty_info").val();
    let prefix = $("#unknown_numbers_prefix").val();
    let limit = $("#unknown_numbers_limit").val();

    if (suppl_id === "0" || suppl_id === undefined) {toastr["error"]("Виберіть постачальника");}
    if (suppl_brand === "0" || suppl_brand === undefined) {toastr["error"]("Виберіть бренд постачальника");}
    if (brand_id === "0" || brand_id === undefined) {toastr["error"]("Виберіть бренд з каталогу");}

    if ((suppl_id !== "0" && suppl_id !== undefined) && (suppl_brand !== "0" && suppl_brand !== undefined) && (brand_id !== "0" && brand_id !== undefined)) {
		swal({
				title: "Заповнити \"Невідомі номера\" бренда " + suppl_brand + "?",
				text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
				confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
			},
			function (isConfirm) {
				if (isConfirm) {
					JsHttpRequest.query($rcapi,{ 'w': 'saveArticlesNumbersList', 'suppl_id':suppl_id, 'suppl_brand':suppl_brand, 'return_delay':return_delay, 'warranty_info':warranty_info, 'prefix':prefix, 'limit':limit},
						function (result, errors){ if (errors) {alert(errors);} if (result){
							if (result["answer"] == 1) {
								swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
							} else {
								swal("Помилка!", result["error"], "error");
							}
						}}, true);
				} else {
					swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
				}
			});
    }
}

function showArticlesUnknown(suppl_id, suppl_index, suppl_brand, prefix) {
    JsHttpRequest.query($rcapi,{ 'w': 'showArticlesUnknown', 'suppl_id':suppl_id, 'suppl_index':suppl_index, 'suppl_brand':suppl_brand, 'prefix':prefix},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result.content);
            $("#FormModalLabel").html(result.header);
        }}, true);
}

function saveArticlesUnknown(suppl_id, suppl_index, suppl_brand, art_id) {
    let return_delay = $("#return_delay").val();
    let warranty_info = $("#warranty_info").val();
	swal({
			title: "Заповнити \"Невідомі номера\" бренда "+suppl_brand+"?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w': 'saveArticlesUnknown', 'suppl_id':suppl_id, 'suppl_index':suppl_index, 'suppl_brand':suppl_brand, 'art_id':art_id, 'return_delay':return_delay, 'warranty_info':warranty_info},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"]==1) {
							swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                            $("#FormModalWindow").modal("hide");
                        } else {
							swal("Помилка!", result["error"], "error");
						}
					}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
}

/*BACK SUPPL*/

function autoSetNumbersList() {
	let suppl_id = $("#select_numbers option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'autoSetNumbersList', 'suppl_id':suppl_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
				console.log(result["numbers_list"]);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

function exportNumbersBrandList() {
	let suppl_id = $("#select_numbers option:selected").val();
	let suppl_brand = $("#unknown_brand option:selected").val();
	if (suppl_id === "0" || suppl_id === undefined) {
		toastr["error"]("Виберіть постачальника");
	}
	if ((suppl_id !== "0" && suppl_id !== undefined)) {
		let url = "/UnknownNumbers/download-brands/" + suppl_id + "/" + suppl_brand;
		window.open(url, '_blank');
	}
}

function changeSupplSelect() {
	let suppl_id = $("#select_numbers option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'changeSupplSelect', 'suppl_id':suppl_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#return_delay").val(result["return_delay"]);
		}}, true);
}

/*
* Back Suppl
* */

function loadBackSupplList() {
	JsHttpRequest.query($rcapi,{ 'w': 'loadBackSupplList'},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let dt = $("#datatable");
			dt.DataTable().destroy();
			$("#back_suppl_range").html(result["content"]);
			dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}}, true);
}

function showBackSupplCard(back_suppl_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showBackSupplCard', 'back_suppl_id':back_suppl_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#BackSupplCard").modal("show");
			$("#BackSupplCardBody").html(result["content"]);
			$("#BackSupplCardLabel").html(back_suppl_id);
			$("#back_suppl_tabs").tab();
		}}, true);
}

function saveBackSuppl(back_suppl_id) {
	swal({
			title: "Зберегти зміни у розділі \"Загальна інформація\"?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
					let client_id = $("#client_id").val();
					let client_seller = $("#client_seller").val();
					let income_id = $("#income_id").val();
					let storage_id = $("#storage_id option:selected").val();
					JsHttpRequest.query($rcapi,{'w':'saveBackSuppl', 'back_suppl_id':back_suppl_id, 'client_id':client_id, 'client_seller':client_seller, 'income_id':income_id, 'storage_id':storage_id},
						function (result, errors){ if (errors) {alert(errors);} if (result){
							if (result["answer"] == 1) {
								swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
								loadBackSupplList();
								showBackSupplCard(result["back_suppl_id"]);
							} else {
								swal("Помилка!", result["error"], "error");
							}
						}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
}

// ADD ROW

function addBackSupplStr(back_suppl_id) {
	let income_id = $("#income_id").val();
	let client_id = $("#client_id").val();
	let client_seller = $("#client_seller").val();
	JsHttpRequest.query($rcapi,{ 'w': 'addBackSupplStr', 'back_suppl_id':back_suppl_id, 'income_id':income_id, 'client_id':client_id, 'client_seller':client_seller},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#BackSupplStrCard").modal("show");
			$("#BackSupplStrCardBody").html(result["content"]);
		}}, true);
}

function addBackSupplStrAmount(back_suppl_id, income_id, art_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'addBackSupplStrAmount', 'back_suppl_id':back_suppl_id, 'income_id':income_id, 'art_id':art_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#BackSupplStrAmountCard").modal("show");
			$("#BackSupplStrAmountCardBody").html(result["content"]);
		}}, true);
}

function saveBackSupplStr(back_suppl_id, income_id, art_id) {
	let amount = $("#input_amount").val();
	$("#saveBackSupplStr").attr("disabled", true);
	JsHttpRequest.query($rcapi,{'w':'saveBackSupplStr', 'back_suppl_id':back_suppl_id, 'income_id':income_id, 'art_id':art_id, 'amount':amount},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				$("#BackSupplStrAmountCard").modal("hide");
				$("#BackSupplStrCard").modal("hide");
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
				showBackSupplCard(back_suppl_id);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

function clearBackSupplStr(back_suppl_id) {
	if (back_suppl_id <= 0 || back_suppl_id === "") {
		toastr["error"](errs[0]);
	}
	if (back_suppl_id > 0) {
		swal({
				title: "Очистити структуру документу?",
				text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
				confirmButtonText: "Очистити", cancelButtonText: "Відміна", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
			},
			function (isConfirm) {
				if (isConfirm) {
					JsHttpRequest.query($rcapi,{ 'w': 'clearBackSupplStr', 'back_suppl_id':back_suppl_id},
						function (result, errors){ if (errors) {alert(errors);} if (result){
							if (result["answer"] == 1) {
								swal("Успішно!", "Структуру накладної очищено!", "success");
								showBackSupplCard(back_suppl_id);
							} else {
								swal("Помилка!", result["error"], "error");
							}
						}}, true);
				} else {
					swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
				}
			});
	}
}

function dropBackSupplStr(back_suppl_id, back_suppl_str_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'dropBackSupplStr', 'back_suppl_id':back_suppl_id, 'back_suppl_str_id':back_suppl_str_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			showBackSupplCard(back_suppl_id);
		}}, true);
}

// clients & sellers

function setBackSupplClient(back_suppl_id, client_id) {
	//if (back_suppl_id > 0) {
		JsHttpRequest.query($rcapi,{ 'w': 'setBackSupplClient', 'back_suppl_id':back_suppl_id, 'client_id':client_id},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				if (result["answer"] == 1) {
					$("#FormModalWindow").modal("hide");
					$("#FormModalBody").html("");
					$("#FormModalLabel").html("");
					if (back_suppl_id > 0) {
						showBackSupplCard(back_suppl_id);
					} else {
						$("#client_id").val(client_id);
						$("#client_name").val(result["client_name"]);
					}
				} else {
					toastr["error"](result["error"]);
				}
			}}, true);
	//}
}

function showBackSupplClientList(back_suppl_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showBackSupplClientList', 'back_suppl_id':back_suppl_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#FormModalWindow").modal("show");
			$("#FormModalBody").html(result["content"]);
			$("#FormModalLabel").html("Контрагенти");
			setTimeout(function() {
				$("#datatable_parrent").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
			}, 500);
		}}, true);
}

function unlinkBackSupplClient(back_suppl_id) {
	swal({
			title: "Відв`язати клієнта від накладної?",text: "Внесені Вами зміни вплинуть на роботу Контрагента",
			type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
			confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w': 'unlinkBackSupplClient', 'back_suppl_id':back_suppl_id},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"] == 1) {
							$("#client_id").val("0");
							$("#client_name").val("");
							swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
						} else {
							toastr["error"](result["error"]);
						}
					}}, true);
			} else {
				swal("Відмінено", "Операцію анульовано.", "error");
			}
		});
}

function setBackSupplClientSeller(back_suppl_id, client_seller) {
	// if (back_suppl_id > 0) {
		JsHttpRequest.query($rcapi,{ 'w': 'setBackSupplClientSeller', 'back_suppl_id':back_suppl_id, 'client_seller':client_seller},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				if (result["answer"] == 1) {
					$("#FormModalWindow").modal("hide");
					$("#FormModalBody").html("");
					$("#FormModalLabel").html("");
					if (back_suppl_id > 0) {
						showBackSupplCard(back_suppl_id);
					} else {
						$("#client_seller").val(client_seller);
						$("#client_seller_name").val(result["client_seller_name"]);
					}
				} else {
					toastr["error"](result["error"]);
				}
			}}, true);
	// }
}

function showBackSupplClientSellerList(back_suppl_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showBackSupplClientSellerList', 'back_suppl_id':back_suppl_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#FormModalWindow").modal("show");
			$("#FormModalBody").html(result["content"]);
			$("#FormModalLabel").html("Продавець");
			setTimeout(function() {
				$("#datatable_parrent").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
			}, 500);
		}}, true);
}

function unlinkBackSupplClientSeller(back_suppl_id) {
	swal({
			title: "Відвязати контагента від накладної?",text: "Внесені Вами зміни вплинуть на роботу Контрагента",
			type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
			confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w': 'unlinkBackSupplClientSeller', 'back_suppl_id':back_suppl_id},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"] == 1) {
							$("#client_seller").val("0");
							$("#client_seller_name").val("");
							swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
						} else {
							toastr["error"](result["error"]);
						}
					}}, true);
			} else {
				swal("Відмінено", "Операцію анульовано.", "error");
			}
		});
}

function showIncomeSupplForm() {
	$("#BackSupplIncome").modal("show");
}

function findIncomeSuppl() {
	$("#catalogue_art_range").html("<div class=\"sk-spinner sk-spinner-wave\"><div class=\"sk-rect1\"></div><div class=\"sk-rect2\"></div><div class=\"sk-rect3\"></div><div class=\"sk-rect4\"></div><div class=\"sk-rect5\"></div></div>");
	let art = $("#catalogue_art").val();
	JsHttpRequest.query($rcapi,{ 'w': 'findIncomeSuppl', 'art':art},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#catalogue_art_range").html(result["content"]);
		}}, true);
}

function saveIncomeSuppl(income_id, client_id, client_seller) {
	let back_suppl_id = $("#back_suppl_id").val();
	JsHttpRequest.query($rcapi,{ 'w': 'saveIncomeSuppl', 'back_suppl_id':back_suppl_id, 'income_id':income_id, 'client_id':client_id, 'client_seller':client_seller},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				$("#BackSupplIncome").modal("hide");
				if (back_suppl_id > 0) {
					showBackSupplCard(back_suppl_id);
				} else {
					$("#income_id").val(income_id);
					$("#income_name").val(result["income_name"]);
					$("#client_id").val(client_id);
					$("#client_name").val(result["client_name"]);
					$("#client_seller").val(client_seller);
					$("#client_seller_name").val(result["client_seller_name"]);
				}
			} else {
				toastr["error"](result["error"]);
			}
		}}, true);
}

/*
* storsel
* */
function makeBackSupplStorsel(back_suppl_id) {
	swal({
			title: "Передати в роботу?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				if (back_suppl_id > 0) {
					JsHttpRequest.query($rcapi,{'w':'makeBackSupplStorsel', 'back_suppl_id':back_suppl_id},
						function (result, errors){ if (errors) {alert(errors);} if (result){
							if (result["answer"] == 1) {
								swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
								loadBackSupplList();
								$("#BackSupplCard").modal("hide");
								console.log(result["select_id"]);
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

function saveFinishBackSuppl(back_suppl_id) {
	JsHttpRequest.query($rcapi,{'w':'saveFinishBackSuppl', 'back_suppl_id':back_suppl_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
				loadBackSupplList();
				$("#BackSupplCard").modal("hide");
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

// function updateGroupCacheArts() {
// 	JsHttpRequest.query($rcapi,{'w':'updateGroupCacheArts'},
// 		function (result, errors){ if (errors) {alert(errors);} if (result){
// 			if (result["answer"] == 1) {
// 				console.log(result["error"]);
// 			} else {
// 				console.log(result["error"]);
// 			}
// 		}}, true);
// }