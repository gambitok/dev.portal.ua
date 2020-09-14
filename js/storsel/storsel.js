var errs=[];
errs[0]="������� �������";
errs[1]="������� �������� ����� ��� ������";

$(document).ready(function() {
    shortcut.add("Insert", function() {
		var select_id=$("#select_id").val();
		var storsel_type_id=$("#storsel_type_id").val();
		if (select_id>0){
			if (storsel_type_id===0){addNewRowLocal();}
			if (storsel_type_id===1){addNewRow();}
		}
	});
	setTimeout(function(){updateStorselRange();},15*1000);
});

$(window).bind('beforeunload', function(e){
    if($("#select_id")){
		closeStorselCard();
		e=null;
	} else e=null;
});

function cancelStorselScan(select_id) {
    swal({
            title: "���������� ���������� ������?",
            text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "���", cancelButtonText: "³������", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
				JsHttpRequest.query($rcapi,{ 'w':'cancelStorselScan', 'select_id':select_id},
					function (result, errors){ if (errors) {alert(errors);} if (result){
						if (result["answer"]==1){
							swal("³������!", "", "success");
							showStorselCard(select_id);
						} else { swal("�������!", result["error"], "error");}
					}}, true);
            } else {
                swal("³������", "������ ���� ���� ����������.", "error");
            }
        });
}

function updateStorselStatus(select_id) {
	$("#storsel_send").attr("disabled", true); //disable button
	JsHttpRequest.query($rcapi,{ 'w': 'updateStorselStatus', 'select_id':select_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){
			if (result["answer"]==1){
				$("#StorselCard").modal('hide');
				document.getElementById('alert_ok').play();
				updateStorselRange();
				toastr["info"]("��������!");
			} else { toastr["error"](result["error"]); }
		}}, true);
}

function show_storsel_search(inf){
	$("#storsel_range").empty();
	JsHttpRequest.query($rcapi,{ 'w': 'show_storsel_search'},
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#storsel_range").html(result["content"]);
		if (inf===1){toastr["info"]("��������!");}
	}}, true);
}

function updateStorselRange(press_btn){
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
	var pred=$("#storsel_count").val();
	JsHttpRequest.query($rcapi,{ 'w': 'show_storsel_search', 'status':status}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		$("#storsel_range").html(result.content[0]);
		$("#storsel_count").val(result.content[1]);
		if (pred!==result.content[1]){
			if (pred!==0) { console.log("��� ����"); document.getElementById('alert_ok').play();}
		} 
		setTimeout(function(){updateStorselRange();},15*1000);
	}}, true);
}

function showStorselCard(select_id){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showStorselCard', 'select_id':select_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#StorselCard").modal('show');
			$("#StorselCardBody").html(result["content"]);
			$("#StorselCardLabel").html(result["doc_prefix_nom"]);
			$("#storsel_tabs").tab();
			//	$('#storsel_data').datepicker({format: "yyyy-mm-dd",autoclose:true})
			$(".i-checks").iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green',});
			//	$("#storage_id_to").select2({placeholder: "������� �����",dropdownParent: $("#JmovingCard")});
			numberOnly();
		}}, true);
	}
}

function unlockStorselCard(select_id){
	if (select_id){
		JsHttpRequest.query($rcapi,{ 'w': 'unlockStorselCard', 'select_id':select_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#StorselCard").modal("hide");
			$("#StorselCardBody").html("");
			$("#StorselCardLabel").html("");
		}}, true);
	} else {
		$("#StorselCard").modal("hide");
        $("#StorselCardBody").html("");
        $("#StorselCardLabel").html("");
	}
}

function closeStorselCard(){
	if ($("#select_id")){
		let select_id=$("#select_id").val();
		JsHttpRequest.query($rcapi,{ 'w': 'closeStorselCard', 'select_id':select_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#StorselCard").modal("hide");
            $("#StorselCardBody").html("");
            $("#StorselCardLabel").html("");
		}}, true);
	} else {
		$("#StorselCard").modal("hide");
        $("#StorselCardBody").html("");
        $("#StorselCardLabel").html("");
	}
}

function printStorselView(select_id){
	if (select_id.length>0){
		window.open("/Storsel/printStS1/"+select_id,"_blank","printWindow");
	}
}

function printStorselView2(select_id){
	if (select_id.length>0){
		window.open("/Storsel/printStS2/"+select_id,"_blank","printWindow");
	}
}

function loadStorselCommetsLabel(select_id){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadStorselCommetsLabel', 'select_id':select_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#label_comments").html(result["label"]);
		}}, true);
	}
}

function loadStorselCommets(select_id){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadStorselCommets', 'select_id':select_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#storsel_commets_place").html(result["content"]);
		}}, true);
	}
}

