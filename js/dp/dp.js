var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

$(document).ready(function() {
    shortcut.add("Insert", function() {
		var  dp_id=$("#dp_id").val();
		var  jmoving_type_id=$("#jmoving_type_id").val();
		if (dp_id>0){
			if (jmoving_type_id==0){addNewRowLocal();}
			if (jmoving_type_id==1){addNewRow();}
		}
	});
	$(document).bind('keydown', 'ctrl+a', function(){ ShowCheckAll2();});
	$(document).bind('keydown', 'a', function(){ ShowCheckAll2();});
	$(document).bind('keydown', 'insert', function(){ addNewRow(); });
	$(document).bind('keydown', 'p', function(){ ShowModalAll(); });
	$(document).bind('keydown', 'f2', function(){ document.getElementById("discountStr").focus()});		

	setTimeout(function(){
		var status=$('#update_status').val();
		if (status==="true") {updateDpRange(false);}
		
	},15*1000);
});

$(window).bind('beforeunload', function(e){
    if($('#dp_id')){
		closeDpCard();
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

function getDpNote(dp_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'getDpNote', 'dp_id':dp_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#dp_note_place").html(result["content"]);
    }}, true);
}

function setDpNote(dp_id) {
	var text=$("#dp_note_field").val();
	JsHttpRequest.query($rcapi,{ 'w':'setDpNote','dp_id':dp_id, 'text':text},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ swal("Збережено!", "Усі дані успішно збережені!", "success"); }
		else{ swal("Помилка!", result["error"], "error"); }
	}}, true);
}

function dropDpNote(dp_id) {
	//var text=$("#dp_note_field").val();
	JsHttpRequest.query($rcapi,{ 'w':'dropDpNote','dp_id':dp_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ swal("Видалено!", "Усі дані успішно видалені!", "success"); $("#dp_note_field").val("");}
		else{ swal("Помилка!", result["error"], "error"); }
	}}, true);
}

function autoUpdateDp(press_btn){
	var status=$('#update_status').val();
	if (press_btn) {
        status==="true" ? status=true : status=false;
		if (status){
			$('#update_status').val('false');
			$('#toggle_update').html("<i class='fa fa-play'></i>");		
		}
		else{
			$('#update_status').val('true');
			$('#toggle_update').html("<i class='fa fa-stop-circle'></i>");
		}	
	}
	updateDpRange();
}

function filterDpsList() {
	var filStatus=$("#filStatusMain option:selected").val();
	var filAuthor=$("#filAuthorMain option:selected").val();
	var filTpoint=$("#filTpointMain option:selected").val();
	var status=$('#input_done').val();
	JsHttpRequest.query($rcapi,{ 'w': 'show_dp_search_filter', 'status':status, 'filStatus':filStatus, 'filAuthor':filAuthor, 'filTpoint':filTpoint}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$('#datatable').DataTable().destroy();
		$("#dp_range").html(result["content"]);
		$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
}
 
function updateDpRange(press_btn){
	var status=$('#input_done').val();
	if (press_btn) {
		status==="true" ? status=true : status=false;
		if (status){
			$('#input_done').val('false');
			$('#toggle_done').html("<i class='fa fa-eye-slash'></i>");		
		}
		else  {
			$('#input_done').val('true');
			$('#toggle_done').html("<i class='fa fa-eye'></i>");
		}	
	} else {
		status==="true" ? status=false : status=true;
	}
	var prevRange=$("#dp_range").html();
	JsHttpRequest.query($rcapi,{ 'w': 'show_dp_search', 'status':status}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		if (prevRange.length != result["content"].length){
			$('#datatable').DataTable().destroy();
			$("#dp_range").html(result["content"]);
			$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}
		status=$('#update_status').val();
		if (status==="true") {setTimeout(function(){updateDpRange();},15*1000);}
	}}, true);
}

function ShowModalAll() {
	var pos=0; var max=0; var list="";
	$('.check_dp').each(function() {max = Math.max(this.id, max);});
	for (pos=1; pos<=max; pos++) {
		if ($('#' + pos).is(":checked")) {list=list+pos;}
	}
	if(list==="") swal("Помилка", "Спочатку виберіть хоч одне значення!", "error");
	else {$('#FormModalWindowAll').modal('show');}
}

function ShowCheckAll() {$('#checkAll').change(function () {$('input:checkbox').prop('checked', $(this).prop('checked'));});}

function ShowCheckAll2() {$("#checkAll").prop("checked", false).trigger("click");}

function check_all_storsel() {$('#all_strosel').change(function () {$('input:checkbox.ch_dp_sts').prop('checked', $(this).prop('checked'));});}

function show_dp_search(inf){
	$("#dp_range").empty();
	JsHttpRequest.query($rcapi,{ 'w': 'show_dp_search'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#dp_range").html(result["content"]);
        if (inf===1 || inf==="1"){toastr["info"]("Виконано!");}
	}}, true);
} 

function fixDeliveryCarrier(){
	var delivery_id=$("#delivery_type_id option:selected").val();
	var delivery_address=$("#delivery_address");
	if(delivery_id===58) {
		$("#delivery_type_storage").removeClass("disabled").removeClass("hidden");
        delivery_address.addClass("disabled").addClass("hidden");
		var text = $("#delivery_type_storage option:selected").text();
        delivery_address.val(text);
	} else {
        delivery_address.removeClass("disabled").removeClass("hidden");
		$("#delivery_type_storage").addClass("disabled").addClass("hidden");
        delivery_address.val("");
	}
	if (delivery_id!==60){ $("#carrier_id").addClass("disabled").addClass("hidden"); }
	else{ $("#carrier_id").removeClass("disabled").removeClass("hidden"); }
}

function setDeliveryAddress() {
	var text=$("#delivery_type_storage option:selected").text();
	$("#delivery_address").val(text);
}

function preNewDpCard(){
	$("#FormModalWindow3").modal("show");
	JsHttpRequest.query($rcapi,{ 'w': 'preNewDpCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#FormModalBody3").html(result["content"]);
        $("#FormModalLabel3").html("Оберіть тип накладної");
	}}, true);
}

