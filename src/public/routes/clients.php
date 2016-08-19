<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\PDO\Database as PDO;
$app->get('/finance/client/{clientID}/calculate', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $clientID = $request->getAttribute('clientID');
    $sth = $pdo->prepare('SELECT *     FROM client     WHERE id= :clientID');
    $sth->bindValue(':clientID', $clientID, PDO::PARAM_INT);
    $sth->execute();
    $result = $sth->fetchAll();
    print_r($result);
    echo  $start=new DateTime($result[0]['time']);
    //  echo date_diff($start,new dateTime);
    $ymd = DateTime::createFromFormat('Y-m-d', '10-16-2003')->format('Y-m-d');



});

$app->any('/finance/client/data={data}/option={option}', function (Request $request, Response $response) {
    $data = json_decode( $request->getAttribute('data'));
    $pdo=dbConnect();

    // $allPutVars = $request->getAttributes();
    //$dataSent=$allPutVars['routeInfo'][2];
    $sql = "INSERT INTO client(
            barcodeID,data,name, userID, playgroundID) 
            VALUES ( :barcodeID,:data,:name,:userID,:playgroundID)";

    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,TRUE);
    $stmt = $pdo->prepare($sql);
    $jsonMonetar=json_encode($data);
    $userID=1;
    $playgroundID=1;
    $barcodeID=2;
    $name=(string)$data->nume;
    $stmt->bindParam(':barcodeID', $barcodeID, PDO::PARAM_STR);
    $stmt->bindParam(':data', $jsonMonetar, PDO::PARAM_STR);
    $stmt->bindParam(':name',$name, PDO::PARAM_STR);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
    $stmt->bindParam(':playgroundID', $playgroundID, PDO::PARAM_STR);

    return json_encode(  sqlExecute($pdo,$stmt));
});
$app->any('/finance/client/getall/plagroundID={playgroundID}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $playgroundID=$request->getAttribute('playgroundID');
    $sth = $pdo->prepare('SELECT *     FROM client     WHERE playgroundID= :playground');
    $sth->bindValue(':playground', $playgroundID, PDO::PARAM_INT);
    $sth->execute();
    $result = $sth->fetchAll();
    print json_encode($result);
});
