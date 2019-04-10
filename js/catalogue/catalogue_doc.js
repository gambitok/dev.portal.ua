var errs=[]; var mess=[];
errs[0]="Помилка карти індексу";
errs[1]="Занадто короткий запит для пошуку";
mess[0]="Уточніть пошук";

function catalogue_article_search(view_op,search_type){
	var art=$("#catalogue_art").val();
	var brand_id=$("#search_brand_id").val();
	var doc_type=$("#search_doc_type").val();
	var doc_id=$("#search_doc_id").val();
	if (art.length<=2 && view_op==""){ $("#srchInG").addClass("has-error");/*	toastr["warning"](errs[1]);*/}
	if (art.length>2 || view_op==1){$("#srchInG").removeClass("has-error");
		$("#waveSpinner_place").html(waveSpinner);
		$("#catalogue_range").empty();
		$("#catalogue_header").empty();
		JsHttpRequest.query($rcapi,{ 'w': 'catalogue_article_search_doc', 'art':art, 'brand_id':brand_id, 'search_type':search_type,'doc_type':doc_type,'doc_id':doc_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			//var table = $('#datatable').DataTable();
			//table.destroy();
			if (result["brand_list"]!="" && result["brand_list"]!=null && search_type==0){
				$("#FormModalWindow").modal('show');
				document.getElementById("FormModalBody").innerHTML=result["brand_list"];
				document.getElementById("FormModalLabel").innerHTML=mess[0]+": оберіть виробника";
			}
			if (result["brand_list"]=="" || result["brand_list"]==null || search_type>0){
				$("#catalogue_range").html(result["content"]);
				$("#catalogue_header").html(result["header"]);
				$("#waveSpinner_place").html("");
				$("#search_brand_id").val("");
			}
		}}, true);
	}
}

function setArticleSearchBrand(art,brand_id){
	$("#search_brand_id").val(""+brand_id);
	$("#catalogue_art").val(""+art);
	catalogue_article_search(art,0);
	$("#FormModalWindow").modal('hide');document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";
}

function showSupplStorageSelectWindow(art_id,article_nr_displ,brand_id,brand_name,doc_type,doc_id){
	JsHttpRequest.query($rcapi,{ 'w': 'showSupplStorageSelectWindow','art_id':art_id,'article_nr_displ':article_nr_displ,'doc_type':doc_type,'doc_id':doc_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
		document.getElementById("FormModalBody2").innerHTML=result["content"];
		document.getElementById("FormModalLabel2").innerHTML="Вкажіть кількість: "+article_nr_displ+" "+brand_name;
		$('#art_idS2').val(art_id);
		$('#article_nr_displS2').val(article_nr_displ);
		$('#brand_idS2').val(brand_id);
		$('#brand_nameS2').val(brand_name);
		setTimeout(function (){$('#amount_select_storage_str').DataTable({keys: true,"aaSorting": [],"order": [[ 3, "asc" ]],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": false,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});},500);
	}}, true);
}

var barcode_settings = {barWidth: 1,barHeight: 50,moduleSize: 5,showHRI: true,addQuietZone: true,marginHRI: 5,bgColor: "#FFFFFF",color: "#000000",fontSize: 14,output: "css",posX: 0,posY: 0};

var chosen_config = {
	'.chosen-select'           : {},
	'.chosen-select-deselect'  : {allow_single_deselect:true},
	'.chosen-select-no-single' : {disable_search_threshold:10},
	'.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
	'.chosen-select-width'     : {width:"95%"}
}

for (var selector in chosen_config) {
	$(selector).chosen(chosen_config[selector]);
}

function checkBrandSelectList(){
	var list_brand_select=$("#list_brand_select").html();
	if (list_brand_select.length>0){
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=list_brand_select;
		document.getElementById("FormModalLabel").innerHTML=mess[0];
//		$("#list_brand_select").html("");
	}
}

function fil4Search(){
	var suppl_id=$('#fil4SupplId').val();
	var brand_id=$('#fil4BrandId').val();	
	var goods_group_id=$('#fil4GoodsGroupId').val();
	var top=$('#fil4Top').val();
	var stok_to=$('#fil4StokTo').val();
	var stok_from=$('#fil4StokFrom').val();
	$("#waveSpinner_place").html(waveSpinner);
	$("#catalogue_range").empty();
	$("#catalogue_header").empty();
	JsHttpRequest.query($rcapi,{ 'w': 'catalogue_fil4_search', 'suppl_id':suppl_id, 'brand_id':brand_id, 'goods_group_id':goods_group_id, 'top':top, 'stok_to':stok_to, 'stok_from':stok_from}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		//var table = $('#datatable').DataTable();
		//table.destroy();
		$("#catalogue_range").html(result["content"]);
		$("#catalogue_header").html(result["header"]);
		$("#waveSpinner_place").html("");
		//$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": false,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
		
	}}, true);
}

function fil2Search(){
	var mfa_id=$('#fil2Manufacture').val();
	var mod_id=$('#fil2Model').val();	
	var typ_id=$('#fil2Modification').val();
	var str_id=$('#fil2StrId').val();
	var art_ids=$('#fil2ArtIds').val();
	if (str_id>0){
		$("#waveSpinner_place").html(waveSpinner);
		$("#catalogue_range").empty();
		$("#catalogue_header").empty();
		JsHttpRequest.query($rcapi,{ 'w': 'catalogue_fil2_search', 'mfa_id':mfa_id, 'mod_id':mod_id, 'typ_id':typ_id, 'str_id':str_id,'art_ids':art_ids}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			//var table = $('#datatable').DataTable();
			//table.destroy();
			$("#catalogue_range").html(result["content"]);
			$("#catalogue_header").html(result["header"]);
			//$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": false,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
			$("#waveSpinner_place").html("");
		}}, true);
	}else{swal("Помилка!", "Оберіть категорію запчастин", "error"); }
}

