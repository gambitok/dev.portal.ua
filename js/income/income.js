var errs = [];
errs[0] = "Помилка індексу";
errs[1] = "Занадто короткий запит для пошуку";

function filterIncomesList() {
	let date_start=$("#date_start").val();
	let date_end=$("#date_end").val();
	JsHttpRequest.query($rcapi,{ 'w': 'filterIncomeList', 'date_start':date_start, 'date_end':date_end},
	function (result, errors){ if (errors) {alert(errors);} if (result){
		let dt=$("#datatable");
		dt.DataTable().destroy();
		$("#income_range").html(result["content"]);
		dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
} 

function getKoursNbu(place, data_place, val_place) {
	let data=$("#"+data_place).val();
	let valuta=$("#"+val_place+" option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'getNBUKours', 'data':data,'valuta':valuta}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#"+place).val(result["content"]);
	}}, true);
	return true;
}

function preNewIncomeCard() {
	$("#FormModalWindow3").modal("show");
	JsHttpRequest.query($rcapi,{ 'w': 'preNewIncomeCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#FormModalBody3").html(result["content"]);
		$("#FormModalLabel3").html("Оберіть тип накладної");
	}}, true);
}

function newIncomeCard(type_id) {
	$("#FormModalWindow3").modal("hide");
	JsHttpRequest.query($rcapi,{ 'w': 'newIncomeCard','type_id':type_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		showIncomeCard(result["income_id"]);
	}}, true);
}

function loadIncomeKours() {
	let data=$("#income_data").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeKours', 'data':data},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if ($("#usd_to_uah")){$("#usd_to_uah").val(result["usd_to_uah"]);}
		if ($("#eur_to_uah")){$("#eur_to_uah").val(result["eur_to_uah"]);}
		if ($("#usd_to_uah_text")){$("#usd_to_uah_text").html(result["usd_to_uah"]);}
		if ($("#eur_to_uah_text")){$("#eur_to_uah_text").html(result["eur_to_uah"]);}
	}}, true);
}

function showIncomeCard(income_id) {
	if (income_id <= 0 || income_id == "") {
		toastr["error"](errs[0]);
	}
	if (income_id > 0) {
		JsHttpRequest.query($rcapi,{ 'w': 'showIncomeCard', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let incomeCard = $("#IncomeCard");
            incomeCard.modal("show");
            $("#IncomeCardBody").html(result.content);
            $("#IncomeCardLabel").html(result["doc_prefix_nom"]);
            $("#income_tabs").tab();
			$("#storage_id").select2({placeholder: "Склад зберігання",dropdownParent: incomeCard});
			$("#storage_cells_id").select2({placeholder: "комірка зберігання",dropdownParent: incomeCard});
			$("#cash_id").select2({placeholder: "Виберіть валюту",dropdownParent: incomeCard});
			$("#invoice_data").datepicker({format: "yyyy-mm-dd",autoclose:true});
			$(".i-checks").iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
			var elem = document.querySelector("#vat_use");
			if (elem) {
	            var vat_use = new Switchery(elem, { color: '#1AB394' });
			}
			numberOnly();
		}}, true);
	}
}

function addNewRow() {
	var row=$("#incomeStrNewRow").html();
	var kol_row=parseInt($("#kol_row").val());
	kol_row+=1;
	$("#kol_row").val(kol_row);
	row=row.replace('nom_i', ''+kol_row);
	row=row.replace('idStr_', 'idStr_'+kol_row);
	row=row.replace('spendingStrId_', 'spendingStrId_'+kol_row);
	row=row.replace('commentStr_', 'commentStr_'+kol_row);
	row=row.replace('summStr_', 'summStr_'+kol_row);
	row=row.replace("id='bids1StrNewRow' class='hidden'", '');
	$("#income_str tbody").append("<tr>"+row+"</tr>");
	return true;
}

function addNewRowLocal(){
	var row=$("#incomeStrNewRow").html();
	var kol_row=parseInt($("#kol_row").val());
	kol_row+=1;
	$("#kol_row").val(kol_row);
	row=row.replace('nom_i', ''+kol_row);
	row=row.replace('idStr_', 'idStr_'+kol_row);
	row=row.replace('spendingStrId_', 'spendingStrId_'+kol_row);
	row=row.replace('commentStr_', 'commentStr_'+kol_row);
	row=row.replace('summStr_', 'summStr_'+kol_row);
	row=row.replace("id='bids1StrNewRow' class='hidden'", '');
	$("#income_str tbody").append("<tr>"+row+"</tr>");
	return true;
}

