var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";


$(document).ready(function() {
	$(document).bind('keydown', 'ctrl+a', function(){ ShowCheckAll2();});
	$(document).bind('keydown', 'a', function(){ ShowCheckAll2();});
	$(document).bind('keydown', 'p', function(){ ShowModalAll(); });
	$(document).bind('keydown', 'f2', function(){ document.getElementById("discountStr").focus()});			
	
});

$(window).bind('beforeunload', function(e){
    if($('#sale_invoice_id')){
		//closeSaveInvoiceCard();
		e=null;
	}
    else e=null; 
});

function show_sale_invoice_search(inf){
	$("#sale_invoice_range").empty();
	JsHttpRequest.query($rcapi,{ 'w': 'show_suppl_order'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("sale_invoice_range").innerHTML=result["content"];
		if (inf==1){toastr["info"]("Виконано!");}
	}}, true);
} 

function printSaleInvoce(invoice_id){
	if (invoice_id=="" || invoice_id==0){toastr["error"](errs[0]);}
	if (invoice_id>0){
		window.open("/SaleInvoice/printSlIv/"+invoice_id,"_blank","printWindow");
	}
}


function showSupplOrder(so_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showSupplOrder','so_id':so_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Картка замовлення";
		$('#delivery_data').datepicker({format: "yyyy-mm-dd",autoclose:true});
		$('#delivery_data_finish').datepicker({format: "yyyy-mm-dd",autoclose:true});
		$('#delivery_time').datepicker({format: "hh:ii",autoclose:true});
		$('#delivery_time_finish').datepicker({format: "hh:ii",autoclose:true});
		numberOnlyPlace("cash_kours");
	}}, true);
}


function saveSupplOrder(){
	var so_id=$("#so_id").val();
	var amount_order=$("#amount_order").val();
	var delivery_data_finish=$("#delivery_data_finish").val();
	var delivery_time_finish=$("#delivery_time_finish").val();
	var delivery_type_id=$("#delivery_type_id option:selected").val();
	var suppl_order_status_id=$("#suppl_order_status_id option:selected").val();
	var suppl_order_doc=$("#suppl_order_doc").val();
	
	if (so_id<=0 || so_id==""){toastr["error"](errs[0]);}
	if (so_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'saveSupplOrder', 'so_id':so_id, 'amount_order':amount_order,'delivery_data_finish':delivery_data_finish,'delivery_time_finish':delivery_time_finish,'delivery_type_id':delivery_type_id,'suppl_order_status_id':suppl_order_status_id,'suppl_order_doc':suppl_order_doc}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
				$("#FormModalWindow").modal('hide');document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";
				setTimeout("window.location.reload();",500);
			}
			else{ toastr["error"](result["error"]); }
		}}, true);
	}
}
