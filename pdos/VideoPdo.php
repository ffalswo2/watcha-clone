<?php

//READ
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

function isMovie($videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "SELECT `time` FROM video WHERE idx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$videoIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['time'];
}

function checkProfileVideoWatch($profileIdxInToken,$videoIdx)
{
    $pdo = pdoSqlConnect();
    $query = "select EXISTS(select * from watchingVideo where profileIdx = ? and videoIdx = ?) exist;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdxInToken,$videoIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function playMovieWithoutInsert($videoIdx)
{
    $pdo = pdoSqlConnect();
    $query = "select videoUrl,watchTime
from video left join watchingVideo on watchingVideo.videoIdx = video.idx
where video.idx = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$videoIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function playMovie($profileIdxInToken,$videoIdx) {
    try {
        $pdo = pdoSqlConnect();

        $pdo->beginTransaction();

        $query1 = "INSERT INTO watchingVideo (profileIdx, videoIdx)
VALUES (?, ?);";

        $st = $pdo->prepare($query1);
        $st->execute([$profileIdxInToken,$videoIdx]);

        $query2 = "select videoUrl,watchTime
from video left join watchingVideo on watchingVideo.videoIdx = video.idx
where video.idx = ?;";

        $st = $pdo->prepare($query2);
        $st->execute([$videoIdx]);
        //    $st->execute();
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

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

function playMovieAlreadyWatched($profileIdxInToken,$videoIdx) {
    try {
        $pdo = pdoSqlConnect();

        $pdo->beginTransaction();

        $query1 = "UPDATE history SET isDeleted = 'Y' where profileIdx = ? and videoIdx = ?;";

        $st = $pdo->prepare($query1);
        $st->execute([$profileIdxInToken,$videoIdx]);

        $query2 = "INSERT INTO watchingVideo (profileIdx, videoIdx)
VALUES (?, ?);";

        $st = $pdo->prepare($query2);
        $st->execute([$profileIdxInToken,$videoIdx]);
        //    $st->execute();

        $query3 = "select videoUrl,watchTime
from video left join watchingVideo on watchingVideo.videoIdx = video.idx
where video.idx = ?;";

        $st = $pdo->prepare($query3);
        $st->execute([$videoIdx]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

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

function playDramaAlreadyWatched($profileIdxInToken,$videoIdx,$episodeIdx) {
    try {
        $pdo = pdoSqlConnect();

        $pdo->beginTransaction();

        $query1 = "UPDATE history SET isDeleted = 'Y' where profileIdx = ? and videoIdx = ?;";

        $st = $pdo->prepare($query1);
        $st->execute([$profileIdxInToken,$videoIdx]);

        $query2 = "INSERT INTO watchingVideo (profileIdx, videoIdx, episodeIdx)
VALUES (?, ?, ?);";

        $st = $pdo->prepare($query2);
        $st->execute([$profileIdxInToken,$videoIdx,$episodeIdx]);
        //    $st->execute();

        $query3 = "select episodeUrl, watchTime
from episode
         left join watchingVideo on watchingVideo.episodeIdx = episode.idx
where episode.idx = ?;";

        $st = $pdo->prepare($query3);
        $st->execute([$episodeIdx]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

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

function isValidEpisodeIdx($episodeIdx)
{
    $pdo = pdoSqlConnect();
    $query = "select EXISTS(select * from episode where idx = ?) exist;";

    $st = $pdo->prepare($query);
    $st->execute([$episodeIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function checkVideoEpisodeCorrect($videoIdx,$episodeIdx)
{
    $pdo = pdoSqlConnect();
    $query = "select EXISTS(select * from episode where videoIdx = ? and idx = ?) exist;";

    $st = $pdo->prepare($query);
    $st->execute([$videoIdx,$episodeIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function checkProfileEpisodeWatch($profileIdxInToken,$episodeIdx)
{
    $pdo = pdoSqlConnect();
    $query = "select EXISTS(select * from watchingVideo where profileIdx = ? and episodeIdx = ? and isDeleted = 'N') exist;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdxInToken,$episodeIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function playDramaWithoutInsert($episodeIdx)
{
    $pdo = pdoSqlConnect();
    $query = "select episodeUrl, watchTime
from episode
         left join watchingVideo on watchingVideo.episodeIdx = episode.idx
where episode.idx = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$episodeIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function playDrama($profileIdxInToken,$videoIdx,$episodeIdx) {
    try {
        $pdo = pdoSqlConnect();

        $pdo->beginTransaction();

        $query1 = "INSERT INTO watchingVideo (profileIdx,videoIdx,episodeIdx)
VALUES (?,?,?);";

        $st = $pdo->prepare($query1);
        $st->execute([$profileIdxInToken,$videoIdx,$episodeIdx]);

        $query2 = "select episodeUrl, watchTime
from episode
         left join watchingVideo on watchingVideo.episodeIdx = episode.idx
where episode.idx = ?;";

        $st = $pdo->prepare($query2);
        $st->execute([$episodeIdx]);
        //    $st->execute();
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

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

function checkMovie($videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "SELECT `time` FROM video WHERE idx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$videoIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['time'];
}

function getMovieInfo($videoIdx) {
    try {
        $pdo = pdoSqlConnect();

        $pdo->beginTransaction();

        $query1 = "select videoUrl,posterImage,
       round((select avg(rating) from rating where rating.videoIdx = video.idx and rating.isDeleted = 'N'),
             1)                                                                                               as ratingAvg,
       videoName,
       case
           when ageGrade > 18 then concat('청불')
           else concat(ageGrade, '세') end                                                                     as ageGrade,
       case
           when video.time is null then concat('에피소드 ', (select count(*) from episode where episode.videoIdx = ?), '개')
           when video.time > 60 then concat(video.time div 60, '시간', video.time % 60, '분')
           when video.time < 60
               then concat(video.time, '분') end                                                               as timeOrEpisode,
       summary,
       director,
       actors,
       concat(group_concat(distinct genre.genreTitle), ' / ', group_concat(distinct country.country), ' / ', concat(year, '년')) as outline
from video
         left join episode on episode.videoIdx = video.idx
         left join genreVideo on genreVideo.videoIdx = video.idx
         left join genre on genreVideo.genreIdx = genre.idx
         left join countryVideo on countryVideo.videoIdx = video.idx
         left join country on countryVideo.countryIdx = country.idx
where video.idx = ?
group by videoName;";

        $st = $pdo->prepare($query1);
        $st->execute([$videoIdx,$videoIdx]);

        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res['info'] = $st->fetchAll();

        $query2 = "select count(*) as reviewNum from review where review.videoIdx = ?;";

        $st = $pdo->prepare($query2);
        $st->execute([$videoIdx]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res['reviewNum'] = $st->fetchAll();

        $query3 = "select review.userName as userName, review.comment as reviewComment from review where review.videoIdx = ?;";

        $st = $pdo->prepare($query3);
        $st->execute([$videoIdx]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res['review'] = $st->fetchAll();

        $query4 = "select video.idx as videoIdx,posterImage, videoName
from video
         left join genreVideo on genreVideo.videoIdx = video.idx
         left join genre on genreVideo.genreIdx = genre.idx
where genre.idx in (select GROUP_CONCAT(genre.idx SEPARATOR ',') as genreIdx
                    from video
                             left join genreVideo on genreVideo.videoIdx = video.idx
                             left join genre on genreVideo.genreIdx = genre.idx
                    where video.idx = ?) and video.idx not in (select idx from video where video.idx = ?);";

        $st = $pdo->prepare($query4);
        $st->execute([$videoIdx,$videoIdx]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res['similarContents'] = $st->fetchAll();

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

function getDramaInfo($videoIdx) {
    try {
        $pdo = pdoSqlConnect();

        $pdo->beginTransaction();

        $query1 = "select videoUrl,posterImage,
       round((select avg(rating) from rating where rating.videoIdx = video.idx and rating.isDeleted = 'N'),
             1)                                                                                               as ratingAvg,
       videoName,
       case
           when ageGrade > 18 then concat('청불')
           else concat(ageGrade, '세') end                                                                     as ageGrade,
       case
           when video.time is null then concat('에피소드 ', (select count(*) from episode where episode.videoIdx = ?), '개')
           when video.time > 60 then concat(video.time div 60, '시간', video.time % 60, '분')
           when video.time < 60
               then concat(video.time, '분') end                                                               as timeOrEpisode,
       summary,
       director,
       actors,
       concat(group_concat(distinct genre.genreTitle), ' / ', group_concat(distinct country.country), ' / ', concat(year, '년')) as outline
from video
         left join episode on episode.videoIdx = video.idx
         left join genreVideo on genreVideo.videoIdx = video.idx
         left join genre on genreVideo.genreIdx = genre.idx
         left join countryVideo on countryVideo.videoIdx = video.idx
         left join country on countryVideo.countryIdx = country.idx
where video.idx = ?
group by videoName;";

        $st = $pdo->prepare($query1);
        $st->execute([$videoIdx,$videoIdx]);

        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res['info'] = $st->fetchAll();

        $query2 = "select count(*) as reviewNum from review where review.videoIdx = ?;";

        $st = $pdo->prepare($query2);
        $st->execute([$videoIdx]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res['reviewNum'] = $st->fetchAll();

        $query3 = "select review.userName as userName, review.comment as reviewComment from review where review.videoIdx = ?;";

        $st = $pdo->prepare($query3);
        $st->execute([$videoIdx]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res['review'] = $st->fetchAll();

        $query4 = "select idx as episodeIdx,episodeUrl, concat('에피소드 ',episodeNum) as episodeNum, episodeTitle,
       case when episodeTime > 60 then concat(episodeTime div 60, '시간', episodeTime % 60, '분')
           when episodeTime < 60 then concat(episodeTime, '분') end                        as episodeTime
from episode
where episode.videoIdx = ?;";

        $st = $pdo->prepare($query4);
        $st->execute([$videoIdx]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res['episode'] = $st->fetchAll();

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

function banVideo($profileIdx,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO bannedVideo (profileIdx,videoIdx) VALUES (?,?);";

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

function checkUserAlreadyRate($profileIdx,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "select exists(select profileIdx,videoIdx from rating where profileIdx = ? and videoIdx = ?) as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx,$videoIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function checkRateDeleted($profileIdx,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "SELECT count(*) as rateNum FROM rating WHERE profileIdx = ? and videoIdx = ? and isDeleted = 'N';";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx,$videoIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['rateNum'];
}

function checkHistoryDeleted($profileIdx,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "SELECT isDeleted FROM history WHERE profileIdx = ? and videoIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx,$videoIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['isDeleted'];
}

function rateWithStar($profileIdx,$videoIdx,$ratingStar) {
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO rating (profileIdx,videoIdx,rating) VALUES (?,?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx,$videoIdx,$ratingStar]);

    $st = null;
    $pdo = null;

}

function deleteRate($profileIdx,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE rating SET isDeleted = 'Y' where profileIdx = ? and videoIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx,$videoIdx]);

    $st = null;
    $pdo = null;

}

function moveMovieToHistory($profileIdxInToken,$videoIdx) {
    try {
        $pdo = pdoSqlConnect();

        $pdo->beginTransaction();

        $query1 = "UPDATE watchingVideo SET isDeleted = 'Y' where profileIdx = ? and videoIdx = ?;";

        $st = $pdo->prepare($query1);
        $st->execute([$profileIdxInToken,$videoIdx]);

        $query2 = "INSERT INTO history (profileIdx,videoIdx) VALUES (?,?);";

        $st = $pdo->prepare($query2);
        $st->execute([$profileIdxInToken,$videoIdx]);

        $pdo->commit();

        $st = null;
        $pdo = null;
    }
    catch (Exception $e) {
        echo $e->getMessage();
        $pdo->rollback();
    }

}

function moveMovieToWatching($profileIdxInToken,$videoIdx) {
    try {
        $pdo = pdoSqlConnect();

        $pdo->beginTransaction();

        $query1 = "UPDATE history SET isDeleted = 'Y' where profileIdx = ? and videoIdx = ?;";

        $st = $pdo->prepare($query1);
        $st->execute([$profileIdxInToken,$videoIdx]);

        $query2 = "INSERT INTO watchingVideo (profileIdx,videoIdx) VALUES (?,?);";

        $st = $pdo->prepare($query2);
        $st->execute([$profileIdxInToken,$videoIdx]);

        $pdo->commit();

        $st = null;
        $pdo = null;
    }
    catch (Exception $e) {
        echo $e->getMessage();
        $pdo->rollback();
    }

}

function changeMovieWatchTime($watchTime,$profileIdxInToken,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE watchingVideo SET watchTime = ? where profileIdx = ? and videoIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$watchTime,$profileIdxInToken,$videoIdx]);

    $st = null;
    $pdo = null;

}

function getLastEpisodeIdx($videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "select max(idx) as idx from episode where videoIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$videoIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['idx'];
}

function getEpisodeTime($episodeIdx) {
    $pdo = pdoSqlConnect();
    $query = "select episodeTime from episode where episode.idx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$episodeIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['episodeTime'];
}

function moveDramaToHistory($profileIdxInToken,$videoIdx,$episodeIdx) {
    try {
        $pdo = pdoSqlConnect();

        $pdo->beginTransaction();

        $query1 = "UPDATE watchingVideo SET isDeleted = 'Y' where profileIdx = ? and videoIdx = ? and episodeIdx = ?;";

        $st = $pdo->prepare($query1);
        $st->execute([$profileIdxInToken,$videoIdx,$episodeIdx]);

        $query2 = "INSERT INTO history (profileIdx,videoIdx) VALUES (?,?);";

        $st = $pdo->prepare($query2);
        $st->execute([$profileIdxInToken,$videoIdx]);

        $pdo->commit();

        $st = null;
        $pdo = null;
    }
    catch (Exception $e) {
        echo $e->getMessage();
        $pdo->rollback();
    }

}

function changeDramaWatchTime($watchTime,$profileIdxInToken,$episodeIdx) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE watchingVideo SET watchTime = ? where profileIdx = ? and episodeIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$watchTime,$profileIdxInToken,$episodeIdx]);

    $st = null;
    $pdo = null;

}

function checkProfileHistory($profileIdxInToken,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "select exists(select profileIdx,videoIdx from history where profileIdx = ? and videoIdx = ?) as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdxInToken,$videoIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function deleteHistory($videoIdx,$profileIdxInToken) {
    $pdo = pdoSqlConnect();
    $query = "update history set isDeleted = 'Y' where history.videoIdx = ? and history.profileIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$videoIdx,$profileIdxInToken]);

    $st = null;
    $pdo = null;

}

function deleteWatchingVideo($videoIdx,$profileIdxInToken) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE watchingVideo SET isDeleted = 'Y' where profileIdx = ? and videoIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$videoIdx,$profileIdxInToken]);

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