function loadFilterModelSelectList(){
	var mfa_id=$("#fil2Manufacture option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadModelSelectList', 'mfa_id':mfa_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("fil2Model").innerHTML=result["content"];
			$("#fil2Model").select2({placeholder: "Вибрати модель"});
		}}, true);
}

function loadFilterModificationSelectList(){
	var mod_id=$("#fil2Model option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'loadFilterModificationSelectList', 'mod_id':mod_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("fil2Modification").innerHTML=result["content"];
			$("#fil2Modification").select2({placeholder: "Вибрати модифікацію"});
		}}, true);
}

function loadFilterGroupTreeListSide(){
	var mfa_id=$("#fil2Manufacture option:selected").val();
	var mod_id=$("#fil2Model option:selected").val();
	var typ_id=$("#fil2Modification option:selected").val();
	if (typ_id>0){
	$("#waveSpinner_place").html(waveSpinner);
	JsHttpRequest.query($rcapi,{ 'w': 'loadFilterGroupTreeListSide', 'mfa_id':mfa_id, 'mod_id':mod_id, 'typ_id':typ_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			document.getElementById("filter_range_place").innerHTML=result["content"];
			document.getElementById("catalogue_side_tree_place").innerHTML=result["tree"];
			document.getElementById("catalogue_result_table_place").innerHTML=result["result_table"];
			var tree = new treefilter($("#tree1"), {
				searcher : $("input#my-search"), offsetLeft :20,
				multiselect : true,expanded : true,
			});
			/*
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML=result["header"];
			$("#waveSpinner_place").html("");
			*/
		}}, true);
	}else{swal("Помилка!", "Оберіть модифікацію", "error"); }
}

function loadFilterGroupTreeList(){
	var mfa_id=$("#fil2Manufacture option:selected").val();
	var mod_id=$("#fil2Model option:selected").val();
	var typ_id=$("#fil2Modification option:selected").val();
	if (typ_id>0){
	$("#waveSpinner_place").html(waveSpinner);
	JsHttpRequest.query($rcapi,{ 'w': 'loadFilterGroupTreeList', 'mfa_id':mfa_id, 'mod_id':mod_id, 'typ_id':typ_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML=result["header"];
			var tree = new treefilter($("#tree1"), {
				searcher : $("input#my-search"), offsetLeft :20,
				multiselect : true,expanded : true,
			});
			$("#waveSpinner_place").html("");
		}}, true);
	}else{swal("Помилка!", "Оберіть модифікацію", "error"); }
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

/*
function setFil2StrInfo(id,name,art_ids){
	$("#fil2StrId").val(id);
	$("#fil2StrText").val(name);
	$("#fil2ArtIds").val(art_ids);
	$("#FormModalWindow").modal('hide');
}*/

function showCatFieldsViewForm(){
	JsHttpRequest.query($rcapi,{ 'w':'showCatFieldsViewDocForm'},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML=result["header"];
		$('.table-sortable tbody').sortable({
			handle: 'span',
			stop: function(event, ui) {
			},
			update: function (event, ui) {
				var data = $(this).sortable('serialize').toString();
			}
		});
	}}, true);
}

function saveCatalogueFieldsViewForm(){
	var table_key=$('#table_key').val();
	var data = $('.table-sortable tbody').sortable('toArray');
	var kol_fields=$("#kol_fields").val();
	var fl_id=[]; var fl_ch=[];
	for (var i=1;i<=kol_fields;i++){
		var field_id=data[i-1].split("_")[1];
		fl_id[i]=field_id;
		var ch=0; if (document.getElementById("use_"+field_id).checked){ch=1;}
		fl_ch[i]=ch;
	}
	if (kol_fields>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueFieldsViewForm','kol_fields':kol_fields,'fl_id':fl_id,'fl_ch':fl_ch,'table_key':table_key},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
				$("#FormModalWindow").modal('hide');
				catalogue_article_search(0,0);
			}
			else{ swal("Помилка!", result["error"], "error"); }
		}}, true);
	}
}

function showCatalogueCard(art_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showCatalogueCard', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#CatalogueCard").modal('show');
			document.getElementById("CatalogueCardBody").innerHTML=result["content"];
			document.getElementById("CatalogueCardArticleNrDispl").innerHTML=result["nr_display"];
			$('#catalogue_tabs').tab();
			$("#barcode-visual").barcode(""+$("#barcode").val(),"code39",barcode_settings);
			$("#article_info").markdown({autofocus:false,savable:false})
			$("#brand_id").select2({placeholder: "Виберіть Бренд",dropdownParent: $("#CatalogueCard")});
		}}, true);
	}
}

