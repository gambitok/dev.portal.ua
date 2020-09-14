var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

function loadClaimList() {
    JsHttpRequest.query($rcapi,{ 'w': 'loadClaimList'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
        	let dt=$("#datatable");
            dt.DataTable().destroy();
            $("#claim_range").html(result["content"]);
            dt.DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
        }}, true);
}

function showClaimCard(claim_id){
	if (claim_id<=0 || claim_id===""){toastr["error"](errs[0]);}
	if (claim_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showClaimCard', 'claim_id':claim_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#ClaimCard").modal('show');
            $("#ClaimCardLabel").html(claim_id);
            $("#ClaimCardBody").html(result.content);
			$("#claim_text_ru").markdown({autofocus:false,savable:false});
			$("#claim_text_ua").markdown({autofocus:false,savable:false});
			$("#claim_text_en").markdown({autofocus:false,savable:false});
		}}, true);
	}
}

function saveClaimCard(claim_id) {
    swal({
            title: "Зберегти зміни у розділі \"Претензії користувчів\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
            	let client_id=$("#claim_client").val();
                let art_id=$("#claim_art").val();
                let brand_id=$("#claim_brand").val();
                let amount=$("#claim_amount").val();
                let data=$("#claim_data").val();
                let supplier=$("#claim_supplier option:selected").val();
                let manufacturer=$("#claim_manufacturer option:selected").val();
                let state=$("#claim_state option:selected").val();
                let client_invoice=$("#claim_client_invoice").val();
                let comment=$("#claim_comment").val();
                let receipt_doc=$("#claim_receipt_doc").val();
                let kilometers=$("#claim_kilometers").val();
                let text_ru=$("#claim_text_ru").val();
                let text_ua=$("#claim_text_ua").val();
                let text_en=$("#claim_text_en").val();

                if (claim_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'saveClaimCard','claim_id':claim_id,'art_id':art_id,'brand_id':brand_id,'amount':amount,'data':data,'supplier':supplier,'manufacturer':manufacturer,'client_id':client_id,'client_invoice':client_invoice,'comment':comment,'receipt_doc':receipt_doc,'kilometers':kilometers,'state':state,'text_ru':text_ru,'text_ua':text_ua,'text_en':text_en},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                $("#ClaimCard").modal('hide');
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                loadClaimList();
                            }
                            else{ swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function loadClaimAct(claim_id){
    if (claim_id<=0 || claim_id===""){toastr["error"](errs[0]);}
    if (claim_id>0){
        JsHttpRequest.query($rcapi,{ 'w': 'loadClaimAct', 'claim_id':claim_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#ClaimCard").modal('show');
                $("#claim_act").html(result.content);
            }}, true);
    }
}

function saveClaimActCard(claim_id) {
    swal({
            title: "Зберегти зміни у розділі \"Акт рекламації\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let sto=$("#claim_sto").val();
                let amount=$("#claim_amount").val();
                let text_ru=$("#claim_text_ru").val();
                let auto=$("#claim_auto").val();
                let year=$("#claim_year").val();
                let number=$("#claim_number").val();
                let vin=$("#claim_vin").val();
                let kilo1=$("#claim_kilo1").val();
                let kilo2=$("#claim_kilo2").val();
                let date1=$("#claim_date1").val();
                let date2=$("#claim_date2").val();
                let state=$("#claim_state option:selected").val();
                if (claim_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'saveClaimActCard','claim_id':claim_id,'sto':sto,'amount':amount,'text_ru':text_ru,'auto':auto,'year':year,'number':number,'vin':vin,'kilo1':kilo1,'kilo2':kilo2,'date1':date1,'date2':date2,'state':state},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                loadClaimList();
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#ClaimCard").modal('hide');
                            }
                            else{ swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}