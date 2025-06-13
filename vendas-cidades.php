<?php
// filepath: app/api/vendas-cidades.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../classes/VendasCidades.class.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $vendas = new VendasCidades();
    $dados = $vendas->getTotalPorCidadeOrigem();

    echo json_encode([
        'success' => true,
        'data' => $dados
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>