function loadArticleParams(art_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleParams', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("params_place").innerHTML=result["content"];
			$('#catalogue_tabs').tab();
			$("#units").select2({placeholder: "Одиниці виміру",dropdownParent: $("#CatalogueCard")});
		}}, true);
	}
}

function loadArticleLogistic(art_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleLogistic', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("logistic_place").innerHTML=result["content"];
			$('#catalogue_tabs').tab();
//			$("#units").select2({placeholder: "Одиниці виміру",dropdownParent: $("#CatalogueCard")});
		}}, true);
	}
}

function preconfirmCatalogueGeneralInfo(){
	swal({
		title: "Зберегти зміни у розділі \"Загальна інформація\"?",text: "",	type: "warning",allowOutsideClick:true,allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
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
	var art_id=$("#art_id").val();
	var article_nr_displ=$("#article_nr_displ").val();
	var barcode=$("#barcode").val();
	var inner_cross=$("#inner_cross").val();
	var brand_id=$("#brand_id").val();
	var goods_group_id=$("#goods_group_id").val();
	var article_name=$("#article_name").val();
	var article_info=$("#article_info").val();
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueGeneralInfo','art_id':art_id,'article_nr_displ':article_nr_displ,'barcode':barcode,'inner_cross':inner_cross,'brand_id':brand_id,'goods_group_id':goods_group_id,'article_name':article_name, 'article_info':article_info},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
				var art=$("#catalogue_art").val();
				if (art.length>0){
					//catalogue_article_search("",0);
				}
			}
			else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function saveCatalogueParams(art_id){
	var mes="Зберегти зміни у розділі \"Характеристики\"?";
	var template_id=$("#goods_group_template_id option:selected").val();
	if (template_id==0){
		mes="Ви дійсно хочете відвязати від шаблону артикул \""+$("#CatalogueCardArticleNrDispl").html()+"\" та видалити всі параметри, які були введені раніше?";
	}
	swal({
		title: mes,text: "",type: "warning",allowOutsideClick:true,allowEscapeKey:true,showCancelButton: true,confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!",cancelButtonText: "Відмінити!",closeOnConfirm: false,closeOnCancel: false,showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var goods_group_id=$("#goods_group_id").val();
			var cn=$("#params_kol").val();
			var fields_type=[];var params_value=[];
			for (var i=1;i<=cn;i++){
				param_id=$("#paramId_"+i).val();
				fields_type[param_id]=$("#param_field_type_"+param_id+" option:selected").val();
				params_value[param_id]=$("#param_value_"+param_id).val();
			}
			if (art_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueParams','art_id':art_id,'goods_group_id':goods_group_id,'template_id':template_id,'fields_type':fields_type,'params_value':params_value},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
					}
					else{ swal("Помилка!", result["error"], "error");}
					
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
			var goods_group_id=$("#tmp_goods_group_id").val();
			var template_id=$("#tmp_template_id").val();
			var template_name=$("#tmp_template_name").val();
			var cn=$("#tmp_params_kol").val();
			var params_id=[];var fields_type=[];var params_name=[];
			for (var i=1;i<=cn;i++){
				params_id[i]=$("#tmp_param_id_"+i).val();
				fields_type[i]=$("#tmp_param_field_type_"+i+" option:selected").val();
				params_name[i]=$("#tmp_param_name_"+i).val();
			}
			if (art_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueParamsTemplate','art_id':art_id,'goods_group_id':goods_group_id,'template_id':template_id,'template_name':template_name,'cn':cn,'params_id':params_id,'fields_type':fields_type,'params_name':params_name},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#FormModalWindow").modal('hide');
						loadArticleParams(art_id);
					}
					else{ swal("Помилка!", result["error"], "error");}
					
				}}, true);
			}
		} else {swal("Відмінено", "Внесені Вами зміни анульовано.", "error");}
	});
}

function unlinkCatalogueGoodGroup(art_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		var goods_group_id=$("#goods_group_id").val();
		JsHttpRequest.query($rcapi,{ 'w': 'unlinkCatalogueGoodGroup', 'art_id':art_id, 'goods_group_id':goods_group_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
				$("#goods_group_id").val("");
				$("#goods_group_name").val("");
				swal("Виконано!", "Товарну групу відвязано", "success");
			}else {swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function showGoodGroupTree(art_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		var goods_group_id=$("#goods_group_id").val();
		JsHttpRequest.query($rcapi,{ 'w': 'showGoodGroupTree', 'art_id':art_id, 'goods_group_id':goods_group_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#GoodsGroupModalWindow").modal('show');
			document.getElementById("GoodsGroupBody").innerHTML=result["content"];
			$('#jsGoodGroupTree') .on('changed.jstree', function (e, data) {
				var i, j, r = [];
				for(i = 0, j = data.selected.length; i < j; i++) {
				  r.push(data.instance.get_node(data.selected[i]).text);
				}
				$('#jsGoodGroupTree_result').val('' + r.join(', '));
				var i, j, r = [];
				for(i = 0, j = data.selected.length; i < j; i++) {
				  r.push(data.instance.get_node(data.selected[i]).id);
				}
				$('#jsGoodGroupTree_id').val(''+r.join(', '));
				
		    }).jstree({
				'core' : {'check_callback' : true},
				'plugins' : [ 'types', 'dnd' ],
				'types' : {'default' : {'icon' : 'fa fa-folder'},}
        	});
		}}, true);
	}
}

function setGoodGroupSelect(){
	var id=$('#jsGoodGroupTree_id').val();
	var name=$('#jsGoodGroupTree_result').val();
	$('#goods_group_id').val(id);
	$('#goods_group_name').val(name);
	$("#GoodsGroupModalWindow").modal('hide');
}

function generateBarcodeIncart(){
	$("#barcode-visual").barcode(""+$("#barcode").val(),"code39",barcode_settings);
}

function loadArticleCommets(art_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleCommets', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("article_commets_place").innerHTML=result["content"];
		}}, true);
	}
}