function saveStorselComment(select_id){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		let comment=$("#storsel_comment_field").val();
		if (comment.length<=0){toastr["error"]("�������� �������� ��������");}
		if (comment.length>0){
			JsHttpRequest.query($rcapi,{ 'w': 'saveStorselComment', 'select_id':select_id, 'comment':comment}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){ 
					loadStorselCommets(select_id); 
					$("#storsel_comment_field").val("");
					loadStorselCommetsLabel(select_id);
				} else { toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function dropStorselComment(select_id,cmt_id){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		if(confirm("�������� �����?")){
			JsHttpRequest.query($rcapi,{ 'w': 'dropStorselComment', 'select_id':select_id, 'cmt_id':cmt_id}, 
			function (result, errors){ if (errors) {alert(errors);} if (result){  
				if (result["answer"]==1){
					loadStorselCommets(select_id);
					toastr["info"]("����� ������ ��������");
					loadJmovingCommetsLabel(select_id);
				} else { toastr["error"](result["error"]); }
			}}, true);
		}
	}
}

function makesJmovingStorageSelect(){
	let select_id=$("#select_id").val();
	swal({
		title: "�������� � ������ ���������� ������?",
		text: "�������� �������� ��� ���� �����������", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���", cancelButtonText: "³������", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (select_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'makesJmovingStorageSelect', 'select_id':select_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("���������!", "���������� �������� � ������", "success");
						showJmovingCard(select_id);
						$("#JmovingPreSelect").modal("hide");
						$("#JmovingPreSelectBody").html("");
						$("#JmovingPreSelectLabel").html("");
						show_storsel_search(0);
					} else { swal("�������!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});
}

function loadJmovingStorageSelect(select_id){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadJmovingStorageSelect', 'select_id':select_id, 'storsel_status':45},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
            $("#storsel_storage_select_place").html(result["content"]);
			// numberOnlyLong();
			$('#income_tabs').tab();
		}}, true);
	}
}

function viewStorsel(select_id,storsel_status){
	if (select_id<=0 || select_id===""){toastr["error"](errs[0]);}
	if (select_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'viewStorsel', 'select_id':select_id, 'storsel_status':storsel_status},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#FormModalWindow2").modal("show");
            $("#FormModalBody2").html(result["content"]);
            $("#FormModalLabel2").html(result["header"]);
			$("#income_tabs").tab();
		}}, true);
	}
}

function collectStorsel(select_id){
	// swal({
	// 	title: "��������� �������� ����������� ������?",
	// 	text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
	// 	confirmButtonText: "���", cancelButtonText: "³������", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	// },
	// function (isConfirm) {
	// 	if (isConfirm) {
	// 		if (select_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'collectStorsel','select_id':select_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){
						showStorselCard(select_id);
					} else { swal("�������!", result["error"], "error");}
				}}, true);
	// 		}
	// 	} else {
	// 		swal("³������", "������ ���� ���� ����������.", "error");
	// 	}
	// });
}

function showStorselBarcodeForm(select_id){
	$("#storsel_check").attr("disabled", true);
	if (select_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showStorselBarcodeForm','select_id':select_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#FormModalWindow4").modal("show");
                $("#FormModalBody4").html(result["content"]);
                $("#FormModalLabel4").html(result["header"]);
			} else { swal("�������!", result["error"], "error");}
		}}, true);
	}
}

function saveStorselBarcodeForm(select_id){
	let barcode=$("#BarCodeInput").val();
	if (select_id.length>0 && barcode.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'saveStorselBarcodeForm', 'select_id':select_id, 'barcode':barcode},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#BarCodeInput").val("");
				$("#BarCodeInput").focus();
				$("#amr_"+result["row_id"]).html(result["amount_barcode"]);
				$("#amrd_"+result["row_id"]).html(result["dif_amount_barcode"]);
				document.getElementById("ok_art_id").play();
			} else {
				document.getElementById("no_art_id").play();
                $("#BarCodeInput").val("");
			}
		}}, true);
	}
}

function finishStorselBarcodeForm(select_id){
	$("#storsel_finish").attr("disabled", true);
	if (select_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'finishStorselBarcodeForm', 'select_id':select_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				showStorselCard(select_id);
				$("#FormModalWindow4").modal("hide");
                $("#FormModalBody4").html("");
                $("#FormModalLabel4").html("");
				swal("��������!", "���������� ���������", "success");
			} else { swal("�������!", result["error"], "error");}
		}}, true);
	}
}

function scanStorselBarcodeForm(select_id) {
    swal({
            title: "��������� �� �������?",
            text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
            confirmButtonText: "���������", cancelButtonText: "³����", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                JsHttpRequest.query($rcapi,{ 'w':'scanStorselBarcodeForm', 'select_id':select_id},
                    function (result, errors){ if (errors) {alert(errors);} if (result){
                        if (result["answer"]==1){
                            swal.close();
                            $("#FormModalWindow4").modal("hide");
                            showStorselCard(select_id);
                        } else { swal("�������!", result["error"], "error");}
                    }}, true);
            } else {swal("³������!", "", "error");}
        });
}

