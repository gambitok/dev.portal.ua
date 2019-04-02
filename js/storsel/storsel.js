var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

$(document).ready(function() {
    shortcut.add("Insert", function() {
		var  select_id=$("#select_id").val();
		var  storsel_type_id=$("#storsel_type_id").val();
		if (select_id>0){
			if (storsel_type_id===0){addNewRowLocal();}
			if (storsel_type_id===1){addNewRow();}
		}
	});
	setTimeout(function(){updateStorselRange();},15*1000);
});

$(window).bind('beforeunload', function(e){
    if($('#select_id')){
		closeStorselCard();
		e=null;
	}
    else e=null; 
});

function updateStorselStatus(select_id) {
	$("#storsel_send").attr("disabled", true); //disable button
	JsHttpRequest.query($rcapi,{ 'w': 'updateStorselStatus', 'select_id':select_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
	if (result["answer"]==1){ 
		$("#StorselCard").modal('hide');
		document.getElementById('alert_ok').play();
		updateStorselRange();
		toastr["info"]("Виконано!"); 
	} 
	else{ toastr["error"](result["error"]); }
	}}, true);
}

function show_storsel_search(inf){
	//var art=$("#catalogue_art").val();
	//if (art.length<=2){	toastr["warning"](errs[1]);}
	//if (art.length>2){
		$("#storsel_range").empty();
		JsHttpRequest.query($rcapi,{ 'w': 'show_storsel_search'}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("storsel_range").innerHTML=result["content"];
			if (inf===1){toastr["info"]("Виконано!");}
		}}, true);
	//}
}

function updateStorselRange(press_btn){
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
	var pred=$("#storsel_count").val();
	JsHttpRequest.query($rcapi,{ 'w': 'show_storsel_search', 'status':status}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
//		console.log("content.length="+result.content[0].length + " - " + result.content[1] + " - " + pred);
		$("#storsel_range").html(result.content[0]);		
		$("#storsel_count").val(result.content[1]);
		console.log('оновлено');
		if (pred!==result.content[1]){
			if (pred!==0) { console.log("був звук"); document.getElementById('alert_ok').play();}
		} 
		setTimeout(function(){updateStorselRange();},15*1000);
	}}, true);
}

function showStorselCard(select_id){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showStorselCard', 'select_id':select_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#StorselCard").modal('show');
			document.getElementById("StorselCardBody").innerHTML=result["content"];
			document.getElementById("StorselCardLabel").innerHTML=result["doc_prefix_nom"];
			$('#storsel_tabs').tab();
//			$('#storsel_data').datepicker({format: "yyyy-mm-dd",autoclose:true})
			$('.i-checks').iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
//			$("#storage_id_to").select2({placeholder: "Виберіть склад",dropdownParent: $("#JmovingCard")});
			numberOnly();
		}}, true);
	}
}

function unlockStorselCard(select_id){
	if (select_id){
		JsHttpRequest.query($rcapi,{ 'w': 'unlockStorselCard', 'select_id':select_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#StorselCard").modal('hide');document.getElementById("StorselCardBody").innerHTML="";document.getElementById("StorselCardLabel").innerHTML="";
		}}, true);
	}else{
		$("#StorselCard").modal('hide');document.getElementById("StorselCardBody").innerHTML="";document.getElementById("StorselCardLabel").innerHTML="";
	}
}

function closeStorselCard(){
	if ($("#select_id")){
		var select_id=$("#select_id").val();
		JsHttpRequest.query($rcapi,{ 'w': 'closeStorselCard', 'select_id':select_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#StorselCard").modal('hide');document.getElementById("StorselCardBody").innerHTML="";document.getElementById("StorselCardLabel").innerHTML="";
		}}, true);
	}else{
		$("#StorselCard").modal('hide');document.getElementById("StorselCardBody").innerHTML="";document.getElementById("StorselCardLabel").innerHTML="";
	}
}

function printStorselView(select_id){
	if (select_id.length>0){
		window.open("/Storsel/printStS1/"+select_id,"_blank","printWindow");
	}
}

function printStorselView2(select_id){
	if (select_id.length>0){
		window.open("/Storsel/printStS2/"+select_id,"_blank","printWindow");
	}
}

function loadStorselCommetsLabel(select_id){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadStorselCommetsLabel', 'select_id':select_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("label_comments").innerHTML=result["label"];
		}}, true);
	}
}

function loadStorselCommets(select_id){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadStorselCommets', 'select_id':select_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("storsel_commets_place").innerHTML=result["content"];
		}}, true);
	}
}

