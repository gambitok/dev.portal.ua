
function showBuhConvertCard(buh_convert_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showBuhConvertCard', 'buh_convert_id':buh_convert_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#BuhIncomeCard").modal('show');
            $("#BuhIncomeCardBody").html(result.content);
        }}, true);
}

function saveBuhConvertCard() {
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let buh_convert_id=$("#buh_convert_id").val();
                let buh_convert_text=$("#buh_convert_text").val();
                let buh_convert_pay_id=$("#buh_convert_pay_id option:selected").val();
                let buh_convert_cash_id_pay=$("#buh_convert_cash_id_pay option:selected").val();
                let buh_convert_cash_id_to=$("#buh_convert_cash_id_to option:selected").val();
                let buh_convert_kours_usd=$("#buh_convert_kours_usd").val();
                let buh_convert_kours_eur=$("#buh_convert_kours_eur").val();
                let buh_convert_summ=$("#buh_convert_summ").val();
                let buh_convert_user_id=$("#buh_convert_user_id").val();
                let buh_convert_data=$("#buh_convert_data").val();
                if (document.getElementById("buh_convert_status").checked){buh_convert_status=1;} else {buh_convert_status=0;}
                if (buh_convert_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'saveBuhConvertCard', 'buh_convert_id':buh_convert_id, 'buh_convert_text':buh_convert_text, 'buh_convert_pay_id':buh_convert_pay_id, 'buh_convert_cash_id_pay':buh_convert_cash_id_pay, 'buh_convert_cash_id_to':buh_convert_cash_id_to, 'buh_convert_kours_usd':buh_convert_kours_usd, 'buh_convert_kours_eur':buh_convert_kours_eur, 'buh_convert_summ':buh_convert_summ, 'buh_convert_user_id':buh_convert_user_id, 'buh_convert_data':buh_convert_data, 'buh_convert_status':buh_convert_status},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#BuhIncomeCard").modal("hide");
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function showBuhIncomeCard(buh_income_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showBuhIncomeCard', 'buh_income_id':buh_income_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#BuhIncomeCard").modal('show');
            $("#BuhIncomeCardBody").html(result.content);
        }}, true);
}

function saveBuhIncomeCard() {
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let buh_income_id=$("#buh_income_id").val();
                let buh_income_pay_id=$("#buh_income_pay_id").val();
                let buh_income_cash_id=$("#buh_income_cash_id option:selected").val();
                let buh_income_state_id=$("#buh_income_state_id option:selected").val();
                let buh_income_text=$("#buh_income_text").val();
                let buh_income_summ=$("#buh_income_summ").val();
                let buh_income_user_id=$("#buh_income_user_id").val();
                let buh_income_data=$("#buh_income_data").val();
                if (document.getElementById("buh_income_status").checked){buh_income_status=1;} else {buh_income_status=0;}
                if (buh_income_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'saveBuhIncomeCard', 'buh_income_id':buh_income_id, 'buh_income_text':buh_income_text, 'buh_income_pay_id':buh_income_pay_id, 'buh_income_cash_id':buh_income_cash_id, 'buh_income_state_id':buh_income_state_id, 'buh_income_summ':buh_income_summ, 'buh_income_user_id':buh_income_user_id, 'buh_income_data':buh_income_data, 'buh_income_status':buh_income_status},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#BuhIncomeCard").modal("hide");
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function getPayCashList() {
    let buh_convert_cash_id_pay=$("#buh_convert_cash_id_pay option:selected").val();
    let buh_convert_user_id=$("#buh_convert_user_id").val();
    JsHttpRequest.query($rcapi,{ 'w': 'getPayCashList', 'buh_convert_cash_id_pay':buh_convert_cash_id_pay, 'buh_convert_user_id':buh_convert_user_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#buh_convert_cash_id_pay").html(result.content);
            console.log(result.content);
        }}, true);
}

// function dropBuhIncomeCard() {
//     let buh_income_id=$("#buh_income_id").val();
//     swal({
//             title: "Видалити Надходження"+buh_income_id+" ?",
//             text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
//             confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
//         },
//         function (isConfirm) {
//             if (isConfirm) {
//                 if (buh_income_id.length>0){
//                     JsHttpRequest.query($rcapi,{ 'w':'dropBuhIncomeCard', 'buh_income_id':buh_income_id},
//                         function (result, errors){ if (errors) {alert(errors);} if (result){
//                             if (result["answer"]==1){
//                                 swal("Видалено!", "", "success");
//                                 $("#BuhIncomeCard").modal("hide");
//                             } else { swal("Помилка!", result["error"], "error");}
//                         }}, true);
//                 }
//             } else {
//                 swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
//             }
//         });
// }