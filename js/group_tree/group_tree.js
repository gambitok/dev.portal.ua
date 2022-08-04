
function showGroupTreeCard(str_id) {
    if (str_id<=0 || str_id==""){toastr["error"](errs[0]);}
    if (str_id>0){
        JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeCard', 'str_id':str_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#GroupTreeCard").modal("show");
                $("#GroupTreeCardBody").html(result.content);
                $("#GroupTreeCardLabel").html("Карта дерева товарів");
            }}, true);
    }
}

function saveGroupTreeCard(str_id) {
    swal({
            title: "Зберегти зміни у розділі \"Дерево товарів\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let position=$("#position_list option:selected").val();
                let disp_text_ru=$("#disp_text_ru").val();
                let disp_text_ua=$("#disp_text_ua").val();
                let disp_text_en=$("#disp_text_en").val();
                if (str_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'saveGroupTreeCard', 'str_id':str_id, 'position':position, 'disp_text_ru':disp_text_ru, 'disp_text_ua':disp_text_ua, 'disp_text_en':disp_text_en},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#GroupTreeCard").modal("hide");
                            }
                            else{ swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function showGroupTreeHeaders() {
    JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeHeaders'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#group-tree").html(result.content);
            $(".tree-head").on('click', function(event){
                $(this).next().toggleClass("dnone");
                $(this).toggleClass("check-head");
            });
        }}, true);
}

function UpdateGroupTreeCard(str_id) {
    if (str_id<=0 || str_id==""){toastr["error"](errs[0]);}
    if (str_id>0){
        JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeCard', 'str_id':str_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#GroupTreeCardBody").html(result.content);
                $("#GroupTreeCardLabel").html("Карта дерева товарів");
            }}, true);
    }
}

function showGroupTreeHeadCard(head_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeHead', 'head_id':head_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#GroupTreeCard").modal("show");
            $("#GroupTreeCardBody").html(result.content);
            $("#GroupTreeCardLabel").html("Карта розділів дерева");
        }}, true);
}

function updateGroupTreeHeadCard(head_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeHead', 'head_id':head_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#GroupTreeCardBody").html(result.content);
        }}, true);
}

function saveGroupTreeHeadCard(head_id) {
    swal({
            title: "Зберегти зміни у розділі \"Дерево товарів\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let disp_text_ru=$("#disp_text_ru").val();
                let disp_text_ua=$("#disp_text_ua").val();
                let disp_text_en=$("#disp_text_en").val();
                let head_status= $("#head_status").prop('checked');
                if (head_id.length>0){
                    JsHttpRequest.query($rcapi,{ 'w': 'saveGroupTreeHead', 'head_id':head_id, 'disp_text_ru':disp_text_ru, 'disp_text_ua':disp_text_ua, 'disp_text_en':disp_text_en, 'head_status':head_status},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                updateGroupTreeHeadCard(head_id);
                            }
                            else{ swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function dropGroupTreeHead(head_id) {
    swal({
            title: "Видалити групу?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (head_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'dropGroupTreeHead','head_id':head_id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#GroupTreeCard").modal("hide");
                                showGroupTreeHeaders();
                            }
                            else{ swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function addGroupTreeHeadStr(head_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'addGroupTreeHeadStr', 'head_id':head_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#GroupTreeCard").modal("show");
            $("#GroupTreeCardBody").html(result.content);
            $("#GroupTreeCardLabel").html("Карта пунктів дерева");
        }}, true);
}

function showGroupTreeHeadStr(group_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeHeadStr', 'group_id':group_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#GroupTreeCard").modal("show");
            $("#GroupTreeCardBody").html(result.content);
            $("#GroupTreeCardLabel").html("Карта пунктів дерева");
            $("#str_list").select2({});
        }}, true);
}

function updateGroupTreeHeadStr(group_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeHeadStr', 'group_id':group_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#GroupTreeCardBody").html(result.content);
        }}, true);
}

