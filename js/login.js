$(function() {
	$("#password").bind("keyup", function(event) {
        if (event.keyCode == "13") {
        	login();
        }
    });
	
	var userName = getCookie("userName");
	$("#userName").val(userName);
});

//Process login
function login() {
	closeTips([ "mustInputUserName", "mustInputPassword", "wrongUserNamePassword" ]);

	var userName = $("#userName");
	var password = $("#password");
	var rememberMe = $("#rememberMe");

	var isVaild = true;
	if (isEmpty(userName.val())) {
		showTip("mustInputUserName");
		isVaild = false;
	}
	if (isEmpty(password.val())) {
		showTip("mustInputPassword");
		isVaild = false;
	}

	if (!isVaild)
		return false;

	setInputDisabled("loginButton", true);
	$.ajax({
		url : domainUrl + "common/action/user_action.php",
		type : "POST",
		async : false,
		data : {
			"method" : "doLogin",
			"userName" : userName.val(),
			"password" : password.val(),
			"rememberMe" : rememberMe.prop("checked") ? "1" : "0"
		},
		dataType : "json",
		success : function(data) {
			// var obj = getObjectFromJSON(data)
			if (data.retCode == "0") {
				location.href = data.redirect;
			} else {
				showTip(data.result);
				userName.focus();
			}
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			showTip("loginException");
		},
		complete : function(XMLHttpRequest, textStatus) {
			setInputDisabled("loginButton", false);
		}
	});
}