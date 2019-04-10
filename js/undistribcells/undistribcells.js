var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

function showUndistribCellsCard(storage_cells_id,income_id){
	if (income_id<=0 || income_id=="" || storage_cells_id=="" || storage_cells_id<=0){toastr["error"](errs[0]);}
	if (income_id>0 && storage_cells_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showUndistribCellsCard', 'storage_cells_id':storage_cells_id, 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#UndistribCellsCard").modal('show');
			document.getElementById("UndistribCellsCardBody").innerHTML=result["content"];
			$('#undistrib_cell_tabs').tab();
			$("#cash_id").select2({placeholder: "Виберіть валюту",dropdownParent: $("#IncomeCard")});
			$('#income_data').datepicker({format: "yyyy-mm-dd",autoclose:true})
			$('.i-checks').iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
			numberOnly();
		}}, true);
	}
}

function showStorageCellSelectForm(art_id,income_id,storage_id,amount){
	if (art_id>0 && income_id>0 && storage_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showStorageCellSelectForm', 'art_id':art_id, 'income_id':income_id, 'storage_id':storage_id, 'amount':amount}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
			document.getElementById("FormModalBody").innerHTML=result["content"];
			$("#storage_cell_id").select2({placeholder: "Виберіть Комірку",dropdownParent: $("#IncomeCard")});
			numberOnly();
		}}, true);
	}
}

function saveUndistribCellsStorageCellForm(){
	swal({
		title: "Зберегти переміщення у комірку?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var art_id=$("#art_id").val();
			var income_id=$("#income_id").val();
			var storage_id=$("#storage_id").val();
			var amount=$("#amount").val();
			var storage_cells_id=$("#storage_cells_id option:selected").val();
			if (income_id.length>0 && storage_id.length>0 && amount.length>0 && storage_cells_id.length>0 && art_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveUndistribCellsStorageCellForm','art_id':art_id,'income_id':income_id,'storage_id':storage_id,'storage_cells_id':storage_cells_id,'amount':amount},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Збережено.", "success");
						$("#FormModalWindow").modal("hide");
						showUndistribCellsCard($("#cur_storage_cells_id").val(),income_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function importIncomeStrCSV(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showImportIncomeStrCSVform', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML=result["header"];
		}}, true);
	}
}

function loadRegionSelectList(){
	var state_id=$("#state_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeRegionSelectList', 'state_id':state_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("region_id").innerHTML=result["content"];
		$("#region_id").select2({placeholder: "Виберіть район",dropdownParent: $("#IncomeCard")});
	}}, true);
}

function loadCitySelectList(){
	var region_id=$("#region_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeCitySelectList', 'region_id':region_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("city_id").innerHTML=result["content"];
		$("#city_id").select2({placeholder: "Виберіть населений пункт",dropdownParent: $("#IncomeCard")})
		.on('select2:close', function() {var el = $(this);
			if(el.val()==="NEW") { var newval = prompt("Введіть нове місто: ");
			  if(newval !== null) { 
				var region_id=$("#region_id option:selected").val();
				JsHttpRequest.query($rcapi,{ 'w': 'addNewCity', 'region_id':region_id, 'name':newval}, 
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					el.append('<option id="'+result["id"]+'">'+newval+'</option>').val(newval);
				}}, true);
			  }
			}
		  });
	}}, true);
}

function loadIncomeStorage(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeStorage', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("storage_place").innerHTML=result["content"];
			$('#income_tabs').tab();
			$("#storage_id").select2({placeholder: "Склад зберігання",dropdownParent: $("#IncomeCard")});
			$("#storage_cells_id").select2({placeholder: "комірка зберігання",dropdownParent: $("#IncomeCard")});
		}}, true);
	}
}