function newDpCard(){
	$("#FormModalWindow3").modal("hide");
	JsHttpRequest.query($rcapi,{ 'w': 'newDpCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		var dp_id=result["dp_id"];
		showDpCard(dp_id);
		show_dp_search(0);
	}}, true);
}

function showDpCard(dp_id){
	if (dp_id<=0 || dp_id===""){toastr["error"](errs[0]);}
	if (dp_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showDpCard', 'dp_id':dp_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#DpCard").modal('show');
            $("#DpCardBody").html(result["content"]);
            $("#DpCardLabel").html(result["doc_prefix_nom"]);
			$('#dp_tabs').tab();
			//$("#cash_id").select2({placeholder: "Виберіть валюту",dropdownParent: $("#DpCard")});
			$('#data_pay').datepicker({format: "yyyy-mm-dd",autoclose:true})
			$('.i-checks').iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
			setTimeout(function (){
				//var dtable=$('#dp_str').DataTable(); dtable.destroy();
				$('#dp_str').DataTable({keys: true,"aaSorting": [],"scrollX": true,"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});},500);
			//$("#carrier_id").select2({placeholder: "Виберіть склад",dropdownParent: $("#DpCard")});
			numberOnly();
		}}, true);
	}
}

function unlockDpCard(dp_id){
	if (dp_id){
		JsHttpRequest.query($rcapi,{ 'w': 'unlockDpCard', 'dp_id':dp_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#DpCard").modal('hide');
            $("#DpCardBody").html("");
            $("#DpCardLabel").html("");
		}}, true);
	}else{
		$("#DpCard").modal('hide');
        $("#DpCardBody").html("");
        $("#DpCardLabel").html("");
	}
}

function closeDpCard(){
	if ($("#dp_id")){
		var dp_id=$("#dp_id").val();
		JsHttpRequest.query($rcapi,{ 'w': 'closeDpCard', 'dp_id':dp_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#DpCard").modal('hide');
            $("#DpCardBody").html("");
            $("#DpCardLabel").html("");
		}}, true);
	}else{
		$("#DpCard").modal('hide');
        $("#DpCardBody").html("");
        $("#DpCardLabel").html("");
	}
}

function addNewRow(){
	var client_id=$("#client_id").val();
	if (client_id===0 || client_id.length===0){
		swal("Помилка!", "Оберіть спочатку клієнта", "error");
	} else {
		var row=$("#dpStrNewRow").html();
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
		row=row.replace('summStr_0', 'summStr_'+kol_row);
		row=row.replace('discountStr_0', 'discountStr_'+kol_row);
		row=row.replace("id='dpStrNewRow' class='hidden'", " id='strRow_"+kol_row+"'");
		var tbody=$("#dp_str tbody").append("<tr>"+row+"</tr>");
		showArticleSearchDocumentForm('i_0','0','0','','dp',''+$("#dp_id").val());
		setTimeout(function (){
			//var dtable=$('#dp_str').DataTable(); dtable.destroy();
			$('#dp_str').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});},500);
	}
	return true;
}

function saveDpCard(){
	var dp_id=$("#dp_id").val();
	var data_pay=$("#data_pay").val();
	var cash_id=$("#cash_id option:selected").val();
	var doc_type_id=$("#doc_type_id option:selected").val();
	var tpoint_id=$("#tpoint_id").val();
	var client_id=$("#client_id").val();
	var client_conto_id=$("#client_conto_id option:selected").val();
	var delivery_type_id=$("#delivery_type_id option:selected").val();
	var carrier_id=$("#carrier_id option:selected").val();
	var delivery_address=$("#delivery_address").val();
	var dp_summ=$("#dp_summ").val();
	var ikr=$("#kol_row").val(); ikr_p=Math.ceil(ikr/20);
	if (dp_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveDpCard','dp_id':dp_id,'data_pay':data_pay,'cash_id':cash_id,'dp_summ':dp_summ,'doc_type_id':doc_type_id,'tpoint_id':tpoint_id,'client_id':client_id,'client_conto_id':client_conto_id,'delivery_type_id':delivery_type_id,'carrier_id':carrier_id,'delivery_address':delivery_address},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				for(var p=1;p<=ikr_p;p++){
					var idStr=[];var artIdStr=[]; var article_nr_displStr=[]; var brandIdStr=[]; var amountStr=[]; var priceStr=[]; var priceEndStr=[];  var discountStr=[]; var summStr=[]; //var storageIdFromStr=[];  var cellIdFromStr=[];
					var frm=(p-1)*20; tto=frm+20; if (tto>ikr){tto=ikr;}
					for (var i=frm;i<=tto;i++){
						idStr[i]=$("#idStr_"+i).val(); artIdStr[i]=$("#artIdStr_"+i).val(); article_nr_displStr[i]=$("#article_nr_displStr_"+i).val(); brandIdStr[i]=$("#brandIdStr_"+i).val();
						amountStr[i]=$("#amountStr_"+i).val(); priceStr[i]=$("#priceStr_"+i).val(); priceEndStr[i]=$("#priceEndStr_"+i).val(); discountStr[i]=$("#discountStr_"+i).val(); summStr[i]=$("#summStr_"+i).val(); 
					}
					JsHttpRequest.query($rcapi,{ 'w':'saveDpCardData','dp_id':dp_id,'cash_id':cash_id,'frm':frm,'tto':tto,'idStr':idStr,'artIdStr':artIdStr,'article_nr_displStr':article_nr_displStr,'brandIdStr':brandIdStr,'amountStr':amountStr,'priceStr':priceStr,'priceEndStr':priceEndStr,'discountStr':discountStr,'summStr':summStr},
					function (result1, errors1){ if (errors1) {alert(errors1);} if (result1){  
						if (result1["answer"]==1){ }
						else{ swal("Помилка!", result1["error"], "error");}
					}}, true);
				}
//				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
//				closeDpCard()
//				show_dp_search(0);
			}
			else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function getClientPaymentDelay(){
	var client_id=$("#client_conto_id option:selected").val();
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getClientPaymentDelay', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
				$("#data_pay").val(result["data_pay"]);
			}else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function getClientDocType(){
	var client_id=$("#client_conto_id option:selected").val();
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getDpClientDocType', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
				$("#doc_type_id").val(result["doc_type_id"]);
				saveDpCard();
			}else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function getClientContoCash(){
	var client_id=$("#client_conto_id option:selected").val();
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getDpClientContoCash', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#cash_id option:selected").val(result["cash_id"]);
				changeDpCash(result["cash_id"]);
			}else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function changeDpCash(){
	var cash_id=$("#cash_id option:selected").val();
	var dp_id=$("#dp_id").val();
	JsHttpRequest.query($rcapi,{ 'w': 'changeDpCash', 'dp_id':dp_id, 'cash_id':cash_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			showDpCard(dp_id);
		}else{ 
			swal("Помилка!", result["error"], "error");
			showDpCard(dp_id);
		}
	}}, true);
}

