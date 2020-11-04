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

//        case "naverSignUp":
//            http_response_code(200);
//
//            if (!isset($req->accessToken) or empty($req->accessToken)==true) {
//                $res->isSuccess = FALSE;
//                $res->code = 222;
//                $res->message = "accessToken을 입력해주세요";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $accessToken = $req->accessToken;
//
//            if (is_numeric($accessToken)) {
//                $res->isSuccess = FALSE;
//                $res->code = 211;
//                $res->message = "accessToken 타입이 틀립니다";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }
//
//            $token = $accessToken;
//            $header = "Bearer ".$token; // Bearer 다음에 공백 추가
//            $url = "https://openapi.naver.com/v1/nid/me";
//            $is_post = false;
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $url);
//            curl_setopt($ch, CURLOPT_POST, $is_post);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            $headers = array();
//            $headers[] = "Authorization: ".$header;
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//            $response = curl_exec ($ch);
//            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
////            echo "status_code:".$status_code."<br>";
//            curl_close ($ch);
//            if($status_code == 200) {
//                $profileResponse = json_decode($response);
//
//                $naverId = $profileResponse->response->id;
//                $profileImg = $profileResponse->response->profile_image;
//                $email = $profileResponse->response->email;
//                $name = $profileResponse->response->name;
//
//                if (checkNaverUser($naverId,$email)) {
//                    $res->isSuccess = FALSE;
//                    $res->code = 219;
//                    $res->message = "이미 등록된 유저입니다";
//                    echo json_encode($res, JSON_NUMERIC_CHECK);
//                    break;
//                }
//
//                addNaverUser($naverId,$email,$name,$profileImg);
//
////                $res->result = getIdxNaverId($naverId);
//                $res->isSuccess = TRUE;
//                $res->code = 100;
//                $res->message = "네이버 회원가입 성공";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            } else {
//                echo "Error 내용:".$response;
//            }

        case "getVideos":
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];
            $userIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->userIdx;
            $profileIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->profileIdx;

            if (!isValidJWT($jwt,JWT_SECRET_KEY)) {
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

        case "searchVidByCategory":
            http_response_code(200);

            $keyword = $_GET['keyword'];

            if (!isValidGenreIdx($keyword)) {
                $res->isSuccess = FALSE;
                $res->code = 222;
                $res->message = "유효하지 않은 장르 idx입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = searchVidByCategory($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "영상 카테고리 검색 성공";
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

        case "banVideo":
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];
            $userIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->userIdx;
            $profileIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->profileIdx;

            if (!isValidJWT($jwt,JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isset($vars['video-idx']) or empty($vars['video-idx'])==true) {
                $res->isSuccess = FALSE;
                $res->code = 221;
                $res->message = "videoIdx를 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $videoIdx = $vars['video-idx'];

            if (!is_numeric($videoIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 213;
                $res->message = "videoIdx 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!isValidVideoIdx($videoIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 223;
                $res->message = "유효하지 않은 비디오 idx입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (checkHateStatus($profileIdxInToken,$videoIdx)=='D') { // status 확인
                changeHateToNothing($profileIdxInToken,$videoIdx); // 'N'으로 바꾸기
                $res->isSuccess = TRUE;
                $res->code = 411;
                $res->message = "해당 영상 관심없습니다 취소";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (checkHateStatus($profileIdxInToken,$videoIdx)=='L') { // status 확인
                changeNothingToHate($profileIdxInToken,$videoIdx); // 'D'으로 바꾸기
                $res->isSuccess = TRUE;
                $res->code = 411;
                $res->message = "해당 영상 관심없습니다 취소";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (checkHateStatus($profileIdxInToken,$videoIdx)=='N') { // status 확인
                changeNothingToHate($profileIdxInToken,$videoIdx); // 'D'으로 바꾸기
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "해당 영상 관심없습니다 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }


            banVideo($profileIdxInToken,$videoIdx); // default D 로 insert
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "해당 영상 관심없습니다 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "likeVideo":
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];
            $userIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->userIdx;
            $profileIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->profileIdx;

            if (!isValidJWT($jwt,JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isset($vars['video-idx']) or empty($vars['video-idx'])==true) {
                $res->isSuccess = FALSE;
                $res->code = 221;
                $res->message = "videoIdx를 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $videoIdx = $vars['video-idx'];

            if (!is_numeric($videoIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 213;
                $res->message = "videoIdx 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!isValidVideoIdx($videoIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 223;
                $res->message = "유효하지 않은 비디오 idx입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (checkHateStatus($profileIdxInToken,$videoIdx)=='L') { // status 확인
                changeHateToNothing($profileIdxInToken,$videoIdx); // 'N'으로 바꾸기
                $res->isSuccess = TRUE;
                $res->code = 411;
                $res->message = "해당 영상 보고싶어요 취소";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (checkHateStatus($profileIdxInToken,$videoIdx)=='D') { // status 확인
                changeNothingToLike($profileIdxInToken,$videoIdx); // 'L'으로 바꾸기
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "해당 영상 보고싶어요 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (checkHateStatus($profileIdxInToken,$videoIdx)=='N') { // status 확인
                changeNothingToLike($profileIdxInToken,$videoIdx); // 'L'으로 바꾸기
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "해당 영상 보고싶어요 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }


            likeVideo($profileIdxInToken,$videoIdx); // L 로 insert
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "해당 영상 보고싶어요 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getProfile":
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];
            $userIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->userIdx;

            if (!isValidJWT($jwt,JWT_SECRET_KEY)) {
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

            if (!isValidJWT($jwt,JWT_SECRET_KEY)) {
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

        case "rateWithStar":
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];
            $userIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->userIdx;
            $profileIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->profileIdx;

            if (!isValidJWT($jwt,JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isset($vars['video-idx']) or empty($vars['video-idx'])==true) {
                $res->isSuccess = FALSE;
                $res->code = 221;
                $res->message = "videoIdx를 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $videoIdx = $vars['video-idx'];

            if (!is_numeric($videoIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 213;
                $res->message = "videoIdx 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!isValidVideoIdx($videoIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 223;
                $res->message = "유효하지 않은 비디오 idx입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!isset($req->ratingStar) or empty($req->ratingStar)==true) {
                $res->isSuccess = FALSE;
                $res->code = 266;
                $res->message = "별점 점수를 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $ratingStar = $req->ratingStar;

            if (!is_numeric($ratingStar)) {
                $res->isSuccess = FALSE;
                $res->code = 216;
                $res->message = "ratingStar 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if ($ratingStar > 5) {
                $res->isSuccess = FALSE;
                $res->code = 250;
                $res->message = "줄 수 있는 최대 별점은 5점입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (checkUserAlreadyRate($profileIdxInToken,$videoIdx) and checkRateDeleted($profileIdxInToken,$videoIdx)=='N') { // 평가한 영상은 또 평가할 수 없음
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "한번 평가한 영상은 더 이상 평가할 수 없습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            rateWithStar($profileIdxInToken,$videoIdx,$ratingStar);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 별점 평가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "searchVidByName":
            http_response_code(200);

            $keyword = $_GET['keyword'];
            $keyword = str_replace(' ','',$keyword);

            $res->result = searchVidByName($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "영상 검색 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getPopularVideos":
            http_response_code(200);

            $popularVideos = getPopularVideosByOrder();

            $res->result = $popularVideos;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "인기 검색 영상 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "deleteRate":
            http_response_code(200);

            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];
            $userIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->userIdx;
            $profileIdxInToken = getDataByJWToken($jwt,JWT_SECRET_KEY)->profileIdx;

            if (!isValidJWT($jwt,JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if (!isset($vars['video-idx']) or empty($vars['video-idx'])==true) {
                $res->isSuccess = FALSE;
                $res->code = 221;
                $res->message = "videoIdx를 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $videoIdx = $vars['video-idx'];

            if (!is_numeric($videoIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 213;
                $res->message = "videoIdx 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!isValidVideoIdx($videoIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 223;
                $res->message = "유효하지 않은 비디오 idx입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!checkUserAlreadyRate($profileIdxInToken,$videoIdx)) { // 이미 삭제된건지 확인
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "평가하지 않은 영상idx입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            deleteRate($profileIdxInToken,$videoIdx);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 별점 평가 취소 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
