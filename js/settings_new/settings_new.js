
var group_ids = [];

$(document).ready(function() {
	$("select#groups option").each(function() {
		group_ids.push($(this).val());
	});
});

//=Language====================================================================

function loadLanguageList() {
	JsHttpRequest.query($rcapi,{ 'w': 'loadLanguageList' }, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		let dt = $("#datatable");
		dt.DataTable().destroy();
		$("#lang_range").html(result.content);
		dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
}

function newLanguageCard(){
	let lang_var = $("#lang_var").val();

	if (lang_var === "" || lang_var === undefined) {
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
    let lang_ru 	= $("#lang_ru").val();
    let lang_ua 	= $("#lang_ua").val();
    let lang_eng 	= $("#lang_eng").val();
    let lang_id 	= $("#lang_id").val();
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

//=Contacts====================================================================

function loadContactsList() {
	JsHttpRequest.query($rcapi,{ 'w': 'loadContactsList' }, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		let dt = $("#datatable");
		dt.DataTable().destroy();
		$("#contacts_range").html(result.content);
		dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
}

function newContactsCard(){
	let lang = $("#lang_select option:selected").val();
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
    let id 			= $("#contact_id").val();
    let title 		= $("#contact_title").val();
    let address 	= $("#contact_address").val();
    let schedule 	= $("#contact_schedule").val();
    let phone 		= $("#contact_phone").val();
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

//=Contacts====================================================================

function loadContactsBotList() {
	JsHttpRequest.query($rcapi,{ 'w': 'loadContactsBotList' }, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
        let dt = $("#datatable");
        dt.DataTable().destroy();
        $("#contacts_range").html(result.content);
        dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
    }}, true);
}

function newContactsBotCard() {
	JsHttpRequest.query($rcapi,{ 'w': 'newContactsBotCard' },
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		showContactsBotCard(result["id"]);
	}}, true);
}

function showContactsBotCard(id) {
	if (id<=0 || id===""){toastr["error"](errs[0]);}
	if (id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showContactsBotCard', 'id':id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#ContactsCard").modal("show");
			$("#ContactsCardBody").html(result["content"]);
			$("#ContactsCardLabel").html($("#contact_id").val());
			$("#contacts_tabs").tab();
			let elem = document.querySelector('#contact_status');
			if (elem){ new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function saveContactsBot() {
    let id 		= $("#contact_id").val();
    let text 	= $("#contact_text").val();
    let icon 	= $("#icon_select option:selected").val();
    let link 	= $("#contact_link").val();

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

//=news====================================================================

function loadNewsPhoto(news_id, lang_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'loadNewsPhoto', 'news_id':news_id, 'lang_id':lang_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){
        let dt = $("#datatable");
        dt.DataTable().destroy();
        $("#news_photo_place").html(result.content);
        dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
    }}, true);
}

function showNewsUploadLogoForm(news_id, lang_id, file_id) {
	$("#photo_news_id").val(news_id);
	$("#photo_lang_id").val(lang_id);
	$("#photo_file_id").val(file_id);
	let myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$("#fileNewsPhotoUploadForm").modal("hide");
		loadNewsPhoto(news_id, lang_id);
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

function loadNewsList() {
	JsHttpRequest.query($rcapi,{ 'w': 'loadNewsList' }, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
        let dt = $("#datatable");
        dt.DataTable().destroy();
        $("#news_range").html(result.content);
        dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
    }}, true);
}

function newNewsCard(){
	let lang = $("#lang_select option:selected").val();
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
			let elem = document.querySelector('#news_status');
			if (elem){ new Switchery(elem, { color: '#1AB394' });}
			initSample();
			loadNewsPhoto(id,$("#lang_id").val());
		}}, true);
	}
}

function saveNews() {
    let id 		= $("#news_id").val();
    let caption = $("#news_caption").val();
    let data 	= $("#news_data").val();
    let short 	= $("#news_short").val();
    let descr 	= CKEDITOR.instances.editor.getData();

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
	if (id) {
		$("#photo_review_id").val(id);
	}
    JsHttpRequest.query($rcapi,{ 'w': 'showReviewCard', 'id':id},
        function (result, errors){ if (errors) {alert(errors);} if (result) {
            $("#ReviewCard").modal("show");
            $("#ReviewCardBody").html(result.content);
            $("#ReviewCardLabel").html(id);
			$("#groups").chosen();
        }}, true);
}

