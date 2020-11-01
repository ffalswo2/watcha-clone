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

        case "naverSignUp":
            http_response_code(200);

            if (!isset($req->accessToken) or empty($req->accessToken)==true) {
                $res->isSuccess = FALSE;
                $res->code = 222;
                $res->message = "accessToken을 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $accessToken = $req->accessToken;

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

                if (checkNaverUser($naverId,$email)) {
                    $res->isSuccess = FALSE;
                    $res->code = 219;
                    $res->message = "이미 등록된 유저입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }

                addNaverUser($naverId,$email,$name,$profileImg);

                $res->result = getIdxNaverId($naverId);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "네이버 회원가입 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            } else {
                echo "Error 내용:".$response;
            }





    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
