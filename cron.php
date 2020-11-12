!#/usr/bin/php7.4 -q
<?php
include "/var/www/html/test/watcha_mock_server_eric/pdos/DatabasePdo.php";


function getLikeGenre($profileIdxInToken)
{
    $pdo = pdoSqlConnect();
    $query = "select a.genreIdx,a.genreTitle,count(a.genreTitle) as count from (select genre.idx as genreIdx, genreTitle
from bannedVideo
         left join video on bannedVideo.videoIdx = video.idx
         left join genreVideo on genreVideo.videoIdx = video.idx
         left join genre on genreVideo.genreIdx = genre.idx
where bannedVideo.profileIdx = ?
  and bannedVideo.status = 'L'
union all
select genre.idx as genreIdx, genreTitle
from rating
         left join video on rating.videoIdx = video.idx
         left join genreVideo on genreVideo.videoIdx = video.idx
         left join genre on genreVideo.genreIdx = genre.idx
where rating.profileIdx = ?
  and rating.isDeleted = 'N' and rating.rating >= 3) a group by a.genreTitle order by count DESC;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$profileIdxInToken,$profileIdxInToken]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getTotalGenre($profileIdxInToken)
{
    $pdo = pdoSqlConnect();
    $query = "select count(a.genreIdx) as total from (select genre.idx as genreIdx, genreTitle
from bannedVideo
         left join video on bannedVideo.videoIdx = video.idx
         left join genreVideo on genreVideo.videoIdx = video.idx
         left join genre on genreVideo.genreIdx = genre.idx
where bannedVideo.profileIdx = ?
  and bannedVideo.status = 'L'
union all
select genre.idx as genreIdx, genreTitle
from rating
         left join video on rating.videoIdx = video.idx
         left join genreVideo on genreVideo.videoIdx = video.idx
         left join genre on genreVideo.genreIdx = genre.idx
where rating.profileIdx = ?
  and rating.isDeleted = 'N' and rating.rating >= 3)a;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$profileIdxInToken,$profileIdxInToken]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['total'];
}

function getRecommendVid($genreId){
    $pdo = pdoSqlConnect();
    $query = "select posterImage, videoName
from video
         left join genreVideo on genreVideo.videoIdx = video.idx
         left join genre on genreVideo.genreIdx = genre.idx
where genre.idx = ?;";


    $st = $pdo->prepare($query);
    $st->execute([$genreId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null; $pdo = null;

    return $res;
}

function pushAlarm($userIdxInToken,$videoName) {
    $pdo = pdoSqlConnect();
    $query = "insert into pushAlarm(userIdx, `comment`)  values (?,concat('오늘은',?,'시청하는게 어떠세요?'));";

    $st = $pdo->prepare($query);
    $st->execute([$userIdxInToken,$videoName]);

    $st = null;
    $pdo = null;

}

$url = 'https://test.ericapp.shop/video/recommend';
$adminKey = '765511f5ffff6f735aa340bfc0a3fa96';
$header = array(
    'X-ACCESS-TOKEN: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2MDQ0MDE2MDIsImV4cCI6MTYzNTkzNzYwMiwidXNlcklkeCI6IjgiLCJwcm9maWxlSWR4IjoiMyJ9.uczVkg7r_-fOzcOB-X3XGyzd85fd-K8kaG08TuGyRQY'
);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);			#접속할 URL 주소
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);	#인증서 체크같은데 true 시 안되는 경우가 많다.
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_HEADER, 1);# 헤더 출력 여부0
curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);

$data = curl_exec($ch);

$favoriteGenre = getLikeGenre($profileIdxInToken);
$totalGenre = getTotalGenre($profileIdxInToken);

$arr = array(
    array()
);

for ($i=0;$i<count($favoriteGenre);$i++) {
    $arr[$i][0] = $favoriteGenre[$i]['genreIdx'];
    $arr[$i][1] = round($favoriteGenre[$i]['count']/$totalGenre*100,0);
}

for ($i=0;$i<count($arr);$i++) {
//                $ball = mt_rand(1,100);
    $ball = mt_rand(1,100);

    if ($ball <= $arr[$i][1]) {
        $genreId = $arr[$i][0];
        if (isset($genreId)) {
            $recommendVid = getRecommendVid($genreId)[mt_rand(0,count(getRecommendVid($genreId))-1)];
            pushAlarm($userIdxInToken,$recommendVid['videoName']);
            $res->result = $recommendVid;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "사용자 추천 영상 불러오기 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        } else {
            continue;
        }

    } else {
        continue;
    }

}
