// JavaScript Document
	function checkSelectNum( s ){  	var regu =/^[0-9]$/;	var re = new RegExp(regu);		if (re.test(s)) {		return true;		}else{		return false;	}	} 
	function checkBeishuNum( s ){  	if(s<=100&&s>0){			return true;		}		return false;	}
	function delCaiPiao(obj){			jQuery(obj).parent().remove();				}		
