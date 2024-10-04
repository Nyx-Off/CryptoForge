<?php
// Fonction pour chiffrer le contenu
function customEncrypt($content, $passphrase) {
    // Création de la clé avec une méthode maison plus robuste
    $key = [];
    $salt = random_int(100, 999); // Génération d'un sel dynamique pour chaque chiffrement
    for ($i = 0; $i < strlen($passphrase); $i++) {
        $key[] = (ord($passphrase[$i]) * ($i + 1) + $salt) % 256; // Transformation plus complexe
    }
    
    $contentArray = str_split($content);
    $encryptedArray = [];

    foreach ($contentArray as $index => $char) {
        $keyChar = $key[$index % count($key)];
        $charCode = ord($char);
        $transformedChar = ($charCode + $keyChar + ($index % 17) + ($index * $salt) % 23) % 256; // Transformation plus robuste
        $encryptedChar = chr($transformedChar);
        $encryptedArray[] = $encryptedChar;
    }

    // Ajouter le sel à la sortie pour le déchiffrement
    $encryptedString = implode('', $encryptedArray);
    return base64_encode($salt . '::' . $encryptedString);
}

// Fonction pour déchiffrer le contenu
function customDecrypt($content, $passphrase) {
    // Décoder le contenu et séparer le sel du message chiffré
    $content = base64_decode($content);
    list($salt, $encryptedString) = explode('::', $content, 2);
    
    // Création de la clé avec le sel récupéré
    $key = [];
    for ($i = 0; $i < strlen($passphrase); $i++) {
        $key[] = (ord($passphrase[$i]) * ($i + 1) + $salt) % 256; // Transformation plus complexe
    }
    
    $contentArray = str_split($encryptedString);
    $decryptedArray = [];

    foreach ($contentArray as $index => $char) {
        $keyChar = $key[$index % count($key)];
        $charCode = ord($char);
        $originalChar = ($charCode - $keyChar - ($index % 17) - ($index * $salt) % 23 + 256) % 256; // Inverse de la transformation plus robuste
        $decryptedChar = chr($originalChar);
        $decryptedArray[] = $decryptedChar;
    }

    return implode('', $decryptedArray);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $passphrase = $_POST['passphrase'];

    if (empty($_FILES['fileInput']['tmp_name']) || empty($passphrase)) {
        http_response_code(400);
        echo json_encode(['error' => 'Veuillez sélectionner un fichier et entrer une passphrase.']);
        exit;
    }

    $content = file_get_contents($_FILES['fileInput']['tmp_name']);
    $fileName = $_FILES['fileInput']['name'];

    if ($action === 'encrypt') {
        $result = customEncrypt($content, $passphrase);
        $outputFileName = $fileName . '.custom_encrypted';
    } elseif ($action === 'decrypt') {
        $result = customDecrypt($content, $passphrase);
        $outputFileName = preg_replace('/\.custom_encrypted$/', '', $fileName);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Action non valide.']);
        exit;
    }

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $outputFileName . '"');
    header('Content-Length: ' . strlen($result));
    echo $result;
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Méthode non autorisée.']);
?>