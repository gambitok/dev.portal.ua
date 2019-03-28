var errs=[];
errs[0]="Помилка індексу";

$(document).ready(function() {
    shortcut.add("Insert", function() {
		var  back_id=$("#back_id").val();
		if (back_id>0){
			addNewRow();
		}
	});
	//$(document).bind('keydown', 'ctrl+a', function(){ ShowCheckAll2();});
	//$(document).bind('keydown', 'a', function(){ ShowCheckAll2();});
	//$(document).bind('keydown', 'insert', function(){ addNewRow(); });
	//$(document).bind('keydown', 'p', function(){ ShowModalAll(); });
	//$(document).bind('keydown', 'f2', function(){ document.getElementById("discountStr").focus()});
	setTimeout(function(){updateBackClientsRange();},30*1000);
});

$(window).bind('beforeunload', function(e){
    if($('#back_id')){
		closeBackClientsCard();
		e=null;
	}
    else e=null; 
});

function runScript(e) {
    if (e.keyCode == 13) {
        calculateDiscountPriceAll();
		$('#FormModalWindowAll').modal('hide');
        return false;
    }
}

function filterBackClientsList(){
	var date_start=$("#date_start").val();
	var date_end=$("#date_end").val();
		JsHttpRequest.query($rcapi,{ 'w': 'filterBackClientsList', 'date_start':date_start, 'date_end':date_end}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$('#datatable').DataTable().destroy();
			$("#back_clients_range").html(result["content"]);
			$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}}, true);
} 

function filterBuhBackClientsList(){
	var date_start=$("#date_start").val();
	var date_end=$("#date_end").val();
	JsHttpRequest.query($rcapi,{ 'w': 'filterBuhBackClientsList', 'date_start':date_start, 'date_end':date_end},
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$('#datatable').DataTable().destroy();
		$("#back_clients_range").html(result["content"]);
		$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
} 

function updateBackClientsRange(){
	var prevRange=$("#back_clients_range").html();
	JsHttpRequest.query($rcapi,{ 'w': 'show_back_clients_search'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (prevRange.length!=result["content"].length){
			$("#back_clients_range").empty();
			document.getElementById("back_clients_range").innerHTML=result["content"];
		}
		setTimeout(function(){updateBackClientsRange();},30*1000);
	}}, true);
}

function ShowModalAll() {
	var pos=0;var id='';var max = 0;var list='';
	$('.check_dp').each(function() {max = Math.max(this.id, max);});
	for (pos=1; pos<=max; pos++) {
		if ($('#' + pos).is(":checked")) {list=list+pos;}
	}
	if(list=='') swal("Помилка", "Спочатку виберіть хоч одне значення!", "error");
	else {$('#FormModalWindowAll').modal('show');}
}

function show_back_clients_search(inf){
	$("#back_clients_range").empty();
	JsHttpRequest.query($rcapi,{ 'w': 'show_back_clients_search'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("back_clients_range").innerHTML=result["content"];
		if (inf==1){toastr["info"]("Виконано!");}
	}}, true);
}

function newBackClientsCard(){
	$("#FormModalWindow3").modal("hide");
	JsHttpRequest.query($rcapi,{ 'w': 'newBackClientsCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		var back_id=result["back_id"];
		showBackClientsCard(back_id);
		show_back_clients_search(0);
	}}, true);
}

function showBackClientsCard(back_id){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showBackClientsCard', 'back_id':back_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#BackClientsCard").modal('show');
			document.getElementById("BackClientsCardBody").innerHTML=result["content"];
			document.getElementById("BackClientsCardLabel").innerHTML=result["doc_prefix_nom"];
			$('#back_clients_tabs').tab();
			$('.i-checks').iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
			setTimeout(function (){
				//var dtable=$('#back_clients_str').DataTable(); dtable.destroy();
				$('#back_clients_str').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});},500);
			//$("#carrier_id").select2({placeholder: "Виберіть склад",dropdownParent: $("#BackClientsCard")});
			//$("#tpoint_id").select2({placeholder: "Виберіть ТТ",dropdownParent: $("#BackClientsCard")});
			//$("#storage_id").select2({placeholder: "Виберіть Склад",dropdownParent: $("#BackClientsCard")});
			numberOnly();
		}}, true);
	}
}

