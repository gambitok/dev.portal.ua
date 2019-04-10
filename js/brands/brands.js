var errs=[];
errs[0]="������� �������";
errs[1]="������� �������� ����� ��� ������";

function loadBrandsList(){
	JsHttpRequest.query($rcapi,{ 'w': 'showBrandsList'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){ 
		$("#brands_range").empty();
		$("#brands_range").html(result["content"]);
		$('#datatable').DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
}

function newBrandsCard(){
	JsHttpRequest.query($rcapi,{ 'w': 'newBrandsCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		var brands_id=result["brands_id"];
		showBrandsCard(brands_id);
		}
	}, true);
}

function showBrandsCard(brands_id){
	if (brands_id<=0 || brands_id==""){toastr["error"](errs[0]);}
	if (brands_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showBrandsCard', 'brands_id':brands_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#BrandsCard").modal('show');
			document.getElementById("BrandsCardBody").innerHTML=result["content"];
			document.getElementById("BrandsCardLabel").innerHTML=$("#brands_name").val()+" (ID:"+$("#brands_id").val()+")";
			$('#brands_tabs').tab();
			$("#descr").markdown({autofocus:false,savable:false})
			$("#brands_kind").select2({placeholder: "������� ���",dropdownParent: $("#BrandsCard")});
			$("#brands_country").select2({placeholder: "������� �����",dropdownParent: $("#BrandsCard")});
		}}, true);
	}
}

function saveBrandsGeneralInfo(){
	swal({
		title: "�������� ���� � ����� \"�������� ����������\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���, ��������!", cancelButtonText: "³������!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var brands_id=$("#brands_id").val();
			var brands_name=$("#brands_name").val();
			var brands_type=$("#brands_type option:selected").val();
			var brands_kind=$("#brands_kind option:selected").val();
			var brands_country=$("#brands_country option:selected").val();
			var brands_visible=$("#brands_visible").val();
			var brands_visible=0;if (document.getElementById("brands_visible").checked){brands_visible=1;}
			if (brands_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveBrandsGeneralInfo','brands_id':brands_id, 'brands_name':brands_name, 'brands_type':brands_type, 'brands_kind':brands_kind, 'brands_country':brands_country, 'brands_visible':brands_visible},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						loadBrandsList();
						swal("���������!", "������ ���� ���� ������ ��������.", "success");
						$("#BrandsCard").modal('hide');
						
					}
					else{ swal("�������!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});	
}

function loadBrandsDetails(brands_id){
	if (brands_id<=0 || brands_id==""){toastr["error"](errs[0]);}
	if (brands_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadBrandsDetails', 'brands_id':brands_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			document.getElementById("brands_details").innerHTML=result["content"];
			$('#brands_tabs').tab();
		}}, true);
	}
}

function saveBrandsDetails(){
	swal({
		title: "�������� ���� � ����� \"�������� ����������\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���, ��������!", cancelButtonText: "³������!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var brands_id=$("#brands_id").val();
			var descr=$("#descr").val();
			var link=$("#link").val();
			if (brands_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveBrandsDetails','brands_id':brands_id, 'descr':descr, 'link':link},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("���������!", "������ ���� ���� ������ ��������.", "success");
						loadBrandsDetails();
					}
					else{ swal("�������!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});	
}

function ExportBrands() {
	var url = "/export2.php?w=ExportBrands";
	window.open(url, '_blank');
}

function ImportBrands() {
	JsHttpRequest.query($rcapi,{ 'w': 'ImportBrands'}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			document.getElementById("BrandsImportCardBody").innerHTML=result["content"];
			$("#BrandsImportCard").modal('show');
	}}, true);
}

function showIndexUploadForm(){
	$("#fileBrandsIndexUploadForm").modal('show');
	var myDropzoneBrands = new Dropzone("#myDropzone4",{ dictDefaultMessage: "�������� ��� ������ ����� ��� ���������� �� �� ����!" });
	myDropzoneBrands.removeAllFiles(true);
	myDropzoneBrands.on("queuecomplete", function() {
		toastr["info"]("������������ ����� ���������.");
		this.removeAllFiles();	
		$('#fileBrandsIndexUploadForm').modal('hide');
		ImportBrands();
	});
}

function deleteBrandsLogo(brands_id){
	swal({
		title: "�������� �������?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���, ��������!", cancelButtonText: "³������!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (brands_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'deleteBrandsLogo','brands_id':brands_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("��������!", "������ ���� ���� ������ ��������.", "success");
						loadBrandsPhoto(brands_id);
					}
					else{ swal("�������!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});	
}

function showBrandsUploadLogoForm(brands_id){
	$("#photo_brands_id").val(brands_id);
	var myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "�������� ��� ������ ����� ��� ���������� �� �� ����!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		toastr["info"]("������������ ����� ���������.");
		this.removeAllFiles();
		$('#fileBrandsPhotoUploadForm').modal('hide');
		loadBrandsPhoto(brands_id);
	});
}

function loadBrandsPhoto(brands_id){
	if (brands_id<=0 || brands_id==""){toastr["error"](errs[0]);}
	if (brands_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadBrandsPhoto', 'brands_id':brands_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			document.getElementById("brands_photo_place").innerHTML=result["content"];
		}}, true);
	}
}

function finishBrandsIndexImport(){
	var start_row=parseInt($("#start_row").val());
	var kol_cols=parseInt($("#kol_cols").val());
	if (start_row<0 || start_row.length<=0){ swal("�������!", "�� ������� ���������� ��� ����������", "error");}
	if (start_row>=0){ 
		var cols=[];var cl=0; var cls_sel=0;
		for (var i=1;i<=kol_cols;i++){
			cl=$("#clm-"+i+" option:selected").val();
			if (cl>0 || cl!=""){cls_sel+=1; cols[i]=cl;}
		}
		if (cls_sel<3){swal("�������!", "�� ������ �� �������� �������", "error");}
		else{
			$("#waveSpinner_place").html(waveSpinner);
			JsHttpRequest.query($rcapi,{ 'w':'finishBrandsIndexImport','start_row':start_row,'kol_cols':kol_cols,'cols':cols},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					swal("������ ����� ���������!", "", "success");
					ImportBrands();
				}
				else{ swal("�������!", result["error"], "error");}
			}}, true);
		}
	}
}

function ErImage() {
	document.getElementById("deleteLogo").disabled = true;
}