function saveGroupTreeHeadStrCard(group_id) {
    swal({
            title: "Зберегти зміни у розділі \"Дерево товарів\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let head_id=$("#head_id").val();
                let str_id=$("#str_list option:selected").val();
                let position=$("#position_list option:selected").val();
                let category=$("#category_list option:selected").val();
                let disp_text_ru=$("#disp_text_ru").val();
                let disp_text_ua=$("#disp_text_ua").val();
                let disp_text_en=$("#disp_text_en").val();
                let disp_text_link=$("#disp_text_link").val();
                if (group_id.length>0){
                    JsHttpRequest.query($rcapi,{ 'w': 'saveGroupTreeHeadStrCard', 'group_id':group_id, 'head_id':head_id, 'str_id':str_id, 'position':position, 'category':category, 'disp_text_ru':disp_text_ru, 'disp_text_ua':disp_text_ua, 'disp_text_en':disp_text_en, 'disp_text_link':disp_text_link},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#GroupTreeCard").modal("hide");
                                showGroupTreeHeaders();
                            }
                            else{ swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function dropGroupTreeHeadStr(group_id) {
    swal({
            title: "Видалити групу?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (group_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'dropGroupTreeHeadStr', 'group_id':group_id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#GroupTreeCard").modal("hide");
                                showGroupTreeHeaders();
                            }
                            else{ swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function changeTreeHeader() {
    let head_id=$("#head_list option:selected").val();
    $("#head_id").val(head_id);
}

function showGroupTreeHeadCategory(cat_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeHeadCategory', 'cat_id':cat_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#GroupTreeCard").modal("show");
            $("#GroupTreeCardBody").html(result.content);
            $("#GroupTreeCardLabel").html("Карта категорій дерева");
        }}, true);
}

function saveGroupTreeHeadCategoryCard(cat_id) {
    swal({
            title: "Зберегти зміни у розділі \"Дерево товарів\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let head_id=$("#head_list option:selected").val();
                let position=$("#position_list option:selected").val();
                let disp_text_ru=$("#disp_text_ru").val();
                let disp_text_ua=$("#disp_text_ua").val();
                let disp_text_en=$("#disp_text_en").val();
                if (cat_id.length>0){
                    JsHttpRequest.query($rcapi,{ 'w': 'saveGroupTreeHeadCategoryCard', 'cat_id':cat_id, 'head_id':head_id, 'position':position, 'disp_text_ru':disp_text_ru, 'disp_text_ua':disp_text_ua, 'disp_text_en':disp_text_en},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#GroupTreeCard").modal("hide");
                                showGroupTreeHeaders();
                            }
                            else{ swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function dropGroupTreeHeadCategory(cat_id) {
    swal({
            title: "Видалити категорію?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (cat_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'dropGroupTreeHeadCategory', 'cat_id':cat_id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#GroupTreeCard").modal("hide");
                                showGroupTreeHeaders();
                            }
                            else{ swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function showUploadPhotoForm(type_id,group_id) {
    $("#GroupTreeUploadForm").modal("show");
    $("#GroupTreeUploadBody").html("");
    JsHttpRequest.query($rcapi,{'w':'showUploadDropzone','type_id':type_id,'group_id':group_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#GroupTreeUploadBody").html(result.content);
            // $("#dropzone-form").dropzone();
            var dropzone = new Dropzone('#dropzone-form', {
                parallelUploads: 2,
                thumbnailHeight: 120,
                thumbnailWidth: 120,
                maxFilesize: 1,
                maxFiles: 1,
                filesizeBase: 1000
            });
        }}, true);
}

function dropUploadPhotoForm(type_id, group_id) {
    swal({
            title: "Видалити зображення?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (group_id.length > 0) {
                    JsHttpRequest.query($rcapi,{'w':'dropUploadPhotoForm', 'type_id':type_id, 'group_id':group_id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"] == 1) {
                                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                                if (type_id == "group") {
                                    updateGroupTreeHeadStr(group_id);
                                }
                                if (type_id == "head") {
                                    updateGroupTreeHeadCard(group_id);
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

/*
* =================================================================================
* */

function loadTreeCons() {
    JsHttpRequest.query($rcapi,{ 'w': 'loadTreeCons'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#form_range").html(result.content);
            loadTreeConsHeader();
            loadTreeConsView();
        }}, true);
}

function loadTreeConsHeader() {
    JsHttpRequest.query($rcapi,{ 'w': 'loadTreeConsHeader'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#form_header").html(result.content);
        }}, true);
}

function loadTreeConsView() {
    JsHttpRequest.query($rcapi,{ 'w': 'loadTreeConsView'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#form_view").html(result.content);
        }}, true);
}

function addTreeConsColumn() {
    let head_id = $("#select_head option:selected").val();
    if (head_id !== "0") {
        JsHttpRequest.query($rcapi,{ 'w': 'addTreeConsColumn', 'head_id':head_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                if (result["answer"] == 1) {
                    loadTreeCons();
                } else {
                    swal("Помилка!", result["error"], "error");
                }
            }}, true);
    } else {
        swal("Помилка!", "Не вибрані дані!", "error");
    }
}

function dropTreeConsColumn(head_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'dropTreeConsColumn', 'head_id':head_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"] == 1) {
                loadTreeCons();
            } else {
                swal("Помилка!", result["error"], "error");
            }
        }}, true);
}

function moveTreeConsColumn(head_id, status) {
    JsHttpRequest.query($rcapi,{ 'w': 'moveTreeConsColumn', 'head_id':head_id, 'status':status},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            loadTreeCons();
        }}, true);
}

function addTreeConsCatForm(head_id) {
    $("#FormModalWindow").modal("show");
    JsHttpRequest.query($rcapi,{ 'w': 'addTreeConsCatForm', 'head_id':head_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalBody").html(result.content);
        }}, true);
}

function addTreeConsCat(head_id) {
    let cat_id = $("#select_cat option:selected").val();
    let cat_col = $("#cat_col").val();
    let cat_row = $("#cat_row").val();
    JsHttpRequest.query($rcapi,{'w':'addTreeConsCat', 'head_id':head_id, 'cat_id':cat_id, 'cat_col':cat_col, 'cat_row':cat_row},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"] == 1) {
                addTreeConsCatForm(head_id);
                loadTreeCons();
            } else {
                swal("Помилка!", result["error"], "error");
            }
        }}, true);
}

function dropTreeConsCat(head_id, cat_id) {
    JsHttpRequest.query($rcapi,{'w':'dropTreeConsCat', 'head_id':head_id, 'cat_id':cat_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"] == 1) {
                loadTreeCons();
            } else {
                swal("Помилка!", result["error"], "error");
            }
        }}, true);
}

