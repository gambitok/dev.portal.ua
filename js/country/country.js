var errs=[];
errs[0]="������� �������";
errs[1]="������� �������� ����� ��� ������";

$(document).ready(function() {});
//download and show countrylist
function loadCountryList(){
	JsHttpRequest.query($rcapi,{ 'w': 'showCountryList'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		$("#country_range").empty();
		$("#country_range").html(result["content"]);
	}}, true);
}
//create country-card
function newCountryCard(){
	JsHttpRequest.query($rcapi,{ 'w': 'newCountryCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){  
		var country_id=result["country_id"];
		showCountryCard(country_id);
		}
	}, true);
}
//display country-card
function showCountryCard(country_id){
	if (country_id<=0 || country_id==""){toastr["error"](errs[0]);}
	if (country_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showCountryCard', 'country_id':country_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#CountryCard").modal('show');
			document.getElementById("CountryCardBody").innerHTML=result["content"];
			document.getElementById("CountryCardLabel").innerHTML=$("#country_name").val()+" (ID:"+$("#country_id").val()+")";
			$('#country_tabs').tab();
		}}, true);
	}
}
//save all
function saveCountryGeneralInfo(){
	swal({
		title: "�������� ���� � ����� \"�������� ����������\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���, ��������!", cancelButtonText: "³������!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			var country_id=$("#country_id").val();
			var country_name=$("#country_name").val();
			var country_alfa2=$("#country_alfa2").val();
			var country_alfa3=$("#country_alfa3").val();
			var country_duty=$("#country_duty").val();
			var country_risk=$("#country_risk").val();
			
			if (country_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveCountryGeneralInfo','country_id':country_id, 'country_name':country_name, 'country_alfa2':country_alfa2, 'country_alfa3':country_alfa3, 'country_duty':country_duty, 'country_risk':country_risk},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("���������!", "������ ���� ���� ������ ��������.", "success");
						$("#CountryCard").modal('hide');
						loadCountryList();
					}
					else{ swal("�������!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});	
}
//delete
function DeleteCountry(country_id){
	swal({
		title: "�������� �����?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "���, ��������!", cancelButtonText: "³������!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			//var country_id=$("#country_id").val();
			
			if (country_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'DeleteCountry','country_id':country_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("��������!", "������ ���� ���� ������ ��������.", "success");
						loadCountryList();
					}
					else{ swal("�������!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("³������", "������ ���� ���� ����������.", "error");
		}
	});	
}