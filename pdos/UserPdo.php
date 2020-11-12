<?php

//READ
function getProfile($userIdxInToken) {
    $pdo = pdoSqlConnect();
    $query = "select profile.idx as profileIdx, name, profileImage, membership.membershipName as membershipName, membership.expireDate as memberShipExpireDate
from profile
         left join user on profile.userIdx = user.idx
         left join membership on membership.userIdx = user.idx
where profile.userIdx = ?
group by name;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdxInToken]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function changeProfileName($profileName,$userIdxInToken) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE profile SET `name` = ? where userIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$profileName,$userIdxInToken]);

    $st = null;
    $pdo = null;

}

function changeProfileInfo($profileImage,$profileName,$userIdxInToken) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE profile SET profileImage = ?,`name` = ? where userIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$profileImage,$profileName,$userIdxInToken]);

    $st = null;
    $pdo = null;

}

function getFavVideos($profileIdxInToken) {
    $pdo = pdoSqlConnect();
    $query = "select video.idx as videoIdx,posterImage, videoName
from bannedVideo
         left join video on bannedVideo.videoIdx = video.idx
where bannedVideo.profileIdx = ?
  and bannedVideo.status = 'L';";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdxInToken]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getWatchingVideo($profileIdxInToken)
{
    $pdo = pdoSqlConnect();
    $query = "select video.idx as videoIdx,posterImage,
       IF(episodeNum is null, videoName, concat(videoName, ':에피소드 ', max(episodeNum))) as watchingTitle,watchTime
from watchingVideo
         left join video on watchingVideo.videoIdx = video.idx
         left join profile on watchingVideo.profileIdx = profile.idx
         left join episode on watchingVideo.episodeIdx = episode.idx
where profile.idx = ? and watchingVideo.isDeleted = 'N' group by videoName;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$profileIdxInToken]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getHistory($profileIdxInToken)
{
    $pdo = pdoSqlConnect();
    $query = "select video.idx as videoIdx,posterImage,videoName from video left join history on history.videoIdx = video.idx where history.profileIdx = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$profileIdxInToken]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getVideos($profileIdx)
{
    $pdo = pdoSqlConnect();
    $query = "select (select count(*) from rating where rating.profileIdx = ? and rating.isDeleted = 'N') as ratingNum,
       video.idx as videoIdx,
       posterImage,
       videoName,
       year
from video
where videoName not in (select videoName
                        from video
                                 right outer join bannedVideo on bannedVideo.videoIdx = video.idx
                                 left join profile on bannedVideo.profileIdx = profile.idx
                        where status = 'D'
                          and bannedVideo.profileIdx = ?)
  and videoName not in (select videoName
                        from video
                                 left join rating on rating.videoIdx = video.idx
                        where rating.isDeleted = 'N');";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$profileIdx,$profileIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function cancelMembership($userIdxInToken) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE membership
SET membershipName = null
where userIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdxInToken]);

    $st = null;
    $pdo = null;

}

function getMembershipName($userIdxInToken)
{
    $pdo = pdoSqlConnect();
    $query = "select membershipName from membership where userIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdxInToken]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['membershipName'];
}

function checkLoginFlag($userIdxInToken,$deviceId){
    $pdo = pdoSqlConnect();
    $query = "select loginFlag from maxLogin where userIdx = ? and deviceId = ?;";


    $st = $pdo->prepare($query);
    $st->execute([$userIdxInToken,$deviceId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null; $pdo = null;

    return $res[0]['loginFlag'];
}

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
    $query = "insert into pushAlarm(userIdx, `comment`)  values (?,concat('오늘은 ',?,' 시청하는게 어떠세요?'));";

    $st = $pdo->prepare($query);
    $st->execute([$userIdxInToken,$videoName]);

    $st = null;
    $pdo = null;

}

// CREATE
//    function addMaintenance($message){
//        $pdo = pdoSqlConnect();
//        $query = "INSERT INTO MAINTENANCE (MESSAGE) VALUES (?);";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message]);
//
//        $st = null;
//        $pdo = null;
//
//    }


// UPDATE
//    function updateMaintenanceStatus($message, $status, $no){
//        $pdo = pdoSqlConnect();
//        $query = "UPDATE MAINTENANCE
//                        SET MESSAGE = ?,
//                            STATUS  = ?
//                        WHERE NO = ?";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message, $status, $no]);
//        $st = null;
//        $pdo = null;
//    }

// RETURN BOOLEAN
//    function isRedundantEmail($email){
//        $pdo = pdoSqlConnect();
//        $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE EMAIL= ?) AS exist;";
//
//
//        $st = $pdo->prepare($query);
//        //    $st->execute([$param,$param]);
//        $st->execute([$email]);
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res = $st->fetchAll();
//
//        $st=null;$pdo = null;
//
//        return intval($res[0]["exist"]);
//
//    }
