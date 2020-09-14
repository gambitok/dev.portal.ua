var errs=[]; var mess=[];
errs[0]="Помилка карти індексу";
errs[1]="Занадто короткий запит для пошуку";
mess[0]="Уточніть пошук";

function catalogue_article_search(view_op,search_type){
    let art=$("#catalogue_art").val();
	if (art.length<=2 && view_op==""){ $("#srchInG").addClass("has-error"); }
	if (art.length>2 || view_op==1){
		$("#srchInG").removeClass("has-error");
		//var hash = $(location).attr('protocol')+"//"+$(location).attr('hostname')+"/Catalogue/"+art;
		//$(window.location).attr('href',hash);
		$("#waveSpinner_place").html(waveSpinner);
		$("#catalogue_range").empty();
		$("#catalogue_header").empty();
		JsHttpRequest.query($rcapi,{ 'w': 'catalogue_article_search', 'art':art, 'search_type':search_type}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			//var table = $('#datatable').DataTable();
			//table.destroy();
			if (result["brand_list"]!="" && result["brand_list"]!=null && search_type==0){
				$("#FormModalWindow").modal("show");
				$("#FormModalBody").html(result["brand_list"]);
				$("#FormModalLabel").html(mess[0]);
			}
			if (result["brand_list"]=="" || result["brand_list"]==null || search_type>0){
				$("#catalogue_range").html(result["content"]);
				$("#catalogue_header").html(result["header"]);
				//$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": false,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
				$("#waveSpinner_place").html("");
			}
		}}, true);
	}
}

var barcode_settings = {barWidth: 1,barHeight: 50,moduleSize: 5,showHRI: true,addQuietZone: true,marginHRI: 5,bgColor: "#FFFFFF",color: "#000000",fontSize: 14,output: "css",posX: 0,posY: 0};

var chosen_config = {
	'.chosen-select'           : {},
	'.chosen-select-deselect'  : {allow_single_deselect:true},
	'.chosen-select-no-single' : {disable_search_threshold:10},
	'.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
	'.chosen-select-width'     : {width:"95%"}
};

for (var selector in chosen_config) {
	$(selector).chosen(chosen_config[selector]);
}

function checkBrandSelectList(){
    let list_brand_select=$("#list_brand_select").html();
	if (list_brand_select.length>0){
		$("#FormModalWindow").modal("show");
		$("#FormModalBody").html(list_brand_select);
		$("#FormModalLabel").html(mess[0]);
	}
}

function fil4Search(){
    let brand_id=$("#fil4BrandId").val();
    let goods_group_id=$("#fil4GoodsGroupId").val();
	$("#waveSpinner_place").html(waveSpinner);
	$("#catalogue_range").empty();
	$("#catalogue_header").empty();
	JsHttpRequest.query($rcapi,{ 'w': 'catalogue_fil4_search', 'brand_id':brand_id, 'goods_group_id':goods_group_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#catalogue_range").html(result["content"]);
		$("#catalogue_header").html(result["header"]);
		$("#waveSpinner_place").html("");
	}}, true);
}

function fil2Search(){
	let str_id=$("#fil2StrId").val();
    let art_ids=$("#fil2ArtIds").val();
	if (str_id>0){
		$("#waveSpinner_place").html(waveSpinner);
		$("#catalogue_range").empty();
		$("#catalogue_header").empty();
		JsHttpRequest.query($rcapi,{ 'w': 'catalogue_fil2_search', 'art_ids':art_ids},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let table=$("#datatable");
            let cat_range=$("#catalogue_range");
			let cat_header=$("#catalogue_header");
            cat_range.html(result["content"]);
            cat_header.html(result["header"]);
			if (table.length) table.DataTable().destroy();
            cat_range.html(result["content"]);
            cat_header.html(result["header"]);
            table.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
			$("#waveSpinner_place").html("");
		}}, true);
	} else {swal("Помилка!", "Оберіть категорію запчастин", "error"); }
}

function loadFilterModelSelectList(){
    let mfa_id=$("#fil2Manufacture option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadModelSelectList', 'mfa_id':mfa_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let filModel=$("#fil2Model");
            filModel.html(result["content"]);
            filModel.select2({placeholder: "Вибрати модель"});
		}}, true);
}

function loadFilterModificationSelectList(){
    let mod_id=$("#fil2Model option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadFilterModificationSelectList', 'mod_id':mod_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
            let filModification=$("#fil2Modification");
            filModification.html(result["content"]);
            filModification.select2({placeholder: "Вибрати модифікацію"});
		}}, true);
}

function loadFilterGroupTreeListSide(){
    let typ_id=$("#fil2Modification option:selected").val();
	if (typ_id>0){
		$("#waveSpinner_place").html(waveSpinner);
		JsHttpRequest.query($rcapi,{ 'w': 'loadFilterGroupTreeListSide', 'typ_id':typ_id},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				$("#filter_range_place").html(result["content"]);
				$("#catalogue_side_tree_place").html(result["tree"]);
				$("#catalogue_result_table_place").html(result["result_table"]);
				treefilter($("#tree1"), {
					searcher : $("input#my-search"), offsetLeft :20,
					multiselect : true,expanded : true,
				});
			}}, true);
	} else {swal("Помилка!", "Оберіть модифікацію", "error"); }
}

function loadFilterGroupTreeList(){
    let typ_id=$("#fil2Modification option:selected").val();
	if (typ_id>0){
		$("#waveSpinner_place").html(waveSpinner);
		JsHttpRequest.query($rcapi,{ 'w': 'loadFilterGroupTreeList', 'typ_id':typ_id},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				$("#FormModalWindow").modal("show");
				$("#FormModalBody").html(result["content"]);
				$("#FormModalLabel").html(result["header"]);
				treefilter($("#tree1"), {
					searcher : $("input#my-search"), offsetLeft :20,
					multiselect : true,expanded : true,
				});
				$("#waveSpinner_place").html("");
			}}, true);
	} else { swal("Помилка!", "Оберіть модифікацію", "error"); }
}

function clearFilterGroupTreeInput(){
	$("#my-search").val("");
	$("input#my-search").keyup().add("");
}

function setFil2StrInfo(id,name,art_ids){
	$("#fil2StrId").val(id);
	$("#fil2StrText").val(name);
	$("#fil2ArtIds").val(art_ids);
	fil2Search();
}

function showCatFieldsViewForm(){
	JsHttpRequest.query($rcapi,{ 'w':'showCatFieldsViewForm'},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
        $("#FormModalBody").html(result["content"]);
        $("#FormModalLabel").html(result["header"]);
		$(".table-sortable tbody").sortable({
			handle: 'span',
			stop: function(event, ui) {},
			update: function (event, ui) {
				var data = $(this).sortable('serialize').toString();
			}
		});
	}}, true);
}

function saveCatalogueFieldsViewForm(){
	var data = $(".table-sortable tbody").sortable("toArray");
	var kol_fields=$("#kol_fields").val();
	var fl_id=[]; var fl_ch=[];
	for (var i=1;i<=kol_fields;i++){
		var field_id=data[i-1].split("_")[1];
		fl_id[i]=field_id;
		var ch=0; if (document.getElementById("use_"+field_id).checked){ch=1;}
		fl_ch[i]=ch;
	}
	if (kol_fields>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueFieldsViewForm', 'kol_fields':kol_fields, 'fl_id':fl_id, 'fl_ch':fl_ch},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
				$("#FormModalWindow").modal("hide");
				catalogue_article_search(1,0);
			} else { swal("Помилка!", result["error"], "error"); }
		}}, true);
	}
}

function showCatalogueCard(art_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showCatalogueCard', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			let cat_card = $("#CatalogueCard");
			cat_card.modal("show");
			$("#CatalogueCardBody").html(result["content"]);
			$("#CatalogueCardArticleNrDispl").html(result["nr_display"]);
			$("#catalogue_tabs").tab();
			$("#barcode-visual").barcode(""+$("#barcode").val(),"code39",barcode_settings);
			$("#article_info").markdown({autofocus:false,savable:false});
			$("#brand_id").select2({placeholder: "Виберіть Бренд",dropdownParent: cat_card});
            var elem = document.querySelector("#price_export_status");if (elem){ var price_export_status = new Switchery(elem, { color: '#1AB394' });}
		}}, true);
	}
}

function loadArticleParams(art_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleParams', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#params_place").html(result["content"]);
			$("#catalogue_tabs").tab();
			$("#units").select2({placeholder: "Одиниці виміру",dropdownParent: $("#CatalogueCard")});
		}}, true);
	}
}

function loadArticleLogistic(art_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleLogistic', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#logistic_place").html(result["content"]);
			$("#catalogue_tabs").tab();
		}}, true);
	}
}

