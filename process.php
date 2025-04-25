<?php
/**
 * CryptoForge - Système de chiffrement/déchiffrement sécurisé
 * Utilisant les standards de l'industrie: AES-256-GCM et PBKDF2
 */

// Configuration
ini_set('memory_limit', '256M');  // Limite mémoire pour gérer les fichiers volumineux
set_time_limit(300);  // Augmente le temps d'exécution maximum à 5 minutes

// Tailles des blocs pour le traitement de fichiers volumineux (en octets)
define('BLOCK_SIZE', 1048576);  // 1 Mo par bloc

/**
 * Fonction pour chiffrer le contenu en utilisant AES-256-GCM
 * @param string $content Le contenu à chiffrer
 * @param string $passphrase La phrase secrète utilisée pour le chiffrement
 * @param int $iterations Nombre d'itérations pour PBKDF2 (par défaut: 100000)
 * @return string Le contenu chiffré avec les métadonnées
 */
function secureEncrypt($content, $passphrase, $iterations = 100000) {
    // Vérification des extensions requises
    if (!extension_loaded('openssl')) {
        throw new Exception("L'extension OpenSSL est requise mais n'est pas installée.");
    }
    
    // Création du matériel cryptographique
    $iv = openssl_random_pseudo_bytes(16);  // Vecteur d'initialisation 16 octets
    $salt = openssl_random_pseudo_bytes(32);  // Sel 32 octets
    
    // Dérivation de clé sécurisée avec PBKDF2
    $key = hash_pbkdf2('sha256', $passphrase, $salt, $iterations, 32, true);
    
    // Chiffrement avec AES-256-GCM
    $tag = null;  // Cette variable contiendra le tag d'authentification
    $ciphertext = openssl_encrypt(
        $content,
        'aes-256-gcm',
        $key,
        OPENSSL_RAW_DATA,
        $iv,
        $tag,
        '',  // Pas de données additionnelles authentifiées
        16   // Taille du tag est de 16 octets
    );
    
    if ($ciphertext === false) {
        throw new Exception("Échec du chiffrement : " . openssl_error_string());
    }
    
    // Format du fichier chiffré:
    // [Marqueur:8][Version:2][Iterations:4][Salt:32][IV:16][TagSize:1][Tag:16][Données]
    
    $output = pack('a8vVa32a16Ca*', 
        'CRYPTFRG',       // Marqueur magique de 8 octets
        1,                // Version du format (2 octets)
        $iterations,      // Nombre d'itérations (4 octets)
        $salt,            // Sel (32 octets)
        $iv,              // Vecteur d'initialisation (16 octets)
        16,               // Taille du tag d'authentification (1 octet)
        $tag . $ciphertext // Tag suivi du contenu chiffré
    );
    
    return $output;
}

/**
 * Fonction pour déchiffrer le contenu en utilisant AES-256-GCM
 * @param string $content Le contenu chiffré à déchiffrer
 * @param string $passphrase La phrase secrète utilisée pour le déchiffrement
 * @return string Le contenu déchiffré
 */
function secureDecrypt($content, $passphrase) {
    // Extraire les métadonnées du fichier chiffré
    $header = unpack('a8marker/vversion/Viterations/a32salt/a16iv/Ctagsize', $content);
    
    // Vérifier le marqueur magique
    if ($header['marker'] !== 'CRYPTFRG') {
        throw new Exception("Format de fichier non reconnu. Ce fichier n'a pas été chiffré avec CryptoForge.");
    }
    
    // Vérifier la version
    if ($header['version'] !== 1) {
        throw new Exception("Version de fichier non supportée.");
    }
    
    // Récupérer les métadonnées
    $iterations = $header['iterations'];
    $salt = $header['salt'];
    $iv = $header['iv'];
    $tagSize = $header['tagsize'];
    
    // Calculer la taille du header
    $headerSize = 8 + 2 + 4 + 32 + 16 + 1; // marqueur + version + iterations + salt + iv + tagsize
    
    // Extraire le tag et le contenu chiffré
    $tag = substr($content, $headerSize, $tagSize);
    $ciphertext = substr($content, $headerSize + $tagSize);
    
    // Dérivation de la clé avec PBKDF2 en utilisant le sel extrait
    $key = hash_pbkdf2('sha256', $passphrase, $salt, $iterations, 32, true);
    
    // Déchiffrement avec vérification d'intégrité
    $plaintext = openssl_decrypt(
        $ciphertext,
        'aes-256-gcm',
        $key,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );
    
    if ($plaintext === false) {
        throw new Exception("Échec du déchiffrement. La passphrase est incorrecte ou le fichier a été altéré.");
    }
    
    return $plaintext;
}

