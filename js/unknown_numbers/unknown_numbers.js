function showNumbersList(suppl_id) {
	var suppl_id=$("#select_numbers option:selected").val();
	$("#unknown_numbers_range").html('<div class="sk-spinner sk-spinner-wave"><div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div></div>');
	JsHttpRequest.query($rcapi,{ 'w': 'showNumbersList', 'suppl_id':suppl_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){ 
			$('#datatable').DataTable().destroy();
			$("#unknown_numbers_range").html(result["content"]);
			$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		} else console.log('error');}, true);
}