function preconfirmCatalogueGeneralInfo(){
	swal({
		title: "Зберегти зміни у розділі \"Загальна інформація\"?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			saveCatalogueGeneralInfo();
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveCatalogueGeneralInfo(){
    let art_id=$("#art_id").val();
    let article_nr_displ=$("#article_nr_displ").val();
    let inner_cross=$("#inner_cross").val();
    let brand_id=$("#brand_id").val();
    let goods_group_id=$("#goods_group_id").val();
    let article_name=$("#article_name").val();
    let article_name_ukr=$("#article_name_ukr").val();
    let article_info=$("#article_info").val();
    let unique_number=$("#unique_number").val();
    let export_status=$("#price_export_status").prop("checked"); if (export_status) export_status=1; else export_status=0;
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueGeneralInfo', 'art_id':art_id, 'article_nr_displ':article_nr_displ, 'inner_cross':inner_cross, 'brand_id':brand_id, 'goods_group_id':goods_group_id, 'article_name':article_name, 'article_info':article_info, 'article_name_ukr':article_name_ukr, 'unique_number':unique_number, 'export_status':export_status},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                showCatalogueCard(art_id);
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function saveCatalogueParams(art_id){
    let mes="Зберегти зміни у розділі \"Характеристики\"?";
    let template_id=$("#goods_group_template_id option:selected").val();
	if (template_id===0){
		mes="Ви дійсно хочете відвязати від шаблону артикул \""+$("#CatalogueCardArticleNrDispl").html()+"\" та видалити всі параметри, які були введені раніше?";
	}
	swal({
		title: mes,text: "",type: "warning",allowOutsideClick:true,allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let goods_group_id=$("#goods_group_id").val();
            let cn=$("#params_kol").val();
			var fields_type=[];var params_value=[];
			for (var i=1;i<=cn;i++){
				let param_id=$("#paramId_"+i).val();
				fields_type[param_id]=$("#param_field_type_"+param_id+" option:selected").val();
				params_value[param_id]=$("#param_value_"+param_id).val();
			}
			if (art_id.length>0){//,'fields_type':fields_type
				JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueParams', 'art_id':art_id, 'goods_group_id':goods_group_id, 'template_id':template_id, 'params_value':params_value},
				function (result, errors){ if (errors) {alert(errors);} if (result){
					if (result["answer"]==1){
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {swal("Відмінено", "Внесені Вами зміни анульовано.", "error");}
	});
}

function saveCatalogueParamsTemplate(art_id){
	swal({
		title: "Зберегти зміни у шаблоні?",text: "",type: "warning",allowOutsideClick:true,allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let goods_group_id=$("#tmp_goods_group_id").val();
            let template_id=$("#tmp_template_id").val();
            let template_name=$("#tmp_template_name").val();
            let template_caption=$("#tmp_template_caption").val();
            let template_descr=$("#tmp_template_descr").val();
            let cn=$("#tmp_params_kol").val();
			var params_id=[];var fields_type=[];var params_name=[];var params_type=[];
			
			for (var i=1;i<=cn;i++){
				params_id[i]=$("#tmp_param_id_"+i).val();
				fields_type[i]=$("#tmp_param_field_type_"+i+" option:selected").val();
				params_type[i]=$("#tmp_param_type_"+i+" option:selected").val();
				params_name[i]=$("#tmp_param_name_"+i).val();
			}
			
			if (art_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueParamsTemplate', 'art_id':art_id, 'goods_group_id':goods_group_id, 'template_id':template_id, 'template_name':template_name, 'template_caption':template_caption, 'template_descr':template_descr, 'cn':cn, 'params_id':params_id, 'fields_type':fields_type, 'params_name':params_name, 'params_type':params_type},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal("hide");
						loadArticleParams(art_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {swal("Відмінено", "Внесені Вами зміни анульовано.", "error");}
	});
}

function unlinkCatalogueGoodGroup(art_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
        let goods_group_id=$("#goods_group_id").val();
		JsHttpRequest.query($rcapi,{ 'w': 'unlinkCatalogueGoodGroup', 'art_id':art_id, 'goods_group_id':goods_group_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
				$("#goods_group_id").val("");
				$("#goods_group_name").val("");
				swal("Виконано!", "Товарну групу відвязано", "success");
			} else {swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function showGoodGroupTree(art_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		let goods_group_id=$("#goods_group_id").val();
		JsHttpRequest.query($rcapi,{ 'w': 'showGoodGroupTree', 'goods_group_id':goods_group_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#GoodsGroupModalWindow").modal("show");
			$("#GoodsGroupBody").html(result["content"]);
			$("#jsGoodGroupTree") .on('changed.jstree', function (e, data) {
				var i, j, r = [];
				for(i = 0, j = data.selected.length; i < j; i++) {
				  r.push(data.instance.get_node(data.selected[i]).text);
				}
				$("#jsGoodGroupTree_result").val('' + r.join(', '));
				var i, j, r = [];
				for(i = 0, j = data.selected.length; i < j; i++) {
				  r.push(data.instance.get_node(data.selected[i]).id);
				}
				$("#jsGoodGroupTree_id").val(''+r.join(', '));
		    }).jstree({
				'core' : {'check_callback' : true},
				'plugins' : [ 'types', 'dnd' ],
				'types' : {'default' : {'icon' : 'fa fa-folder'},}
        	});
		}}, true);
	}
}

function setGoodGroupSelect(){
	let id=$("#jsGoodGroupTree_id").val();
    let name=$("#jsGoodGroupTree_result").val();
	$("#goods_group_id").val(id);
	$("#goods_group_name").val(name);
	$("#GoodsGroupModalWindow").modal("hide");
}

function generateBarcodeIncart(){
	$("#barcode-visual").barcode(""+$("#barcode").val(),"code39",barcode_settings);
}

function loadArticleCommets(art_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleCommets', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#article_commets_place").html(result["content"]);
		}}, true);
	}
}

function saveArticleComment(art_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
        let comment=$("#article_comment_field").val();
		if (comment.length<=0){toastr["error"]("Напишіть коментар спочатку");}
		if (comment.length>0){
			JsHttpRequest.query($rcapi,{ 'w': 'saveArticleComment', 'art_id':art_id, 'comment':comment}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadArticleCommets(art_id);
					$("#article_comment_field").val("");
				} else { toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function dropArticleComment(art_id,cmt_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		if(confirm('Видалити запис?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'dropArticleComment', 'art_id':art_id, 'cmt_id':cmt_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadArticleCommets(art_id);
					toastr["info"]("Запис успішно видалено");
				} else { toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function loadArticleCDN(art_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleCDN', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#article_cdn_place").html(result["content"]);
		}}, true);
	}
}

function showArticlesCDNUploadForm(art_id){
	$("#cdn_art_id").val(art_id);
	Dropzone.autoDiscover = false;
	var myDropzone2 = new Dropzone("#myDropzone2",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone2.removeAllFiles(true);
	myDropzone2.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$("#fileArticlesCDNUploadForm").modal("hide");
		loadArticleCDN(art_id);
	});
}

function showArticlesCDNDropConfirmForm(art_id,file_id,file_name){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){	
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'articlesCDNDropFile', 'art_id':art_id, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadArticleCDN(art_id);
					toastr["info"]("Файл успішно видалено");
				} else { toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function showArtilceGallery(art_id,article_nr_displ){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showArtilceGallery', 'art_id':art_id,'article_nr_displ':article_nr_displ}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal("show");
			$("#FormModalBody").html(result["content"]);
			$("#FormModalLabel").html(result["header"]);
		}}, true);
	}
}

function loadArticleFoto(art_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleFoto', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#article_foto_place").html(result["content"]);
		}}, true);
	}
}

function setArticlesFotoMain(art_id,file_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'setArticlesFotoMain', 'art_id':art_id, 'file_id':file_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
				loadArticleFoto(art_id);
				toastr["info"]("Основне фото успішно призначено");
			} else { toastr["error"](result["error"]); }
		}}, true);
	}
}

function showArticlesFotoUploadForm(art_id){
	$("#foto_art_id").val(art_id);
	var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$("#fileArticlesFotoUploadForm").modal("hide");
		loadArticleFoto(art_id);
	});
}

function showArticlesFotoDropConfirmForm(art_id,file_id,file_name){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){		
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'articlesFotoDropFile', 'art_id':art_id, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadArticleFoto(art_id);
					toastr["info"]("Файл успішно видалено");
				} else { toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function showArticlesSchemeUploadForm(){
	let template_id=$("#goods_group_template_id").val();
	if (template_id>0){
		$("#scheme_template_id").val(template_id);
		$("#fileArticlesSchemeUploadForm").modal("show");
		Dropzone.autoDiscover = false;
		var myDropzone4 = new Dropzone("#myDropzone4",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
		myDropzone4.removeAllFiles(true);
		myDropzone4.on("queuecomplete", function() {
			toastr["info"]("Завантаження файлів завершено.");
			this.removeAllFiles();
			$("#fileArticlesSchemeUploadForm").modal("hide");
			loadArticleScheme(template_id);
		});
	} else {swal("Помилка", "Оберіть шаблон для завантаження схеми.", "error");}
}

function loadArticleScheme(template_id){
	if (template_id<=0 || template_id===""){toastr["error"](errs[0]);}
	if (template_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleScheme', 'template_id':template_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#articles_params_scheme_files").html(result["content"]);
		}}, true);
	}
}

