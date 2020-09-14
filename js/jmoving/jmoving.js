var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

$(document).ready(function() {
    shortcut.add("Insert", function() {
		let jmoving_id=$("#jmoving_id").val();
		let jmoving_type_id=$("#jmoving_type_id").val();
		if (jmoving_id>0){
			if (jmoving_type_id==0){addNewRowLocal();}
			if (jmoving_type_id==1){addNewRow();}
		}
	});
	var status=$("#update_status").val();
	if (status==="true") { setTimeout(function(){updateJmovingRange();},15*1000);}
});

$(window).bind('beforeunload', function(e){
    if($("#jmoving_id")){
		closeJmovingCard();
		e=null;
	}
    else e=null; 
});

function filterJmovingsList() {
	let name=$("#filJmovingName").val();
    let data_from=$("#filDataFrom").val();
    let data_to=$("#filDataTo").val();
    let status=$("#filStatusMain option:selected").val();
	$("#jmoving_range").empty();
	JsHttpRequest.query($rcapi,{ 'w': 'filterJmovingList', 'name':name, 'data_from':data_from, 'data_to':data_to, 'status':status}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#jmoving_range").html(result.content);
		$("#filDataFrom").datepicker({format: "yyyy-mm-dd",autoclose:true});
		$("#filDataTo").datepicker({format: "yyyy-mm-dd",autoclose:true});
	}}, true);
}

function show_jmoving_search(inf){
	$("#jmoving_range").empty();
	JsHttpRequest.query($rcapi,{ 'w': 'show_jmoving_search'},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#jmoving_range").html(result.content);
			if (inf==1){toastr["info"]("Виконано!");}
		}}, true);
}

function autoUpdateJmoving(press_btn){
	var status=$("#update_status").val();
	if (press_btn) {
		status==="true" ? status=true : status=false;
		if (status){
			$("#update_status").val("false");
			$("#toggle_update").html("<i class='fa fa-play'></i>");
		} else  {
			$("#update_status").val("true");
			$("#toggle_update").html("<i class='fa fa-stop-circle'></i>");
		}	
	}
	updateJmovingRange();
}

function updateJmovingRange(press_btn){
	var status=$("#input_done").val();
	if (press_btn) {
        status==="true" ? status=true : status=false;
		if (status){
			$("#input_done").val("false");
			$("#toggle_done").html("<i class='fa fa-eye-slash'></i>");
		} else {
			$("#input_done").val("true");
			$("#toggle_done").html("<i class='fa fa-eye'></i>");
		}	
	} else {
		status==="true" ? status=false : status=true;
	}
	var prevRange=$("#jmoving_range").html();
	JsHttpRequest.query($rcapi,{ 'w': 'show_jmoving_search', 'status':status},
	function (result, errors){ if (errors) {alert(errors);} if (result){
		if (prevRange.length != result["content"].length){
			$("#jmoving_range").empty();
			$("#jmoving_range").html(result.content);
		}
		var status=$("#update_status").val();
		if (status==="true") {
			  setTimeout(function(){updateJmovingRange();},15*1000);
		}
	}}, true);
}

function preNewJmovingCard(){
	$("#FormModalWindow3").modal("show");
	JsHttpRequest.query($rcapi,{ 'w': 'preNewJmovingCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#FormModalBody3").html(result.content);
		$("#FormModalLabel3").html("Оберіть тип накладної");
	}}, true);
}

function newJmovingCard(type_id){
	$("#FormModalWindow3").modal("hide");
	JsHttpRequest.query($rcapi,{ 'w': 'newJmovingCard', 'type_id':type_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		let jmoving_id=result["jmoving_id"];
		if (type_id==1){ showJmovingCard(jmoving_id);}
		if (type_id==0){ showJmovingCardLocal(jmoving_id);}
		show_jmoving_search(0);
	}}, true);
}

function showJmovingCard(jmoving_id){
	if (jmoving_id<=0 || jmoving_id===""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showJmovingCard', 'jmoving_id':jmoving_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#JmovingCard").modal("show");
            $("#JmovingCardBody").html(result["content"]);
            $("#JmovingCardLabel").html(result["doc_prefix_nom"]);
			$("#jmoving_tabs").tab();
			$("#cash_id").select2({placeholder: "Виберіть валюту",dropdownParent: $("#JmovingCard")});
			$("#jmoving_data").datepicker({format: "yyyy-mm-dd",autoclose:true});
			$(".i-checks").iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
			//$("#storage_id_to").select2({placeholder: "Виберіть склад",dropdownParent: $("#JmovingCard")});
			//$("#cell_id_to").select2({placeholder: "Виберіть комірку",dropdownParent: $("#JmovingCard")});
            numberOnly();
		}}, true);
	}
}
function showJmovingCardLocal(jmoving_id){
	if (jmoving_id<=0 || jmoving_id===""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showJmovingCardLocal', 'jmoving_id':jmoving_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#JmovingCard").modal("show");
            $("#JmovingCardBody").html(result["content"]);
            $("#JmovingCardLabel").html(result["doc_prefix_nom"]);
			$("#jmoving_tabs").tab();
			$("#cash_id").select2({placeholder: "Виберіть валюту",dropdownParent: $("#JmovingCard")});
			$("#jmoving_data").datepicker({format: "yyyy-mm-dd",autoclose:true});
			$(".i-checks").iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
			//$("#storage_id_to").select2({placeholder: "Виберіть склад",dropdownParent: $("#JmovingCard")});
			//$("#cell_id_to").select2({placeholder: "Виберіть комірку",dropdownParent: $("#JmovingCard")});
            numberOnly();
		}}, true);
	}
}

function closeJmovingCard(){
	if ($("#jmoving_id")){
		let jmoving_id=$("#jmoving_id").val();
		JsHttpRequest.query($rcapi,{ 'w': 'closeJmovingCard', 'jmoving_id':jmoving_id},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				$("#JmovingCard").modal("hide");
				$("#JmovingCardBody").html("");
				$("#JmovingCardLabel").html("");
			}}, true);
	} else {
        $("#JmovingCard").modal("hide");
        $("#JmovingCardBody").html("");
        $("#JmovingCardLabel").html("");
	}
}

function addNewRow(){
	let storage_id_to=$("#storage_id_to option:selected").val();
	if (storage_id_to=="" || storage_id_to==0) {swal("Помилка!", "Оберіть склад переміщення!", "error");}
	else {
		var row=$("#jmovingStrNewRow").html();
		var kol_row=parseInt($("#kol_row").val());
		kol_row+=1;$("#kol_row").val(kol_row);
		row=row.replace('nom_i', ''+kol_row);
		row=row.replace('i_0', ''+kol_row);
		row=row.replace('i_0', ''+kol_row);
		row=row.replace('idStr_0', 'idStr_'+kol_row);
		row=row.replace('artIdStr_0', 'artIdStr_'+kol_row);
		row=row.replace('article_nr_displStr_0', 'article_nr_displStr_'+kol_row);
		row=row.replace('brandIdStr_0', 'brandIdStr_'+kol_row);
		row=row.replace('brandNameStr_0', 'brandNameStr_'+kol_row);
		row=row.replace('amountStr_0', 'amountStr_'+kol_row);
		row=row.replace('storageIdFrom_0', 'storageIdFrom_'+kol_row);
		row=row.replace('storageNameFrom_0', 'storageNameFrom_'+kol_row);
		row=row.replace('cellIdFrom_0', 'cellIdFrom_'+kol_row);
		row=row.replace('cellNameFrom_0', 'cellNameFrom_'+kol_row);
		row=row.replace('amountRestStr_0', 'amountRestStr_'+kol_row);
		row=row.replace('max_stock_0', 'max_stock_'+kol_row);
		row=row.replace('amountStr_0', 'amountStr_'+kol_row);
		row=row.replace("id='jmovingStrNewRow' class='hidden'", " id='strRow_"+kol_row+"'");
		$("#jmoving_str tbody").append("<tr>"+row+"</tr>");
		let jmoving_id=$("#jmoving_id").val();
		showJmovingArticleSearchForm(''+kol_row,'0','0','',''+jmoving_id,'');
	}
	return true;
}

function addNewRowLocal(){
	let storage_id_to=$("#storage_id_to option:selected").val();
	if (storage_id_to=="" || storage_id_to==0) {swal("Помилка!", "Оберіть склад переміщення!", "error");}
	else {
		var row=$("#jmovingStrNewRow").html();
		var kol_row=parseInt($("#kol_row").val());
		kol_row+=1;$("#kol_row").val(kol_row);
		row=row.replace('nom_i', ''+kol_row);
		row=row.replace('i_0', ''+kol_row);
		row=row.replace('i_0', ''+kol_row);
		row=row.replace('idStr_0', 'idStr_'+kol_row);
		row=row.replace('artIdStr_0', 'artIdStr_'+kol_row);
		row=row.replace('article_nr_displStr_0', 'article_nr_displStr_'+kol_row);
		row=row.replace('brandIdStr_0', 'brandIdStr_'+kol_row);
		row=row.replace('brandNameStr_0', 'brandNameStr_'+kol_row);
		row=row.replace('amountStr_0', 'amountStr_'+kol_row);
		row=row.replace('storageIdFrom_0', 'storageIdFrom_'+kol_row);
		row=row.replace('storageNameFrom_0', 'storageNameFrom_'+kol_row);
		row=row.replace('cellIdFrom_0', 'cellIdFrom_'+kol_row);
		row=row.replace('cellNameFrom_0', 'cellNameFrom_'+kol_row);
		row=row.replace('amountRestStr_0', 'amountRestStr_'+kol_row);
		row=row.replace('max_stock_0', 'max_stock_'+kol_row);
		row=row.replace('amountStr_0', 'amountStr_'+kol_row);
		row=row.replace('cellIdTo_0', 'cellIdTo_'+kol_row);
		row=row.replace("id='jmovingStrNewRow' class='hidden'", " id='strRow_"+kol_row+"'");
		$("#jmoving_str tbody").append("<tr>"+row+"</tr>");
		let jmoving_id=$("#jmoving_id").val();
		showJmovingArticleLocalSearchForm(''+kol_row,'0','0','',''+jmoving_id,'');
	}
	return true;
}