function showDpDocumentList(dp_id){
	var jmoving_op_id=$("#jmoving_op_id option:selected").val();
	var document_id=$("#document_id").val();
	$("#FormModalWindow").modal("show");
	JsHttpRequest.query($rcapi,{ 'w': 'showDpDocumentList', 'dp_id':dp_id, 'jmoving_op_id':jmoving_op_id, 'document_id':document_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#FormModalBody").html(result["content"]);
        $("#FormModalLabel").html(result["header"]);
	}}, true);
}

function findDpDocumentsSearch(dp_id,jmoving_op_id){
	var s_nom=$("#form_document_search").val();
	JsHttpRequest.query($rcapi,{ 'w': 'findDpDocumentsSearch', 'dp_id':dp_id, 'jmoving_op_id':jmoving_op_id, 's_nom':s_nom}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#documents_search_result").html(result["content"]);
    }}, true);
}

function setDocumentToForm(document_id,document_name){
	$("#document_id").val(document_id);
	$("#document_name").val(document_name);
	$("#FormModalWindow").modal("hide");
    $("#FormModalBody").html("");
    $("#FormModalLabel").html("");
}

function loadDpCDN(dp_id){
	if (dp_id<=0 || dp_id===""){toastr["error"](errs[0]);}
	if (dp_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadDpCDN', 'dp_id':dp_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#dp_cdn_place").html(result["content"]);
        }}, true);
	}
}

function showDpCDNUploadForm(dp_id){
	$("#cdn_dp_id").val(dp_id);
	var myDropzone2 = new Dropzone("#myDropzone2",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone2.removeAllFiles(true);
	myDropzone2.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$('#fileDpCDNUploadForm').modal('hide');
		loadDpCDN(dp_id);
	});
}

function showDpCDNDropConfirmForm(dp_id,file_id,file_name){
	if (dp_id<=0 || dp_id===""){toastr["error"](errs[0]);}
	if (dp_id>0){
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'dpCDNDropFile', 'dp_id':dp_id, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadDpCDN(dp_id); toastr["info"]("Файл успішно видалено"); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function loadDpCommetsLabel(dp_id){
	if (dp_id<=0 || dp_id===""){toastr["error"](errs[0]);}
	if (dp_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadDpCommetsLabel', 'dp_id':dp_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#label_comments").html(result["label"]);
		}}, true);
	}
}

function loadDpCommets(dp_id){
	if (dp_id<=0 || dp_id===""){toastr["error"](errs[0]);}
	if (dp_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadDpCommets', 'dp_id':dp_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#dp_commets_place").html(result["content"]);
        }}, true);
	}
}

function saveDpComment(dp_id){
	if (dp_id<=0 || dp_id===""){toastr["error"](errs[0]);}
	if (dp_id>0){
		var comment=$("#dp_comment_field").val();
		if (comment.length<=0){toastr["error"]("Напишіть коментар спочатку");}
		if (comment.length>0){
			JsHttpRequest.query($rcapi,{ 'w': 'saveDpComment', 'dp_id':dp_id, 'comment':comment}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					loadDpCommets(dp_id); 
					$("#dp_comment_field").val("");
					loadDpCommetsLabel(dp_id);
				}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function dropDpComment(dp_id,cmt_id){
	if (dp_id<=0 || dp_id===""){toastr["error"](errs[0]);}
	if (dp_id>0){
		if(confirm('Видалити запис?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'dropDpComment', 'dp_id':dp_id, 'cmt_id':cmt_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadDpCommets(dp_id);
					toastr["info"]("Запис успішно видалено");
					loadDpCommetsLabel(dp_id);
				}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function loadDpClientContoList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'loadDpClientContoList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#client_conto_id").html(result["content"]);
		getClientContoCash();
		getClientDocType();
	}}, true);
}

function showDpClientList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showDpClientList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
        $("#FormModalBody").html(result["content"]);
        $("#FormModalLabel").html("Контрагенти");
		setTimeout(function() { $('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}}); }, 500);
	}}, true);
}

function filterMdlClientsList(){
	var sel_id=$('#client_id').val();
	var client_id=$("#filMdlClientId").val();
	var client_name=$("#filMdlClientName").val();
	var phone=$("#filMdlPhone").val();
	var email=$("#filMdlEmail").val();
	var state_id=$("#filMdlState option:selected").val();
	$("#client_range").empty();
	JsHttpRequest.query($rcapi,{ 'w': 'filterDpClientsList', 'sel_id':sel_id, 'client_id':client_id, 'client_name':client_name, 'phone':phone, 'email':email, 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#client_range").html(result["content"]);
	}}, true);
}

function ClearMdlClientSearch(){
	$("#filMdlClientId").val("");
	$("#filMdlClientName").val("");
	$("#filMdlPhone").val("");
	$("#filMdlEmail").val("");
	$("#filMdlState option:selected").val(0);
	filterMdlClientsList();
}

function setDpClient(id,name,tpoint_id,tpoint_name){
	var dp_id=$("#dp_id").val();
	$('#client_id').val(id);
	name = name.replace("`", '"');
	name = name.replace("`", '"');
	$('#client_name').val(name);
	setDpTpoint(tpoint_id,tpoint_name);
	if (dp_id<=0 || dp_id===""){toastr["error"](errs[0]);}
	if (dp_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'setDpClient', 'dp_id':dp_id, 'client_id':id,'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#FormModalWindow").modal('hide');document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";
				loadDpClientContoList(id);
			}
			else{ toastr["error"](result["error"]);}
		}}, true);
	}
}

