var errs = [];
errs[0] = "������� �������";
errs[1] = "������� �������� ����� ��� ������";

$(document).ready(function() {
	$(document).bind('keydown', 'ctrl+a', function(){ ShowCheckAll2();});
	$(document).bind('keydown', 'a', function(){ ShowCheckAll2();});
	$(document).bind('keydown', 'p', function(){ ShowModalAll(); });
	$(document).bind('keydown', 'f2', function(){ document.getElementById("discountStr").focus()});
	//setTimeout(function(){updateSaleInvoiceRange();},15*1000);
});

$(window).bind('beforeunload', function(e){
    if($("#sale_invoice_id")){
		//closeSaveInvoiceCard();
		e=null;
	}
    else e=null; 
});

function filterInvoiceList() {
	let date_start=$("#date_start").val();
    let date_end=$("#date_end").val();
    let prefix=$("#invoice_prefix").val();
    let doc_nom=$("#invoice_number").val();
	JsHttpRequest.query($rcapi,{ 'w': 'filterInvoiceList', 'date_start':date_start, 'date_end':date_end, 'prefix':prefix, 'doc_nom':doc_nom},
	function (result, errors){ if (errors) {alert(errors);} if (result){
		let dt=$("#datatable");
        dt.DataTable().destroy();
		$("#sale_invoice_range").html(result.content[0]);
		$("#sale_invoice_summ").html(result.content[1]);
        dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
} 

function filterBuhInvoiceList() {
	let date_start = $("#date_start").val();
	let date_end = $("#date_end").val();
	JsHttpRequest.query($rcapi,{ 'w': 'filterBuhInvoiceList', 'date_start':date_start, 'date_end':date_end},
	function (result, errors){ if (errors) {alert(errors);} if (result){
        let dt = $("#datatable");
        dt.DataTable().destroy();
		$("#sale_invoice_range").html(result.content[0]);
		$("#sale_invoice_summ").html(result.content[1]);
        dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
} 

function show_sale_invoice_search(inf) {
	$("#sale_invoice_range").empty();
	JsHttpRequest.query($rcapi,{ 'w': 'show_sale_invoice_search'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#sale_invoice_range").html(result.content);
		if (inf == 1) {
			toastr["info"]("��������!");
		}
	}}, true);
}

// function updateSaleInvoiceRange(){
// 	let invoice_range = $("#sale_invoice_range");
// 	let prevRange=invoice_range.html();
// 	JsHttpRequest.query($rcapi,{ 'w': 'show_sale_invoice_search'},
// 	function (result, errors){ if (errors) {alert(errors);} if (result){
// 		if (prevRange.length!=result["content"].length){
//             invoice_range.empty();
//             invoice_range.html(result.content);
//         }
// 	}}, true);
// }

function printSaleInvoce(invoice_id, type) {
	if (invoice_id == "" || invoice_id == 0) {
		toastr["error"](errs[0]);
	}
	if (invoice_id > 0) {
		window.open("/SaleInvoice/printSlIv/" + invoice_id + "/" + type, "_blank", "printWindow");
	}
}

function printDpSaleInvoce(invoice_id) {
	if (invoice_id == "" || invoice_id == 0) {
		toastr["error"](errs[0]);
	}
	if (invoice_id > 0) {
		window.open("/JournalDp/printDpSlIv/" + invoice_id, "_blank", "printWindow");
	}
}

function printBarcode(invoice_id) {
	if (invoice_id == "" || invoice_id == 0) {
		toastr["error"](errs[0]);
	}
	if (invoice_id > 0) {
		window.open("/SaleInvoice/printBarcode/" + invoice_id, "_blank", "printWindow");
	}
}

// function printSaleInvoceBuh(invoice_id){
// 	if (invoice_id=="" || invoice_id==0){toastr["error"](errs[0]);}
// 	if (invoice_id>0){
// 		window.open("/SaleInvoice/printSlIvBuh/"+invoice_id,"_blank","printWindow");
// 	}
// }

function exportSaleInvoceExcel(invoice_id) {
    swal({
		title: "������� ����� ��������",text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",confirmButtonText: "������", cancelButtonText: "����", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			swal.close();
			window.open("/SaleInvoice/exportExcelSlIv/"+invoice_id+"/point","_blank","printWindow");
		} else {
			swal.close();
			window.open("/SaleInvoice/exportExcelSlIv/"+invoice_id+"/comma","_blank","printWindow");
		}
	});
}

function showSaleInvoiceCard(invoice_id) {
	if (invoice_id <= 0 || invoice_id == "") {
		toastr["error"](errs[0]);
	}
	if (invoice_id > 0) {
		JsHttpRequest.query($rcapi,{ 'w': 'showSaleInvoiceCard', 'invoice_id':invoice_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#SaleInvoiceCard").modal("show");
            $("#SaleInvoiceCardBody").html(result["content"]);
            $("#SaleInvoiceCardLabel").html(result["doc_prefix_nom"]);
            $("#sale_invoice_tabs").tab();
			//$("#cash_id").select2({placeholder: "������� ������",dropdownParent: $("#DpCard")});
			$("#data_pay").datepicker({format: "yyyy-mm-dd", autoclose:true});
			$(".i-checks").iCheck({checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green',});
		}}, true);
	}
}

// function closeSaleInvoiceCard(){
// 	if ($("#dp_id")){
// 		var dp_id=$("#dp_id").val();
// 		JsHttpRequest.query($rcapi,{ 'w': 'closeSaleInvoiceCard', 'dp_id':dp_id},
// 		function (result, errors){ if (errors) {alert(errors);} if (result){
// 			$("#SaleInvoiceCard").modal("hide");
// 			$("#SaleInvoiceCardBody").html("");
// 			$("#SaleInvoiceCardLabel").html("");
// 		}}, true);
// 	}else{
// 		$("#SaleInvoiceCard").modal("hide");
//         $("#SaleInvoiceCardBody").html("");
//         $("#SaleInvoiceCardLabel").html("");
// 	}
// }

function loadSaleInvoiceMoneyPay(invoice_id){
	JsHttpRequest.query($rcapi,{ 'w': 'loadSaleInvoiceMoneyPay', 'invoice_id':invoice_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#money_pay_place").html(result.content);
		$("#sale_invoice_tabs").tab();
	}}, true);
}

function showSaleInvoceMoneyPayForm(invoice_id,pay_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showSaleInvoceMoneyPayForm', 'invoice_id':invoice_id, 'pay_id':pay_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal("show");
		$("#FormModalBody2").html(result.content);
		$("#FormModalLabel2").html("������ ��������");
		numberOnlyPlace("sale_invoice_kredit");
		numberOnlyPlace("cash_kours");
	}}, true);
}

function saveSaleInvoiceMoneyPay(){
    let invoice_id=$("#pay_invoice_id").val();
    let pay_id=$("#pay_id").val();
    let kredit=parseFloat($("#sale_invoice_kredit").val()).toFixed(2);
    let pay_type_id=$("#pay_type_id option:selected").val();
    let paybox_id=$("#paybox_id option:selected").val();
    let doc_cash_id=$("#doc_cash_id").val();
    let cash_id=$("#cash_id option:selected").val();
    let cash_kours=$("#cash_kours").val();
	if (invoice_id<=0 || invoice_id==""){
		toastr["error"](errs[0]);
	}
	if (invoice_id>0) {
		if (kredit<=0) {
			toastr["error"]("������ ���� ������");
		}
		if (kredit>0) {
			JsHttpRequest.query($rcapi,{ 'w': 'saveSaleInvoiceMoneyPay', 'invoice_id':invoice_id, 'pay_id':pay_id, 'kredit':kredit, 'pay_type_id':pay_type_id, 'paybox_id':paybox_id, 'doc_cash_id':doc_cash_id, 'cash_id':cash_id, 'cash_kours':cash_kours},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				if (result["answer"]==1){
					showSaleInvoceMoneyPayForm(invoice_id, result["pay_id"]);
					//$("#FormModalWindow3").modal('hide');document.getElementById("FormModalBody3").innerHTML="";document.getElementById("FormModalLabel3").innerHTML="";
					loadSaleInvoiceMoneyPay(invoice_id);
				} else {
					toastr["error"](result["error"]);
				}
			}}, true);
		}
	}
}

