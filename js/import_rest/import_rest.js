var errs=[];
errs[0]="������� �������";
errs[1]="������� �������� ����� ��� ������";

function showCsvUploadForm(){
	$("#fileImportCsvUploadForm").modal("show");
	var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "�������� ��� ������ ����� ��� ���������� �� �� ����!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		toastr["info"]("������������ ����� ���������.");
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
			swal("������ ����� ���������!", "�������� ��� �������.", "success");
			$("#waveSpinner_place").html("");
		}
		else{ swal("�������!", result["error"], "error");}
	}}, true);
}
