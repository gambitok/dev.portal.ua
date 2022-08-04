var errs = [];
errs[0] = "Помилка індексу";
errs[1] = "Занадто короткий запит для пошуку";

function showCsvUploadForm() {
	$("#fileImportCsvUploadForm").modal("show");
	var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$("#fileImportCsvUploadForm").modal("hide");
		importStrCSV();
	});
}

function importStrCSV() {
	JsHttpRequest.query($rcapi,{ 'w': 'showImportArtpriceStrCSVform'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("importArtpricePlace").innerHTML=result["content"];
		setTimeout(function(){
			$("#csv_table").DataTable({keys: false,"processing": true,"scrollX": true,"bSort": false, "searching": false,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function getKoursNbu(place, data_place, val_place) {
    let data = $("#"+data_place).val();
	let valuta = $("#"+val_place+" option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'getNBUKours', 'data':data, 'valuta':valuta},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#" + place).val(result["content"]);
	}}, true);
	return true;
}

function finishCsvImport(imp) {
	var start_row = parseInt($("#csv_from_row").val());
	var kol_cols = parseInt($("#kol_cols").val());
	if (start_row < 0 || start_row.length <= 0) {
		swal("Помилка!", "Не вказано початковий ряд зчитування", "error");
	}
	if (start_row>=0) {
		var cols=[]; var cl=0; var cls_sel=0;
		for (var i=1;i<=kol_cols;i++) {
			cl=$("#clm-"+i+" option:selected").val();
			if (cl>0) {
				cls_sel+=1;
				cols[i]=cl;
			}
		}
		if (cls_sel<5) {
			swal("Помилка!", "Не вказано мінімальний набір значення колонок", "error");
		} else {
			$("#waveSpinner_place").html(waveSpinner);
			JsHttpRequest.query($rcapi,{ 'w':'finishArtpriceCsvImport', 'start_row':start_row, 'kol_cols':kol_cols, 'cols':cols},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1) {
					swal("Імпорт даних завершено!", "Перевірте нові дані накладної.", "success");
					$("#waveSpinner_place").html("");
					importStrCSV();
				} else {
					swal("Помилка!", result["error"], "error");
				}
			}}, true);
		}
	}
}

//----------------------------------------------------------------------------------------------------------------------------------
//						CALCULATE STR INCOME
//----------------------------------------------------------------------------------------------------------------------------------

function setIncomeVat() {
    let income_id = $("#income_id").val();
    let vat_use = 0;
    if (document.getElementById("vat_use").checked) {
    	vat_use = 1;
    }
	JsHttpRequest.query($rcapi,{ 'w': 'setIncomeVat', 'income_id':income_id, 'vat_use':vat_use},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"] == 1) {
			recalculateIncomeStrLocal();
		} else {
			swal("Помилка", result["err"], "error");
		}
	}}, true);
	
}

function round(value, exp) {
	if (typeof exp === 'undefined' || +exp === 0)
	return Math.round(value);
	value = +value;
	exp = +exp;
	if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0))
	return NaN;
	value = value.toString().split('e');
	value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp)));
	value = value.toString().split('e');
	return +(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp));
}


function randString(id) {
	var dataSet = $(id).attr('data-character-set').split(',');
	var possible = "";  var text = "";
	if ($.inArray('a-z', dataSet) >= 0) {
		possible += 'abcdefghijklmnopqrstuvwxyz';
	}
	if ($.inArray('A-Z', dataSet) >= 0) {
		possible += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}
	if ($.inArray('0-9', dataSet) >= 0) {
		possible += '0123456789';
	}
	if ($.inArray('#', dataSet) >= 0) {
		possible += '![]{}()%&*$#^<>~@|';
	}
	for (var i=0; i < $(id).attr('data-size'); i++) {
		text += possible.charAt(Math.floor(Math.random() * possible.length));
	}
	$(id).val(""+text);
	return text;
}

/*
* Import INDEX
* */

function showCsvUploadIndexForm(user_id) {
	$("#fileCsvUploadForm").modal("show");
	$("#csv_user_id").val(user_id);
	var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		this.removeAllFiles();
		$("#fileCsvUploadForm").modal("hide");
		loadImportIndex(user_id);
	});
}

function loadImportIndex(user_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'loadImportIndex', 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#import_index").html(result.content);
			$("#datatable").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}}, true);
}

