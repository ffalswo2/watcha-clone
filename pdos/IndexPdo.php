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


//function addNaverUser($naverId,$email,$name,$profileImg) {
//    try {
//        $pdo = pdoSqlConnect();
//
//        $pdo->beginTransaction();
//
//        $query1 = "INSERT INTO user (naverId,email,naverName) VALUES (?,?,?);";
//
//        $st = $pdo->prepare($query1);
//        $st->execute([$naverId,$email,$name]);
//
//        $query2 = "INSERT INTO profile (userIdx,profileImage,`name`) VALUES (LAST_INSERT_ID(),?,?);";
//
//        $st = $pdo->prepare($query2);
//        $st->execute([$profileImg,$name]);
//
//        $pdo->commit();
//
//        $st = null;
//        $pdo = null;
//    }
//    catch (Exception $e) {
//        echo $e->getMessage();
//        $pdo->rollback();
//    }
//
//}

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

//function searchVidByCategory($keyword)
//{
//    $pdo = pdoSqlConnect();
//    $query = "select posterImage, videoName
//from video
//         left join genreVideo on genreVideo.videoIdx = video.idx
//         left join genre on genreVideo.genreIdx = genre.idx
//where genre.idx = ?;";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$keyword]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res;
//}

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

//function isValidGenreIdx($keyword) {
//    $pdo = pdoSqlConnect();
//    $query = "select exists(select idx from genre where idx = ?) as exist;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$keyword]);
//    //    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res[0]['exist'];
//}

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

function changeProfileInfo($profileImage,$profileName,$userIdxInToken) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE profile SET profileImage = ?,`name` = ? where userIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$profileImage,$profileName,$userIdxInToken]);

    $st = null;
    $pdo = null;

}

function changeProfileName($profileName,$userIdxInToken) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE profile SET `name` = ? where userIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$profileName,$userIdxInToken]);

    $st = null;
    $pdo = null;

}

function rateWithStar($profileIdx,$videoIdx,$ratingStar) {
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO rating (profileIdx,videoIdx,rating) VALUES (?,?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx,$videoIdx,$ratingStar]);

    $st = null;
    $pdo = null;

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

//function searchVidByName($keyword)
//{
//    try {
//        $pdo = pdoSqlConnect();
//
//        $pdo->beginTransaction();
//
//        $query1 = "select posterImage,videoName from video where replace(videoName, ' ', '') like concat('%',?,'%');";
//
//        $st = $pdo->prepare($query1);
//        $st->execute([$keyword]);
//
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res = $st->fetchAll();
//
//        $query2 = "INSERT INTO searchHistory (keyword) VALUES (?);";
//
//        $st = $pdo->prepare($query2);
//        $st->execute([$keyword]);
//
//        $pdo->commit();
//
//        $st = null;
//        $pdo = null;
//
//        return $res;
//    }
//    catch (Exception $e) {
//        echo $e->getMessage();
//        $pdo->rollback();
//    }
//}

function getPopularVideosByOrder() {
    $pdo = pdoSqlConnect();
    $query = "select posterImage, videoName
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

function checkRateDeleted($profileIdx,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "SELECT isDeleted FROM rating WHERE profileIdx = ? and videoIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx,$videoIdx]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['isDeleted'];
}

function deleteRate($profileIdx,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE rating SET isDeleted = 'Y' where profileIdx = ? and videoIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdx,$videoIdx]);

    $st = null;
    $pdo = null;

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

        $query1 = "select videoUrl,
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

        $query4 = "select posterImage, videoName
from video
         left join genreVideo on genreVideo.videoIdx = video.idx
         left join genre on genreVideo.genreIdx = genre.idx
where genre.idx in (select GROUP_CONCAT(genre.idx SEPARATOR ',') as genreIdx
                    from video
                             left join genreVideo on genreVideo.videoIdx = video.idx
                             left join genre on genreVideo.genreIdx = genre.idx
                    where video.idx = ?);";

        $st = $pdo->prepare($query4);
        $st->execute([$videoIdx]);
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

        $query1 = "select videoUrl,
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

function getCountryIdx()
{
    $pdo = pdoSqlConnect();
    $query = "select * from country;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getFavVideos($profileIdxInToken) {
    $pdo = pdoSqlConnect();
    $query = "select posterImage, videoName
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

function getVideoIdx()
{
    $pdo = pdoSqlConnect();
    $query = "select idx,videoName from video;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function changeMovieWatchTime($watchTime,$profileIdxInToken,$videoIdx) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE watchingVideo SET watchTime = ? where profileIdx = ? and videoIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$watchTime,$profileIdxInToken,$videoIdx]);

    $st = null;
    $pdo = null;

}

function changeDramaWatchTime($watchTime,$profileIdxInToken,$episodeIdx) {
    $pdo = pdoSqlConnect();
    $query = "UPDATE watchingVideo SET watchTime = ? where profileIdx = ? and episodeIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$watchTime,$profileIdxInToken,$episodeIdx]);

    $st = null;
    $pdo = null;

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

function getWatchingVideo($profileIdxInToken)
{
    $pdo = pdoSqlConnect();
    $query = "select posterImage,case when episodeNum is null then videoName else concat(videoName,':에피소드 ',episodeNum) end as watchingTitle,watchTime
from watchingVideo
         left join video on watchingVideo.videoIdx = video.idx
         left join profile on watchingVideo.profileIdx = profile.idx
         left join episode on watchingVideo.episodeIdx = episode.idx
where profile.idx = ? and watchingVideo.isDeleted = 'N';";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$profileIdxInToken]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
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

function getHistory($profileIdxInToken)
{
    $pdo = pdoSqlConnect();
    $query = "select posterImage,videoName from video left join history on history.videoIdx = video.idx where history.profileIdx = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$profileIdxInToken]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
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

function checkProfileHistory($profileIdxInToken,$value) {
    $pdo = pdoSqlConnect();
    $query = "select exists(select profileIdx,videoIdx from history where profileIdx = ? and videoIdx = ?) as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$profileIdxInToken,$value]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
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