function saveJmovingCard(){
	var jmoving_id=$("#jmoving_id").val();
	var jmoving_op_id=$("#jmoving_op_id option:selected").val();
	var data=$("#jmoving_data").val();
	var storage_id_to=$("#storage_id_to option:selected").val();
	var cell_id_to=$("#cell_id_to option:selected").val();
	var comment=$("#jmoving_comment").val();
	var ikr=$("#kol_row").val();
	var idStr=[];var artIdStr=[]; var article_nr_displStr=[]; var brandIdStr=[]; var amountStr=[]; var storageIdFromStr=[];  var cellIdFromStr=[];
	for (var i=1;i<=ikr;i++){
		idStr[i]=$("#idStr_"+i).val(); artIdStr[i]=$("#artIdStr_"+i).val(); article_nr_displStr[i]=$("#article_nr_displStr_"+i).val(); brandIdStr[i]=$("#brandIdStr_"+i).val();
		storageIdFromStr[i]=$("#storageIdFrom_"+i).val(); cellIdFromStr[i]=$("#cellIdFrom_"+i).val(); amountStr[i]=$("#amountStr_"+i).val();
	}
	if (jmoving_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveJmovingCard','jmoving_id':jmoving_id,'jmoving_op_id':jmoving_op_id,'data':data,'storage_id_to':storage_id_to,'cell_id_to':cell_id_to,'comment':comment,'kol_row':ikr,'idStr':idStr,'artIdStr':artIdStr,'article_nr_displStr':article_nr_displStr,'brandIdStr':brandIdStr,'storageIdFromStr':storageIdFromStr,'cellIdFromStr':cellIdFromStr,'amountStr':amountStr},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				show_jmoving_search(0);
			}
			else{ swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function saveJmovingCardLocal(){
	var jmoving_id=$("#jmoving_id").val();
	var jmoving_op_id=$("#jmoving_op_id option:selected").val();
	var data=$("#jmoving_data").val();
	var storage_id_to=$("#storage_id_to option:selected").val();
	var comment=$("#jmoving_comment").val();
	var ikr=$("#kol_row").val();
	var idStr=[];var artIdStr=[]; var cellIdToStr=[];
	var cell_er=0;
	for (var i=1;i<=ikr;i++){
		idStr[i]=$("#idStr_"+i).val(); artIdStr[i]=$("#artIdStr_"+i).val(); cellIdToStr[i]=$("#cellIdTo_"+i+" option:selected").val();
		if (idStr[i]>0 && (cellIdToStr[i]==0 || cellIdToStr[i]=="")){cell_er=1;}
	}
	if (jmoving_id.length>0 && cell_er==1){swal("Помилка!", "Оберіть комірку мереміщення для всіх артикулів", "error");}
	if (jmoving_id.length>0 && cell_er==0){
		JsHttpRequest.query($rcapi,{ 'w':'saveJmovingCardLocal','jmoving_id':jmoving_id,'jmoving_op_id':jmoving_op_id,'data':data,'storage_id_to':storage_id_to,'comment':comment,'kol_row':ikr,'idStr':idStr,'artIdStr':artIdStr,'cellIdToStr':cellIdToStr},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				show_jmoving_search(0);
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function showJmovingDocumentList(jmoving_id){
	let jmoving_op_id=$("#jmoving_op_id option:selected").val();
    let document_id=$("#document_id").val();
	$("#FormModalWindow").modal("show");
	JsHttpRequest.query($rcapi,{ 'w': 'showJmovingDocumentList', 'jmoving_id':jmoving_id, 'jmoving_op_id':jmoving_op_id, 'document_id':document_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#JmovingCardBody").html(result.content);
        $("#JmovingCardLabel").html(result.header);
	}}, true);
}

function findJmovingDocumentsSearch(jmoving_id,jmoving_op_id){
    let s_nom=$("#form_document_search").val();
	JsHttpRequest.query($rcapi,{ 'w': 'findJmovingDocumentsSearch', 'jmoving_id':jmoving_id, 'jmoving_op_id':jmoving_op_id, 's_nom':s_nom}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
        $("#documents_search_result").html(result.content);
	}}, true);
}

function setDocumentToForm(document_id,document_name){
	$("#document_id").val(document_id);
	$("#document_name").val(document_name);
	$("#FormModalWindow").modal("hide");
    $("#FormModalBody").html("");
    $("#FormModalLabel").html("");
}

function loadJmovingStorage(jmoving_id){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingStorage', 'jmoving_id':jmoving_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#storage_place").html(result.content);
			$("#jmoving_tabs").tab();
			$("#storage_id").select2({placeholder: "Склад зберігання",dropdownParent: $("#JmovingCard")});
			$("#storage_cells_id").select2({placeholder: "комірка зберігання",dropdownParent: $("#JmovingCard")});
		}}, true);
	}
}

function loadStorageCellsSelectList(place,place_to){
	if (place==""){ var storage_id=$("#storage_id_to option:selected").val();}
	if (place!=""){ var storage_id=$("#"+place+" option:selected").val();}
	if (place_to==""){ place_to="cell_id_to";}
	if (storage_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingStorageCellsSelectList', 'storage_id':storage_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById(place_to).innerHTML=result["content"];
			var cells_show=result["cells_show"];
			if (cells_show==0){ $("#lbl_cell").removeClass("hidden").addClass("hidden"); $("#dv_cell").removeClass("hidden").addClass("hidden"); }
			if (cells_show==1){ $("#lbl_cell").removeClass("hidden"); $("#dv_cell").removeClass("hidden"); }
			$("#"+place_to).select2({placeholder: "комірка зберігання",dropdownParent: $("#JmovingCard")});
			saveJmovingCard();
		}}, true);
	}
}

function showJmovingLocalAutoCellForm(){
	let jmoving_id=$("#jmoving_id").val();
	let storage_id_to=$("#storage_id_to option:selected").val();
	if (storage_id_to<=0 || storage_id_to==""){
		swal("Помилка!", "Оберіть склад переміщення", "error");
	}
	if (storage_id_to>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showJmovingLocalAutoCellForm', 'jmoving_id':jmoving_id, 'storage_id_to':storage_id_to},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow3").modal("show");
			$("#FormModalBody3").html(result.content);
			$("#FormModalLabel3").html("Вибір комірки");
		}}, true);
	}
}

function saveJmovingLocalAutoCell(){
	let jmoving_id=$("#jmoving_idA").val();
    let storage_id_to=$("#storage_id_toA").val();
    let cell_id_from=$("#cell_from_move option:selected").val();
    let cell_id_to=$("#cell_from_move_to option:selected").val();
	if (cell_id_from==0 || cell_id_from==""){swal("Помилка!", "Оберіть комірку розміщення", "error");}
	if (cell_id_from>0){
		JsHttpRequest.query($rcapi,{ 'w': 'saveJmovingLocalAutoCell','jmoving_id':jmoving_id,'storage_id_to':storage_id_to,'cell_id_from':cell_id_from,'cell_id_to':cell_id_to},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow3").modal("hide");
			$("#FormModalBody3").html("");
			$("#FormModalLabel3").html("");
			if (result["no_row"]==1){
				swal("Помилка!", "В комірці відбору немає товару!", "error");
			}
			if (result["no_row"]==0){
				showJmovingCardLocal(jmoving_id);
			}
		}}, true);
	}
}

function showJmovingArticleSearchForm(i,art_id,brand_id,article_nr_displ,jmoving_id,storage_id_to){
	var jmoving_id=$("#jmoving_id").val();
	if (storage_id_to=="" || storage_id_to==0){ storage_id_to=$("#storage_id_to option:selected").val(); }
	if (storage_id_to=="" || storage_id_to==0){ swal("Помилка!", "Оберіть склад переміщення", "error"); } else {
		JsHttpRequest.query($rcapi,{ 'w': 'showJmovingArticleSearchForm', 'brand_id':brand_id, 'article_nr_displ':article_nr_displ, 'jmoving_id':jmoving_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow").modal("show");
			$("#FormModalBody").html(result.content);
			$("#FormModalLabel").html("Номенклатура");
			$("#row_pos").val(i);
			$("#datatable_parrent").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
		}}, true);
	}
}

function showJmovingArticleLocalSearchForm(i,art_id,brand_id,article_nr_displ,jmoving_id,storage_id_from){
	JsHttpRequest.query($rcapi,{ 'w': 'showJmovingArticleLocalSearchForm', 'brand_id':brand_id, 'article_nr_displ':article_nr_displ, 'jmoving_id':jmoving_id, 'storage_id_from':storage_id_from},
	function (result, errors){ if (errors) {alert(errors);} if (result){
        $("#FormModalWindow").modal("show");
        $("#FormModalBody").html(result.content);
        $("#FormModalLabel").html("Номенклатура");
		$("#row_pos").val(i);
		$("#datatable_parrent").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Russian.json"}});
	}}, true);
}

