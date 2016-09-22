<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
/**
 * Created by PhpStorm.
 * User: dprundus
 * Date: 20/09/16
 * Time: 11:12
 */
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