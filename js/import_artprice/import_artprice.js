var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";


function showCsvUploadForm(){
	$("#fileImportCsvUploadForm").modal('show');
	
	var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$('#fileImportCsvUploadForm').modal('hide');
		importStrCSV();
	});
}

function importStrCSV(){
	JsHttpRequest.query($rcapi,{ 'w': 'showImportArtpriceStrCSVform'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("importArtpricePlace").innerHTML=result["content"];
		setTimeout(function(){ $('#csv_table').DataTable({keys: false,"processing": true,"scrollX": true,"bSort": false, "searching": false,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});}, 500);
	}}, true);
}

function getKoursNbu(place,data_place,val_place){
	var data=$("#"+data_place).val();
	var valuta=$("#"+val_place+" option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'getNBUKours', 'data':data,'valuta':valuta}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#"+place).val(result["content"]);
	}}, true);
	return;
}

function finishCsvImport(imp){
	var start_row=parseInt($("#csv_from_row").val());
	var kol_cols=parseInt($("#kol_cols").val());
	var cls_kol=5; 
	
	if (start_row<0 || start_row.length<=0){ swal("Помилка!", "Не вказано початковий ряд зчитування", "error");}
	if (start_row>=0){ 
		var cols=[];var cl=0; var cls_sel=0;
		for (var i=1;i<=kol_cols;i++){
			cl=$("#clm-"+i+" option:selected").val();
			if (cl>0){cls_sel+=1; cols[i]=cl;}
		}
		if (cls_sel<5){swal("Помилка!", "Не вказано мінімальний набір значення колонок", "error");}
		else{
			$("#waveSpinner_place").html(waveSpinner);
			JsHttpRequest.query($rcapi,{ 'w':'finishArtpriceCsvImport','start_row':start_row,'kol_cols':kol_cols,'cols':cols},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Імпорт даних завершено!", "Перевірте нові дані накладної.", "success");
					$("#waveSpinner_place").html("");
					importStrCSV();
				}
				else{ swal("Помилка!", result["error"], "error");}
			}}, true);
		}
	}
}


//----------------------------------------------------------------------------------------------------------------------------------
//						CALCULATE STR INCOME
//----------------------------------------------------------------------------------------------------------------------------------

function setIncomeVat(){
	var income_id=$("#income_id").val();
	var vat_use=0;if (document.getElementById("vat_use").checked){vat_use=1;}
	JsHttpRequest.query($rcapi,{ 'w': 'setIncomeVat', 'income_id':income_id,'vat_use':vat_use}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if(result["answer"]==1){
			recalculateIncomeStrLocal();
		}else{swal("Помилка", result["err"], "error");}
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