function showReviewCardInfo(id, lang_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showReviewCardInfo', 'id':id, 'lang_id':lang_id},
		function (result, errors){ if (errors) {alert(errors);} if (result) {
			$("#ReviewLangCard").modal("show");
			$("#ReviewLangCardBody").html(result.content);

			$("#summernote").summernote({
				height: 200,
				callbacks: {
					onImageUpload: function(files) {
						for(let i=0; i < files.length; i++) {
							$.upload(files[i]);
						}
					}
				}
			});
			$.upload = function (file) {
				let out = new FormData();
				out.append('file', file, file.name);

				$.ajax({
					method: 'POST',
					url: 'https://portal.myparts.pro/upload_saved_images.php',
					contentType: false,
					cache: false,
					processData: false,
					data: out,
					success: function (img) {
						$('#summernote').summernote('insertImage', img);
						console.log('inserted');
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.error(textStatus + " " + errorThrown);
					}
				});
			}

		}}, true);
}

function saveReviewCardInfo(lang_id) {
	let id 			= $("#review_id").val();
	let t 			= $("#review_t").val();
	let d 			= $("#review_d").val();
	let title 		= $("#review_title").val();
	let text 		= $("#summernote").next().find($(".note-editable")).html().replace(/"/g,"'");

	swal({
			title: "Зберегти зміни?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{'w':'saveReviewCardInfo', 'id':id, 'lang_id':lang_id, 't':t, 'd':d, 'title':title, 'text':text},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"]==1){
							swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
							$("#ReviewLangCard").modal("hide");
						} else {
							swal("Помилка!", result["error"], "error");
						}
					}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
}

function saveReview() {
    let id 			= $("#review_id").val();
    let title 		= $("#review_title").val();
    let title_ua 	= $("#review_title_ua").val();
    let title_en 	= $("#review_title_en").val();
    let data 		= $("#review_data").val();
    let data_create = $("#review_data_create").val();
    let status 		= 0; if (document.getElementById("review_status").checked){status=1;}
	let groups 		= $("#groups").chosen().val();

    swal({
            title: "Зберегти зміни?",
            text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                JsHttpRequest.query($rcapi,{'w':'saveReview', 'id':id, 'title':title, 'title_ua':title_ua, 'title_en':title_en, 'data':data, 'data_create':data_create, 'status':status, 'groups':groups},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        if (result["answer"]==1){
                            swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
							if (id) {
								$("#photo_review_id").val(id);
							}
							if (id === '0') {
								showReviewCard(result["review_id"]);
							} else {
								$("#ReviewCard").modal("hide");
							}
							loadReviewsList();
                        } else {
                        	swal("Помилка!", result["error"], "error");
                        }
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
    let drop = new Dropzone("#dropReview",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
    drop.removeAllFiles(true);
    drop.on("queuecomplete", function() {
        toastr["info"]("Завантаження файлів завершено.");
        this.removeAllFiles();
        $("#fileReviewsPhotoUploadForm").modal("hide");
        showReviewCard(review_id);
    });
}

/**
 *
 * 767867867867
 */

function list_image() {
	$.ajax({
		url:"upload_dropzone.php",
		success:function(data){
			$('#preview').html(data);
		}
	});
}

function removeReviewCard(a) {
	let name = $(a).attr('id');
	console.log('removed' + name);
	$.ajax({
		url: "upload_dropzone.php",
		method: "POST",
		data: {name:name},
		success: function(data) {
			list_image();
		}
	})
}

function choseReviewCardImage(a) {
	let review_id = $("#review_id").val();
	let file_name = $(a).attr('id');
	JsHttpRequest.query($rcapi,{ 'w':'choseReviewCardImage', 'review_id':review_id, 'file_name':file_name},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"]==1){
				$("#fileReviewsPhotoUploadForm2").modal('hide');
				showReviewCard(review_id);
			} else {
				swal("Помилка!", result["error"], "error");
			}
		}}, true);
}

function copyReviewImagePath(element) {
	let text = "https://portal.myparts.pro/" +  $(element).attr('data-src');
	navigator.clipboard.writeText(text);
	toastr["info"]("Скопійовано до буферу!");
}

function OpenReviewsUploadForm() {

	// Dropzone.options.dropzoneFrom = {
	// 	autoProcessQueue: false,
	// 	acceptedFiles:".png,.jpg,.gif,.bmp,.jpeg,.webp",
	// 	init: function(){
	// 		var submitButton = document.querySelector('#submit-all');
	// 		myDropzone = this;
	// 		submitButton.addEventListener("click", function(){
	// 			myDropzone.processQueue();
	// 		});
	// 		this.on("complete", function(){
	// 			if(this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0)
	// 			{
	// 				var _this = this;
	// 				_this.removeAllFiles();
	// 			}
	// 			list_image();
	// 		});
	// 	},
	// };

	list_image();
}

// function OpenReviewsUploadForm(review_id) {
	// Dropzone.autoDiscover = false;

	// $('#dropReview').dropzone({
	// 	addRemoveLinks: true,
	// 	addSelection: true,
	//
	// 	init: function () {
	// 		let myDropzone = this;
	//
	// 		$.ajax({
	// 			url: 'dropzone/init.php',
	// 			type: 'post',
	// 			data: {request: 2},
	// 			dataType: 'json',
	//
	// 			success: function(response) {
	//
	// 				$.each(response, function(key, value) {
	// 					let mockFile = {name: value.name, size: value.size};
	//
	// 					myDropzone.emit('addedfile', mockFile);
	// 					myDropzone.emit('thumbnail', mockFile, value.path);
	// 					myDropzone.emit('complete', mockFile);
	//
	// 				})
	// 			}
	// 		});
	// 	},

		// uploadedfile: function(file) {
		// 	console.log(file.name);
		// },
		//
		// uploaded: function(file) {
		// 	console.log(file.name);
		// },
		//
		// uploadedFiles: function(file) {
		// 	console.log(file.name);
		// },

		// addedfile: function(file) {
		// 	$.ajax({
		// 		url:'dropzone/add.php',
		// 		type:'post',
		// 		data : {"file_name" : file.name},
		//
		// 		success : function () {
		// 			console.log('added ' + file.name);
		// 		}
		// 	});
		// },

		// removedfile: function(file) {
		// 	$.ajax({
		// 		url:'dropzone/remove.php',
		// 		type:'post',
		// 		data : {"file_name" : file.name},
		//
		// 		success : function () {
		// 			console.log('removed ' + file.name);
		// 		}
		// 	});
		// 	let _ref;
		// 	return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
		// }
	// });
// }

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
    let id 		= $("#request_id").val();
    let vin 	= $("#request_vin").val();
    let phone 	= $("#request_phone").val();
    let text 	= $("#request_text").val();

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
                        } else {
                        	swal("Помилка!", result["error"], "error");
                        }
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