function catalogue_article_storage_rest_search(search_type){
	var art=$("#catalogue_art").val();
	var brand_id=0;
	if ($("#list2_art").val().length>0){
		art=$("#list2_art").val();$("#list2_art").val("");
		brand_id=$("#list2_brand_id").val();$("#list2_brand_id").val("");
	}
	if (art.length<=2){ $("#srchInG").addClass("has-error");/*	toastr["warning"](errs[1]);*/}
	if (art.length>2){$("#srchInG").removeClass("has-error");
		$("#waveSpinnerCat_place").html(waveSpinner);
		$("#catalogue_range").empty();
		let jmoving_id=$("#jmoving_id").val();
		//alert('doc_catalogue_article_search art='+art+' brand_id='+brand_id+' search_type='+search_type);
		JsHttpRequest.query($rcapi,{ 'w': 'catalogue_article_storage_rest_search', 'art':art, 'brand_id':brand_id, 'search_type':search_type, 'jmoving_id':jmoving_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["brand_list"]!="" && result["brand_list"]!=null && search_type==0){
				$("#FormModalWindow2").modal("show");
				$("#FormModalBody2").html(result["brand_list"]);
				$("#FormModalLabel2").html(mess[0]);
			}
			if (result["brand_list"]=="" || result["brand_list"]==null || search_type>0){
				$("#catalogue_range").html(result["content"]);
				$("#waveSpinnerCat_place").html("");
			}
		}}, true);
	}
}

function catalogue_article_storage_rest_search_local(search_type){
	var art=$("#catalogue_art").val();
	var brand_id=0;
	if ($("#list2_art").val().length>0){
		art=$("#list2_art").val();$("#list2_art").val("");
		brand_id=$("#list2_brand_id").val();$("#list2_brand_id").val("");
	}
	if (art.length<=2){ $("#srchInG").addClass("has-error");/*	toastr["warning"](errs[1]);*/}
	if (art.length>2){$("#srchInG").removeClass("has-error");
		$("#waveSpinnerCat_place").html(waveSpinner);
		$("#catalogue_range").empty();
		let jmoving_id=$("#jmoving_id").val();
        let storage_id=$("#storage_id_to").val();
		JsHttpRequest.query($rcapi,{ 'w': 'catalogue_article_storage_rest_search_local', 'art':art, 'brand_id':brand_id, 'search_type':search_type, 'jmoving_id':jmoving_id, 'storage_id':storage_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["brand_list"]!="" && result["brand_list"]!=null && search_type==0){
				$("#FormModalWindow2").modal("show");
				$("#FormModalBody2").html(result["brand_list"]);
				$("#FormModalLabel2").html(mess[0]);
			}
			if (result["brand_list"]=="" || result["brand_list"]==null || search_type>0){
				$("#catalogue_range").html(result["content"]);
				$("#waveSpinnerCat_place").html("");
			}
		}}, true);
	}
}

function setArticleToSelectAmountJmoving(art_id,article_nr_displ,brand_id,brand_name,storage_id,storage_name,cell_id,cell_name,max_stock,amountRest){
	//var jmoving_type_id=$("#jmoving_type_id").val();
	//var j_storage=0; if (jmoving_type_id==0){j_storage=$("#storage_id_to option:selected").val();}
	let j_storage=$("#storage_id_to option:selected").val();
	JsHttpRequest.query($rcapi,{ 'w': 'setArticleToSelectAmountJmoving', 'art_id':art_id, 'j_storage':j_storage},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal("show");
		$("#FormModalBody2").html(result.content);
		$("#FormModalLabel2").html("Вкажіть кількість: "+article_nr_displ+" "+brand_name);
		numberOnlyPlace("amount_move_numbers");
		$("#art_idS2").val(art_id);
		$("#article_nr_displS2").val(article_nr_displ);
		$("#brand_idS2").val(brand_id);
		$("#brand_nameS2").val(brand_name);
		/*
		$('#storage_idS2').val(storage_id);
		$('#storage_nameS2').val(storage_name);
		$('#cell_idS2').val(cell_id);
		$('#cell_nameS2').val(cell_name);
		*/
		$("#max_stockS2").val(max_stock);
		$("#amountRestS2").val(amountRest);
		$("#amountRestTextS2").html(amountRest);
	}}, true);
}

function setArticleToSelectAmountJmovingLocal(art_id,article_nr_displ,brand_id,brand_name,storage_id,storage_name,cell_id,cell_name,max_stock,amountRest){
	JsHttpRequest.query($rcapi,{ 'w': 'setArticleToSelectAmountJmovingLocal', 'art_id':art_id, 'storage_id':storage_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow2").modal("show");
        $("#FormModalBody2").html(result.content);
        $("#FormModalLabel2").html("Вкажіть кількість: "+article_nr_displ+" "+brand_name);
		numberOnlyPlace("amount_move_numbers");
        $("#art_idS2").val(art_id);
        $("#article_nr_displS2").val(article_nr_displ);
        $("#brand_idS2").val(brand_id);
        $("#brand_nameS2").val(brand_name);
		/*
		$('#storage_idS2').val(storage_id);
		$('#storage_nameS2').val(storage_name);
		$('#cell_idS2').val(cell_id);
		$('#cell_nameS2').val(cell_name);
		*/
        $("#max_stockS2").val(max_stock);
        $("#amountRestS2").val(amountRest);
        $("#amountRestTextS2").html(amountRest);
	}}, true);
}

function setCellMaxMoving(){
	let max_value=$("#cell_from_move option:selected").attr('data-max-mov');
	$("#amount_move").attr({"max" : max_value,"min" : 1});
	$("#amountMaxValue").val(max_value);
	//$('#storage_idS2').val($("#storage_move option:selected").val());
	//$('#cell_idS2').val($("#storage_move option:selected").attr('data-cellId-mov')); 
	return true;
}

function setStorageMaxMoving(){
	let max_value=$("#storage_move option:selected").attr('data-max-mov');
	$("#amount_move").attr({"max" : max_value,"min" : 1});
	$("#amountMaxValue").val(max_value);
	$("#storage_idS2").val($("#storage_move option:selected").val());
	$("#cell_idS2").val($("#storage_move option:selected").attr("data-cellId-mov"));
	return true;
}

function setArticleToJmoving(){
	var jmoving_id=$("#jmoving_id").val();
	var amount_move=parseFloat($("#amount_move").val());
	var pos=$("#row_pos").val();
	var max_value=parseFloat($("#amountMaxValue").val());
	if (max_value<amount_move){swal("Помилка!", "Кількість для переміщення більша за залишок!", "error");}
	if (jmoving_id.length>0 && pos>0 && amount_move>0 && amount_move<=max_value){
		var idStr=$("#idStr_"+pos).val();
		var artIdStr=$("#art_idS2").val();
		var article_nr_displStr=$("#article_nr_displS2").val();
		var brandIdStr=$("#brand_idS2").val();
		var storageIdFromStr=$("#storage_idS2").val();
		var cellIdFromStr=$("#cell_idS2").val();
		var amountStr=amount_move;
		JsHttpRequest.query($rcapi,{ 'w':'setArticleToJmoving','jmoving_id':jmoving_id,'idStr':idStr,'artIdStr':artIdStr,'article_nr_displStr':article_nr_displStr,'brandIdStr':brandIdStr,'storageIdFromStr':storageIdFromStr,'cellIdFromStr':cellIdFromStr,'amountStr':amountStr},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
				document.getElementById("jmoving_weight").innerHTML=result["weight"];
				document.getElementById("jmoving_volume").innerHTML=result["volume"];
				document.getElementById("label_un_articles").innerHTML=result["label_empty"];
				console.log("answer = 1");

                let storage_id_to=$("#storage_id_to").val();
				JsHttpRequest.query($rcapi,{ 'w': 'showJmovingCardStr', 'jmoving_id':jmoving_id, 'storage_id_to':storage_id_to},
				function (result, errors){ if (errors) {alert(errors);} if (result){
					console.log("updated range");
					$("#jmoving_сhild_range").html(result.content);
					numberOnly();
				}}, true);
				
				$("#FormModalWindow2").modal("hide");document.getElementById("FormModalBody2").innerHTML="";document.getElementById("FormModalLabel2").innerHTML="";
				$("#FormModalWindow").modal("hide");document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";

				$("#row_pos").val("");
				var kol_row=parseInt($("#kol_row").val());
				if (kol_row>0){
					$("#storage_id_to").attr("disabled","disabled");
					$("#storage_id_to").addClass("disabled");
				}
				if (kol_row==0){
					$("#storage_id_to").attr("disabled","");
					$("#storage_id_to").removeClass("disabled");
				}
			} else { swal("Помилка!", result["error"], "error"); }
		}}, true);
	}
}