function saveArticleComment(art_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		var comment=$("#article_comment_field").val();
		if (comment.length<=0){toastr["error"]("Напишіть коментар спочатку");}
		if (comment.length>0){
			JsHttpRequest.query($rcapi,{ 'w': 'saveArticleComment', 'art_id':art_id, 'comment':comment}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadArticleCommets(art_id); $("#article_comment_field").val(""); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function dropArticleComment(art_id,cmt_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		
		if(confirm('Видалити запис?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'dropArticleComment', 'art_id':art_id, 'cmt_id':cmt_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadArticleCommets(art_id); toastr["info"]("Запис успішно видалено"); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function loadArticleCDN(art_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleCDN', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("article_cdn_place").innerHTML=result["content"];
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
		$('#fileArticlesCDNUploadForm').modal('hide');
		loadArticleCDN(art_id);
	});
}

function showArticlesCDNDropConfirmForm(art_id,file_id,file_name){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'articlesCDNDropFile', 'art_id':art_id, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadArticleCDN(art_id); toastr["info"]("Файл успішно видалено"); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function showArtilceGallery(art_id,article_nr_displ){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showArtilceGallery', 'art_id':art_id,'article_nr_displ':article_nr_displ}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal('show');
			document.getElementById("FormModalBody").innerHTML=result["content"];
			document.getElementById("FormModalLabel").innerHTML=result["header"];
		}}, true);
	}
}

function loadArticleFoto(art_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleFoto', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("article_foto_place").innerHTML=result["content"];
		}}, true);
	}
}

function setArticlesFotoMain(art_id,file_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'setArticlesFotoMain', 'art_id':art_id, 'file_id':file_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ loadArticleFoto(art_id); toastr["info"]("Основне фото успішно призначено"); }
			else{ toastr["error"](result["error"]); }
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
		$('#fileArticlesFotoUploadForm').modal('hide');
		loadArticleFoto(art_id);
	});
}

function showArticlesFotoDropConfirmForm(art_id,file_id,file_name){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){		
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'articlesFotoDropFile', 'art_id':art_id, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadArticleFoto(art_id); toastr["info"]("Файл успішно видалено"); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function showArticlesSchemeUploadForm(){
	var template_id=$("#goods_group_template_id").val();
	if (template_id>0){
		$("#scheme_template_id").val(template_id);
		$("#fileArticlesSchemeUploadForm").modal('show');
		Dropzone.autoDiscover = false;
		var myDropzone4 = new Dropzone("#myDropzone4",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
		myDropzone4.removeAllFiles(true);
		myDropzone4.on("queuecomplete", function() {
			toastr["info"]("Завантаження файлів завершено.");
			this.removeAllFiles();
			$('#fileArticlesSchemeUploadForm').modal('hide');
			loadArticleScheme(template_id);
		});
	}else{swal("Помилка", "Оберіть шаблон для завантаження схеми.", "error");}
}

function loadArticleScheme(template_id){
	if (template_id<=0 || template_id==""){toastr["error"](errs[0]);}
	if (template_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleScheme', 'template_id':template_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("articles_params_scheme_files").innerHTML=result["content"];
		}}, true);
	}
}

function showArticlesSchemeDropConfirmForm(template_id,file_id,file_name){
	if (template_id<=0 || template_id==""){toastr["error"](errs[0]);}
	if (template_id>0){		
		if(confirm('Видалити схему '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'articlesSchemeDropFile', 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ loadArticleScheme(template_id); toastr["info"]("Файл успішно видалено"); }
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function loadArticleAnalogs(art_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleAnalogs', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("analog_place").innerHTML=result["content"];
			$('#catalogue_tabs').tab();
			$('#datatable1').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});
			$('#datatable2').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});
			$('#datatable3').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});
			$('#datatable4').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}, "dom": '<"top">frt<"bottom"lpi><"clear">'});
		}}, true);
	}
}

function loadArticleAplicability(art_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleAplicability', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("aplicability_place").innerHTML=result["content"];
			$('#catalogue_tabs').tab();
		}}, true);
	}
}

