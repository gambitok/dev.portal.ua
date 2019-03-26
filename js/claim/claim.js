var errs=[];
errs[0]="Помилка індексу";
errs[1]="Занадто короткий запит для пошуку";

function showClaimCard(claim_id){
	if (claim_id<=0 || claim_id==""){toastr["error"](errs[0]);}
	if (claim_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'showClaimCard', 'claim_id':claim_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#ClaimCard").modal('show');
			document.getElementById("ClaimCardBody").innerHTML=result["content"];
			$("#claim_text_ukr").markdown({autofocus:false,savable:false})
			$("#claim_text_eng").markdown({autofocus:false,savable:false})
		}}, true);
	}
}

function loadClaimAct(claim_id){
	if (claim_id<=0 || claim_id==""){toastr["error"](errs[0]);}
	if (claim_id>0){
		JsHttpRequest.query($rcapi,{ 'w': 'loadClaimAct', 'claim_id':claim_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){
			$("#ClaimCard").modal('show');
			document.getElementById("claim_act").innerHTML=result["content"];

		}}, true);
	}
}

function closeClaimCard(){
	if ($("#claim_id")){
		var claim_id=$("#claim_id").val();
		JsHttpRequest.query($rcapi,{ 'w': 'closeClaimCard', 'claim_id':claim_id},
		function (result, errors){ if (errors) {alert(errors);} if (result){  
			$("#ClaimCard").modal('hide');
			document.getElementById("ClaimCardBody").innerHTML="";
			document.getElementById("ClaimCardLabel").innerHTML="";
		}}, true);
	}else{
		$("#ClaimCard").modal('hide');
		document.getElementById("ClaimCardBody").innerHTML="";
		document.getElementById("ClaimCardLabel").innerHTML="";
	}
}