<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
/**
 * Created by PhpStorm.
 * User: dprundus
 * Date: 20/09/16
 * Time: 11:10
 */



$app->any('/finance/inventory/getall/plagroundID={playgroundID}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $playgroundID=$request->getAttribute('playgroundID');
    $sth = $pdo->prepare('SELECT  barcodeID,name,barcodeID,owner,qty,um,price    FROM products     WHERE playgroundID= :playground  order by name');
    $sth->bindValue(':playground', $playgroundID, PDO::PARAM_INT);
    $sth->execute();
    $result = $sth->fetchAll();
    print json_encode($result);
});

$app->any('/finance/inventory/checkProduct/{playgroundID}/{barcode}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $playgroundID=$request->getAttribute('playgroundID');
    $barcode=$request->getAttribute('barcode');
    $sth = $pdo->prepare('SELECT  barcodeID,name    FROM products  WHERE playgroundID= :playground AND barcodeID= :barcode');
    $sth->bindValue(':playground', $playgroundID, PDO::PARAM_INT);
    $sth->bindValue(':barcode', $barcode, PDO::PARAM_INT);
    $sth->execute();
    //$sth->debugDumpParams();
    $result = $sth->fetch();

    if($sth->rowCount()>0){
        $return=array(
            "operation"=>"ok",
            "data"=> json_encode(array("name"=>$result['name'])),
        );

    }
    else{
        $return=array(
            "operation"=>"failed",
        );


    }
    print json_encode($return);
});

$app->any('/finance/inventory/totalProduct/{playgroundID}/{barcode}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $sth = $pdo->prepare('SELECT  sum(qty)  as qty   FROM product_list  WHERE playgroundID= :playground AND barcodeID= :barcode');
    if( $sth->execute(array(
        "barcode" => $request->getAttribute('barcode'),
        "playground" => $request->getAttribute('playgroundID'),
    ))){
        $result = $sth->fetch();
        $return=array(
            "operation"=>"ok",
            "data"=> json_encode(array("qty"=>(int)$result['qty'])),
        );

    }
    else
        $return=array("operation"=>"failed");

    print json_encode($return);
});
$app->any('/finance/inventory/totalsForProducts/{playgroundID}[/{tip}]', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $where="";
    if($request->getAttribute('tip')=='vanzari'){
        $where="AND product_list.addedDate>=CURRENT_DATE and product_list.addedDate<CURDATE() + INTERVAL 1 day  AND qty<0";
    }
    $sth = $pdo->prepare('SELECT name,sum(qty) as qty,price ,sum(price*qty) as totalPrice , products.barcodeID FROM product_list , products WHERE product_list.barcodeID=products.barcodeID AND  product_list.playgroundID= :playground '.$where.' GROUP BY products.barcodeID  order by provider,name,qty');
    if( $sth->execute(array(
        "playground" => $request->getAttribute('playgroundID'),
    ))){
        $result = $sth->fetchAll();
        $return=array(
            "operation"=>"ok",
            "data"=> json_encode($result),
        );

    }
    else
        $return=array("operation"=>"failed");

    print json_encode($return);
});

$app->any('/finance/inventory/addProduct/{playgroundID}/{barcode}/{addedBy}/{qty}[/{clientID}]', function (Request $request, Response $response) {
    $pdo=dbConnect();
    if($request->getAttribute('clientID')>0){
        $clientID=$request->getAttribute('clientID');
    }
    else{
        $clientID=0;
    }
    $sth = $pdo->prepare('insert into  product_list (barcodeID,playgroundID,addedby,qty,clientID) values (:barcode , :playground  , :addedBy , :qty  , :clientID ) ');
    if( $sth->execute(array(
        "barcode" => $request->getAttribute('barcode'),
        "playground" => $request->getAttribute('playgroundID'),
        "addedBy" => $request->getAttribute('addedBy'),
        "qty" => $request->getAttribute('qty'),
        "clientID" => $clientID,
    ))){

        $return=array("operation"=>"ok");
        $sth->debugDumpParams();
    }

    else
        $return=array("operation"=>"failed");

    print json_encode($return);
});

$app->any('/finance/inventory/createProduct/{playgroundID}/{barcode}/{addedBy}/{name}/{price}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $playgroundID=$request->getAttribute('playgroundID');
    $barcode=$request->getAttribute('barcode');
    $addedBy=$request->getAttribute('addedBy');
    $name=$request->getAttribute('name');
     $price=$request->getAttribute('price');
    $sth = $pdo->prepare('insert into  products (barcodeID,playgroundID,addedby,name,price) 
    values ("'.$barcode.'" , "'.$playgroundID.'" , "'.$addedBy.'" , "'.$name.'" , \''.$price.'\' ) ');
    if( $sth->execute() ){
        $sth = $pdo->prepare('insert into  product_list (barcodeID,playgroundID,addedby) values (:barcode , :playground  , :addedBy ) ');



        if( $sth->execute(array(
            "barcode" => $barcode,
            "playground" => $playgroundID,
            "addedBy" => $addedBy,
        )))
            $return=array("operation"=>"ok");
        else
            $return=array("operation"=>"failed 1");

    }
    else
        $return=array("operation"=>"failed 2");

    print json_encode($return);
});

$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("Hello");

    return $response;
});
