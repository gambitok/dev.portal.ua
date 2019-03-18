var select_ids = [];
$(document).ready(function(e) {
    $('select#clients option').each(function(index, element) {
        select_ids.push($(this).val());
    })
});

function showReportClients() {
	var date_start=$("#date_start").val();
	var date_end=$("#date_end").val();
	var clients = Array.prototype.filter.call( document.getElementById("clients").options, el => el.selected).map(el => el.value).join(",");	
	var cash_id=$("#cash_select option:selected").val();
	var tpoint_id=$("#tpoint_select option:selected").val();
	
	JsHttpRequest.query($rcapi,{ 'w': 'showReportClients', 'date_start':date_start, 'date_end':date_end, 'clients':clients, 'cash_id':cash_id, 'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){ 
			$('#datatable').DataTable().destroy();
			$("#report_clients_range").html(result["content"]);
			$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}}, true);
}