function loadStorageCellsSelectList(){
	var storage_id=$("#storage_id option:selected").val();
	if (storage_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeStorageCellsSelectList', 'storage_id':storage_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("storage_cells_id").innerHTML=result["content"];
			$("#storage_cells_id").select2({placeholder: "комірка зберігання",dropdownParent: $("#IncomeCard")});
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
			var storage_id=$("#storage_id option:selected").val();
			var storage_cells_id=$("#storage_cells_id option:selected").val();
			if (income_id.length>0 && storage_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveIncomeStorage','income_id':income_id,'storage_id':storage_id,'storage_cells_id':storage_cells_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
					}
					else{ swal("Помилка!", result["error"], "error");}
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
			document.getElementById("spend_place").innerHTML=result["content"];
			$('#income_tabs').tab();
			$("#cash_id").select2({placeholder: "Основна валюта",dropdownParent: $("#IncomeCard")});
			$("#country_cash_id").select2({placeholder: "Національна валюта",dropdownParent: $("#IncomeCard")});
			$("#price_lvl").select2({placeholder: "Прайс",dropdownParent: $("#IncomeCard")});
			$("#credit_cash_id").select2({placeholder: "Валюта кредиту",dropdownParent: $("#IncomeCard")});
		}}, true);
	}
}

function showIncomeSpendItemRow(income_id, spend_item_id, str_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showIncomeSpendItemRow', 'income_id':income_id, 'spend_item_id':spend_item_id, 'str_id':str_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML=result["header"];
			$('#str_data').datepicker({format: "yyyy-mm-dd",autoclose:true})
			numberOnly();
		}}, true);
	}
}

function summSpendRowForm(){
	var cash=parseFloat($("#str_summ_cash").val());
	var kours=parseFloat($("#str_kours").val());
	var summ=cash*kours;
	$("#str_summ_uah").val(summ);
}

