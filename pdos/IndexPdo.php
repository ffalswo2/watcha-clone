<?php

//READ
function getUsers()
{
    $pdo = pdoSqlConnect();
    $query = "select * from user;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//READ
function getUserDetail($userIdx)
{
    $pdo = pdoSqlConnect();
    $query = "select * from Users where userIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

//READ
function isValidUserIdx($userIdx)
{
    $pdo = pdoSqlConnect();
    $query = "select EXISTS(select * from Users where userIdx = ?) exist;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}


function createUser($ID, $pwd, $name)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Users (ID, pwd, name) VALUES (?,?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$ID, $pwd, $name]);

    $st = null;
    $pdo = null;

}

function addNaverUser($naverId,$email,$name,$profileImg) {
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO user (naverId,email,naverName,naverProfile) VALUES (?,?,?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$naverId,$email,$name,$profileImg]);

    $st = null;
    $pdo = null;

}

function checkNaverUser($naverId, $email) {
    $pdo = pdoSqlConnect();
    $query = "select exists(select naverId,email from user where naverId = ? and email = ?) as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$naverId,$email]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function getIdxNaverId($naverId)
{
    $pdo = pdoSqlConnect();
    $query = "select idx from user where naverId = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$naverId]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['idx'];
}

function getVideos($profileIdx)
{
    $pdo = pdoSqlConnect();
    $query = "select (select count(*) from rating where rating.profileIdx = ?) as ratingNum, posterImage, videoName, year
from video
where videoName not in (select videoName
                        from video
                                 right outer join bannedVideo on bannedVideo.videoIdx = video.idx where status = 'D');";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$profileIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function searchVidByCategory($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "select posterImage, videoName
from video
         left join genreVideo on genreVideo.videoIdx = video.idx
         left join genre on genreVideo.genreIdx = genre.idx
where genre.idx = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getGenreIdx()
{
    $pdo = pdoSqlConnect();
    $query = "select * from genre;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function isValidProfileIdx($profileIdx) {
    $pdo = pdoSqlConnect();
    $query = "select exists(select idx from profile where idx = ?) as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function checkUserIdProfileId($userIdxInToken,$profileIdx) {
    $pdo = pdoSqlConnect();
    $query = "select exists(select userIdx,idx from profile where userIdx = ? and idx = ?) as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdxInToken,$profileIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function isValidGenreIdx($keyword) {
    $pdo = pdoSqlConnect();
    $query = "select exists(select idx from genre where idx = ?) as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function isValidVideoIdx($videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "select exists(select idx from video where idx = ?) as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$videoIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function banVideo($profileIdx,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO bannedVideo (profileIdx,videoIdx) VALUES (?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx,$videoIdx]);

    $st = null;
    $pdo = null;

}

function checkHateStatus($profileIdx,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "SELECT status FROM bannedVideo WHERE profileIdx = ? and videoIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx,$videoIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['status'];
}

function changeHateToNothing($profileIdx,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE bannedVideo SET status = 'N' where profileIdx = ? and videoIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx,$videoIdx]);

    $st = null;
    $pdo = null;

}

function changeNothingToHate($profileIdx,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE bannedVideo SET status = 'D' where profileIdx = ? and videoIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx,$videoIdx]);

    $st = null;
    $pdo = null;

}

function changeNothingToLike($profileIdx,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE bannedVideo SET status = 'L' where profileIdx = ? and videoIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx,$videoIdx]);

    $st = null;
    $pdo = null;

}

function likeVideo($profileIdx,$videoIdx) {
    try {
        $pdo = pdoSqlConnect();

        $pdo->beginTransaction();

        $query1 = "INSERT INTO bannedVideo (profileIdx,videoIdx) VALUES (?,?);";

        $st = $pdo->prepare($query1);
        $st->execute([$profileIdx,$videoIdx]);

        $query2 = "UPDATE bannedVideo SET status = 'L' where profileIdx = ? and videoIdx = ?;";

        $st = $pdo->prepare($query2);
        $st->execute([$profileIdx,$videoIdx]);

        $pdo->commit();

        $st = null;
        $pdo = null;
    }
    catch (Exception $e) {
        echo $e->getMessage();
        $pdo->rollback();
    }

}

function getProfile($userIdxInToken) {
    $pdo = pdoSqlConnect();
    $query = "select profile.idx as profileIdx,name,naverProfile as profileImage from profile left join user on profile.userIdx = user.idx where profile.userIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdxInToken]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
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