function loadArticleAplicabilityModels(art_id,mfa_id){
	JsHttpRequest.query($rcapi,{ 'w': 'loadArticleAplicabilityModels', 'art_id':art_id, 'mfa_id':mfa_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("aplic_tab"+mfa_id).innerHTML=result["content"];
		$('#catalogue_tabs').tab();
		$('#datatable_models'+mfa_id).DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
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
				}else{swal("Помилка", result["answer"], "error");}
			}}, true);
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function clearActicleAplicabilityManuf(art_id,mfa_id){
	swal({
		title: "Відвязати "+$("#CatalogueCardArticleNrDispl").html()+" від всіх авто?", text: "Притчу помнишь? ааааа...???", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
		confirmButtonText: "Відвязати", cancelButtonText: "Відмінити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w': 'clearActicleAplicabilityManuf', 'art_id':art_id, 'mfa_id':mfa_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadArticleAplicabilityModels(art_id,mfa_id);
					swal("Виконано!", "", "success");
				}else{swal("Помилка", result["answer"], "error");}
			}}, true);
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadArticleAplicabilityNew(art_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		var index=$("#CatalogueCardArticleNrDispl").html();
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleAplicabilityNew', 'art_id':art_id,'index':index}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow3").modal('show');
			document.getElementById("FormModalBody3").innerHTML=result["content"];
			document.getElementById("FormModalLabel3").innerHTML=result["header"];
			var tree = new treefilter($("#na_tree"), {
				searcher : $("na_input#na_my-search"), offsetLeft :20,
				multiselect : true,expanded : true,
			});
		}}, true);
	}
}

function loadAplicabilityModelList(mfa_id){
	if (mfa_id<=0 || mfa_id==""){toastr["error"](errs[0]);}
	if (mfa_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadAplicabilityModelList', 'mfa_id':mfa_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#na_mod_id").html(result["content"]);
		}}, true);
	}
}

function loadAplicabilityModificationList(mfa_id,mod_id){
	if (mfa_id<=0 || mod_id<=0 || mfa_id==""){toastr["error"](errs[0]);}
	if (mfa_id>0 && mod_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadAplicabilityModificationList', 'mfa_id':mfa_id, 'mod_id':mod_id}, 
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
	var comment=$("#na_comment").val();
	var modif_kol=$("#modif_kol").val();var tp_id=0;var typ_array="";
	for (i=1;i<=modif_kol;i++){if (document.getElementById("modif"+i).checked){typ_array+=document.getElementById("modif"+i).value+",";}}
	
	var str_kol=$("#menu_kol_elem").val();var str_array="";
	for (i=1;i<=str_kol;i++){
		if (document.getElementById("na_tree_"+i)){
			if (document.getElementById("na_tree_"+i).checked){
				str_array+=document.getElementById("na_tree_"+i).value+",";
			}
		}
	}

	if (art_id.length>0 && typ_array!=""){
		JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueAplicabilityForm','art_id':art_id,'display_number':display_number,'comment':comment,'typ_array':typ_array,'str_array':str_array},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
				loadArticleAplicability(art_id);
//				loadArticleAplicabilityNew(art_id);
			}
			else{ swal("Помилка!", result["error"], "error");}
			
		}}, true);
	}
}

function showLaIdCommentForm(art_id,type_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0 && type_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showLaIdCommentForm', 'art_id':art_id,'type_id':type_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow3").modal('show');
			document.getElementById("FormModalBody3").innerHTML=result["content"];
			document.getElementById("FormModalLabel3").innerHTML=result["header"];
		}}, true);
	}
}

function saveLaIdCommentForm(){
	var art_id=$("#cm_art_id").val();
	var type_id=$("#cm_type_id").val();
	var kol=$("#kol_elem_la").val();
	var la_ids=[];var sorts=[];var types=[];var text_names=[];var texts=[];
	for (var i=1;i<=kol;i++){
		la_ids[i]=$("#la_id_"+i).val();
		sorts[i]=$("#sort_"+i).val();
		types[i]=$("#type_"+i).val();
		text_names[i]=$("#text_name_"+i).val();
		texts[i]=$("#text_"+i).val();
	}
	JsHttpRequest.query($rcapi,{ 'w': 'saveLaIdCommentForm', 'art_id':art_id,'type_id':type_id,'kol':kol,'la_ids':la_ids,'sorts':sorts,'types':types,'text_names':text_names,'texts':texts}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			showLaIdCommentForm(art_id,type_id);
		}
		else{ swal("Помилка!", result["error"], "error");}
		
	}}, true);
}

function dropLaIdComment(art_id,type_id,la_id,sortf,typef){
	swal({
		title: "Видалити LA_ID коментар?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
		confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w':'dropLaIdComment','art_id':art_id,'type_id':type_id,'la_id':la_id,'sortf':sortf,'typef':typef},
			function (result, errors){ if(errors){alert(errors);} if (result){  
				if (result["answer"]==1){
					swal("Видалено!", "Коментар успішно видалено.", "success");
					showLaIdCommentForm(art_id,type_id);
				}
				else{ swal("Помилка!", result["error"], "error");}
			}}, true);
		} else {swal("Відмінено", "Внесені Вами зміни анульовано.", "error");}
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
	var index_pack=$("#index_pack").val();
	var height=$("#height").val();
	var length=$("#length").val();
	var width=$("#width").val();
	var volume=$("#volume").val();
	var weight_netto=$("#weight_netto").val();
	var weight_brutto=$("#weight_brutto").val();
	var necessary_amount_car=$("#necessary_amount_car").val();
	var units_id=$("#units_id").val();
	var multiplicity_package=$("#multiplicity_package").val();
	var shoulder_delivery=$("#shoulder_delivery").val();
	var general_quant=$("#general_quant").val();

	var work_pair_n=$("#work_pair_n").val();
	var work_pair=[];
	for (var i=1;i<=work_pair_n;i++){
		work_pair[i]=$("#work_pair_"+i).val();
	}

	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueLogistic','art_id':art_id,'index_pack':index_pack,'height':height,'length':length,'width':width,'volume':volume,'weight_netto':weight_netto,'weight_brutto':weight_brutto, 'necessary_amount_car':necessary_amount_car, 'units_id':units_id, 'multiplicity_package':multiplicity_package, 'shoulder_delivery':shoulder_delivery, 'general_quant':general_quant, 'work_pair':work_pair},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			}
			else{ swal("Помилка!", result["error"], "error");}
			
		}}, true);
	}
}

