
function showBrandCard(brand_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showBrandCard', 'brand_id':brand_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let brand_card = $("#SmartCard");
            brand_card.modal("show");
            $("#SmartCardBody").html(result["content"]);
            $("#SmartCardLabel").html(brand_id);
            $("#brands_tab").tab();
            $("#datatable_card").DataTable();
            getSmartBrandImage(brand_id);
        }}, true);
}

function saveBrandCard() {
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let brand_id=$("#brand_id").val();
                let brand_name=$("#brand_name").val();
                let brand_text=$("#brand_text").val();
                let brand_pos=$("#brand_pos").val();
                let brand_status=$("#brand_status").prop("checked");
                if (brand_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'saveBrandCard','brand_id':brand_id, 'brand_name':brand_name, 'brand_text':brand_text, 'brand_pos':brand_pos, 'brand_status':brand_status},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                showBrandCard(result["brand_id"]);
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function getSmartBrandImage(brand_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'getSmartBrandImage', 'brand_id':brand_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#smart-image").html(result["content"]);
        }}, true);
}

function showSmartPhotoUploadForm(brand_id){
    $("#photo_smart_brand_id").val(brand_id);
    var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
    myDropzone3.removeAllFiles(true);
    myDropzone3.on("queuecomplete", function() {
        toastr["info"]("Завантаження файлів завершено.");
        this.removeAllFiles();
        $("#fileSmartPhotoUploadForm").modal("hide");
        getSmartBrandImage(brand_id);
    });
}

function deleteSmartPhoto(brand_id){
    swal({
            title: "Видалити зображення?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                JsHttpRequest.query($rcapi,{'w':'deleteSmartPhoto', 'brand_id':brand_id},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        if (result["answer"]==1){
                            swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                            getSmartBrandImage(brand_id);
                        } else { swal("Помилка!", result["error"], "error");}
                    }}, true);
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

/*================================================================*/

function showStoreCard(store_id) {
    let brand_id=$("#brand_id").val();
    JsHttpRequest.query($rcapi,{ 'w': 'showStoreCard', 'store_id':store_id, 'brand_id':brand_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let store_card = $("#SmartCard");
            store_card.modal("show");
            $("#SmartCardBody").html(result["content"]);
            $("#SmartCardLabel").html(store_id);
        }}, true);
}

function saveStoreCard() {
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let store_id=$("#store_id").val();
                let brand_id=$("#brand_id").val();
                let address=$("#address").val();
                let store_pos=$("#store_pos").val();
                let store_status=$("#store_status").prop("checked");
                if (brand_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'saveStoreCard', 'store_id':store_id, 'brand_id':brand_id, 'address':address, 'store_pos':store_pos, 'store_status':store_status},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#SmartCard").modal("hide");
                                showBrandCard(brand_id);
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function showNavCard(nav_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showNavCard', 'nav_id':nav_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#SmartCard").modal("show");
            $("#SmartCardBody").html(result["content"]);
            $("#SmartCardLabel").html(nav_id);
            $("#brands_tab").tab();
        }}, true);
}

function saveNavCard() {
    let nav_id=$("#nav_id").val();
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let nav_text=$("#nav_text").val();
                let nav_text_ru=$("#nav_text_ru").val();
                let nav_link=$("#nav_link").val();
                let nav_pos=$("#nav_pos").val();
                let nav_status=$("#nav_status").prop("checked");
                if (nav_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'saveNavCard', 'nav_id':nav_id, 'nav_text':nav_text, 'nav_text_ru':nav_text_ru, 'nav_link':nav_link, 'nav_pos':nav_pos, 'nav_status':nav_status},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#SmartCard").modal("hide");
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

/*================================================================*/

function showFaqCard(faq_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showFaqCard', 'faq_id':faq_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#SmartCard").modal("show");
            $("#SmartCardBody").html(result["content"]);
            $("#SmartCardLabel").html(faq_id);
            $("#brands_tab").tab();
        }}, true);
}

function saveFaqCard() {
    let faq_id=$("#faq_id").val();
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let faq_question = $("#faq_question").val();
                let faq_answer = CKEDITOR.instances.editor.getData();
                let faq_question_ru = $("#faq_question_ru").val();
                let faq_answer_ru = CKEDITOR.instances.editor2.getData();
                let faq_pos = $("#faq_pos").val();
                let faq_status = $("#faq_status").prop("checked");
                if (faq_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'saveFaqCard', 'faq_id':faq_id, 'faq_question':faq_question, 'faq_answer':faq_answer, 'faq_question_ru':faq_question_ru, 'faq_answer_ru':faq_answer_ru, 'faq_pos':faq_pos, 'faq_status':faq_status},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#SmartCard").modal("hide");
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

/*================================================================*/

function showSmartNewsCard(news_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showSmartNewsCard', 'news_id':news_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#SmartCard").modal("show");
            $("#SmartCardBody").html(result["content"]);
            $("#SmartCardLabel").html(news_id);
            getSmartNewsImage(news_id);
            $("#news_tab").tab();
        }}, true);
}

function saveSmartNewsCard() {
    let news_id=$("#news_id").val();
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let news_title = $("#news_title").val();
                let news_title_ru = $("#news_title_ru").val();
                let news_text = CKEDITOR.instances.editor.getData();
                let news_text_ru = CKEDITOR.instances.editor2.getData();
                let news_pos = $("#news_pos").val();
                let news_status = $("#news_status").prop("checked");
                if (news_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'saveSmartNewsCard', 'news_id':news_id, 'news_title':news_title, 'news_text':news_text, 'news_title_ru':news_title_ru, 'news_text_ru':news_text_ru, 'news_pos':news_pos, 'news_status':news_status},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                $("#SmartCard").modal("hide");
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function getSmartNewsImage(news_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'getSmartNewsImage', 'news_id':news_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#news-image").html(result["content"]);
        }}, true);
}

function showSmartNewsPhotoUploadForm(news_id){
    $("#photo_smart_news_id").val(news_id);
    var myDropzone4 = new Dropzone("#myDropzone4",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
    myDropzone4.removeAllFiles(true);
    myDropzone4.on("queuecomplete", function() {
        toastr["info"]("Завантаження файлів завершено.");
        this.removeAllFiles();
        $("#fileSmartNewsPhotoUploadForm").modal("hide");
        getSmartNewsImage(news_id);
    });
}

function deleteSmartNewsPhoto(news_id){
    swal({
            title: "Видалити зображення?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                JsHttpRequest.query($rcapi,{'w':'deleteSmartNewsPhoto', 'news_id':news_id},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        if (result["answer"]==1){
                            swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
                            getSmartNewsImage(news_id);
                        } else { swal("Помилка!", result["error"], "error");}
                    }}, true);
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}