function unlockBackClientsCard(back_id){
	if (back_id){
		JsHttpRequest.query($rcapi,{ 'w': 'unlockBackClientsCard', 'back_id':back_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#BackClientsCard").modal('hide');document.getElementById("BackClientsCardBody").innerHTML="";document.getElementById("BackClientsCardLabel").innerHTML="";
		}}, true);
	}else{
		$("#BackClientsCard").modal('hide');document.getElementById("BackClientsCardBody").innerHTML="";document.getElementById("BackClientsCardLabel").innerHTML="";
	}
}

function closeBackClientsCard(){
	if ($("#back_id")){
		var back_id=$("#back_id").val();
		JsHttpRequest.query($rcapi,{ 'w': 'closeBackClientsCard', 'back_id':back_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#BackClientsCard").modal('hide');document.getElementById("BackClientsCardBody").innerHTML="";document.getElementById("BackClientsCardLabel").innerHTML="";
		}}, true);
	}else{
		$("#BackClientsCard").modal('hide');document.getElementById("BackClientsCardBody").innerHTML="";document.getElementById("BackClientsCardLabel").innerHTML="";
	}
}

function addNewRow(){
	var client_id=$("#client_id").val();
	var sale_invoice_id=$("#sale_invoice_id").val();
	if (client_id==0 || client_id.length==0){ 
		swal("Помилка!", "Оберіть спочатку клієнта", "error");
	} 
	if (sale_invoice_id==0 || sale_invoice_id.length==0){ 
		swal("Помилка!", "Оберіть спочатку документ основи", "error");
	} 
	if (client_id.length>0 && sale_invoice_id.length>0) {
		var row=$("#bcStrNewRow").html();
		var kol_row=parseInt($("#kol_str_row").val());
		kol_row+=1;$("#kol_str_row").val(kol_row);
		
		row=row.replace('nom_i', ''+kol_row);
		row=row.replace('i_0', ''+kol_row);
		row=row.replace('i_0', ''+kol_row);
		row=row.replace('idStr_0', 'idStr_'+kol_row);
		row=row.replace('artIdStr_0', 'artIdStr_'+kol_row);
		row=row.replace('article_nr_displStr_0', 'article_nr_displStr_'+kol_row);
		row=row.replace('brandIdStr_0', 'brandIdStr_'+kol_row);
		row=row.replace('brandNameStr_0', 'brandNameStr_'+kol_row);
		row=row.replace('amountStr_0', 'amountStr_'+kol_row);
		row=row.replace('priceStr_0', 'priceStr_'+kol_row);
		row=row.replace('priceSibStr_0', 'priceSibStr_'+kol_row);
		row=row.replace('summStr_0', 'summStr_'+kol_row);
		row=row.replace("id='bcStrNewRow' class='hidden'", " id='strRow_"+kol_row+"'");
		$("#back_clients_str tbody").append("<tr>"+row+"</tr>");
		showSaleInvoiceArticleSearchForm('i_0','0','0',''+$("#back_id").val(),''+$("#sale_invoice_id").val());
		//$i','$si_str_id','$art_id','$brand_id','$article_nr_displ','$back_id','$si_id
		setTimeout(function (){
			//var dtable=$('#back_clients_str').DataTable(); dtable.destroy();
			$('#back_clients_str').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});},500);
	}
	return true;
}

function showBackClientsArticleAmountWindow(art_id,article_nr_displ,brand_name,amount,price,summ,sis_id,max_back){
	var back_id=$("#back_id").val();
	var sale_invoice_id=$("#sale_invoice_id").val();
	JsHttpRequest.query($rcapi,{ 'w': 'showBackClientsArticleAmountWindow','art_id':art_id,'back_id':back_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
		document.getElementById("FormModalBody2").innerHTML=result["content"];
		document.getElementById("FormModalLabel2").innerHTML="Вкажіть кількість: "+article_nr_displ+" "+brand_name;
		$('#article_nr_displS2').val(article_nr_displ);
		console.log("max_back="+max_back);
		$('#max_amount').val(max_back);
		$('#sis_id').val(sis_id);
		numberOnlyPlace("amount_move_numbers");
		$("#amount_back").focus();
	}}, true);
}