/**
 * Fonction pour traiter les fichiers volumineux par blocs
 * @param string $inputPath Chemin vers le fichier d'entrée
 * @param string $outputPath Chemin vers le fichier de sortie
 * @param string $passphrase Phrase secrète 
 * @param string $action Action à effectuer ('encrypt' ou 'decrypt')
 * @param int $iterations Nombre d'itérations pour PBKDF2
 */
function processFileByBlocks($inputPath, $outputPath, $passphrase, $action, $iterations = 100000) {
    $inputHandle = fopen($inputPath, 'rb');
    $outputHandle = fopen($outputPath, 'wb');
    
    if (!$inputHandle || !$outputHandle) {
        throw new Exception("Impossible d'ouvrir les fichiers pour le traitement.");
    }
    
    try {
        if ($action === 'encrypt') {
            // Pour le chiffrement, nous devons d'abord lire tout le contenu
            // car AES-GCM nécessite de traiter l'ensemble des données
            $content = '';
            while (!feof($inputHandle)) {
                $content .= fread($inputHandle, BLOCK_SIZE);
            }
            
            $encryptedContent = secureEncrypt($content, $passphrase, $iterations);
            fwrite($outputHandle, $encryptedContent);
        } else {
            // Pour le déchiffrement, nous devons également lire tout le contenu
            $content = '';
            while (!feof($inputHandle)) {
                $content .= fread($inputHandle, BLOCK_SIZE);
            }
            
            $decryptedContent = secureDecrypt($content, $passphrase);
            fwrite($outputHandle, $decryptedContent);
        }
    } finally {
        // Fermer les fichiers dans tous les cas
        fclose($inputHandle);
        fclose($outputHandle);
    }
}

// Vérifier si une requête a été soumise
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les paramètres
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $passphrase = isset($_POST['passphrase']) ? $_POST['passphrase'] : '';
    $iterations = isset($_POST['iterations']) ? intval($_POST['iterations']) : 100000;
    
    // Vérifier les contraintes sur les itérations (pour la sécurité)
    if ($iterations < 10000) {
        $iterations = 10000; // Minimum pour des raisons de sécurité
    } elseif ($iterations > 1000000) {
        $iterations = 1000000; // Maximum pour des raisons de performance
    }
    
    // Valider les entrées
    if (empty($_FILES['fileInput']['tmp_name']) || empty($passphrase)) {
        http_response_code(400);
        echo json_encode(['error' => 'Veuillez sélectionner un fichier et entrer une passphrase.']);
        exit;
    }
    
    // Obtenir le chemin temporaire du fichier uploadé
    $inputPath = $_FILES['fileInput']['tmp_name'];
    $fileName = $_FILES['fileInput']['name'];
    
    // Créer un nom de fichier temporaire pour la sortie
    $outputPath = tempnam(sys_get_temp_dir(), 'cryptoforge_');
    
    try {
        // Traiter le fichier par blocs
        processFileByBlocks($inputPath, $outputPath, $passphrase, $action, $iterations);
        
        // Définir le nom du fichier de sortie
        if ($action === 'encrypt') {
            $outputFileName = $fileName . '.encrypted';
        } else {
            $outputFileName = preg_replace('/\.encrypted$/', '', $fileName);
        }
        
        // Envoyer le fichier au client
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $outputFileName . '"');
        header('Content-Length: ' . filesize($outputPath));
        header('Pragma: no-cache');
        
        // Lire et envoyer le fichier par blocs
        $handle = fopen($outputPath, 'rb');
        while (!feof($handle)) {
            echo fread($handle, BLOCK_SIZE);
            flush();
        }
        fclose($handle);
        
        // Supprimer le fichier temporaire
        @unlink($outputPath);
        exit;
    } catch (Exception $e) {
        // Nettoyer en cas d'erreur
        @unlink($outputPath);
        
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

// Si on arrive ici, c'est que la méthode n'est pas autorisée
http_response_code(405);
echo json_encode(['error' => 'Méthode non autorisée.']);
?>