function saveIncomeCard() {
	var caption=$("#IncomeCardLabel").html();
	var stop=false;
	swal({
		title: "Зберегти накладну \""+caption+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var income_id=$("#income_id").val();
			var type_id=$("#income_type_id").val();
			var document_prefix=$("#income_document_prefix").val();
			var data=$("#income_data").val();
			var client_seller=$("#client_seller").val();
			var invoice_income=$("#invoice_income").val();
			var cash_id=$("#cash_id option:selected").val();
			var client_id=$("#client_id").val();
			var invoice_data=$("#invoice_data").val();
			var cours_to_uah=$("#cours_to_uah").val();
			var cours_to_uah_nbu=$("#cours_to_uah_nbu").val();
			var invoice_summ=$("#invoice_summ").val();
			var comment=$("#comment").val();
			var usd_to_uah=$("#usd_to_uah").val();
			var eur_to_uah=$("#eur_to_uah").val();
			var costums_pd_uah=$("#costums_pd_uah").val();
			var costums_pp_uah=$("#costums_pp_uah").val();
			var costums_summ_uah=$("#costums_summ_uah").val();
			var storage_id=$("#storage_id option:selected").val();
			var storage_cells_id=$("#storage_cells_id option:selected").val();
			var ikr=$("#kol_row").val(); ikr_p=Math.ceil(ikr/20);
			
			if (income_id.length>0 && storage_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveIncomeStorage','income_id':income_id,'storage_id':storage_id,'storage_cells_id':storage_cells_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			}
			
			if (income_id.length>0) {
				//console.log('w: saveIncomeCard; income_id='+income_id+'; type_id='+type_id+'; data='+data+'; client_seller='+client_seller+'; invoice_income='+invoice_income+'; cash_id='+cash_id+'; client_id='+client_id+'; invoice_data='+invoice_data+'; cours_to_uah='+cours_to_uah+'; cours_to_uah_nbu='+cours_to_uah_nbu+'; invoice_summ='+invoice_summ+'; comment='+comment+'; usd_to_uah='+usd_to_uah+'; eur_to_uah='+eur_to_uah+'; costums_pd_uah='+costums_pd_uah+'; costums_pp_uah='+costums_pp_uah+'; costums_summ_uah='+costums_summ_uah+'; kol_row='+ikr+'; ');
				//console.log('idStr='+idStr+'; \nartIdStr='+artIdStr+'; \narticle_nr_displStr='+article_nr_displStr+'; \nbrandIdStr='+brandIdStr+'; \ncountryIdStr='+countryIdStr+'; \ncostumsIdStr='+costumsIdStr+'; \namountStr='+amountStr+'; \nprice_buh_cashinStr='+price_buh_cashinStr+'; \nweightNettoStr='+weightNettoStr+'; \nrateStr='+rateStr+'; \ntypeDeclarationIdStr='+typeDeclarationIdStr+'; \nprice_man_cashinStr='+price_man_cashinStr+'; \nprice_man_usdStr='+price_man_usdStr+'; \nprice_buh_uahStr='+price_buh_uahStr+'; \nprice_man_uahStr='+price_man_uahStr);
				JsHttpRequest.query($rcapi,{ 'w':'saveIncomeCard','income_id':income_id,'data':data,'client_seller':client_seller,'invoice_income':invoice_income,'cash_id':cash_id,'client_id':client_id,'invoice_data':invoice_data,'cours_to_uah':cours_to_uah,'cours_to_uah_nbu':cours_to_uah_nbu,'invoice_summ':invoice_summ,'usd_to_uah':usd_to_uah,'eur_to_uah':eur_to_uah,'costums_pd_uah':costums_pd_uah,'costums_pp_uah':costums_pp_uah,'costums_summ_uah':costums_summ_uah},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						for(var p=1; p<=ikr_p; p++) {
							var idStr=[];var artIdStr=[]; var article_nr_displStr=[]; var brandIdStr=[]; var countryIdStr=[]; var costumsIdStr=[]; var amountStr=[]; var price_buh_cashinStr=[]; var weightNettoStr=[]; var rateStr=[];
							var typeDeclarationIdStr=[]; var price_man_cashinStr=[]; var price_man_usdStr=[]; var price_buh_uahStr=[]; var price_man_uahStr=[];
							
							var frm=(p-1)*20; tto=frm+20; if (tto>ikr){tto=ikr;} 
							var art_empty_count=0;
							for (var i=frm; i<=tto; i++) {
								console.log('clear field #'+i);
								idStr[i]=$("#idStr_"+i).val(); artIdStr[i]=$("#artIdStr_"+i).val(); article_nr_displStr[i]=$("#article_nr_displStr_"+i).val(); 
								if (article_nr_displStr[i]=="") art_empty_count++;	
								brandIdStr[i]=$("#brandIdStr_"+i).val();
								countryIdStr[i]=$("#countryIdStr_"+i).val(); costumsIdStr[i]=$("#costumsIdStr_"+i).val(); amountStr[i]=$("#amountStr_"+i).val(); price_buh_cashinStr[i]=$("#price_buh_cashinStr_"+i).val();
								weightNettoStr[i]=$("#weightNettoStr_"+i).val(); rateStr[i]=$("#rateStr_"+i).val(); typeDeclarationIdStr[i]=$("#typeDeclarationIdStr_"+i).val(); price_man_cashinStr[i]=$("#price_man_cashinStr_"+i).val();
								price_man_usdStr[i]=$("#price_man_usdStr_"+i).val(); price_buh_uahStr[i]=$("#price_buh_uahStr_"+i).val(); price_man_uahStr[i]=$("#price_man_uahStr_"+i).val();
							}
							console.log(art_empty_count+" - "+tto);
							
							if (art_empty_count==tto) { swal("Помилка!", "Не заповнена таблиця!", "error"); stop=true; break; } else {
								JsHttpRequest.query($rcapi,{ 'w':'saveIncomeCardData','income_id':income_id,'frm':frm,'tto':tto,'idStr':idStr,'artIdStr':artIdStr,'article_nr_displStr':article_nr_displStr,'brandIdStr':brandIdStr,'countryIdStr':countryIdStr,'costumsIdStr':costumsIdStr,'amountStr':amountStr,'price_buh_cashinStr':price_buh_cashinStr,'weightNettoStr':weightNettoStr,'rateStr':rateStr,'typeDeclarationIdStr':typeDeclarationIdStr,'price_man_cashinStr':price_man_cashinStr,'price_man_usdStr':price_man_usdStr,'price_buh_uahStr':price_buh_uahStr,'price_man_uahStr':price_man_uahStr},
								function (result1, errors1){ if (errors1) {alert(errors1);} if (result1){  
									if (result1["answer"]==1){ showIncomeStrList(); }
									else { swal("Помилка!", result1["error"], "error");}
								}}, true);
							}
							
						}
						if (!stop) {
							swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
							filterIncomesList();
						} else {
							swal("Помилка!",  "Не заповнена таблиця!", "error");
						}
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function printIncome(){
	let income_id=$("#income_id").val();
	if (income_id.length>0){
		window.open("/JournalIncome/printIn/"+income_id,"_blank","printWindow");
	}
}

function printIncomeLocal(){
	let income_id=$("#income_id").val();
	if (income_id.length>0){
		window.open("/JournalIncome/printInL/"+income_id,"_blank","printWindow");
	}
}

function importIncomeStrCSV(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showImportIncomeStrCSVform', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalBody").html(result.content);
            $("#FormModalLabel").html(result["header"]);
			setTimeout(function(){ $("#csv_table").DataTable({keys: false,"processing": true,"scrollX": true,"bSort": false, "searching": false,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});}, 500);
		}}, true);
	}
}

function finishCsvImport(income_id){
	var start_row=parseInt($("#csv_from_row").val());
	var kol_cols=parseInt($("#kol_cols").val());
	var income_type_id=parseInt($("#income_type_id").val());
	//var cls_kol=7; if (income_type_id==0){cls_kol=6;}
	if (start_row<0 || start_row.length<=0) {
		swal("Помилка!", "Не вказано початковий ряд зчитування", "error");
	}
	if (start_row>=0) {
		var cols=[];var cl=0; var cls_sel=0;
		for (var i=1;i<=kol_cols;i++){
			cl=$("#clm-"+i+" option:selected").val();
			if (cl>0){cls_sel+=1; cols[i]=cl;}
		}
		if (income_type_id==1 && cls_sel<7){
			swal("Помилка!", "Не вказані усі значення колонок", "error");
		}
		if (income_type_id==0 && cls_sel<5){
			swal("Помилка!", "Не вказані усі значення колонок", "error");
		} else {
			$("#waveSpinner_place").html(waveSpinner);
			JsHttpRequest.query($rcapi,{ 'w':'finishCsvImport','income_id':income_id,'start_row':start_row,'kol_cols':kol_cols,'cols':cols},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Імпорт даних завершено!", "Перевірте нові дані накладної.", "success");
					$("#FormModalWindow").modal("hide");
					showIncomeCard(income_id);
				} else {
					swal("Помилка!", result["error"], "error");
				}
			}}, true);
		}
	}
}

function loadRegionSelectList(){
	let state_id=$("#state_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeRegionSelectList', 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		let region =$("#region_id");
        region.html(result.content);
        region.select2({placeholder: "Виберіть район",dropdownParent: $("#IncomeCard")});
	}}, true);
}

function loadCitySelectList(){
    let region_id=$("#region_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeCitySelectList', 'region_id':region_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#city_id").html(result.content);
		$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#IncomeCard")}).on('select2:close', function() {
			var el = $(this);
			if(el.val()==="NEW") {
				var newval = prompt("Введіть нове значення: ");
				if (newval !== null) {
					JsHttpRequest.query($rcapi,{ 'w': 'addNewCity', 'region_id':region_id, 'name':newval},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						el.append('<option id="'+result["id"]+'">'+newval+'</option>').val(newval);
					}}, true);
				}
			}
		});
	}}, true);
}

function loadIncomeUnknownArticles(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeUnknownArticles', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#unknown_articles_place").html(result.content);
            $("#income_tabs").tab();
		}}, true);
	}
}

function checkIncomeUnStrAll(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		var kol_rows=$("#kol_rows_un").val();
		for (var i=1;i<=kol_rows;i++){
			var un_id=$("#idUnStr_"+i).val();
			if (un_id>0){
				checkIncomUnStr(income_id,i,un_id,1);
			}
		}
	}
}

function checkIncomUnStr(income_id,pos,unknown_id,inf){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0 && unknown_id>0){
		var art_id=$("#artIdUnStr_"+pos).val();
		var brand_id=$("#brandIdUnStr_"+pos).val();
		var country_id=$("#countryIdStr_"+pos).val();
		var costums_id=$("#costumsIdUnStr_"+pos).val();
		var amount=$("#amountUnStr_"+pos).val();
		var price=$("#price_buh_cashinUnStr_"+pos).val();
		var weight=$("#weightNettoUnStr_"+pos).val();
		var article_nr_displ=$("#article_nr_displUnStr_"+pos).val();
		if (art_id>0 && amount>0 && price>0){
			JsHttpRequest.query($rcapi,{ 'w': 'checkIncomUnStr', 'income_id':income_id,'unknown_id':unknown_id,'art_id':art_id,'article_nr_displ':article_nr_displ,'brand_id':brand_id,'country_id':country_id,'costums_id':costums_id,'amount':amount,'price':price,'weight':weight}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					if (inf==0) {
						swal("Успішно!", "Артикул "+article_nr_displ+" перенесено у основну структуру прихідної накладної!", "success");
					}
					$("#strUnRow_"+pos).html("");
					$("#strUnRow_"+pos).attr('visibility','hidden');
					//informer update
					updateInformerUnknownArticles(income_id);
				}
			}}, true);
		} else {
			if (inf==0) {
				swal("Помилка!", "Не заповнені всі поля для артикулу "+article_nr_displ+"!", "error");
			}
		}
	}
}