function showArticlesSchemeDropConfirmForm(template_id,file_id,file_name){
	if (template_id<=0 || template_id===""){toastr["error"](errs[0]);}
	if (template_id>0){		
		if(confirm('Видалити схему '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'articlesSchemeDropFile', 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadArticleScheme(template_id);
					toastr["info"]("Файл успішно видалено");
				} else { toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function loadArticleAnalogs(art_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleAnalogs', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#analog_place").html(result["content"]);
			$("#catalogue_tabs").tab();
			$("#datatable1").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});
			$("#datatable2").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});
			$("#datatable3").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});
			$("#datatable4").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});
			$("#datatable5").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});
		}}, true);
	}
}

function loadArticleAplicability(art_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleAplicability', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#aplicability_place").html(result["content"]);
            $("#catalogue_tabs").tab();
		}}, true);
	}
}

/*==== TREE ARTS ====*/

function loadArticleTree() {
	let art_id = $("#art_id").val();
    if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
    if (art_id>0){
        JsHttpRequest.query($rcapi,{ 'w': 'loadArticleTree', 'art_id':art_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#tree_place").html(result["content"]);
                $("#catalogue_tabs").tab();
            }}, true);
    }
}

function showArticleTreeTecdoc() {
    $("#CatalogAddCard").modal("show");
    JsHttpRequest.query($rcapi,{ 'w': 'showArticleTreeTecdoc'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#CatalogAddCardBody").html(result.content);
            treefilter($("#my-tree"), {searcher : $("input#my-search")});
        }}, true);
}

function showArticleTreeNew() {
    $("#CatalogAddCard").modal("show");
    JsHttpRequest.query($rcapi,{ 'w': 'showArticleTreeNew'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#CatalogAddCardBody").html(result.content);
        }}, true);
}

function saveArticleTreeTecdoc(str_id) {
    let art_id = $("#art_id").val();
    swal({
            title: "Привязати індекс?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так", cancelButtonText: "Відмінити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                JsHttpRequest.query($rcapi,{ 'w': 'saveArticleTreeTecdoc', 'art_id':art_id, 'str_id':str_id},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        swal("Виконано!", "", "success");
                        loadArticleTree();
                        $("#CatalogAddCard").modal("hide")
                    }}, true);
            } else {
                swal("Відмінено", "", "error");
            }
        });
}

function saveArticleTreeNew(group_id) {
    let art_id = $("#art_id").val();
    swal({
            title: "Привязати індекс?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так", cancelButtonText: "Відмінити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                JsHttpRequest.query($rcapi,{ 'w': 'saveArticleTreeNew', 'art_id':art_id, 'group_id':group_id},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        swal("Виконано!", "", "success");
                        loadArticleTree();
                        $("#CatalogAddCard").modal("hide")
                    }}, true);
            } else {
                swal("Відмінено", "", "error");
            }
        });
}

function dropArticleTreeTecdoc(art_id,str_id) {
    swal({
            title: "Відвязати індекс?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так", cancelButtonText: "Відмінити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                JsHttpRequest.query($rcapi,{ 'w': 'dropArticleTreeTecdoc', 'art_id':art_id, 'str_id':str_id},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        swal("Виконано!", "", "success");
                        loadArticleTree();
                    }}, true);
            } else {
                swal("Відмінено", "", "error");
            }
        });
}

function dropArticleTreeNew(art_id,group_id) {
    swal({
            title: "Відвязати індекс?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Так", cancelButtonText: "Відмінити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                JsHttpRequest.query($rcapi,{ 'w': 'dropArticleTreeNew', 'art_id':art_id, 'group_id':group_id},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        swal("Виконано!", "", "success");
                        loadArticleTree();
                    }}, true);
            } else {
                swal("Відмінено", "", "error");
            }
        });
}

function searchCatInput() {
    var input, filter, li, a, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    if (filter!=="") {
        $(".tree-list").each(function () {
            $(this).removeClass("dnone");
        });
    } else {
        $(".tree-list").each(function () {
            $(this).addClass("dnone");
        });
    }
    li = $(".group-tree li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

/*==== /TREE ARTS ====*/

function loadArticleAplicabilityModels(art_id,mfa_id){
	JsHttpRequest.query($rcapi,{ 'w': 'loadArticleAplicabilityModels', 'art_id':art_id, 'mfa_id':mfa_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#aplic_tab"+mfa_id).html(result.content);
		$("#catalogue_tabs").tab();
		$("#datatable_models"+mfa_id).DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
	}}, true);
}

function unlinkArticleAplicabilityModel(mfa_id,art_id,typ_id,typ_text){
	swal({
		title: "Відвязати "+$("#CatalogueCardArticleNrDispl").html()+" від \""+typ_text+"\"?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
		confirmButtonText: "Так", cancelButtonText: "Відмінити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'unlinkArticleAplicabilityModel', 'art_id':art_id, 'typ_id':typ_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadArticleAplicabilityModels(art_id,mfa_id);
					swal("Виконано!", "", "success");
				} else {swal("Помилка", result["answer"], "error");}
			}}, true);
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function clearActicleAplicabilityManuf(art_id,mfa_id){
	swal({
		title: "Відвязати "+$("#CatalogueCardArticleNrDispl").html()+" від всіх авто?", text: "Ви впевнені?", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Відвязати", cancelButtonText: "Відмінити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'clearActicleAplicabilityManuf', 'art_id':art_id, 'mfa_id':mfa_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadArticleAplicabilityModels(art_id,mfa_id);
					swal("Виконано!", "", "success");
				} else {swal("Помилка", result["answer"], "error");}
			}}, true);
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadArticleAplicabilityNew(art_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		let index=$("#CatalogueCardArticleNrDispl").html();
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleAplicabilityNew', 'art_id':art_id, 'index':index},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow3").modal("show");
			$("#FormModalBody3").html(result["content"]);
			$("#FormModalLabel3").html(result["header"]);
			treefilter($("#na_tree"), {
				searcher : $("na_input#na_my-search"), offsetLeft :20,
				multiselect : true,expanded : true,
			});
		}}, true);
	}
}

function loadAplicabilityModelList(mfa_id){
	if (mfa_id<=0 || mfa_id===""){toastr["error"](errs[0]);}
	if (mfa_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadAplicabilityModelList', 'mfa_id':mfa_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#na_mod_id").html(result["content"]);
		}}, true);
	}
}

function loadAplicabilityModificationList(mfa_id,mod_id){
	if (mfa_id<=0 || mod_id<=0 || mfa_id===""){toastr["error"](errs[0]);}
	if (mfa_id>0 && mod_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadAplicabilityModificationList', 'mod_id':mod_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#na_typ_id").html(result["content"]);
		}}, true);
	}
}

function checkModifAll(el){ var er=0;
	var modif_kol=$("#modif_kol").val();
	if (el.checked){ er=1;
		for (i=1;i<=modif_kol;i++){
			document.getElementById("modif"+i).checked="checked";
		}	
	}
	if (!el.checked && er==0){ er=1;
		for (i=1;i<=modif_kol;i++){
			document.getElementById("modif"+i).checked="";
		}	
	}
}

function saveCatalogueAplicabilityForm(art_id,display_number){
	let comment=$("#na_comment").val();
	let modif_kol=$("#modif_kol").val();

	var typ_array="";
	for (i=1;i<=modif_kol;i++){
		if (document.getElementById("modif"+i).checked){
			typ_array+=document.getElementById("modif"+i).value+",";
		}
	}

	var str_array="";
	let str_kol=$("#menu_kol_elem").val();
	for (i=1;i<=str_kol;i++){
		if (document.getElementById("na_tree_"+i)){
			if (document.getElementById("na_tree_"+i).checked){
				str_array+=document.getElementById("na_tree_"+i).value+",";
			}
		}
	}

	if (art_id.length>0 && typ_array!==""){
		JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueAplicabilityForm', 'art_id':art_id, 'comment':comment, 'typ_array':typ_array, 'str_array':str_array},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
				loadArticleAplicability(art_id);
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function showLaIdCommentForm(art_id,type_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0 && type_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showLaIdCommentForm', 'art_id':art_id, 'type_id':type_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow3").modal("show");
            $("#FormModalBody3").html(result["content"]);
            $("#FormModalLabel3").html(result["header"]);
		}}, true);
	}
}

function saveLaIdCommentForm(){
    let art_id=$("#cm_art_id").val();
    let type_id=$("#cm_type_id").val();
    let kol=$("#kol_elem_la").val();
	var la_ids=[], sorts=[], types=[], text_names=[], texts=[];
	for (var i=1;i<=kol;i++){
		la_ids[i]=$("#la_id_"+i).val();
		sorts[i]=$("#sort_"+i).val();
		types[i]=$("#type_"+i).val();
		text_names[i]=$("#text_name_"+i).val();
		texts[i]=$("#text_"+i).val();
	}
	JsHttpRequest.query($rcapi,{ 'w': 'saveLaIdCommentForm', 'art_id':art_id, 'type_id':type_id, 'kol':kol, 'la_ids':la_ids, 'sorts':sorts, 'types':types, 'text_names':text_names, 'texts':texts},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			showLaIdCommentForm(art_id,type_id);
		} else { swal("Помилка!", result["error"], "error");}
	}}, true);
}