function saveTreeConsCatPos(head_id, cat_id) {
    let cat_col = $("#cat_col_" + cat_id).val();
    let cat_row = $("#cat_row_" + cat_id).val();
    JsHttpRequest.query($rcapi,{'w':'saveTreeConsCatPos', 'head_id':head_id, 'cat_id':cat_id, 'cat_col':cat_col, 'cat_row':cat_row},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"] == 1) {
                loadTreeCons();
            } else {
                swal("Помилка!", result["error"], "error");
            }
        }}, true);
}

function loadTreeConsViewList(head_id) {
    $("#nav-hide").html("");
    JsHttpRequest.query($rcapi,{ 'w': 'loadTreeConsViewList', 'head_id':head_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#nav-hide").html(result.content);
        }}, true);
}

function addHeadPopular(head_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'addHeadPopular', 'head_id':head_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            loadTreeCons();
        }}, true);
}

function moveTreeConsCat(head_id, cat_id, status) {
    JsHttpRequest.query($rcapi,{ 'w': 'moveTreeConsCat', 'head_id':head_id, 'cat_id':cat_id, 'status':status},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            loadTreeCons();
            console.log(result.content);
        }}, true);
}

function loadCatalogExist(status) {
    $("#form_cron_content").html("");
    JsHttpRequest.query($rcapi,{ 'w': 'loadCatalogExist', 'status':status},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#form_cron_content").html(result.content);
        }}, true);
}

function saveGroupStatus(type, group_id) {
    let field = $("#status_group_" + group_id);
    if (type == 1) {
        field = $("#status_auto_group_" + group_id);
    }
    let status = field.val();
    JsHttpRequest.query($rcapi,{ 'w': 'saveGroupStatus', 'type':type, 'group_id':group_id, 'status':status},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            toastr["success"]("Збережено!");
            console.log('done: ' + type + " - " + group_id + " - " + result.content);
            field.val(result.content);
        }}, true);
}

function showGroupExistCard(group_id) {
    $("#FormModalWindow").modal("show");
    JsHttpRequest.query($rcapi,{ 'w': 'showGroupExistCard', 'group_id':group_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalBody").html(result.content);
            $("#FormModalLabel").text("Картка групи");
            $("#reviews").chosen();
        }}, true);
}

function saveGroupExistCard() {
    let group_id = $("#group_id").val();
    let text_ru = $("#text_ru").val();
    let text_ua = $("#text_ua").val();
    let text_en = $("#text_en").val();
    let one_ru = $("#one_ru").val();
    let one_ua = $("#one_ua").val();
    let one_en = $("#one_en").val();
    let h1_ru = $("#h1_ru").val();
    let h1_ua = $("#h1_ua").val();
    let h1_en = $("#h1_en").val();
    let descr_ru = $("#descr_ru").val();
    let descr_ua = $("#descr_ua").val();
    let descr_en = $("#descr_en").val();
    let text_link = $("#text_link").val();
    let status = $("#group_status").val();
    let status_auto = $("#group_status_auto").val();
    let reviews = $("#reviews").chosen().val();

    if (group_id.length > 0) {
        JsHttpRequest.query($rcapi,{'w':'saveGroupExistCard', 'group_id':group_id, 'text_ru':text_ru, 'text_ua':text_ua, 'text_en':text_en, 'one_ru':one_ru, 'one_ua':one_ua, 'one_en':one_en, 'h1_ru':h1_ru, 'h1_ua':h1_ua, 'h1_en':h1_en, 'descr_ru':descr_ru, 'descr_ua':descr_ua, 'descr_en':descr_en, 'text_link':text_link, 'status':status, 'status_auto':status_auto, 'reviews':reviews},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                if (result["answer"] == 1) {
                    swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                    $("#FormModalWindow").modal("hide");
                } else {
                    swal("Помилка!", result["error"], "error");
                }
            }}, true);
    } else {
        swal("Помилка!", result["error"], "error");
    }
}

var review_ids = [];

$(document).ready(function() {
    $("select#reviews option").each(function() {
        review_ids.push($(this).val());
    });
});