function saveBackClientsCard(){
	/*var back_id=$("#back_id").val();
	var data=$("#data_pay").val();
	var cash_id=$("#cash_id option:selected").val();
	var doc_type_id=$("#doc_type_id option:selected").val();
	var tpoint_id=$("#tpoint_id").val();
	var client_id=$("#client_id").val();
	var client_conto_id=$("#client_conto_id option:selected").val();
	var delivery_type_id=$("#delivery_type_id option:selected").val();
	var carrier_id=$("#carrier_id option:selected").val();
	var delivery_address=$("#delivery_address").val();
	var back_clients_summ=$("#back_clients_summ").val();
	var ikr=$("#kol_row").val(); ikr_p=Math.ceil(ikr/20);
	
	if (back_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveBackClientsCard','back_id':back_id,'data_pay':data_pay,'cash_id':cash_id,'back_clients_summ':back_clients_summ,'doc_type_id':doc_type_id,'tpoint_id':tpoint_id,'client_id':client_id,'client_conto_id':client_conto_id,'delivery_type_id':delivery_type_id,'carrier_id':carrier_id,'delivery_address':delivery_address},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				for(var p=1;p<=ikr_p;p++){
					var idStr=[];var artIdStr=[]; var article_nr_displStr=[]; var brandIdStr=[]; var amountStr=[]; var priceStr=[]; var priceEndStr=[];  var discountStr=[]; var summStr=[]; //var storageIdFromStr=[];  var cellIdFromStr=[];
					var frm=(p-1)*20; tto=frm+20; if (tto>ikr){tto=ikr;}
					for (var i=frm;i<=tto;i++){
						idStr[i]=$("#idStr_"+i).val(); artIdStr[i]=$("#artIdStr_"+i).val(); article_nr_displStr[i]=$("#article_nr_displStr_"+i).val(); brandIdStr[i]=$("#brandIdStr_"+i).val();
						amountStr[i]=$("#amountStr_"+i).val(); priceStr[i]=$("#priceStr_"+i).val(); priceEndStr[i]=$("#priceEndStr_"+i).val(); discountStr[i]=$("#discountStr_"+i).val(); summStr[i]=$("#summStr_"+i).val(); 
					}
					JsHttpRequest.query($rcapi,{ 'w':'saveBackClientsCardData','back_id':back_id,'cash_id':cash_id,'frm':frm,'tto':tto,'idStr':idStr,'artIdStr':artIdStr,'article_nr_displStr':article_nr_displStr,'brandIdStr':brandIdStr,'amountStr':amountStr,'priceStr':priceStr,'priceEndStr':priceEndStr,'discountStr':discountStr,'summStr':summStr},
					function (result1, errors1){ if (errors1) {alert(errors1);} if (result1){  
						if (result1["answer"]==1){ }
						else{ swal("Помилка!", result1["error"], "error");}
					}}, true);
				}
//				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
//				closeBackClientsCard()
//				show_back_clients_search(0);
			}
			else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}*/
	swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
}

function showBackClientsDocumentList(back_id){
	var jmoving_op_id=$("#jmoving_op_id option:selected").val();
	var document_id=$("#document_id").val();
	$("#FormModalWindow").modal("show");
	JsHttpRequest.query($rcapi,{ 'w': 'showBackClientsDocumentList', 'back_id':back_id, 'jmoving_op_id':jmoving_op_id, 'document_id':document_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML=result["header"];
	}}, true);
}

function findBackClientsDocumentsSearch(back_id,jmoving_op_id){
	var s_nom=$("#form_document_search").val();
	JsHttpRequest.query($rcapi,{ 'w': 'findBackClientsDocumentsSearch', 'back_id':back_id, 'jmoving_op_id':jmoving_op_id, 's_nom':s_nom}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("documents_search_result").innerHTML=result["content"];
	}}, true);
}

function setDocumentToForm(document_id,document_name){
	$("#document_id").val(document_id);
	$("#document_name").val(document_name);
	$("#FormModalWindow").modal("hide");
	document.getElementById("FormModalBody").innerHTML="";
	document.getElementById("FormModalLabel").innerHTML="";
}

function loadBackClientsCDN(back_id){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadBackClientsCDN', 'back_id':back_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("back_clients_cdn_place").innerHTML=result["content"];
		}}, true);
	}
}

