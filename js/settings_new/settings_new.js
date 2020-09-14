//=Language====================================================================

function loadLanguageList() {
	JsHttpRequest.query($rcapi,{ 'w': 'loadLanguageList' }, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		let dt=$("#datatable");
		dt.DataTable().destroy();
		$("#lang_range").html(result.content);
		dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
}

function newLanguageCard(){
	let lang_var=$("#lang_var").val();
	if (lang_var==="" || lang_var===undefined) {
		toastr["error"]("Введіть значення змінної!");
		$("#lang_var").select();
	} else {
		JsHttpRequest.query($rcapi,{ 'w': 'newLanguageCard', 'lang_var':lang_var}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			showLanguageCard(result["id"]);
		}}, true);
	}
}

function showLanguageCard(id){
	if (id<=0 || id===""){toastr["error"](errs[0]);}
	if (id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showLanguageCard', 'id':id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#LanguageCard").modal("show");
			$("#LanguageCardBody").html(result["content"]);
			$("#LanguageCardLabel").html($("#lang_id").val());
			$("#language_tabs").tab();
		}}, true);
	}
}

function saveLanguage() {
    let lang_ru = $("#lang_ru").val();
    let lang_ua = $("#lang_ua").val();
    let lang_eng = $("#lang_eng").val();
    let lang_id = $("#lang_id").val();
	swal({
		title: "Зберегти зміни?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (lang_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveLanguage', 'lang_id':lang_id, 'lang_ru':lang_ru, 'lang_ua':lang_ua, 'lang_eng':lang_eng},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#LanguageCard").modal("hide");
						loadLanguageList();
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}

function dropLanguage(id) { 
	swal({
		title: "Видалити мовну змінну?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (id.length>0){ 
				JsHttpRequest.query($rcapi,{ 'w':'dropLanguage', 'id':id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						$("#LanguageCard").modal("hide");
						loadLanguageList();
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

//=Contacts====================================================================

function loadContactsList() {
	JsHttpRequest.query($rcapi,{ 'w': 'loadContactsList' }, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		let dt=$("#datatable");
		dt.DataTable().destroy();
		$("#contacts_range").html(result.content);
		dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
}

function newContactsCard(){
	let lang =$("#lang_select option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'newContactsCard', 'lang':lang},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		showContactsCard(result["id"]);
	}}, true);
}

function showContactsCard(id){
	if (id<=0 || id===""){toastr["error"](errs[0]);}
	if (id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showContactsCard', 'id':id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#ContactsCard").modal("show");
			$("#ContactsCardBody").html(result["content"]);
			$("#ContactsCardLabel").html("«" + $("#contact_title").val() + "» (" + $("#contact_id").val() + ")");
			$("#contacts_tabs").tab();
		}}, true);
	}
}

function saveContacts() {
    let id = $("#contact_id").val();
    let title = $("#contact_title").val();
    let address = $("#contact_address").val();
    let schedule = $("#contact_schedule").val();
    let phone = $("#contact_phone").val();
	swal({
		title: "Зберегти зміни?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveContacts', 'id':id, 'title':title, 'address':address, 'schedule':schedule, 'phone':phone},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#ContactsCard").modal("hide");
						loadContactsList();
					} else {swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}

function dropContacts(id) { 
	swal({
		title: "Видалити контакт?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (id.length>0){ 
				JsHttpRequest.query($rcapi,{ 'w':'dropContacts', 'id':id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						$("#ContactsCard").modal("hide");
						loadContactsList();
					} else {swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

//=Contacts====================================================================

function loadContactsBotList() {
	JsHttpRequest.query($rcapi,{ 'w': 'loadContactsBotList' }, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
        let dt=$("#datatable");
        dt.DataTable().destroy();
        $("#contacts_range").html(result.content);
        dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
    }}, true);
}

function newContactsBotCard(){
	JsHttpRequest.query($rcapi,{ 'w': 'newContactsBotCard' },
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		showContactsBotCard(result["id"]);
	}}, true);
}

function showContactsBotCard(id){
	if (id<=0 || id===""){toastr["error"](errs[0]);}
	if (id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showContactsBotCard', 'id':id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#ContactsCard").modal("show");
			$("#ContactsCardBody").html(result["content"]);
			$("#ContactsCardLabel").html($("#contact_id").val());
			$("#contacts_tabs").tab();
			var elem = document.querySelector('#contact_status');if (elem){ var dflt = new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function saveContactsBot() {
    let id = $("#contact_id").val();
    let text = $("#contact_text").val();
    let icon =$("#icon_select option:selected").val();
    let link = $("#contact_link").val();
	swal({
		title: "Зберегти зміни?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (id.length>0){
                let status=0;if (document.getElementById("contact_status").checked){status=1;}
				JsHttpRequest.query($rcapi,{'w':'saveContactsBot', 'id':id, 'text':text, 'icon':icon, 'link':link, 'status':status},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#ContactsCard").modal("hide");
						loadContactsBotList();
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}

function dropContactsBot(id) { 
	swal({
		title: "Видалити контакт?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (id.length>0){ 
				JsHttpRequest.query($rcapi,{ 'w':'dropContactsBot', 'id':id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						$("#ContactsCard").modal("hide");
						loadContactsBotList();
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

//=news====================================================================

function loadNewsPhoto(news_id,lang_id){
	JsHttpRequest.query($rcapi,{ 'w': 'loadNewsPhoto', 'news_id':news_id, 'lang_id':lang_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){
        let dt=$("#datatable");
        dt.DataTable().destroy();
        $("#news_photo_place").html(result.content);
        dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
    }}, true);
}

function showNewsUploadLogoForm(news_id,lang_id,file_id){
	$("#photo_news_id").val(news_id);
	$("#photo_lang_id").val(lang_id);
	$("#photo_file_id").val(file_id);
	var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$("#fileNewsPhotoUploadForm").modal("hide");
		loadNewsPhoto(news_id,lang_id);
	});
}

function deleteNewsLogo(news_id){
	swal({
		title: "Видалити логотип?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (news_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'deleteNewsLogo', 'news_id':news_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
						loadNewsPhoto(news_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}

function loadNewsList() {
	JsHttpRequest.query($rcapi,{ 'w': 'loadNewsList' }, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
        let dt=$("#datatable");
        dt.DataTable().destroy();
        $("#news_range").html(result.content);
        dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
    }}, true);
}

function newNewsCard(){
	let lang =$("#lang_select option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'newNewsCard', 'lang':lang}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		showNewsCard(result["id"]);
	}}, true);
}

function showNewsCard(id){
	if (id<=0 || id===""){toastr["error"](errs[0]);}
	if (id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showNewsCard', 'id':id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#NewsCard").modal("show");
			document.getElementById("NewsCardBody").innerHTML=result["content"];
			document.getElementById("NewsCardLabel").innerHTML="«" + $("#news_caption").val() + "» (" + $("#news_id").val() + ")";
			$("#news_tabs").tab();
			var elem = document.querySelector('#news_status');if (elem){ var dflt = new Switchery(elem, { color: '#1AB394' });}	
			initSample();
			loadNewsPhoto(id,$("#lang_id").val());
		}}, true);
	}
}

function saveNews() {
    let id = $("#news_id").val();
    let caption = $("#news_caption").val();
    let data = $("#news_data").val();
    let short = $("#news_short").val();
    let descr = CKEDITOR.instances.editor.getData();
	swal({
		title: "Зберегти зміни?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (id.length>0){
				var status=0;if (document.getElementById("news_status").checked){status=1;}
				JsHttpRequest.query($rcapi,{'w':'saveNews', 'id':id, 'caption':caption, 'data':data, 'short':short, 'descr':descr, 'status':status},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#NewsCard").modal("hide");
						loadNewsList();
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}

function dropNews(id) { 
	swal({
		title: "Видалити новину?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (id.length>0){ 
				JsHttpRequest.query($rcapi,{ 'w':'dropNews', 'id':id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						$("#NewsCard").modal("hide");
						loadNewsList();
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

/*==== REVIEW ====*/

function loadReviewsList() {
    JsHttpRequest.query($rcapi,{ 'w': 'loadReviewsList' },
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let dt = $("#datatable");
            dt.DataTable().destroy();
            $("#reviews_range").html(result.content);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
        }}, true);
}

function showReviewCard(id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showReviewCard', 'id':id},
        function (result, errors){ if (errors) {alert(errors);} if (result) {
            $("#ReviewCard").modal("show");
            $("#ReviewCardBody").html(result.content);
        }}, true);
}

function saveReview() {
    let id = $("#review_id").val();
    let title = $("#review_title").val();
    let text = $(".note-editable").html().replace(/"/g,"'");

    let data = $("#review_data").val();
    let status = 0; if (document.getElementById("review_status").checked){status=1;}

    swal({
            title: "Зберегти зміни?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                JsHttpRequest.query($rcapi,{'w':'saveReview', 'id':id, 'title':title, 'text':text, 'data':data, 'status':status},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        if (result["answer"]==1){
                            swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                            $("#ReviewCard").modal("hide");
                            loadReviewsList();
                        } else { swal("Помилка!", result["error"], "error");}
                    }}, true);
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function dropReview() {
    let id = $("#review_id").val();
    swal({
            title: "Видалити статтю?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (id.length>0) {
                    JsHttpRequest.query($rcapi,{ 'w':'dropReview', 'id':id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Видалено!", "", "success");
                                $("#ReviewCard").modal("hide");
                                loadReviewsList();
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function showReviewsUploadForm(review_id) {
    $("#photo_review_id").val(review_id);
    let drop = new Dropzone("#dropReview",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
    drop.removeAllFiles(true);
    drop.on("queuecomplete", function() {
        toastr["info"]("Завантаження файлів завершено.");
        this.removeAllFiles();
        $("#fileReviewsPhotoUploadForm").modal("hide");
    });
}

/*==== Request ====*/

function loadRequestsList() {
    JsHttpRequest.query($rcapi,{ 'w': 'loadRequestsList' },
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let dt = $("#datatable");
            dt.DataTable().destroy();
            $("#requests_range").html(result.content);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
        }}, true);
}

function showRequestCard(id) {
    JsHttpRequest.query($rcapi,{ 'w': 'showRequestCard', 'id':id},
        function (result, errors){ if (errors) {alert(errors);} if (result) {
            $("#RequestCard").modal("show");
            $("#RequestCardBody").html(result.content);
        }}, true);
}

function saveRequest() {
    let id = $("#request_id").val();
    let vin = $("#request_vin").val();
    let phone = $("#request_phone").val();
    let text = $("#request_text").val();

    swal({
            title: "Зберегти зміни?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                JsHttpRequest.query($rcapi,{'w':'saveRequest', 'id':id, 'vin':vin, 'phone':phone, 'text':text},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        if (result["answer"]==1){
                            swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                            $("#RequestCard").modal("hide");
                            loadRequestsList();
                        } else { swal("Помилка!", result["error"], "error");}
                    }}, true);
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function dropRequest() {
    let id = $("#request_id").val();
    swal({
            title: "Видалити статтю?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                if (id.length>0) {
                    JsHttpRequest.query($rcapi,{ 'w':'dropRequest', 'id':id},
                        function (result, errors){ if (errors) {alert(errors);} if (result){
                            if (result["answer"]==1){
                                swal("Видалено!", "", "success");
                                $("#RequestCard").modal("hide");
                                loadRequestsList();
                            } else { swal("Помилка!", result["error"], "error");}
                        }}, true);
                }
            } else {
                swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
            }
        });
}

function closeRequestCard() {
    if ($("#request_id")){
        let id = $("#request_id").val();
        JsHttpRequest.query($rcapi,{ 'w': 'closeRequestCard', 'id':id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#RequestCard").modal("hide");
                $("#RequestCardBody").html("");
                $("#RequestCardLabel").html("");
            }}, true);
    } else {
        $("#RequestCard").modal("hide");
    }
}

function unlockRequestCard(id) {
    if (id) {
        JsHttpRequest.query($rcapi,{ 'w': 'unlockRequestCard', 'id':id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                showRequestCard(id);
            }}, true);
    } else {
        $("#RequestCard").modal("hide");
    }
}

