function change_image (id,image) {
	document.getElementById(id).src = image;
}

function ffff_encrypt(form) {
//	var key = hex_hmac_md5(salt.value,hex_sha1(password.value));
//	var pt = Form.serialize(form);
//	var ct = rijndaelEncrypt(pt,hexToByteArray(key),"ECB");
	var pt = "506812A45F08C889B97F5980038B8359"
	var key = "00010203050607080A0B0C0D0F101112"
	var ct = rijndaelEncrypt(hexToByteArray(pt),hexToByteArray(key),"ECB");
	formkey.value = key;
	response.value = byteArrayToHex(ct);
	plaintext.value = pt;
	
//	salt.value=''
//	password.value=''
//	newpassword.value=''
//	newpassconf.value=''
}