function dropLaIdComment(art_id,type_id,la_id,sortf,typef){
	swal({
		title: "Видалити LA_ID коментар?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
		confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w':'dropLaIdComment', 'art_id':art_id, 'type_id':type_id, 'la_id':la_id, 'sortf':sortf, 'typef':typef},
			function (result, errors){ if(errors){alert(errors);} if (result){  
				if (result["answer"]==1){
					swal("Видалено!", "Коментар успішно видалено.", "success");
					showLaIdCommentForm(art_id,type_id);
				} else { swal("Помилка!", result["error"], "error"); }
			}}, true);
		} else { swal("Відмінено", "Внесені Вами зміни анульовано.", "error"); }
	});
}

function preconfirmCatalogueLogistic(art_id){
	swal({
		title: "Зберегти зміни у розділі \"Логістика\"?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {
			saveCatalogueLogistic(art_id);
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function saveCatalogueLogistic(art_id){
    let index_pack=$("#index_pack").val();
    let height=$("#height").val();
    let length=$("#length").val();
    let width=$("#width").val();
    let volume=$("#volume").val();
    let weight_netto=$("#weight_netto").val();
    let weight_brutto=$("#weight_brutto").val();
    let necessary_amount_car=$("#necessary_amount_car").val();
    let units_id=$("#units_id").val();
    let multiplicity_package=$("#multiplicity_package").val();
    let shoulder_delivery=$("#shoulder_delivery").val();
    let general_quant=$("#general_quant").val();
    let work_pair_n=$("#work_pair_n").val();
	var work_pair=[];
	for (var i=1;i<=work_pair_n;i++){
		work_pair[i]=$("#work_pair_"+i).val();
	}
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueLogistic', 'art_id':art_id, 'index_pack':index_pack, 'height':height, 'length':length, 'width':width, 'volume':volume, 'weight_netto':weight_netto, 'weight_brutto':weight_brutto, 'necessary_amount_car':necessary_amount_car, 'units_id':units_id, 'multiplicity_package':multiplicity_package, 'shoulder_delivery':shoulder_delivery, 'general_quant':general_quant, 'work_pair':work_pair},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function loadArticleZED(art_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleZED', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#zed_place").html(result["content"]);
			$("#catalogue_tabs").tab();
		}}, true);
	}
}

function preconfirmCatalogueZED(art_id){
	swal({
		title: "Зберегти зміни у розділі \"ЗЕД\"?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {saveCatalogueZED(art_id);}
		else {swal("Відмінено", "Внесені Вами зміни анульовано.", "error");}
	});
}

function saveCatalogueZED(art_id){
	let country_id=$("#country_id").val();
    let costums_id=$("#costums_id").val();
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueZED', 'art_id':art_id, 'country_id':country_id, 'costums_id':costums_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function showCountryManual(){
    let country_id=$("#country_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCountryManual', 'country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CountryModalWindow").modal("show");
		$("#CountryBody").html(result["content"]);
		setTimeout(function(){
			$("#datatable_country").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCountry(id,name){
	$("#country_id").val(id);
	$("#country_name").val(name);
	$("#CountryModalWindow").modal("hide");
}

function showCountryForm(country_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCountryForm', 'country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
		$("#FormModalBody").html(result["content"]);
		$("#FormModalLabel").html(result["header"]);
	}}, true);
}

function saveCatalogueCountryForm(){
    let id=$("#form_country_id").val();
    let name=$("#form_country_name").val();
    let alfa2=$("#form_country_alfa2").val();
    let alfa3=$("#form_country_alfa3").val();
    let duty=$("#form_country_duty").val();
    let risk=$("#form_country_risk").val();
	JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueCountryForm', 'id':id, 'name':name, 'alfa2':alfa2, 'alfa3':alfa3, 'duty':duty, 'risk':risk},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			$("#country_id").val(id);
			showCountryManual();
		} else { swal("Помилка!", result["error"], "error");}
	}}, true);
}

function dropCountry(id){
	swal({
		title: "Видалити країну у розділі \"ЗЕД\"?", text: "Ви впевнені?", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, видалити!", cancelButtonText: "Хочу жити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w':'dropCountry', 'country_id':id},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Видалено!", "Країну успішно видалено.", "success");
					showCountryManual();
				} else { swal("Помилка!", result["error"], "error");}
			}}, true);
		} else {swal("Відмінено", "Внесені Вами зміни анульовано.", "error");}
	});
}

function clearArtilceCountry(){
	$("#country_id").val("");
	$("#country_name").val("");
}

function showCostumsManual(){
    let costums_id=$("#costums_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsManual', 'costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CostumsModalWindow").modal("show");
		$("#CostumsBody").html(result["content"]);
		setTimeout(function(){
			$("#datatable_costums").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCostums(id,code){
	$("#costums_id").val(id);
	$("#costums_code").val(code);
	$("#CostumsModalWindow").modal("hide");
}

function showCostumsForm(costums_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsForm', 'costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
		$("#FormModalBody").html(result["content"]);
		$("#FormModalLabel").html(result["header"]);
	}}, true);
}

function saveCatalogueCostumsForm(){
    let id=$("#form_costums_id").val();
    let code=$("#form_costums_code").val();
    let name=$("#form_costums_name").val();
    let preferential_rate=$("#form_costums_preferential_rate").val();
    let full_rate=$("#form_costums_full_rate").val();
    let type_declaration=$("#form_costums_type_declaration").val();
    let sertification=$("#form_costums_sertification").val();
    let gos_standart=$("#form_costums_gos_standart").val();
	JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueCostumsForm', 'id':id, 'code':code, 'name':name, 'preferential_rate':preferential_rate, 'full_rate':full_rate, 'type_declaration':type_declaration, 'sertification':sertification, 'gos_standart':gos_standart},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			$("#CostumsModalWindow").modal("hide");
			$("#FormModalWindow").modal("hide");
			showCostumsManual();
		} else { swal("Помилка!", result["error"], "error");}
	}}, true);
}

function dropCostums(id){
	swal({
		title: "Видалити митний код у розділі \"ЗЕД\"?", text: "Ви впевнені?", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, видалити!", cancelButtonText: "Хочу жити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w':'dropCostums', 'costums_id':id},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Видалено!", "Митний код успішно видалено.", "success");
					showCostumsManual();
				} else { swal("Помилка!", result["error"], "error");}
			}}, true);
		} else {swal("Відмінено", "Внесені Вами зміни анульовано.", "error");}
	});
}

function clearArtilceCostums(){
	$("#costums_id").val("");
	$("#costums_code").val("");
}

function showCatalogueGoodGroupTemplateForm(art_id,template_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCatalogueGoodGroupTemplateForm', 'art_id':art_id, 'template_id':template_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
        $("#FormModalBody").html(result["content"]);
        $("#FormModalLabel").html(result["header"]);
	}}, true);
}

function editCatalogueGoodGroupTemplateForm(art_id){
	let template_id=$("#goods_group_template_id").val();
	if (template_id>0){
		showCatalogueGoodGroupTemplateForm(art_id,template_id);
	} else { swal("Помилка", "Оберіть шаблон для редагування", "error"); }
}

function loadCatalogueGoodGroupTemplateParams(art_id){
	let template_id=$("#goods_group_template_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'loadCatalogueGoodGroupTemplateParams', 'art_id':art_id, 'template_id':template_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#articles_params_range").html(result["content"]);
		$("#articles_params_scheme_view_files").html(result["scheme_list"]);
	}}, true);
}

function showCatalogueAnalogIndexSearch(){
	JsHttpRequest.query($rcapi,{ 'w':'showCatalogueAnalogIndexSearch'},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal("show");
        $("#FormModalBody2").html(result["content"]);
        $("#FormModalLabel2").html(result["header"]);
	}}, true);
}

function findCatalogueAnalogIndexSearch(){
	let index=$("#form_analog_search").val();
	if (index.length>1){
		JsHttpRequest.query($rcapi,{ 'w':'findCatalogueAnalogIndexSearch', 'index': index},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#analog_search_result").html(result["content"]);
		}}, true);
	}
}

function setAnalogSearchIndex(art_id,article_nr_displ,brand_id,brand_name){
	$("#form_analog_art_id2").val(art_id);
	$("#form_analog_display_nr").val(article_nr_displ);
	$("#form_analog_brand_id").val(brand_id).trigger("change");
	$("#FormModalWindow2").modal("hide");
}

function showCatalogueAnalogForm(art_id,kind,relation,search_number){
	JsHttpRequest.query($rcapi,{ 'w':'showCatalogueAnalogForm', 'art_id':art_id, 'kind':kind, 'relation':relation, 'search_number':search_number},
	function (result, errors){ if (errors) {alert(errors);} if (result){
		let formWindow=$("#FormModalWindow");
        formWindow.modal("show");
        $("#FormModalBody").html(result["content"]);
        $("#FormModalLabel").html(result["header"]);
		$("#form_analog_brand_id").select2({placeholder: "Виберіть Бренд", dropdownParent: formWindow});
	}}, true);
}

