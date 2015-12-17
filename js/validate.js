//Check value is empty
function isEmpty(val){
	if(val == ""){
		return true
	}
	return false;
}

//检查是否为整数
function checkInteger(value){
	var exp = /^-?\d+$/;
	var re = new RegExp(exp);
	return re.test(value);
}
//检查是否为小数
function checkFloat(value){
	var exp = /^(-?\d+)(\.\d+)?$/;
	var re = new RegExp(exp);
	return re.test(value);
}