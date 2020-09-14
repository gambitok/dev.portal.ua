
function filterReportOverdraftList() {
	let data=$("#date_start").val();
    let client_id=$("#client_select option:selected").val();
    let tpoint_id=$("#tpoint_select option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'filterReportOverdraftList', 'data':data, 'client_id_cur':client_id, 'tpoint_id_cur':tpoint_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){
		let dt = $("#datatable");
		dt.DataTable().destroy();
		$("#report_overdraft_range").html(result.content[0]);
		dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		$('#report_overdraft_summ').html(result.content[1]);
	}}, true);
}

function getClientOverdraftList() {
    let data=$("#date_start").val();
    let tpoint_id=$("#tpoint_select option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'getClientOverdraftList', 'data':data, 'tpoint_id':tpoint_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#client_select").html(result.content);
		filterReportOverdraftList();
	}}, true);
}

function showDocsProlongationForm(client_id,invoice_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showDocsProlongationForm', 'client_id':client_id, 'invoice_id':invoice_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
		$("#FormModalBody").html(result.content);
		$("#FormModalLabel").html("Пролонгація докукментів");
	}}, true);
}

function showClientGeneralSaldoForm(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showClientGeneralSaldoForm', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
        $("#FormModalBody").html(result["content"]);
        $("#FormModalLabel").html(result["header"]);
		$("#saldo_data_start").datepicker({format: "yyyy-mm-dd",autoclose:true});
		$("#saldo_data_end").datepicker({format: "yyyy-mm-dd",autoclose:true});
	}}, true);
}

function filterClientGeneralSaldoForm(client_id){
	if (client_id.length>0){
        let from=$("#saldo_data_start").val();
        let to=$("#saldo_data_end").val();
		JsHttpRequest.query($rcapi,{ 'w': 'filterClientGeneralSaldoForm', 'client_id':client_id, 'from':from, 'to':to},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#client_saldo_list_range").html(result["range"]);
			$("#client_saldo_start").html(result["saldo_start"]);
			$("#client_saldo_end").html(result["saldo_end"]);
			$("#client_saldo_data_start").html(result["saldo_data_start"]);
			$("#client_saldo_data_end").html(result["saldo_data_end"]);
		}}, true);
	}
}

function printGeneralSaldoList() {
    let saldo_data_start=$("#saldo_data_start").val();
    let saldo_data_end=$("#saldo_data_end").val();
    let client_id=$("#client_id").val();
	window.open("/Clients/printCl1/"+client_id+"/"+saldo_data_start+"/"+saldo_data_end,"_blank","printWindow");
}

function showSaleInvoiceCard(invoice_id){
	if (invoice_id<=0 || invoice_id==""){toastr["error"](errs[0]);}
	if (invoice_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showSaleInvoiceCard', 'invoice_id':invoice_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#SaleInvoiceCard").modal("show");
            $("#SaleInvoiceCardBody").html(result["content"]);
            $("#SaleInvoiceCardLabel").html(result["doc_prefix_nom"]);
			$("#sale_invoice_tabs").tab();
			$("#data_pay").datepicker({format: "yyyy-mm-dd",autoclose:true});
			$(".i-checks").iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
		}}, true);
	}
}

function viewJpayMoneyPay(pay_id){
	JsHttpRequest.query($rcapi,{ 'w': 'viewJpayMoneyPay', 'pay_id':pay_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal("show");
		$("#FormModalBody2").html(result["content"]);
		$("#FormModalLabel2").html("Оплата накладної");
		numberOnlyPlace("sale_invoice_kredit");
		numberOnlyPlace("cash_kours");
	}}, true);
}
