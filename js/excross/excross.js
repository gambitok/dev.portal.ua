var select_ids = [];
$(document).ready(function(e) {
    $('select#expcross_brands option').each(function(index, element) {
        select_ids.push($(this).val());
    })
});

function exportDocs() {
	var client_id=$("#client_list option:selected").val();
	var date_start=$("#date_start").val();
	var date_end=$("#date_end").val();
	if (client_id==0 || (date_start>date_end)){toastr["error"]("Не вірно вибрані дані!");} else {
		var url = "/export_doc/download/"+client_id+"/"+date_start+"/"+date_end;
		window.open(url, '_blank');
	}
}

function ExportCross() {
	var expcross_type=$("#expcross_type option:selected").val();
	//var expcross_brands=$("#expcross_brands").val();
	var e = document.getElementById("expcross_brands");	
	//var expcross_all=0;
	
	//if (document.getElementById("expcross_all").checked) expcross_all=1; else {expcross_all=0; 
	var expcross_brands = Array.prototype.filter.call( document.getElementById("expcross_brands").options, el => el.selected).map(el => el.text).join(",");
																			   //var expcross_brands = e.options[e.selectedIndex].text;
	//																		  }
		//excel format
		if (expcross_type==1) {
			var url = "/export2.php?w=ExportCross&expcross_brands="+expcross_brands+"&expcross_type="+expcross_type;
			window.open(url, '_blank');
		} else
		//csv format
		if (expcross_type==2) {
			var url = "/export2.php?w=ExportCross&expcross_brands="+expcross_brands+"&expcross_type="+expcross_type;
			window.open(url, '_blank');
		}
}

function ExportJustCross() {
	var url = "/export2.php?w=ExportCross&expcross_brands=BBC&expcross_type=2";
	window.open(url, '_blank');
}

function DisableInput() {
	if (document.getElementById("expcross_all").checked){
		document.getElementById("expcross_type").selectedIndex = 1;
		document.getElementById("expcross_type").disabled = true;

		$('select#expcross_brands').val(select_ids);
		$('select#expcross_brands').trigger('chosen:updated');
		alert("Вигрузка займе багато часу!!!");
		
	} else {
		$('#expcross_brands option').prop('selected', false);
		
		$('select#expcross_brands').trigger('chosen:updated');
		document.getElementById("expcross_type").disabled = false;
		$('select#expcross_brands').val('');
	}
}

