<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
/**
 * Created by PhpStorm.
 * User: dprundus
 * Date: 20/09/16
 * Time: 11:10
 */
$app->any('/finance/client/{playgroundID}/{clientID}/calculate', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $clientID=$request->getAttribute('clientID');
    $playgroundID=$request->getAttribute('playgroundID');
    $sth = $pdo->prepare('update client set exitTime=CURRENT_TIMESTAMP,price= (if(TIMESTAMPDIFF (minute,time,CURRENT_TIMESTAMP)*(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek)/60<(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek),(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek),TIMESTAMPDIFF (minute,time,CURRENT_TIMESTAMP)*(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek)/60)) WHERE client.id='.$clientID);
    if( $sth->execute()){
        $return=array(
            "operation"=>"ok",
        );

    }
    else
        $return=array("operation"=>"failed");

    print json_encode($return);
});
$app->any('/finance/client/{clientID}/comment/{comment}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $clientID=$request->getAttribute('clientID');
    $comment=$request->getAttribute('comment');
    $sth = $pdo->prepare('update client set data=Concat(\'{"nume":"\',name,\'","detalii":"'.$comment.'"}\')     WHERE client.id='.$clientID);
    $sth->execute();

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
//inance/client/updateDetails/'+playgroundID+'/'+addedBy+'/'+clientID+'/'+details
$app->any('/finance/client/updateDetails/{clientID}/{details}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $clientID=$request->getAttribute('clientID');
    $details=json_encode($request->getAttribute('details'));
    $sth = $pdo->prepare('update client set data='.$details.' where id=:clientID');
    $sth->bindValue(':clientID', $clientID, PDO::PARAM_INT);
    $sth->execute();
});

$app->any('/finance/client/getall/plagroundID={playgroundID}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $playgroundID=$request->getAttribute('playgroundID');
    //(SELECT sum(-qty*price) as price FROM `product_list`,products WHERE clientID='.$clientID.' and product_list.barcodeID=products.barcodeID and product_list.playgroundID='.$playgroundID.')
    //echo 'SELECT   id,barcodeID,name,data,(SELECT GROUP_CONCAT( name SEPARATOR \'<br> \')FROM `product_list`, products WHERE product_list.barcodeID=products.barcodeID and clientID=client.id) as consumed,DATE_FORMAT(CONVERT_TZ(time+INTERVAL 1 hour,\'US/Pacific\',\'GMT\'),\'%T\') AS time, IFNULL(DATE_FORMAT(CONVERT_TZ(exitTime+INTERVAL 1 hour,\'US/Pacific\',\'GMT\'),\'%T\'),\'00:00:00\') AS exitTime,@t1:=floor((if(TIMESTAMPDIFF (minute,time,CURRENT_TIMESTAMP)*(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek)/60<(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek),(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek),TIMESTAMPDIFF (minute,time,CURRENT_TIMESTAMP)*(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek)/60)+ifnull((SELECT sum(-qty*price) as price FROM `product_list`,products WHERE clientID=client.id and product_list.barcodeID=products.barcodeID and product_list.playgroundID='.$playgroundID.'),0)))  as price  ,@t2:=IFNULL((SELECT sum(-qty*price) as price FROM `product_list`,products WHERE clientID=client.id and product_list.barcodeID=products.barcodeID and product_list.playgroundID=client.playgroundID),0) as cost, @t1+@t2 as total_general FROM client     WHERE playgroundID= :playground and client.time>CURDATE()';
    //$sth = $pdo->prepare('SELECT   id,barcodeID,name,data,(SELECT GROUP_CONCAT( name SEPARATOR \'<br> \')FROM `product_list`, products WHERE product_list.barcodeID=products.barcodeID and clientID=client.id) as consumed,DATE_FORMAT(CONVERT_TZ(time+INTERVAL 1 hour,\'US/Pacific\',\'GMT\'),\'%T\') AS time, IFNULL(DATE_FORMAT(CONVERT_TZ(exitTime+INTERVAL 1 hour,\'US/Pacific\',\'GMT\'),\'%T\'),\'00:00:00\') AS exitTime,@t1:=floor((if(TIMESTAMPDIFF (minute,time,CURRENT_TIMESTAMP)*(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek)/60<(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek),(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek),TIMESTAMPDIFF (minute,time,CURRENT_TIMESTAMP)*(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek)/60)+ifnull((SELECT sum(-qty*price) as price FROM `product_list`,products WHERE clientID=client.id and product_list.barcodeID=products.barcodeID and product_list.playgroundID='.$playgroundID.'),0)))  as price  ,@t2:=IFNULL((SELECT sum(-qty*price) as price FROM `product_list`,products WHERE clientID=client.id and product_list.barcodeID=products.barcodeID and product_list.playgroundID=client.playgroundID),0) as cost, @t1+@t2 as total_general FROM client     WHERE playgroundID= :playground and client.time>CURDATE()');
    $sth = $pdo->prepare('SELECT id,barcodeID,name,data,(SELECT GROUP_CONCAT( name SEPARATOR \'
\')FROM `product_list`, products WHERE product_list.barcodeID=products.barcodeID and clientID=client.id) as consumed,DATE_FORMAT(CONVERT_TZ(time+INTERVAL 1 hour,\'US/Pacific\',\'GMT\'),\'%T\') AS time, IFNULL(DATE_FORMAT(CONVERT_TZ(exitTime+INTERVAL 1 hour,\'US/Pacific\',\'GMT\'),\'%T\'),\'00:00:00\') AS exitTime,@t1:=floor((if(TIMESTAMPDIFF (minute,time,CURRENT_TIMESTAMP)*(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek)/60<(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek),(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek),TIMESTAMPDIFF (minute,time,CURRENT_TIMESTAMP)*(SELECT price FROM `money_table` WHERE DAYOFWEEK(CURRENT_TIMESTAMP)=dayOfWeek)/60))) as price ,@t2:=IFNULL((SELECT sum(-qty*price) as price FROM `product_list`,products WHERE clientID=client.id and product_list.barcodeID=products.barcodeID and product_list.playgroundID=client.playgroundID),0) as cost, @t1+@t2 as total_general FROM client WHERE playgroundID= :playground and client.time>CURDATE()');

    $sth->bindValue(':playground', $playgroundID, PDO::PARAM_INT);
    $sth->execute();
    $result = $sth->fetchAll();
   // print_r( $result);
    print json_encode($result);
});

$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});