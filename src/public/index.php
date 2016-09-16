<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\PDO\Database as PDO;

require 'vendor/autoload.php';
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);
$corsOptions = array(
    "origin" => "*",
    "exposeHeaders" => array("Content-Type", "X-Requested-With", "X-authentication", "X-client"),
    "allowMethods" => array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS')
);
$cors = new \CorsSlim\CorsSlim($corsOptions);

$app->add($cors);
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
$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});
$app->any('/finance/client/{playgroundID}/{clientID}/calculate', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $clientID=$request->getAttribute('clientID');
    $playgroundID=$request->getAttribute('playgroundID');
    $sth = $pdo->prepare('update client set exitTime=CURRENT_TIMESTAMP,price= (TIMESTAMPDIFF (SECOND,time,CURRENT_TIMESTAMP)*10/3600+(SELECT sum(-qty*price) as price FROM `product_list`,products WHERE clientID='.$clientID.' and product_list.barcodeID=products.barcodeID and product_list.playgroundID='.$playgroundID.'))    WHERE client.id= '.$clientID);
    $sth->execute();

});
$app->any('/finance/monetar/data={data}/option={option}', function (Request $request, Response $response) {
    $data = $request->getAttribute('data');
    $data = json_decode($data);

$pdo=dbConnect();

   // $allPutVars = $request->getAttributes();
    //$dataSent=$allPutVars['routeInfo'][2];
    $sql = "INSERT INTO monetar(userID,
            data,
            total,
            operatiune,
            playgroundID) VALUES (
            :userID,
            :data,
            :total,
            :operatiune,
            :playgroundID)";

    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,TRUE);
    $stmt = $pdo->prepare($sql);
    $jsonMonetar=json_encode($data);
    $userID=1;
    $playgroundID=1;
    $timestamp=time();
     $operatiune=  $request->getAttribute('option');

switch ($operatiune){
    case    'factura':
    case    'zet':
    case    'retragere':
    case    'faraDocumente':  $total=$data->valoare;break;
    case    'seara':

    case    'dimineata':
                        foreach($data as $key=>$val)
                        {
                            switch($key){
                                case "cincisute" : $total+=$val*500;break;
                                case "douasute" : $total+=$val*200;break;
                                case "unasuta" : $total+=$val*100;break;
                                case "cincizeci" : $total+=$val*50;break;
                                case "zece" : $total+=$val*10;break;
                                case "cinci" : $total+=$val*5;break;
                                case "unleu" : $total+=$val;break;
                                case "bani50" : $total+=$val*0.5;break;
                                case "bani10" : $total+=$val*0.5;break;

                            }


                        }
                        break;

}

    $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
    $stmt->bindParam(':data', $jsonMonetar, PDO::PARAM_STR);
// use PARAM_STR although a number
    $stmt->bindParam(':total', $total, PDO::PARAM_STR);
    //$stmt->bindParam(':time', $timestamp, PDO::PARAM_STR);
    $stmt->bindParam(':playgroundID', $playgroundID, PDO::PARAM_STR);
    $stmt->bindParam(':operatiune', $operatiune, PDO::PARAM_STR);


    try {
        $pdo->beginTransaction();
        $stmt->execute();
        $pdo->commit();
         $response=array("OK"=>'Yes');
    } catch(PDOExecption $e) {
        $pdo->rollback();
        $response=array("OK"=>"No");
    }
    return json_encode($response);
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
    //(SELECT sum(-qty*price) as price FROM `product_list`,products WHERE clientID='.$clientID.' and product_list.barcodeID=products.barcodeID and product_list.playgroundID='.$playgroundID.')
    $sth = $pdo->prepare('SELECT   id,barcodeID,name,data,(SELECT GROUP_CONCAT( name SEPARATOR \', \')FROM `product_list`, products WHERE product_list.barcodeID=products.barcodeID and clientID=client.id) as consumed,DATE_FORMAT(time,\'%T\') AS time, DATE_FORMAT(exitTime,\'%T\') AS exitTime,floor(TIMESTAMPDIFF (minute,time,IF(exitTime=0,CURRENT_TIMESTAMP,exitTime))*10/60)  as price  ,IFNULL((SELECT sum(-qty*price) as price FROM `product_list`,products WHERE clientID=client.id and product_list.barcodeID=products.barcodeID and product_list.playgroundID=client.playgroundID),0) as cost  FROM client     WHERE playgroundID= :playground and client.time>CURDATE()');
    $sth->bindValue(':playground', $playgroundID, PDO::PARAM_INT);
    $sth->execute();
    $result = $sth->fetchAll();
    print json_encode($result);
  });