function saveIncomeSpendStrForm(income_id,spend_item_id,str_id){
	var caption=$("#str_caption").val();
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
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function showIncomeSpendItemFileUpload(income_id,str_id){
	$("#cdn_income_str_id").val(str_id);
	$('#fileIncomeStrUploadForm').modal('show');
	var myDropzone4 = new Dropzone("#myDropzone4",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone4.removeAllFiles(true);
	myDropzone4.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$('#fileIncomeStrUploadForm').modal('hide');
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
				JsHttpRequest.query($rcapi,{ 'w':'dropIncomeSpendItemRow','income_id':income_id,'spend_item_id':spend_item_id,'str_id':str_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadIncomeSpend(income_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function showIncomeCountrySearchForm(pos, art_id, country_id){
	$("#FormModalWindow").modal("show");
	JsHttpRequest.query($rcapi,{ 'w': 'showIncomeCountrySearchForm', 'country_id':country_id,'pos':pos}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Країна походження";
		setTimeout(function(){
		  $('#datatable_country').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCountryDocument(id,name){
	var pos=$("#doc_pos").val();
	$("#countryIdStr_"+pos).val(id);
	$("#countryAbrStr_"+pos).val(name);
	$("#FormModalWindow").modal('hide');
	getRateTypeDeclarationdocumentPos(pos);
}

function showIncomeCostumsSearchForm(pos, art_id, costums_id){
	$("#FormModalWindow").modal("show");
	JsHttpRequest.query($rcapi,{ 'w': 'showIncomeCostumsSearchForm', 'costums_id':costums_id,'pos':pos}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Митний код";
		setTimeout(function(){
		  $('#datatable_costums').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCostumsDocument(id,name){
	var pos=$("#doc_pos").val();
	$("#costumsIdStr_"+pos).val(id);
	$("#costumsStr_"+pos).val(name);
	$("#FormModalWindow").modal('hide');
	getRateTypeDeclarationdocumentPos(pos);
}

function getRateTypeDeclarationdocumentPos(pos){
	var costums_id=$("#costumsIdStr_"+pos).val();
	var country_id=$("#countryIdStr_"+pos).val();
	JsHttpRequest.query($rcapi,{ 'w': 'getRateTypeDeclarationdocumentPos', 'costums_id':costums_id,'country_id':country_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#rateStr_"+pos).val(result["rate"]);
		$("#typeDeclarationStr_"+pos).val(result["type_declaration"]);
		$("#typeDeclarationIdStr_"+pos).val(result["type_declaration_id"]);
	}}, true);
}

//----------------------------------------------------------------------------------------------------------------------------------
//						CALCULATE STR INCOME
//----------------------------------------------------------------------------------------------------------------------------------

function setIncomeVat(){
	var income_id=$("#income_id").val();
	var vat_use=0;if (document.getElementById("vat_use").checked){vat_use=1;}
	JsHttpRequest.query($rcapi,{ 'w': 'setIncomeVat', 'income_id':income_id,'vat_use':vat_use}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if(result["answer"]==1){
			recalculateIncomeStrLocal();
		}else{swal("Помилка", result["err"], "error");}
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

function getPWeight(ikr,weight_netto){ var w=ws=0; 
	for (var i=1;i<=ikr;i++){var wn=parseFloat($("#weightNettoStr_"+i).val().replace(/[,]+/g, '.'));	if (wn>0){ws+=wn; }}
	w=round(weight_netto/ws*100,4).toFixed(4);
	return w;
}

function countInvoiceSumm(ikr){var invoice_summ=0;
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
	var income_id=$("#income_id").val();
	var ikr=$("#kol_row").val();
	var invoice_summ=countInvoiceSumm(ikr);	$("#invoice_summ").val(invoice_summ); 
	var tz=parseFloat($("#income_spend_item_1").val());
	var tl=parseFloat($("#income_spend_item_2").val());
	var rb=parseFloat($("#income_spend_item_3").val());
	var ro=parseFloat($("#income_spend_item_4").val());
	var cours_to_uah=parseFloat($("#cours_to_uah").val()); if (cours_to_uah==0 || cours_to_uah==""){cours_to_uah=1;$("#cours_to_uah").val(cours_to_uah);}
	var cours_to_uah_nbu=parseFloat($("#cours_to_uah_nbu").val());if (cours_to_uah_nbu==0 || cours_to_uah_nbu==""){cours_to_uah_nbu=1;$("#cours_to_uah_nbu").val(cours_to_uah_nbu);}
	var cash_id=$("#cash_id option:selected").val();
	var costums_pd_uah=costums_pp_uah=0;
	var costums_summ_uah=0;
	
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
			
			var ts_uah=round(parseFloat(fs_uah)+parseFloat(tz_uah),4);ts_uah=ts_uah.toFixed(4);
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
			if (cash_id==1){kurs=1;}if (cash_id==2){kurs=parseFloat($("#usd_to_uah").val());}if (cash_id==3){kurs=parseFloat($("#eur_to_uah").val());}
			alrt+="kurs="+kurs+"\n";
			
			var suvsdo=parseFloat(price_income*amount)+((parseFloat(tz_uah)+parseFloat(tl_uah)+parseFloat(rb_uah)+parseFloat(ro_uah)+parseFloat(po_b_uah)+parseFloat(nds_uah))/parseFloat(kurs));
			suvsdo=round(suvsdo/parseFloat(amount),4);
			suvsdo=suvsdo.toFixed(4);
			$("#price_man_cashinStr_"+i).val(suvsdo);
			alrt+="step3) suvsdo="+suvsdo+"\n";
			
			var su_usd=round(suvsdo*kurs/kurs_usd,4);su_usd=su_usd.toFixed(4);
			$("#price_man_usdStr_"+i).val(su_usd);
			alrt+="step4) su_usd="+su_usd+"\n";
			
			var sb_uah=parseFloat(price_income*amount*cours_to_uah)+(parseFloat(tz_uah)+parseFloat(tl_uah)+parseFloat(rb_uah)+parseFloat(po_b_uah));
			sb_uah=sb_uah/parseFloat(amount);
			sb_uah=round(sb_uah,4);sb_uah=sb_uah.toFixed(4);
			$("#price_buh_uahStr_"+i).val(sb_uah);
			alrt+="step5) sb_uah="+sb_uah+"\n";
			
			
			var su_uah=parseFloat(price_income*amount*cours_to_uah_nbu)+(parseFloat(tz_uah)+parseFloat(tl_uah)+parseFloat(rb_uah)+parseFloat(ro_uah)+parseFloat(po_b_uah)+parseFloat(nds_uah));
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
			//alert(alrt);
		}
	}
	$("#costums_pd_uah").val(costums_pd_uah.toFixed(2));
	$("#costums_pp_uah").val(costums_pp_uah.toFixed(2));
	
	costums_summ_uah=parseFloat(costums_pd_uah)+parseFloat(costums_pp_uah);
	$("#costums_summ_uah").val(costums_summ_uah.toFixed(2));
}


function recalculateIncomeStrLocal(){
	var income_id=$("#income_id").val();
	var ikr=$("#kol_row").val();
	var vat_use=0;if (document.getElementById("vat_use").checked){vat_use=1;}
	var invoice_summ=countInvoiceSumm(ikr);	$("#invoice_summ").val(invoice_summ); 
	var tz=parseFloat($("#income_spend_item_1").val());
	var tl=parseFloat($("#income_spend_item_2").val());
	var rb=parseFloat($("#income_spend_item_3").val());
	var ro=parseFloat($("#income_spend_item_4").val());
	var cours_to_uah=parseFloat($("#cours_to_uah").val()); if (cours_to_uah==0 || cours_to_uah==""){cours_to_uah=1;$("#cours_to_uah").val(cours_to_uah);}
	var cash_id=$("#cash_id option:selected").val();
	var costums_pd_uah=costums_pp_uah=0;
	var costums_summ_uah=0;
	
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
			
			alert(alrt);
		}
	}	
}

function preconfirmIncomeDetails(){
	swal({
		title: "Зберегти зміни у розділі \"Реквізити\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			saveIncomeDetails();
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveIncomeDetails(){
	var income_id=$("#income_id").val();
	var address_jur=$("#address_jur").val();
	var address_fakt=$("#address_fakt").val();
	var edrpou=$("#edrpou").val();
	var svidotctvo=$("#svidotctvo").val();
	var vytjag=$("#vytjag").val();
	var vat=$("#vat").val();
	var mfo=$("#mfo").val();
	var bank=$("#bank").val();
	var account=$("#account").val();
	var nr_details=$("#nr_details").val();
	var not_resident=0; if (document.getElementById("not_resident").checked){not_resident=1;}else{nr_details="";}

	if (income_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveIncomeDetails','income_id':income_id,'address_jur':address_jur,'address_fakt':address_fakt,'edrpou':edrpou,'svidotctvo':svidotctvo,'vytjag':vytjag,'vat':vat,'mfo':mfo, 'bank':bank,'account':account,'not_resident':not_resident,'nr_details':nr_details},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			}
			else{ swal("Помилка!", result["error"], "error");}
			
		}}, true);
	}
}

function loadIncomeContacts(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeContacts', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("contacts_place").innerHTML=result["content"];
			$('#catalogue_tabs').tab();
		}}, true);
	}
}

function showIncomeContactForm(income_id, contact_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showIncomeContactForm', 'income_id':income_id, 'contact_id':contact_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
		}}, true);
	}
}

function dropIncomeContact(income_id,contact_id,contact_name){
	swal({
		title: "Видалити контакт \""+contact_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (income_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropIncomeContact','income_id':income_id,'contact_id':contact_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadIncomeContacts(income_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveIncomeContactForm(income_id,contact_id){
	var contact_name=$("#contact_name").val();
	swal({
		title: "Зберегти зміни контакту \""+contact_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var contact_name=$("#contact_name").val();
			var contact_post=$("#contact_post").val();
			var cn=$("#contact_con_kol").val();

			var con_id = []; var sotc_cont = []; var contact_value = []; 
			for (var i=1;i<=cn;i++){
				con_id[i]=$("#con_id_"+i).val();
				sotc_cont[i]=$("#sotc_cont_"+i+" option:selected").val();
				contact_value[i]=$("#contact_value_"+i).val();
			}
			if (income_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveIncomeContactForm','income_id':income_id,'contact_id':contact_id,'contact_name':contact_name,'contact_post':contact_post,'contact_con_kol':cn,'con_id':con_id,'sotc_cont':sotc_cont,'contact_value':contact_value},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadIncomeContacts(income_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function preconfirmIncomeGeneralInfo(){
	swal({
		title: "Зберегти зміни у розділі \"Загальна інформація\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			saveIncomeGeneralInfo();
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveIncomeGeneralInfo(){
	var income_id=$("#income_id").val();
	var org_type=$("#org_type option:selected").val();
	var income_name=$("#income_name").val();
	var income_full_name=$("#income_full_name").val();
	var phone=$("#phone").val();
	var email=$("#email").val();
	var parrent_id=$("#parrent_id").val();
	var country_id=$("#country_id option:selected").val();
	var state_id=$("#state_id option:selected").val();
	var region_id=$("#region_id option:selected").val();
	var city_id=$("#city_id option:selected").val(); 
	var c_category_kol=$("#c_category_kol").val();
	var c_category=[]; var cc="";
	for (var i=1;i<=c_category_kol;i++){var cc=0;if(document.getElementById("c_category_"+i).checked) { cc=$("#c_category_"+i).val(); }c_category[i]=cc;}
	if (income_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveIncomeGeneralInfo','income_id':income_id,'org_type':org_type,'name':income_name,'full_name':income_full_name,'phone':phone,'email':email, 'parrent_id':parrent_id, 'country_id':country_id, 'state_id':state_id, 'region_id':region_id, 'city_id':city_id, 'c_category_kol':c_category_kol, 'c_category':c_category},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
				//var art=$("#catalogue_art").val();
				//if (art.length>0){
				//	catalogue_income_search();
				//}
			}
			else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function preconfirmIncomeConditions(income_id){
	swal({
		title: "Зберегти зміни у розділі \"Умови\"?",text: "Внесені Вами зміни вплинуть на роботу Клієнта",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			saveIncomeConditions(income_id);
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveIncomeConditions(income_id){
	var cash_id=$("#cash_id option:selected").val();
	var country_cash_id=$("#country_cash_id option:selected").val();
	var price_lvl=$("#price_lvl option:selected").val();
	var payment_delay=$("#payment_delay").val();
	var credit_limit=$("#credit_limit").val();
	var credit_cash_id=$("#credit_cash_id option:selected").val();
	var credit_return=$("#credit_return").val();
	if (income_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveIncomeConditions','income_id':income_id,'cash_id':cash_id,'country_cash_id':country_cash_id,'price_lvl':price_lvl,'payment_delay':payment_delay,'credit_limit':credit_limit,'credit_cash_id':credit_cash_id,'credit_return':credit_return},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			}
			else{ swal("Помилка!", result["error"], "error");}
			
		}}, true);
	}
}

function showIncomeClientList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showIncomeClientList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Контрагенти";
		setTimeout(function() { $('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}}); }, 500);
	}}, true);
}

function setIncomeClient(id,name){
	$('#client_id').val(id);
	$('#client_name').val(name);
	$("#FormModalWindow").modal('hide');
	document.getElementById("FormModalBody").innerHTML="";
	document.getElementById("FormModalLabel").innerHTML="";
}

function unlinkIncomeClient(income_id){
	swal({
		title: "Відвязати контагента від накладної?",text: "Внесені Вами зміни вплинуть на роботу Контагента",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'unlinkIncomeClient', 'income_id':income_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					$('#client_id').val("0");
					$('#client_name').val("");
					swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
				}
				else{ toastr["error"](result["error"]); }
			}}, true);	
		} else {
			swal("Відмінено", "Операцію анульовано.", "error");
		}
	});
}

function showIncomeClientSellerList(client_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showIncomeClientSellerList', 'client_id':client_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Контрагенти";
		setTimeout(function() { $('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}}); }, 500);
	}}, true);
}

function setIncomeClientSeller(id,name){
	$('#client_seller').val(id);
	$('#client_seller_name').val(name);
	$("#FormModalWindow").modal('hide');
	document.getElementById("FormModalBody").innerHTML="";
	document.getElementById("FormModalLabel").innerHTML="";
}

function unlinkIncomeClientSeller(income_id){
	swal({
		title: "Відвязати контагента від накладної?",text: "Внесені Вами зміни вплинуть на роботу Контагента",
		type: "warning",allowOutsideClick:true,	allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'unlinkIncomeClientSeller', 'income_id':income_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					$('#client_seller').val("0");
					$('#client_seller_name').val("");
					swal("Виконано!", "Внесені Вами зміни успішно збережені.", "success");
				}
				else{ toastr["error"](result["error"]); }
			}}, true);	
		} else {
			swal("Відмінено", "Операцію анульовано.", "error");
		}
	});
}

function showIncomeArticleSearchForm(i,art_id,brand_id,article_nr_displ){
	JsHttpRequest.query($rcapi,{ 'w': 'showIncomeArticleSearchForm', 'art_id':art_id,'brand_id':brand_id,'article_nr_displ':article_nr_displ}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML="Номенклатура";
		$("#row_pos").val(i);
		$('#datatable_parrent').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
	}}, true);
}