function showBackClientsCDNUploadForm(back_id){
	$("#cdn_back_id").val(back_id);
	var myDropzone2 = new Dropzone("#myDropzone2",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone2.removeAllFiles(true);
	myDropzone2.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$('#fileBackClientsCDNUploadForm').modal('hide');
		loadBackClientsCDN(back_id);
	});
}

function showBackClientsCDNDropConfirmForm(back_id,file_id,file_name){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'dpCDNDropFile', 'back_id':back_id, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadBackClientsCDN(back_id); toastr["info"]("Файл успішно видалено"); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function loadBackClientsCommetsLabel(back_id){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadBackClientsCommetsLabel', 'back_id':back_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("label_comments").innerHTML=result["label"];
		}}, true);
	}
}

function loadBackClientsCommets(back_id){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadBackClientsCommets', 'back_id':back_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("back_clients_commets_place").innerHTML=result["content"];
		}}, true);
	}
}

function saveBackClientsComment(back_id){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		var comment=$("#back_clients_comment_field").val();
		if (comment.length<=0){toastr["error"]("Напишіть коментар спочатку");}
		if (comment.length>0){
			JsHttpRequest.query($rcapi,{ 'w': 'saveBackClientsComment', 'back_id':back_id, 'comment':comment}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					loadBackClientsCommets(back_id); 
					$("#back_clients_comment_field").val("");
					loadBackClientsCommetsLabel(back_id);
				}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function dropBackClientsComment(back_id,cmt_id){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		if(confirm('Видалити запис?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'dropBackClientsComment', 'back_id':back_id, 'cmt_id':cmt_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadBackClientsCommets(back_id); toastr["info"]("Запис успішно видалено");loadBackClientsCommetsLabel(back_id);}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function showBackClientsSaleInvoiceList(){
	var client_id=$("#client_id").val();
	var sale_invoice_id=$("#sale_invoice_id").val();
	if (client_id==0){ toastr["error"]("Оберіть клієнта спочатку");}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showBackClientsSaleInvoiceList', 'client_id':client_id,'sale_invoice_id':sale_invoice_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML="Накладні клієнта";
			setTimeout(function() { $('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}}); }, 500);
		}}, true);
	}
}

function setBackClientsSaleInvoice(id,name,cash_id,cash_name,seller_id){
	var back_id=$("#back_id").val();
	$('#sale_invoice_id').val(id);
	$('#sale_invoice_name').val(name);
	$('#cash_name').val(cash_name);
	JsHttpRequest.query($rcapi,{ 'w': 'setBackClientsSaleInvoice', 'back_id':back_id, 'sale_invoice_id':id,'cash_id':cash_id,'seller_id':seller_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			$("#FormModalWindow").modal('hide'); document.getElementById("FormModalBody").innerHTML=""; document.getElementById("FormModalLabel").innerHTML="";
		}
		else{ toastr["error"](result["error"]); }
	}}, true);
}

function showBackClientsClientList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showBackClientsClientList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Контрагенти";
		setTimeout(function() { $('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}}); }, 500);
	}}, true);
}

function setBackClientsClient(id,tpoint_id,tpoint_name){
	var back_id=$("#back_id").val();
	$("#client_id").val(id);
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'setBackClientsClient', 'back_id':back_id, 'client_id':id,'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#FormModalWindow").modal('hide'); document.getElementById("FormModalBody").innerHTML=""; document.getElementById("FormModalLabel").innerHTML="";
				$("#tpoint_id").val(tpoint_id);
				$("#tpoint_name").val(tpoint_name);
				$("#client_name").val(result["client_name"]);
				//----
				JsHttpRequest.query($rcapi,{ 'w': 'loadBackClientsTpointStorage', 'tpoint_id':tpoint_id}, 
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					document.getElementById("storage_id").innerHTML=result["content"];
				}}, true);
				//----
			}
			else{ toastr["error"](result["error"]); }
		}}, true);
	}
}

function setBackClientsTpointStorage(){
	var back_id=$("#back_id").val();
	var tpoint_id=$("#tpoint_id").val();
	var storage_id=$("#storage_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'setBackClientsTpointStorage', 'back_id':back_id, 'tpoint_id':tpoint_id, 'storage_id':storage_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		//document.getElementById("storage_id").innerHTML=result["content"];
		updateCellsList();
	}}, true);
}

