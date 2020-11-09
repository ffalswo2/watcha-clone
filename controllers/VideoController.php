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
        case "playVideo":
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

            if (empty($_GET['video']) and empty($_GET['episode']))  {
                $res->isSuccess = FALSE;
                $res->code = 250;
                $res->message = "video,episode 둘 중 하나는 입력해주셔야 합니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

//            if (!empty($_GET['video']) and !empty($_GET['episode']))  {
//                $res->isSuccess = FALSE;
//                $res->code = 260;
//                $res->message = "videoIdx,episodeIdx 둘 중 하나만 입력해주셔야 합니다";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }

            $videoIdx = $_GET['video'];
            $episodeIdx = $_GET['episode'];

            if (!isset($episodeIdx)) { //영화를 볼때

                if (!is_numeric($videoIdx)) {
                    $res->isSuccess = FALSE;
                    $res->code = 212;
                    $res->message = "videoIdx 타입이 틀립니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }

                if (!isValidVideoIdx($videoIdx)) {
                    $res->isSuccess = FALSE;
                    $res->code = 222;
                    $res->message = "유효하지 않은 비디오 idx입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }

                if (!isMovie($videoIdx)) {
                    $res->isSuccess = FALSE;
                    $res->code = 430;
                    $res->message = "해당 영상은 영화가 아닙니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }

                if (checkProfileHistory($profileIdxInToken,$videoIdx) and checkHistoryDeleted($profileIdxInToken,$videoIdx)=='N') {
                    // history 테이블에 있고(이미 한번 다본 영화)
                    playMovieAlreadyWatched($profileIdxInToken,$videoIdx); // history에서 지우고 watchingVideo로 다시가야함
                    $res->isSuccess = TRUE;
                    $res->code = 180;
                    $res->message = "영화 재시청 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }

                if (checkProfileVideoWatch($profileIdxInToken,$videoIdx)) {
                    $res->result = playMovieWithoutInsert($videoIdx)[0];
                    $res->isSuccess = TRUE;
                    $res->code = 110;
                    $res->message = "영화 URL 불러오기 성공(영상 재생)";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }


                $res->result = playMovie($profileIdxInToken,$videoIdx)[0];
                $res->isSuccess = TRUE;
                $res->code = 110;
                $res->message = "영화 URL 불러오기 성공(영상 재생)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!is_numeric($videoIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 212;
                $res->message = "videoIdx 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!isValidVideoIdx($videoIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 222;
                $res->message = "유효하지 않은 비디오 idx입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!is_numeric($episodeIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 213;
                $res->message = "episodeIdx 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!isValidEpisodeIdx($episodeIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 223;
                $res->message = "유효하지 않은 episodeIdx입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if(!checkVideoEpisodeCorrect($videoIdx,$episodeIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 310;
                $res->message = "해당 드라마의 에피소드가 아닙니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (checkProfileHistory($profileIdxInToken,$videoIdx) and checkHistoryDeleted($profileIdxInToken,$videoIdx)=='N') {
                // history 테이블에 있고(이미 한번 다본 영화 드라마)
                playDramaAlreadyWatched($profileIdxInToken,$videoIdx,$episodeIdx); // history에서 지우고 watchingVideo로 다시가야함
                $res->isSuccess = TRUE;
                $res->code = 180;
                $res->message = "드라마 재시청 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (checkProfileEpisodeWatch($profileIdxInToken,$episodeIdx)) {
                $res->result = playDramaWithoutInsert($episodeIdx)[0];
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "드라마 URL 불러오기 성공(영상 재생)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = playDrama($profileIdxInToken,$videoIdx,$episodeIdx)[0];
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "드라마 URL 불러오기 성공(영상 재생)";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getVideoInfo":
            http_response_code(200);

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

            if (checkMovie($videoIdx)) {
                $res->result = getMovieInfo($videoIdx);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "특정 영화 정보 조회 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getDramaInfo($videoIdx);
            $res->isSuccess = TRUE;
            $res->code = 110;
            $res->message = "특정 드라마 정보 조회 성공";
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

            if (checkUserAlreadyRate($profileIdxInToken,$videoIdx) and checkRateDeleted($profileIdxInToken,$videoIdx)==1) { // 평가한 영상은 또 평가할 수 없음
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

        case "changeWatchTime":
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

            if (!isset($req->watchTime) or empty($req->watchTime)==true) {
                $res->isSuccess = FALSE;
                $res->code = 221;
                $res->message = "watchTime을 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (empty($req->videoIdx) and empty($req->episodeIdx))  {
                $res->isSuccess = FALSE;
                $res->code = 250;
                $res->message = "videoIdx,episodeIdx 둘 중 하나는 입력해주셔야 합니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

//            if (!empty($req->videoIdx) and !empty($req->episodeIdx))  {
//                $res->isSuccess = FALSE;
//                $res->code = 260;
//                $res->message = "videoIdx,episodeIdx 둘 중 하나만 입력해주셔야 합니다";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                break;
//            }

            $watchTime = $req->watchTime;
            $videoIdx = $req->videoIdx;
            $episodeIdx = $req->episodeIdx;

            if (!is_numeric($watchTime)) {
                $res->isSuccess = FALSE;
                $res->code = 213;
                $res->message = "watchTime 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!isset($episodeIdx)) { //영화를 볼때

                if (!is_numeric($videoIdx)) {
                    $res->isSuccess = FALSE;
                    $res->code = 212;
                    $res->message = "videoIdx 타입이 틀립니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }

                if (!isValidVideoIdx($videoIdx)) {
                    $res->isSuccess = FALSE;
                    $res->code = 222;
                    $res->message = "유효하지 않은 비디오 idx입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }

                if (!checkProfileVideoWatch($profileIdxInToken,$videoIdx)) {
                    $res->isSuccess = FALSE;
                    $res->code = 390;
                    $res->message = "시청중인 영화가 아닙니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }


                if (isMovie($videoIdx)-10 <= $watchTime/60 ) {

                    moveMovieToHistory($profileIdxInToken,$videoIdx);
                    $res->isSuccess = TRUE;
                    $res->code = 120;
                    $res->message = "영화를 모두 시청했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }

                changeMovieWatchTime($watchTime,$profileIdxInToken,$videoIdx);
                $res->isSuccess = TRUE;
                $res->code = 110;
                $res->message = "영화 시청시간 수정 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!is_numeric($videoIdx)) { // 드라마를 볼 때
                $res->isSuccess = FALSE;
                $res->code = 212;
                $res->message = "videoIdx 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!isValidVideoIdx($videoIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 222;
                $res->message = "유효하지 않은 비디오 idx입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!is_numeric($episodeIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 213;
                $res->message = "episodeIdx 타입이 틀립니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!isValidEpisodeIdx($episodeIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 223;
                $res->message = "유효하지 않은 episodeIdx입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if(!checkVideoEpisodeCorrect($videoIdx,$episodeIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 310;
                $res->message = "해당 드라마의 에피소드가 아닙니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!checkProfileEpisodeWatch($profileIdxInToken,$episodeIdx)) {
                $res->isSuccess = FALSE;
                $res->code = 380;
                $res->message = "시청중인 드라마가 아닙니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (getLastEpisodeIdx($videoIdx)== $episodeIdx and getEpisodeTime($episodeIdx)-3 <= $watchTime/60) {

                moveDramaToHistory($profileIdxInToken,$videoIdx,$episodeIdx);
                $res->isSuccess = TRUE;
                $res->code = 150;
                $res->message = "드라마를 모두 시청했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            changeDramaWatchTime($watchTime,$profileIdxInToken,$episodeIdx);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "드라마 시청시간 수정 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "deleteHistory":
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

            if (!isset($req->videoIdx) or empty($req->videoIdx)==true) {
                $res->isSuccess = FALSE;
                $res->code = 221;
                $res->message = "videoIdx를 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $videoIdx = $req->videoIdx;
            $errorArr = array();

            foreach ($videoIdx as $value) {

                if (!is_numeric($value)) {
                    array_push($errorArr,1);
                    break;
                }
                if (!isValidVideoIdx($value)) {
                    array_push($errorArr,2);
                    break;
                }

                if (!checkProfileHistory($profileIdxInToken,$value)) {
                    array_push($errorArr,3);
                    break;
                }
            }

            if (in_array(1,$errorArr)) {
                $res->isSuccess = FALSE;
                $res->code = 249;
                $res->message = "틀린 videoIdx 타입이 포함되어 있습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (in_array(2,$errorArr)) {
                $res->isSuccess = FALSE;
                $res->code = 248;
                $res->message = "유효하지않은 videoIdx가 포함되어 있습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (in_array(3,$errorArr)) {
                $res->isSuccess = FALSE;
                $res->code = 401;
                $res->message = "다보지않은 video의 idx가 포함되어 있습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            foreach ($videoIdx as $value) {
                deleteHistory($value,$profileIdxInToken);
            }

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "다 본 작품 항목 삭제 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "deleteWatchingVideo":
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

            if (!isset($req->videoIdx) or empty($req->videoIdx)==true) {
                $res->isSuccess = FALSE;
                $res->code = 221;
                $res->message = "videoIdx를 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $videoIdx = $req->videoIdx;
            $errorArr = array();

            foreach ($videoIdx as $value) {

                if (!is_numeric($value)) {
                    array_push($errorArr,1);
                    break;
                }
                if (!isValidVideoIdx($value)) {
                    array_push($errorArr,2);
                    break;
                }

                if (!checkProfileVideoWatch($profileIdxInToken,$value)) {
                    array_push($errorArr,3);
                    break;
                }
            }

            if (in_array(1,$errorArr)) {
                $res->isSuccess = FALSE;
                $res->code = 249;
                $res->message = "틀린 videoIdx 타입이 포함되어 있습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (in_array(2,$errorArr)) {
                $res->isSuccess = FALSE;
                $res->code = 248;
                $res->message = "유효하지않은 videoIdx가 포함되어 있습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (in_array(3,$errorArr)) {
                $res->isSuccess = FALSE;
                $res->code = 401;
                $res->message = "조회하지 않은 video의 idx가 포함되어 있습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            foreach ($videoIdx as $value) {
                deleteWatchingVideo($value,$profileIdxInToken);
            }

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "이어보기 항목 삭제 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;



    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
