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
	JsHttpRequest.query($rcapi,{ 'w': 'show_sale_invoice_search'}, 
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

function filterJPayList() {
	var date_start=$("#date_start").val();
	var date_end=$("#date_end").val();
	var doc_type=$("#doc_type option:selected").val();
	var jpay_name=$("#jpay_name option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'filterJPayList', 'date_start':date_start, 'date_end':date_end, 'doc_type':doc_type, 'jpay_name':jpay_name}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){ 
		$('#datatable').DataTable().destroy();
		$("#jpay_range").html(result["content"]);
		$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
}


function showJpayMoneyPayForm(){
	JsHttpRequest.query($rcapi,{ 'w': 'showJpayMoneyPayForm'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Оплата накладної";
		numberOnlyPlace("sale_invoice_kredit");
		numberOnlyPlace("cash_kours");
	}}, true);
}

function viewJpayMoneyPay(pay_id){
	JsHttpRequest.query($rcapi,{ 'w': 'viewJpayMoneyPay', 'pay_id':pay_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Оплата накладної";
		numberOnlyPlace("sale_invoice_kredit");
		numberOnlyPlace("cash_kours");
	}}, true);
}

function saveJpayMoneyPay(){
	var invoice_id=$("#sale_invoice_id").val();
	var kredit=parseFloat($("#sale_invoice_kredit").val()).toFixed(2);
	var pay_type_id=$("#pay_type_id option:selected").val();
	var paybox_id=$("#paybox_id option:selected").val();
	var doc_cash_id=$("#doc_cash_id").val();
	var cash_id=$("#cash_id option:selected").val();
	var cash_kours=$("#cash_kours").val();
	
	if (invoice_id<=0 || invoice_id==""){toastr["error"](errs[0]);}
	if (invoice_id>0){
		if (kredit<=0){toastr["error"]("Введіть суму оплати");}
		if (paybox_id<=0){toastr["error"]("Виберіть касу");}
		if (kredit>0 && paybox_id>0){
			$("#save_money_spend").attr("disabled", true); //disable button
			JsHttpRequest.query($rcapi,{ 'w': 'saveJpayMoneyPay', 'invoice_id':invoice_id, 'kredit':kredit,'pay_type_id':pay_type_id,'paybox_id':paybox_id,'doc_cash_id':doc_cash_id,'cash_id':cash_id,'cash_kours':cash_kours}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
			
					$("#FormModalWindow").modal('hide');document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";
					setTimeout("window.location.reload();",500);
				}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}
function saveJpayAutoMoneyPay(){
	var client_id=parseFloat($("#client_id").val()).toFixed(2);
	var kredit=parseFloat($("#sale_invoice_kredit").val()).toFixed(2);
	var pay_type_id=$("#pay_type_id option:selected").val();
	var paybox_id=$("#paybox_id option:selected").val();
	var cash_id=$("#cash_id option:selected").val();
	var cash_kours=$("#cash_kours").val();
	var doc_cash_id=$("#doc_cash_id").val();
	
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		if (kredit<=0){toastr["error"]("Введіть суму оплати");}
		if (paybox_id<=0){toastr["error"]("Виберіть касу");}
		if (kredit>0 && paybox_id>0){
			$("#jpay_save").attr("disabled", true); //disable button
			JsHttpRequest.query($rcapi,{ 'w': 'saveJpayAutoMoneyPay', 'client_id':client_id, 'kredit':kredit,'pay_type_id':pay_type_id,'paybox_id':paybox_id,'cash_id':cash_id,'cash_kours':cash_kours,'doc_cash_id':doc_cash_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					
					$("#FormModalWindow").modal('hide');document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";
					setTimeout("window.location.reload();",500);
				}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function saveJpayAvansMoneyPay(){
	var client_id=parseFloat($("#client_id").val()).toFixed(2);
	var kredit=parseFloat($("#avans_kredit").val()).toFixed(2);
	var pay_type_id=$("#pay_type_id option:selected").val();
	var doc_cash_id=$("#doc_cash_id").val();
		
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		if (kredit<=0){toastr["error"]("Введіть суму оплати з авансу");}
		if (kredit>0){
			$("#jpay_save").attr("disabled", true); //disable button
			JsHttpRequest.query($rcapi,{ 'w': 'saveJpayAvansMoneyPay', 'client_id':client_id, 'kredit':kredit,'pay_type_id':pay_type_id,'doc_cash_id':doc_cash_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					
					$("#FormModalWindow").modal('hide');document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";
					setTimeout("window.location.reload();",500);
				}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}


function saveJpayAvansPay(){
	var client_id=parseFloat($("#client_id").val()).toFixed(2);
	var kredit=parseFloat($("#sale_invoice_kredit").val()).toFixed(2);
	var pay_type_id=$("#pay_type_id option:selected").val();
	var paybox_id=$("#paybox_id option:selected").val();
	var doc_cash_id=$("#doc_cash_id").val();
	var cash_id=$("#cash_id option:selected").val();
	var cash_kours=$("#cash_kours").val();
	
	
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		if (kredit<=0){toastr["error"]("Введіть суму оплати");}
		if (kredit>0){
			$("#jpay_save").attr("disabled", true); //disable button
			JsHttpRequest.query($rcapi,{ 'w': 'saveJpayAvansPay', 'client_id':client_id, 'kredit':kredit,'pay_type_id':pay_type_id,'paybox_id':paybox_id,'cash_id':cash_id,'cash_kours':cash_kours,'doc_cash_id':doc_cash_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
			
					$("#FormModalWindow").modal('hide');document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";
					setTimeout("window.location.reload();",500);
				}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function showJpayAvansMoneyPayForm(){
	JsHttpRequest.query($rcapi,{ 'w': 'showJpayAvansMoneyPayForm'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Оплата з авансу";
		//numberOnlyPlace("sale_invoice_kredit");
		numberOnlyPlace("cash_kours");
	}}, true);
}

function showJpayAutoMoneyPayForm(){
	JsHttpRequest.query($rcapi,{ 'w': 'showJpayAutoMoneyPayForm'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Автооплата";
		//numberOnlyPlace("sale_invoice_kredit");
		numberOnlyPlace("cash_kours");
	}}, true);
}

function showJpayAvansForm(){
	JsHttpRequest.query($rcapi,{ 'w': 'showJpayAvansForm'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Аванс";
		numberOnlyPlace("sale_invoice_kredit");
		numberOnlyPlace("cash_kours");
	}}, true);
}
function showJpayMoneyBackForm(){
	JsHttpRequest.query($rcapi,{ 'w': 'showJpayMoneyBackForm'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Видача коштів";
		numberOnlyPlace("avans_debit");
		//numberOnlyPlace("cash_kours");
	}}, true);
}

function saveJpayMoneyBackPay(){
	var client_id=parseFloat($("#client_id").val()).toFixed(2);
	var doc_cash_id=$("#doc_cash_id").val();
	var cash_id=$("#cash_id option:selected").val();
	var avans_debit=parseFloat($("#avans_debit").val()).toFixed(2);
	var pay_type_id=$("#pay_type_id option:selected").val();
	var paybox_id=$("#paybox_id option:selected").val();
	var cash_kours=$("#cash_kours").val();
	
	if (client_id<=0 || client_id==""){toastr["error"](errs[0]);}
	if (client_id>0){
		if (avans_debit<=0){toastr["error"]("Введіть суму виплати");}
		if (avans_debit>0){
			$("#jpay_save").attr("disabled", true); //disable button
			JsHttpRequest.query($rcapi,{ 'w': 'saveJpayMoneyBackPay', 'client_id':client_id, 'avans_debit':avans_debit,'pay_type_id':pay_type_id,'paybox_id':paybox_id,'cash_id':cash_id,'cash_kours':cash_kours,'doc_cash_id':doc_cash_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
		
					$("#FormModalWindow").modal('hide'); document.getElementById("FormModalBody").innerHTML=""; document.getElementById("FormModalLabel").innerHTML="";
					setTimeout("window.location.reload();",500);
				}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
	
}

function unlockKours(id){
	var invoice_id=$("#pay_invoice_id").val();
	var pay_id=$("#pay_id").val();
	
	if (invoice_id<=0 || invoice_id==""){toastr["error"](errs[0]);}
	if (invoice_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'unlockSaleInvoiceMoneyPayKours', 'invoice_id':invoice_id, 'pay_id':pay_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$(""+id).attr('type', 'text'); 
				$(""+id).removeAttr('disabled'); 
			}
			else{ toastr["error"](result["error"]); }
		}}, true);
	}
}

function getCashKours(){
	var cash_id=$("#cash_id option:selected").val();
	var doc_cash_id=$("#doc_cash_id").val();
	if (cash_id<=0 || cash_id==""){toastr["error"](errs[0]);}
	if (cash_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getCashKoursSaleInvoiceMoneyPay', 'doc_cash_id':doc_cash_id, 'cash_id':cash_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#cash_kours").val(result["cash_kours"]);
			}
			else{ toastr["error"](result["error"]); }
		}}, true);
	}
}

function getPayBoxBalans(){
	var paybox_id=$("#paybox_id option:selected").val();
	if (paybox_id<=0 || paybox_id==""){toastr["error"](errs[0]);}
	if (paybox_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getPayBoxBalans', 'paybox_id':paybox_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#paybox_balans").html(result["content"]);
		}}, true);
	}
}

