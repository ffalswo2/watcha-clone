<?php

require './pdos/DatabasePdo.php';
require './pdos/IndexPdo.php';
require './pdos/JWTPdo.php';
require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

date_default_timezone_set('Asia/Seoul');
ini_set('default_charset', 'utf8mb4');

//에러출력하게 하는 코드
//error_reporting(E_ALL); ini_set("display_errors", 1);

//Main Server API main main
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    /* ******************   JWT   ****************** */
    $r->addRoute('POST', '/jwt', ['JWTController', 'createJwt']);   // JWT 생성: 로그인 + 해싱된 패스워드 검증 내용 추가
    $r->addRoute('GET', '/jwt', ['JWTController', 'validateJwt']);  // JWT 유효성 검사

    /* ******************   Test   ****************** */
    $r->addRoute('GET', '/', ['IndexController', 'index']);
    $r->addRoute('GET', '/users', ['IndexController', 'getUsers']);
    $r->addRoute('GET', '/users/{userIdx}', ['IndexController', 'getUserDetail']);
    $r->addRoute('POST', '/user', ['IndexController', 'createUser']); // 비밀번호 해싱 예시 추가

    $r->addRoute('POST', '/naver/user', ['IndexController', 'naverSignUp']); // 네이버 회원가입
    $r->addRoute('POST', '/naver/login', ['JWTController', 'naverLogin']); // 네이버 로그인
    $r->addRoute('GET', '/user/videos', ['IndexController', 'getVideos']); // 유저 기반 모든 영상 조회 (평가하기)
    $r->addRoute('GET', '/genre', ['IndexController', 'getGenreIdx']); // 장르 idx 조회
    $r->addRoute('GET', '/videos/category', ['IndexController', 'searchVidByCategory']); // 카테고리별로 영상 검색
    $r->addRoute('PATCH', '/user/no-video/{video-idx}', ['IndexController', 'banVideo']); // 특정 영상 관심없어요 추가/삭제
    $r->addRoute('PATCH', '/user/good-video/{video-idx}', ['IndexController', 'likeVideo']); // 특정 영상 보고싶어요 추가/삭제
    $r->addRoute('POST', '/user/rating-video/{video-idx}', ['IndexController', 'rateWithStar']); // 별점 평가하기
    $r->addRoute('GET', '/user/profile', ['IndexController', 'getProfile']); // 유저 프로필 조회
    $r->addRoute('PATCH', '/user/profile-info', ['IndexController', 'changeProfileInfo']); // 유저 프로필 이미지 수정



//    $r->addRoute('GET', '/users', 'get_all_users_handler');
//    // {id} must be a number (\d+)
//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//    // The /{title} suffix is optional
//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'IndexController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/IndexController.php';
                break;
            case 'JWTController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/JWTController.php';
                break;
//            case 'EventController':
//                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
//                require './controllers/EventController.php';
//                break;
//            case 'ProductController':
//                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
//                require './controllers/ProductController.php';
//                break;
//            case 'SearchController':
//                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
//                require './controllers/SearchController.php';
//                break;
//            case 'ReviewController':
//                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
//                require './controllers/ReviewController.php';
//                break;
//            case 'ElementController':
//                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
//                require './controllers/ElementController.php';
//                break;
//            case 'AskFAQController':
//                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
//                require './controllers/AskFAQController.php';
//                break;
        }

        break;
}