function saveCatalogueAnalogForm(art_id){
    let kind=$("#form_analog_kind").val();
    let relation=$("#form_analog_relation").val();
    let search_number=$("#form_analog_search_number").val();
    let display_nr=$("#form_analog_display_nr").val();
    let brand_id=$("#form_analog_brand_id option:selected").val();
    let art_id2=$("#form_analog_art_id2").val();
	if (display_nr.length>0 && brand_id>0){
		if (art_id2!==""){
			if (relation==="3") {
                saveCatalogueAnalogData(art_id,kind,relation,search_number,display_nr,brand_id,"","");
			} else {
				swal({
					title: "Виконати двостороннє кросування?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
					confirmButtonText: "Так, Двостороннє!", cancelButtonText: "Ні, Одностороннє",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
				},
				function (isConfirm) {
					if (isConfirm) {
						let index2=$("#CatalogueCardArticleNrDispl").html();
						saveCatalogueAnalogData(art_id,kind,relation,search_number,display_nr,brand_id,art_id2,index2);
					} else {
						saveCatalogueAnalogData(art_id,kind,relation,search_number,display_nr,brand_id,"","");
					}
				});
            }
		}
		if (art_id2===""){saveCatalogueAnalogData(art_id,kind,relation,search_number,display_nr,brand_id,"","");}
	} else { swal("Помилка!", "Заповніть коректно форму", "error");}
}
 
function saveCatalogueAnalogData(art_id,kind,relation,search_number,display_nr,brand_id,art_id2,index2){
	JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueAnalogForm','art_id':art_id,'kind':kind,'relation':relation,'search_number':search_number,'display_nr':display_nr,'brand_id':brand_id,'art_id2':art_id2,'index2':index2},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			$("#FormModalWindow").modal("hide");
			loadArticleAnalogs(art_id);
		} else { swal("Помилка!", result["error"], "error");}
	}}, true);
}

function dropCatalogueAnalog(art_id,kind,relation,search_number,brand_id,display_nr){
	swal({
		title: "Видалити аналог "+display_nr+" на індекс "+$("#CatalogueCardArticleNrDispl").html()+"?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
		confirmButtonText: "Так, видалити!", cancelButtonText: "Відміна",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w':'dropCatalogueAnalog','art_id':art_id,'kind':kind,'relation':relation,'search_number':search_number,'brand_id':brand_id,'display_nr':display_nr},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Видалено!", "Аналог "+display_nr+" на індекс "+$("#CatalogueCardArticleNrDispl").html()+" успішно видалено.", "success");
					loadArticleAnalogs(art_id);
				} else { swal("Помилка!", result["error"], "error");}
			}}, true);
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function clearCatalogueAnalogArticle(art_id,kind,relation){
	let ArticleNrDispl=$("#CatalogueCardArticleNrDispl").html();
	let txt="OE Номера";
	if (kind==4 && relation==0){txt="інші номера";}
	if (kind==4 && relation==1){txt="артикул включає в себе";}
	if (kind==4 && relation==2){txt="артикул присутній в";}
	if (kind==4 && relation==3){txt="супутня деталь";}
	swal({
		title: "Очистити аналоги "+ArticleNrDispl+" в частині \""+txt+"\"?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, очистити!", cancelButtonText: "Відміна",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w':'clearCatalogueAnalogArticle','art_id':art_id,'kind':kind,'relation':relation},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Очищено!", "Каталог успішно очищено!", "success");
					loadArticleAnalogs(art_id);
				} else { swal("Помилка!", result["error"], "error");}
			}}, true);
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function showCatNewArticle(){
	JsHttpRequest.query($rcapi,{ 'w':'showCatNewArticle'},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
        $("#FormModalBody").html(result["content"]);
        $("#FormModalLabel").html(result["header"]);
	}}, true);	
}

function setBrandLetter(){
	let vl=$("#new_art_brand_id option:selected").val();
    let vla=vl.split("-");
	$("#art_p1").html(vla[1]);
	return true;
}

function setGoodsGroupKey(){
    let vl=$("#new_art_goods_group_id option:selected").val();
    let vla=vl.split("-");
	$("#art_p2").html(vla[1]);
	JsHttpRequest.query($rcapi,{ 'w':'showGoodsGroupLetterListSelect', 'prnt_id':vla[0]},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#new_art_subgoods_group_id").html(result["content"]);
	}}, true);	
	return true;
}

function setSubGoodsGroupKey(){
    let vl=$("#new_art_subgoods_group_id option:selected").val();
    let vla=vl.split("-");
	$("#art_p3").html(vla[1]);
	JsHttpRequest.query($rcapi,{ 'w':'loadRefinementList', 'subgoods_group_id':vla[0]},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#new_art_refinement_id").html(result["content"]);
	}}, true);	
	return true;
}

function setManufKey(){
    let vl=$("#new_art_manuf_id option:selected").val();
    let vla=vl.split("-");
	$("#art_p4").html(vla[1]);
	return true;
}

function setRefinementKey(){
    let vl=$("#new_art_refinement_id option:selected").val();
	$("#art_p6").html(vl);
	return true;
}

function setNewArtNum(){
    let vl=$("#new_art_next_num").val();
	$("#art_p5").html(vl);
	return true;
}

function findNewArtNextNum(){
	let brand=$("#new_art_brand_id option:selected").val();
    let group=$("#new_art_goods_group_id option:selected").val();
    let sub_group=$("#new_art_subgoods_group_id option:selected").val();
    let manuf=$("#new_art_manuf_id option:selected").val();
	$("#waveSpinner_num_place").html(waveSpinner);
	JsHttpRequest.query($rcapi,{ 'w':'findNewArtNextNum', 'brand':brand, 'group':group, 'sub_group':sub_group, 'manuf':manuf},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
        $("#new_art_next_num").html(result["content"]);
		$("#waveSpinner_num_place").html("");
	}}, true);	
	return true;
}

function findNewArtID(){
    let brand=$("#new_art_brand_id option:selected").val();
	$("#waveSpinner_art_place").html(waveSpinner);
	JsHttpRequest.query($rcapi,{ 'w':'findNewArtID', 'brand':brand},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
        $("#new_art_id").html(result["content"]);
		$("#waveSpinner_art_place").html("");
	}}, true);	
	return true;
}

function checkCatalogueNewArt(){
    let num=$("#art_p1").html()+""+$("#art_p2").html()+""+$("#art_p3").html()+""+$("#art_p4").html()+""+$("#art_p5").html()+""+$("#art_p6").html();
    let art_id=$("#new_art_id").val();
	$("#waveSpinner_art_place").html(waveSpinner);
	JsHttpRequest.query($rcapi,{ 'w':'checkCatalogueNewArt', 'num':num, 'art_id':art_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){
			$("#waveSpinner_art_place").html("");
			$("#saveButtonNewArt").removeAttr("disabled");
		} else {swal("Помилка!", result["error"], "error");$("#saveButtonNewArt").removeAttr("disabled").attr("disabled");}
	}}, true);	
}

function saveCatalogueNewArt(){
    let brand=$("#new_art_brand_id option:selected").val();
	//var group=$("#new_art_goods_group_id option:selected").val();
    let sub_group=$("#new_art_subgoods_group_id option:selected").val();
	//var manuf=$("#new_art_manuf_id option:selected").val();
	let num=$("#art_p1").html()+""+$("#art_p2").html()+""+$("#art_p3").html()+""+$("#art_p4").html()+""+$("#art_p5").html()+""+$("#art_p6").html();
    let art_id=$("#new_art_id").val();
	if (art_id.length>0 && num.length>0){
		swal({
			title: "Створити новий артикул?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
			confirmButtonText: "Так!", cancelButtonText: "Ні",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueNewArt', 'num':num, 'art_id':art_id, 'brand':brand, 'sub_group':sub_group},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){
						$("#FormModalWindow").modal("hide");
						showCatalogueCard(result["art_id"]);
					} else {swal("Помилка!", result["error"], "error");$("#saveButtonNewArt").removeAttr("disabled").attr("disabled");}
				}}, true);	
			} else {swal("Відмінено", "", "error");}
		});
	}
}

function selectFromList2(brand_id,art){
	$("#list2_art").val(""+art);
	$("#list2_brand_id").val(""+brand_id);
	doc_catalogue_article_search(0);
	$("#FormModalWindow2").modal("hide");
}

function doc_catalogue_article_search(search_type){
	var art=$("#catalogue_art").val();
	var brand_id=0;
	if ($("#list2_art").val().length>0){
		art=$("#list2_art").val();$("#list2_art").val("");
		brand_id=$("#list2_brand_id").val();$("#list2_brand_id").val("");
	}
	if (art.length<=2 && view_op==""){ $("#srchInG").addClass("has-error");}
	if (art.length>2 || view_op==1){$("#srchInG").removeClass("has-error");
		$("#waveSpinnerCat_place").html(waveSpinner);
		$("#catalogue_range").empty();
		//alert('doc_catalogue_article_search art='+art+' brand_id='+brand_id+' search_type='+search_type);
		JsHttpRequest.query($rcapi,{ 'w': 'doc_catalogue_article_search', 'art':art, 'brand_id':brand_id, 'search_type':search_type}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["brand_list"]!=="" && result["brand_list"]!=null && search_type==0){
				$("#FormModalWindow2").modal("show");
                $("#FormModalBody2").html(result["brand_list"]);
                $("#FormModalLabel2").html(mess[0]);
			}
			if (result["brand_list"]==="" || result["brand_list"]==null || search_type>0){
				$("#catalogue_range").html(result["content"]);
				$("#waveSpinnerCat_place").html("");
			}
		}}, true);
	}
}