function showJpayClientList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showJpayClientList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
		document.getElementById("FormModalBody2").innerHTML=result["content"];
		document.getElementById("FormModalLabel2").innerHTML="Контрагенти";
		setTimeout(function() { $('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}}); }, 500);
	}}, true);
}
function setJpayClient(id,name){ 
	$('#client_id').val(id); $('#client_name').val(Base64.decode(name));
	$('#sale_invoice_id').val("");$('#sale_invoice_name').val("");
	$("#FormModalWindow2").modal('hide');document.getElementById("FormModalBody2").innerHTML="";document.getElementById("FormModalLabel2").innerHTML="";
	if ($('#pay_auto').val()=="1"){
		JsHttpRequest.query($rcapi,{ 'w': 'loadJpayClientSaleInvoiceUnpayedList', 'client_id':id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#client_balans").val(result["summ_balans"]);
			$("#jpay_sale_invoice_range").html(result["content"]);
			$('#doc_cash_id').val(result["cash_id"]);
			$('#doc_cash_name').val(result["cash_name"]);
			$('#client_cash_name').html(result["cash_name"]);
			$('#selectBox option:eq('+result["cash_id"]+')').prop('selected', true);
			getCashKours();
			loadCashBoxList(id,result["document_type_id"],result["seller_id"]);
		}}, true);
	}
	if ($('#pay_auto').val()=="3"){
		JsHttpRequest.query($rcapi,{ 'w': 'getJpayClientDocCashId', 'client_id':id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$('#doc_cash_id').val(result["cash_id"]);
			$('#doc_cash_name').val(result["cash_name"]);
			$('#selectBox option:eq('+result["cash_id"]+')').prop('selected', true);
			getCashKours();
			loadCashBoxList(id,result["doc_type_id"],0);
		}}, true);
	}
	if ($('#pay_auto').val()=="4"){ //pay from avans
		JsHttpRequest.query($rcapi,{ 'w': 'loadJpayClientSaleInvoiceUnpayedList', 'client_id':id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#client_balans").val(result["summ_balans"]); //console.log("summ_balans="+result["summ_balans"]);
			$("#jpay_sale_invoice_range").html(result["content"]);
			
			$('#doc_cash_id').val(result["cash_id"]);
			$('#doc_cash_name').val(result["cash_name"]);
			$('#client_cash_name').html(result["cash_name"]);
			$('#client_balans_cash_name').html(result["cash_name"]);
			$('#client_balans_avans').val(result["client_balans_avans"]);
			
			var avans_kredit=0;
			var summ_balans=parseFloat(result["summ_balans"]);
			var client_balans_avans=parseFloat(result["client_balans_avans"]);
			if (client_balans_avans>summ_balans){ avans_kredit=summ_balans; }//console.log("avans_kredit 1="+avans_kredit);}
			if (client_balans_avans<=summ_balans){ avans_kredit=client_balans_avans; }//console.log("avans_kredit 2="+avans_kredit);}
			$('#avans_kredit').val(avans_kredit);
		}}, true);
	}
	if ($('#pay_auto').val()=="5"){ //pay_back from avans
		JsHttpRequest.query($rcapi,{ 'w': 'loadJpayClientSaleInvoiceUnpayedList', 'client_id':id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#client_balans").val(result["summ_balans"]); //console.log("summ_balans="+result["summ_balans"]);
			$("#jpay_sale_invoice_range").html(result["content"]);
			
			$('#doc_cash_id').val(result["cash_id"]);
			$('#doc_cash_name').val(result["cash_name"]);
			$('#client_cash_name').html(result["cash_name"]);
			$('#client_balans_cash_name').html(result["cash_name"]);
			$('#client_balans_avans').val(result["client_balans_avans"]);
			
			var avans_debit=0;
			var summ_balans=parseFloat(result["summ_balans"]);
			var client_balans_avans=parseFloat(result["client_balans_avans"]);
			if (client_balans_avans>summ_balans){ avans_debit=summ_balans; }//console.log("avans_kredit 1="+avans_kredit);}
			if (client_balans_avans<=summ_balans){ avans_debit=client_balans_avans; }//console.log("avans_kredit 2="+avans_kredit);}
			$('#avans_debit').val(avans_debit);
			getCashKours();
		}}, true);
	}
}

