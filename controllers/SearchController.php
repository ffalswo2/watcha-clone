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
        case "searchVidByCategory":
            http_response_code(200);

            $genre = $_GET['genre'];
            $country = $_GET['country'];

            if(empty($_GET['genre']) and empty($_GET['country'])){
                $res->isSuccess = FALSE;
                $res->code = 250;
                $res->message = "장르와 국가 하나는 입력해 주셔야 합니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if(!empty($_GET['genre']) and !empty($_GET['country'])){
                $res->isSuccess = FALSE;
                $res->code = 240;
                $res->message = "한가지 필터로만 검색할 수 있습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if(!empty($_GET['genre']) and empty($_GET['country'])){

                if (!isValidGenreIdx($genre)) {
                    $res->isSuccess = FALSE;
                    $res->code = 222;
                    $res->message = "유효하지 않은 장르 idx입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                } else {
                    echo $genre,$country;
                    $res->result = searchVidByCategory($genre);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "영상 카테고리 검색 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }

            }

            if (empty($_GET['genre']) and !empty($_GET['country'])) {

                if (!isValidCountryIdx($country)) {
                    $res->isSuccess = FALSE;
                    $res->code = 233;
                    $res->message = "유효하지 않은 국가 idx입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                } else {
                    $res->result = searchVidByCountry($country);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "영상 카테고리 검색 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
            }

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



    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
