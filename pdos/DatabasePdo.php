<?php

//DB 정보
function pdoSqlConnect()
{
    try {
        $DB_HOST = "ericserver-1.cipxoaemvcst.ap-northeast-2.rds.amazonaws.com";
        $DB_NAME = "watcha";
        $DB_USER = "ffalswo2";
        $DB_PW = "stephan98";
        $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PW);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}