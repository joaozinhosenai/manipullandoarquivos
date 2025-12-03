<?php
// Configuração e Definição de Variáveis
date_default_timezone_set('America/Sao_Paulo');
$arquivo_dados = 'registros.txt';
$mensagem_status = '';

// Função para exibir dados salvos
function exibirDados($arquivo) {
    if (!file_exists($arquivo)) {
        echo '<p style="color: orange;">Nenhum registro encontrado ainda.</p>';
        return;
    }
    
    $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if (empty($linhas)) {
        echo '<p style="color: orange;">Nenhum registro encontrado ainda.</p>';
        return;
    }
    
    echo '<h2>Registros Salvos</h2>';
    echo '<table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
    echo '<thead style="background-color: #4CAF50; color: white;">';
    echo '<tr>';
    echo '<th>Data da Coleta</th>';
    echo '<th>Nota</th>';
    echo '<th>Nome do Usuário</th>';
    echo '<th>Data/Hora do Envio</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($linhas as $linha) {
        $dados = explode(' | ', $linha);
        
        if (count($dados) === 4) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($dados[0]) . '</td>';
            echo '<td>' . htmlspecialchars($dados[1]) . '</td>';
            echo '<td>' . htmlspecialchars($dados[2]) . '</td>';
            echo '<td>' . htmlspecialchars($dados[3]) . '</td>';
            echo '</tr>';
        }
    }
    
    echo '</tbody>';
    echo '</table>';
}

// Coleta e Validação dos Dados do Formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['salvar_registro'])) {
        // Coleta e filtra os dados
        $nome_usuario = filter_input(INPUT_POST, 'nome_usuario', FILTER_SANITIZE_SPECIAL_CHARS);
        $nota = filter_input(INPUT_POST, 'nota', FILTER_SANITIZE_SPECIAL_CHARS);
        $data_coletada = filter_input(INPUT_POST, 'data_coletada', FILTER_SANITIZE_SPECIAL_CHARS);
        
        // Cria marca de tempo
        $data_envio = date('d/m/Y H:i:s');
        
        // Valida se todos os campos foram preenchidos
        if (!empty($nome_usuario) && !empty($nota) && !empty($data_coletada)) {
            // Formata os dados com delimitador
            $registro = $data_coletada . ' | ' . $nota . ' | ' . $nome_usuario . ' | ' . $data_envio . PHP_EOL;
            
            // Armazena no arquivo
            if (file_put_contents($arquivo_dados, $registro, FILE_APPEND | LOCK_EX)) {
                $mensagem_status = '<p style="color: green; font-weight: bold;">✓ Registro salvo com sucesso!</p>';
            } else {
                $mensagem_status = '<p style="color: red; font-weight: bold;">✗ Erro ao salvar o registro.</p>';
            }
        } else {
            $mensagem_status = '<p style="color: red; font-weight: bold;">✗ Por favor, preencha todos os campos!</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Registro de Dados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        form {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .botoes {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        button[name="salvar_registro"] {
            background-color: #4CAF50;
            color: white;
        }
        button[name="salvar_registro"]:hover {
            background-color: #45a049;
        }
        button[name="exibir_dados"] {
            background-color: #2196F3;
            color: white;
        }
        button[name="exibir_dados"]:hover {
            background-color: #0b7dda;
        }
        table {
            margin-top: 20px;
        }
        table th {
            text-align: left;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Sistema de Registro de Dados</h1>
        
        <?php echo $mensagem_status; ?>
        
        <form method="POST" action="">
            <label for="data_coletada">Data da Coleta:</label>
            <input type="date" id="data_coletada" name="data_coletada" required>
            
            <label for="nota">Nota:</label>
            <input type="text" id="nota" name="nota" placeholder="Digite a nota" required>
            
            <label for="nome_usuario">Nome do Usuário:</label>
            <input type="text" id="nome_usuario" name="nome_usuario" placeholder="Digite seu nome" required>
            
            <div class="botoes">
                <button type="submit" name="salvar_registro">Salvar Registro</button>
                <button type="submit" name="exibir_dados">Mostrar Todos os Dados Salvos</button>
            </div>
        </form>
        
        <?php
        // Exibição dos Registros
        if (isset($_POST['exibir_dados'])) {
            exibirDados($arquivo_dados);
        }
        ?>
    </div>
</body>
</html>