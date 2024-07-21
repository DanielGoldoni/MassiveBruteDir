<?php

/**
 * Pesquisa iterativa de diretórios ou arquivos comuns em um site alvo, com base na URL fornecida.
 * A lista de diretórios ou arquivos comuns é carregada a partir de um arquivo externo.
 * 
 * Este script verifica a presença de uma série de arquivos em um site e reporta quais arquivos são encontrados.
 * A lista de arquivos é lida a partir de um arquivo de texto, permitindo fácil atualização e manutenção.
 * 
 * @package     FileScanner
 * @subpackage  Scanner
 * @author      Daniel Goldoni Gomes <goldonigomesdaniel@gmail.com>
 */

// Define o site alvo para a pesquisa
$targetPath = 'targets.txt';

// Define o caminho para o arquivo de wordlist contendo a lista de diretórios e/ou arquivos
$wordlistPath = 'wordlist.txt';

/**
 * Lê a lista de alvos a partir de um arquivo de texto.
 *
 * @param string $targetPath Caminho para o arquivo de wordlist.
 * @return array Array contendo os nomes dos alvos listados na wordlist.
 * @throws Exception Se o arquivo de wordlist não for encontrado.
 */
function getTargets($targetPath) {
    $targets = [];

    if (!file_exists($targetPath)) {
        throw new Exception('O arquivo de alvos não foi encontrado.');
    }

    $file = fopen($targetPath, 'r');
    while (($line = fgets($file)) !== false) {
        $line = trim($line); // Remove espaços em branco e quebras de linha (para não haver erros de sintaxe ou conflitos durante a execução)
        if (!empty($line)) {
            $files[] = $line;
        }
    }
    fclose($file);

    return $files;
}
/**
 * Lê a lista de diretorios comuns a partir de um arquivo de texto.
 *
 * @param string $wordlistPath Caminho para o arquivo de wordlist.
 * @return array Array contendo os nomes dos itens listados na wordlist.
 * @throws Exception Se o arquivo de wordlist não for encontrado.
 */
function getCommonFiles($wordlistPath) {
    $files = [];

    if (!file_exists($wordlistPath)) {
        throw new Exception('O arquivo de wordlist não foi encontrado.');
    }

    $file = fopen($wordlistPath, 'r');
    while (($line = fgets($file)) !== false) {
        $line = trim($line); // Remove espaços em branco e quebras de linha (para não haver erros de sintaxe ou conflitos durante a execução)
        if (!empty($line)) {
            $files[] = $line;
        }
    }
    fclose($file);

    return $files;
}
/**
 * Verifica a existência de arquivos em paralelo utilizando cURL.
 *
 * @param string $targetPage URL do site alvo.
 * @param array $commonFiles Array de arquivos a serem verificados.
 * @return array Array contendo URLs de arquivos encontrados.
 */
function checkFilesInParallel($targets, $commonFiles) {
    $foundFiles = [];
    $multiCurl = curl_multi_init();
    $curlHandles = [];

    foreach ($commonFiles as $commonFile) {
        $url = $targetPage . '/' . $commonFile;
        $curlHandles[$url] = curl_init($url);

        curl_setopt($curlHandles[$url], CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandles[$url], CURLOPT_NOBODY, true); // Apenas cabeçalhos
        curl_setopt($curlHandles[$url], CURLOPT_HEADER, true);

        curl_multi_add_handle($multiCurl, $curlHandles[$url]);
    }

    $running = null;
    do {
        curl_multi_exec($multiCurl, $running);
        
    } while ($running > 0);

    foreach ($curlHandles as $url => $ch) {
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($responseCode == 200) {
            $foundFiles[] = $url;
        }
        curl_multi_remove_handle($multiCurl, $ch);
        curl_close($ch);
    }

    curl_multi_close($multiCurl);

    return $foundFiles;
}

try {
    // Obtém todos os itens da lista a partir de uma  wordlist externa (.txt)
    $commonFiles = getCommonFiles($wordlistPath);
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
    exit;
}
try {
    // Obtém todos os alvos da lista a partir de uma wordlist(.txt)
    $targets = getTargets($targetPath);
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
    exit;
}

// Define os cabeçalhos HTTP
http_response_code(200);
header('Content-Type: text/html');

// Cria o loop e verifica sua existência no site
foreach ($targets as $target){
    foreach ($commonFiles as $commonFile) {
    // Constrói a URL completa com cada item da wordlist
    $url = $target . '/' . $commonFile;}
    

    // Obtém os cabeçalhos da URL e verifica se o código de status é 200 (OK)
    $headers = @get_headers($url);
    if ($headers && strpos($headers[0], '200') !== false) {
        // Exibe a URL do diretório encontrado
        echo 'Encontrado: ' . $url . PHP_EOL;
    }
}
?>