function getDpTpointInfo() {
	var client_id=$("#client_id_input").val(); 
	if (client_id<=0 || client_id==="" || client_id===undefined){toastr["error"](errs[0]);}
	if (client_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'getDpTpointInfo', 'client_id':client_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			setDpClient(client_id,result.content[0],result.content[1],result.content[2]);
		}}, true);
	}
}

function unlinkDpClient(dp_id){
	swal({
		title: "Відв`язати клієнта від накладної?",text: "Внесені Вами зміни вплинуть на роботу Контрагента",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'unlinkDpClient', 'dp_id':dp_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					$("#client_id").val("0");
					$("#client_name").val("");
					$("#client_conto_id").html("<option value='0'>-- оберіть зі списку --</option>");
					swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
				}
				else{ toastr["error"](result["error"]); }
			}}, true);	
		} else { swal("Відмінено", "Операцію анульовано.", "error"); }
	});
}

function showDpTpointList(tpoint_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showDpTpointList', 'tpoint_id':tpoint_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		$("#FormModalBody").html(result["content"]);
		$("#FormModalLabel").html("Торгові точки");
		setTimeout(function() { $('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}}); }, 500);
	}}, true);
}

function setDpTpoint(id,name){
	$('#tpoint_id').val(id);
	$('#tpoint_name').val(name);
	$("#FormModalWindow").modal('hide');
    $("#FormModalBody").html("");
    $("#FormModalLabel").html("");
	saveDpCard();
}

function unlinkDpTpoint(dp_id){
	swal({
		title: "Відвязати торгову точку від накладної?",text: "Внесені Вами зміни вплинуть на роботу",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'unlinkDpTpoint', 'dp_id':dp_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					$('#tpoint_id').val("0");
					$('#tpoint_name').val("");
					swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
				}
				else{ toastr["error"](result["error"]); }
			}}, true);	
		} else {
			swal("Відмінено", "Операцію анульовано.", "error");
		}
	});
}