function dropIncomUnStr(income_id,pos,unknown_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0 && unknown_id>0){
        let article_nr_displ=$("#article_nr_displUnStr_"+pos).val();
		swal({
			title: "Видалити артикул "+article_nr_displ+" з накладної і з нерозподілених записів?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				if (income_id.length>0 && unknown_id.length>0) {
					JsHttpRequest.query($rcapi,{ 'w':'dropIncomUnStr','income_id':income_id,'unknown_id':unknown_id},
					function (result, errors){ if (errors) {alert(errors);} if (result){  
						if (result["answer"]==1){ 
							swal("Успішно!", "Артикул "+article_nr_displ+" видалено!", "success");
							$("#strUnRow_"+pos).html("");
							$("#strUnRow_"+pos).attr('visibility','hidden');
							updateInformerUnknownArticles(income_id);
						} else {
							swal("Помилка!", result["error"], "error");
						}
					}}, true);
				}
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
	}
}

function dropIncomeStr(pos,income_id,art_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
        let article_nr_displ=$("#article_nr_displStr_"+pos).val();
		swal({
			title: "Видалити артикул "+article_nr_displ+" з накладної і нероподілених записів?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				if (income_id.length>0){
					JsHttpRequest.query($rcapi,{ 'w':'dropIncomeStr','income_id':income_id, 'art_id':art_id},
					function (result, errors){ if (errors) {alert(errors);} if (result){  
						if (result["answer"]==1){ 
							swal("Успішно!", "Артикул "+article_nr_displ+" видалено!", "success");
							$("#strRow_"+pos).html("");
							$("#strRow_"+pos).attr('visibility','hidden');
							showIncomeStrList();
							//updateInformerUnknownArticles(income_id);
						} else {
							swal("Помилка!", result["error"], "error");
						}
					}}, true);
				}
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
	}
}

function updateInformerUnknownArticles(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'updateInformerUnknownArticles', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#label_un_articles").html(result.content);
        }}, true);
	}
}

function clearIncomeStr(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		swal({
			title: "Очистити структуру накладної?",
			text: "Ви впевнені, що хочете очистити структуру накладної?!!", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Очистити", cancelButtonText: "Цур мене", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w': 'clearIncomeStr', 'income_id':income_id}, 
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Успішно!", "Структуру накладної очищено!", "success");
						showIncomeCard(income_id);
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
	}
}

function exportIncomeUnStr(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		let url = "/export.php?w=incomeUnStr&income_id="+income_id;
		window.open(url, '_blank');
	}
}

function exportIncomeStr(income_id){
    if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
    if (income_id>0){
        let url = "/export.php?w=incomeStr&income_id="+income_id;
        window.open(url, '_blank');
    }
}

function loadIncomeStorage(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeStorage', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#storage_place").html(result.content);
            $("#income_tabs").tab();
			$("#storage_id").select2({placeholder: "Склад зберігання",dropdownParent: $("#IncomeCard")});
			$("#storage_cells_id").select2({placeholder: "комірка зберігання",dropdownParent: $("#IncomeCard")});
		}}, true);
	}
}

function loadStorageCellsSelectList(){
	let storage_id=$("#storage_id option:selected").val();
	if (storage_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeStorageCellsSelectList', 'storage_id':storage_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let storage_cell_id = $("#storage_cells_id");
            storage_cell_id.html(result.content);
            storage_cell_id.select2({placeholder: "комірка зберігання",dropdownParent: $("#IncomeCard")});
		}}, true);
	}
}

function saveIncomeStorage(income_id){
	swal({
		title: "Застосувати складське зберігання?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			let storage_id=$("#storage_id option:selected").val();
			let storage_cells_id=$("#storage_cells_id option:selected").val();
			if (income_id.length>0 && storage_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveIncomeStorage', 'income_id':income_id, 'storage_id':storage_id, 'storage_cells_id':storage_cells_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadIncomeSpend(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeSpend', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let incomeCard=$("#IncomeCard");
            $("#spend_place").html(result.content);
            $("#income_tabs").tab();
			$("#cash_id").select2({placeholder: "Основна валюта",dropdownParent: incomeCard});
			$("#country_cash_id").select2({placeholder: "Національна валюта",dropdownParent: incomeCard});
			$("#price_lvl").select2({placeholder: "Прайс",dropdownParent: incomeCard});
			$("#credit_cash_id").select2({placeholder: "Валюта кредиту",dropdownParent: incomeCard});
		}}, true);
	}
}

function showIncomeSpendItemRow(income_id, spend_item_id, str_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showIncomeSpendItemRow', 'income_id':income_id, 'spend_item_id':spend_item_id, 'str_id':str_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalBody").html(result.content);
            $("#FormModalLabel").html(result["header"]);
			$("#str_data").datepicker({format: "yyyy-mm-dd",autoclose:true});
			numberOnly();
		}}, true);
	}
}

function summSpendRowForm(){
	let cash=parseFloat($("#str_summ_cash").val());
	let kours=parseFloat($("#str_kours").val());
    let summ=cash*kours;
	$("#str_summ_uah").val(summ);
}

