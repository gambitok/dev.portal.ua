var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

function loadCountryList(){
	JsHttpRequest.query($rcapi,{ 'w': 'showCountryList'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		let country_range = $("#country_range");
        country_range.empty();
        country_range.html(result["content"]);
	}}, true);
}

function newCountryCard(){
	JsHttpRequest.query($rcapi,{ 'w': 'newCountryCard'}, 
	function (result, errors){ if (errors) {alert(errors);} if (result){
		let country_id=result["country_id"];
		showCountryCard(country_id);
	}}, true);
}

function showCountryCard(country_id){
	if (country_id<=0 || country_id==""){toastr["error"](errs[0]);}
	if (country_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showCountryCard', 'country_id':country_id}, 
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#CountryCard").modal("show");
			$("#CountryCardBody").html(result["content"]);
			$("#CountryCardLabel").html($("#country_name").val()+" (ID:"+$("#country_id").val()+")");
			$("#country_tabs").tab();
		}}, true);
	}
}

function saveCountryGeneralInfo(){
	swal({
		title: "Зберегти зміни у розділі \"Загальна інформація\"?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, зберегти!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
            let country_id=$("#country_id").val();
            let country_name=$("#country_name").val();
            let country_alfa2=$("#country_alfa2").val();
            let country_alfa3=$("#country_alfa3").val();
            let country_duty=$("#country_duty").val();
            let country_risk=$("#country_risk").val();
			if (country_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'saveCountryGeneralInfo', 'country_id':country_id, 'country_name':country_name, 'country_alfa2':country_alfa2, 'country_alfa3':country_alfa3, 'country_duty':country_duty, 'country_risk':country_risk},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Збережено!", "Внесені Вами зміни успішно збережені.", "success");
						$("#CountryCard").modal("hide");
						loadCountryList();
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}

function dropCountry(country_id){
	swal({
		title: "Видалити країну?",
		text: "", type: "warning", allowOutsideClick:true, allowEscapeKey:true, showCancelButton: true, confirmButtonColor: "#1ab394",
		confirmButtonText: "Так, видалити!", cancelButtonText: "Відмінити!", closeOnConfirm: false, closeOnCancel: false, showLoaderOnConfirm: true
	},
	function (isConfirm) {
		if (isConfirm) {
			if (country_id.length>0){
				JsHttpRequest.query($rcapi,{'w':'dropCountry', 'country_id':country_id},
				function (result, errors){ if (errors) {alert(errors);} if (result){  
					if (result["answer"]==1){ 
						swal("Видалено!", "Внесені Вами зміни успішно збережені.", "success");
						loadCountryList();
					} else { swal("Помилка!", result["error"], "error");}
				}}, true);
			}
		} else {
			swal("Відмінено", "Внесені Вами зміни анульовано.", "error");
		}
	});	
}