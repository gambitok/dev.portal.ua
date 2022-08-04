
function showModuleList() {
    JsHttpRequest.query($rcapi,{ 'w': 'showModuleList'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let module_range = $("#module_range");
            module_range.empty();
            module_range.html(result["content"]);
        }}, true);
}

function showModuleCard() {
    let id=$("#module_id").val();
    JsHttpRequest.query($rcapi,{ 'w': 'showModuleCard', 'id':id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#ModuleCard").modal("show");
            $("#ModuleCardBody").html(result["content"]);
            $("#ModuleCardLabel").html($("#module_name").val()+" (ID:"+$("#module_id").val()+")");
            $("#modules_tabs").tab();
        }}, true);
}

function saveModuleCard() {
    let id=$("#module_id").val();
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let name=$("#module_name").val();
                let status=$("#module_status").prop("checked");
                JsHttpRequest.query($rcapi,{'w':'saveModuleCard', 'id':id, 'name':name, 'status':status},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        if (result["answer"]==1){
                            swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                            $("#ModuleCard").modal("hide");
                        } else { swal("Помилка!", result["error"], "error");}
                    }}, true);
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function dropModuleCard() {
    let id=$("#module_id").val();
    swal({
            title: "Видалити модуль?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'dropModuleCard','id':id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#ModuleCard").modal("hide");
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}