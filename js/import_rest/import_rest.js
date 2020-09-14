var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

function showCsvUploadForm(){
	$("#fileImportCsvUploadForm").modal("show");
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
	JsHttpRequest.query($rcapi,{ 'w': 'showImportRestCSVform'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("importRestPlace").innerHTML=result["content"];
		setTimeout(function(){ $('#csv_table').DataTable({keys: false,"processing": true,"scrollX": true,"bSort": false, "searching": false,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});}, 500);
	}}, true);
}

function finishCsvImport(){
	$("#waveSpinner_place").html(waveSpinner);
	JsHttpRequest.query($rcapi,{ 'w':'finishRestCsvImport'},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Імпорт даних завершено!", "Перевірте нові накладні.", "success");
			$("#waveSpinner_place").html("");
		}
		else{ swal("Помилка!", result["error"], "error");}
	}}, true);
}
