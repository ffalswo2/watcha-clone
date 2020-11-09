<?php

//READ
function getProfile($userIdxInToken) {
    $pdo = pdoSqlConnect();
    $query = "select profile.idx as profileIdx, name, profileImage,membershipName,membership.expireDate as memberShipExpireDate
from profile
         left join user on profile.userIdx = user.idx
         left join membership on profile.userIdx = user.idx
where profile.userIdx = ? group by name;";

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
