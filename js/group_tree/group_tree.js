
function showGroupTreeCard(str_id) {
	if (str_id<=0 || str_id==""){toastr["error"](errs[0]);}
	if (str_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeCard', 'str_id':str_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#GroupTreeCard").modal('show');
			$("#GroupTreeCardBody").html(result.content);
		}}, true);
	}
}

function saveGroupTreeCard(str_id) {
	swal({
		title: "�������� ���� � ����� \"������ ������\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���, ��������!", cancelButtonText: "³������!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var position=$("#position_list option:selected").val(); 
			var disp_text_ru=$("#disp_text_ru").val();
			var disp_text_ua=$("#disp_text_ua").val();
			var disp_text_en=$("#disp_text_en").val();

			if (str_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveGroupTreeCard', 'str_id':str_id, 'position':position, 'disp_text_ru':disp_text_ru, 'disp_text_ua':disp_text_ua, 'disp_text_en':disp_text_en},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("���������!", "������ ���� ���� ������ ��������.", "success");
						$("#GroupTreeCard").modal('hide');
					}
					else{ swal("�������!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});	
}

function showGroupTreeHeaders() {
	JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeHeaders'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#group-tree").html(result.content);
	}}, true);	
}

function addStrHeader() {
	var str_id=$("#str_id").val();
	var head_id=$("#head_list option:selected").val();
	console.log(str_id);
	console.log(head_id);
	if (str_id>0 && head_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'addStrHeader', 'str_id':str_id, 'head_id':head_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			UpdateGroupTreeCard(str_id);
		}}, true);
	}
}

function dropStrHeader(head_id) {
	var str_id=$("#str_id").val();
	console.log(str_id);
	console.log(head_id);
	if (str_id>0 && head_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'dropStrHeader', 'str_id':str_id, 'head_id':head_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			UpdateGroupTreeCard(str_id);
		}}, true);
	}
}

function UpdateGroupTreeCard(str_id) {
	if (str_id<=0 || str_id==""){toastr["error"](errs[0]);}
	if (str_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeCard', 'str_id':str_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#GroupTreeCardBody").html(result.content);
		}}, true);
	}
}

function showGroupTreeHeadCard(head_id) {
	JsHttpRequest.query($rcapi,{ 'w': 'showGroupTreeHead', 'head_id':head_id}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#GroupTreeCard").modal('show');
		$("#GroupTreeCardBody").html(result.content);
	}}, true);
}

function saveGroupTreeHeadCard(head_id) {
	swal({
		title: "�������� ���� � ����� \"������ ������\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���, ��������!", cancelButtonText: "³������!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var disp_text_ru=$("#disp_text_ru").val();
			var disp_text_ua=$("#disp_text_ua").val();
			var disp_text_en=$("#disp_text_en").val();

			if (head_id.length>0){
				JsHttpRequest.query($rcapi,{ 'w': 'saveGroupTreeHead', 'head_id':head_id, 'disp_text_ru':disp_text_ru, 'disp_text_ua':disp_text_ua, 'disp_text_en':disp_text_en}, 
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("���������!", "������ ���� ���� ������ ��������.", "success");
						$("#GroupTreeCard").modal('hide');
						showGroupTreeHeaders();
					}
					else{ swal("�������!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});	
}

function dropGroupTreeHead(head_id) {
	swal({
		title: "�������� �����?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���, ��������!", cancelButtonText: "³������!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (brands_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'dropGroupTreeHead','head_id':head_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("��������!", "������ ���� ���� ������ ��������.", "success");
						$("#GroupTreeCard").modal('hide');
						showGroupTreeHeaders();
					}
					else{ swal("�������!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});	
}