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
    if($('#tax_id')){
		//closeSaveInvoiceCard();
		e=null;
	}
    else e=null; 
});

function printTaxInvoice(tax_id){
	if (tax_id=="" || tax_id==0){toastr["error"](errs[0]);}
	if (tax_id>0){
		window.open("/TaxInvoice/printTI/"+tax_id,"_blank","printWindow");
	}
}

function showTaxInvoiceBackCard(tax_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showTaxInvoiceBackCard','tax_id':tax_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#TaxBackCard").modal('show');
		document.getElementById("TaxBackCardBody").innerHTML=result["content"];
		document.getElementById("TaxBackCardLabel").innerHTML="КНН-"+result["doc_nom"];
		numberOnlyPlace("summ");
		$('#tax_invoice_back_tabs').tab();
		$('#data_send').datepicker({format: "yyyy-mm-dd",autoclose:true})
	}}, true);
}

function showTaxInvoiceCard(tax_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showTaxInvoiceCard','tax_id':tax_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#TaxCard").modal('show');
		document.getElementById("TaxCardBody").innerHTML=result["content"];
		document.getElementById("TaxCardLabel").innerHTML="НН-"+result["doc_nom"];
		numberOnlyPlace("summ");
		$('#tax_invoice_tabs').tab();
		$('#data_send').datepicker({format: "yyyy-mm-dd",autoclose:true})
	}}, true);
}

function dropTaxStr(pos,tax_id,tax_str_id){
	console.log("tax_id="+tax_id+"; tax_str_id="+tax_str_id);
	if (tax_id<=0 || tax_id<=0){
		$("#strRow_"+pos).html("");
		$("#strRow_"+pos).attr('visibility','hidden');
	}
	if (tax_id>0 && tax_str_id>0 && tax_str_id!=""){
	swal({
		title: "Видалити товар з накладної?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, видалити", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w':'dropTaxStr','tax_id':tax_id,'tax_str_id':tax_str_id},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Видалено!", "", "success");
					$("#strRow_"+pos).html("");
					$("#strRow_"+pos).attr('visibility','hidden');
					//document.getElementById("tax_summ").value=result["tax_summ"];
				}
				else{ swal("Помилка!", result["error"], "error");}
			}}, true);
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
	}
}