/*
* SEO TEXT
* */

function showSeoTextCard(id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showSeoTextCard', 'id':id},
		function (result, errors){ if (errors) {alert(errors);} if (result) {
			$("#SeoTextCard").modal("show");
			$("#SeoTextCardBody").html(result.content);
		}}, true);
}

function saveSeoText(id) {
	let router 	= $("#seo_router").val();
	let link 	= $("#seo_link").val();
	let text_ru = $("#seo_text_ru").next().find($(".note-editable")).html().replace(/"/g,"'");
	let text_ua = $("#seo_text_ua").next().find($(".note-editable")).html().replace(/"/g,"'");
	let text_en = $("#seo_text_en").next().find($(".note-editable")).html().replace(/"/g,"'");

	swal({
			title: "Зберегти зміни?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{'w':'saveSeoText', 'id':id, 'router':router, 'link':link, 'text_ru':text_ru, 'text_ua':text_ua, 'text_en':text_en},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"] == 1) {
							swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
							$("#SeoTextCard").modal("hide");
						} else {
							swal("Помилка!", result["error"], "error");
						}
					}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
}

function dropSeoText(id) {
	swal({
			title: "Видалити текст?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w':'dropSeoText', 'id':id},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"] == 1) {
							swal("Видалено!", "", "success");
							$("#SeoTextCard").modal("hide");
						} else {
							swal("Помилка!", result["error"], "error");
						}
					}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
}

function showSeoTitleCard(id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showSeoTitleCard', 'id':id},
		function (result, errors){ if (errors) {alert(errors);} if (result) {
			$("#SeoTextCard").modal("show");
			$("#SeoTextCardBody").html(result.content);
		}}, true);
}

