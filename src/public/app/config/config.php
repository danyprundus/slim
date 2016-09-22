<?php
/**
 * Created by PhpStorm.
 * User: dprundus
 * Date: 20/09/16
 * Time: 11:09
 */
function dbConnect()
{
    if($_SERVER['SERVER_NAME']=='daniel.dev'){
        $dsn = 'mysql:host=localhost;dbname=playground_management';
        $username = 'root';
        $password = 'mysql';
    }else{
        $dsn = 'mysql:host=localhost;dbname=azteck_slim';
        $username = 'azteck_slim';
        $password = 'nietzche10';

    }
    $pdo = new PDO($dsn, $username, $password);
    return $pdo;

}
function sqlExecute($pdo,$stmt)
{

    try {
        $pdo->beginTransaction();

        $stmt->execute();
        $pdo->commit();
        $response=array("OK"=>'Yes');
    } catch(PDOExecption $e) {
        $pdo->rollback();
        $response=array("OK"=>"No");
    }
    return $response;

}