function saveTaxCard(){
	var tax_id=$("#tax_id").val();
	var data_create=$("#data_create").val();
	var data_send=$("#data_send").val();
	var cash_id=$("#cash_id").val();
	var tax_type_id=$("#tax_type_id option:selected").val();
	var tpoint_id=$("#tpoint_id option:selected").val();
	var seller_id=$("#seller_id option:selected").val();
	var client_id=$("#client_id").val();
	var status_tax=$("#status_tax option:selected").val();
	var doc_xml_nom=$("#doc_xml_nom").val();
	var tax_summ=$("#tax_summ").val();
	var ikr=$("#kol_str_row").val(); ikr_p=Math.ceil(ikr/20);
	
	if (tax_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveTaxCard','tax_id':tax_id,'data_create':data_create,'data_send':data_send,'cash_id':cash_id,'tax_summ':tax_summ,'tax_type_id':tax_type_id,'tpoint_id':tpoint_id,'seller_id':seller_id,'client_id':client_id,'status_tax':status_tax,'doc_xml_nom':doc_xml_nom},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ tax_id=result["tax_id"];
				console.log("ikr="+ikr+"; ikr_p="+ikr_p);
				for(var p=1;p<=ikr_p;p++){
					var idStr=[];var zedStr=[]; var goods_nameStr=[]; var amountStr=[]; var priceStr=[]; var summStr=[]; 
					var frm=(p-1)*20; tto=frm+20; if (tto>ikr){tto=ikr;}
					for (var i=frm;i<=tto;i++){
						idStr[i]=$("#idStr_"+i).val(); zedStr[i]=$("#zedStr_"+i).val(); goods_nameStr[i]=$("#goods_nameStr_"+i).val(); 
						amountStr[i]=$("#amountStr_"+i).val(); priceStr[i]=$("#priceStr_"+i).val(); summStr[i]=$("#summStr_"+i).val(); 
					}
					console.log("saveTaxCardData;");
					JsHttpRequest.query($rcapi,{ 'w':'saveTaxCardData','tax_id':tax_id,'frm':frm,'tto':tto,'idStr':idStr,'zedStr':zedStr,'goods_nameStr':goods_nameStr,'amountStr':amountStr,'priceStr':priceStr,'summStr':summStr},
					function (result1, errors1){ if (errors1) {alert(errors1);} if (result1){  
						if (result1["answer"]==1){ }
						else{ swal("Помилка!", result1["error"], "error");}
					}}, true);
				}
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
//				closeDpCard()
				showTaxInvoiceCard(tax_id);
			}
			else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function saveTaxBackCard(){
	var tax_id=$("#tax_id").val();
	var data_create=$("#data_create").val();
	var data_send=$("#data_send").val();
	var cash_id=$("#cash_id").val();
	var tax_type_id=$("#tax_type_id option:selected").val();
	var tax_to_back_id=$("#tax_to_back_id").val();
	var tpoint_id=$("#tpoint_id option:selected").val();
	var seller_id=$("#seller_id option:selected").val();
	var client_id=$("#client_id").val();
	var status_tax=$("#status_tax option:selected").val();
	var doc_xml_nom=$("#doc_xml_nom").val();
	var tax_summ=$("#tax_summ").val();
	var ikr=$("#kol_str_row").val(); ikr_p=Math.ceil(ikr/20);
	
	if (tax_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveTaxBackCard','tax_id':tax_id,'tax_to_back_id':tax_to_back_id,'data_create':data_create,'data_send':data_send,'cash_id':cash_id,'tax_summ':tax_summ,'tax_type_id':tax_type_id,'tpoint_id':tpoint_id,'seller_id':seller_id,'client_id':client_id,'status_tax':status_tax,'doc_xml_nom':doc_xml_nom},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ tax_id=result["tax_id"];
				console.log("ikr="+ikr+"; ikr_p="+ikr_p);
				for(var p=1;p<=ikr_p;p++){
					var idStr=[];var nomStr=[];var tsidStr=[];var zedStr=[]; var goods_nameStr=[]; var amountStr=[]; var priceStr=[]; var summStr=[]; 
					var frm=(p-1)*20; tto=frm+20; if (tto>ikr){tto=ikr;}
					for (var i=frm;i<=tto;i++){
						idStr[i]=$("#idStr_"+i).val();tsidStr[i]=$("#tax_str_idStr_"+i).val(); nomStr[i]=$("#nomStr_"+i).val(); zedStr[i]=$("#zedStr_"+i).val(); goods_nameStr[i]=$("#goods_nameStr_"+i).val(); 
						amountStr[i]=$("#amountStr_"+i).val(); priceStr[i]=$("#priceStr_"+i).val(); summStr[i]=$("#summStr_"+i).val(); 
					}
					console.log("saveTaxBackCardData;");
					JsHttpRequest.query($rcapi,{ 'w':'saveTaxBackCardData','tax_id':tax_id,'frm':frm,'tto':tto,'idStr':idStr,'tsidStr':tsidStr,'nomStr':nomStr,'zedStr':zedStr,'goods_nameStr':goods_nameStr,'amountStr':amountStr,'priceStr':priceStr,'summStr':summStr},
					function (result1, errors1){ if (errors1) {alert(errors1);} if (result1){  
						if (result1["answer"]==1){ }
						else{ swal("Помилка!", result1["error"], "error");}
					}}, true);
				}
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
//				closeDpCard()
				showTaxInvoiceBackCard(tax_id);
			}
			else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function addNewRow(){
	var client_id=$("#client_id").val();
	if (client_id==0 || client_id.length==0){ 
		swal("Помилка!", "Оберіть спочатку клієнта", "error");
	} else {
		var row=$("#taxStrNewRow").html();
		var kol_row=parseInt($("#kol_str_row").val());
		kol_row+=1;$("#kol_str_row").val(kol_row);
		
		row=row.replace('nom_i', ''+kol_row);
		row=row.replace('i_0', ''+kol_row);
		row=row.replace('i_0', ''+kol_row);
		row=row.replace('idStr_0', 'idStr_'+kol_row);
		row=row.replace('goods_nameStr_0', 'goods_nameStr_'+kol_row);
		row=row.replace('amountStr_0', 'amountStr_'+kol_row);
		row=row.replace('priceStr_0', 'priceStr_'+kol_row);
		row=row.replace('summStr_0', 'summStr_'+kol_row);
		row=row.replace("id='taxStrNewRow' class='hidden'", " id='strRow_"+kol_row+"'");
		var tbody=$("#tax_str tbody").append("<tr>"+row+"</tr>");
		
		/*setTimeout(function (){
			//var dtable=$('#dp_str').DataTable(); dtable.destroy();
			$('#tax_str').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});},500);
			*/
	}
	return;
}

function addNewRowBack(){
	var tax_to_back_id=$("#tax_to_back_id").val();
	if (tax_to_back_id==0 || tax_to_back_id.length==0){ 
		swal("Помилка!", "Оберіть спочатку накладну для коригування", "error");
	} else {
		var row=$("#taxStrNewRow").html();
		var kol_row=parseInt($("#kol_str_row").val());
		kol_row+=1;$("#kol_str_row").val(kol_row);
		
		row=row.replace('nom_i', ''+kol_row);
		row=row.replace('i_0', ''+kol_row);
		row=row.replace('i_0', ''+kol_row);
		row=row.replace('i_0', ''+kol_row);
		row=row.replace('idStr_0', 'idStr_'+kol_row);
		row=row.replace('zedStr_0', 'zedStr_'+kol_row);
		row=row.replace('tax_str_idStr_0', 'tax_str_idStr_'+kol_row);
		row=row.replace('nomStr_0', 'nomStr_'+kol_row);
		row=row.replace('goods_nameStr_0', 'goods_nameStr_'+kol_row);
		row=row.replace('amountStr_0', 'amountStr_'+kol_row);
		row=row.replace('priceStr_0', 'priceStr_'+kol_row);
		row=row.replace('summStr_0', 'summStr_'+kol_row);
		row=row.replace("id='taxStrNewRow' class='hidden'", " id='strRow_"+kol_row+"'");
		var tbody=$("#tax_str tbody").append("<tr>"+row+"</tr>");
	}
	return;
}

function closeTaxCard(){
	if ($("#tax_id")){
		var tax_id=$("#tax_id").val();
		JsHttpRequest.query($rcapi,{ 'w': 'closeTaxCard', 'tax_id':tax_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#TaxCard").modal('hide'); document.getElementById("TaxCardBody").innerHTML=""; document.getElementById("TaxCardLabel").innerHTML="";
		}}, true);
	}
	else{ $("#TaxCard").modal('hide'); document.getElementById("TaxCardBody").innerHTML=""; document.getElementById("TaxCardLabel").innerHTML=""; }
}

function closeTaxBackCard(){
	$("#TaxBackCard").modal('hide'); document.getElementById("TaxBackCardBody").innerHTML="";
	document.getElementById("TaxBackCardLabel").innerHTML="";
}

function findTaxStr(pos,tax_id,str_id,tax_str_id){
	var tax_to_back_id=$("#tax_to_back_id").val();
	JsHttpRequest.query($rcapi,{ 'w': 'findTaxStr', 'tax_id':tax_id,'tax_to_back_id':tax_to_back_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Структура накладної";
		$("#find_pos").val(pos);
		$("#find_str_id").val(str_id);
		$("#find_tax_str_id").val(tax_str_id);
		
		setTimeout(function() { $('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}}); }, 500);
	}}, true);
}

function setTaxBackArticle(id,nom,zed,goods_name,amount,price,summ){
	var str_id=$("#find_str_id").val();
	var tax_str_id=$("#find_tax_str_id").val();
	var pos=$("#find_pos").val();
	$('#idStr_'+pos).val(str_id);console.log("str_id="+str_id);
	$('#nomStr_'+pos).val(nom); console.log("nom="+nom);
	$('#tax_str_idStr_'+pos).val(id);console.log("id="+id);
	$('#zedStr_'+pos).val(zed);console.log("zed="+zed);
	$('#goods_nameStr_'+pos).val(Base64.decode(goods_name));console.log("name="+Base64.decode(goods_name));
	$('#priceStr_'+pos).val(price);console.log("price="+price);
	$('#summStr_'+pos).val(summ);console.log("summ="+summ);
	$("#FormModalWindow").modal('hide'); document.getElementById("FormModalBody").innerHTML=""; document.getElementById("FormModalLabel").innerHTML="";
}

function showTaxSelectList(tax_to_back_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showTaxSelectList', 'tax_to_back_id':tax_to_back_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Податкові накладні";
		setTimeout(function() { $('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}}); }, 500);
	}}, true);
}

function setTaxBackSelect(id,name){
	$('#tax_to_back_id').val(id);
	$('#tax_to_back_name').val(Base64.decode(name));
	$("#FormModalWindow").modal('hide'); document.getElementById("FormModalBody").innerHTML=""; document.getElementById("FormModalLabel").innerHTML="";
	loadTaxBackSellerClient(id);
}

function loadTaxBackSellerClient(tax_id){
	if (tax_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadTaxBackSellerClient', 'tax_id':tax_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$('#client_id').val(result["client_id"]);
				$('#client_name').val(result["client_name"]);
				$('#seller_id option:eq('+result["seller_id"]+')').prop('selected', true);
			}
			else{ toastr["error"](result["error"]); }
		}}, true);
	}
}

