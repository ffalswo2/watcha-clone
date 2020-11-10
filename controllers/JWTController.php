<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 1
         * API Name : JWT 생성 테스트 API (로그인)
         * 마지막 수정 날짜 : 20.08.29
         */
        case "createJwt":
            http_response_code(200);

            // 1) 로그인 시 email, password 받기
            if (!isValidUser($req->userID, $req->pwd)) { // JWTPdo.php 에 구현
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 아이디 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            // 2) JWT 발급
            // Payload에 맞게 다시 설정 요함, 아래는 Payload에 userIdx를 넣기 위한 과정
            $userIdx = getUserIdxByID($req->userID);  // JWTPdo.php 에 구현
            $jwt = getJWT($userIdx, JWT_SECRET_KEY); // function.php 에 구현

            $res->result->jwt = $jwt;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 2
         * API Name : JWT 유효성 검사 테스트 API
         * 마지막 수정 날짜 : 20.08.29
         */
        case "validateJwt":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            // 1) JWT 유효성 검사
            if (!isValidJWT($jwt, JWT_SECRET_KEY)) { // function.php 에 구현
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다"; 
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            // 2) JWT Payload 반환
            http_response_code(200);
            $res->result = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "naverLogin":
            http_response_code(200);

            if (!isset($req->accessToken) or empty($req->accessToken)==true) {
                $res->isSuccess = FALSE;
                $res->code = 222;
                $res->message = "accessToken을 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $accessToken = $req->accessToken;
            $deviceId = $req->deviceId;

            if (is_numeric($accessToken)) {
                $res->isSuccess = FALSE;
                $res->code = 211;
                $res->message = "accessToken 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $token = $accessToken;
            $header = "Bearer ".$token; // Bearer 다음에 공백 추가
            $url = "https://openapi.naver.com/v1/nid/me";
            $is_post = false;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, $is_post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $headers = array();
            $headers[] = "Authorization: ".$header;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec ($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//            echo "status_code:".$status_code."<br>";
            curl_close ($ch);
            if($status_code == 200) {
                $profileResponse = json_decode($response);

                $naverId = $profileResponse->response->id;
                $profileImg = $profileResponse->response->profile_image;
                $email = $profileResponse->response->email;
                $name = $profileResponse->response->name;

                // 1) 로그인 시 email, password 받기
                if (!isValidNaverUser($naverId, $email)) { // JWTPdo.php 에 구현, 회원가입까지 시키고 jwt 발급을 여기서하도록

                    addNaverUser($naverId,$email,$name,$profileImg);

                    // 2) JWT 발급
                    // Payload에 맞게 다시 설정 요함, 아래는 Payload에 userIdx를 넣기 위한 과정
                    $userIdx = getUserIdxByNaverId($naverId);  // JWTPdo.php 에 구현
                    $profileIdx = getProfileIdxByUserIdx($userIdx);

                    $jwt = getJWT($userIdx,$profileIdx,$deviceId, JWT_SECRET_KEY); // function.php 에 구현

                    if (getLoginDeviceNum($userIdx)==3) {
                        loginCutIn($userIdx,$deviceId,$jwt);
                        $res->result->jwt = $jwt;
                        $res->isSuccess = TRUE;
                        $res->code = 101;
                        $res->message = "네이버 회원가입 후 로그인 성공(jwt 발급 성공)";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        break;
                    }

                    addLoginLog($userIdx,$deviceId,$jwt); // eric
                    $res->result->jwt = $jwt;
                    $res->isSuccess = TRUE;
                    $res->code = 101;
                    $res->message = "네이버 회원가입 후 로그인 성공(jwt 발급 성공)";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }

                // 2) JWT 발급
                // Payload에 맞게 다시 설정 요함, 아래는 Payload에 userIdx를 넣기 위한 과정
                $userIdx = getUserIdxByNaverId($naverId);  // JWTPdo.php 에 구현
                $profileIdx = getProfileIdxByUserIdx($userIdx);

                $jwt = getJWT($userIdx,$profileIdx,$deviceId, JWT_SECRET_KEY); // function.php 에 구현

                if (getLoginDeviceNum($userIdx)==3) {
                    loginCutIn($userIdx,$deviceId,$jwt);
                    $res->result->jwt = $jwt;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "네이버 로그인 성공(jwt 발급 성공)";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }

                addLoginLog($userIdx,$deviceId,$jwt); // eric
                $res->result->jwt = $jwt;
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "네이버 로그인 성공(jwt 발급 성공)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            } else {
//                echo "Error 내용:".$response;
                $res->result->$response;
                $res->isSuccess = FALSE;
                $res->code = 24;
                $res->message = "인증 실패 오류";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }






    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