function changeArticleToJmoving(){
	var jmoving_id=$("#jmoving_id").val();
	var jmoving_str_id=$("#jmoving_str_id").val();
	var amount_move=parseFloat($('#amount_move').val());
	//var max_value=parseFloat($("#max_stock_ch").val());
	//if (max_value<amount_move){swal("Помилка!", "Кількість для переміщення більша за залишок!", "error");}
	//if (jmoving_id.length>0 && parseFloat(amount_move)>0 && amount_move<=max_value){
	JsHttpRequest.query($rcapi,{ 'w':'changeArticleToJmoving','jmoving_id':jmoving_id,'jmoving_str_id':jmoving_str_id,'amount_change':amount_move},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
				document.getElementById("jmoving_weight").innerHTML=result["weight"];
				document.getElementById("jmoving_volume").innerHTML=result["volume"];
				document.getElementById("label_un_articles").innerHTML=result["label_empty"];

				JsHttpRequest.query($rcapi,{ 'w': 'showJmovingCardStr', 'jmoving_id':jmoving_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					$("#jmoving_сhild_range").html(result.content);
					numberOnly();
				}}, true);
				
				$("#FormModalWindow2").modal("hide");document.getElementById("FormModalBody2").innerHTML="";document.getElementById("FormModalLabel2").innerHTML="";
				$("#FormModalWindow").modal("hide");document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	//}
}

function changeArticleToJmovingLocal(){
	var jmoving_id=$("#jmoving_id").val();
	var jmoving_str_id=$("#jmoving_str_id").val();
	var amount_move=parseFloat($('#amount_move').val());
	var max_value=parseFloat($("#max_stock_ch").val());
	if (max_value<amount_move){swal("Помилка!", "Кількість для переміщення більша за залишок!", "error");}
	if (parseFloat(amount_move)<0){swal("Помилка!", "Кількість для переміщення менша за 0!", "error"); }
	if (jmoving_id.length>0 && parseFloat(amount_move)>0 && amount_move<=max_value){
		JsHttpRequest.query($rcapi,{ 'w':'changeArticleToJmovingLocal','jmoving_id':jmoving_id,'jmoving_str_id':jmoving_str_id,'amount_change':amount_move},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
				document.getElementById("jmoving_weight").innerHTML=result["weight"];
				document.getElementById("jmoving_volume").innerHTML=result["volume"];
				document.getElementById("label_un_articles").innerHTML=result["label_empty"];

				JsHttpRequest.query($rcapi,{ 'w': 'showJmovingLocalCardStr', 'jmoving_id':jmoving_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
                    $("#jmoving_сhild_range").html(result.content);
                    numberOnly();
				}}, true);
				
				$("#FormModalWindow2").modal("hide");document.getElementById("FormModalBody2").innerHTML="";document.getElementById("FormModalLabel2").innerHTML="";
				$("#FormModalWindow").modal("hide");document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";
			}
			else{ toastr["error"](result["error"]); }
		}}, true);
	}
}

function setArticleToJmovingLocal(){
	var jmoving_id=$("#jmoving_id").val();
	var amount_move=parseFloat($('#amount_move').val());
	var pos=$("#row_pos").val();
	var max_value=parseFloat($("#amountMaxValue").val());
	if (max_value<amount_move){swal("Помилка!", "Кількість для переміщення більша за залишок!", "error");}
	if (jmoving_id.length>0 && pos>0 && parseFloat(amount_move)>0 && amount_move<=max_value){
		var idStr=$("#idStr_"+pos).val();
		var artIdStr=$('#art_idS2').val();
		var article_nr_displStr=$('#article_nr_displS2').val();
		var brandIdStr=$('#brand_idS2').val();
		var storageId=$('#storage_id_to').val();
		var cell_from_move=$('#cell_from_move option:selected').val(); 
		var cell_to_move=$('#cell_to_move option:selected').val(); 
		var amountStr=amount_move;
		if (cell_from_move==0){
			swal("Помилка!", "Оберіть коректно комірку відбору!", "error");
		}
		if (cell_from_move>0){
			JsHttpRequest.query($rcapi,{ 'w':'setArticleToJmovingLocal','jmoving_id':jmoving_id,'idStr':idStr,'artIdStr':artIdStr,'article_nr_displStr':article_nr_displStr,'brandIdStr':brandIdStr,'storageId':storageId,'cell_from_move':cell_from_move,'cell_to_move':cell_to_move,'amountStr':amountStr},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					document.getElementById("jmoving_weight").innerHTML=result["weight"];
					document.getElementById("jmoving_volume").innerHTML=result["volume"];
					document.getElementById("label_un_articles").innerHTML=result["label_empty"];
	
					JsHttpRequest.query($rcapi,{ 'w': 'showJmovingLocalCardStr', 'jmoving_id':jmoving_id},
					function (result, errors){ if (errors) {alert(errors);} if (result){
                        $("#jmoving_сhild_range").html(result.content);
						numberOnly();
					}}, true);
					
					$("#FormModalWindow2").modal("hide");document.getElementById("FormModalBody2").innerHTML="";document.getElementById("FormModalLabel2").innerHTML="";
					$("#FormModalWindow").modal("hide");document.getElementById("FormModalBody").innerHTML="";document.getElementById("FormModalLabel").innerHTML="";
					$("#row_pos").val("");
				}
				else{ toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function dropJmovingStr(pos,jmoving_id,jmoving_str_id){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0 && jmoving_str_id>0){
		swal({
			title: "Видалити відбір з переміщення?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так, видалити", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				if (jmoving_id.length>0){
					JsHttpRequest.query($rcapi,{ 'w':'dropJmovingStr', 'jmoving_id':jmoving_id, 'jmoving_str_id':jmoving_str_id},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"]==1){
							swal("Видалено!", "", "success");
							$("#strRow_"+pos).html("");
							$("#strRow_"+pos).attr('visibility','hidden');
							var kol_row=parseInt($("#kol_row").val());
							if (kol_row>0){
								$("#storage_id_to").attr("disabled","disabled");
								$("#storage_id_to").addClass("disabled");
							}
							if (kol_row==0){
								$("#storage_id_to").attr("disabled","");
								$("#storage_id_to").removeClass("disabled");
							}
						} else { swal("Помилка!", result["error"], "error");}
					}}, true);
				}
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
	}
}

function dropJmovingLocalStr(pos,jmoving_id,jmoving_str_id){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0 && jmoving_str_id>0){
		swal({
			title: "Видалити відбір з переміщення?",
			text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так, видалити", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				if (jmoving_id.length>0){
					JsHttpRequest.query($rcapi,{ 'w':'dropJmovingLocalStr', 'jmoving_id':jmoving_id, 'jmoving_str_id':jmoving_str_id},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"]==1){
							swal("Видалено!", "", "success");
							$("#strRow_"+pos).html("");
							$("#strRow_"+pos).attr('visibility','hidden');
						} else { swal("Помилка!", result["error"], "error");}
					}}, true);
				}
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
	}
}

function clearJmovingLocalAutoCellForm(){
	var jmoving_id=$("#jmoving_id").val();
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
		swal({
			title: "Очистити переміщення?",
			text: "Ви впевнені, що хочете очистити переміщення?!", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
			confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
		},
		function (isConfirm) {
			if (isConfirm) {
				if (jmoving_id.length>0){
					JsHttpRequest.query($rcapi,{ 'w':'clearJmovingLocalAutoCellForm', 'jmoving_id':jmoving_id},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"]==1){
							swal("Видалено!", "", "success");
							showJmovingCardLocal(jmoving_id);
						} else { swal("Помилка!", result["error"], "error");}
					}}, true);
				}
			} else {
				swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
			}
		});
	}
}

function showJmovingArticleAmountChange(pos,str_id,art_id,amount){
	let jmoving_id=$("#jmoving_id").val();
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0 && str_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showJmovingArticleAmountChange', 'art_id':art_id, 'str_id':str_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow2").modal("show");
			$("#FormModalBody2").html(result.content);
			$("#FormModalLabel2").html("Вкажіть кількість: "+result["article_nr_displ"]+" "+result["brand_name"]);
			numberOnlyPlace("amount_move_numbers");
		}}, true);
	}
}

function showJmovingArticleAmountLocalChange(pos,str_id,art_id,amount){
    let jmoving_id=$("#jmoving_id").val();
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0 && str_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showJmovingArticleAmountLocalChange', 'art_id':art_id, 'str_id':str_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalWindow2").modal("show");
            $("#FormModalBody2").html(result.content);
            $("#FormModalLabel2").html("Вкажіть кількість: "+result["article_nr_displ"]+" "+result["brand_name"]);
			numberOnlyPlace("amount_move_numbers");
		}}, true);
	}
}

function randString(id){
	var dataSet = $(id).attr('data-character-set').split(',');
	var possible = "";  var text = "";
	if($.inArray('a-z', dataSet) >= 0){possible += 'abcdefghijklmnopqrstuvwxyz';}
	if($.inArray('A-Z', dataSet) >= 0){possible += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';}
	if($.inArray('0-9', dataSet) >= 0){possible += '0123456789';}
	if($.inArray('#', dataSet) >= 0){possible += '![]{}()%&*$#^<>~@|';}
	for(var i=0; i < $(id).attr('data-size'); i++) {text += possible.charAt(Math.floor(Math.random() * possible.length));}
	$(id).val(""+text);
	return text;
}

function loadJmovingCommetsLabel(jmoving_id){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingCommetsLabel', 'jmoving_id':jmoving_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#label_comments").html(result["label"]);
		}}, true);
	}
}

function loadJmovingCommets(jmoving_id){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingCommets', 'jmoving_id':jmoving_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#jmoving_commets_place").html(result.content);
        }}, true);
	}
}