function unlinkJpayClient(){
	swal({
		title: "Відвязати клієнта від оплати?",text: "Внесені Вами зміни вплинуть на роботу Контагента",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			$('#client_id').val("0");$('#client_name').val("");
			$('#sale_invoice_id').val("");$('#sale_invoice_name').val("");
			swal("Виконано!", "", "success");
		} else { swal("Відмінено", "Операцію анульовано.", "error"); }
	});
}
function unlinkJpayAutoClient(){
	swal({
		title: "Відвязати клієнта від оплати?",text: "Внесені Вами зміни вплинуть на роботу Контагента",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			$('#client_id').val("0");$('#client_name').val("");
			$('#jpay_sale_invoice_range').html(" <tr><td colspan=\"11\" class=\"text-center\">Оберіть клієнта для відображення накладних!</td></tr>");
			swal("Виконано!", "", "success");
		} else { swal("Відмінено", "Операцію анульовано.", "error"); }
	});
}


function showJpayClientSaleInvoiceList(){
	var client_id=$('#client_id').val();
	JsHttpRequest.query($rcapi,{ 'w': 'showJpayClientSaleInvoiceList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
		document.getElementById("FormModalBody2").innerHTML=result["content"];
		document.getElementById("FormModalLabel2").innerHTML="Контрагенти";
		setTimeout(function() { $('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}}); }, 500);
	}}, true);
}
function loadCashBoxList(client_id,document_type_id,seller_id){
	//client_id=$("client_id").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadJpayCashBoxList', 'client_id':client_id, 'document_type_id':document_type_id,'seller_id':seller_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("paybox_id").innerHTML=result["content"];
	}}, true);
}
function setJpaySaleInvoice(id,name,summ,summ_debit,tpoint_id,document_type_id,cash_id,cash_name,seller_id){
	$('#sale_invoice_id').val(id);$('#sale_invoice_name').val(name);
	$('#sale_invoice_summ').val(summ);$('#sale_invoice_debit').val(summ_debit);$('#sale_invoice_kredit').val(summ_debit);
	$('#doc_cash_id').val(cash_id);
	//$("#cash_id option:selected").val(cash_id);
	$('#cash_id option:eq('+cash_id+')').prop('selected', true);
	getCashKours();
	loadCashBoxList(id,document_type_id,seller_id);
	
	$("#FormModalWindow2").modal('hide');document.getElementById("FormModalBody2").innerHTML="";document.getElementById("FormModalLabel2").innerHTML="";
}