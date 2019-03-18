
function filterReportOverdraftList() {
	var data=$("#date_start").val();
	var client_id=$("#client_select option:selected").val();
	var tpoint_id=$("#tpoint_select option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'filterReportOverdraftList', 'data':data, 'client_id':client_id, 'tpoint_id':tpoint_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){ 
		$('#datatable').DataTable().destroy();
		$("#report_overdraft_range").html(result.content[0]);
		$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		$('#report_overdraft_summ').html(result.content[1]);
	}}, true);
}

function getClientOverdraftList() {
	var data=$("#date_start").val();
	var tpoint_id=$("#tpoint_select option:selected").val(); 
	JsHttpRequest.query($rcapi,{ 'w': 'getClientOverdraftList', 'data':data, 'tpoint_id':tpoint_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#client_select").html(result.content);
		filterReportOverdraftList();
	}}, true);
}

function showDocsProlongationForm(client_id,invoice_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showDocsProlongationForm', 'client_id':client_id, 'invoice_id':invoice_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		$("#FormModalBody").html(result.content);
		$("#FormModalLabel").html("Пролонгація докукментів");
	}}, true);
}

function showClientGeneralSaldoForm(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showClientGeneralSaldoForm', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML=result["header"];
		$('#saldo_data_start').datepicker({format: "yyyy-mm-dd",autoclose:true})
		$('#saldo_data_end').datepicker({format: "yyyy-mm-dd",autoclose:true})

	}}, true);
}

function filterClientGeneralSaldoForm(client_id){
	if (client_id.length>0){
		var from=$("#saldo_data_start").val();
		var to=$("#saldo_data_end").val();
		JsHttpRequest.query($rcapi,{ 'w': 'filterClientGeneralSaldoForm', 'client_id':client_id,'from':from,'to':to}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("client_saldo_list_range").innerHTML=result["range"];
			document.getElementById("client_saldo_start").innerHTML=result["saldo_start"];
			document.getElementById("client_saldo_end").innerHTML=result["saldo_end"];
			document.getElementById("client_saldo_data_start").innerHTML=result["saldo_data_start"];
			document.getElementById("client_saldo_data_end").innerHTML=result["saldo_data_end"];
			
		}}, true);
	}
}

function printGeneralSaldoList() {
	var saldo_data_start=$("#saldo_data_start").val();
	var saldo_data_end=$("#saldo_data_end").val();
	var client_id=$("#client_id").val();
	window.open("/Clients/printCl1/"+client_id+"/"+saldo_data_start+"/"+saldo_data_end,"_blank","printWindow");
}

function showSaleInvoiceCard(invoice_id){
	if (invoice_id<=0 || invoice_id==""){toastr["error"](errs[0]);}
	if (invoice_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showSaleInvoiceCard', 'invoice_id':invoice_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#SaleInvoiceCard").modal('show');
			document.getElementById("SaleInvoiceCardBody").innerHTML=result["content"];
			document.getElementById("SaleInvoiceCardLabel").innerHTML=result["doc_prefix_nom"];
			$('#sale_invoice_tabs').tab();
			$('#data_pay').datepicker({format: "yyyy-mm-dd",autoclose:true})
			$('.i-checks').iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
		}}, true);
	}
}

function viewJpayMoneyPay(pay_id){
	JsHttpRequest.query($rcapi,{ 'w': 'viewJpayMoneyPay', 'pay_id':pay_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
		document.getElementById("FormModalBody2").innerHTML=result["content"];
		document.getElementById("FormModalLabel2").innerHTML="Оплата накладної";
		numberOnlyPlace("sale_invoice_kredit");
		numberOnlyPlace("cash_kours");
	}}, true);
}