function saveJmovingComment(jmoving_id){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
        let comment=$("#jmoving_comment_field").val();
		if (comment.length<=0){toastr["error"]("Напишіть коментар спочатку");}
		if (comment.length>0){
			JsHttpRequest.query($rcapi,{ 'w': 'saveJmovingComment', 'jmoving_id':jmoving_id, 'comment':comment}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					loadJmovingCommets(jmoving_id); 
					$("#jmoving_comment_field").val("");
					loadJmovingCommetsLabel(jmoving_id);
				} else { toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function dropJmovingComment(jmoving_id,cmt_id){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
		if(confirm('Видалити запис?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'dropJmovingComment', 'jmoving_id':jmoving_id, 'cmt_id':cmt_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadJmovingCommets(jmoving_id);
					toastr["info"]("Запис успішно видалено");
					loadJmovingCommetsLabel(jmoving_id);
				} else { toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function loadJmovingCDN(jmoving_id){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingCDN', 'jmoving_id':jmoving_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#jmoving_storage_select_place").html(result.content);
        }}, true);
	}
}

function showJmovingCDNUploadForm(jmoving_id){
	$("#cdn_jmoving_id").val(jmoving_id);
	var myDropzone2 = new Dropzone("#myDropzone2",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
	myDropzone2.removeAllFiles(true);
	myDropzone2.on("queuecomplete", function() {
		toastr["info"]("Завантаження файлів завершено.");
		this.removeAllFiles();
		$("#fileJmovingCDNUploadForm").modal("hide");
		loadJmovingCDN(jmoving_id);
	});
}

function showJmovingCDNDropConfirmForm(jmoving_id,file_id,file_name){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
		if(confirm('Видалити файл '+file_name+'?')){ 
			JsHttpRequest.query($rcapi,{ 'w': 'jmovingCDNDropFile', 'jmoving_id':jmoving_id, 'file_id':file_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadJmovingCDN(jmoving_id);
					toastr["info"]("Файл успішно видалено");
				} else { toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function showCountryManual(){
	let country_id=$("#country_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCountryManual', 'country_id':country_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CountryModalWindow").modal("show");
        $("#CountryBody").html(result.content);
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
        $("#FormModalBody").html(result.content);
        $("#FormModalLabel").html(result.header);
    }}, true);
}

function showCostumsManual(){
	let costums_id=$("#costums_id").val();
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsManual', 'costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#CostumsModalWindow").modal("show");
        $("#CostumsBody").html(result.content);
		setTimeout(function(){
			$("#datatable_costums").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[10, 20, 100, -1], [10, 20, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}, 500);
	}}, true);
}

function selectCostums(id,name){
	$("#costums_id").val(id);
	$("#costums_name").val(name);
	$("#CostumsModalWindow").modal("hide");
}

function showCostumsForm(costums_id){
	JsHttpRequest.query($rcapi,{ 'w':'showCostumsForm', 'costums_id':costums_id},
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#FormModalWindow").modal("show");
        $("#FormModalBody").html(result.content);
        $("#FormModalLabel").html(result.header);
	}}, true);
}

function makeJmovingCardFinish(){
    let jmoving_id=$("#jmoving_id").val();
	swal({
		title: "Провести накладну і зафіксувати?",
		text: "Подальше внесення змін у будь який розділ накладної буде заблоковано", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Провести", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (jmoving_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'makeJmovingCardFinish', 'jmoving_id':jmoving_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Накладну проведено", "success");
						showJmovingCard(jmoving_id)
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function loadJmovingUnknownArticles(jmoving_id){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingUnknownArticles', 'jmoving_id':jmoving_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#unknown_articles_place").html(result.content);
			numberOnlyLong();
			$("#income_tabs").tab();
		}}, true);
	}
}

function checkJmovingUnStr(jmoving_id,pos,art_id){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0 && art_id>0){
		var article_nr_displ=$("#article_nr_displUnStr_"+pos).val();
		var volume=$("#volumeUnStr_"+pos).val();
		var weight=$("#weightNettoUnStr_"+pos).val();
		var weight2=$("#weightBruttoUnStr_"+pos).val();
		if (art_id>0 && volume>0 && weight>0 && weight2>0){
			JsHttpRequest.query($rcapi,{ 'w': 'checkJmovingUnStr', 'jmoving_id':jmoving_id, 'art_id':art_id, 'volume':volume, 'weight':weight, 'weight2':weight2},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					swal("Успішно!", "Артикул "+article_nr_displ+" присвоєно значення ваги та об'єму!", "success");
					$("#strUnRow_"+pos).html("");
					$("#strUnRow_"+pos).attr('visibility','hidden');
					//informer update
					updateInformerUnknownArticles(jmoving_id);
				}
			}}, true);
		} else {
			swal("Помилка!", "Не заповнені всі поля для артикулу "+article_nr_displ+"!", "error");
		}
	}
}

function startJmovingStorageSelect(){
	$("#jmoving_start").attr("disabled", true);
	let jmoving_id=$("#jmoving_id").val();
	if (jmoving_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'startJmovingStorageSelect', 'jmoving_id':jmoving_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				swal.close();
				loadJmovingStorageSelect2(jmoving_id,44);
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function startJmovingStorageSelectLocal(){
	$("#jmoving_send").attr("disabled", true);
	var jmoving_id=$("#jmoving_id").val();
	var jmoving_op_id=$("#jmoving_op_id option:selected").val();
	var data=$("#jmoving_data").val();
	var storage_id_to=$("#storage_id_to option:selected").val();
	var comment=$("#jmoving_comment").val();
	var ikr=$("#kol_row").val();
	var idStr=[];var artIdStr=[]; var cellIdToStr=[];
	var cell_er=0;
	for (var i=1;i<=ikr;i++){
		idStr[i]=$("#idStr_"+i).val(); artIdStr[i]=$("#artIdStr_"+i).val(); cellIdToStr[i]=$("#cellIdTo_"+i+" option:selected").val();
		if (idStr[i]>0 && (cellIdToStr[i]==0 || cellIdToStr[i]=="")){cell_er=1;}
	}
	if (jmoving_id.length>0 && cell_er==1){swal("Помилка!", "Оберіть комірку мереміщення для всіх артикулів", "error");}
	if (jmoving_id.length>0 && cell_er==0){
		JsHttpRequest.query($rcapi,{ 'w':'saveJmovingCardLocal','jmoving_id':jmoving_id,'jmoving_op_id':jmoving_op_id,'data':data,'storage_id_to':storage_id_to,'comment':comment,'kol_row':ikr,'idStr':idStr,'artIdStr':artIdStr,'cellIdToStr':cellIdToStr},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				JsHttpRequest.query($rcapi,{ 'w':'startJmovingStorageSelectLocal','jmoving_id':jmoving_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal.close();
						loadJmovingStorageSelect2Local(jmoving_id,44);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function loadJmovingStorageSelect2(jmoving_id,jmoving_status){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0 && jmoving_status>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingStorageSelect', 'jmoving_id':jmoving_id, 'jmoving_status':jmoving_status},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#JmovingPreSelect").modal("show");
			$("#JmovingPreSelectBody").html(result.content);
			$("#JmovingPreSelectLabel").html("Попередній перегляд");
		}}, true);
	}
}

function loadJmovingStorageSelect2Local(jmoving_id,jmoving_status){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0 && jmoving_status>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingStorageSelectLocal', 'jmoving_id':jmoving_id, 'jmoving_status':jmoving_status},
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#JmovingPreSelect").modal("show");
            $("#JmovingPreSelectBody").html(result.content);
            $("#JmovingPreSelectLabel").html("Попередній перегляд");
		}}, true);
	}
}

function makesJmovingStorageSelect(){
	$("#jmoving_make").attr("disabled", true);
	let jmoving_id=$("#jmoving_id").val();
	swal({
		title: "Передати в роботу переміщення складу?",
		text: "Подальше внесення змін буде заблоковано", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (jmoving_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'makesJmovingStorageSelect', 'jmoving_id':jmoving_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Переміщення передано в роботу", "success");
						showJmovingCard(jmoving_id);
                        $("#JmovingPreSelect").modal("hide");
                        $("#JmovingPreSelectBody").html("");
                        $("#JmovingPreSelectLabel").html("");
						show_jmoving_search(0);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function makesJmovingStorageSelectLocal(){
	let jmoving_id=$("#jmoving_id").val();
	swal({
		title: "Передати в роботу внутрішнє переміщення складу?",
		text: "Подальше внесення змін буде заблоковано", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (jmoving_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'makesJmovingStorageSelectLocal', 'jmoving_id':jmoving_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Переміщення передано в роботу", "success");
						showJmovingCardLocal(jmoving_id);
                        $("#JmovingPreSelect").modal("hide");
                        $("#JmovingPreSelectBody").html("");
                        $("#JmovingPreSelectLabel").html("");
						show_jmoving_search(0);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function clearJmovingStorageSelect(){
	let jmoving_id=$("#jmoving_id").val();
	if (jmoving_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'clearJmovingStorageSelect', 'jmoving_id':jmoving_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
                $("#JmovingPreSelect").modal("hide");
                $("#JmovingPreSelectBody").html("");
                $("#JmovingPreSelectLabel").html("");
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function clearJmovingStorageSelectLocal(){
	let jmoving_id=$("#jmoving_id").val();
	if (jmoving_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'clearJmovingStorageSelectLocal', 'jmoving_id':jmoving_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
                $("#JmovingPreSelect").modal("hide");
                $("#JmovingPreSelectBody").html("");
                $("#JmovingPreSelectLabel").html("");
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function updateInformerUnknownArticles(jmoving_id){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'updateInformerJmovingUnknownArticles', 'jmoving_id':jmoving_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#label_un_articles").html(result.content);
        }}, true);
	}
}

function loadJmovingStorageSelect(jmoving_id){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingStorageSelect', 'jmoving_id':jmoving_id, 'jmoving_status':45},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#jmoving_storage_select_place").html(result.content);
            $("#income_tabs").tab();
		}}, true);
	}
}

function loadJmovingStorageSelectLocal(jmoving_id){
	if (jmoving_id<=0 || jmoving_id==""){toastr["error"](errs[0]);}
	if (jmoving_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingStorageSelectLocal', 'jmoving_id':jmoving_id, 'jmoving_status':45},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#jmoving_storage_select_place").html(result.content);
			$("#income_tabs").tab();
		}}, true);
	}
}

function viewJmovingStorageSelect(jmoving_id,select_id,jmoving_status){
	if (jmoving_id<=0 || jmoving_id=="" || select_id=="" || select_id==0){toastr["error"](errs[0]);}
	if (jmoving_id>0 && select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'viewJmovingStorageSelect', 'jmoving_id':jmoving_id, 'select_id':select_id, 'jmoving_status':jmoving_status},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow2").modal("show");
			$("#FormModalBody2").html(result.content);
			$("#FormModalLabel2").html(result.header);
			$("#income_tabs").tab();
        }}, true);
	}
}

