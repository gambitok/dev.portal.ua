var errs = [];
errs[0] = "������� �������";
errs[1] = "������� �������� ����� ��� ������";

function loadBrandsList() {
	JsHttpRequest.query($rcapi,{ 'w': 'showBrandsList'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		let br = $("#brands_range");
		br.empty();
		br.html(result["content"]);
		$("#datatable").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
	}}, true);
}

function newBrandsCard() {
	JsHttpRequest.query($rcapi,{ 'w': 'newBrandsCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		let brands_id = result["brands_id"];
		showBrandsCard(brands_id);
	}}, true);
}

function showBrandsCard(brands_id) {
	if (brands_id<=0 || brands_id==="") {
		toastr["error"](errs[0]);
	}
	if (brands_id>0) {
		JsHttpRequest.query($rcapi,{ 'w': 'showBrandsCard', 'brands_id':brands_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let brand_card = $("#BrandsCard");
            brand_card.modal("show");
			$("#BrandsCardBody").html(result["content"]);
			$("#BrandsCardLabel").html($("#brands_name").val()+" (ID:"+$("#brands_id").val()+")");
			$("#brands_tab").tab();
			$("#descr").markdown({autofocus:false,savable:false})
			$("#brands_kind").select2({placeholder: "������� ���",dropdownParent: brand_card});
			$("#brands_country").select2({placeholder: "������� �����",dropdownParent: brand_card});
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
			let brands_id		= $("#brands_id").val();
            let brands_name		= $("#brands_name").val();
            let brands_type		= $("#brands_type option:selected").val();
            let brands_kind		= $("#brands_kind option:selected").val();
            let brands_country	= $("#brands_country option:selected").val();
			if (document.getElementById("brands_visible").checked){brands_visible=1;} else {brands_visible=0;}

			if (brands_id.length > 0) {
				JsHttpRequest.query($rcapi,{'w':'saveBrandsGeneralInfo','brands_id':brands_id, 'brands_name':brands_name, 'brands_type':brands_type, 'brands_kind':brands_kind, 'brands_country':brands_country, 'brands_visible':brands_visible},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"] == 1) {
						loadBrandsList();
						swal("���������!", "������ ���� ���� ������ ��������.", "success");
						$("#BrandsCard").modal("hide");
					} else {
						swal("�������!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});	
}

function loadBrandsDetails(brands_id) {
	if (brands_id <= 0 || brands_id === "") {
		toastr["error"](errs[0]);
	}
	if (brands_id > 0) {
		JsHttpRequest.query($rcapi,{ 'w': 'loadBrandsDetails', 'brands_id':brands_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#brands_details").html(result["content"]);
			$("#brands_tab").tab();
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
            let brands_id	= $("#brands_id").val();
            let descr 		= $("#descr").val();
            let descr_ua 	= $("#descr_ua").val();
            let descr_en 	= $("#descr_en").val();
            let link 		= $("#link").val();

			if (brands_id.length > 0) {
				JsHttpRequest.query($rcapi,{'w':'saveBrandsDetails', 'brands_id':brands_id, 'descr':descr, 'descr_ua':descr_ua, 'descr_en':descr_en, 'link':link},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"] == 1) {
						swal("���������!", "������ ���� ���� ������ ��������.", "success");
						loadBrandsDetails();
					} else {
						swal("�������!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});	
}

function ExportBrands() {
    let url = "/export2.php?w=ExportBrands";
	window.open(url, '_blank');
}

function ImportBrands() {
	JsHttpRequest.query($rcapi,{ 'w': 'ImportBrands'}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#BrandsImportCardBody").html(result["content"]);
			$("#BrandsImportCard").modal('show');
	}}, true);
}

function showIndexUploadForm() {
	$("#fileBrandsIndexUploadForm").modal('show');
	let myDropzoneBrands = new Dropzone("#myDropzone4",{ dictDefaultMessage: "�������� ��� ������ ����� ��� ���������� �� �� ����!" });
	myDropzoneBrands.removeAllFiles(true);
	myDropzoneBrands.on("queuecomplete", function() {
		toastr["info"]("������������ ����� ���������.");
		this.removeAllFiles();	
		$("#fileBrandsIndexUploadForm").modal("hide");
		ImportBrands();
	});
}

function deleteBrandsLogo(brands_id) {
	swal({
		title: "�������� �������?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���, ��������!", cancelButtonText: "³������!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (brands_id.length > 0) {
				JsHttpRequest.query($rcapi,{'w':'deleteBrandsLogo', 'brands_id':brands_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"] == 1) {
						swal("��������!", "������ ���� ���� ������ ��������.", "success");
						loadBrandsPhoto(brands_id);
					} else {
						swal("�������!", result["error"], "error");
					}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});	
}

function showBrandsUploadLogoForm(brands_id) {
	$("#photo_brands_id").val(brands_id);
	let myDropzone3 = new Dropzone("#myDropzone3",{ dictDefaultMessage: "�������� ��� ������ ����� ��� ���������� �� �� ����!" });
	myDropzone3.removeAllFiles(true);
	myDropzone3.on("queuecomplete", function() {
		toastr["info"]("������������ ����� ���������.");
		this.removeAllFiles();
		$("#fileBrandsPhotoUploadForm").modal("hide");
		loadBrandsPhoto(brands_id);
	});
}

function loadBrandsPhoto(brands_id) {
	if (brands_id <= 0 || brands_id === "") {
		toastr["error"](errs[0]);
	}
	if (brands_id > 0) {
		JsHttpRequest.query($rcapi,{ 'w': 'loadBrandsPhoto', 'brands_id':brands_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#brands_photo_place").html(result["content"]);
		}}, true);
	}
}

function finishBrandsIndexImport(){
    let start_row = parseInt($("#start_row").val());
    let kol_cols = parseInt($("#kol_cols").val());
	if (start_row < 0 || start_row.length <= 0) {
		swal("�������!", "�� ������� ���������� ��� ����������", "error");
	}
	if (start_row >= 0) {
		var cols = []; var cl = 0; var cls_sel = 0;
		for (var i = 1; i <= kol_cols; i++) {
			cl = $("#clm-" + i + " option:selected").val();
			if (cl > 0 || cl != "") {
				cls_sel += 1;
				cols[i] = cl;
			}
		}
		if (cls_sel < 3) {
			swal("�������!", "�� ������ �� �������� �������", "error");
		} else {
			$("#waveSpinner_place").html(waveSpinner);
			JsHttpRequest.query($rcapi,{ 'w':'finishBrandsIndexImport', 'start_row':start_row, 'kol_cols':kol_cols, 'cols':cols},
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"] == 1) {
					swal("������ ����� ���������!", "", "success");
					ImportBrands();
				} else {
					swal("�������!", result["error"], "error");
				}
			}}, true);
		}
	}
}

/*
* CERTIFICATES
* */

function getCertificatesList() {
	JsHttpRequest.query($rcapi,{ 'w': 'getCertificatesList'},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			let range = $("#certificates_range");
			range.empty();
			range.html(result["content"]);
			$("#datatable").DataTable({keys: true,"aaSorting": [],"processing": true,"scrollX": true,fixedColumns: {leftColumns: 2},"searching": true,fixedHeader: true,"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]], "language": {"url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Ukrainian.json"}});
		}}, true);
}

function showCertificateCard(certificate_id = 0) {
	$("#CertificateCard").modal("show");
	JsHttpRequest.query($rcapi,{ 'w': 'showCertificateCard', 'certificate_id':certificate_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#CertificateCardBody").html(result.content);
		}}, true);
}

function saveCertificateCard() {
	let certificate_id = $("#certificate_id").val();
	let brand_id = $("#brand_list option:selected").val();
	let suppl_id = $("#suppl_list option:selected").val();
	let date_from = $("#date_from").val();
	let date_to = $("#date_to").val();
	let status = $("#certificate_status").prop("checked");

	if (certificate_id.length > 0) {
		JsHttpRequest.query($rcapi,{'w':'saveCertificateCard', 'certificate_id':certificate_id, 'brand_id':brand_id, 'suppl_id':suppl_id, 'date_from':date_from, 'date_to':date_to, 'status':status},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				if (result["answer"] == 1) {
					swal("���������!", "������ ���� ���� ������ ��������.", "success");
					showCertificateCard(result["certificate_id"]);
					getCertificatesList();
				} else {
					swal("�������!", result["error"], "error");
				}
			}}, true);
	}
}

function dropCertificateCard() {
	let certificate_id = $("#certificate_id").val();

	if (certificate_id.length > 0) {
		JsHttpRequest.query($rcapi,{'w':'dropCertificateCard', 'certificate_id':certificate_id},
			function (result, errors){ if (errors) {alert(errors);} if (result){
				if (result["answer"] == 1) {
					swal("��������!", "������ ���� ���� ������ ��������.", "success");
					$("#CertificateCard").modal("hide");
					getCertificatesList();
				} else {
					swal("�������!", result["error"], "error");
				}
			}}, true);
	}
}

function dropCertificatePhoto(certificate_id) {
	JsHttpRequest.query($rcapi,{'w':'dropCertificatePhoto', 'certificate_id':certificate_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			console.log('done');
			showCertificateCard(certificate_id);
			getCertificatesList();
		}}, true);
}

function showCertificateUploadForm(certificate_id) {
	$("#photo_certificate_id").val(certificate_id);
	let drop = new Dropzone("#myDropzone3", { dictDefaultMessage: "�������� ��� ������ ����� ��� ���������� �� �� ����!" });
	drop.removeAllFiles(true);
	drop.on("queuecomplete", function() {
		toastr["info"]("������������ ����� ���������.");
		this.removeAllFiles();
		$("#fileCertificatePhotoUploadForm").modal("hide");
		showCertificateCard(certificate_id);
		getCertificatesList();
	});
}