function showArticlePartitionsRestForm(art_id){
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showArticlePartitionsRestForm', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html(result["header"]);
		}}, true);
	}
}

function showArticleStorageCellsRestForm(art_id){
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showArticleStorageCellsRestForm', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html(result["header"]);
		}}, true);
	}
}

function showArticleJDocs(art_id){
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showArticleJDocs', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html(result["header"]);
		}}, true);
	}
}

function showCatalogueDonorForm(art_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCatalogueDonorForm', 'art_id':art_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
        $("#FormModalBody").html(result["content"]);
        $("#FormModalLabel").html(result["header"]);
	}}, true);
}

function showCatalogueDonorIndexSearch(){
	JsHttpRequest.query($rcapi,{ 'w':'showCatalogueDonorIndexSearch'},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal("show");
        $("#FormModalBody2").html(result["content"]);
        $("#FormModalLabel2").html(result["header"]);
	}}, true);
}

function findCatalogueDonorIndexSearch(){
	let index=$("#form_donor_search").val();
	if (index.length>1){
		JsHttpRequest.query($rcapi,{ 'w':'findCatalogueDonorIndexSearch', 'index': index},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#donor_search_result").html(result["content"]);
        }}, true);
	}
}

function setDonorSearchIndex(art_id,article_nr_displ,brand_id,brand_name){
	$("#form_donor_art_id2").val(art_id);
	$("#form_donor_display_nr").val(article_nr_displ);
	$("#form_donor_brand_id").val(brand_id).trigger("change");
	$("#FormModalWindow2").modal("hide");
}

function saveCatalogueDonorForm(art_id){
	let search_number=$("#form_donor_search_number").val();
    let display_nr=$("#form_donor_display_nr").val();
    let art_id2=$("#form_donor_art_id2").val();
	var ch=[];for (var i=1;i<=7;i++){
	var che=0;if (document.getElementById("don_"+i).checked){che=1;}ch[i]=che;}
	if (display_nr.length>0){
		if (art_id2!=""){
			swal({title: "Виконати підключення інформації для індексу "+search_number+" від донора "+display_nr+"?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", confirmButtonText: "Так", cancelButtonText: "Ні",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true},
			function (isConfirm) {
				if (isConfirm) {
					JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueDonorForm', 'art_id':art_id, 'display_nr':display_nr, 'art_id2':art_id2, 'ch':ch},
					function (result, errors){ if (errors) {alert(errors);} if (result){  
						if (result["answer"]==1){
							swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
							$("#FormModalWindow").modal("hide");
							loadArticleAnalogs(art_id);
						} else {swal("Помилка!", result["error"], "error");}
					}}, true);	
				}
			});
		}
	} else { swal("Помилка!", "Заповніть коректно форму", "error");}
}

function viewArticleReservDocs(art_id,storage_id){
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'viewArticleReservDocs', 'art_id':art_id, 'storage_id':storage_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
                $("#FormModalWindow2").modal("show");
                $("#FormModalBody2").html(result["content"]);
                $("#FormModalLabel2").html(result["header"]);
			} else {swal("Помилка!", result["error"], "error");}
		}}, true);	
	}
}

function viewArticleCellsRest(art_id,storage_id){
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'viewArticleCellsRest', 'art_id':art_id, 'storage_id':storage_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
				$("#FormModalWindow2").modal("show");
                $("#FormModalBody2").html(result["content"]);
                $("#FormModalLabel2").html(result["header"]);
			} else {swal("Помилка!", result["error"], "error");}
		}}, true);	
	}
}

function loadArticlePricing(art_id,brand_id){
	if (art_id<=0 || art_id===""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticlePricing', 'art_id':art_id, 'brand_id':brand_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#pricing_place").html(result["content"]);
			$("#catalogue_tabs").tab();
            $(".tooltips").tooltip();
			numberOnlyPlace("price_numbers");
		}}, true);
	}
}

function loadPriceRatingTemplate(art_id){
	if (art_id.length>0){
		let template_id=$("#priceRatingTemplate_"+art_id+" option:selected").val();
		JsHttpRequest.query($rcapi,{ 'w':'loadPriceRatingTemplate', 'template_id':template_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
				$("#artMinMarkUp_"+art_id).val(result["min_markup"]);
				var kol_val=result["kol_val"]; var rating=result["rating"];
				for (var i=1;i<=kol_val;i++){
					$("#artRatingPersent_"+art_id+"_"+i).val(rating[i]);
				}
                recalcPRArt(art_id,"1","0");
			} else {swal("Помилка!", result["error"], "error");}
		}}, true);	
	}
}

function recalcPRArt(art_id,k,st){
	var kol_elm=12; var prev_price=prev_percent=price=percent=oper_price=0; var ii=0; var ch=0;
    oper_price=parseFloat($("#artOperPrice_"+art_id).val());
    console.log("oper_price="+oper_price.toString());

    for (var i=k;i<=kol_elm;i++){  var i0=parseInt(i)-1;
        if (i==1){prev_price=oper_price; prev_percent=percent;}
        if (i>1){prev_price=$("#artRatingPrice_"+art_id+"_"+i0).val(); prev_price=parseFloat(prev_price);}

        price=$("#artRatingPrice_"+art_id+"_"+i).val(); if (price=="" || price=="NaN"){price=prev_price;} price=parseFloat(price);
        percent=$("#artRatingPersent_"+art_id+"_"+i).val(); if (percent==""){percent=0;} percent=parseFloat(percent); //if (price<parseFloat(oper_price)){percent=parseFloat(percent)/100;}
        console.log("i="+i+"; price="+price+"; percent="+percent);

        if (st==0){
            var new_price=(prev_price+(prev_price*percent/100));
            if (ch==0){$("#artRatingPrice_"+art_id+"_"+i).val(new_price);}ch=0;

            ii=parseInt(i)+parseInt(1); ii=parseInt(ii);
            var next_percent=parseFloat($("#artRatingPersent_"+art_id+"_"+ii).val()); if (next_percent==""){next_percent=0;}
            console.log("ii="+ii+"; next_percent="+next_percent);
            var next_price=(parseFloat(new_price)+(parseFloat(new_price)*parseFloat(next_percent)/100));
            console.log("next_price="+next_price);
            $("#artRatingPrice_"+art_id+"_"+ii).val(next_price.toString());
            prev_price=next_price;
        }
        if (st==1){
            var new_percent=((parseFloat(price)/parseFloat(prev_price)-1)*100).toFixed(2);
            // if (price<parseFloat(oper_price)){new_percent=(parseFloat(new_percent)/100).toFixed(2);}

            console.log("-----------------");
            console.log("dlya price="+price+"; prev_price="+prev_price+"; new_percent="+new_percent);

            $("#artRatingPersent_"+art_id+"_"+(i)).val(new_percent);
            i=parseInt(i)-1; st=0; ch=1;
            //var new_price=(parseFloat(prev_price)+(parseFloat(prev_price)*parseFloat(percent)/100)).toFixed(2);
            //$("#artRatingPrice_"+art_id+"_"+(i)).val(new_price);
        }
    }
}

// function recalcPRArt(art_id,k,st){
// 	var kol_elm=12; var prev_price=prev_percent=price=percent=oper_price=0; var ii=0; var ch=0;
// 	oper_price=parseFloat($("#artOperPrice_"+art_id).val());
// 	console.log("oper_price="+oper_price.toString());
// 	for (var i=k;i<=kol_elm;i++){
// 		var i0=parseInt(i)-1;
// 		if (i==1){prev_price=oper_price; prev_percent=percent;}
// 		if (i>1){prev_price=$("#artRatingPrice_"+art_id+"_"+i0).val(); prev_price=parseFloat(prev_price);}
//
// 		price=$("#artRatingPrice_"+art_id+"_"+i).val(); if (price=="" || price=="NaN"){price=prev_price;} price=parseFloat(price);
// 		percent=$("#artRatingPersent_"+art_id+"_"+i).val(); if (percent==""){percent=0;}percent=parseFloat(percent);
// 		console.log("i="+i+"; price="+price+"; percent="+percent);
// 		console.log("prev_price="+prev_price+"; prev_percent="+prev_percent);
// 		console.log("----------------");
//
// 		if (st==0){
// 			var new_price=(prev_price+(prev_price*percent/100));
// 			if (ch==0){$("#artRatingPrice_"+art_id+"_"+i).val(new_price);}ch=0;
//
// 			ii=parseInt(i)+parseInt(1); ii=parseInt(ii);
// 			var next_percent=parseFloat($("#artRatingPersent_"+art_id+"_"+ii).val()); if (next_percent==""){next_percent=0;}
// 			console.log("ii="+ii+"; next_percent="+next_percent);
// 			var next_price=(parseFloat(new_price)+(parseFloat(new_price)*parseFloat(next_percent)/100));
// 			console.log("next_price="+next_price);
// 			$("#artRatingPrice_"+art_id+"_"+ii).val(next_price.toString());
// 			prev_price=next_price;
// 		}
// 		if (st==1){
// 			var new_percent=((parseFloat(price)/parseFloat(prev_price)-1)*100).toFixed(2);
// 			$("#artRatingPersent_"+art_id+"_"+(i)).val(new_percent);
// 			i=parseInt(i)-1; st=0; ch=1;
// 			//var new_price=(parseFloat(prev_price)+(parseFloat(prev_price)*parseFloat(percent)/100)).toFixed(2);
// 			//$("#artRatingPrice_"+art_id+"_"+(i)).val(new_price);
// 		}
// 	}
// }