function saveIncomeSpendStrForm(income_id,spend_item_id,str_id){
	let caption=$("#str_caption").val();
	swal({
		title: "Зберегти витрату \""+caption+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var data=$("#str_data").val();
			var cash_id=$("#str_cash_id option:selected").val();
			var summ_cash=$("#str_summ_cash").val();
			var kours=$("#str_kours").val();
			var summ_uah=$("#str_summ_uah").val();
			if (income_id.length>0 && spend_item_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveIncomeSpendStrForm','income_id':income_id,'spend_item_id':spend_item_id,'str_id':str_id,'caption':caption,'data':data,'cash_id':cash_id,'summ_cash':summ_cash,'kours':kours,'summ_uah':summ_uah},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadIncomeSpend(income_id);
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function showIncomeSpendItemFileUpload(income_id,str_id){
	$("#cdn_income_str_id").val(str_id);
	$("#fileIncomeStrUploadForm").modal("show");
	var myDropzone4 = new Dropzone("#myDropzone4",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone4.removeAllFiles(true);
	myDropzone4.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$("#fileIncomeStrUploadForm").modal("hide");
		loadIncomeSpend(income_id);
	});
}

function dropIncomeSpendItemRow(income_id,spend_item_id,str_id){
	swal({
		title: "Видалити витрату?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (income_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropIncomeSpendItemRow', 'income_id':income_id, 'str_id':str_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadIncomeSpend(income_id);
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function showIncomeCountrySearchForm(pos, art_id, country_id){
	$("#FormModalWindow").modal("show");
	JsHttpRequest.query($rcapi,{ 'w': 'showIncomeCountrySearchForm', 'country_id':country_id, 'pos':pos},
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#FormModalBody").html(result.content);
        $("#FormModalLabel").html("Країна походження");
		setTimeout(function(){
			$("#datatable_country").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCountryDocument(id,name){
	var pos=$("#doc_pos").val(); var un="";
	if($("#cat_tab7").attr("class")=="tab-pane active"){un="Un";}
	$("#countryId"+un+"Str_"+pos).val(id);
	$("#countryAbr"+un+"Str_"+pos).val(name);
	$("#FormModalWindow").modal("hide");
	getRateTypeDeclarationdocumentPos(pos);
}

function showIncomeCostumsSearchForm(pos, art_id, costums_id){
	$("#FormModalWindow").modal("show");
	JsHttpRequest.query($rcapi,{ 'w': 'showIncomeCostumsSearchForm', 'costums_id':costums_id, 'pos':pos},
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#FormModalBody").html(result.content);
        $("#FormModalLabel").html("Митний код");
        setTimeout(function(){
			$("#datatable_costums").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCostumsDocument(id, name){
	var pos=$("#doc_pos").val();var un="";
	if($("#cat_tab7").attr("class")=="tab-pane active"){un="Un";}
	$("#costumsId"+un+"Str_"+pos).val(id);
	$("#costums"+un+"Str_"+pos).val(name);
	$("#FormModalWindow").modal('hide');
	getRateTypeDeclarationdocumentPos(pos);
	var art_id=$("#artIdStr_"+pos).val();
	var costums_id=$("#costumsIdStr_"+pos).val();
	var article=$("#article_nr_displStr_"+pos).val();
	checkArticleZed(art_id, costums_id, article);
}

function checkArticleZed(art_id, costums_id, article) {
	JsHttpRequest.query($rcapi,{ 'w': 'checkArticleZed', 'art_id':art_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		if (result.content===false) {
			saveArticleZed(art_id, costums_id);
		} else {
			swal({
				title: "Для вибраного артикула ("+article+") уже прописаний Код УКТЗЕД - "+result.content,
				text: "Бажаєте змінити Код УКТЗЕД по замовчуванню?", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
				confirmButtonText: "Так, змінити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
			},
			function (isConfirm) {
				if (isConfirm) {
					swal.close();
					saveArticleZed(art_id, costums_id);
				} else {
					swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
				}
			});
		}
	}}, true);
}

function saveArticleZed(art_id,costums_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'saveArticleZed', 'art_id':art_id, 'costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){
		if(result["answer"]==1){
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
		} else {
			swal("Помилка", result["err"], "error");
		}
	}}, true);
}

function getRateTypeDeclarationdocumentPos(pos){
    let costums_id=$("#costumsIdStr_"+pos).val();
    let country_id=$("#countryIdStr_"+pos).val();
	JsHttpRequest.query($rcapi,{ 'w': 'getRateTypeDeclarationdocumentPos', 'costums_id':costums_id, 'country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){
        let un="";
		if($("#cat_tab7").attr("class")=="tab-pane active"){un="Un";}
		$("#rate"+un+"Str_"+pos).val(result["rate"]);
		$("#typeDeclaration"+un+"Str_"+pos).val(result["type_declaration"]);
		$("#typeDeclarationId"+un+"Str_"+pos).val(result["type_declaration_id"]);
	}}, true);
}

//----------------------------------------------------------------------------------------------------------------------------------
//						CALCULATE STR INCOME
//----------------------------------------------------------------------------------------------------------------------------------

function setIncomeVat(){
	let income_id=$("#income_id").val();
	let vat_use=0;
	if (document.getElementById("vat_use").checked){vat_use=1;}
	JsHttpRequest.query($rcapi,{ 'w': 'setIncomeVat', 'income_id':income_id, 'vat_use':vat_use},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1) {
			recalculateIncomeStrLocal();
		} else {
			swal("Помилка", result["err"], "error");
		}
	}}, true);
}

function round(value, exp) {
	if (typeof exp === 'undefined' || +exp === 0)
	return Math.round(value);
	value = +value;
	exp = +exp;
	if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0))
	return NaN;
	value = value.toString().split('e');
	value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp)));
	value = value.toString().split('e');
	return +(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp));
}

function getPWeight(ikr,weight_netto){
	var ws=0;
	for (var i=1;i<=ikr;i++) {
		var wn=parseFloat($("#weightNettoStr_"+i).val().replace(/[,]+/g, '.'));
		if (wn>0){ws+=wn;}
	}
	let w=round(weight_netto/ws*100,4).toFixed(4);
	return w;
}

function countInvoiceSumm(ikr){
	var invoice_summ=0;
	var amount=0;var price_income=0;
	for (var i=1;i<=ikr;i++){
		amount=parseFloat($("#amountStr_"+i).val().replace(/[,]+/g, '.'));
		price_income=parseFloat($("#price_buh_cashinStr_"+i).val().replace(/[,]+/g, '.'));
		if (amount>0 && price_income>0){
			invoice_summ+=amount*price_income;
		}
	}
	invoice_summ=round(invoice_summ,2).toFixed(2);
	return invoice_summ;
}

