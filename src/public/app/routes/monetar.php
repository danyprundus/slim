<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
/**
 * Created by PhpStorm.
 * User: dprundus
 * Date: 20/09/16
 * Time: 11:10
 */
$app->any('/finance/monetar/getall/{playgroundID}/{date}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $playgroundID=$request->getAttribute('playgroundID');
     $date=$request->getAttribute('date');
    $sth = $pdo->prepare("SELECT * FROM `monetar` where str_to_date('$date','%Y-%m-%d')=DATE_FORMAT(time, '%Y-%m-%d') AND playgroundID=$playgroundID");
    $sth->bindValue(':playground', $playgroundID, PDO::PARAM_INT);
    $sth->execute();
    $result = $sth->fetchAll();
    print json_encode($result);
});

$app->any('/finance/monetar/getZetForMonth/{playgroundID}/{yearmonth}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $return=array();
    $playgroundID=$request->getAttribute('playgroundID');
     $yearmonth=$request->getAttribute('yearmonth');
    $sth = $pdo->prepare("SELECT sum(total) as total,concat(EXTRACT( YEAR_MONTH FROM time ),@day:=EXTRACT(DAY FROM time )) as YearMonthDay FROM `monetar` where  EXTRACT( YEAR_MONTH FROM time )='$yearmonth' and playgroundID=$playgroundID and operatiune='zet' GROUP BY EXTRACT(DAY FROM time )");
    $sth->execute();
    $results = $sth->fetchAll(PDO::FETCH_NAMED);
    foreach ($results as $result)
    {
        $return[$result['YearMonthDay']]=$result['total'];
    }
    print json_encode($return);
});


$app->any('/finance/monetar/getCountForMonth/{playgroundID}/{yearmonth}', function (Request $request, Response $response) {
    $pdo=dbConnect();
    $return=array();
    $playgroundID=$request->getAttribute('playgroundID');
     $yearmonth=$request->getAttribute('yearmonth');
    $sth = $pdo->prepare("SELECT max(total) as max_total,min(total) as min_total,concat(EXTRACT( YEAR_MONTH FROM time ),@day:=EXTRACT(DAY FROM time )) as YearMonthDay FROM `monetar` where  EXTRACT( YEAR_MONTH FROM time )='$yearmonth' and playgroundID=$playgroundID and operatiune='seara' GROUP BY EXTRACT(DAY FROM time )");
    $sth->execute();
    $results = $sth->fetchAll(PDO::FETCH_NAMED);
    foreach ($results as $result)
    {
        $return[$result['YearMonthDay']]=array("max"=>$result['max_total'],'min'=>$result['min_total']);
    }
    print json_encode($return);
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