function saveStorselComment(select_id){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		var comment=$("#storsel_comment_field").val();
		if (comment.length<=0){toastr["error"]("Напишіть коментар спочатку");}
		if (comment.length>0){
			JsHttpRequest.query($rcapi,{ 'w': 'saveStorselComment', 'select_id':select_id, 'comment':comment}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					loadStorselCommets(select_id); 
					$("#storsel_comment_field").val("");
					loadStorselCommetsLabel(select_id);
				}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function dropStorselComment(select_id,cmt_id){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		if(confirm('Видалити запис?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'dropStorselComment', 'select_id':select_id, 'cmt_id':cmt_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadStorselCommets(select_id); toastr["info"]("Запис успішно видалено");loadJmovingCommetsLabel(select_id);}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function makesJmovingStorageSelect(){
	var select_id=$("#select_id").val();
	swal({
		title: "Передати в роботу переміщення складу?",
		text: "Подальше внесення змін буде заблоковано", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (select_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'makesJmovingStorageSelect','select_id':select_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Переміщення передано в роботу", "success");
						showJmovingCard(select_id);
						$("#JmovingPreSelect").modal('hide');
						document.getElementById("JmovingPreSelectBody").innerHTML="";document.getElementById("JmovingPreSelectLabel").innerHTML="";
						show_storsel_search(0);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadJmovingStorageSelect(select_id){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingStorageSelect', 'select_id':select_id,'storsel_status':45}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("storsel_storage_select_place").innerHTML=result["content"];
//			numberOnlyLong();
			$('#income_tabs').tab();
		}}, true);
	}
}

function viewStorsel(select_id,storsel_status){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'viewStorsel', 'select_id':select_id,'storsel_status':storsel_status}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow2").modal('show');
			document.getElementById("FormModalBody2").innerHTML=result["content"];
			document.getElementById("FormModalLabel2").innerHTML=result["header"];
			$('#income_tabs').tab();
		}}, true);
	}
}

