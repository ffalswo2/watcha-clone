<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (object)array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "index":
            echo "API Server";
            break;
        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;
        /*
         * API No. 4
         * API Name : 테스트 API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "getUsers":
            http_response_code(200);

            $res->result = getUsers();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 5
         * API Name : 테스트 Path Variable API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "getUserDetail":
            http_response_code(200);

            $res->result = getUserDetail($vars["userIdx"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 6
         * API Name : 테스트 Body & Insert API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "createUser":
            http_response_code(200);

            // Packet의 Body에서 데이터를 파싱합니다.
            $userID = $req->userID;
            $pwd_hash = password_hash($req->pwd, PASSWORD_DEFAULT); // Password Hash
            $name = $req->name;

            $res->result = createUser($userID, $pwd_hash, $name);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getGenreIdx":
            http_response_code(200);

            $res->result = getGenreIdx();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "장르 idx 불러오기 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getCountryIdx":
            http_response_code(200);

            $res->result = getCountryIdx();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "국가 idx 불러오기 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        case "getVideoIdx":
            http_response_code(200);

            $res->result = getVideoIdx();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "영상 idx 불러오기 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "kakaoPayClient":
            http_response_code(200);
            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];
            $userIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->userIdx;
            $profileIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->profileIdx;
            $deviceId = getDataByJWToken($jwt,JWT_SECRET_KEY)->deviceId;

            if (!isValidJWT($jwt,JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (getLoginFlag($userIdxInToken,$deviceId)=='OFF') {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (checkMembership($userIdxInToken)) {
                $res->isSuccess = FALSE;
                $res->code = 260;
                $res->message = "이미 베이직 이용권을 구매하신 회원입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $url = 'https://kapi.kakao.com/v1/payment/ready';
            $adminKey = '765511f5ffff6f735aa340bfc0a3fa96';
            $header = array(
                "Authorization: KakaoAK ".$adminKey,
                "Content-type: application/x-www-form-urlencoded;charset=utf-8"
                );
            $data = array(
                "cid" => "TC0ONETIME",
                "partner_order_id" => "5",
                "partner_user_id" => $userIdxInToken,
                "item_name" => "베이직 이용권",
                "quantity" => '1',
                "total_amount" => '18000',
                "tax_free_amount" => '0',
                "approval_url" => "https://test.ericapp.shop/success",
                "fail_url" => "https://test.ericapp.shop/fail",
                "cancel_url" => "https://test.ericapp.shop/cancel"
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);			#접속할 URL 주소
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_HEADER, 0);			# 헤더 출력 여부
            curl_setopt($ch, CURLOPT_POST, 1);				# Post Get 접속 여부
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $res = curl_exec($ch);
            echo $res;
            break;

        case "kakaoPayServer":
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];
            $userIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->userIdx;
            $profileIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->profileIdx;
            $deviceId = getDataByJWToken($jwt,JWT_SECRET_KEY)->deviceId;

            if (!isValidJWT($jwt,JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (getLoginFlag($userIdxInToken,$deviceId)=='OFF') {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isset($req->tid,$req->pgToken) or empty($req->tid)==true or empty($req->pgToken)==true) {
                $res->isSuccess = FALSE;
                $res->code = 222;
                $res->message = "tid와 pgToken을 모두 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $tid = $req->tid;
            $pgToken = $req->pgToken;

            if (is_numeric($tid)) {
                $res->isSuccess = FALSE;
                $res->code = 211;
                $res->message = "tid 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (is_numeric($pgToken)) {
                $res->isSuccess = FALSE;
                $res->code = 215;
                $res->message = "pgToken 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $url = 'https://kapi.kakao.com/v1/payment/approve';
            $adminKey = '765511f5ffff6f735aa340bfc0a3fa96';
            $header = array(
                "Authorization: KakaoAK ".$adminKey,
                "Content-type: application/x-www-form-urlencoded;charset=utf-8"
            );
            $approveData = array(
                "cid" => "TC0ONETIME",
                "partner_order_id" => "5",
                "partner_user_id" => $userIdxInToken,
                "tid" => $tid,
                "pg_token" => $pgToken
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);			#접속할 URL 주소
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);	#인증서 체크같은데 true 시 안되는 경우가 많다.
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_HEADER, 0);			# 헤더 출력 여부
            curl_setopt($ch, CURLOPT_POST, 1);				# Post Get 접속 여부
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($approveData));	# Post 값 Get 방식처럼적는다.
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result1 = curl_exec($ch);

            $paid = json_decode($result1);
            $itemName = $paid->item_name;

            if (!isset($itemName)) {
                $res->isSuccess = FALSE;
                $res->code = 422;
                $res->message = "결제 유효시간이 지났습니다. 다시 결제를 진행해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            savePayment($userIdxInToken,$itemName);
            $res->result = $itemName;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "카카오페이 결제 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
