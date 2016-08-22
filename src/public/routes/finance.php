<?php
/**
 * Created by PhpStorm.
 * User: dprundus
 * Date: 19/08/16
 * Time: 15:53
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\PDO\Database as PDO;

$app->any('/finance/monetar/data={data}/option={option}', function (Request $request, Response $response) {
    $data = $request->getAttribute('data');
    $data = json_decode($data);

    $pdo = dbConnect();

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

    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);
    $stmt = $pdo->prepare($sql);
    $jsonMonetar = json_encode($data);
    $userID = 1;
    $playgroundID = 1;
    $timestamp = time();
    $operatiune = $request->getAttribute('option');

    switch ($operatiune) {
        case    'factura':
        case    'zet':
        case    'retragere':
        case    'faraDocumente':
            $total = $data->valoare;
            break;
        case    'seara':

        case    'dimineata':
            foreach ($data as $key => $val) {
                switch ($key) {
                    case "cincisute" :
                        $total += $val * 500;
                        break;
                    case "douasute" :
                        $total += $val * 200;
                        break;
                    case "unasuta" :
                        $total += $val * 100;
                        break;
                    case "cincizeci" :
                        $total += $val * 50;
                        break;
                    case "zece" :
                        $total += $val * 10;
                        break;
                    case "cinci" :
                        $total += $val * 5;
                        break;
                    case "unleu" :
                        $total += $val;
                        break;
                    case "bani50" :
                        $total += $val * 0.5;
                        break;
                    case "bani10" :
                        $total += $val * 0.5;
                        break;

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
        $response = array("OK" => 'Yes');
    } catch (PDOExecption $e) {
        $pdo->rollback();
        $response = array("OK" => "No");
    }
    return json_encode($response);
});
$app->get('/finance/params', function (Request $request, Response $response) {
    $response = array(
        "financeMonetarOptions" => array(
            "cincisute" => "500",
            "douasute" => "200",
            "unasuta" => "100",
            "cincizeci" => "50",
            "zece" => "10",
            "cinci" => "5",
            "unleu" => "1",
            "bani50" => "0.5",
            "bani10" => "0.10",
        ),
        "financeOptions" => array(
            "seara" => "Numerar seara",
            "dimineata" => "Numerar dimineata",
            "bon" => "Plati cu bon",
            "factura" => "Plati cu factura",
            "zet" => "Z",
            "retragere" => "Retragere Numerar",
            "faraDocumente" => "Plati fara documente",
        ),
        'financeBonOptions' => array(
            "firma" => "Denumire Firma",
            "descriereServicii" => "Descriere",
            "bon" => "Numar bon",
            "valoare" => "Suma",
        ),
        'financeFacturaOptions' => array(
            "firma" => "Denumire Firma",
            "descriereServicii" => "Descriere",
            "bon" => "Numar factura",
            "valoare" => "Suma",
        ),
        'financeZetOptions' => array(
            "valoare" => "Suma",
        ),
        'financeClientiOptions' => array(
            "id" => "#",
            "nume" => "Nume",
            "intrare" => "Intrare",
            "detalii" => "detalii",
            "consum" => "consum",
            "pret" => "pret",
            "iesire" => "iesire",
        ),
        'financeProductsOptions' => array(
            "id" => "#",
            "name" => "Nume",
            "barcodeID" => "Barcode",
            "owner" => "Categorie",
            "qty" => "Cantitate",
            "um" => "Unitatea de Masura",
            "price" => "Pret",
        ),

    );
    return json_encode($response);
});