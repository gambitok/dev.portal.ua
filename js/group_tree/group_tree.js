
function showGroupTreeCard(str_id) {
    if (str_id<=0 || str_id==""){toastr["error"](errs[0]);}
    if (str_id>0){
        JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeCard', 'str_id':str_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#GroupTreeCard").modal('show');
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
                var position=$("#position_list option:selected").val();
                var disp_text_ru=$("#disp_text_ru").val();
                var disp_text_ua=$("#disp_text_ua").val();
                var disp_text_en=$("#disp_text_en").val();

                if (str_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'saveGroupTreeCard', 'str_id':str_id, 'position':position, 'disp_text_ru':disp_text_ru, 'disp_text_ua':disp_text_ua, 'disp_text_en':disp_text_en},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#GroupTreeCard").modal('hide');
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
            $("#GroupTreeCard").modal('show');
            $("#GroupTreeCardBody").html(result.content);
            $("#GroupTreeCardLabel").html("Карта розділів дерева");
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
                var disp_text_ru=$("#disp_text_ru").val();
                var disp_text_ua=$("#disp_text_ua").val();
                var disp_text_en=$("#disp_text_en").val();

                if (head_id.length>0){
                    JsHttpRequest.query($rcapi,{ 'w': 'saveGroupTreeHead', 'head_id':head_id, 'disp_text_ru':disp_text_ru, 'disp_text_ua':disp_text_ua, 'disp_text_en':disp_text_en},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#GroupTreeCard").modal('hide');
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
                                $("#GroupTreeCard").modal('hide');
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
            $("#GroupTreeCard").modal('show');
            $("#GroupTreeCardBody").html(result.content);
            $("#GroupTreeCardLabel").html("Карта пунктів дерева");
        }}, true);
}

function showGroupTreeHeadStr(group_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeHeadStr', 'group_id':group_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#GroupTreeCard").modal('show');
            $("#GroupTreeCardBody").html(result.content);
            $("#GroupTreeCardLabel").html("Карта пунктів дерева");
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
                var head_id=$("#head_id").val();
                var str_id=$("#str_list option:selected").val();
                var position=$("#position_list option:selected").val();
                var category=$("#category_list option:selected").val();
                var disp_text_ru=$("#disp_text_ru").val();
                var disp_text_ua=$("#disp_text_ua").val();
                var disp_text_en=$("#disp_text_en").val();

                if (group_id.length>0){
                    JsHttpRequest.query($rcapi,{ 'w': 'saveGroupTreeHeadStrCard', 'group_id':group_id, 'head_id':head_id, 'str_id':str_id, 'position':position, 'category':category, 'disp_text_ru':disp_text_ru, 'disp_text_ua':disp_text_ua, 'disp_text_en':disp_text_en},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#GroupTreeCard").modal('hide');
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
                    JsHttpRequest.query($rcapi,{'w':'dropGroupTreeHeadStr','group_id':group_id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#GroupTreeCard").modal('hide');
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
            $("#GroupTreeCard").modal('show');
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
                var head_id=$("#head_list option:selected").val();
                var position=$("#position_list option:selected").val();
                var disp_text_ru=$("#disp_text_ru").val();
                var disp_text_ua=$("#disp_text_ua").val();
                var disp_text_en=$("#disp_text_en").val();

                if (cat_id.length>0){
                    JsHttpRequest.query($rcapi,{ 'w': 'saveGroupTreeHeadCategoryCard', 'cat_id':cat_id, 'head_id':head_id, 'position':position, 'disp_text_ru':disp_text_ru, 'disp_text_ua':disp_text_ua, 'disp_text_en':disp_text_en},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#GroupTreeCard").modal('hide');
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
                    JsHttpRequest.query($rcapi,{'w':'dropGroupTreeHeadCategory','cat_id':cat_id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#GroupTreeCard").modal('hide');
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