function loadArticleZED(art_id){
	if (art_id<=0 || art_id==""){toastr["error"](errs[0]);}
	if (art_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadArticleZED', 'art_id':art_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("zed_place").innerHTML=result["content"];
			$('#catalogue_tabs').tab();
		}}, true);
	}
}

function preconfirmCatalogueZED(art_id){
	swal({
		title: "Зберегти зміни у розділі \"ЗЕД\"?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {saveCatalogueZED(art_id);} else {swal("Відмінено", "Внесені Вами зміни анульовано.", "error");}
	});
}

function saveCatalogueZED(art_id){
	var country_id=$("#country_id").val();
	var costums_id=$("#costums_id").val();
	if (art_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueZED','art_id':art_id,'country_id':country_id,'costums_id':costums_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			}
			else{ swal("Помилка!", result["error"], "error");}
			
		}}, true);
	}
}

function showCountryManual(){
	var country_id=$("#country_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCountryManual','country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CountryModalWindow").modal('show');
		document.getElementById("CountryBody").innerHTML=result["content"];
		setTimeout(function(){
		  $('#datatable_country').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCountry(id,name){
	$("#country_id").val(id);
	$("#country_name").val(name);
	$("#CountryModalWindow").modal('hide');
}

function showCountryForm(country_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCountryForm','country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML=result["header"];
	}}, true);
}

function saveCatalogueCountryForm(){
	var id=$("#form_country_id").val();
	var name=$("#form_country_name").val();
	var alfa2=$("#form_country_alfa2").val();
	var alfa3=$("#form_country_alfa3").val();
	var duty=$("#form_country_duty").val();
	var risk=$("#form_country_risk").val();
	JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueCountryForm','id':id,'name':name,'alfa2':alfa2,'alfa3':alfa3,'duty':duty,'risk':risk},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			$("#country_id").val(id);
			showCountryManual();
		}
		else{ swal("Помилка!", result["error"], "error");}
	}}, true);
}

function dropCountry(id){
	swal({
		title: "Видалити країну у розділі \"ЗЕД\"?", text: "Небезпечно для здоровя людини! Ризикнемо?", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
		confirmButtonText: "Так, видалити!", cancelButtonText: "Хочу жити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w':'dropCountry','country_id':id},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Видалено!", "Країну успішно видалено.", "success");
					showCountryManual();
				}
				else{ swal("Помилка!", result["error"], "error");}
			}}, true);
		} else {swal("Відмінено", "Внесені Вами зміни анульовано.", "error");}
	});
}

function clearArtilceCountry(){
	$("#country_id").val("");
	$("#country_name").val("");
}

function showCostumsManual(){
	var costums_id=$("#costums_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsManual','costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CostumsModalWindow").modal('show');
		document.getElementById("CostumsBody").innerHTML=result["content"];
		setTimeout(function(){
		  $('#datatable_costums').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
		
	}}, true);
}

function selectCostums(id,code){
	$("#costums_id").val(id);
	$("#costums_code").val(code);
	$("#CostumsModalWindow").modal('hide');
}

function showCostumsForm(costums_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsForm','costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML=result["header"];
	}}, true);
}

function saveCatalogueCostumsForm(){
	var id=$("#form_costums_id").val();
	var code=$("#form_costums_code").val();
	var name=$("#form_costums_name").val();
	var preferential_rate=$("#form_costums_preferential_rate").val();
	var full_rate=$("#form_costums_full_rate").val();
	var type_declaration=$("#form_costums_type_declaration").val();
	var sertification=$("#form_costums_sertification").val();
	var gos_standart=$("#form_costums_gos_standart").val();
	JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueCostumsForm','id':id,'code':code,'name':name,'preferential_rate':preferential_rate,'full_rate':full_rate,'type_declaration':type_declaration,'sertification':sertification,'gos_standart':gos_standart},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
//			$("#costums_id").val(id);
			$("#CostumsModalWindow").modal('hide');
			$("#FormModalWindow").modal('hide');
			showCostumsManual();
		}
		else{ swal("Помилка!", result["error"], "error");}
		
	}}, true);
}

function dropCostums(id){
	swal({
		title: "Видалити митний код у розділі \"ЗЕД\"?", text: "Небезпечно для здоровя людини! Ризикнемо?", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
		confirmButtonText: "Так, видалити!", cancelButtonText: "Хочу жити!",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w':'dropCostums','costums_id':id},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Видалено!", "Митний код успішно видалено.", "success");
					showCostumsManual();
				}
				else{ swal("Помилка!", result["error"], "error");}
			}}, true);
		} else {swal("Відмінено", "Внесені Вами зміни анульовано.", "error");}
	});
}

