var select_ids = [];

$(document).ready(function(e) {
    $("select#expcross_brands option").each(function(index, element) {
        select_ids.push($(this).val());
    });
});

function exportDocs() {
    let client_id=$("#client_list option:selected").val();
    let date_start=$("#date_start").val();
    let date_end=$("#date_end").val();
	if (client_id==0 || (date_start>date_end)){toastr["error"]("Не вірно вибрані дані!");} else {
        let url = "/export_doc/download/"+client_id+"/"+date_start+"/"+date_end;
		window.open(url, '_blank');
	}
}

function ExportCross() {
    let expcross_type=$("#expcross_type option:selected").val();
    let e = document.getElementById("expcross_brands");
    let expcross_brands = Array.prototype.filter.call( document.getElementById("expcross_brands").options, el => el.selected).map(el => el.text).join(",");
	//excel format
	if (expcross_type==1) {
        let url = "/export2.php?w=ExportCross&expcross_brands="+expcross_brands+"&expcross_type="+expcross_type;
		window.open(url, '_blank');
	} else
	//csv format
	if (expcross_type==2) {
        let url = "/export2.php?w=ExportCross&expcross_brands="+expcross_brands+"&expcross_type="+expcross_type;
		window.open(url, '_blank');
	}
}

function ExportJustCross() {
    let url = "/export2.php?w=ExportCross&expcross_brands=BBC&expcross_type=2";
	window.open(url, '_blank');
}

function DisableInput() {
	let seb = $("select#expcross_brands");
	if (document.getElementById("expcross_all").checked){
		document.getElementById("expcross_type").selectedIndex = 1;
		document.getElementById("expcross_type").disabled = true;
		seb.val(select_ids);
        seb.trigger('chosen:updated');
		alert("Вигрузка займе багато часу!!!");
	} else {
		$("#expcross_brands option").prop('selected', false);
        seb.trigger('chosen:updated');
		document.getElementById("expcross_type").disabled = false;
        seb.val('');
	}
}

