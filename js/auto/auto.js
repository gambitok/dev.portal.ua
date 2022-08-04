
function showManufacturersCard(mfa_id) {
    if (mfa_id<=0 || mfa_id==""){toastr["error"](errs[0]);}
    if (mfa_id>0){
        JsHttpRequest.query($rcapi,{ 'w': 'showManufacturersCard', 'mfa_id':mfa_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#ManufacturersCard").modal("show");
                $("#ManufacturersCardBody").html(result["content"]);
                let dt = $("#models_str");
                dt.DataTable().destroy();
                dt.DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
                $("#ManufacturersCardLabel").html(result["mfa_brand"]);
                let elem = document.querySelector("#mfa_active"); if (elem) { new Switchery(elem, {color: '#1AB394'}); }
                $("#mfa_tabs").tab();
            }}, true);
    }
}

function saveManufacturersCard(mfa_id) {
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let mfa_brand = $("#mfa_brand").val();
                let mfa_logo = $("#mfa_logo").val();
                let mfa_position = $("#mfa_position").val();
                if (document.getElementById("mfa_active").checked){mfa_active=1;} else {mfa_active=0;}
                if (mfa_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'saveManufacturersCard', 'mfa_id':mfa_id, 'mfa_brand':mfa_brand, 'mfa_logo':mfa_logo, 'mfa_position':mfa_position, 'mfa_active':mfa_active},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                showManufacturersCard(mfa_id);
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function showModelsCard(mod_id) {
    if (mod_id<=0 || mod_id==""){toastr["error"](errs[0]);}
    if (mod_id>0){
        JsHttpRequest.query($rcapi,{ 'w': 'showModelsCard', 'mod_id':mod_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#ModelsCard").modal("show");
                $("#ModelsCardBody").html(result["content"]);
                let dt = $("#types_str");
                dt.DataTable().destroy();
                dt.DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
                $("#ModelsCardLabel").html(result["mod_tex_text"]);
                let elem = document.querySelector("#mod_active"); if (elem) { new Switchery(elem, {color: '#1AB394'}); }
                let elem2 = document.querySelector("#mod_img_status"); if (elem2) { new Switchery(elem2, {color: '#1AB394'}); }
            }}, true);
    }
}

function saveModelsCard(mod_id) {
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let mod_mfa_id = $("#mod_mfa_id").val();
                let mod_model = $("#mod_model").val();
                let mod_tex_text = $("#mod_tex_text").val();
                let mod_date_start = $("#mod_date_start").val();
                let mod_date_end = $("#mod_date_end").val();
                let mod_img = $("#mod_img").val();
                if (document.getElementById("mod_img_status").checked){mod_img_status=1;} else {mod_img_status=0;}
                if (document.getElementById("mod_active").checked){mod_active=1;} else {mod_active=0;}
                if (mod_id.length>0){
                        JsHttpRequest.query($rcapi,{'w':'saveModelsCard', 'mod_id':mod_id, 'mod_mfa_id':mod_mfa_id, 'mod_model':mod_model, 'mod_tex_text':mod_tex_text, 'mod_date_start':mod_date_start, 'mod_date_end':mod_date_end, 'mod_img':mod_img, 'mod_img_status':mod_img_status, 'mod_active':mod_active},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                showModelsCard(mod_id);
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function showTypesCard(typ_id) {
    if (typ_id<=0 || typ_id==""){toastr["error"](errs[0]);}
    if (typ_id>0){
        JsHttpRequest.query($rcapi,{ 'w': 'showTypesCard', 'typ_id':typ_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#TypesCard").modal("show");
                $("#TypesCardBody").html(result["content"]);
                $("#TypesCardLabel").html(result["typ_mmt"]);
                let elem = document.querySelector("#typ_active"); if (elem) { new Switchery(elem, {color: '#1AB394'}); }
            }}, true);
    }
}

function saveTypesCard(typ_id) {
    swal({
            title: "Зберегти зміни у розділі \"Загальна інформація\"?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                let typ_text = $("#typ_text").val();
                let typ_mmt = $("#typ_mmt").val();
                let typ_mod = $("#typ_mod").val();
                let typ_sort = $("#typ_sort").val();
                let typ_pcon_start = $("#typ_pcon_start").val();
                let typ_pcon_end = $("#typ_pcon_end").val();
                let typ_kw_from = $("#typ_kw_from").val();
                let typ_hp_from = $("#typ_hp_from").val();
                let typ_ccm = $("#typ_ccm").val();
                let fuel_id = $("#fuel_id").val();
                let body_id = $("#body_id").val();
                let eng_cod = $("#eng_cod").val();
                if (document.getElementById("typ_active").checked){typ_active=1;} else {typ_active=0;}
                if (typ_id.length>0){
                    JsHttpRequest.query($rcapi,{'w':'saveTypesCard', 'typ_id':typ_id, 'typ_text':typ_text, 'typ_mmt':typ_mmt, 'typ_mod':typ_mod, 'typ_sort':typ_sort, 'typ_pcon_start':typ_pcon_start, 'typ_pcon_end':typ_pcon_end, 'typ_kw_from':typ_kw_from, 'typ_hp_from':typ_hp_from, 'typ_ccm':typ_ccm, 'fuel_id':fuel_id, 'body_id':body_id, 'eng_cod':eng_cod, 'typ_active':typ_active},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                                showTypesCard(typ_id);
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function showModelsUploadForm(model_id) {
    $("#photo_model_id").val(model_id);
    let drop = new Dropzone("#dropModel", { dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
    drop.removeAllFiles(true);
    drop.on("queuecomplete", function() {
        toastr["info"]("Завантаження файлів завершено.");
        this.removeAllFiles();
        $("#fileModelPhotoUploadForm").modal("hide");
        showModelsCard(model_id);
    });
}