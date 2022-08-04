
function showModuleList() {
    JsHttpRequest.query($rcapi,{ 'w': 'showModuleList'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let module_range = $("#module_range");
            module_range.empty();
            module_range.html(result["content"]);
        }}, true);
}

function showModuleCard(module_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showModuleCard', 'module_id':module_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#ModuleCard").modal("show");
            $("#ModuleCardBody").html(result["content"]);
            $("#ModuleCardLabel").html($("#module_caption").val()+" (ID:"+$("#module_id").val()+")");
            $("#modules_tabs").tab();
        }}, true);
}

function saveModuleCard() {
    let module_id=$("#module_id").val();
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let module_caption=$("#module_caption").val();
                let module_link=$("#module_link").val();
                let module_icon=$("#module_icon").val();
                let module_file=$("#module_file").val();
                let module_lenta=$("#module_lenta").val();
                let module_ison=$("#module_ison").val();
                JsHttpRequest.query($rcapi,{'w':'saveModuleCard', 'module_id':module_id, 'module_caption':module_caption, 'module_link':module_link, 'module_icon':module_icon, 'module_file':module_file, 'module_lenta':module_lenta, 'module_ison':module_ison},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        if (result["answer"]==1){
                            swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                            $("#ModuleCard").modal("hide");
                            showModuleList();
                        } else { swal("Помилка!", result["error"], "error");}
                    }}, true);
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function dropModuleCard() {
    let module_id=$("#module_id").val();
    swal({
            title: "Видалити модуль?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (module_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'dropModuleCard','module_id':module_id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#ModuleCard").modal("hide");
                                showModuleList();
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

/*=======================PAGES==========================*/

function showModulePagesList() {
    JsHttpRequest.query($rcapi,{ 'w': 'showModulePagesList'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let module_pages_range = $("#module_pages_range");
            module_pages_range.empty();
            module_pages_range.html(result["content"]);
        }}, true);
}

function showModulePageCard(page_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showModulePageCard', 'page_id':page_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#ModuleCard").modal("show");
            $("#ModuleCardBody").html(result["content"]);
            $("#ModuleCardLabel").html($("#page_caption").val()+" (ID:"+$("#page_id").val()+")");
            $("#modules_tabs").tab();
        }}, true);
}

function saveModulePageCard() {
    let page_id=$("#page_id").val();
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let page_mid=$("#page_mid").val();
                let page_module=$("#page_module").val();
                let page_caption=$("#page_caption").val();
                let page_file=$("#page_file").val();
                let page_link=$("#page_link").val();
                JsHttpRequest.query($rcapi,{'w':'saveModulePageCard', 'page_id':page_id, 'page_mid':page_mid, 'page_module':page_module, 'page_caption':page_caption, 'page_file':page_file, 'page_link':page_link},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        if (result["answer"]==1){
                            swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                            $("#ModuleCard").modal("hide");
                            showModulePagesList();
                        } else { swal("Помилка!", result["error"], "error");}
                    }}, true);
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function dropModulePageCard() {
    let page_id=$("#page_id").val();
    swal({
            title: "Видалити модуль?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (page_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'dropModulePageCard','page_id':page_id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#ModuleCard").modal("hide");
                                showModulePagesList();
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

/*=======================FILES==========================*/

function showModuleFilesList() {
    JsHttpRequest.query($rcapi,{ 'w': 'showModuleFilesList'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let module_files_range = $("#module_files_range");
            module_files_range.empty();
            module_files_range.html(result["content"]);
        }}, true);
}

function showModuleFileCard(file_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showModuleFileCard', 'file_id':file_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#ModuleCard").modal("show");
            $("#ModuleCardBody").html(result["content"]);
            $("#ModuleCardLabel").html($("#file_caption").val()+" (ID:"+$("#file_id").val()+")");
            $("#modules_tabs").tab();
        }}, true);
}

function saveModuleFileCard() {
    let file_id=$("#file_id").val();
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let file_caption=$("#file_caption").val();
                let file_file=$("#file_file").val();
                let file_system=$("#file_system").val();
                JsHttpRequest.query($rcapi,{'w':'saveModuleFileCard', 'file_id':file_id, 'file_caption':file_caption, 'file_file':file_file, 'file_system':file_system},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        if (result["answer"]==1){
                            swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                            $("#ModuleCard").modal("hide");
                            showModuleFilesList();
                        } else { swal("Помилка!", result["error"], "error");}
                    }}, true);
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function dropModuleFileCard() {
    let file_id=$("#file_id").val();
    swal({
            title: "Видалити модуль?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (file_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'dropModuleFileCard','file_id':file_id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#ModuleCard").modal("hide");
                                showModuleFilesList();
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}