function clearDpStr(dp_id){
	if (dp_id<=0 || dp_id==""){toastr["error"](errs[0]);}
	if (dp_id>0){
		swal({
			title: "Очистити структуру документу?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Очистити", cancelButtonText: "Відміна", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w': 'clearDpStr', 'dp_id':dp_id}, 
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Успішно!", "Структуру накладної очищено!", "success");
						showDpCard(dp_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
	}
}

function dropDpStr(pos,dp_id,dp_str_id){
	if (dp_id<=0 || dp_id==""){toastr["error"](errs[0]);}
	if (dp_id>0 && dp_str_id>0){
		swal({
			title: "Видалити артикул з замовлення?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так, видалити", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				if (dp_id.length>0){
					JsHttpRequest.query($rcapi,{ 'w':'dropDpStr','dp_id':dp_id,'dp_str_id':dp_str_id},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"]==1){
							swal("Видалено!", "", "success");
							$("#strRow_"+pos).html("");
							$("#strRow_"+pos).attr('visibility','hidden');
							document.getElementById("dp_summ").value=result["dp_summ"];
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

function formCatalogueModalLabel(){
	document.getElementById("CatalogueModalLabel").innerHTML="Номенклатура| клієнт: "+$("#client_conto_id option:selected").html()+"; документ: "+$("#DpCardLabel").html()+"; Сумма: "+$("#dp_summ").val()+"; валюта: "+$("#cash_id option:selected").html();
	return true;
}

function showArticleSearchDocumentForm(i,art_id,brand_id,article_nr_displ,doc_type,dp_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showArticleSearchDocumentForm', 'art_id':art_id,'brand_id':brand_id,'article_nr_displ':article_nr_displ,'doc_type':doc_type,'doc_id':dp_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CatalogueModalWindow").modal('show');
        $("#CatalogueModalBody").html(result["content"]);
        $("#CatalogueModalLabel").html("");
		formCatalogueModalLabel();
		$("#row_pos").val(i);
		$('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
	}}, true);
}

function showDpArticleSearchForm(i,art_id,brand_id,article_nr_displ,dp_id,tpoint_id){
	var dp_id=$("#dp_id").val();
	if (tpoint_id=="" || tpoint_id==0){tpoint_id=$("#tpoint_id").val();}
	if (tpoint_id=="" || tpoint_id==0){ swal("Помилка!", "Оберіть Торгову точку переміщення", "error"); } else {
		JsHttpRequest.query($rcapi,{ 'w': 'showDpArticleSearchForm', 'art_id':art_id,'brand_id':brand_id,'article_nr_displ':article_nr_displ,'dp_id':dp_id,'tpoint_id':tpoint_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html("Номенклатура");
			$("#row_pos").val(i);
			$('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
		}}, true);
	}
}

function setArticleToSelectAmountDp(art_id,article_nr_displ,brand_id,brand_name,dp_id){
	JsHttpRequest.query($rcapi,{ 'w': 'setArticleToSelectAmountDp','art_id':art_id,'dp_id':dp_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
        $("#FormModalBody2").html(result["content"]);
        $("#FormModalLabel2").html("Вкажіть кількість: "+article_nr_displ+" "+brand_name);
		$('#art_idS2').val(art_id);
		$('#article_nr_displS2').val(article_nr_displ);
		$('#brand_idS2').val(brand_id);
		$('#brand_nameS2').val(brand_name);
	}}, true);
}

function showDpAmountInputWindow(art_id,storage_id){
	var dp_id=$("#dp_id").val();
	JsHttpRequest.query($rcapi,{ 'w': 'showDpAmountInputWindow','art_id':art_id,'dp_id':dp_id,'storage_id':storage_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow3").modal('show');
        $("#FormModalBody3").html(result["content"]);
        $("#FormModalLabel3").html("Вкажіть кількість");
		numberOnlyPlace("amount_move_numbers");
		$("#amount_storage_id").val(storage_id);
		setTimeout(function (){$('#amount_select_storage_str').DataTable({keys: true,"aaSorting": [],"order": [[ 3, "asc" ]],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});},500);
	}}, true);
}

function showDpSupplAmountInputWindow(art_id,article_nr_displ,brand_id,brand_name,dp_id,suppl_id,suppl_storage_id,price){
	JsHttpRequest.query($rcapi,{ 'w': 'showDpSupplAmountInputWindow','art_id':art_id,'article_nr_displ':article_nr_displ,'brand_id':brand_id,'dp_id':dp_id,'suppl_id':suppl_id,'suppl_storage_id':suppl_storage_id,'price':price}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow3").modal('show');
        $("#FormModalBody3").html(result["content"]);
        $("#FormModalLabel3").html("Вкажіть кількість: "+article_nr_displ+" "+brand_name);
		numberOnlyPlace("amount_move_numbers");
	}}, true);
}

function closeAmountInputWindow(art_id){
	var dp_id=$("#dp_id").val();
	if (dp_id.length>0){
		var article_nr_displStr=$('#article_nr_displS2').val();
		var brandIdStr=$('#brand_idS2').val();
		var brand_nameS2=$('#brand_nameS2').val();
		setArticleToSelectAmountDp(art_id,article_nr_displStr,brandIdStr,brand_nameS2,dp_id);
	}
}

function closeAmountSupplInputWindow(){
	$("#FormModalWindow3").modal('hide');
    $("#FormModalBody3").html("");
    $("#FormModalLabel3").html("");
}

function countSumm(pf1,pf2,rf){
	var p1=parseFloat($("#"+pf1).val().replace(',', '.'));
	var p2=parseFloat($("#"+pf2).val().replace(',', '.'));
	var summ=parseFloat(p1*p2).toFixed(2);
	$("#"+rf).val(summ);
	return true;
}

function calculateDiscountPrice(pos){
	var rId=$("#idStr_"+pos).val();
	var amount=parseFloat($("#amountStr_"+pos).val().replace(',', '.'));$("#amountStr_"+pos).val(amount);
	var price=parseFloat($("#priceStr_"+pos).val().replace(',', '.'));$("#priceStr_"+pos).val(price);
	var discount=parseFloat($("#discountStr_"+pos).val().replace(',', '.'));$("#priceEndStr_"+pos).val(price_end);
	var max_discount_persent=parseFloat($("#maxDiscountPersentStr_"+pos).val().replace(',', '.'));$("#maxDiscountPersentStr_"+pos).val(max_discount_persent);
	var max_discount_price=parseFloat($("#maxDiscountPriceStr_"+pos).val().replace(',', '.'));$("#maxDiscountPriceStr_"+pos).val(max_discount_price);
	var price_end=0; var summ=0;
	var cash_id=$("#cash_id option:selected").val();
	if ((discount<=max_discount_persent && max_discount_persent>=0) || (discount==0)){
		price_end=parseFloat(price-price*discount/100).toFixed(2);
		if (discount<=max_discount_persent || discount==0){
			summ=parseFloat(amount*price_end).toFixed(2);
			$("#priceEndStr_"+pos).val(price_end);
			$("#summStr_"+pos).val(summ);
			calculateDpSumm();
			updateDpStrPrice(rId,discount,cash_id,price_end,summ);
		}else{
			toastr["warning"]("Не можливо призначити таку знижку");
			$("#discountStr_"+pos).val(max_discount_persent);
			setTimeout(function() { calculateDiscountPrice(pos);}, 1000);
		}
	}else{
		toastr["warning"]("Не можливо призначити таку знижку");
		if (max_discount_persent>=0){
			max_discount_persent=parseFloat($("#maxDiscountPersentStr_"+pos).val());
			$("#discountStr_"+pos).val(max_discount_persent);
		}
		if (max_discount_persent<0){
			$("#discountStr_"+pos).val(0);
			$("#priceEndStr_"+pos).val(price);
		}
		setTimeout(function() { calculateDiscountPrice(pos);}, 1000);
	}
}

function calculateDiscountPriceAll(){
	var pos=0;var id='';var max = 0;
	var list1='';var list2='';
	$('.check_dp').each(function() {max = Math.max(this.id, max);});
	var ud=$("#uncontrolUserDiscount").val();
	console.log("ud="+ud);
	for (pos=1; pos<=max; pos++) {
		if ($('#' + pos).is(":checked")) {
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
			if (discount<=max_discount_persent || ud==1){
				price_end=parseFloat(price-price*discount/100).toFixed(2);
				if (discount<=max_discount_persent || ud==1){
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
				list2=list2+"#"+pos+" зі знижкою - "+discount2+" і ціною - "+price+"\n";
				max_discount_persent=parseFloat($("#maxDiscountPersentStr_"+pos).val());
				$("#discountStr_"+pos).val(max_discount_persent);
				$("#priceEndStr_"+pos).val(price_end2);
				price_end2=0;
				table = $('#dp_str').DataTable( {retrieve: true} );
				table.destroy();
				table = $('#dp_str').DataTable( {retrieve: true,"order": [[ 6, "asc" ]]} );
			}
		}
	}
	if (list2 == '') {swal("Операцію виконано!", "Знижка "+discount+" була встановлена для усіх помічених позицій!", "success");} 
	else {swal("Увага", "Знижка "+discount+"% не була встановлена для усіх помічених позицій. Товар відсортований згідно встановленої додаткової знижки, будь ласка ознайомтесь з результатом.", "error");}
	list2='';
	$("#discountStr").val('');
}

function calculateDiscountPersent(pos){
	var rId=$("#idStr_"+pos).val();
	var amount=parseFloat($("#amountStr_"+pos).val().replace(',', '.'));$("#amountStr_"+pos).val(amount);
	var price=parseFloat($("#priceStr_"+pos).val().replace(',', '.'));$("#priceStr_"+pos).val(price);
	var price_end=parseFloat($("#priceEndStr_"+pos).val().replace(',', '.'));$("#priceEndStr_"+pos).val(price_end);
	var discount=parseFloat($("#discountStr_"+pos).val().replace(',', '.'));$("#discountStr_"+pos).val(discount);
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
			
			updateDpStrPrice(rId,discount,cash_id,price_end,summ);
			if (discount<0 && price_end<price){
				toastr["error"]("Ціна вища за прайсову;");
				discount=0;max_discount_price=0;
				$("#discountStr_"+pos).val(discount);
				$("#priceEndStr_"+pos).val(price);
			}
			if (discount<0 && price_end>=price){
				discount=parseFloat(((price_end/price)-1)*100*(-1)).toFixed(2);
				$("#discountStr_"+pos).val(discount);
				updateDpStrPrice(rId,discount,cash_id,price_end,summ);
			}
			//calculateDpSumm();
		}else{
			//toastr["warning"]("Не можливо призначити таку знижку discount="+discount+"<=max_discount_persent="+max_discount_persent);
			$("#discountStr_"+pos).val(max_discount_persent);
			setTimeout(function() { calculateDiscountPrice(pos);}, 1000);
		}
		calculateDpSumm();
	}else{
		if (price_end<price){
			toastr["warning"]("Не можливо призначити таку знижку");
			price_end=max_discount_price;
			$("#priceEndStr_"+pos).val(price_end);
			discount=parseFloat(((price_end/price)-1)*100*(-1)).toFixed(2);
			$("#discountStr_"+pos).val(discount);
			setTimeout(function() { calculateDiscountPersent(pos);}, 1000);
		}
		if (price_end>=price){
			discount=parseFloat(((price_end/price)-1)*100*(-1)).toFixed(2);
			$("#discountStr_"+pos).val(discount);
			updateDpStrPrice(rId,discount,cash_id,price_end,summ);
		}
		calculateDpSumm();
	}
}

function updateDpStrPrice(rId,discount,cash_id,price_end,summ){
	var dp_id=$("#dp_id").val();
	if (dp_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'updateDpStrPrice', 'dp_id':dp_id,'str_id':rId,'discount':discount,'cash_id':cash_id,'price_end':price_end,'summ':summ},
		function (result, errors){ if (errors) {alert(errors);} if (result){ 
			//showDpCard(dp_id);
			JsHttpRequest.query($rcapi,{ 'w': 'showDpCardStr', 'dp_id':dp_id},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				document.getElementById("dp_doc_range").innerHTML=result["content"];
				numberOnly();
				calculateDpSumm();
			}}, true);
		}}, true);
	}
}

