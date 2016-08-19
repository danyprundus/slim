<?php
/**
 * Created by PhpStorm.
 * User: dprundus
 * Date: 19/08/16
 * Time: 15:56
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\PDO\Database as PDO;
$app->any('/finance/inventory/getall/plagroundID={playgroundID}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $playgroundID=$request->getAttribute('playgroundID');
    $sth = $pdo->prepare('SELECT  id,name,barcodeID,owner,qty,um,price    FROM products     WHERE playgroundID= :playground');
    $sth->bindValue(':playground', $playgroundID, PDO::PARAM_INT);
    $sth->execute();
    $result = $sth->fetchAll();
    print json_encode($result);
});
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("Hello");

    return $response;
});