function setArticleToDoc(art_id,article_nr_displ,brand_id,brand_name,costums_id,costums_code,country_id,country_name){
	var pos=$("#row_pos").val();
	$('#artIdStr_'+pos).val(art_id);
	$('#article_nr_displStr_'+pos).val(article_nr_displ);
	$('#brandIdStr_'+pos).val(brand_id);
	$('#brandNameStr_'+pos).val(brand_name);
	$('#costumsIdStr_'+pos).val(costums_id);
	$('#costumsStr_'+pos).val(costums_code);
	$('#countryIdStr_'+pos).val(country_id);
	$('#countryAbrStr_'+pos).val(country_name);
	$("#FormModalWindow").modal('hide');
	document.getElementById("FormModalBody").innerHTML="";
	document.getElementById("FormModalLabel").innerHTML="";
	$("#row_pos").val("");
}

function showIncomeUserForm(income_id, user_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		$("#FormModalWindow").modal("show");
		JsHttpRequest.query($rcapi,{ 'w': 'showIncomeUserForm', 'income_id':income_id, 'user_id':user_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("FormModalBody").innerHTML=result["content"];
			$('.i-checks').iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
		}}, true);
	}
}

function randString(id){
	var dataSet = $(id).attr('data-character-set').split(',');
	var possible = '';  var text = '';
	if($.inArray('a-z', dataSet) >= 0){possible += 'abcdefghijklmnopqrstuvwxyz';}
	if($.inArray('A-Z', dataSet) >= 0){possible += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';}
	if($.inArray('0-9', dataSet) >= 0){possible += '0123456789';}
	if($.inArray('#', dataSet) >= 0){possible += '![]{}()%&*$#^<>~@|';}
	for(var i=0; i < $(id).attr('data-size'); i++) {text += possible.charAt(Math.floor(Math.random() * possible.length));}
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
			if (income_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropIncomeUser','income_id':income_id,'user_id':user_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadIncomeUsers(income_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveIncomeUserForm(income_id,user_id){
	var user_name=$("#user_name").val();
	swal({
		title: "Зберегти зміни Користувача \""+user_name+"\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var user_name=$("#user_name").val();
			var user_email=$("#user_email").val();
			var user_phone=$("#user_phone").val();
			var user_pass=$("#user_pass").val();
			var user_main=0; if(document.getElementById("user_main").checked) { user_main=1;}

			if (income_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveIncomeUserForm','income_id':income_id,'user_id':user_id,'user_name':user_name,'user_email':user_email,'user_phone':user_phone,'user_pass':user_pass,'user_main':user_main},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadIncomeUsers(income_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
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
			document.getElementById("label_comments").innerHTML=result["label"];
		}}, true);
	}
}

function loadIncomeCommets(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeCommets', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("income_commets_place").innerHTML=result["content"];
		}}, true);
	}
}