function calculateDpSumm(){
	var dp_summ=0;
	var kol_row=$("#kol_str_row").val();
	var summ_str=0;
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
		var tpoint_id=$('#tpoint_id').val();
		var article_nr_displStr=$('#article_nr_displS2').val();
		var brandIdStr=$('#brand_idS2').val();
		var brand_nameS2=$('#brand_nameS2').val();
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
							document.getElementById("dp_doc_range").innerHTML=result["content"];
							numberOnly();
							setTimeout(function (){//var dtable=$('#dp_str').DataTable(); dtable.destroy();
								$('#dp_str').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});
							},500);
						}}, true);
                        showDpCard(dp_id);
						$("#FormModalWindow3").modal('hide');
						$("#FormModalBody3").html("");
						$("#FormModalLabel3").html("");
						formCatalogueModalLabel();
						setArticleToSelectAmountDp(art_id,article_nr_displStr,brandIdStr,brand_nameS2,dp_id);
					}
					else{ 
						swal("Помилка!", result["error"], "error"); 
						setArticleToSelectAmountDp(art_id,article_nr_displStr,brandIdStr,brand_nameS2,dp_id);
					}
				}}, true);
			}else{swal("Помилка!", "Неможливо відібрати зі складу обраний артикул", "error"); }
		}else{swal("Помилка!", "Кількість для замовлення має бути більша 0", "error"); }
	}
}

function setArticleSupplToDp(art_id){
	var dp_id=$("#dp_id").val();
	if (dp_id.length>0){
		var tpoint_id=$('#tpoint_id').val();
		var article_nr_displ=$('#article_nr_displStr').val();
		var brandId=$('#brand_idStr').val();
		var amount_move=parseFloat($("#amount_move").val());var amountStr=amount_move;
		var supplId=$("#suppl_idStr").val();
		var supplStorageId=$("#suppl_storage_idStr").val();
		
		if (amountStr>0){
			if (supplStorageId>0 && supplId>0 && art_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'setArticleSupplToDp','dp_id':dp_id,'tpoint_id':tpoint_id,'art_id':art_id,'article_nr_displ':article_nr_displ,'brandId':brandId,'supplId':supplId,'supplStorageId':supplStorageId,'amountStr':amountStr},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){
                        $("#dp_weight").html(result["weight"]);
                        $("#dp_volume").html(result["volume"]);
                        $("#dp_summ").val(result["dp_summ"]);
//						document.getElementById("label_un_articles").innerHTML=result["label_empty"];
						JsHttpRequest.query($rcapi,{ 'w': 'showDpCardStr', 'dp_id':dp_id},
						function (result, errors){ if (errors) {alert(errors);} if (result){
							$("#dp_doc_range").html(result["content"]);
							numberOnly();
						}}, true);
						
						$("#FormModalWindow3").modal('hide');
                        $("#FormModalBody3").html("");
                        $("#FormModalLabel3").html("");
						formCatalogueModalLabel();
						setArticleToSelectAmountDp(art_id,article_nr_displStr,brandIdStr,brand_nameS2,dp_id);
					}
					else{ 
						swal("Помилка!", result["error"], "error"); 
					}
				}}, true);
			}else{swal("Помилка!", "Неможливо відібрати зі складу обраний артикул", "error"); }
		}else{swal("Помилка!", "Кількість для замовлення має бути більша 0", "error"); }
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
				$("#FormModalWindow2").modal('show');
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

function startDpExecute(){
	var dp_id=$("#dp_id").val();
	if (dp_id.length>0){
		swal({
			title: "Передати в роботу замовлення?",text: "Подальше внесення змін буде заблоковано", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w':'startDpExecute','dp_id':dp_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						if (result["suppl_ex"]==1){
							swal({
								title: "Уточнення?",text: "У замовленні знаходяться позиції з віддалених складів. Зробити переміщення цих товарів до Вашої торгової точки (Переміщення), або відвантажити товар одразу клієнту (Відправка)?", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",confirmButtonText: "Переміщення", cancelButtonText: "Відправка", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
							},
							function (isConfirm) {
								if (isConfirm) {
									JsHttpRequest.query($rcapi,{ 'w': 'makeDpJmovingStorselPreorder', 'dp_id':dp_id,'local':41},
									function (result, errors){ if (errors) {alert(errors);} if (result){  
										if (result["answer"]==1){
											showDpCard(dp_id);
											swal("Замовленя передано в роботу!", "", "success");
										}
									}}, true);
								}
								else{ 
									JsHttpRequest.query($rcapi,{ 'w': 'makeDpJmovingStorselPreorder', 'dp_id':dp_id,'local':42},
									function (result, errors){ if (errors) {alert(errors);} if (result){  
										if (result["answer"]==1){
											showDpCard(dp_id);
											swal("Замовленя було розділено на декілька замовлень  і передано в роботу!", "", "success");
										}
									}}, true);
								}
							});
						}
						if (result["suppl_ex"]==0){
							showDpCard(dp_id);
							swal("Замовленя передано в роботу!", "", "success");
						}
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			} else {swal("Відмінено", "", "error");}
		});
	}
}