function recalculateIncomeStr(){
	console.log('recalculate income');
	// var income_id=$("#income_id").val();
	var ikr=$("#kol_row").val();
	var invoice_summ=countInvoiceSumm(ikr);	$("#invoice_summ").val(invoice_summ); 
	var tz=parseFloat($("#income_spend_item_1").val());
	var tl=parseFloat($("#income_spend_item_2").val());
	var rb=parseFloat($("#income_spend_item_3").val());
	var ro=parseFloat($("#income_spend_item_4").val());
	var sz=parseFloat($("#income_spend_item_5").val());
	var cours_to_uah=parseFloat($("#cours_to_uah").val());
	if (cours_to_uah==0 || cours_to_uah==""){cours_to_uah=1;$("#cours_to_uah").val(cours_to_uah);}
	var cours_to_uah_nbu=parseFloat($("#cours_to_uah_nbu").val());
	if (cours_to_uah_nbu==0 || cours_to_uah_nbu==""){cours_to_uah_nbu=1;$("#cours_to_uah_nbu").val(cours_to_uah_nbu);}
	var cash_id=$("#cash_id option:selected").val();
	var costums_pd_uah=0;
	var costums_pp_uah=0;
	// var costums_summ_uah=0;
	
	for (var i=1;i<=ikr;i++){
		var str_id=$("#idStr_"+i).val();
		var amount=parseFloat($("#amountStr_"+i).val().replace(/[,]+/g, '.'));
		var price_income=parseFloat($("#price_buh_cashinStr_"+i).val().replace(/[,]+/g, '.'));
		var weight_netto=parseFloat($("#weightNettoStr_"+i).val().replace(/[,]+/g, '.'));
		if (amount>0 && price_income>0){
			var rate=parseFloat($("#rateStr_"+i).val().replace(/[,]+/g, '.'));
			alrt="step1) str_id="+str_id+"; amount="+amount+"; price_income="+price_income+"; weight_netto="+weight_netto+"\n";
			
			var p_weight=getPWeight(ikr,weight_netto);
			alrt+="step2.1) p_weight="+p_weight+"\n";
			
			var p_money=round(amount*price_income/invoice_summ*100,4);p_money=p_money.toFixed(4);
			alrt+="step2.2) p_money="+p_money+"\n";
			
			var tz_uah=round(tz*p_weight/100,4); tz_uah=tz_uah.toFixed(4);
			alrt+="step2.3) tz_uah="+tz_uah+"\n";
			
			var fs_uah=round(amount*price_income*cours_to_uah_nbu,4);fs_uah=fs_uah.toFixed(4);
			alrt+="step2.4) fs_uah="+fs_uah+"\n";
			
			var sz_uah=round(sz*p_money/100,4);sz_uah=sz_uah.toFixed(4);
			alrt+="step2.4.1) sz_uah="+sz_uah+"\n";
			
			var ts_uah=round(parseFloat(fs_uah)+parseFloat(tz_uah)+parseFloat(sz_uah),4);ts_uah=ts_uah.toFixed(4);
			alrt+="step2.5) ts_uah="+ts_uah+"\n";
			
			var tl_uah=0;
			if (p_weight>0){
				tl_uah=round(tl*p_weight/100,4);tl_uah=tl_uah.toFixed(4);
			}
			alrt+="step2.6) tl_uah="+tl_uah+"\n";
			
			var rb_uah=round(rb*p_money/100,4);rb_uah=rb_uah.toFixed(4);
			alrt+="step2.7) rb_uah="+rb_uah+"\n";
			
			var ro_uah=round(ro*p_money/100,4);ro_uah=ro_uah.toFixed(4);
			alrt+="step2.8) ro_uah="+ro_uah+"\n";
			
			var po_b_uah=po_b_uah=round(((parseFloat(ts_uah)+parseFloat(ts_uah)*rate)-ts_uah)/100,4);po_b_uah=po_b_uah.toFixed(4);
			alrt+="step2.9) po_b_uah="+po_b_uah+"\n";
			
			var nds_uah=round(((parseFloat(ts_uah)+parseFloat(po_b_uah))*0.2),4);nds_uah=nds_uah.toFixed(4);
			alrt+="step2.10) nds_uah="+nds_uah+"\n";
			
			var kurs=1; var kurs_usd=parseFloat($("#usd_to_uah").val());
			if (cash_id==1){kurs=1;}
			if (cash_id==2){kurs=parseFloat($("#usd_to_uah").val());}
			if (cash_id==3){kurs=parseFloat($("#eur_to_uah").val());}
			alrt+="kurs="+kurs+"\n";
			
			var suvsdo=parseFloat(price_income*amount)+((parseFloat(tz_uah)+parseFloat(tl_uah)+parseFloat(sz_uah)+parseFloat(rb_uah)+parseFloat(ro_uah)+parseFloat(po_b_uah)+parseFloat(nds_uah))/parseFloat(kurs));
			suvsdo=round(suvsdo/parseFloat(amount),4);
			suvsdo=suvsdo.toFixed(4);
			$("#price_man_cashinStr_"+i).val(suvsdo);
			alrt+="step3) suvsdo="+suvsdo+"\n";
			
			var su_usd=round(suvsdo*kurs/kurs_usd,4);su_usd=su_usd.toFixed(4);
			$("#price_man_usdStr_"+i).val(su_usd);
			alrt+="step4) su_usd="+su_usd+"\n";
			
			var sb_uah=parseFloat(price_income*amount*cours_to_uah)+(parseFloat(tz_uah)+parseFloat(tl_uah)+parseFloat(sz_uah)+parseFloat(rb_uah)+parseFloat(po_b_uah));
			sb_uah=sb_uah/parseFloat(amount);
			sb_uah=round(sb_uah,4);sb_uah=sb_uah.toFixed(4);
			$("#price_buh_uahStr_"+i).val(sb_uah);
			alrt+="step5) sb_uah="+sb_uah+"\n";
			
			var su_uah=parseFloat(price_income*amount*cours_to_uah)+(parseFloat(tz_uah)+parseFloat(tl_uah)+parseFloat(sz_uah)+parseFloat(rb_uah)+parseFloat(ro_uah)+parseFloat(po_b_uah)+parseFloat(nds_uah));
			
			alrt+="(price_income("+price_income+")*amount("+amount+")*cours_to_uah_nbu("+cours_to_uah+"))+(tz_uah("+tz_uah+")+tl_uah("+tl_uah+")+sz_uah("+sz_uah+")+rb_uah("+rb_uah+")+ro_uah("+ro_uah+")+po_b_uah("+po_b_uah+")+nds_uah("+nds_uah+"))/amount("+amount+")\n";
			
			su_uah=su_uah/parseFloat(amount);
			su_uah=round(su_uah,4);su_uah=su_uah.toFixed(4);
			$("#price_man_uahStr_"+i).val(su_uah);
			alrt+="step6) su_uah="+su_uah+"\n";
			
			var costums_pd=costums_pp=0;
			if ($("#typeDeclarationIdStr_"+i).val()==1){
				costums_pd=parseFloat(po_b_uah)+parseFloat(nds_uah);
				costums_pd_uah+=costums_pd;
				alrt+="pd="+costums_pd_uah+"\n";
			}
			if ($("#typeDeclarationIdStr_"+i).val()==2){
				costums_pp=parseFloat(po_b_uah)+parseFloat(nds_uah);
				costums_pp_uah+=costums_pp;
				alrt+="pp="+costums_pp_uah;
			}
						
			if (suvsdo==0 || su_usd==0 || sb_uah==0 || su_uah==0) setIncomeCardPriceZero("1"); else setIncomeCardPriceZero("0");
			// if (i<4){alert(alrt);}
		}
	}
	$("#costums_pd_uah").val(costums_pd_uah.toFixed(2));
	$("#costums_pp_uah").val(costums_pp_uah.toFixed(2));

	costums_summ_uah=parseFloat(costums_pd_uah)+parseFloat(costums_pp_uah);
	$("#costums_summ_uah").val(costums_summ_uah.toFixed(2));

    saveIncomeCard();
}

function showIncomeStrList() {
	let income_id=$("#income_id").val();
    let type_id=$("#income_type_id").val();
    let oper_status=$("#income_oper_status").val();
	JsHttpRequest.query($rcapi,{ 'w':'showIncomeStrList', 'income_id':income_id, 'type_id':type_id, 'oper_status':oper_status},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#income_shild_range").html(result.content);
	}}, true);
}

function recalculateIncomeStrLocal(){
	console.log('recalculate income local');
	// var income_id=$("#income_id").val();
	var ikr=$("#kol_row").val();
	var vat_use=0;if (document.getElementById("vat_use").checked){vat_use=1;}
	var invoice_summ=countInvoiceSumm(ikr);	$("#invoice_summ").val(invoice_summ); 
	// var tz=parseFloat($("#income_spend_item_1").val());
	var tl=parseFloat($("#income_spend_item_2").val());
	var rb=parseFloat($("#income_spend_item_3").val());
	var ro=parseFloat($("#income_spend_item_4").val());
	// var sz=parseFloat($("#income_spend_item_5").val());
	var cours_to_uah=parseFloat($("#cours_to_uah").val());
	if (cours_to_uah==0 || cours_to_uah==""){cours_to_uah=1;$("#cours_to_uah").val(cours_to_uah);}
	var cash_id=$("#cash_id option:selected").val();
	// var costums_pd_uah=0;
	// var costums_pp_uah=0;
	// var costums_summ_uah=0;
	
	for (var i=1;i<=ikr;i++){
		var str_id=$("#idStr_"+i).val();
		var amount=parseFloat($("#amountStr_"+i).val().replace(/[,]+/g, '.'));
		var price_income=parseFloat($("#price_buh_cashinStr_"+i).val().replace(/[,]+/g, '.'));
		var weight_netto=parseFloat($("#weightNettoStr_"+i).val().replace(/[,]+/g, '.'));
		if (amount>0 && price_income>0){
			alrt="step1) str_id="+str_id+"; amount="+amount+"; price_income="+price_income+"; weight_netto="+weight_netto+"\n";
			
			var p_weight=getPWeight(ikr,weight_netto);
			alrt+="step2.1) p_weight="+p_weight+"\n";
			
			var p_money=round(amount*price_income/invoice_summ*100,4);p_money=p_money.toFixed(4);
			alrt+="step2.2) p_money="+p_money+"\n";
			
			var tl_uah=0;
			if (p_weight>0){
				tl_uah=round(tl*p_weight/100,4);tl_uah=tl_uah.toFixed(4);
			}
			alrt+="step2.6) tl_uah="+tl_uah+"\n";
			
			var rb_uah=round(rb*p_money/100,4);rb_uah=rb_uah.toFixed(4);
			alrt+="step2.7) rb_uah="+rb_uah+"\n";
			
			var ro_uah=round(ro*p_money/100,4);ro_uah=ro_uah.toFixed(4);
			alrt+="step2.8) ro_uah="+ro_uah+"\n";
			
			//var sz_uah=round(sz*p_money/100,4);sz_uah=sz_uah.toFixed(4);
			//alrt+="step2.9) sz_uah="+sz_uah+"\n";
			
			var nds_uah=0;
			if (vat_use==1){ nds_uah=round(parseFloat(price_income*amount*cours_to_uah*0.2),4);nds_uah=nds_uah.toFixed(4); }
			alrt+="step2.10) nds_uah="+nds_uah+"\n";
			
			var kurs=1; var kurs_usd=parseFloat($("#usd_to_uah").val());
			if (cash_id==1){kurs=1;}if (cash_id==2){kurs=parseFloat($("#usd_to_uah").val());}if (cash_id==3){kurs=parseFloat($("#eur_to_uah").val());}
			alrt+="kurs="+kurs+"\n";
			
			var suvsdo=parseFloat(price_income*amount)+((parseFloat(tl_uah)+parseFloat(rb_uah)+parseFloat(ro_uah)+parseFloat(nds_uah))/parseFloat(kurs));
			suvsdo=round(suvsdo/parseFloat(amount),4);
			suvsdo=suvsdo.toFixed(4);
			$("#price_man_cashinStr_"+i).val(suvsdo);
			alrt+="step3) suvsdo="+suvsdo+"\n";
			
			var su_usd=round(suvsdo*kurs/kurs_usd,4);su_usd=su_usd.toFixed(4);
			$("#price_man_usdStr_"+i).val(su_usd);
			alrt+="step4) su_usd="+su_usd+"\n";
			
			var sb_uah=parseFloat(price_income*amount*cours_to_uah)+(parseFloat(tl_uah)+parseFloat(rb_uah));
			sb_uah=sb_uah/parseFloat(amount);
			sb_uah=round(sb_uah,4);sb_uah=sb_uah.toFixed(4);
			$("#price_buh_uahStr_"+i).val(sb_uah);
			alrt+="step5) sb_uah="+sb_uah+"\n";
			
			var su_uah=parseFloat(price_income*amount*cours_to_uah)+(parseFloat(tl_uah)+parseFloat(rb_uah)+parseFloat(ro_uah)+parseFloat(nds_uah));
			su_uah=su_uah/parseFloat(amount);
			su_uah=round(su_uah,4);su_uah=su_uah.toFixed(4);
			$("#price_man_uahStr_"+i).val(su_uah);
			alrt+="step6) su_uah="+su_uah+"\n";
			
			if (suvsdo==0 || su_usd==0 || sb_uah==0 || su_uah==0) setIncomeCardPriceZero("1"); else setIncomeCardPriceZero("0");
		}
	}
    saveIncomeCard();
}