function saveCsvImportIndex(user_id) {
	let start_row = parseInt($("#csv_from_row").val());
	let kol_cols = parseInt($("#kol_cols").val());
	let cls_kol = 8;
	if (start_row < 0 || start_row.length <= 0) {
		swal("Помилка!", "Не вказано початковий ряд зчитування", "error");
	}
	if (start_row >= 0) {
		var cols = [];
		var cl = 0;
		var cls_sel = 0;
		for (var i = 1; i <= kol_cols; i++) {
			cl = $("#clm-" + i + " option:selected").val();
			if (cl > 0) {
				cls_sel += 1;
				cols[i] = cl;
			}
		}
		if (cls_sel < cls_kol) {
			swal("Помилка!", "Не вказані усі значення колонок", "error");
		} else {
			JsHttpRequest.query($rcapi,{ 'w':'saveCsvImportIndex', 'user_id':user_id, 'start_row':start_row, 'kol_cols':kol_cols, 'cols':cols},
				function (result, errors){ if (errors) {alert(errors);} if (result){
					if (result["answer"] == 1) {
						$("#FormModalWindow").modal("hide");
						swal("Імпорт даних завершено!", "Перевірте нові дані.", "success");
						loadImportIndex(user_id);
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
		}
	}
}

function clearImportIndex(user_id) {
	JsHttpRequest.query($rcapi,{ 'w':'clearImportIndex', 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				loadImportIndex(user_id);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

function finishImportIndex(user_id) {
	JsHttpRequest.query($rcapi,{ 'w':'finishImportIndex', 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				swal("Імпорт даних завершено!", "Перевірте нові дані.", "success");
				$("#FormModalWindow").modal("hide");
				loadImportIndex(user_id);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

/*
* IMPORT PHOTOS
* */

function showImportPhotoForm(user_id) {
	$("#fileCsvUploadForm").modal("show");
	$("#csv_user_id").val(user_id);
	var myDropzone3 = new Dropzone("#myDropzone",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		this.removeAllFiles();
		$("#fileCsvUploadForm").modal("hide");
		loadImportPhoto(user_id);
	});
}

function loadImportPhoto(user_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'loadImportPhoto', 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#import_photo").html(result.content);
		}}, true);
}

function dropImportPhoto(user_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'dropImportPhoto', 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			loadImportPhoto(user_id)
		}}, true);
}

/*
* IMPORT PHOTOS CSV
* */

function showImportPhotoCsvForm(user_id) {
	$("#fileCsvUploadForm2").modal("show");
	$("#csv_user_id2").val(user_id);
	var myDropzone3 = new Dropzone("#myDropzone2",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		this.removeAllFiles();
		$("#fileCsvUploadForm2").modal("hide");
		loadImportPhotoCsv(user_id);
	});
}

function loadImportPhotoCsv(user_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'loadImportPhotoCsv', 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#import_photo_csv").html(result.content);
		}}, true);
}