$app->any('/finance/inventory/getall/plagroundID={playgroundID}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $playgroundID=$request->getAttribute('playgroundID');
    $sth = $pdo->prepare('SELECT  id,name,barcodeID,owner,qty,um,price    FROM products     WHERE playgroundID= :playground');
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
$app->any('/finance/inventory/totalsForProducts/{playgroundID}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $sth = $pdo->prepare('SELECT name,sum(qty) as qty,price ,sum(price*qty) as totalPrice , products.barcodeID FROM product_list , products WHERE product_list.barcodeID=products.barcodeID AND product_list.addedDate>=CURRENT_DATE and product_list.addedDate<CURDATE() + INTERVAL 1 day AND  product_list.playgroundID= :playground GROUP BY products.barcodeID  ');
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
$app->any('/finance/inventory/addProduct/{playgroundID}/{barcode}/{addedBy}/{qty}', function (Request $request, Response $response) {
    $pdo=dbConnect();
     $sth = $pdo->prepare('insert into  product_list (barcodeID,playgroundID,addedby,qty) values (:barcode , :playground  , :addedBy , :qty ) ');
   if( $sth->execute(array(
        "barcode" => $request->getAttribute('barcode'),
        "playground" => $request->getAttribute('playgroundID'),
        "addedBy" => $request->getAttribute('addedBy'),
        "qty" => $request->getAttribute('qty'),
    )))
        $return=array("operation"=>"ok");
    else
        $return=array("operation"=>"failed");

    print json_encode($return);
});

$app->any('/finance/inventory/addProduct/{playgroundID}/{barcode}/{addedBy}/{name}/{price}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $playgroundID=$request->getAttribute('playgroundID');
    $barcode=$request->getAttribute('barcode');
    $addedBy=$request->getAttribute('addedBy');
    $name=$request->getAttribute('name');
    $price=$request->getAttribute('price');
    $sth = $pdo->prepare('insert into  products (barcodeID,playgroundID,addedby,name,price) values (:barcode , :playground  , :addedBy , :name , :price ) ');
    if( $sth->execute(array(
        "barcode" => $barcode,
        "playground" => $playgroundID,
        "addedBy" => $addedBy,
        "name" => $name,
        "price" => $price,
    ))) {
        $sth = $pdo->prepare('insert into  product_list (barcodeID,playgroundID,addedby) values (:barcode , :playground  , :addedBy ) ');



        if( $sth->execute(array(
            "barcode" => $barcode,
            "playground" => $playgroundID,
            "addedBy" => $addedBy,
        )))
            $return=array("operation"=>"ok");
        else
            $return=array("operation"=>"failed");

    }
    else
        $return=array("operation"=>"failed");

    print json_encode($return);
});

$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("Hello");

    return $response;
});

$app->get('/finance/params', function (Request $request, Response $response) {
    $response=array(
      "financeMonetarOptions"=>array(
          "cincisute"=>"500",
          "douasute"=>"200",
          "unasuta"=>"100",
          "cincizeci"=>"50",
          "zece"=>"10",
          "cinci"=>"5",
          "unleu"=>"1",
          "bani50"=>"0.5",
          "bani10"=>"0.10",
           ),
       "financeOptions"=>  array(
           "seara"=>"Numerar seara",
           "dimineata"=>"Numerar dimineata",
           "bon"=>"Plati cu bon",
           "factura"=>"Plati cu factura",
           "zet"=>"Z",
           "retragere"=>"Retragere Numerar",
           "faraDocumente"=>"Plati fara documente",
       ),
        'financeBonOptions'=> array(
            "firma"=>"Denumire Firma",
            "descriereServicii"=>"Descriere",
            "bon"=>"Numar bon",
            "valoare"=>"Suma",
        ),
        'financeFacturaOptions'=> array(
            "firma"=>"Denumire Firma",
            "descriereServicii"=>"Descriere",
            "bon"=>"Numar factura",
            "valoare"=>"Suma",
        ),
        'financeZetOptions'=> array(
            "valoare"=>"Suma",
        ),
        'financeClientiOptions'=>array(
            "id"=>"#",
            "nume"=>"Nume",
            "intrare"=>"Intrare",
            "detalii"=>"detalii",
            "consum"=>"consum",
            "pret"=>"pret",
            "iesire"=>"iesire",
        ),
        'financeProductsOptions'=>array(
            "id"=>"#",
            "name"=>"Nume",
            "barcodeID"=>"Barcode",
            "owner"=>array("name"=>"Categorie",field_type=>"dropdown",params=>array("1"=>"Coca Cola",'2'=>"Tymbarc")),
            "qty"=>"Cantitate",
            "um"=>"Unitatea de Masura",
            "price"=>"Pret",
        ),
        'financeProductAddOptions'=>array(
            "barcodeID"=>"Barcode",
            "qty"=>"Cantitatea",
        ),

);
    return json_encode($response);
});

$app->run();