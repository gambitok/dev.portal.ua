
$(document).ready(function() {
	var elem2 = document.querySelector('#client_status');if (elem2){ new Switchery(elem2, { color: '#1AB394' });}
	var elem3 = document.querySelector('#doc_status');if (elem3){ new Switchery(elem3, { color: '#1AB394' });}
});

function showReportMargin() {
    let date_start=$("#date_start").val();
    let date_end=$("#date_end").val();
    let doc_type_id=$("#doc_type_id option:selected").val();
    let cash_id=$("#cash_select option:selected").val();
    let client_status=$("#client_status").prop("checked"); if (client_status) client_status=1; else client_status=0;
    let doc_status=$("#doc_status").prop("checked"); if (doc_status) doc_status=1; else doc_status=0;
	JsHttpRequest.query($rcapi,{ 'w': 'showReportMargin', 'date_start':date_start, 'date_end':date_end, 'doc_type_id':doc_type_id, 'client_status':client_status, 'doc_status':doc_status, 'cash_id':cash_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let dt = $("#datatable");
			dt.DataTable().destroy();
			$("#report_margin_range").html(result["content"]);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}}, true);
}

function exportReportMargin() {
    let date_start=$("#date_start").val();
    let date_end=$("#date_end").val();
    let doc_type_id=$("#doc_type_id option:selected").val();
    let cash_id=$("#cash_select option:selected").val();
    let client_status=$("#client_status").prop("checked"); if (client_status) client_status=1; else client_status=0;
    let doc_status=$("#doc_status").prop("checked"); if (doc_status) doc_status=1; else doc_status=0;
    let url = "/export_report_margin.php?w=Export&date_start="+date_start+"&date_end="+date_end+"&doc_type_id="+doc_type_id+"&client_status="+client_status+"&doc_status="+doc_status+"&cash_id="+cash_id;
	window.open(url, '_blank');
}
