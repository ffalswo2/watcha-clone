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
        case "getProfile":
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

            if (checkLoginFlag($userIdxInToken,$deviceId)=='OFF') {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = getProfile($userIdxInToken);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 프로필 불러오기 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "changeProfileInfo":
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

            if (checkLoginFlag($userIdxInToken,$deviceId)=='OFF') {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

//            if (!isset($req->profileImage) or empty($req->profileImage)==true) {
//                $res->isSuccess = FALSE;
//                $res->code = 221;
//                $res->message = "이미지URL을 입력해주세요";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }

            $profileImage = $req->profileImage;
            $profileName = $req->profileName;

            if (!isset($profileImage)) {
                $profileImage = null;

                changeProfileName($profileName,$userIdxInToken);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "유저 프로필 정보 수정 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (is_numeric($profileImage)) {
                $res->isSuccess = FALSE;
                $res->code = 213;
                $res->message = "입력하신 이미지URL값의 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

//            if(mb_strlen($req->profileName,'UTF-8')< 2 or mb_strlen($req->profileName,'UTF-8') > 20) {
//                $res->isSuccess = FALSE;
//                $res->code = 215;
//                $res->message = "이름은 최소 2자 최대 20자 입니다";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }

            changeProfileInfo($profileImage,$profileName,$userIdxInToken);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 프로필 정보 수정 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getFavVideos":
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

            if (checkLoginFlag($userIdxInToken,$deviceId)=='OFF') {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = getFavVideos($profileIdxInToken);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저가 보고싶어요 표시한 영상 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getWatchingVideo":
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

            if (checkLoginFlag($userIdxInToken,$deviceId)=='OFF') {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = getWatchingVideo($profileIdxInToken);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "시청중인 영상 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getHistory":
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

            if (checkLoginFlag($userIdxInToken,$deviceId)=='OFF') {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = getHistory($profileIdxInToken);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "다 본 작품 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getVideos":
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

            if (checkLoginFlag($userIdxInToken,$deviceId)=='OFF') {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

//            if (!isset($vars['profile-idx']) or empty($vars['profile-idx'])==true) {
//                $res->isSuccess = FALSE;
//                $res->code = 222;
//                $res->message = "profileIdx를 입력해주세요";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $profileIdx = $vars['profile-idx'];
//
//            if (!is_numeric($profileIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 211;
//                $res->message = "profileIdx 타입이 틀립니다";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!checkUserIdProfileId($userIdxInToken,$profileIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 208;
//                $res->message = "다른 유저의 프로필입니다";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            if (!isValidProfileIdx($profileIdx)) {
//                $res->isSuccess = FALSE;
//                $res->code = 200;
//                $res->message = "유효하지 않은 프로필 idx입니다.";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }

            $videos = getVideos($profileIdxInToken);

            $ratingNum = $videos[0]['ratingNum'];
            $resArr = [];
            $resArr['ratingNum'] = $ratingNum;

            for ($i=0;$i<count($videos);$i++) {
                $resArr['videos'][$i]['posterImage'] = $videos[$i]['posterImage'];
                $resArr['videos'][$i]['videoName'] = $videos[$i]['videoName'];
                $resArr['videos'][$i]['year'] = $videos[$i]['year'];
            }



            $res->result = $resArr;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 기반 모든 영상 불러오기 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "cancelMembership":
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

            if (checkLoginFlag($userIdxInToken,$deviceId)=='OFF') {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (getMembershipName($userIdxInToken)== null) {
                $res->isSuccess = FALSE;
                $res->code = 301;
                $res->message = "이용하고 계신 이용권이 없습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            cancelMembership($userIdxInToken);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "베이직 이용권 멤버쉽 해지 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;






    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