function loadDpJmoving(dp_id){
	if (dp_id<=0 || dp_id==""){toastr["error"](errs[0]);}
	if (dp_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadDpJmoving', 'dp_id':dp_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#dp_jmoving_place").html(result["content"]);
		}}, true);
	}
}

function loadDpStorsel(dp_id){
	if (dp_id<=0 || dp_id==""){toastr["error"](errs[0]);}
	if (dp_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadDpStorsel', 'dp_id':dp_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#dp_storsel_place").html(result["content"]);
        }}, true);
	}
}

function viewDpStorageSelect(dp_id,select_id,select_status){
	if (dp_id<=0 || dp_id=="" || select_id=="" || select_id==0){toastr["error"](errs[0]);}
	if (dp_id>0 && select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'viewDpStorageSelect', 'dp_id':dp_id,'select_id':select_id,'select_status':select_status}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow2").modal('show');
			$("#FormModalBody2").html(result["content"]);
			$("#FormModalLabel2").html(result["header"]);
			$('#dp_tabs').tab();
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
			$("#FormModalWindow").modal('show');
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html("Оберіть відбори для формування видаткової накладної");
		}}, true);
	}
}

function sendDpStorselToSaleInvoice(dp_id){
	//var dp_id=$("#dp_id").val();
	if (dp_id.length>0){
	$("#send_dp").attr("disabled", true); //disable button
		var kol_storsel=$('#kol_storsel').val();
		var ar_storsel=[]; var sel=0;
		for (var i=1;i<=kol_storsel;i++){
			if (document.getElementById("dp_strosel_"+i)){
				if (document.getElementById("dp_strosel_"+i).checked){ar_storsel[i]=$("#dp_strosel_"+i).val();sel=1; }
			}
		}
		if (sel==1){
			JsHttpRequest.query($rcapi,{ 'w':'sendDpStorselToSaleInvoice','dp_id':dp_id,'kol_storsel':kol_storsel,'ar_storsel':ar_storsel},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					$("#FormModalWindow").modal('hide');
					$("#FormModalBody").html("");
					$("#FormModalLabel").html("");
					swal({
						title: "Документ сформовано!", text: "Номер накладної: "+result["sale_invoice_prefix"], type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",confirmButtonText: "Готово", cancelButtonText: "Друкувати документ", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
					},
						function (isConfirm) {
							if (isConfirm) {
								//swal("Документ відправлено!", "Номер накладної: "+result["sale_invoice_prefix"], "success"); 
								swal.close();
								$("#FormModalWindow").modal('hide');document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";
								closeDpCard();
								updateDpRange();
							}
							else {
								printSaleInvoceFromDp(result["sale_invoice_nom"]);				 
							}
						}
					);
				}
				if (result["answer"]==2){
					swal({
						title: "Уточнення!",text: result["error"], type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",confirmButtonText: "Відобразити", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
					},
					function (isConfirm) {
						if (isConfirm) {
							JsHttpRequest.query($rcapi,{ 'w':'viewDpDatapayLimitSaleInvoice','dp_id':dp_id},
							function (result, errors){ if (errors) {alert(errors);} if (result){  
								swal.close();
								$("#FormModalWindow2").modal('show');
                                $("#FormModalBody2").html(result["content"]);
                                $("#FormModalLabel2").html(result["header"]);
								setTimeout(function(){
									$('#dp_sale_invoice_data_pay_list').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": false,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
								},500);
							}}, true);
						} else {swal("Відмінено", "", "error");}
					});
				}
				if (result["answer"]==0){ 
					swal("Помилка!", result["error"], "error"); 
				}
			}}, true);
		} else {swal("Помилка!", "Для формування накладної оберіть хоча б один відбір", "error"); }
	}
}

function viewDpSaleInvoice(dp_id,invoice_id){
	if (dp_id<=0 || dp_id=="" || invoice_id=="" || invoice_id==0){toastr["error"](errs[0]);}
	if (dp_id>0 && invoice_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'viewDpSaleInvoice', 'dp_id':dp_id,'invoice_id':invoice_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow2").modal('show');
            $("#FormModalBody2").html(result["content"]);
            $("#FormModalLabel2").html(result["header"]);
			$('#dp_tabs').tab();
		}}, true);
	}
}

function openSaleInvoice(invoice_id){
	if (invoice_id=="" || invoice_id==0){toastr["error"](errs[0]);}
	if (invoice_id>0){
		window.open("/SaleInvoice/view/"+invoice_id,"_blank");
	}
}

function printSaleInvoce(invoice_id){
	if (invoice_id=="" || invoice_id==0){toastr["error"](errs[0]);}
	if (invoice_id>0){
		window.open("/JournalDp/printSlIv/"+invoice_id,"_blank","printWindow");
	}
}

function printSaleInvoceFromDp(invoice_id){
	if (invoice_id=="" || invoice_id==0){toastr["error"](errs[0]);}
	if (invoice_id>0){
		window.open("/SaleInvoice/printSlIv/"+invoice_id,"_blank","printWindow");
	}
}

function printDpSaleInvoce(invoice_id){
	if (invoice_id=="" || invoice_id==0){toastr["error"](errs[0]);}
	if (invoice_id>0){
		window.open("/JournalDp/printDpSlIv/"+invoice_id,"_blank","printWindow");
	}
}