function saveArticlePriceRating(art_id){
	var kol_elm=12; var prc=[]; var prs=[];
	if (art_id.length>0){
		var minMarkup=$("#artMinMarkUp_"+art_id).val();var er=0;
		var template_id=$("#priceRatingTemplate_"+art_id+" option:selected").val();
		var cash_id=$("#priceRatingCash_"+art_id+" option:selected").val();
		for (var i=0;i<=kol_elm;i++){
			var i0=parseInt(i)-1;
			price=$("#artRatingPrice_"+art_id+"_"+i).val(); if (price=="" || price=="NaN"){price=prev_price;} prc[i]=price;
			percent=$("#artRatingPersent_"+art_id+"_"+i).val();
			if (percent==""){percent=0;} prs[i]=percent;
			if (i>1 && percent<0){er=1;}
		}
		if (cash_id==="0" || cash_id===undefined) er=1;
		if (er==1){ swal("Помилка!","Збереження відмінено","error");}
		if (er==0){
			JsHttpRequest.query($rcapi,{ 'w':'saveArticlePriceRating', 'art_id':art_id, 'kol_elm':kol_elm, 'template_id':template_id, 'minMarkup':minMarkup, 'cash_id':cash_id, 'prc':prc, 'prs':prs},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					swal("Збережено!", "", "success");
					$("#artAuthorName_"+art_id).html(result["user_name"]);
					$("#artDataUpdate_"+art_id).html(result["date"]);
                    loadArticlePricing(art_id,$("#brand_id").val());
				} else {swal("Помилка!", result["error"], "error");}
			}}, true);	
		}
	}
}

function showArticlePriceRatingHistory(art_id){
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showArticlePriceRatingHistory', 'art_id':art_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
				$("#FormModalWindow3").modal("show");
				$("#FormModalBody3").html(result["content"]);
				$("#FormModalLabel3").html(result["header"]);
			} else {swal("Помилка!", result["error"], "error");}
		}}, true);	
	}
}

function showArticleSales(art_id){
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showArticleSales', 'art_id':art_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
				$("#FormModalWindow3").modal("show");
                $("#FormModalBody3").html(result["content"]);
                $("#FormModalLabel3").html(result["header"]);
			} else {swal("Помилка!", result["error"], "error");}
		}}, true);	
	}
}

function changeArtId() {
	let suppl_status=$("#suppl_status").prop("checked");
	if (!suppl_status) {
		JsHttpRequest.query($rcapi,{ 'w':'getMaxIndex'},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#art_id").val(result.content);
			toastr["info"]("Вибрано індекс");
		}}, true);	
	} else {
		JsHttpRequest.query($rcapi,{ 'w':'getMaxSupplIndex'},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#art_id").val(result.content);
			toastr["info"]("Вибрано індекс постачальника");
		}}, true);	
	}
}

function saveIndexArticle() {
	let art_id=$("#art_id").val();
    let article_nr_displ=$("#article_nr_displ").val();
    let brand_id=$("#brand_id option:selected").val();
    let article_name=$("#article_name").val();
    let article_name_ukr=$("#article_name_ukr").val();
    let article_info=$("#article_info").val();
	if (art_id>0 && art_id!=="") {
		JsHttpRequest.query($rcapi,{ 'w':'saveIndexArticle', 'art_id':art_id, 'article_nr_displ':article_nr_displ, 'brand_id':brand_id, 'article_name':article_name, 'article_name_ukr':article_name_ukr, 'article_info':article_info},
		function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"]==1){
                changeArtId();
                toastr["info"]("Артикул успішно додано!");
                $("#article_nr_displ").val("");
                $("#article_name").val("");
                $("#article_name_ukr").val("");
                $("#article_info").val("");
            } else {swal("Помилка!", result["error"], "error");}
		}}, true);	
	}
}

function showArticleLogs(art_id) {
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showArticleLogs', 'art_id':art_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow3").modal("show");
            $("#FormModalBody3").html(result["content"]);
            $("#FormModalLabel3").html("Історія змін "+art_id);
		}}, true);	
	}
}

/*==== T2_INFO ====*/

function addArticleInfo(art_id) {
    let text=$("#info_text").val();
    let value=$("#info_value").val();
    let sort=$("#info_sort").val();
    let lang_id=$("#info_lang option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w':'addArticleInfo', 'art_id':art_id, 'lang_id':lang_id, 'text':text, 'value':value, 'sort':sort},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"]==1){
                swal("Додано!", "Внесені Вами зміни успішно збережені.", "success");
				loadArticleInfo($("#art_id").val());
            } else { swal("Помилка!", result["error"], "error");}
        }}, true);
}

function loadArticleInfo(art_id) {
    if (art_id.length>0){
        JsHttpRequest.query($rcapi,{ 'w':'loadArticleInfo', 'art_id':art_id},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                $("#index_article_info").html(result.content);
            }}, true);
    }
}

function saveArticleInfo(id) {
	let art_id=$("#art_id").val();
	let text=$("#info_text-"+id).val();
    let value=$("#info_value-"+id).val();
    let sort=$("#info_sort-"+id).val();
	JsHttpRequest.query($rcapi,{ 'w':'saveArticleInfo', 'id':id, 'text':text, 'value':value, 'sort':sort},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			loadArticleInfo(art_id);
		} else { swal("Помилка!", result["error"], "error");}
	}}, true);
}

function dropArticleInfo(id) {
    let art_id=$("#art_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'dropArticleInfo', 'id':id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){
			swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
			loadArticleInfo(art_id);
		} else { swal("Помилка!", result["error"], "error");}
	}}, true);
}

/*==== /T2_INFO ====*/

/*==== T2_SHORT_INFO ====*/

function addArticleShortInfo(art_id) {
    let text=$("#short_info_text").val();
    let value=$("#short_info_value").val();
    let sort=$("#short_info_sort").val();
    let lang_id=$("#short_info_lang option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w':'addArticleShortInfo', 'art_id':art_id, 'lang_id':lang_id, 'text':text, 'value':value, 'sort':sort},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"]==1){
                swal("Додано!", "Внесені Вами зміни успішно збережені.", "success");
                loadArticleShortInfo($("#art_id").val());
            } else { swal("Помилка!", result["error"], "error");}
        }}, true);
}

function loadArticleShortInfo(art_id) {
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'loadArticleShortInfo', 'art_id':art_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#index_article_short_info").html(result.content);
		}}, true);	
	}
}

function saveArticleShortInfo(id) {
    let art_id=$("#art_id").val();
    let text=$("#short_info_text-"+id).val();
    let value=$("#short_info_value-"+id).val();
    let sort=$("#short_info_sort-"+id).val();
	JsHttpRequest.query($rcapi,{ 'w':'saveArticleShortInfo', 'id':id, 'text':text, 'value':value, 'sort':sort},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			loadArticleShortInfo(art_id);
		} else { swal("Помилка!", result["error"], "error");}
	}}, true);
}

function dropArticleShortInfo(id) {
    let art_id=$("#art_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'dropArticleShortInfo', 'id':id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){
			swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
			loadArticleShortInfo(art_id);
		} else { swal("Помилка!", result["error"], "error");}
	}}, true);
}

/*==== /T2_SHORT_INFO ====*/

function showArticleCross(art_id) {
	JsHttpRequest.query($rcapi,{ 'w':'showArticleCross', 'art_id':art_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
		$("#FormModalBody").html(result.content);
		$("#FormModalLabel").html("Наявність по кросам");
	}}, true);
}

function saveArticleCross() {
    let cross=$("#cross_value").val();
    let new_cross=$("#new_cross_value").val();
	JsHttpRequest.query($rcapi,{ 'w':'saveArticleCross', 'cross':cross, 'new_cross':new_cross},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
		} else { swal("Помилка!", result["error"], "error");}
	}}, true);
}

function ClearFil2Search() {
	$("#fil4SupplId").empty().trigger("change");
	$("#fil4BrandId").empty().trigger("change");
	$("#fil4GoodsGroupId").empty().trigger("change");
	$("#fil4Top").val("");
	$("#fil4StokTo").val("");
	$("#fil4StokFrom").val("");
}

function ClearFil4Search() {
    $("#fil2Manufacture").val("0").trigger("change");
    $("#fil2Model").val("0").trigger("change");
    $("#fil2Modification").val("0").trigger("change");
}

