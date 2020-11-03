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