function saveIncomeComment(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		var comment=$("#income_comment_field").val();
		if (comment.length<=0){toastr["error"]("Напишіть коментар спочатку");}
		if (comment.length>0){
			JsHttpRequest.query($rcapi,{ 'w': 'saveIncomeComment', 'income_id':income_id, 'comment':comment}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					loadIncomeCommets(income_id); 
					$("#income_comment_field").val("");
					loadIncomeCommetsLabel(income_id);
				}
				else{ toastr["error"](result["error"]); }
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
				if (result["answer"]==1){ loadIncomeCommets(income_id); toastr["info"]("Запис успішно видалено");loadIncomeCommetsLabel(income_id);}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function loadIncomeCDN(income_id){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadIncomeCDN', 'income_id':income_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("income_cdn_place").innerHTML=result["content"];
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
		$('#fileIncomeCDNUploadForm').modal('hide');
		loadIncomeCDN(income_id);
	});
}

function showIncomeCDNDropConfirmForm(income_id,file_id,file_name){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'incomeCDNDropFile', 'income_id':income_id, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadIncomeCDN(income_id); toastr["info"]("Файл успішно видалено"); }
				else{ toastr["error"](result["error"]); }
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
			document.getElementById("income_details_files_place").innerHTML=result["content"];
		}}, true);
	}
}

