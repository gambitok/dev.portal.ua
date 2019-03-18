
function showReportSales() {
	var tpoint=$("#tpoint_select option:selected").val();
	var date_start=$("#date_start").val();
	JsHttpRequest.query($rcapi,{ 'w': 'showReportSales', 'date_start':date_start, 'tpoint':tpoint}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){ 
		$('#datatable').DataTable().destroy();
		$("#report_sales_range").html(result["content"]);
		$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
}