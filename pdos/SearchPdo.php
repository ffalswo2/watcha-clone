<?php

//READ
function searchVidByCategory($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "select idx as videoIdx,posterImage, videoName
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

function searchVidByName($keyword)
{
    try {
        $pdo = pdoSqlConnect();

        $pdo->beginTransaction();

        $query1 = "select idx as videoIdx,posterImage,videoName from video where replace(videoName, ' ', '') like concat('%',?,'%');";

        $st = $pdo->prepare($query1);
        $st->execute([$keyword]);

        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

        $query2 = "INSERT INTO searchHistory (keyword) VALUES (?);";

        $st = $pdo->prepare($query2);
        $st->execute([$keyword]);

        $pdo->commit();

        $st = null;
        $pdo = null;

        return $res;
    }
    catch (Exception $e) {
        echo $e->getMessage();
        $pdo->rollback();
    }
}

function isValidCountryIdx($keyword) {
    $pdo = pdoSqlConnect();
    $query = "select exists(select idx from country where idx = ?) as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function searchVidByCountry($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "select idx as videoIdx,posterImage, videoName
from video
         left join countryVideo on countryVideo.videoIdx = video.idx
         left join country on countryVideo.countryIdx = country.idx
where country.idx = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getPopularVideosByOrder() {
    $pdo = pdoSqlConnect();
    $query = "select idx as videoIdx,posterImage, videoName
from searchHistory
         left join video on keyword = videoName
where keyword = videoName
group by keyword
order by count(keyword) DESC
limit 6;";

    $st = $pdo->prepare($query);
    $st->execute([]);
    //    $st->execute();
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
