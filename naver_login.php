<?php
//naver_login.php
$client_id = "knaqqiZdc9WDsZnqINbl"; // 위에서 발급받은 Client ID 입력
$redirectURI = urlencode("https://test.ericapp.shop/naver_login_callback.php"); //자신의 Callback URL 입력
$state = "RAMDOM_STATE";
$apiURL = "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=".$client_id."&redirect_uri=".$redirectURI."&state=".$state;
?>
<a href="<?php echo $apiURL ?>"><img height="50" src="http://static.nid.naver.com/oauth/small_g_in.PNG"/></a>