function setBackClientsStorageCell(){
	var back_id=$("#back_id").val();
	var cell_id=$("#cell_id option:selected").val();	console.log(back_id);
		console.log(cell_id);
	JsHttpRequest.query($rcapi,{ 'w': 'setBackClientsStorageCell', 'back_id':back_id, 'cell_id':cell_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		//updated
	}}, true);
}

function updateCellsList(){
	var storage_id=$("#storage_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'showStorageCellsList', 'storage_id':storage_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#cell_id").html(result.content);
	}}, true);
}

function unlinkBackClientsClient(back_id){
	swal({
		title: "Відвязати клієнта від накладної?",text: "Внесені Вами зміни вплинуть на роботу Контагента",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'unlinkBackClientsClient', 'back_id':back_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					$('#client_id').val("0");
					$('#client_name').val("");
					swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
				}
				else{ toastr["error"](result["error"]); }
			}}, true);	
		} else { swal("Відмінено", "Операцію анульовано.", "error"); }
	});
}

function clearBackClientsStr(back_id){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		swal({
			title: "Очистити структуру повернення?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Очистити", cancelButtonText: "Відміна", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w': 'clearBackClientsStr', 'back_id':back_id}, 
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Успішно!", "Структуру повернення очищено!", "success");
						showBackClientsCard(back_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
	}
}

function dropBackClientsStr(pos,back_id,back_str_id){
	if (back_id<=0 || back_id=="" || back_str_id<=0 || back_str_id==""){toastr["error"](errs[0]);}
	if (back_id>0 && back_str_id>0){
	swal({
		title: "Видалити артикул з повернення?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, видалити", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (back_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropBackClientsStr','back_id':back_id,'back_str_id':back_str_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						$("#strRow_"+pos).html("");
						$("#strRow_"+pos).attr('visibility','hidden');
						document.getElementById("back_summ").value=result["back_summ"];
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
	}
}

function createBackClientsTax(){
	var back_id=$("#back_id").val();
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
	swal({
		title: "Створити нову коригуючу податкову накладну?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (back_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'createBackClientsTax','back_id':back_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Створено накладну №"+result["tax_nom"], "", "success");
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
	}
}

function acceptBackClients(){
	var back_id=$("#back_id").val();
	var tpoint_id=$("#tpoint_id option:selected").val();
	var storage_id=$("#storage_id option:selected").val();
	var cell_id=$("#cell_id option:selected").val();
	if (tpoint_id=="0" || storage_id=="0" || cell_id=="0") { 
		swal("Помилка!", "Виберіть всі дані", "error"); 
	} else {
		if (back_id>0 && back_id.length>0){
			JsHttpRequest.query($rcapi,{ 'w': 'acceptBackClients', 'back_id':back_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){ 
				$("#acceptBtn").attr("disabled", true); //disable button
				if (result["answer"]==1){
					showBackClientsCard(back_id);
					setTimeout(function(){
						printBackClients();
					},1000);
				}
				else{ 
					swal("Помилка!", result["error"], "error"); 
					//setArticleToSelectAmountBackClients(art_id,article_nr_displStr,brandIdStr,brand_nameS2,back_id);
				}
			}}, true);
		}
	}
}

function showArticleSearchDocumentForm(i,art_id,brand_id,article_nr_displ,doc_type,back_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showArticleSearchDocumentForm', 'art_id':art_id,'brand_id':brand_id,'article_nr_displ':article_nr_displ,'doc_type':doc_type,'doc_id':back_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CatalogueModalWindow").modal('show');
		document.getElementById("CatalogueModalLabel").innerHTML="";
		document.getElementById("CatalogueModalBody").innerHTML=result["content"];
		formCatalogueModalLabel();
		$("#row_pos").val(i);
		$('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
	}}, true);
}

function showSaleInvoiceArticleSearchForm(i,si_str_id,art_id,back_id,si_id){
	var back_id=$("#back_id").val();
	if (back_id>0 && back_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showSaleInvoiceArticleSearchForm', 'back_id':back_id,'si_id':si_id,'si_str_id':si_str_id,'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');document.getElementById("FormModalBody").innerHTML=result["content"];document.getElementById("FormModalLabel").innerHTML="Товари документа основи";
			$("#row_pos").val(i);
			$('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
		}}, true);
	}
}