function fileIncomeDetailsUploadForm(income_id,file_type){
	$("#dtls_income_id").val(income_id);
	$("#dtls_file_type").val(file_type);
	$("#fileIncomeDetailsUploadForm").modal("show");
	var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$('#fileIncomeDetailsUploadForm').modal('hide');
		viewIncomeDetailsFile(income_id,file_type);
	});
}

function showIncomeDetailsDropConfirmForm(income_id,file_type,file_id,file_name){
	if (income_id<=0 || income_id==""){toastr["error"](errs[0]);}
	if (income_id>0){
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'incomeDetailsDropFile', 'income_id':income_id, 'file_type':file_type, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ viewIncomeDetailsFile(income_id,file_type); toastr["info"]("Файл успішно видалено"); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function showCountryManual(){
	var country_id=$("#country_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCountryManual','country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CountryModalWindow").modal('show');
		document.getElementById("CountryBody").innerHTML=result["content"];
		setTimeout(function(){
		  $('#datatable_country').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCountry(id,name){
	$("#country_id").val(id);
	$("#country_name").val(name);
	$("#CountryModalWindow").modal('hide');
}

function showCountryForm(country_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCountryForm','country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML=result["header"];
	}}, true);
}

function saveIncomeCountryForm(){
	var id=$("#form_country_id").val();
	var name=$("#form_country_name").val();
	var alfa2=$("#form_country_alfa2").val();
	var alfa3=$("#form_country_alfa3").val();
	var duty=$("#form_country_duty").val();
	var risk=$("#form_country_risk").val();
	JsHttpRequest.query($rcapi,{ 'w':'saveIncomeCountryForm','id':id,'name':name,'alfa2':alfa2,'alfa3':alfa3,'duty':duty,'risk':risk},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			$("#country_id").val(id);
			showCountryManual();
		}
		else{ swal("Помилка!", result["error"], "error");}
	}}, true);
}