function unlockKours(id){
	let invoice_id = $("#pay_invoice_id").val();
	let pay_id = $("#pay_id").val();
	if (invoice_id <= 0 || invoice_id == "") {
		toastr["error"](errs[0]);
	}
	if (invoice_id > 0) {
		JsHttpRequest.query($rcapi,{ 'w': 'unlockSaleInvoiceMoneyPayKours', 'invoice_id':invoice_id, 'pay_id':pay_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"] == 1) {
				$("" + id).attr("type", "text");
				$("" + id).removeAttr("disabled");
			} else {
				toastr["error"](result["error"]);
			}
		}}, true);
	}
}

function getCashKours(){
	let cash_id = $("#cash_id").val();
	let doc_cash_id = $("#doc_cash_id").val();
	if (cash_id <= 0 || cash_id == "") {
		toastr["error"](errs[0]);
	}
	if (cash_id > 0) {
		JsHttpRequest.query($rcapi,{ 'w': 'getCashKoursSaleInvoiceMoneyPay', 'doc_cash_id':doc_cash_id, 'cash_id':cash_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#cash_kours").val(result["cash_kours"]);
			} else {
				toastr["error"](result["error"]);
			}
		}}, true);
	}
}

function loadSaleInvoicePartitions(invoice_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'loadSaleInvoicePartitions', 'invoice_id':invoice_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#partiotion_place").html(result.content);
		$("#sale_invoice_tabs").tab();
	}}, true);
}