function setArticleToSelectAmountBackClients(art_id,article_nr_displ,brand_id,brand_name,back_id){
	JsHttpRequest.query($rcapi,{ 'w': 'setArticleToSelectAmountBackClients','art_id':art_id,'back_id':back_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
		document.getElementById("FormModalBody2").innerHTML=result["content"];
		document.getElementById("FormModalLabel2").innerHTML="Вкажіть кількість: "+article_nr_displ+" "+brand_name;
		$('#art_idS2').val(art_id);
		$('#article_nr_displS2').val(article_nr_displ);
		$('#brand_idS2').val(brand_id);
		$('#brand_nameS2').val(brand_name);
	}}, true);
}

function closeAmountInputWindow(art_id){
	var back_id=$("#back_id").val();
	if (back_id.length>0){
		$("#FormModalWindow2").modal('hide');
		document.getElementById("FormModalBody2").innerHTML="";
		document.getElementById("FormModalLabel2").innerHTML="";
	}
}

function closeAmountSupplInputWindow(){
	$("#FormModalWindow3").modal('hide');
	document.getElementById("FormModalBody3").innerHTML="";document.getElementById("FormModalLabel3").innerHTML="";
}

function countSumm(pf1,pf2,rf){
	var p1=parseFloat($("#"+pf1).val().replace(',', '.'));
	var p2=parseFloat($("#"+pf2).val().replace(',', '.'));
	var summ=0;
	summ=parseFloat(p1*p2).toFixed(2);
	$("#"+rf).val(summ);
	return;
}

function calculateBackClientsSumm(){
	var back_clients_summ=0;
	var kol_row=$("#kol_row").val();
	var sum_str=0;
	for (var i=1;i<=kol_row;i++){
		summ_str=parseFloat($("#summStr_"+i).val());
		back_clients_summ=back_clients_summ+summ_str;
	}back_clients_summ=parseFloat(back_clients_summ).toFixed(2);
	$("#back_clients_summ").val(back_clients_summ);
}

function setArticleToBackClients(){
	var back_id=$("#back_id").val();
	var si_id=$("#sale_invoice_id").val();
	var sis_id=$("#sis_id").val();
	if (back_id.length>0 && si_id>0 && sis_id>0){
		var art_id=$("#art_idS2").val();
		var article_nr_displ=$('#article_nr_displS2').val();
		var amount_back=parseFloat($("#amount_back").val());var amountStr=amount_back;
		var max_amount=$("#max_amount").val();
		if (amountStr>0 && art_id.length>0){
			if (amount_back>max_amount){swal("Помилка!", "Кількість має бути не більша за "+max_amount, "error"); }
			if (amount_back<=max_amount){
				//console.log('w'+'=setArticleToBackClients; back_id='+back_id+'; si_id='+si_id+'; art_id='+art_id+'; article_nr_displ='+article_nr_displ+'; amount_back='+amount_back);
				JsHttpRequest.query($rcapi,{ 'w':'setArticleToBackClients','back_id':back_id,'si_id':si_id,'sis_id':sis_id,'art_id':art_id,'article_nr_displ':article_nr_displ,'amount_back':amount_back},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){
						document.getElementById("back_summ").value=result["back_clients_summ"];
						if (result["back_clients_summ"]>0){
							$("#sale_invoice_name").attr("disabled","disabled");
							$("#client_name").attr("disabled","disabled");
							$("#clientSelBtn").attr("disabled","disabled");
							$("#clientDropBtn").attr("disabled","disabled");
							$("#siSelBtn").attr("disabled","disabled");
							$("#siDropBtn").attr("disabled","disabled");
							$("#acceptBtn").removeAttr("disabled");

						}
						if (result["back_clients_summ"]==0){
							$("#sale_invoice_name").removeAttr("disabled");
							$("#client_name").removeAttr("disabled");
							$("#clientSelBtn").removeAttr("disabled");
							$("#clientDropBtn").removeAttr("disabled");
							$("#siSelBtn").removeAttr("disabled");
							$("#siDropBtn").removeAttr("disabled");
							$("#acceptBtn").attr("disabled","disabled");
						}
						JsHttpRequest.query($rcapi,{ 'w': 'showBackClientsStrList', 'back_id':back_id,'si_id':si_id},
						function (result, errors){ if (errors) {alert(errors);} if (result){  
							document.getElementById("back_clients_doc_range").innerHTML=result["content"];
							numberOnly();
							setTimeout(function (){
								//var dtable=$('#back_clients_str').DataTable(); dtable.destroy();
								$('#back_clients_str').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});},500);
						}}, true);

						closeAmountInputWindow();
						$("#FormModalWindow").modal('hide');document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";
					}
					else{ 
						swal("Помилка!", result["error"], "error"); 
						//setArticleToSelectAmountBackClients(art_id,article_nr_displStr,brandIdStr,brand_nameS2,back_id);
					}
				}}, true);
			}
		}else{swal("Помилка!", "Кількість має бути більша 0", "error"); }
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
		var back_id=$("#back_id").val();
		var tpoint_id=$("#tpoint_id").val();
		JsHttpRequest.query($rcapi,{ 'w': 'catalogue_article_storage_rest_search_dp', 'art':art, 'brand_id':brand_id, 'search_type':search_type, 'back_id':back_id, 'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["brand_list"]!="" && result["brand_list"]!=null && search_type==0){
				$("#FormModalWindow2").modal('show');
				document.getElementById("FormModalBody2").innerHTML=result["brand_list"];
				document.getElementById("FormModalLabel2").innerHTML=mess[0];
			}
			if (result["brand_list"]=="" || result["brand_list"]==null || search_type>0){
				$("#catalogue_range").html(result["content"]);
				$("#waveSpinnerCat_place").html("");
			}
		}}, true);
	}
}