function clearImportPhoto(user_id) {
	JsHttpRequest.query($rcapi,{ 'w':'clearImportPhoto', 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				loadImportPhotoCsv(user_id);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

function saveImportPhotoCsv(user_id) {
	let start_row = parseInt($("#csv_from_row").val());
	let kol_cols = parseInt($("#kol_cols").val());
	let cls_kol = 4;
	if (start_row < 0 || start_row.length <= 0) {
		swal("Помилка!", "Не вказано початковий ряд зчитування", "error");
	}
	if (start_row >= 0) {
		var cols = [];
		var cl = 0;
		var cls_sel = 0;
		for (var i = 1; i <= kol_cols; i++) {
			cl = $("#clm-" + i + " option:selected").val();
			if (cl > 0) {
				cls_sel += 1;
				cols[i] = cl;
			}
		}
		if (cls_sel < cls_kol) {
			swal("Помилка!", "Не вказані усі значення колонок", "error");
		} else {
			JsHttpRequest.query($rcapi,{ 'w':'saveImportPhotoCsv', 'user_id':user_id, 'start_row':start_row, 'kol_cols':kol_cols, 'cols':cols},
				function (result, errors){ if (errors) {alert(errors);} if (result){
					if (result["answer"] == 1) {
						$("#FormModalWindow").modal("hide");
						swal("Імпорт даних завершено!", "Перевірте нові дані.", "success");
						loadImportPhotoCsv(user_id);
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
		}
	}
}

function finishImportPhoto(user_id) {
	JsHttpRequest.query($rcapi,{ 'w':'finishImportPhoto', 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				swal("Імпорт даних завершено!", "Перевірте нові дані.", "success");
				$("#FormModalWindow").modal("hide");
				loadImportPhotoCsv(user_id);
				loadImportPhoto(user_id);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

function finishImportExistPhoto(user_id) {
	JsHttpRequest.query($rcapi,{ 'w':'finishImportExistPhoto', 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				swal("Імпорт даних завершено!", "Перевірте нові дані.", "success");
				$("#FormModalWindow").modal("hide");
				loadImportPhotoCsv(user_id);
				loadImportPhoto(user_id);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

/*
* Import Cross
* */

function showImportCrossForm(user_id) {
	$("#fileCsvUploadForm").modal("show");
	$("#csv_user_id").val(user_id);
	var myDropzone3 = new Dropzone("#myDropzone",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		this.removeAllFiles();
		$("#fileCsvUploadForm").modal("hide");
		loadImportCross(user_id);
	});
}

function loadImportCross(user_id) {
	$("#import_cross").html($("#loader-2"));
	JsHttpRequest.query($rcapi,{ 'w': 'loadImportCross', 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
				$("#import_cross").html(result.content);
				$("#datatable").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
		}}, true);
}

function saveImportCross(user_id) {
	let start_row = parseInt($("#csv_from_row").val());
	let kol_cols = parseInt($("#kol_cols").val());
	let cls_kol = 5;
	if (start_row < 0 || start_row.length <= 0) {
		swal("Помилка!", "Не вказано початковий ряд зчитування", "error");
	}
	if (start_row >= 0) {
		var cols = [];
		var cl = 0;
		var cls_sel = 0;
		for (var i = 1; i <= kol_cols; i++) {
			cl = $("#clm-" + i + " option:selected").val();
			if (cl > 0) {
				cls_sel += 1;
				cols[i] = cl;
			}
		}
		if (cls_sel < cls_kol) {
			swal("Помилка!", "Не вказані усі значення колонок", "error");
		} else {
			$("#import_cross").html($("#loader-2"));
			JsHttpRequest.query($rcapi,{ 'w':'saveImportCross', 'user_id':user_id, 'start_row':start_row, 'kol_cols':kol_cols, 'cols':cols},
				function (result, errors){ if (errors) {alert(errors);} if (result){
					if (result["answer"] == 1) {
						$("#FormModalWindow").modal("hide");
						swal("Імпорт даних завершено!", "Перевірте нові дані.", "success");
						loadImportCross(user_id);
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
		}
	}
}

function clearImportCross(user_id) {
	JsHttpRequest.query($rcapi,{ 'w':'clearImportCross', 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				loadImportCross(user_id);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

function finishImportCross(user_id) {
	JsHttpRequest.query($rcapi,{ 'w':'finishImportCross', 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				swal("Імпорт даних завершено!", "Перевірте нові дані.", "success");
				loadImportCross(user_id);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

function setUnknownBrands(user_id, type) {
	let brand_id_from = 0;
	let brand_id_to = 0;
	if (type === "result_brand") {
		brand_id_from = $("#brand_result_from option:selected").val();
		brand_id_to = $("#brand_result_to option:selected").val();
	}
	if (type === "cross_brand") {
		brand_id_from = $("#brand_cross_from option:selected").val();
		brand_id_to = $("#brand_cross_to option:selected").val();
	}
 	JsHttpRequest.query($rcapi,{ 'w':'setUnknownBrands', 'user_id':user_id, 'type':type, 'brand_id_from':brand_id_from, 'brand_id_to':brand_id_to},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				loadImportCross(user_id);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

function getUnknownBrandsCatalog(type) {
	let brand_id_select = 0;
	if (type === "result_brand") {
		brand_id_select = $("#brand_result_from option:selected").val();
	}
	if (type === "cross_brand") {
		brand_id_select = $("#brand_cross_from option:selected").val();
	}
	JsHttpRequest.query($rcapi,{ 'w':'getUnknownBrandsCatalog', 'brand_id_select':brand_id_select},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (type === "result_brand") {
				$("#brand_result_to").html(result.content);
			}
			if (type === "cross_brand") {
				$("#brand_cross_to").html(result.content);
			}
		}}, true);
}

function getUnknownBrandsAll(type) {
	JsHttpRequest.query($rcapi,{ 'w':'getUnknownBrandsAll'},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (type === "result_brand") {
				$("#brand_result_to").html(result.content);
			}
			if (type === "cross_brand") {
				$("#brand_cross_to").html(result.content);
			}
		}}, true);
}

function showClarifyForm(import_id) {
	$("#CrossCard").modal("show");
	JsHttpRequest.query($rcapi,{ 'w': 'showClarifyForm', 'import_id':import_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#CrossCardBody").html(result.content);
		}}, true);
}

function saveClarifyForm(import_id, art_id) {
	JsHttpRequest.query($rcapi,{ 'w':'saveClarifyForm', 'import_id':import_id, 'art_id':art_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				swal("Збережено!", "Перевірте нові дані.", "success");
				$("#CrossCard").modal("hide");
				let user_id = $("#media_user_id").val();
				loadImportCross(user_id);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

function loadCrossTablePreview(user_id) {
	let status_check = $("#status_check").val();
	if (status_check == "0") {
		status_check = 1;
	} else {
		status_check = 0;
	}
	console.log(status_check);
	JsHttpRequest.query($rcapi,{ 'w':'loadCrossTablePreview', 'user_id':user_id, 'status_check':status_check},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let dt = $("#datatable");
			dt.DataTable().destroy();
			$("#range_table_import").html(result.content);
			dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
			$("#status_check").val(status_check);
		}}, true);

}

function saveImportCrossUnknown(user_id) {
	JsHttpRequest.query($rcapi,{ 'w':'saveImportCrossUnknown', 'user_id':user_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				swal("Виконано!", "Перевірте нові дані.", "success");
				loadImportCross(user_id);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}



