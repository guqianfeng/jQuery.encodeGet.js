<?php
define('DEFAULT_PWD', 'guqianfeng');//decode key, must same as the javascript encode key
define('GET_TAG', 'get');//url params name

//还原参数
//当收到客户端的GET加密消息后，首先调用$_GET = extract_arg($_GET);
//然后就可以使用常规方式获取GET值了
function extract_arg( $arr ) {
    if ( isset( $arr[GET_TAG] ) ) {
        return arg_arr2str( make_my_urlarg( $arr[GET_TAG] , true ), true );
    } else {
        return array('');
    }
}
//数组字符串互换
function arg_arr2str( $data, $is_str2arr = false ) {
	//将传入的参数数组转成用&链接的字符串
    $tmp = '';
    if ( $is_str2arr && is_string( $data ) ) {
        $arr = explode('&', $data);
        foreach( $arr as $index => $data ) {
            if ( false !== strpos($data, '=') ) {
                list($k , $val) = explode('=', $data);
                $tmp[$k] = urldecode($val);
            }
        }
    } else if( !$is_str2arr && is_array( $data ) ) {
        foreach( $data as $k => $val ) $tmp .= '&' . $k . '=' . $val;
        $tmp = substr($tmp, 1);
    }
    return $tmp;
}
//根据相应的算法加解密参数
function make_my_urlarg($str, $is_decode = false) {
    return ($is_decode) ? authcode($str, 'DECODE', DEFAULT_PWD) : urlencode( authcode($str, 'ENCODE', DEFAULT_PWD) );
}

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 1;
    $key = md5($key != "" ? $key : DEFAULT_PWD);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
	$time = time();
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5($time), -$ckey_length)) : '';
    $cryptkey = $keya . md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.base64_encode($result);
    }
}
$_GET = extract_arg($_GET);

foreach ( array( "_GET" ) as $req )
{
	foreach ( $$req as $_key => $_value )
	{
		if ( $_key[0] != "_" )
		{
			$$_key = dhtmlchars( strip_tags( daddslashes( $_value ) ) );
		}
	}
}
?>
