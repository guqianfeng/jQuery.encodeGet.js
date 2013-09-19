jQuery.encodeGet.js

===================

How to Use:

1- Install:
Copy jQuery.encodeGet.js to the dir of your server, for example: include/javascript/
Copy ppd.encodeget.php to the dir of your server php includes, for example: include/php/

2- From the client side:
<script type="text/javascript" src="include/javascript/jQuery.encodeGet.js"></script>
<script type="text/javascript">
var params_name = "get";                       // this must same as GET_TAG in ppd.encodeget.php
var url = "http://www.test.com/encode.php";
var params = "id=120&userid=jackygu";          // get params
var encode_key = "!@#$%^&";                    // encode key
var _authcode = encodeURIComponent($.encodeGet( {data:params, key:encode_key} ));
var url = url + "?" + params_name + "=" + _authcode; // this is new encoded url
</script>

3- From the server side, at the begin of php, for example: decode.php:
require_once( "include/ppd.encodeget.php" );

Then you can get params id as: 
$id = $_GET['id'];
$userid = $_GET['userid'];

For more information, please visit http://jackygu.com/wp/?p=6.