function formCatalogueModalLabel() {
	document.getElementById("CatalogueModalLabel").innerHTML="������������| �볺��: "+$("#client_conto_id option:selected").html()+"; ��������: "+$("#DpCardLabel").html()+"; �����: "+$("#dp_summ").val()+"; ������: "+$("#cash_id option:selected").html();
	return true;
}

function showArticleSearchDocumentForm(i, art_id, brand_id, article_nr_displ, doc_type, dp_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showArticleSearchDocumentForm', 'brand_id':brand_id, 'article_nr_displ':article_nr_displ, 'doc_type':doc_type, 'doc_id':dp_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CatalogueModalWindow").modal("show");
		$("#CatalogueModalLabel").html("");
		$("#CatalogueModalBody").html(result.content);
		formCatalogueModalLabel();
		$("#row_pos").val(i);
		$("#datatable_parrent").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
	}}, true);
}

function setArticleToSelectAmountDp(art_id, article_nr_displ, brand_id, brand_name, dp_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'setArticleToSelectAmountDp', 'art_id':art_id, 'dp_id':dp_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal("show");
		$("#FormModalBody2").html(result.content);
		$("#FormModalLabel2").html("������ �������: "+article_nr_displ+" "+brand_name);
		$("#art_idS2").val(art_id);
		$("#article_nr_displS2").val(article_nr_displ);
		$("#brand_idS2").val(brand_id);
		$("#brand_nameS2").val(brand_name);
	}}, true);
}

function showDpAmountInputWindow(art_id, storage_id) {
    let dp_id = $("#dp_id").val();
	JsHttpRequest.query($rcapi,{ 'w': 'showDpAmountInputWindow', 'art_id':art_id, 'dp_id':dp_id, 'storage_id':storage_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow3").modal("show");
        $("#FormModalBody3").html(result.content);
        $("#FormModalLabel3").html("������ �������");
		numberOnlyPlace("amount_move_numbers");
		$("#amount_storage_id").val(storage_id);
		setTimeout(function (){$("#amount_select_storage_str").DataTable({keys: true,"aaSorting": [],"order": [[ 3, "asc" ]],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});},500);
	}}, true);
}

function showDpSupplAmountInputWindow(art_id, article_nr_displ, brand_id, brand_name, dp_id, suppl_id, suppl_storage_id, price) {
	JsHttpRequest.query($rcapi,{ 'w': 'showDpSupplAmountInputWindow','art_id':art_id,'article_nr_displ':article_nr_displ,'brand_id':brand_id,'dp_id':dp_id,'suppl_id':suppl_id,'suppl_storage_id':suppl_storage_id,'price':price},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow3").modal("show");
        $("#FormModalBody3").html(result.content);
        $("#FormModalLabel3").html("������ �������: " + article_nr_displ + " " + brand_name);
		numberOnlyPlace("amount_move_numbers");
	}}, true);
}