function setIncomeCardPriceZero(status) {
	$("#income_price_status").val(status);
}

function saveIncomeDetails() {
	let income_id=$("#income_id").val();
    let address_jur=$("#address_jur").val();
    let address_fakt=$("#address_fakt").val();
    let edrpou=$("#edrpou").val();
    let svidotctvo=$("#svidotctvo").val();
    let vytjag=$("#vytjag").val();
    let vat=$("#vat").val();
    let mfo=$("#mfo").val();
    let bank=$("#bank").val();
    let account=$("#account").val();
    let nr_details=$("#nr_details").val();
    let not_resident=0;
    if (document.getElementById("not_resident").checked){not_resident=1;}else{nr_details="";}
	if (income_id.length>0) {
		JsHttpRequest.query($rcapi,{ 'w':'saveIncomeDetails','income_id':income_id,'address_jur':address_jur,'address_fakt':address_fakt,'edrpou':edrpou,'svidotctvo':svidotctvo,'vytjag':vytjag,'vat':vat,'mfo':mfo, 'bank':bank,'account':account,'not_resident':not_resident,'nr_details':nr_details},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
	}
}

function loadIncomeContacts(income_id) {
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeContacts', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#contacts_place").html(result.content);
			$("#income_tabs").tab();
		}}, true);
	}
}

function dropIncomeContact(income_id,contact_id,contact_name) {
	swal({
		title: "Видалити контакт \""+contact_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (income_id.length>0) {
				JsHttpRequest.query($rcapi,{ 'w':'dropIncomeContact','income_id':income_id,'contact_id':contact_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadIncomeContacts(income_id);
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveIncomeContactForm(income_id, contact_id) {
	let contact_name = $("#contact_name").val();
	swal({
		title: "Зберегти зміни контакту \""+contact_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let contact_post=$("#contact_post").val();
			var cn=$("#contact_con_kol").val();
			var con_id = []; var sotc_cont = []; var contact_value = [];
			for (var i=1;i<=cn;i++){
				con_id[i]=$("#con_id_"+i).val();
				sotc_cont[i]=$("#sotc_cont_"+i+" option:selected").val();
				contact_value[i]=$("#contact_value_"+i).val();
			}
			if (income_id.length>0) {
				JsHttpRequest.query($rcapi,{ 'w':'saveIncomeContactForm','income_id':income_id,'contact_id':contact_id,'contact_name':contact_name,'contact_post':contact_post,'contact_con_kol':cn,'con_id':con_id,'sotc_cont':sotc_cont,'contact_value':contact_value},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadIncomeContacts(income_id);
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveIncomeConditions(income_id) {
	let cash_id=$("#cash_id option:selected").val();
    let country_cash_id=$("#country_cash_id option:selected").val();
    let price_lvl=$("#price_lvl option:selected").val();
    let payment_delay=$("#payment_delay").val();
    let credit_limit=$("#credit_limit").val();
    let credit_cash_id=$("#credit_cash_id option:selected").val();
    let credit_return=$("#credit_return").val();
	if (income_id.length>0) {
		JsHttpRequest.query($rcapi,{ 'w':'saveIncomeConditions','income_id':income_id,'cash_id':cash_id,'country_cash_id':country_cash_id,'price_lvl':price_lvl,'payment_delay':payment_delay,'credit_limit':credit_limit,'credit_cash_id':credit_cash_id,'credit_return':credit_return},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
	}
}

function showIncomeClientList(client_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showIncomeClientList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
        $("#FormModalBody").html(result["content"]);
        $("#FormModalLabel").html("Контрагенти");
		setTimeout(function() {
			$("#datatable_parrent").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
		}, 500);
	}}, true);
}

function setIncomeClient(id, name) {
	$("#client_id").val(id);
	$("#client_name").val(Base64.decode(name));
	$("#FormModalWindow").modal("hide");
    $("#FormModalBody").html("");
    $("#FormModalLabel").html("");
	/*
	swal({
		title: "При зміні отримувача буде змінено префікс документу?",text: "Ви підтверджуєте зміну отримувача",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так",cancelButtonText: "Відмінити!",closeOnConfirm: true,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			$('#client_id').val(id);
			$('#client_name').val(name);
			$("#FormModalWindow").modal('hide');
			document.getElementById("FormModalBody").innerHTML="";
			document.getElementById("FormModalLabel").innerHTML="";
			
			var income_id=$("#income_id").val();
			JsHttpRequest.query($rcapi,{ 'w': 'getIncomeClientPrefixDocument', 'income_id':income_id, 'client_id':id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				document.getElementById("doc_prefix").innerHTML=result["doc_prefix"];
				document.getElementById("IncomeCardLabel").innerHTML=result["doc_prefix"];
				document.getElementById("income_document_prefix").value=result["doc_prefix"];
				swal.close();
			}}, true);
		} else {
			swal("Відмінено", "Операцію анульовано.", "error");
		}
	});*/
}

function unlinkIncomeClient(income_id){
	swal({
		title: "Відвязати контагента від накладної?",text: "Внесені Вами зміни вплинуть на роботу Контрагента",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'unlinkIncomeClient', 'income_id':income_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					$("#client_id").val("0");
					$("#client_name").val("");
					swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
				} else {
					toastr["error"](result["error"]);
				}
			}}, true);	
		} else {
			swal("Відмінено", "Операцію анульовано.", "error");
		}
	});
}

function showIncomeClientSellerList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showIncomeClientSellerList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
        $("#FormModalBody").html(result["content"]);
        $("#FormModalLabel").html("Контрагенти");
		setTimeout(function() {
			$("#datatable_parrent").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
		}, 500);
	}}, true);
}