function clearArtilceCostums(){
	$("#costums_id").val("");
	$("#costums_code").val("");
}

function showCatalogueGoodGroupTemplateForm(art_id,template_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCatalogueGoodGroupTemplateForm','art_id':art_id, 'template_id':template_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML=result["header"];
	}}, true);
}

function editCatalogueGoodGroupTemplateForm(art_id){
	var template_id=$("#goods_group_template_id").val();
	if (template_id>0){
		showCatalogueGoodGroupTemplateForm(art_id,template_id);
	}else{swal("Помилка", "Оберіть шаблон для редагування", "error");}
}

function loadCatalogueGoodGroupTemplateParams(art_id){
	var template_id=$("#goods_group_template_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'loadCatalogueGoodGroupTemplateParams','art_id':art_id, 'template_id':template_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#articles_params_range").html(result["content"]);
		$("#articles_params_scheme_view_files").html(result["scheme_list"]);
	}}, true);
}

function showCatalogueAnalogIndexSearch(){
	JsHttpRequest.query($rcapi,{ 'w':'showCatalogueAnalogIndexSearch'},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal('show');
		document.getElementById("FormModalBody2").innerHTML=result["content"];
		document.getElementById("FormModalLabel2").innerHTML=result["header"];
	}}, true);
}

function findCatalogueAnalogIndexSearch(){
	var index=$("#form_analog_search").val();
	if (index.length>1){
		JsHttpRequest.query($rcapi,{ 'w':'findCatalogueAnalogIndexSearch', 'index': index},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("analog_search_result").innerHTML=result["content"];
		}}, true);
	}
}

function setAnalogSearchIndex(art_id,article_nr_displ,brand_id,brand_name){
	$("#form_analog_art_id2").val(art_id);
	$("#form_analog_display_nr").val(article_nr_displ);
	$("#form_analog_brand_id").val(brand_id).trigger("change");
	$("#FormModalWindow2").modal('hide');
}

function showCatalogueAnalogForm(art_id,kind,relation,search_number){
	JsHttpRequest.query($rcapi,{ 'w':'showCatalogueAnalogForm','art_id':art_id, 'kind':kind,'relation':relation, 'search_number':search_number},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML=result["header"];
		$("#form_analog_brand_id").select2({placeholder: "Виберіть Бренд",dropdownParent: $("#FormModalWindow")});
	}}, true);
}

function saveCatalogueAnalogForm(art_id){
	var kind=$("#form_analog_kind").val();
	var relation=$("#form_analog_relation").val();
	var search_number=$("#form_analog_search_number").val();
	var display_nr=$("#form_analog_display_nr").val();
	var brand_id=$("#form_analog_brand_id option:selected").val();
	var art_id2=$("#form_analog_art_id2").val();
	if (display_nr.length>0 && brand_id>0){
		if (art_id2!=""){
			swal({
				title: "Виконати двостороннє кросування?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
				confirmButtonText: "Так, Двостороннє!", cancelButtonText: "Ні, Одностороннє",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
			},
			function (isConfirm) {
				if (isConfirm) {
					var index2=$("#CatalogueCardArticleNrDispl").html();
					saveCatalogueAnalogData(art_id,kind,relation,search_number,display_nr,brand_id,art_id2,index2);
				} else {
					saveCatalogueAnalogData(art_id,kind,relation,search_number,display_nr,brand_id,"","");
				}
			});
		}
		if (art_id2==""){saveCatalogueAnalogData(art_id,kind,relation,search_number,display_nr,brand_id,"","");}
	}else{ swal("Помилка!", "Заповніть коректно форму", "error");}
}
 
function saveCatalogueAnalogData(art_id,kind,relation,search_number,display_nr,brand_id,art_id2,index2){
	JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueAnalogForm','art_id':art_id,'kind':kind,'relation':relation,'search_number':search_number,'display_nr':display_nr,'brand_id':brand_id,'art_id2':art_id2,'index2':index2},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){ 
			swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
			$("#FormModalWindow").modal('hide');
			loadArticleAnalogs(art_id);
		}
		else{ swal("Помилка!2", result["error"], "error");}
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
				}
				else{ swal("Помилка!", result["error"], "error");}
			}}, true);
		} else {swal("Відмінено", "Внесені Вами зміни анульовано.", "error");}
	});
}

function clearCatalogueAnalogArticle(art_id,kind,relation){
	var ArticleNrDispl=$("#CatalogueCardArticleNrDispl").html();
	var txt="OE Номера";
	if (kind==4 && relation==0){txt="інші номера";}
	if (kind==4 && relation==1){txt="артикул включає в себе";}
	if (kind==4 && relation==2){txt="артикул присутній в";}
	var text_info="У некоторого человека было два сына; и сказал младший из них отцу: отче! дай мне ответ на глобальный вопрос. И отец ответил ему: Циля думай шо делаешь...";
	swal({
		title: "Очистити аналоги "+ArticleNrDispl+" в чатині \""+txt+"\"?", text: text_info, type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
		confirmButtonText: "Так, очистити!", cancelButtonText: "Спаси и сохрани",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w':'clearCatalogueAnalogArticle','art_id':art_id,'kind':kind,'relation':relation},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("Очищено!", "Ну що тут можна ще добавити \"успішно видалено\".", "success");
					loadArticleAnalogs(art_id);
				}
				else{ swal("Помилка!", result["error"], "error");}
			}}, true);
		} else {swal("Відмінено", "Внесені Вами зміни анульовано.", "error");}
	});
}

