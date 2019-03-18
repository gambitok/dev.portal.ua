var orderBy="serial";
var asc="asc";


function AuthAcount(){var er=0;
	var phone =$("#phone").val(); 
	if (phone==""){ er=1; alert (er);
		$("#group-phone").attr('class', 'form-group has-warning');
		toastr["warning"]("Введіть номер телефону");
	}
	if (phone!=""){
		intRegex = /[0-9 -()+]+$/;
		if((phone.length < 12) || (!intRegex.test(phone))){er=1;
			 $("#group-phone").attr('class', 'form-group has-error');
			 toastr["error"]("Номер телефону введено не коректно");
		}
	}
	var pass =$("#pass").val(); 
	if (pass==""){er=1;
		$("#group-pass").attr('class', 'form-group has-warning');
		toastr["warning"]("Введіть пароль");
	}
	if (pass!=""){
		if(pass.length < 3){er=1;
			$("#group-pass").attr('class', 'form-group has-error');
			toastr["error"]("Пароль введено не коректно");
		}
	}
	
	if (er==0){ 
		$("#group-phone").attr('class', 'form-group has-success');
		$("#group-pass").attr('class', 'form-group has-success');
		var remember=0; if ($("#remember").is(':checked')){remember=1;}
		
		JsHttpRequest.query($rcapi,{ 'w': 'authUser','phone':phone,'pass':pass,'remember':remember}, 
		function (result, errors){ if (errors) {} if (result){ 
			if(result["answer"]==1){ 
				toastr["info"]("Вхід успішно виконано");
				setTimeout("window.location.reload();", 1500);
			}
			else{ toastr["error"](result["answer"]);}
		}}, true);
	}
}

function logOut(){
	JsHttpRequest.query($rcapi,{ 'w': 'logOutUser'}, 
	function (result, errors){ if (errors) {} if (result){ 
		if(result["answer"]==1){ window.location.reload(); }
		else{ toastr["error"](result["answer"]);}
	}}, true);
}

function generateKey(){
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 20;
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	document.getElementById("key").value=randomstring;
}
function appendKey(){
	generateKey();
	var client_id=document.getElementById("client_id").value;
	var key=document.getElementById("key").value;
	JsHttpRequest.query($rcapi,{ 'w': 'appendClientKey','client_id':client_id,'key':key}, 
	function (result, errors){ if (errors) {} if (result){ alert(result["answer"]);}}, true);
}
function setCookieNavBarMini(){
	JsHttpRequest.query($rcapi,{ 'w': 'setCookieNavBarMini'}, 
	function (result, errors){ if (errors) {} if (result){ }}, true);
}