function closeAmountInputWindow(art_id){
    let dp_id = $("#dp_id").val();
	if (dp_id.length > 0) {
        let article_nr_displStr = $("#article_nr_displS2").val();
        let brandIdStr = $("#brand_idS2").val();
        let brand_nameS2 = $("#brand_nameS2").val();
		setArticleToSelectAmountDp(art_id, article_nr_displStr, brandIdStr, brand_nameS2, dp_id);
	}
}

function closeAmountSupplInputWindow(){
	$("#FormModalWindow3").modal("hide");
	$("#FormModalBody3").html("");
	$("#FormModalLabel3").html("");
}

function countSumm(pf1,pf2,rf){
    let p1 = parseFloat($("#"+pf1).val().replace(',', '.'));
    let p2 = parseFloat($("#"+pf2).val().replace(',', '.'));
    let summ = parseFloat(p1*p2).toFixed(2);
	$("#" + rf).val(summ);
	return true;
}

function calculateDiscountPrice(pos){
	var rId = $("#idStr_" + pos).val();
	var amount = parseFloat($("#amountStr_"+pos).val().replace(',', '.'));$("#amountStr_"+pos).val(amount);
	var price = parseFloat($("#priceStr_"+pos).val().replace(',', '.'));$("#priceStr_"+pos).val(price);
	var discount = parseFloat($("#discountStr_"+pos).val().replace(',', '.'));$("#priceEndStr_"+pos).val(price_end);
	var max_discount_persent = parseFloat($("#maxDiscountPersentStr_"+pos).val().replace(',', '.'));$("#maxDiscountPersentStr_"+pos).val(max_discount_persent);
	var max_discount_price = parseFloat($("#maxDiscountPriceStr_"+pos).val().replace(',', '.'));$("#maxDiscountPriceStr_"+pos).val(max_discount_price);
	var price_end = 0; var summ = 0;
	var cash_id = $("#cash_id option:selected").val();
	if (discount<=max_discount_persent){
		price_end=parseFloat(price-price*discount/100).toFixed(2);
		if (discount<=max_discount_persent){
			summ=parseFloat(amount*price_end).toFixed(2);
			$("#priceEndStr_"+pos).val(price_end);
			$("#summStr_"+pos).val(summ);
			calculateDpSumm();
			updateDpStrPrice(rId,discount,cash_id,price_end,summ);
		} else {
			toastr["warning"]("�� ������� ���������� ���� ������");
			$("#discountStr_"+pos).val(max_discount_persent);
			setTimeout(function() { calculateDiscountPrice(pos);}, 1000);
		}
	} else {
		toastr["warning"]("�� ������� ���������� ���� ������");
		max_discount_persent=parseFloat($("#maxDiscountPersentStr_"+pos).val());
		$("#discountStr_"+pos).val(max_discount_persent);
		setTimeout(function() { calculateDiscountPrice(pos);}, 1000);
	}
}

