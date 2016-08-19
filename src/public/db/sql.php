<?php
/**
 * Created by PhpStorm.
 * User: dprundus
 * Date: 19/08/16
 * Time: 15:58
 */
function dbConnect()
{$dsn = 'mysql:host=localhost;dbname=playground_management';
    $username = 'root';
    $password = 'mysql';
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