function startBackClientsExecute(){
	var back_id=$("#back_id").val();
	if (back_id.length>0){
		swal({
			title: "Передати в роботу замовлення?",text: "Подальше внесення змін буде заблоковано", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w':'startBackClientsExecute','back_id':back_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						if (result["suppl_ex"]==1){
							swal({
								title: "Уточнення?",text: "У замовленні знаходяться позиції з віддалених складів. Зробити переміщення цих товарів до Вашої торгової точки (Переміщення), або відвантажити товар одразу клієнту (Відправка)?", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",confirmButtonText: "Переміщення", cancelButtonText: "Відправка", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
							},
							function (isConfirm) {
								if (isConfirm) {
									JsHttpRequest.query($rcapi,{ 'w': 'makeBackClientsJmovingStorselPreorder', 'back_id':back_id,'local':41},
									function (result, errors){ if (errors) {alert(errors);} if (result){  
										if (result["answer"]==1){
											showBackClientsCard(back_id);
											swal("Замовленя передано в роботу!", "", "success");
										}
									}}, true);
								}
								else{ 
									JsHttpRequest.query($rcapi,{ 'w': 'makeBackClientsJmovingStorselPreorder', 'back_id':back_id,'local':42},
									function (result, errors){ if (errors) {alert(errors);} if (result){  
										if (result["answer"]==1){
											showBackClientsCard(back_id);
											swal("Замовленя було розділено на декілька замовлень  і передано в роботу!", "", "success");
										}
									}}, true);
								}
							});
						}
						if (result["suppl_ex"]==0){
							showBackClientsCard(back_id);
							swal("Замовленя передано в роботу!", "", "success");
						}
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			} else {swal("Відмінено", "", "error");}
		});
	}
}

function loadBackClientsJmoving(back_id){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadBackClientsJmoving', 'back_id':back_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("back_clients_jmoving_place").innerHTML=result["content"];
		}}, true);
	}
}

function loadBackClientsStorsel(back_id){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadBackClientsStorsel', 'back_id':back_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("back_clients_storsel_place").innerHTML=result["content"];
		}}, true);
	}
}

function viewBackClientsStorageSelect(back_id,select_id,select_status){
	if (back_id<=0 || back_id=="" || select_id=="" || select_id==0){toastr["error"](errs[0]);}
	if (back_id>0 && select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'viewBackClientsStorageSelect', 'back_id':back_id,'select_id':select_id,'select_status':select_status}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow2").modal('show');
			document.getElementById("FormModalBody2").innerHTML=result["content"];
			document.getElementById("FormModalLabel2").innerHTML=result["header"];
			$('#back_clients_tabs').tab();
		}}, true);
	}
}