function setIncomeClientSeller(id,name){
	$("#client_seller").val(id);
	$("#client_seller_name").val(Base64.decode(name));
	$("#FormModalWindow").modal("hide");
    $("#FormModalBody").html("");
    $("#FormModalLabel").html("");
}

function unlinkIncomeClientSeller(income_id){
	swal({
		title: "Відвязати контагента від накладної?",text: "Внесені Вами зміни вплинуть на роботу Контрагента",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'unlinkIncomeClientSeller', 'income_id':income_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					$("#client_seller").val("0");
					$("#client_seller_name").val("");
					swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
				} else {
					toastr["error"](result["error"]);
				}
			}}, true);	
		} else {
			swal("Відмінено", "Операцію анульовано.", "error");
		}
	});
}

function showIncomeArticleSearchForm(i,art_id,brand_id,article_nr_displ){
	JsHttpRequest.query($rcapi,{ 'w': 'showIncomeArticleSearchForm', 'brand_id':brand_id, 'article_nr_displ':article_nr_displ},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
        $("#FormModalBody").html(result.content);
        $("#FormModalLabel").html("Номенклатура");
		$("#row_pos").val(i);
		$("#datatable_parrent").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
	}}, true);
}

function setArticleToDoc(art_id,article_nr_displ,brand_id,brand_name,costums_id,costums_code,country_id,country_name){
	var pos=$("#row_pos").val(); var un="";
	if($('#cat_tab7').attr('class')=="tab-pane active"){un="Un";}
	$('#artId'+un+'Str_'+pos).val(art_id);
	$('#article_nr_displ'+un+'Str_'+pos).val(article_nr_displ);
	$('#brandId'+un+'Str_'+pos).val(brand_id);
	$('#brandName'+un+'Str_'+pos).val(brand_name);
	if ($('#costumsId'+un+'Str_'+pos).val()==0){
		$('#costumsId'+un+'Str_'+pos).val(costums_id);
		$('#costums'+un+'Str_'+pos).val(costums_code);
	}
	if ($('#countryId'+un+'Str_'+pos).val()==0){
		$('#countryId'+un+'Str_'+pos).val(country_id);
		$('#countryAbr'+un+'Str_'+pos).val(country_name);
	}
	$("#FormModalWindow").modal("hide");
    $("#FormModalBody").html("");
    $("#FormModalLabel").html("");
	$("#row_pos").val("");
	var art_id=$("#artIdStr_"+pos).val();
	var costums_id=$("#costumsIdStr_"+pos).val();
	var article=$("#article_nr_displStr_"+pos).val();
	if (costums_id !== ""){
		checkArticleZed(art_id, costums_id, article);
	}
}

function randString(id){
	var dataSet = $(id).attr('data-character-set').split(',');
	var possible = "";  var text = "";
	if($.inArray('a-z', dataSet) >= 0){possible += 'abcdefghijklmnopqrstuvwxyz';}
	if($.inArray('A-Z', dataSet) >= 0){possible += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';}
	if($.inArray('0-9', dataSet) >= 0){possible += '0123456789';}
	if($.inArray('#', dataSet) >= 0){possible += '![]{}()%&*$#^<>~@|';}
	for(var i=0; i < $(id).attr('data-size'); i++) {
		text += possible.charAt(Math.floor(Math.random() * possible.length));
	}
	$(id).val(""+text);
	return text;
}

function dropIncomeUser(income_id,user_id,user_name){
	swal({
		title: "Видалити користувача \""+user_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (income_id.length>0) {
				JsHttpRequest.query($rcapi,{ 'w':'dropIncomeUser', 'income_id':income_id, 'user_id':user_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveIncomeUserForm(income_id,user_id){
	let user_name = $("#user_name").val();
	swal({
		title: "Зберегти зміни Користувача \""+user_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let user_email=$("#user_email").val();
            let user_phone=$("#user_phone").val();
            let user_pass=$("#user_pass").val();
            let user_main=0; if(document.getElementById("user_main").checked) { user_main=1;}
			if (income_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveIncomeUserForm','income_id':income_id,'user_id':user_id,'user_name':user_name,'user_email':user_email,'user_phone':user_phone,'user_pass':user_pass,'user_main':user_main},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
					} else {
						swal("Помилка!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadIncomeCommetsLabel(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeCommetsLabel', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#label_comments").html(result["label"]);
        }}, true);
	}
}

function loadIncomeCommets(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeCommets', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#income_commets_place").html(result.content);
        }}, true);
	}
}

function saveIncomeComment(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		let comment=$("#income_comment_field").val();
		if (comment.length<=0){toastr["error"]("Напишіть коментар спочатку");}
		if (comment.length>0){
			JsHttpRequest.query($rcapi,{ 'w': 'saveIncomeComment', 'income_id':income_id, 'comment':comment}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					loadIncomeCommets(income_id); 
					$("#income_comment_field").val("");
					loadIncomeCommetsLabel(income_id);
				} else {
					toastr["error"](result["error"]);
				}
			}}, true);
		}
	}
}

function dropIncomeComment(income_id,cmt_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		if(confirm('Видалити запис?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'dropIncomeComment', 'income_id':income_id, 'cmt_id':cmt_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadIncomeCommets(income_id);
					toastr["info"]("Запис успішно видалено");loadIncomeCommetsLabel(income_id);
				} else {
					toastr["error"](result["error"]);
				}
			}}, true);
		}
	}
}

function loadIncomeCDN(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeCDN', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#income_cdn_place").html(result.content);
		}}, true);
	}
}

function showIncomeCDNUploadForm(income_id){
	$("#cdn_income_id").val(income_id);
	var myDropzone2 = new Dropzone("#myDropzone2",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone2.removeAllFiles(true);
	myDropzone2.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$("#fileIncomeCDNUploadForm").modal("hide");
		loadIncomeCDN(income_id);
	});
}

function showIncomeCDNDropConfirmForm(income_id,file_id,file_name){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'incomeCDNDropFile', 'income_id':income_id, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1) {
					loadIncomeCDN(income_id);
					toastr["info"]("Файл успішно видалено");
				} else {
					toastr["error"](result["error"]);
				}
			}}, true);
		}
	}
}

function viewIncomeDetailsFile(income_id,file_type){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		$("#viewDetailsForm").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeDetailsFile', 'income_id':income_id, 'file_type':file_type}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#income_details_files_place").html(result.content);
		}}, true);
	}
}

// function fileIncomeDetailsUploadForm(income_id,file_type){
// 	$("#dtls_income_id").val(income_id);
// 	$("#dtls_file_type").val(file_type);
// 	$("#fileIncomeDetailsUploadForm").modal("show");
// 	var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
// 	myDropzone3.removeAllFiles(true);
// 	myDropzone3.on("queuecomplete", function() {
// 		toastr["info"]("Завантаження файлів завершено.");
// 		this.removeAllFiles();
// 		$("#fileIncomeDetailsUploadForm").modal("hide");
// 		viewIncomeDetailsFile(income_id,file_type);
// 	});
// }

// function showIncomeDetailsDropConfirmForm(income_id,file_type,file_id,file_name){
// 	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
// 	if (income_id>0){
// 		if(confirm('Видалити файл '+file_name+'?')){
// 			JsHttpRequest.query($rcapi,{ 'w': 'incomeDetailsDropFile', 'income_id':income_id, 'file_type':file_type, 'file_id':file_id},
// 			function (result, errors){ if (errors) {alert(errors);} if (result){
// 				if (result["answer"]==1){ viewIncomeDetailsFile(income_id,file_type); toastr["info"]("Файл успішно видалено"); }
// 				else{ toastr["error"](result["error"]); }
// 			}}, true);
// 		}
// 	}
// }