function viewJmovingStorageSelectLocal(jmoving_id,select_id,jmoving_status){
	if (jmoving_id<=0 || jmoving_id=="" || select_id=="" || select_id==0){toastr["error"](errs[0]);}
	if (jmoving_id>0 && select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'viewJmovingStorageSelectLocal', 'jmoving_id':jmoving_id, 'select_id':select_id, 'jmoving_status':jmoving_status},
		function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalWindow2").modal("show");
            $("#FormModalBody2").html(result.content);
            $("#FormModalLabel2").html(result.header);
			//$("#income_tabs").tab();
		}}, true);
	}
}

function dropJmovingStorageSelect(jmoving_id,select_id){
	swal({
		title: "Видалити складський відбір?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (jmoving_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropJmovingStorageSelect', 'jmoving_id':jmoving_id, 'select_id':select_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadJmovingStorageSelect2(jmoving_id,44);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function dropJmovingStorageSelectLocal(jmoving_id,select_id){
	swal({
		title: "Видалити складський відбір?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (jmoving_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'dropJmovingStorageSelectLocal', 'jmoving_id':jmoving_id, 'select_id':select_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "", "success");
						loadJmovingStorageSelect2Local(jmoving_id,44);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function collectJmovingStorageSelect(jmoving_id,select_id){
	swal({
		title: "Розпочати збирання складського відбору?",
		text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (jmoving_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'collectJmovingStorageSelect', 'jmoving_id':jmoving_id, 'select_id':select_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Зафіксовано!", "", "success");
						loadJmovingStorageSelect(jmoving_id);
						viewJmovingStorageSelect(jmoving_id,select_id,46);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function collectJmovingStorageSelectLocal(jmoving_id,select_id){
	swal({
		title: "Розпочати збирання складського відбору?",
		text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Відмінити", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (jmoving_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'collectJmovingStorageSelectLocal', 'jmoving_id':jmoving_id, 'select_id':select_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Зафіксовано!", "", "success");
						viewJmovingStorageSelectLocal(jmoving_id,select_id,46);
						loadJmovingStorageSelectLocal(jmoving_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function cutJmovingStorageAll(){
	$("#jmoving_cut").attr("disabled", true);
    let jmoving_id=$("#jmoving_id").val();
	var select_id=[];
	$(".select_id").each(function() {
		select_id.push(this.id);
	});
    let comment=$("#jmoving_comment").val();
	swal({
		title: "Відділити складський відбори у окремі документи переміщення?",
		text: "Будуть сформовані нові документи переміщення", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Відділити", cancelButtonText: "Скасувати", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (jmoving_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'cutJmovingStorageAll', 'jmoving_id':jmoving_id, 'select_id':select_id, 'comment':comment},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Сформовано нові документи переміщення!", "", "success");
						var jmovArray=result["ids"];
						console.log(jmovArray);
						var jmov_id=0;
						for (var i = 0; i < jmovArray.length; i++) {
							console.log(jmovArray[i]);
							jmov_id=parseInt(jmovArray[i]);
							startJmovingStorageSelectAll(jmov_id);				
						}
						loadJmovingStorageSelect2(jmoving_id,44);
						showJmovingCard(jmoving_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function makesJmovingStorageSelectAll(jmoving_id){
	JsHttpRequest.query($rcapi,{ 'w':'makesJmovingStorageSelect', 'jmoving_id':jmoving_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				console.log(jmoving_id+" - сформовано");
			} else {
				console.log(jmoving_id+" - не сформовано!");
            }
	}}, true);	
}

function startJmovingStorageSelectAll(jmoving_id){
	JsHttpRequest.query($rcapi,{ 'w':'startJmovingStorageSelect', 'jmoving_id':jmoving_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				console.log(jmoving_id+" - збережено");
				setTimeout(function(){makesJmovingStorageSelectAll(jmoving_id);},500);		
			} else {
				console.log(jmoving_id+" - не збережено!");
            }
	}}, true);	
}

function cutJmovingStorage(jmoving_id,select_id){
	swal({
		title: "Відділити складський відбір у окремий документ переміщення?",
		text: "Буде сформовано новий документ переміщення", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Відділити", cancelButtonText: "Скасувати", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (jmoving_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'cutJmovingStorage', 'jmoving_id':jmoving_id, 'select_id':select_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Сформовано новий документ переміщення!", "", "success");
						loadJmovingStorageSelect2(jmoving_id,44);
						showJmovingCard(jmoving_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});
}

function printJmovingStorageSelectView(jmoving_id,select_id){
	if (jmoving_id.length>0 && select_id.length>0){
		window.open("/Jmoving/printJmS1/"+jmoving_id+"/"+select_id,"_blank","printWindow");
	}
}

function printJmovingStorageSelectViewLocal(jmoving_id,select_id){
	if (jmoving_id.length>0 && select_id.length>0){
		window.open("/Jmoving/printJmS1L/"+jmoving_id+"/"+select_id,"_blank","printWindow");
	}
}

function showJmovingStorageSelectBarcodeForm(jmoving_id,select_id){
	if (jmoving_id.length>0 && select_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showJmovingStorageSelectBarcodeForm', 'jmoving_id':jmoving_id, 'select_id':select_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#FormModalWindow4").modal("show");
				$("#FormModalBody4").html(result.content);
				$("#FormModalLabel4").html(result.header);
				loadJmovingStorageSelect(jmoving_id);
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function saveJmovingStorageSelectBarcodeForm(jmoving_id,select_id){
    let barcode=$("#BarCodeInput").val();
	if (jmoving_id.length>0 && select_id.length>0 && barcode.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveJmovingStorageSelectBarcodeForm', 'jmoving_id':jmoving_id, 'select_id':select_id, 'barcode':barcode},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#BarCodeInput").val("");
				$("#BarCodeInput").focus();
				$("#amr_"+result["row_id"]).html(result["amount_barcode"]);
				$("#amrd_"+result["row_id"]).html(result["dif_amount_barcode"]);
				document.getElementById('ok_art_id').play();
			} else{
				swal("Помилка!", result["error"], "error");
				document.getElementById('no_art_id').play();
			}
		}}, true);
	}
}

function finishJmovingStorageSelectBarcodeForm(jmoving_id,select_id){
	if (jmoving_id.length>0 && select_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'finishJmovingStorageSelectBarcodeForm', 'jmoving_id':jmoving_id, 'select_id':select_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				loadJmovingStorageSelect(jmoving_id);
                $("#FormModalWindow4").modal("hide");
                $("#FormModalBody4").html("");
                $("#FormModalLabel4").html("");
				viewJmovingStorageSelect(jmoving_id,select_id,result["status_jmoving"])
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function finishJmovingLocalStorageSelect(jmoving_id,select_id){
	if (jmoving_id.length>0 && select_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'finishJmovingLocalStorageSelect', 'jmoving_id':jmoving_id, 'select_id':select_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				loadJmovingStorageSelectLocal(jmoving_id);
                $("#FormModalWindow4").modal("hide");
                $("#FormModalBody4").html("");
                $("#FormModalLabel4").html("");
				viewJmovingStorageSelectLocal(jmoving_id,select_id,result["status_jmoving"])
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function showJmovingStorageSelectSendTruckForm(jmoving_id){
	$("#jmoving_send").attr("disabled", true);
	swal({
		title: "Передати складські вібдори на відправку?",
		text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Перадати", cancelButtonText: "Скасувати", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (jmoving_id.length>0){
//				var jmoving_type_id=$("#jmoving_type_id").val();
//				if (jmoving_id>0){
//					if (jmoving_type_id==0){saveJmovingCardLocal(jmoving_id)}
//					if (jmoving_type_id==1){saveJmovingCard(jmoving_id);}
//				}
				JsHttpRequest.query($rcapi,{ 'w':'setJmovingSendTruck','jmoving_id':jmoving_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						showJmovingCard(jmoving_id);
						swal.close();
						window.open("/Jmoving/printJmSTP/"+jmoving_id,"_blank","printWindow");
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "", "error");
		}
	});
}

function showJmovingStorageSelectBugForm(jmoving_id,select_id,str_id){
	if (jmoving_id.length>0 && select_id.length>0 && str_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showJmovingStorageSelectBugForm', 'jmoving_id':jmoving_id, 'select_id':select_id, 'str_id':str_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
                $("#FormModalWindow5").modal("show");
                $("#FormModalBody5").html(result.content);
                $("#FormModalLabel5").html(result.header);
				numberOnly();
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function saveJmovingStorageSelectBugForm(jmoving_id,select_id,str_id){
	if (jmoving_id.length>0 && select_id.length>0 && str_id.length>0){
		var err=0; 
		var storage_select_bug=$("#storage_select_bug option:selected").val();
		var amount=parseFloat($("#bug_amount").val());
		var dif_amount_barcode=parseFloat($("#bug_dif_amount_barcode").val());
		if (storage_select_bug<=0 || storage_select_bug.length==0){er=1;swal("Помилка!", "Оберіть тип відхилення", "error");}
		if (dif_amount_barcode<=0 || dif_amount_barcode==0){er=1;swal("Помилка!", "Не вказано кількість відхилення", "error");}
		if (dif_amount_barcode>amount){er=1;swal("Помилка!", "Кількість відхилення не повинна перевищувати кількісті відбору", "error");}
		if (err==0){
			swal({
				title: "Зафіксувати складське відхилення?",
				text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
				confirmButtonText: "Зафіксувати", cancelButtonText: "Скасувати", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
			},
			function (isConfirm) {
				if (isConfirm) {
					JsHttpRequest.query($rcapi,{ 'w':'saveJmovingStorageSelectBugForm','jmoving_id':jmoving_id,'select_id':select_id,'str_id':str_id,'storage_select_bug':storage_select_bug,'dif_amount_barcode':dif_amount_barcode},
					function (result, errors){ if (errors) {alert(errors);} if (result){  
						if (result["answer"]==1){ 
							loadJmovingStorageSelect(jmoving_id);
							viewJmovingStorageSelect(jmoving_id,select_id);
							swal.close();
                            $("#FormModalWindow5").modal("hide");
                            $("#FormModalBody5").html("");
                            $("#FormModalLabel5").html("");
							$("#BarCodeInput").val("");
							$("#BarCodeInput").focus();
							$("#ssbug_"+result["row_id"]).html(result["storage_select_bug_name"]);
							$("#amrd_"+result["row_id"]).html(result["dif_amount_barcode"]);
							$("#ambg_"+result["row_id"]).html(result["amount_bug"]);
							$("#amr_"+result["row_id"]).html(result["amount_barcode"]);
							$("#amrns_"+result["row_id"]).html(result["amount_barcode_noscan"]);
						} else { swal("Помилка!", result["error"], "error");}
					}}, true);
				} else {swal("Відмінено", "", "error");}
			});
		}
	}	
}

function showJmovingAcceptBugForm(jmoving_id,str_id){
	if (jmoving_id.length>0 && str_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showJmovingAcceptBugForm', 'jmoving_id':jmoving_id, 'str_id':str_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
                $("#FormModalWindow5").modal("show");
                $("#FormModalBody5").html(result.content);
                $("#FormModalLabel5").html(result.header);
				numberOnly();
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function saveJmovingAcceptBugForm(jmoving_id,str_id){
	if (jmoving_id.length>0 && str_id.length>0){
		var err=0; 
		var storage_select_bug=$("#storage_select_bug option:selected").val();
		var amount=parseFloat($("#bug_amount").val());
		var dif_amount_barcode=parseFloat($("#bug_dif_amount_barcode").val());
		if (storage_select_bug<=0 || storage_select_bug.length==0){er=1;swal("Помилка!", "Оберіть тип відхилення", "error");}
		if (dif_amount_barcode<=0 || dif_amount_barcode==0){er=1;swal("Помилка!", "Не вказано кількість відхилення", "error");}
		if (dif_amount_barcode>amount){er=1;swal("Помилка!", "Кількість відхилення не повинна перевищувати кількісті відбору", "error");}
		if (err==0){
			swal({
				title: "Зафіксувати складське відхилення?",
				text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
				confirmButtonText: "Зафіксувати", cancelButtonText: "Скасувати", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
			},
			function (isConfirm) {
				if (isConfirm) {
					JsHttpRequest.query($rcapi,{ 'w':'saveJmovingAcceptBugForm','jmoving_id':jmoving_id,'str_id':str_id,'storage_select_bug':storage_select_bug,'dif_amount_barcode':dif_amount_barcode},
					function (result, errors){ if (errors) {alert(errors);} if (result){  
						if (result["answer"]==1){ 
							swal.close();
                            $("#FormModalWindow5").modal("hide");
                            $("#FormModalBody5").html("");
                            $("#FormModalLabel5").html("");
							$("#BarCodeInput").val("");
							$("#BarCodeInput").focus();
							$("#ssbug_"+result["row_id"]).html(result["storage_bug_name"]);
							$("#amrd_"+result["row_id"]).html(result["dif_amount_barcode"]);
							$("#ambg_"+result["row_id"]).html(result["amount_bug"]);
						} else { swal("Помилка!", result["error"], "error");}
					}}, true);
				} else {swal("Відмінено", "", "error");}
			});
		}
	}	
}

function showJmovingStorageAcceptForm(jmoving_id){
	$("#jmoving_take").attr("disabled", true);
	if (jmoving_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showJmovingStorageAcceptForm', 'jmoving_id':jmoving_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
                $("#FormModalWindow4").modal("show");
                $("#FormModalBody4").html(result.content);
                $("#FormModalLabel4").html(result.header);
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function saveJmovingAcceptBarcodeForm(jmoving_id,select_id){
	let barcode=$("#BarCodeInput").val();
	if (jmoving_id.length>0 && barcode.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveJmovingAcceptBarcodeForm', 'jmoving_id':jmoving_id, 'barcode':barcode},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#BarCodeInput").val("");
				$("#BarCodeInput").focus();
				$("#amr_"+result["row_id"]).html(result["amount_barcode"]);
				$("#amrd_"+result["row_id"]).html(result["dif_amount_barcode"]);
				document.getElementById('ok_art_id').play();
			} else {
				swal("Помилка!", result["error"], "error");
				document.getElementById('no_art_id').play();
			}
		}}, true);
	}
}

function separateJmovingByDefect(jmoving_id) {
	swal({
		title: "Створити зворотне переміщення для відхилених позицій?",
		text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так", cancelButtonText: "Ні", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {			
			JsHttpRequest.query($rcapi,{ 'w':'separateJmovingByDefect', 'jmoving_id':jmoving_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal.close();
						showJmovingCard(jmoving_id);
                        $("#FormModalWindow4").modal("hide");
                        $("#FormModalBody4").html("");
                        $("#FormModalLabel4").html("");
						show_jmoving_search(0);
					} else { swal("Помилка!", result["error"], "error");}
			}}, true);
		}
		else { swal("Відміна!", "", "error");}
	});	
}

function checkJmovingBugs(jmoving_id) {
	JsHttpRequest.query($rcapi,{ 'w':'checkJmovingBugs', 'jmoving_id':jmoving_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if(result["content"]==1) {
				separateJmovingByDefect(jmoving_id);
			}
			else { swal("Завершено!", "", "success");}
	}}, true);
}

function scanJmovingAcceptForm(jmoving_id) {
    swal({
            title: "Сканувати всі індекси?",
            text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "Завершити", cancelButtonText: "Відміна", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                JsHttpRequest.query($rcapi,{ 'w':'scanJmovingAcceptForm', 'jmoving_id':jmoving_id},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        if (result["answer"]==1){
                            swal.close();
                            $("#FormModalWindow4").modal("hide");
                            showJmovingCard(jmoving_id);
                        } else { swal("Помилка!", result["error"], "error");}
                    }}, true);
            } else {swal("Відмінено!", "", "error");}
        });
}
	
function finishJmovingAcceptForm(jmoving_id){
	swal({
		title: "Завершити прийняття перемішення?",
		text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Завершити", cancelButtonText: "Відміна", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w':'finishJmovingAcceptForm', 'jmoving_id':jmoving_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal.close();
						showJmovingCard(jmoving_id);
                        $("#FormModalWindow4").modal("hide");
                        $("#FormModalBody4").html("");
                        $("#FormModalLabel4").html("");
						show_jmoving_search(0);
						checkJmovingBugs(jmoving_id);
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
		} else {swal("Відмінено!", "", "error");}
	});	
}

function finishJmovingLocalAcceptForm(jmoving_id){
	$("#jmoving_complete").attr("disabled", true);
	if (jmoving_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'finishJmovingLocalAcceptForm', 'jmoving_id':jmoving_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				showJmovingCardLocal(jmoving_id);
				swal("Виконано!", "", "info");
				show_jmoving_search(0);
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function showJmovingStorageSelectNoscanForm(jmoving_id,select_id,art_id,str_id){
	if (jmoving_id.length>0 && select_id.length>0 && art_id.length>0 && str_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showJmovingStorageSelectNoscanForm', 'jmoving_id':jmoving_id, 'select_id':select_id, 'str_id':str_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
                $("#FormModalWindow5").modal("show");
                $("#FormModalBody5").html(result.content);
                $("#FormModalLabel5").html(result.header);
				numberOnly();
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function saveJmovingStorageSelectNoscanForm(jmoving_id,select_id,art_id,str_id){
	if (jmoving_id.length>0 && select_id.length>0 && art_id.length>0 && str_id.length>0){
		var err=0; 
		var amount=parseFloat($("#amount_barcode_noscan").val());
		var dif_amount_barcode=parseFloat($("#noscan_dif_amount_barcode").val());
		if (dif_amount_barcode<=0 || dif_amount_barcode==0){er=1;swal("Помилка!", "Не вказано кількість без сканування", "error");}
		if (dif_amount_barcode>amount){er=1;swal("Помилка!", "Кількість без сканування не повинна перевищувати кількості для приймання", "error");}
		if (err==0){
			swal({
				title: "Зафіксувати пакування товару без сканування штрих-коду?",
				text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
				confirmButtonText: "Зафіксувати", cancelButtonText: "Скасувати", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
			},
			function (isConfirm) {
				if (isConfirm) {
					JsHttpRequest.query($rcapi,{ 'w':'saveJmovingStorageSelectNoscanForm','jmoving_id':jmoving_id,'select_id':select_id,'art_id':art_id,'str_id':str_id,'amount_barcode_noscan':amount},
					function (result, errors){ if (errors) {alert(errors);} if (result){  
						if (result["answer"]==1){ 
							swal.close();
                            $("#FormModalWindow5").modal("hide");
                            $("#FormModalBody5").html("");
                            $("#FormModalLabel5").html("");
							$("#BarCodeInput").val("");
							$("#BarCodeInput").focus();
							$("#amrd_"+result["row_id"]).html(result["dif_amount_barcode"]);
							$("#amrns_"+result["row_id"]).html(result["amount_barcode_noscan"]);
						} else { swal("Помилка!", result["error"], "error");}
					}}, true);
				} else {swal("Відмінено", "", "error");}
			});
		}
	}
}

function showJmovingAcceptNoscanForm(jmoving_id,art_id,str_id){
	if (jmoving_id.length>0 && art_id.length>0 && str_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showJmovingAcceptNoscanForm','jmoving_id':jmoving_id,'str_id':str_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){
                $("#FormModalWindow5").modal("show");
                $("#FormModalBody5").html(result.content);
                $("#FormModalLabel5").html(result.header);
				numberOnly();
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
	}
}

function saveJmovingAcceptNoscanForm(jmoving_id,art_id,str_id){
	if (jmoving_id.length>0 && art_id.length>0 && str_id.length>0){
		var err=0; 
		var amount=parseFloat($("#amount_barcode_noscan").val());
		var dif_amount_barcode=parseFloat($("#noscan_dif_amount_barcode").val());
		if (dif_amount_barcode<=0 || dif_amount_barcode==0){er=1;swal("Помилка!", "Не вказано кількість без сканування", "error");}
		if (dif_amount_barcode>amount){er=1;swal("Помилка!", "Кількість без сканування не повинна перевищувати кількості для приймання", "error");}
		if (err==0){
			swal({
				title: "Зафіксувати приймання надходження без сканування?",
				text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
				confirmButtonText: "Зафіксувати", cancelButtonText: "Скасувати", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
			},
			function (isConfirm) {
				if (isConfirm) {
					JsHttpRequest.query($rcapi,{ 'w':'saveJmovingAcceptNoscanForm','jmoving_id':jmoving_id,'art_id':art_id,'str_id':str_id,'amount_barcode_noscan':amount},
					function (result, errors){ if (errors) {alert(errors);} if (result){  
						if (result["answer"]==1){ 
							swal.close();
                            $("#FormModalWindow5").modal("hide");
                            $("#FormModalBody5").html("");
                            $("#FormModalLabel5").html("");
							$("#BarCodeInput").val("");
							$("#BarCodeInput").focus();
							$("#amrd_"+result["row_id"]).html(result["dif_amount_barcode"]);
							$("#amrns_"+result["row_id"]).html(result["amount_barcode_noscan"]);
						} else { swal("Помилка!", result["error"], "error");}
					}}, true);
				} else {swal("Відмінено", "", "error");}
			});
		}
	}	
}

function cancelJmoving() {
    let jmoving_id=$("#jmoving_id").val();
	swal({
		title: "Анулювати переміщення?",
		type: "danger", text: "Увага! Переміщення буде видалено!", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394", confirmButtonText: "Підтвердити", cancelButtonText: "Скасувати", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			JsHttpRequest.query($rcapi,{ 'w':'cancelJmoving','jmoving_id':jmoving_id},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				if (result["answer"]==1){
					swal("Видалено!", "Дані успішно анульовані.", "success");
					$("#JmovingCard").modal("hide");
					updateJmovingRange();
				} else { swal("Помилка!", result["error"], "error");}
			}}, true);
		} else {swal("Відмінено", "", "error");}
	});
}

/* ===== IMPORT ===== */

function showCsvUploadForm(jmoving_id) {
    $("#fileJmovingCsvUploadForm").modal("show");
    $("#csv_jmoving_id").val(jmoving_id);
    var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "Натисніть для вибору файлів або перетягніть їх це поле!" });
    myDropzone3.removeAllFiles(true);
    myDropzone3.on("queuecomplete", function() {
        this.removeAllFiles();
        $("#fileJmovingCsvUploadForm").modal("hide");
        loadJmovingImport(jmoving_id);
    });
}

function saveCsvJmovingImport(jmoving_id) {
    let start_row=parseInt($("#csv_from_row").val());
    let kol_cols=parseInt($("#kol_cols").val());
    let cls_kol=3;
    if (start_row<0 || start_row.length<=0) { swal("Помилка!", "Не вказано початковий ряд зчитування", "error");}
    if (start_row>=0) {
        var cols=[];var cl=0; var cls_sel=0;
        for (var i=1;i<=kol_cols;i++){
            cl=$("#clm-"+i+" option:selected").val();
            if (cl>0){cls_sel+=1; cols[i]=cl;}
        }
        if (cls_sel<cls_kol){swal("Помилка!", "Не вказані усі значення колонок", "error");}
        else {
            JsHttpRequest.query($rcapi,{ 'w':'saveCsvJmovingImport', 'jmoving_id':jmoving_id, 'start_row':start_row, 'kol_cols':kol_cols, 'cols':cols},
                function (result, errors){ if (errors) {alert(errors);} if (result){
                    if (result["answer"]==1){
                        $("#FormModalWindow").modal("hide");
                        swal("Імпорт даних завершено!", "Перевірте нові дані.", "success");
                        loadJmovingImport(jmoving_id);
                    } else { swal("Помилка!", result["error"], "error");}
                }}, true);
        }
    }
}

function clearJmovingImport(jmoving_id) {
    JsHttpRequest.query($rcapi,{ 'w':'clearJmovingImport', 'jmoving_id':jmoving_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"]==1){
                loadJmovingImport(jmoving_id);
            } else { swal("Помилка!", result["error"], "error");}
        }}, true);
}

function finishJmovingImport(jmoving_id){
    JsHttpRequest.query($rcapi,{ 'w':'finishJmovingImport', 'jmoving_id':jmoving_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            if (result["answer"]==1){
                swal("Імпорт даних завершено!", "Перевірте нові дані.", "success");
                $("#FormModalWindow").modal("hide");
                showJmovingCard(jmoving_id);
            } else { swal("Помилка!", result["error"], "error");}
        }}, true);
}

function loadJmovingImport(jmoving_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingImport', 'jmoving_id':jmoving_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#jmoving_import").html(result.content);
        }}, true);
}