function calculateDiscountPriceAll(){
	var pos=0;var id="";var max = 0;
	var list1="";var list2="";
	$(".check_dp").each(function() {max = Math.max(this.id, max);});
	for (pos=1; pos<=max; pos++) {
		if ($("#" + pos).is(":checked")) {
			var discount2=parseFloat($("#discountStr_"+pos).val().replace(',', '.'));
			var rId=$("#idStr_"+pos).val();
			var amount=parseFloat($("#amountStr_"+pos).val().replace(',', '.'));$("#amountStr_"+pos).val(amount);
			var price=parseFloat($("#priceStr_"+pos).val().replace(',', '.'));$("#priceStr_"+pos).val(price);
			var discount=parseFloat($("#discountStr").val().replace(',', '.'));
			var discountPast=parseFloat($("#discountStr").val().replace(',', '.'));
			
			var max_discount_persent=parseFloat($("#maxDiscountPersentStr_"+pos).val().replace(',', '.'));$("#maxDiscountPersentStr_"+pos).val(max_discount_persent);
			var max_discount_price=parseFloat($("#maxDiscountPriceStr_"+pos).val().replace(',', '.'));$("#maxDiscountPriceStr_"+pos).val(max_discount_price);
			
			var price_end=0; var summ=0;
			var cash_id=$("#cash_id option:selected").val();
			if (discount<=max_discount_persent){
				price_end=parseFloat(price-price*discount/100).toFixed(2);
				if (discount<=max_discount_persent){
					summ=parseFloat(amount*price_end).toFixed(2);
					$("#discountStr_"+pos).val(discount);
					$("#priceEndStr_"+pos).val(price_end);
					$("#summStr_"+pos).val(summ);
					calculateDpSumm();
					updateDpStrPrice(rId,discount,cash_id,price_end,summ);
				}else{
					$("#discountStr_"+pos).val(max_discount_persent);
					$("#priceEndStr_"+pos).val(price_end);
				}
			}else{
				price_end2=parseFloat(price-price*max_discount_persent/100).toFixed(2);
				list2=list2+"#"+pos+" � ������� - "+discount2+" � ����� - "+price+"\n";
				max_discount_persent=parseFloat($("#maxDiscountPersentStr_"+pos).val());
				$("#discountStr_"+pos).val(max_discount_persent);
				$("#priceEndStr_"+pos).val(price_end2);
				price_end2=0;
				table = $("#dp_str").DataTable( {retrieve: true} );
				table.destroy();
				table = $("#dp_str").DataTable( {retrieve: true,"order": [[ 6, "asc" ]]} );
			}
		}
	}
	if (list2 == "") {swal("�������� ��������!", "������ "+discount+" ���� ����������� ��� ��� �������� �������!", "success");}
	else {swal("�����", "������ "+discount+"% �� ���� ����������� ��� ��� �������� �������. ����� ������������ ����� ����������� ��������� ������, ���� ����� ����������� � �����������.", "error");}
	$("#discountStr").val("");
}

function calculateDiscountPersent(pos){
	var rId=$("#idStr_"+pos).val();
	var amount=parseFloat($("#amountStr_"+pos).val().replace(',', '.'));$("#amountStr_"+pos).val(amount);
	var price=parseFloat($("#priceStr_"+pos).val().replace(',', '.'));$("#priceStr_"+pos).val(price);
	var price_end=parseFloat($("#priceEndStr_"+pos).val().replace(',', '.'));$("#priceEndStr_"+pos).val(price_end);
	var max_discount_persent=parseFloat($("#maxDiscountPersentStr_"+pos).val().replace(',', '.'));$("#maxDiscountPersentStr_"+pos).val(max_discount_persent);
	var max_discount_price=parseFloat($("#maxDiscountPriceStr_"+pos).val().replace(',', '.'));$("#maxDiscountPriceStr_"+pos).val(max_discount_price);
	var summ=0;	var discount=0;
	var cash_id=$("#cash_id option:selected").val();
	if (price_end>=max_discount_price){
		discount=parseFloat(((price_end/price)-1)*100*(-1)).toFixed(2);
		if (discount<=max_discount_persent){
			summ=parseFloat(amount*price_end).toFixed(2);
			$("#discountStr_"+pos).val(discount);
			$("#summStr_"+pos).val(summ);
			calculateDpSumm();
			updateDpStrPrice(rId,discount,cash_id,price_end,summ);
			if (discount<0){toastr["error"]("ֳ�� ���� �� ��������");}
		}else{
			//toastr["warning"]("�� ������� ���������� ���� ������ discount="+discount+"<=max_discount_persent="+max_discount_persent);
			$("#discountStr_"+pos).val(max_discount_persent);
			setTimeout(function() { calculateDiscountPrice(pos);}, 1000);
		}
	}else{
		toastr["warning"]("�� ������� ���������� ���� ������");
		price_end=max_discount_price;
		$("#priceEndStr_"+pos).val(price_end);
		setTimeout(function() { calculateDiscountPersent(pos);}, 1000);
	}
}

function updateDpStrPrice(rId,discount,cash_id,price_end,summ){
	let dp_id=$("#dp_id").val();
	if (dp_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'updateDpStrPrice', 'dp_id':dp_id, 'str_id':rId, 'discount':discount, 'cash_id':cash_id, 'price_end':price_end, 'summ':summ},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			//updated
		}}, true);
	}
}