function loadBackClientsSaleInvoice(back_id){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadBackClientsSaleInvoice', 'back_id':back_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("back_clients_sale_invoice_place").innerHTML=result["content"];
		}}, true);
	}
}

function showBackClientsStorselForSaleInvoice(back_id){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showBackClientsStorselForSaleInvoice', 'back_id':back_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML="Оберіть відбори для формування видаткової накладної";
		}}, true);
	}
}

function sendBackClientsStorselToSaleInvoice(back_id){
	var back_id=$("#back_id").val();
	if (back_id.length>0){
		var kol_storsel=$('#kol_storsel').val();
		var ar_storsel=[]; var sel=0;
		for (var i=1;i<=kol_storsel;i++){
			if (document.getElementById("back_clients_strosel_"+i)){
				if (document.getElementById("back_clients_strosel_"+i).checked){ar_storsel[i]=$("#back_clients_strosel_"+i).val();sel=1; }
			}
		}
		if (sel==1){
			JsHttpRequest.query($rcapi,{ 'w':'sendBackClientsStorselToSaleInvoice','back_id':back_id,'kol_storsel':kol_storsel,'ar_storsel':ar_storsel},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					$("#FormModalWindow").modal('hide');document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";
					swal("Документ сформовано!", "Номер накладної: "+result["sale_invoice_nom"], "success"); 
				}
				if (result["answer"]==2){
					swal({
						title: "Уточнення!",text: result["error"], type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",confirmButtonText: "Відобразити", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
					},
					function (isConfirm) {
						if (isConfirm) {
							JsHttpRequest.query($rcapi,{ 'w':'viewBackClientsDatapayLimitSaleInvoice','back_id':back_id},
							function (result, errors){ if (errors) {alert(errors);} if (result){  
								swal.close();
								$("#FormModalWindow2").modal('show');
								document.getElementById("FormModalBody2").innerHTML=result["content"];
								document.getElementById("FormModalLabel2").innerHTML=result["header"];
								setTimeout(function(){
									$('#back_clients_sale_invoice_data_pay_list').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": false,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
								},500);
							}}, true);
						} else {swal("Відмінено", "", "error");}
					});
				}
				if (result["answer"]==0){ 
					swal("Помилка!", result["error"], "error"); 
				}
			}}, true);
		}else{swal("Помилка!", "Для формування накладної оберіть хоча б один відбір", "error"); }
	}
}
 
function viewBackClientsSaleInvoice(back_id,invoice_id){
	if (back_id<=0 || back_id=="" || invoice_id=="" || invoice_id==0){toastr["error"](errs[0]);}
	if (back_id>0 && invoice_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'viewBackClientsSaleInvoice', 'back_id':back_id,'invoice_id':invoice_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow2").modal('show');
			document.getElementById("FormModalBody2").innerHTML=result["content"];
			document.getElementById("FormModalLabel2").innerHTML=result["header"];
			$('#back_clients_tabs').tab();
		}}, true);
	}
}

function openSaleInvoice(invoice_id){
	if (invoice_id=="" || invoice_id==0){toastr["error"](errs[0]);}
	if (invoice_id>0){
		window.open("/SaleInvoice/view/"+invoice_id,"_blank");
	}
}

function printBackClients(){
	var back_id=$("#back_id").val();
	var status_back=$("#status_back_id").val();
	if (back_id=="" || back_id==0){toastr["error"](errs[0]);}
	if (back_id>0){
		if (status_back==103){
			window.open("/BackClients/printBCn1/"+back_id,"_blank","printWindow");
		}else{
			swal("Помилка!", "Повернення не прийнято", "error"); 
		}
	}
}

function loadBackClientsMoneyPay(back_id){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadBackClientsMoneyPay', 'back_id':back_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("back_clients_money_pay_place").innerHTML=result["content"];
		}}, true);
	}
}

function loadBackClientsPartition(back_id){
	if (back_id<=0 || back_id==""){toastr["error"](errs[0]);}
	if (back_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadBackClientsPartition', 'back_id':back_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("back_clients_partition_form").innerHTML=result["content"];
		}}, true);
	}
}