function unlinkTaxBack(tax_id){
	swal({
		title: "Відвязати накладну основу?",text: "",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'unlinkTaxBack', 'tax_id':tax_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					$('#tax_to_back_id').val("0"); $('#tax_to_back_name').val("");
					swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
				}
				else{ toastr["error"](result["error"]); }
			}}, true);	
		} else { swal("Відмінено", "Операцію анульовано.", "error"); }
	});
}

function showTaxClientList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showDpClientList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Контрагенти";
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

function setDpClient(id,name,tpoint_id,$tpoint_name){
	var tax_id=$("#tax_id").val();
	$('#client_id').val(id);
//	$('#client_name').val(Base64.decode(name));
	name = name.replace("`", '"');
	name = name.replace("`", '"');
	$('#client_name').val(name);
	$("#FormModalWindow").modal('hide'); document.getElementById("FormModalBody").innerHTML=""; document.getElementById("FormModalLabel").innerHTML="";
}

function unlinkTaxClient(tax_id){
	swal({
		title: "Відвязати клієнта від накладної?",text: "Внесені Вами зміни вплинуть на Ваше майбутнє",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'unlinkTaxClient', 'tax_id':tax_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					$('#client_id').val("0"); $('#client_name').val("");
					swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
				}
				else{ toastr["error"](result["error"]); }
			}}, true);	
		} else { swal("Відмінено", "Операцію анульовано.", "error"); }
	});
}