function saveSeoTitle(id) {
	let router 		= $("#seo_router").val();
	let link 		= $("#seo_link").val();
	let text_ru 	= $("#seo_text_ru").next().find($(".note-editable")).html().replace(/"/g,"'");
	let text_ua 	= $("#seo_text_ua").next().find($(".note-editable")).html().replace(/"/g,"'");
	let text_en 	= $("#seo_text_en").next().find($(".note-editable")).html().replace(/"/g,"'");
	let descr_ru 	= $("#seo_descr_ru").next().find($(".note-editable")).html().replace(/"/g,"'");
	let descr_ua 	= $("#seo_descr_ua").next().find($(".note-editable")).html().replace(/"/g,"'");
	let descr_en 	= $("#seo_descr_en").next().find($(".note-editable")).html().replace(/"/g,"'");

	swal({
			title: "Зберегти зміни?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{'w':'saveSeoTitle', 'id':id, 'router':router, 'link':link, 'text_ru':text_ru, 'text_ua':text_ua, 'text_en':text_en, 'descr_ru':descr_ru, 'descr_ua':descr_ua, 'descr_en':descr_en},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"] == 1) {
							swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
							$("#SeoTextCard").modal("hide");
						} else {
							swal("Помилка!", result["error"], "error");
						}
					}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
}

function dropSeoTitle(id) {
	swal({
			title: "Видалити текст?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w':'dropSeoTitle', 'id':id},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"] == 1) {
							swal("Видалено!", "", "success");
							$("#SeoTextCard").modal("hide");
						} else {
							swal("Помилка!", result["error"], "error");
						}
					}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
}

/*
* SEO FOOTER
* */

function showSeoFooterCard(id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showSeoFooterCard', 'id':id},
		function (result, errors){ if (errors) {alert(errors);} if (result) {
			$("#SeoTextCard").modal("show");
			$("#SeoTextCardBody").html(result.content);
		}}, true);
}

function saveSeoFooter(id) {
	let router 	= $("#seo_router").val();
	let link 	= $("#seo_link").val();
	let text_ru = $("#seo_text_ru").next().find($(".note-editable")).html().replace(/"/g,"'");
	let text_ua = $("#seo_text_ua").next().find($(".note-editable")).html().replace(/"/g,"'");
	let text_en = $("#seo_text_en").next().find($(".note-editable")).html().replace(/"/g,"'");

	swal({
			title: "Зберегти зміни?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{'w':'saveSeoFooter', 'id':id, 'router':router, 'link':link, 'text_ru':text_ru, 'text_ua':text_ua, 'text_en':text_en},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"] == 1) {
							swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
							$("#SeoTextCard").modal("hide");
						} else {
							swal("Помилка!", result["error"], "error");
						}
					}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
}

function dropSeoFooter(id) {
	swal({
			title: "Видалити текст?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w':'dropSeoFooter', 'id':id},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"] == 1) {
							swal("Видалено!", "", "success");
							$("#SeoTextCard").modal("hide");
						} else {
							swal("Помилка!", result["error"], "error");
						}
					}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
}

/*
* SEO GENERATE
* */

function showSeoGenerateCard(id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showSeoGenerateCard', 'id':id},
		function (result, errors){ if (errors) {alert(errors);} if (result) {
			$("#SeoTextCard").modal("show");
			$("#SeoTextCardBody").html(result.content);
		}}, true);
}

function saveSeoGenerate(id) {
	let router 	= $("#seo_router").val();
	let link 	= $("#seo_link").val();
	let text_ru = $("#seo_text_ru").next().find($(".note-editable")).html().replace(/"/g,"'");
	let text_ua = $("#seo_text_ua").next().find($(".note-editable")).html().replace(/"/g,"'");
	let text_en = $("#seo_text_en").next().find($(".note-editable")).html().replace(/"/g,"'");

	swal({
			title: "Зберегти зміни?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{'w':'saveSeoGenerate', 'id':id, 'router':router, 'link':link, 'text_ru':text_ru, 'text_ua':text_ua, 'text_en':text_en},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"] == 1) {
							swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
							$("#SeoTextCard").modal("hide");
						} else {
							swal("Помилка!", result["error"], "error");
						}
					}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
}

function dropSeoGenerate(id) {
	swal({
			title: "Видалити текст?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w':'dropSeoGenerate', 'id':id},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"] == 1) {
							swal("Видалено!", "", "success");
							$("#SeoTextCard").modal("hide");
						} else {
							swal("Помилка!", result["error"], "error");
						}
					}}, true);
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
}