function showCountryManual(){
	let country_id = $("#country_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCountryManual', 'country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CountryModalWindow").modal("show");
		$("#CountryBody").html(result.content);
		setTimeout(function(){
			$("#datatable_country").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCountry(id,name){
	$("#country_id").val(id);
	$("#country_name").val(name);
	$("#CountryModalWindow").modal("hide");
}

function showCountryForm(country_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCountryForm', 'country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
		$("#FormModalBody").html(result.content);
		$("#FormModalLabel").html(result.header);
	}}, true);
}

function showCostumsManual(){
	let costums_id=$("#costums_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsManual', 'costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CostumsModalWindow").modal("show");
        $("#CostumsBody").html(result["content"]);
        setTimeout(function(){
		  $("#datatable_costums").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCostums(id,name){
	$("#costums_id").val(id);
	$("#costums_name").val(name);
	$("#CostumsModalWindow").modal("hide");
}

function showCostumsForm(costums_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsForm', 'costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
		$("#FormModalBody").html(result["content"]);
		$("#FormModalLabel").html(result["header"]);
	}}, true);
}

function cardFinish(status_income) {
	var income_id = $("#income_id").val();
	var seller = $("#client_seller").val();
	var provider = $("#client_name").val();
	var price = parseFloat($("#cours_to_uah").val());
	if (parseInt($("#income_type_id").val()) > 0) {
		var price_usd = parseFloat($("#usd_to_uah").val());
		var price_eur = parseFloat($("#eur_to_uah").val());
	}	
	var invoice_income = $("#invoice_income").val();
	var date = $("#invoice_data").val();
	
	var empty_row = 0;
	var rows = $("#income_str tbody tr").length;
	if (rows > 0) {
		for (i = 1; i < rows; i++) {
			if ($('#article_nr_displStr_'+i).val() !== "") {
				empty_row++;
			}
		}
	}	
	
	swal({
		title: "Провести накладну і зафіксувати?",
		text: "Подальше внесення змін у будь-який розділ накладної буде заблоковано", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Провести", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (income_id.length > 0 && seller !== "" && provider !== "" && price !== 0 && invoice_income !== "" && date !== "") {
				if ((parseInt($("#income_type_id").val()) > 0 && price_usd !== 0 && price_eur !== 0) || (parseInt($("#income_type_id").val()) === 0)) {
					if (status_income === "") {
						if (empty_row != 0) {
							$("#make_income").addClass("disabled");
							JsHttpRequest.query($rcapi,{ 'w':'makeIncomeCardFinish','income_id':income_id},
							function (result, errors){ if (errors) {alert(errors);} if (result){
								if (result["answer"] == 1) {
									swal("Збережено!", "Накладну проведено", "success");
									showIncomeCard(income_id);
								} else {
									swal("Помилка!", result["error"], "error");
								}
							}}, true);
						} else {
							swal("Відмінено", "Додайте хоча б один індекс!", "error");
						}
					} else {
						swal("Відмінено", "В базе уже есть такой приходной документ: " + status_income, "error");
					}
				} else {
					swal("Відмінено", "Не заповнені курси валют!", "error");
				}
			} else {
				swal("Відмінено", "Не заповнені всі поля!", "error");
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function makeIncomeCardFinish() {
    let seller = $("#client_seller").val();
	// var income_id=$("#income_id").val();
	// var provider=$("#client_name").val();
	// var price=parseFloat($("#cours_to_uah").val());
	// if (parseInt($("#income_type_id").val())>0) {
	// 	var price_usd=parseFloat($("#usd_to_uah").val());
	// 	var price_eur=parseFloat($("#eur_to_uah").val());
	// }
    let invoice_income = $("#invoice_income").val();
    let date = $("#invoice_data").val();
    let status = $("#income_price_status").val();
	if (status === "1") {
		alert("error");
	} else {
		JsHttpRequest.query($rcapi,{ 'w':'checkInvoiceIncome', 'invoice_income':invoice_income, 'seller':seller, 'date':date},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				if (parseInt($("#income_type_id").val()) > 0) {
					recalculateIncomeStr();
				} else {
					recalculateIncomeStrLocal();
				}
				if (result["uktzed_status"] == 1) {
					swal({
							title: "Продовжити проведення документу?",
							text: "В документі не в усіх артикулах вказаний код УКТЗЕД!", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
							confirmButtonText: "Продовжити", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
						},
						function (isConfirm) {
							if (isConfirm) {
								cardFinish(result["content"]);
							} else {
								swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
							}
						});
				} else {
					cardFinish(result["content"]);
				}

		}}, true);
	}
}

function showCsvUploadForm(income_id) {
	$("#fileIncomesCsvUploadForm").modal("show");
	$("#csv_income_id").val(income_id);
	var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$("#fileIncomesCsvUploadForm").modal("hide");
		importIncomeStrCSV(income_id);
	});
}

function setIncomeBarcodes(income_id) {
    swal({
            title: "Виставити усім індексам накладної штрих-код автоматично?",
            text: "Подальші зміни можна буде переглянути у розділі 'Номенклатура'.", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Продовжити", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
				$("#FormModalWindow").modal("show");
				JsHttpRequest.query($rcapi,{ 'w':'setIncomeBarcodes', 'income_id':income_id},
					function (result, errors){ if (errors) {alert(errors);} if (result){
                        swal.close();
						$("#FormModalBody").html(result.content);
						$("#FormModalLabel").html("Штрих-коди");
					}}, true);
			} else {
            	swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
		});
}

function setIncomePrices(income_id) {
    $("#FormModalWindow").modal("show");
    JsHttpRequest.query($rcapi,{ 'w':'setIncomePrices', 'income_id':income_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalBody").html(result.content);
            $("#FormModalLabel").html("Начислення націнки");
            numberOnlyPlace("price_numbers");
        }}, true);
}

function saveIncomeArticlePriceRating(income_id) {
    let kol_elm=12;
    let minMarkup=$("#minMarkup").val();
    let prc=[]; let prs=[];
    let price=0; let percent=0;
    let price_cash_id = $("#price_cash_id option:selected").val();
    for (var i=0;i<=kol_elm;i++){
        price=$("#artRatingPrice_"+i).val();
        if (price=="" || price=="NaN"){price=prev_price;}
        prc[i]=price;
        percent=$("#artRatingPersent_"+i).val();
        if (percent==""){percent=0;}
        prs[i]=percent;
    }
    swal({
            title: "Виставити усім індексам накладної націнку?",
            text: "Подальші зміни можна буде переглянути у розділі 'Номенклатура'.", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Продовжити", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                JsHttpRequest.query($rcapi,{ 'w':'saveIncomeArticlePriceRating', 'income_id':income_id, 'price_cash_id':price_cash_id, 'minMarkup':minMarkup, 'prs':prs},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        swal.close();
                        swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                        $("#FormModalWindow").modal("hide");
                    }}, true);
            } else {
            	swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function showPreArticlePriceRating(income_id) {
    let kol_elm=12;
    let prs=[];
    let percent=0;
    let price_cash_id = $("#price_cash_id option:selected").val();
    for (var i = 0; i <= kol_elm; i++) {
        percent = $("#artRatingPersent_"+i).val();
        if (percent == "") {
        	percent = 0;
        }
        prs[i] = percent;
    }
    JsHttpRequest.query($rcapi,{ 'w':'showPreArticlePriceRating', 'income_id':income_id, 'price_cash_id':price_cash_id, 'prs':prs},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#table_body").html(result.content);
        }}, true);
}

function recalculateMadeIncome(income_id) {
    JsHttpRequest.query($rcapi,{ 'w':'recalculateMadeIncome', 'income_id':income_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            showIncomeCard(income_id);
        }}, true);
}

function backIncome(income_id) {
	JsHttpRequest.query($rcapi,{ 'w':'backIncome', 'income_id':income_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			swal("Все виконано!", "Внесені Вами зміни успішно збережені.", "success");
		}}, true);
}