function showJmovingStorageSelectSendTruckForm(select_id){
	swal({
		title: "�������� �������� ������ �� ��������?",
		text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "��������", cancelButtonText: "���������", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (select_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w':'setJmovingStorageSelectSendTruck', 'select_id':select_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						showJmovingCard(select_id);
						swal.close();
						window.open("/Jmoving/printJmSTP/"+select_id,"_blank","printWindow");
					} else { swal("�������!", result["error"], "error");}
				}}, true);
			}
		} else { swal("³������", "", "error"); }
	});
}

function showStorselBugForm(select_id,str_id){
	if (select_id.length>0 && str_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showStorselBugForm', 'select_id':select_id, 'str_id':str_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#FormModalWindow5").modal("show");
                $("#FormModalBody5").html(result["content"]);
                $("#FormModalLabel5").html(result["header"]);
				numberOnly();
			} else {swal("�������!", result["error"], "error");}
		}}, true);
	}
}

function saveStorselBugForm(select_id,str_id){
	if (select_id.length>0 && str_id.length>0){
		var err=0; 
		var storage_select_bug=$("#storage_select_bug option:selected").val();
		var amount=parseFloat($("#bug_amount").val());
		var dif_amount_barcode=parseFloat($("#bug_dif_amount_barcode").val());
		if (storage_select_bug<=0 || storage_select_bug.length==0){er=1;swal("�������!", "������ ��� ���������", "error");}
		if (dif_amount_barcode<=0 || dif_amount_barcode==0){er=1;swal("�������!", "�� ������� ������� ���������", "error");}
		if (dif_amount_barcode>amount){er=1;swal("�������!", "ʳ������ ��������� �� ������� ������������ ������ ������", "error");}
		if (err==0){
			swal({
				title: "����������� ��������� ���������?",
				text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
				confirmButtonText: "�����������", cancelButtonText: "���������", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
			},
			function (isConfirm) {
				if (isConfirm) {
					JsHttpRequest.query($rcapi,{ 'w':'saveStorselBugForm','select_id':select_id,'str_id':str_id,'storage_select_bug':storage_select_bug,'dif_amount_barcode':dif_amount_barcode},
					function (result, errors){ if (errors) {alert(errors);} if (result){  
						if (result["answer"]==1){ 
							viewStorsel(select_id);
							swal.close();
							$("#FormModalWindow5").modal('hide');
                            $("#FormModalBody5").html("");
                            $("#FormModalLabel5").html("");
							$("#BarCodeInput").val("");
							$("#BarCodeInput").focus();
							$("#ssbug_"+result["row_id"]).html(result["storage_select_bug_name"]);
							$("#amrd_"+result["row_id"]).html(result["dif_amount_barcode"]);
							$("#ambg_"+result["row_id"]).html(result["amount_bug"]);
							$("#amr_"+result["row_id"]).html(result["amount_barcode"]);
							$("#amrns_"+result["row_id"]).html(result["amount_barcode_noscan"]);
						} else { swal("�������!", result["error"], "error");}
					}}, true);
				} else {swal("³������", "", "error");}
			});
		}
	}	
}

function showStorselNoscanForm(select_id,art_id,str_id){
	if (select_id.length>0 && art_id.length>0 && str_id.length>0){
		JsHttpRequest.query($rcapi,{ 'w':'showStorselNoscanForm', 'select_id':select_id, 'str_id':str_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			if (result["answer"]==1){ 
				$("#FormModalWindow5").modal("show");
                $("#FormModalBody5").html(result["content"]);
                $("#FormModalLabel5").html(result["header"]);
				numberOnly();
			} else {swal("�������!", result["error"], "error");}
		}}, true);
	}
}

function saveStorselNoscanForm(select_id,art_id,str_id){
	if (select_id.length>0 && art_id.length>0 && str_id.length>0){
		var err=0; 
		var amount=parseFloat($("#amount_barcode_noscan").val());
		var dif_amount_barcode=parseFloat($("#noscan_dif_amount_barcode").val());
		if (dif_amount_barcode<=0 || dif_amount_barcode==0){er=1;swal("�������!", "�� ������� ������� ��� ����������", "error");}
		if (dif_amount_barcode>amount){er=1;swal("�������!", "ʳ������ ��� ���������� �� ������� ������������ ������� ��� ���������", "error");}
		if (err==0){
			swal({
				title: "����������� ��������� ������ ��� ���������� �����-����?",
				text: "", type: "info", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
				confirmButtonText: "�����������", cancelButtonText: "���������", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
			},
			function (isConfirm) {
				if (isConfirm) {
					JsHttpRequest.query($rcapi,{ 'w':'saveStorselNoscanForm','select_id':select_id,'art_id':art_id,'str_id':str_id,'amount_barcode_noscan':amount},
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
						} else {swal("�������!", result["error"], "error");}
					}}, true);
				} else {swal("³������", "", "error");}
			});
		}
	}
}

function calculateStorselParams(select_id) {
    JsHttpRequest.query($rcapi,{ 'w': 'calculateStorselParams', 'select_id':select_id},
        function (result, errors){ if (errors) {alert(errors);} if (result){
            alert(result.content);
        }}, true);
}