function showCatNewArticle(){
	JsHttpRequest.query($rcapi,{ 'w':'showCatNewArticle'},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal('show');
		document.getElementById("FormModalBody").innerHTML=result["content"];
		document.getElementById("FormModalLabel").innerHTML=result["header"];
	}}, true);	
}

function setBrandLetter(){
	var vl=$("#new_art_brand_id option:selected").val();
	var vla=vl.split("-");
	$("#art_p1").html(vla[1]);
	return true;
}

function setGoodsGroupKey(){
	var vl=$("#new_art_goods_group_id option:selected").val();
	var vla=vl.split("-");
	$("#art_p2").html(vla[1]);
	JsHttpRequest.query($rcapi,{ 'w':'showGoodsGroupLetterListSelect','prnt_id':vla[0]},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#new_art_subgoods_group_id").html(result["content"]);
	}}, true);	
	return true;
}

function setSubGoodsGroupKey(){
	var vl=$("#new_art_subgoods_group_id option:selected").val();
	var vla=vl.split("-");
	$("#art_p3").html(vla[1]);
	JsHttpRequest.query($rcapi,{ 'w':'loadRefinementList', 'subgoods_group_id':vla[0]},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("new_art_refinement_id").innerHTML=result["content"];
	}}, true);	
	return true;
}

function setManufKey(){
	var vl=$("#new_art_manuf_id option:selected").val();
	var vla=vl.split("-");
	$("#art_p4").html(vla[1]);
	return true;
}

function setRefinementKey(){
	var vl=$("#new_art_refinement_id option:selected").val();
	$("#art_p6").html(vl);
	return true;
}

function setNewArtNum(){
	var vl=$("#new_art_next_num").val();
	$("#art_p5").html(vl);
	return true;
}

function findNewArtNextNum(){
	var brand=$("#new_art_brand_id option:selected").val();
	var group=$("#new_art_goods_group_id option:selected").val();
	var sub_group=$("#new_art_subgoods_group_id option:selected").val();
	var manuf=$("#new_art_manuf_id option:selected").val();
	$("#waveSpinner_num_place").html(waveSpinner);
	JsHttpRequest.query($rcapi,{ 'w':'findNewArtNextNum', 'brand':brand,'group':group,'sub_group':sub_group,'manuf':manuf},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("new_art_next_num").value=result["content"];
		$("#waveSpinner_num_place").html("");
	}}, true);	
	return true;
}

function findNewArtID(){
	var brand=$("#new_art_brand_id option:selected").val();
	$("#waveSpinner_art_place").html(waveSpinner);
	JsHttpRequest.query($rcapi,{ 'w':'findNewArtID', 'brand':brand},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		document.getElementById("new_art_id").value=result["content"];
		$("#waveSpinner_art_place").html("");
	}}, true);	
	return true;
}

function checkCatalogueNewArt(){
	var num=$("#art_p1").html()+""+$("#art_p2").html()+""+$("#art_p3").html()+""+$("#art_p4").html()+""+$("#art_p5").html()+""+$("#art_p6").html();
	var art_id=$("#new_art_id").val();
	$("#waveSpinner_art_place").html(waveSpinner);
	JsHttpRequest.query($rcapi,{ 'w':'checkCatalogueNewArt', 'num':num, 'art_id':art_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		if (result["answer"]==1){
			$("#waveSpinner_art_place").html("");
			$("#saveButtonNewArt").removeAttr("disabled");
		}
		else{swal("Помилка!", result["error"], "error");$("#saveButtonNewArt").removeAttr("disabled").attr("disabled");}
	}}, true);	
}

function saveCatalogueNewArt(){
	var brand=$("#new_art_brand_id option:selected").val();
	var group=$("#new_art_goods_group_id option:selected").val();
	var sub_group=$("#new_art_subgoods_group_id option:selected").val();
	var manuf=$("#new_art_manuf_id option:selected").val();
	var num=$("#art_p1").html()+""+$("#art_p2").html()+""+$("#art_p3").html()+""+$("#art_p4").html()+""+$("#art_p5").html()+""+$("#art_p6").html();
	var art_id=$("#new_art_id").val();

	if (art_id.length>0 && num.length>0){
		swal({
			title: "Створити новий артикул?", text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", 
			confirmButtonText: "Так!", cancelButtonText: "Ні",  closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true 
		},
		function (isConfirm) {
			if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w':'saveCatalogueNewArt', 'num':num, 'art_id':art_id, 'brand':brand,'group':group,'sub_group':sub_group,'manuf':manuf},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){
						$("#FormModalWindow").modal('hide');
						showCatalogueCard(result["art_id"]);
					}
					else{swal("Помилка!", result["error"], "error");$("#saveButtonNewArt").removeAttr("disabled").attr("disabled");}
				}}, true);	
			} else {swal("Відмінено", "", "error");}
		});
	}
}

function selectFromList2(brand_id,art){
	$("#list2_art").val(""+art);
	$("#list2_brand_id").val(""+brand_id);
	doc_catalogue_article_search(0);
	$("#FormModalWindow2").modal('hide');
}