function calculateDpSumm(){
	var dp_summ=0;
	var kol_row=$("#kol_row").val();
	for (var i=1;i<=kol_row;i++){
		summ_str=parseFloat($("#summStr_"+i).val());
		dp_summ=dp_summ+summ_str;
	}
	dp_summ=parseFloat(dp_summ).toFixed(2);
	$("#dp_summ").val(dp_summ);
}

function setArticleToDp(art_id){
	var dp_id=$("#dp_id").val();
	if (dp_id.length>0){
		var artIdStr=art_id;
		var tpoint_id=$("#tpoint_id").val();
		var article_nr_displStr=$("#article_nr_displS2").val();
		var brandIdStr=$("#brand_idS2").val();
		var brand_nameS2=$("#brand_nameS2").val();
		var amount_move=parseFloat($("#amount_move").val());
		var amountStr=amount_move;
		var storageIdStr=$("#amount_storage_id").val();
		
		if (amountStr>0){
			if (storageIdStr>0 && art_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'setArticleToDp','dp_id':dp_id,'tpoint_id':tpoint_id,'artIdStr':artIdStr,'article_nr_displStr':article_nr_displStr,'brandIdStr':brandIdStr,'storageIdStr':storageIdStr,'amountStr':amountStr},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){
						$("#dp_weight").html(result["weight"]);
						$("#dp_volume").html(result["volume"]);
						$("#dp_summ").val(result["dp_summ"]);

						JsHttpRequest.query($rcapi,{ 'w': 'showDpCardStr', 'dp_id':dp_id},
						function (result, errors){ if (errors) {alert(errors);} if (result){  
							$("#dp_doc_range").html(result.content);
							numberOnly();
						}}, true);
						
						$("#FormModalWindow3").modal("hide");
						$("#FormModalBody3").html("");
						$("#FormModalLabel3").html("");
						formCatalogueModalLabel();
						setArticleToSelectAmountDp(art_id,article_nr_displStr,brandIdStr,brand_nameS2,dp_id);
					} else {
						swal("�������!", result["error"], "error"); 
						setArticleToSelectAmountDp(art_id,article_nr_displStr,brandIdStr,brand_nameS2,dp_id);
					}
				}}, true);
			}else{swal("�������!", "��������� ������� � ������ ������� �������", "error"); }
		}else{swal("�������!", "ʳ������ ��� ���������� �� ���� ����� 0", "error"); }
	}
}

function setArticleSupplToDp(art_id){
	var dp_id=$("#dp_id").val();
	if (dp_id.length>0){
		//var tpoint_id=$("#tpoint_id").val();
		var article_nr_displ=$("#article_nr_displStr").val();
		var brandId=$("#brand_idStr").val();
		var amount_move=parseFloat($("#amount_move").val());
		var amountStr=amount_move;
		var supplId=$("#suppl_idStr").val();
		var supplStorageId=$("#suppl_storage_idStr").val();
		
		if (amountStr>0){
			if (supplStorageId>0 && supplId>0 && art_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'setArticleSupplToDp','dp_id':dp_id,'art_id':art_id,'article_nr_displ':article_nr_displ,'brandId':brandId,'supplId':supplId,'supplStorageId':supplStorageId,'amountStr':amountStr},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){
						$("#dp_weight").html(result["weight"]);
						$("#dp_volume").html(result["volume"]);
						$("#dp_summ").val(result["dp_summ"]);

						JsHttpRequest.query($rcapi,{ 'w': 'showDpCardStr', 'dp_id':dp_id},
						function (result, errors){ if (errors) {alert(errors);} if (result){  
							$("#dp_doc_range").html(result.content);
							numberOnly();
						}}, true);
						
						$("#FormModalWindow3").modal("hide");
						$("#FormModalBody3").html("");
						$("#FormModalLabel3").html("");
						formCatalogueModalLabel();
						setArticleToSelectAmountDp(art_id,article_nr_displStr,brandIdStr,brand_nameS2,dp_id);
					} else{
						swal("�������!", result["error"], "error"); 
					}
				}}, true);
			}else{swal("�������!", "��������� ������� � ������ ������� �������", "error"); }
		}else{swal("�������!", "ʳ������ ��� ���������� �� ���� ����� 0", "error"); }
	}
}