function loadTablePreview(jmoving_id) {
    let brands = $("#csv_brands option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingTablePreview', 'jmoving_id':jmoving_id, 'brands':brands},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#range_table_import").html(result.content);
        }}, true);
}

function saveTablePreview(jmoving_id) {
    let brands = $("#csv_brands option:selected").val();
    JsHttpRequest.query($rcapi,{ 'w': 'saveJmovingTablePreview', 'jmoving_id':jmoving_id, 'brands':brands},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            loadJmovingImport(jmoving_id);
        }}, true);
}

function loadJmovingUnknown(jmoving_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingUnknown', 'jmoving_id':jmoving_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#jmoving_unknown").html(result.content);
        }}, true);
}

function clearJmovingUnknown(jmoving_id) {
	JsHttpRequest.query($rcapi,{ 'w':'clearJmovingUnknown', 'jmoving_id':jmoving_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"]==1){
                loadJmovingUnknown(jmoving_id);
			} else { swal("Помилка!", result["error"], "error");}
		}}, true);
}

/* END IMPORT */

function showStorageFieldsViewForm(jmoving_id){
    JsHttpRequest.query($rcapi,{ 'w':'showJmovingStorageFieldsViewForm', 'jmoving_id':jmoving_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            $("#FormModalWindow").modal("show");
            $("#FormModalBody").html(result["content"]);
            $("#FormModalLabel").html("Склади");
            $(".table-sortable tbody").sortable({
                handle: "span",
                stop: function(event, ui) {},
                update: function (event, ui) {
                    var data = $(this).sortable("serialize").toString();
                }
            });
        }}, true);
}

function saveJmovingStorageFieldsViewForm() {
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
        JsHttpRequest.query($rcapi,{ 'w':'saveJmovingStorageFieldsViewForm', 'kol_fields':kol_fields, 'fl_id':fl_id, 'fl_ch':fl_ch},
            function (result, errors){ if (errors) {alert(errors);} if (result){
                if (result["answer"]==1){
                    swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
                    $("#FormModalWindow").modal("hide");
                    let dp_id=$("#dp_id").val();
                    loadDpImport(dp_id);
                } else { swal("Помилка!", result["error"], "error"); }
            }}, true);
    }
}

function dropJmovingCard(jmoving_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'dropJmovingCard', 'jmoving_id':jmoving_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
            $("#JmovingCard").modal("hide");
        }}, true);
}