function showCostumsManual(){
	var costums_id=$("#costums_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsManual','costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CostumsModalWindow").modal('show');
		document.getElementById("CostumsBody").innerHTML=result["content"];
		setTimeout(function(){
		  $('#datatable_costums').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCostums(id,name){
	$("#costums_id").val(id);
	$("#costums_name").val(name);
	$("#CostumsModalWindow").modal('hide');
}

function showCostumsForm(costums_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsForm','costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML=result["header"];
	}}, true);
}

function saveIncomeCostumsForm(){
	var id=$("#form_costums_id").val();
	var name=$("#form_costums_name").val();
	var preferential_rate=$("#form_costums_preferential_rate").val();
	var full_rate=$("#form_costums_full_rate").val();
	var type_declaration=$("#form_costums_type_declaration").val();
	var sertification=$("#form_costums_sertification").val();
	var gos_standart=$("#form_costums_gos_standart").val();
	JsHttpRequest.query($rcapi,{ 'w':'saveIncomeCostumsForm','id':id,'name':name,'preferential_rate':preferential_rate,'full_rate':full_rate,'type_declaration':type_declaration,'sertification':sertification,'gos_standart':gos_standart},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			$("#costums_id").val(id);
			showCostumsManual();
		}
		else{ swal("Помилка!", result["error"], "error");}
	}}, true);
}

function makeIncomeCardFinish(){
	var income_id=$("#income_id").val();
	swal({
		title: "Провести накладну і зафіксувати?",
		text: "Подальше внесення змін у будь який розділ накладної буде заблоковано", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Провести", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (income_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'makeIncomeCardFinish','income_id':income_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Накладну проведено", "success");
						showIncomeCard(income_id)
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}