function catalogue_article_storage_rest_search(search_type){
	var art=$("#catalogue_art").val();
	var brand_id=0;
	if ($("#list2_art").val().length>0){
		art=$("#list2_art").val();$("#list2_art").val("");
		brand_id=$("#list2_brand_id").val();$("#list2_brand_id").val("");
	}
	if (art.length<=2){ $("#srchInG").addClass("has-error");/*	toastr["warning"](errs[1]);*/}
	if (art.length>2){$("#srchInG").removeClass("has-error");
		$("#waveSpinnerCat_place").html(waveSpinner);
		$("#catalogue_range").empty();
		var dp_id=$("#dp_id").val();
		var tpoint_id=$("#tpoint_id").val();
		
		JsHttpRequest.query($rcapi,{ 'w': 'catalogue_article_storage_rest_search_dp', 'art':art, 'brand_id':brand_id, 'search_type':search_type, 'dp_id':dp_id, 'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["brand_list"]!="" && result["brand_list"]!=null && search_type==0){
				$("#FormModalWindow2").modal("show");
                $("#FormModalBody2").html(result["brand_list"]);
                $("#FormModalLabel2").html(mess[0]);
			}
			if (result["brand_list"]=="" || result["brand_list"]==null || search_type>0){
				$("#catalogue_range").html(result["content"]);
				$("#waveSpinnerCat_place").html("");
			}
		}}, true);
	}
}

function createTaxInvoice(invoice_id){
	var invoice_id=$("#invoice_id").val();
	if (invoice_id.length>0){
		swal({
			title: "�������� ��������� ��������?",text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",confirmButtonText: "���", cancelButtonText: "³������", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w':'createTaxInvoice', 'invoice_id':invoice_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("�������� ��������� ��������!", "�������� �"+result["tax_id"], "success");	
						showSaleInvoiceCard(invoice_id);
					} else { swal("�������!", result["error"], "error");}
				}}, true);
			}else {swal("³������", "", "error");}
		});
	}
}

function loadDpStorsel(dp_id){
	if (dp_id<=0 || dp_id==""){toastr["error"](errs[0]);}
	if (dp_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadDpStorsel', 'dp_id':dp_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#dp_storsel_place").html(result.content);
		}}, true);
	}
}

function viewDpStorageSelect(dp_id,select_id,select_status){
	if (dp_id<=0 || dp_id=="" || select_id=="" || select_id==0){toastr["error"](errs[0]);}
	if (dp_id>0 && select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'viewDpStorageSelect', 'dp_id':dp_id, 'select_id':select_id, 'select_status':select_status},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow2").modal("show");
            $("#FormModalBody2").html(result["content"]);
            $("#FormModalLabel2").html(result["header"]);
			$("#dp_tabs").tab();
		}}, true);
	}
}

function loadDpSaleInvoice(dp_id){
	if (dp_id<=0 || dp_id==""){toastr["error"](errs[0]);}
	if (dp_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadDpSaleInvoice', 'dp_id':dp_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#dp_sale_invoice_place").html(result["content"]);
        }}, true);
	}
}

function showDpStorselForSaleInvoice(dp_id){
	if (dp_id<=0 || dp_id==""){toastr["error"](errs[0]);}
	if (dp_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showDpStorselForSaleInvoice', 'dp_id':dp_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html("������ ������ ��� ���������� ��������� ��������");
		}}, true);
	}
}

function sendDpStorselToSaleInvoice(dp_id){
	var dp_id=$("#dp_id").val();
	if (dp_id.length>0){
		var kol_storsel=$("#kol_storsel").val();
		var ar_storsel=[]; var sel=0;
		for (var i=1;i<=kol_storsel;i++){
			if (document.getElementById("dp_strosel_"+i).checked){ar_storsel[i]=$("#dp_strosel_"+i).val();sel=1; }
		}
		if (sel==1){
			JsHttpRequest.query($rcapi,{ 'w':'sendDpStorselToSaleInvoice', 'dp_id':dp_id, 'kol_storsel':kol_storsel, 'ar_storsel':ar_storsel},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					$("#FormModalWindow").modal("hide");
                    $("#FormModalBody").html("");
                    $("#FormModalLabel").html("");
					swal("�������� ����������!", "����� ��������: "+result["sale_invoice_nom"], "success"); 
				} else {
					swal("�������!", result["error"], "error"); 
				}
			}}, true);
		} else {swal("�������!", "��� ���������� �������� ������ ���� � ���� ����", "error"); }
	}
}