function printDpJournal(invoice_id){
	if (invoice_id=="" || invoice_id==0){toastr["error"](errs[0]);}
	if (invoice_id>0){
		window.open("/JournalDp/printDpJournal/"+invoice_id,"_blank","printWindow");
	}
}

function loadDpMoneyPay(dp_id){
	if (dp_id<=0 || dp_id==""){toastr["error"](errs[0]);}
	if (dp_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadDpMoneyPay', 'dp_id':dp_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#dp_money_pay_place").html(result["content"]);
		}}, true);
	}
}

function printStorselView(select_id){
	if (select_id.length>0){
		window.open("/Storsel/printStS1/"+select_id,"_blank","printWindow");
	}
}

function showOrdersSite(){
	JsHttpRequest.query($rcapi,{ 'w': 'showOrdersSite'}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html("Не оброблені замовлення з сайту");
			$('#orders_site_range').DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
			$('#data_start').datepicker({format: "yyyy-mm-dd",autoclose:true});
			$('#data_end').datepicker({format: "yyyy-mm-dd",autoclose:true});
		}}, true);
}

function showOrderSiteRange(press_btn) {
	var status=$('#input_done1').val();
	if (press_btn) {
        status==="true" ? status=true : status=false;
		if (status){
			$('#input_done1').val('false');
			$('#toggle_done1').html("<i class='fa fa-eye-slash'></i>");		
		}
		else  {
			$('#input_done1').val('true');
			$('#toggle_done1').html("<i class='fa fa-eye'></i>");
		}	
	} else {
        status==="true" ? status=false : status=true;
	}
	JsHttpRequest.query($rcapi,{ 'w': 'showOrderSiteRange', 'status':status}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$('#orders_site_range').DataTable().destroy();
		$("#orders_site_list").html(result["content"]);
		$('#orders_site_range').DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
}

function showOrderSiteRangeFilter(status) {
	$('#input_done1').val('false');
	$('#toggle_done1').html("<i class='fa fa-eye-slash'></i>");	
	var data_start=$("#data_start").val();
	var data_end=$("#data_end").val();
	JsHttpRequest.query($rcapi,{ 'w': 'showOrderSiteRange', 'status':status, 'data_start':data_start, 'data_end':data_end}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$('#orders_site_range').DataTable().destroy();
		$("#orders_site_list").html(result["content"]);
		$('#orders_site_range').DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
}

function showOrdersSiteCard(order_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showOrdersSiteCard', 'order_id':order_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
        $("#FormModalBody2").html(result["content"]);
        $("#FormModalLabel2").html(result["header"]);
	}}, true);
}

function deleteOrderSite(order_id) {
	if (order_id<=0 || order_id==""){toastr["error"](errs[0]);}
	if (order_id>0){
		swal({
			title: "Видалити замовлення?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
					JsHttpRequest.query($rcapi,{'w':'deleteOrderSite','order_id':order_id},
					function (result, errors){ if (errors) {alert(errors);} if (result){  
						if (result["answer"]==1){ 
							swal.close();
							$("#FormModalWindow2").modal('hide');
							$("#FormModalWindow").modal('hide');
							showDpCard(result["dp_id"]);
							show_dp_search(0);
						}
						else{ swal("Помилка!", result["error"], "error");}
					}}, true);
			} else {swal("Відмінено", "", "error");}
		});	
	}
}

function createDpFromOrder(order_id){
	if (order_id<=0 || order_id==""){toastr["error"](errs[0]);}
	if (order_id>0){
		swal({ 
			title: "Передати замовлення в ДП?",text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w': 'createDpFromOrder', 'order_id':order_id}, 
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal.close();
						$("#FormModalWindow2").modal('hide');
						$("#FormModalWindow").modal('hide');
						showDpCard(result["dp_id"]);
						show_dp_search(0);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			} else {swal("Відмінено", "", "error");}
		});
	}
}

function loadDpSiteOrder(dp_id){ 
	if (dp_id<=0 || dp_id===""){toastr["error"](errs[0]);}
	if (dp_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadDpSiteOrder', 'dp_id':dp_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#dp_order_place").html(result["content"]);
        }}, true);
	}
}

function showSupplToLocalChangeForm(pos,art_id,brand_id,article_nr_displ,dp_id,dp_str_id){
	if (art_id>0 && dp_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showSupplToLocalChangeForm', 'art_id':art_id, 'brand_id':brand_id, 'dp_id':dp_id, 'dp_str_id':dp_str_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow2").modal('show');
            $("#FormModalBody2").html(result["content"]);
            $("#FormModalLabel2").html(result["header"]);
		}}, true);
	}
}

function saveDpSupplToLocalChangeForm(dp_id,dp_str_id,art_id){
	if (art_id.length>0 && dp_id.length>0 && dp_str_id.length>0){
		var stock_n=$("#stock_n").val();
		var price=0; var storage_id=0; var cell_id=0; var max_value=0; var amount=0;
		for (i=1;i<=stock_n;i++){
			if ($("#stlca_"+i).val()>0){ i=stock_n;
				amount=parseFloat($("#stlca_"+i).val());
				price=parseFloat($("#stlca_"+i).attr("data-price"));
				storage_id=parseInt($("#stlca_"+i).attr("data-storage"));
				cell_id=parseInt($("#stlca_"+i).attr("data-cell"));
				max_value=parseFloat($("#stlca_"+i).attr("data-maxvalue"));
				console.log(amount+";"+price+";"+storage_id+";"+cell_id+";"+max_value+";");
				if (amount<=max_value && price>0 && storage_id>0){
					JsHttpRequest.query($rcapi,{ 'w': 'saveDpSupplToLocalChangeForm', 'dp_id':dp_id, 'dp_str_id':dp_str_id, 'art_id':art_id, 'amount':amount, 'price':price, 'storage_id':storage_id, 'cell_id':cell_id}, 
					function (result, errors){ if (errors) {alert(errors);} if (result){  
						if (result["answer"]==1){
                            $("#FormModalBody2").html("");
                            $("#FormModalLabel2").html("");
							$("#FormModalWindow2").modal('hide');
							swal("Збережено!", "Артикул передано у роботу!", "success");
							startDpExecute();
						} else { swal("Помилка!", result["error"], "error"); }
					}}, true);
				}
			}
		}
	}
}