function printTaxInvoce(tax_id){
	if (tax_id=="" || tax_id==0){toastr["error"](errs[0]);}
	if (tax_id>0){
		window.open("/TaxInvoice/printTIv/"+tax_id,"_blank","printWindow");
	}
}

function exportTaxInvoceXML(tax_id){
	if (tax_id=="" || tax_id==0){toastr["error"](errs[0]);}
	if (tax_id>0){
		window.open("/TaxInvoice/exportTIvXML/"+tax_id,"_blank","printWindow");
	}
}

function exportTaxBackInvoceXML(tax_id){
	if (tax_id=="" || tax_id==0){toastr["error"](errs[0]);}
	if (tax_id>0){
		window.open("/TaxInvoice/exportTBIvXML/"+tax_id,"_blank","printWindow");
	}
}

function loadTaxInvoiceCDN(tax_id){
	if (tax_id<=0 || tax_id==""){toastr["error"](errs[0]);}
	if (tax_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadTaxInvoiceCDN', 'tax_id':tax_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("tax_invoice_cdn_place").innerHTML=result["content"];
		}}, true);
	}
}

function showTaxInvoiceCDNUploadForm(tax_id){
	$("#cdn_tax_id").val(tax_id);
	var myDropzone2 = new Dropzone("#myDropzone2",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone2.removeAllFiles(true);
	myDropzone2.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$('#fileTaxInvoiceCDNUploadForm').modal('hide');
		loadTaxInvoiceCDN(tax_id);
	});
}

function showTaxInvoiceCDNDropConfirmForm(tax_id,file_id,file_name){
	if (tax_id<=0 || tax_id==""){toastr["error"](errs[0]);}
	if (tax_id>0){
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'moneySpendCDNDropFile', 'tax_id':tax_id, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadTaxInvoiceCDN(tax_id); toastr["info"]("Файл успішно видалено"); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}
