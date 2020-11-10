<?php

function isValidUser($ID, $pwd){
    $pdo = pdoSqlConnect();
    $query = "SELECT ID, pwd as hash FROM Users WHERE ID= ?;";


    $st = $pdo->prepare($query);
    $st->execute([$ID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return password_verify($pwd, $res[0]['hash']);

}
function getUserIdxByNaverId($naverId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT idx FROM user WHERE naverId = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$naverId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['idx'];
}

function getProfileIdxByUserIdx($userIdx)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT idx FROM profile WHERE userIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['idx'];
}

function isValidNaverUser($naverId, $email){
    $pdo = pdoSqlConnect();
    $query = "select exists(select naverId,email from user where naverId = ? and email = ?) as exist;";


    $st = $pdo->prepare($query);
    $st->execute([$naverId,$email]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null; $pdo = null;

    return $res[0]['exist'];
}

function addNaverUser($naverId,$email,$name,$profileImg) {
    try {
        $pdo = pdoSqlConnect();

        $pdo->beginTransaction();

        $query1 = "INSERT INTO user (naverId,email,naverName) VALUES (?,?,?);";

        $st = $pdo->prepare($query1);
        $st->execute([$naverId,$email,$name]);

        $query2 = "INSERT INTO profile (userIdx,profileImage,`name`) VALUES (LAST_INSERT_ID(),?,?);";

        $st = $pdo->prepare($query2);
        $st->execute([$profileImg,$name]);

        $pdo->commit();

        $st = null;
        $pdo = null;
    }
    catch (Exception $e) {
        echo $e->getMessage();
        $pdo->rollback();
    }

}

function getLoginDeviceNum($userIdx){
    $pdo = pdoSqlConnect();
    $query = "select count(userIdx) as count from maxLogin where userIdx = ? and loginFlag = 'ON';";


    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null; $pdo = null;

    return $res[0]['count'];
}

function loginCutIn($userIdx,$deviceId,$jwt) {
    try {
        $pdo = pdoSqlConnect();

        $pdo->beginTransaction();

        $query1 = "update maxLogin set loginFlag = 'OFF' where createdAt = (select * from (select min(createdAt) from maxLogin where userIdx = ? and loginFlag = 'ON') as temp);";

        $st = $pdo->prepare($query1);
        $st->execute([$userIdx]);

        $query2 = "insert into maxLogin (userIdx,deviceId,token) values (?,?,?);";

        $st = $pdo->prepare($query2);
        $st->execute([$userIdx,$deviceId,$jwt]);

        $pdo->commit();

        $st = null;
        $pdo = null;
    }
    catch (Exception $e) {
        echo $e->getMessage();
        $pdo->rollback();
    }

}

function addLoginLog($userIdx,$deviceId,$jwt) {
    $pdo = pdoSqlConnect();
    $query = "insert into maxLogin (userIdx,deviceId,token) values (?,?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx,$deviceId,$jwt]);

    $st = null;
    $pdo = null;

}