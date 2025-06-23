<?php
// filepath: app/api/vendas-cidades.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../classes/VendasCidades.class.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$action = $_GET['action'] ?? 'cidade';
$data_inicial = $_GET['data_inicial'] ?? "";
$data_final = $_GET['data_final'] ?? "";
$ano = $_GET['ano'] ?? date('Y');

try {
    $vendas = new VendasCidades();
    
    if ($action === 'viagens-mensais') {
        // Nova funcionalidade para viagens mensais
        $dados = $vendas->getViagensPassageirosPorMesSimplificado($ano);
        
        // Se não encontrar dados com a primeira função, tenta a simplificada
        if (empty($dados)) {
            $dados = $vendas->getViagensPassageirosPorMesSimplificado($ano);
        }
        
    } else {
        // Funcionalidade original para cidades
        $dados = $vendas->getTotalPorCidadeOrigem($data_inicial, $data_final);
    }

    echo json_encode([
        'success' => true,
        'data' => $dados,
        'action' => $action
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'action' => $action ?? 'unknown'
    ]);
}
?>