function loadArticleCatalogue(art_id) {
    JsHttpRequest.query($rcapi,{ 'w':'loadArticleCatalogue', 'art_id':art_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#index_article_catalogue").html(result.content);
        }}, true);
}

/*==== TEMPLATES PARAMS VALUES ====*/

function loadTemplateList(art_id) {
	let template_id=$("#catalogue_template_id option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w':'loadTemplateList', 'art_id':art_id, 'template_id':template_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let dt=$("#datatable");
            if (dt.length) dt.DataTable().destroy();
            $("#articles_params_range").html(result.content);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
        }}, true);
}

function showCatalogueTemplateForm(template_id) {
	if (template_id==="0") template_id=0; else template_id=$("#catalogue_template_id option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCatalogueTemplateForm', 'template_id':template_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#FormModalWindow").modal("show");
			$("#FormModalBody").html(result.content);
            if (template_id==="0") $("#FormModalLabel").html("Додати шаблон"); else $("#FormModalLabel").html("Редагувати шаблон");
            let elem1 = document.querySelector("#child_status");if (elem1){ var dflt = new Switchery(elem1, { color: '#1AB394' });}
            let elem2 = document.querySelector("#template_status");if (elem2){ var dflt = new Switchery(elem2, { color: '#1AB394' });}
		}}, true);
}

function saveCatalogueTemplateForm(template_id) {
	let template_name=$("#template_name").val();
	let parent_id=$("#parent_id option:selected").val();
    let child_status=0; if (document.getElementById("child_status").checked) {child_status=1;}
    let template_status=0; if (document.getElementById("template_status").checked) {template_status=1;}
    JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueTemplateForm', 'template_id':template_id, 'template_name':template_name, 'child_status':child_status, 'parent_id':parent_id, 'template_status':template_status},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"]==1){
                $("#FormModalWindow").modal("hide");
                swal("Збережено!", result["error"], "success");
                loadArticleCatalogue($("#art_id").val());
            } else { swal("Помилка!", result["error"], "error");}
        }}, true);
}

function dropCatalogueTemplate(template_id) {
    if (template_id<=0 || template_id===""){toastr["error"](errs[0]);}
    if (template_id>0){
        if(confirm("Видалити запис?")){
            JsHttpRequest.query($rcapi,{ 'w': 'dropCatalogueTemplate', 'template_id':template_id},
                function (result, errors){ if (errors) {alert(errors);} if (result){
                    if (result["answer"]==1){
                        $("#FormModalWindow").modal("hide");
                        toastr["info"]("Запис успішно видалено");
                        loadArticleCatalogue($("#art_id").val());
                    } else { toastr["error"](result["error"]); }
                }}, true);
        }
    }
}

function dropCatalogueTemplateArticle() {
    let template_id=$("#catalogue_template_id option:selected").val();
	let art_id = $("#art_id").val();
    if (template_id<=0 || template_id===""){toastr["error"](errs[0]);}
    if (template_id>0){
        if(confirm("Відвязати шаблон?")){
            JsHttpRequest.query($rcapi,{ 'w': 'dropCatalogueTemplateArticle', 'template_id':template_id, 'art_id':art_id},
                function (result, errors){ if (errors) {alert(errors);} if (result){
                    if (result["answer"]==1){
                        $("#FormModalWindow").modal("hide");
                        toastr["info"]("Запис успішно видалено");
                        loadArticleCatalogue($("#art_id").val());
                    } else { toastr["error"](result["error"]); }
                }}, true);
        }
    }
}

/*==== /TEMPLATES PARAMS VALUES ====*/

/*==== TEMPLATES PARAMS VALUES ====*/

function showCatalogueTemplateParamsForm(param_id) {
	let template_id=$("#catalogue_template_id option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w':'showCatalogueTemplateParamsForm', 'param_id':param_id, 'template_id':template_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result.content);
        }}, true);
}

function saveCatalogueTemplateParamsForm(param_id) {
    let template_id=$("#param_template_id option:selected").val();
    let param_name=$("#param_name").val();
    JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueTemplateParamsForm', 'param_id':param_id, 'template_id':template_id, 'param_name':param_name},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"]==1){
                $("#FormModalWindow").modal("hide");
                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                loadArticleCatalogue($("#art_id").val());
            } else { swal("Помилка!", result["error"], "error");}
        }}, true);
}

function dropCatalogueTemplateParams(param_id) {
    if (param_id<=0 || param_id===""){toastr["error"](errs[0]);}
    if (param_id>0){
        if(confirm('Видалити запис?')){
            JsHttpRequest.query($rcapi,{ 'w': 'dropCatalogueTemplateParams', 'param_id':param_id},
                function (result, errors){ if (errors) {alert(errors);} if (result){
                    if (result["answer"]==1){
                        $("#FormModalWindow").modal("hide");
                        toastr["info"]("Запис успішно видалено");
                        loadArticleCatalogue($("#art_id").val());
                    } else { toastr["error"](result["error"]); }
                }}, true);
        }
    }
}

/*==== /TEMPLATES PARAMS VALUES ====*/

/*==== TEMPLATES PARAMS VALUES ====*/

function showCatalogueTemplateParamsValueForm(template_id) {
    JsHttpRequest.query($rcapi,{ 'w':'showCatalogueTemplateParamsValueForm', 'template_id':template_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result.content);
        }}, true);
}

function showCatalogueParamValueForm(param_id) {
	let template_id=$("#catalogue_template_id option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w':'showCatalogueParamValueForm', 'template_id':template_id, 'param_id':param_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result.content);
        }}, true);
}

function saveCatalogueParamValueForm() {
    let art_id=$("#art_id").val();
    let template_id=$("#param_template_id option:selected").val();
    let param_id=$("#param_param_id option:selected").val();
    let value_id=$("#param_value_id option:selected").val();
    if (template_id==0 || template_id==undefined || param_id==0 || param_id==undefined || value_id==0 || value_id==undefined) {toastr["error"]("Виберіть всі параметри!");}
    else {
		JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueParamValueForm', 'art_id':art_id, 'template_id':template_id, 'param_id':param_id,'value_id':value_id},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				if (result["answer"]==1){
					$("#FormModalWindow").modal("hide");
                    swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                    loadArticleCatalogue($("#art_id").val());
				} else { swal("Помилка!", result["error"], "error");}
			}}, true);
	}
}

function saveCatalogueTemplateParamsValue(id) {
	let value_id=$("#catalogue_params_id_"+id).val();
	JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueTemplateParamsValue', 'id':id, 'value_id':value_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"]==1){
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                loadArticleCatalogue($("#art_id").val());
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
}

function saveCatalogueTemplateParamsValueForm() {
    let template_id=$("#param_template_id option:selected").val();
    let param_id=$("#param_param_id option:selected").val();
    let param_value=$("#param_value").val();
    JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueTemplateParamsValueForm', 'template_id':template_id, 'param_id':param_id, 'param_value':param_value},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"]==1){
                $("#FormModalWindow").modal("hide");
                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                loadArticleCatalogue($("#art_id").val());
            } else { swal("Помилка!", result["error"], "error");}
        }}, true);
}

function dropCatalogueTemplateParamsValue(id) {
    if (id<=0 || id===""){toastr["error"](errs[0]);}
    if (id>0){
        if(confirm("Видалити запис?")){
            JsHttpRequest.query($rcapi,{ 'w': 'dropCatalogueTemplateParamsValue', 'id':id},
                function (result, errors){ if (errors) {alert(errors);} if (result){
                    if (result["answer"]==1){
                        $("#FormModalWindow").modal("hide");
                        toastr["info"]("Запис успішно видалено");
                        loadArticleCatalogue($("#art_id").val());
                    } else { toastr["error"](result["error"]); }
                }}, true);
        }
    }
}

function getCatalogueParamsList() {
	let template_id=$("#param_template_id option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w':'getCatalogueParamsList', 'template_id':template_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#param_param_id").html(result.content);
        }}, true);
}

function getCatalogueValuesList() {
    let template_id=$("#param_template_id option:selected").val();
    let param_id=$("#param_param_id option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w':'getCatalogueValuesList', 'template_id':template_id, 'param_id':param_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#param_value_id").html(result.content);
        }}, true);
}

/*==== /TEMPLATES PARAMS VALUES ====*/

function generateBarcode() {
    JsHttpRequest.query($rcapi,{ 'w':'generateBarcode'},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#barcode").val(result.content);
        }}, true);
}

function saveBarcode() {
	let art_id=$("#art_id").val();
	let barcode=$("#barcode").val();
    JsHttpRequest.query($rcapi,{ 'w':'saveBarcode', 'art_id':art_id, 'barcode':barcode},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"]==1){
                swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
            } else { swal("Помилка!", result["error"], "error");}
        }}, true);
}

function showReportSales() {
    let tpoint=$("#tpoint_select option:selected").val();
    let date_start=$("#date_start").val();
    JsHttpRequest.query($rcapi,{ 'w': 'showReportSales', 'date_start':date_start, 'tpoint':tpoint},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            let dt = $("#datatable");
            dt.DataTable().destroy();
            $("#report_sales_range").html(result["content"]);
            dt.DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
        }}, true);
}