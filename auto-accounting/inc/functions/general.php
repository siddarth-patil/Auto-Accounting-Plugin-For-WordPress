<?php

function wpa_moneyFormat($str){
	if(!is_numeric($str)){
		return WPA_CUR.$str;
	}
	return WPA_CUR.number_format($str,2);
}
?>