function viewDpSaleInvoice(dp_id,invoice_id){
	if (dp_id<=0 || dp_id=="" || invoice_id=="" || invoice_id==0){toastr["error"](errs[0]);}
	if (dp_id>0 && invoice_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'viewDpSaleInvoice', 'dp_id':dp_id, 'invoice_id':invoice_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow2").modal("show");
            $("#FormModalBody2").html(result["content"]);
            $("#FormModalLabel2").html(result["header"]);
			$("#dp_tabs").tab();
		}}, true);
	}
}

function openSaleInvoice(invoice_id){
	if (invoice_id=="" || invoice_id==0){toastr["error"](errs[0]);}
	if (invoice_id>0){
		window.open("/SaleInvoice/view/"+invoice_id,"_blank");
	}
}

function loadDpMoneyPay(dp_id) {
	if (dp_id <= 0 || dp_id == "") {
		toastr["error"](errs[0]);
	}
	if (dp_id > 0) {
		JsHttpRequest.query($rcapi,{ 'w': 'loadDpMoneyPay', 'dp_id':dp_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#dp_money_pay_place").html(result.content);
		}}, true);
	}
}

function getPartitionsInvoiceAmount(partition_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'getPartitionsInvoiceAmount', 'partition_id':partition_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#ModalPartiotionAmount").modal("show");
		$("#partition_id").val(result.content[0]);
		$("#invoice_amount").val(result.content[1]);
		$("#invoice_id_tab").val(result.content[2]);
	}}, true);
}

function savePartitionsInvoiceAmount() {
    let invoice_amount 	= $("#invoice_amount").val();
    let partition_id 	= $("#partition_id").val();
    let invoice_id_tab	= $("#invoice_id_tab").val();
	JsHttpRequest.query($rcapi,{ 'w': 'savePartitionsInvoiceAmount', 'partition_id':partition_id, 'invoice_amount':invoice_amount}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		toastr["info"]("��������!");
		$("#ModalPartiotionAmount").modal("hide");
		loadSaleInvoicePartitions(invoice_id_tab);
	}}, true);
}

function showCbCheckForm() {
	let invoice_id 	= $("#cb_check_invoice_id").val();
	let type_id 	= $("#cb_check_doc_type_id").val();
	JsHttpRequest.query($rcapi,{ 'w': 'showCbCheckForm', 'invoice_id':invoice_id, 'type_id':type_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#CheckBoxWindow").modal("show");
			$("#CheckBoxBody").html(result["content"]);
		}}, true);
}

function addCbCheck() {
	$("#btn-add-cb").prop('disabled', true);
	let invoice_id 	= $("#cb_check_invoice_id").val();
	let type_id 	= $("#cb_check_doc_type_id").val();
	let payment_id 	= $('input[name="cb_payment"]:checked').val();
	let email 		= $("#cb_check_email").val();
	JsHttpRequest.query($rcapi,{ 'w':'addCbCheck', 'invoice_id':invoice_id, 'type_id':type_id, 'payment_id':payment_id, 'email':email},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				$("#CheckBoxWindow").modal("hide");
				swal("���������!", "������� ���� ���� ������ ���������.", "success");
				showSaleInvoiceCard(invoice_id);
			} else {
				swal("�������!", result["error"], "error");
			}
		}}, true);
}

function showCbCheck() {
	let invoice_id 	= $("#cb_check_invoice_id").val();
	let type_id 	= $("#cb_check_doc_type_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCbCheck', 'invoice_id':invoice_id, 'type_id':type_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"] == 1) {
				window.open(
					"https://check.checkbox.ua/" + result["check_id"],
					'_blank'
				);
			} else {
				swal("�������!", result["error"], "error");
			}
		}}, true);
}