function collectStorsel(select_id){
	swal({
		title: "Розпочати збирання складського відбору?",
		text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (select_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'collectStorsel','select_id':select_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Зафіксовано!", "", "success");
						showStorselCard(select_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function showStorselBarcodeForm(select_id){
	$("#storsel_check").attr("disabled", true); //disable button
	if (select_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showStorselBarcodeForm','select_id':select_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#FormModalWindow4").modal('show');
				document.getElementById("FormModalBody4").innerHTML=result["content"];
				document.getElementById("FormModalLabel4").innerHTML=result["header"];
			}
			else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function saveStorselBarcodeForm(select_id){
	var barcode=$("#BarCodeInput").val();
	if (select_id.length>0 && barcode.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveStorselBarcodeForm','select_id':select_id,'barcode':barcode},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#BarCodeInput").val("");
				$("#BarCodeInput").focus();
				$("#amr_"+result["row_id"]).html(result["amount_barcode"]);
				$("#amrd_"+result["row_id"]).html(result["dif_amount_barcode"]);
				document.getElementById('ok_art_id').play();
			}
			else{ 
				swal("Помилка!", result["error"], "error");
				document.getElementById('no_art_id').play();
                $("#BarCodeInput").val("");
			}
		}}, true);
	}
}

function finishStorselBarcodeForm(select_id){
	$("#storsel_finish").attr("disabled", true); //disable button
	if (select_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'finishStorselBarcodeForm','select_id':select_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				showStorselCard(select_id);
				$("#FormModalWindow4").modal('hide');
				document.getElementById("FormModalBody4").innerHTML="";
				document.getElementById("FormModalLabel4").innerHTML="";
				swal("Виконано!", "Сканування завершено", "success");
			}
			else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function showJmovingStorageSelectSendTruckForm(select_id){
	swal({
		title: "Передати складські вібдори на відбправку?",
		text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Перадати", cancelButtonText: "Скасувати", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (select_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'setJmovingStorageSelectSendTruck','select_id':select_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						showJmovingCard(select_id);
						swal.close();
						window.open("/Jmoving/printJmSTP/"+select_id,"_blank","printWindow");
					}
					else{ swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		}else {
			swal("Відмінено", "", "error");
		}
	});
}

function showStorselBugForm(select_id,str_id){
	if (select_id.length>0 && str_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showStorselBugForm','select_id':select_id,'str_id':str_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#FormModalWindow5").modal('show');
				document.getElementById("FormModalBody5").innerHTML=result["content"];
				document.getElementById("FormModalLabel5").innerHTML=result["header"];
				numberOnly();
			}
			else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function saveStorselBugForm(select_id,str_id){
	if (select_id.length>0 && str_id.length>0){
		var err=0; 
		var storage_select_bug=$("#storage_select_bug option:selected").val();
		var amount=parseFloat($("#bug_amount").val());
		var dif_amount_barcode=parseFloat($("#bug_dif_amount_barcode").val());
		if (storage_select_bug<=0 || storage_select_bug.length==0){er=1;swal("Помилка!", "Оберіть тип відхилення", "error");}
		if (dif_amount_barcode<=0 || dif_amount_barcode==0){er=1;swal("Помилка!", "Не вказано кількість відхилення", "error");}
		if (dif_amount_barcode>amount){er=1;swal("Помилка!", "Кількість відхилення не повинна перевищувати кількісті відбору", "error");}
		if (err==0){
			swal({
				title: "Зафіксувати складське відхилення?",
				text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
				confirmButtonText: "Зафіксувати", cancelButtonText: "Скасувати", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
			},
			function (isConfirm) {
				if (isConfirm) {
					JsHttpRequest.query($rcapi,{ 'w':'saveStorselBugForm','select_id':select_id,'str_id':str_id,'storage_select_bug':storage_select_bug,'dif_amount_barcode':dif_amount_barcode},
					function (result, errors){ if (errors) {alert(errors);} if (result){  
						if (result["answer"]==1){ 
							viewStorsel(select_id);
							swal.close();
							$("#FormModalWindow5").modal('hide');document.getElementById("FormModalBody5").innerHTML="";document.getElementById("FormModalLabel5").innerHTML="";
							$("#BarCodeInput").val("");
							$("#BarCodeInput").focus();
							$("#ssbug_"+result["row_id"]).html(result["storage_select_bug_name"]);
							$("#amrd_"+result["row_id"]).html(result["dif_amount_barcode"]);
							$("#ambg_"+result["row_id"]).html(result["amount_bug"]);
							$("#amr_"+result["row_id"]).html(result["amount_barcode"]);
							$("#amrns_"+result["row_id"]).html(result["amount_barcode_noscan"]);
						}
						else{ swal("Помилка!", result["error"], "error");}
					}}, true);
				}else {swal("Відмінено", "", "error");}
			});
		}
	}	
}

function showStorselNoscanForm(select_id,art_id,str_id){
	if (select_id.length>0 && art_id.length>0 && str_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showStorselNoscanForm','select_id':select_id,'art_id':art_id,'str_id':str_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#FormModalWindow5").modal('show');
				document.getElementById("FormModalBody5").innerHTML=result["content"];
				document.getElementById("FormModalLabel5").innerHTML=result["header"];
				numberOnly();
			}
			else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function saveStorselNoscanForm(select_id,art_id,str_id){
	if (select_id.length>0 && art_id.length>0 && str_id.length>0){
		var err=0; 
		var amount=parseFloat($("#amount_barcode_noscan").val());
		var dif_amount_barcode=parseFloat($("#noscan_dif_amount_barcode").val());
		if (dif_amount_barcode<=0 || dif_amount_barcode==0){er=1;swal("Помилка!", "Не вказано кількість без сканування", "error");}
		if (dif_amount_barcode>amount){er=1;swal("Помилка!", "Кількість без сканування не повинна перевищувати кількості для приймання", "error");}
		if (err==0){
			swal({
				title: "Зафіксувати пакування товару без сканування штрих-коду?",
				text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
				confirmButtonText: "Зафіксувати", cancelButtonText: "Скасувати", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
			},
			function (isConfirm) {
				if (isConfirm) {
					JsHttpRequest.query($rcapi,{ 'w':'saveStorselNoscanForm','select_id':select_id,'art_id':art_id,'str_id':str_id,'amount_barcode_noscan':amount},
					function (result, errors){ if (errors) {alert(errors);} if (result){  
						if (result["answer"]==1){ 
							swal.close();
							$("#FormModalWindow5").modal('hide');document.getElementById("FormModalBody5").innerHTML="";document.getElementById("FormModalLabel5").innerHTML="";
							$("#BarCodeInput").val("");
							$("#BarCodeInput").focus();
							$("#amrd_"+result["row_id"]).html(result["dif_amount_barcode"]);
							$("#amrns_"+result["row_id"]).html(result["amount_barcode_noscan"]);
						}
						else{ swal("Помилка!", result["error"], "error");}
					}}, true);
				}else {swal("Відмінено", "", "error");}
			});
		}
	}
}

function calculateStorselParams(select_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'calculateStorselParams', 'select_id':select_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            alert(result.content);
        }}, true);
}
