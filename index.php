<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptoForge - Chiffrement/Déchiffrement Sécurisé</title>
    <style>
        /* Styles globaux */
        body, html {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: #2c2c2c;
            color: #e0e0e0;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            width: 100%;
            max-width: 500px;
            padding: 40px;
            position: relative;
            z-index: 1;
            background-color: rgba(44, 44, 44, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 300;
            color: #ff9800;
        }
        
        /* Entrées et boutons */
        input, button, .file-input-wrapper, .select-wrapper {
            margin: 15px 0;
            display: block;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        input[type="password"] {
            background-color: #3d3d3d;
            color: #e0e0e0;
        }
        input[type="password"]:focus {
            outline: none;
            box-shadow: 0 0 0 2px #ff9800;
        }
        
        /* Wrapper pour le sélecteur de fichier */
        .file-input-wrapper {
            background-color: #3d3d3d;
            color: #e0e0e0;
            cursor: pointer;
            text-align: center;
            overflow: hidden;
            position: relative;
        }
        .file-input-wrapper:hover {
            background-color: #4a4a4a;
        }
        .file-input-wrapper input[type="file"] {
            position: absolute;
            font-size: 100px;
            right: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
        
        /* Sélecteur de force de chiffrement */
        .select-wrapper {
            background-color: #3d3d3d;
            color: #e0e0e0;
            position: relative;
            padding: 0;
        }
        select {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background-color: #3d3d3d;
            color: #e0e0e0;
            appearance: none;
            cursor: pointer;
        }
        .select-wrapper::after {
            content: '\25BC';
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #ff9800;
        }
        
        /* Boutons d'action */
        button {
            background-color: #ff9800;
            color: #2c2c2c;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        button:hover {
            background-color: #ffa726;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        button:active {
            transform: translateY(0);
        }
        
        /* Zones d'informations */
        #output {
            margin-top: 20px;
            padding: 15px;
            background-color: #3d3d3d;
            border-radius: 5px;
            min-height: 20px;
        }
        
        /* Animation matrix */
        #matrix-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
        
        /* Messages de notification */
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 5px;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            animation: fadeIn 0.3s ease;
        }
        .popup.error {
            background-color: #f44336;
        }
        
        /* Progress bar */
        .progress-container {
            width: 100%;
            background-color: #3d3d3d;
            border-radius: 5px;
            margin: 15px 0;
            overflow: hidden;
            display: none;
        }
        .progress-bar {
            height: 20px;
            width: 0;
            background-color: #ff9800;
            text-align: center;
            line-height: 20px;
            color: #2c2c2c;
            font-weight: bold;
            transition: width 0.3s ease;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #999;
        }
        
        /* Indicateur de force de mot de passe */
        .password-strength {
            margin-top: 5px;
            height: 5px;
            background-color: #3d3d3d;
            border-radius: 5px;
            overflow: hidden;
        }
        .strength-meter {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }
        .strength-text {
            font-size: 12px;
            text-align: right;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <canvas id="matrix-background"></canvas>
    <div class="container">
        <h1>CryptoForge</h1>
        <form id="cryptForm">
            <div class="file-input-wrapper">
                <span>Choisir un fichier</span>
                <input type="file" name="fileInput" id="fileInput">
            </div>
            
            <div>
                <input type="password" name="passphrase" id="passphrase" placeholder="Entrez votre passphrase" autocomplete="new-password" oninput="checkPasswordStrength()">
                <div class="password-strength">
                    <div id="strength-meter" class="strength-meter"></div>
                </div>
                <div id="strength-text" class="strength-text">Force du mot de passe</div>
            </div>
            
            <div class="select-wrapper">
                <select id="security-level" name="security-level">
                    <option value="100000">Sécurité standard (recommandé)</option>
                    <option value="250000">Sécurité élevée (plus lent)</option>
                    <option value="50000">Performance (moins sécurisé)</option>
                </select>
            </div>
            
            <div class="progress-container" id="progress-container">
                <div class="progress-bar" id="progress-bar">0%</div>
            </div>
            
            <button type="button" onclick="processFile('encrypt')">Chiffrer</button>
            <button type="button" onclick="processFile('decrypt')">Déchiffrer</button>
        </form>
        <div id="output"></div>
        
        <div class="footer">
            CryptoForge - Sécurité AES-256-GCM - PBKDF2
        </div>
    </div>
    <div id="popup" class="popup">Opération réussie !</div>

    <script>
        // Animation de la matrice
        const canvas = document.getElementById('matrix-background');
        const ctx = canvas.getContext('2d');

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*()_+-=[]{}|;:,.<>?";
        const columns = canvas.width / 20;
        const drops = [];

        for (let i = 0; i < columns; i++) {
            drops[i] = 1;
        }

        function drawMatrix() {
            ctx.fillStyle = 'rgba(44, 44, 44, 0.05)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = '#ff9800';
            ctx.font = '15px monospace';

            for (let i = 0; i < drops.length; i++) {
                const text = characters[Math.floor(Math.random() * characters.length)];
                const x = i * 20;
                const y = drops[i] * 20;

                ctx.fillText(text, x, y);

                if (y > canvas.height && Math.random() > 0.99) {
                    drops[i] = 0;
                }

                drops[i]++;
            }
        }

        setInterval(drawMatrix, 50);

        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        // Fonction pour vérifier la force du mot de passe
        function checkPasswordStrength() {
            const password = document.getElementById('passphrase').value;
            const meter = document.getElementById('strength-meter');
            const text = document.getElementById('strength-text');
            
            // Critères de force
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumbers = /\d/.test(password);
            const hasSpecialChars = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
            const length = password.length;
            
            // Calcul du score
            let score = 0;
            if (length > 0) score += Math.min(length * 4, 25);
            if (hasUpperCase) score += 10;
            if (hasLowerCase) score += 10;
            if (hasNumbers) score += 10;
            if (hasSpecialChars) score += 15;
            if (length >= 12) score += 20;
            
            // Limiter le score à 100
            score = Math.min(score, 100);
            
            // Mise à jour visuelle
            meter.style.width = score + '%';
            
            // Couleur en fonction du score
            if (score < 30) {
                meter.style.backgroundColor = '#f44336'; // Rouge
                text.textContent = 'Très faible';
                text.style.color = '#f44336';
            } else if (score < 60) {
                meter.style.backgroundColor = '#ff9800'; // Orange
                text.textContent = 'Faible';
                text.style.color = '#ff9800';
            } else if (score < 80) {
                meter.style.backgroundColor = '#ffc107'; // Jaune
                text.textContent = 'Moyen';
                text.style.color = '#ffc107';
            } else {
                meter.style.backgroundColor = '#4CAF50'; // Vert
                text.textContent = 'Fort';
                text.style.color = '#4CAF50';
            }
        }

        // Fonction pour traiter le fichier (chiffrement ou déchiffrement)
        function processFile(action) {
            const fileInput = document.getElementById('fileInput');
            const passphrase = document.getElementById('passphrase');
            const securityLevel = document.getElementById('security-level');
            const output = document.getElementById('output');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            
            // Validation des entrées
            if (!fileInput.files.length || !passphrase.value) {
                showPopup('Veuillez sélectionner un fichier et entrer une passphrase.', true);
                return;
            }
            
            // Avertissement si mot de passe faible
            if (action === 'encrypt' && passphrase.value.length < 8) {
                if (!confirm('Votre mot de passe est court et potentiellement faible. Continuer quand même?')) {
                    return;
                }
            }

            // Préparer les données
            const formData = new FormData();
            formData.append('fileInput', fileInput.files[0]);
            formData.append('passphrase', passphrase.value);
            formData.append('action', action);
            formData.append('iterations', securityLevel.value);

            // Afficher la barre de progression
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            progressBar.textContent = '0%';
            
            // Message de traitement
            output.textContent = `${action === 'encrypt' ? 'Chiffrement' : 'Déchiffrement'} en cours...`;
            
            // Simuler la progression (en réalité, le serveur ne renvoie pas la progression)
            let progress = 0;
            const progressInterval = setInterval(() => {
                if (progress < 90) {
                    progress += Math.random() * 5;
                    progressBar.style.width = `${progress}%`;
                    progressBar.textContent = `${Math.round(progress)}%`;
                }
            }, 300);

            // Envoyer la requête
            fetch('process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                clearInterval(progressInterval);
                
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Une erreur est survenue');
                    });
                }
                
                // Compléter la barre de progression
                progressBar.style.width = '100%';
                progressBar.textContent = '100%';
                
                return response.blob();
            })
            .then(blob => {
                let originalFileName = fileInput.files[0].name;

                // Gérer les extensions de fichier
                if (action === 'decrypt') {
                    // Si le fichier se termine par ".encrypted", on le retire
                    originalFileName = originalFileName.replace(/\.encrypted$/, '');
                }

                // Créer et déclencher le téléchargement
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = originalFileName + (action === 'encrypt' ? '.encrypted' : '');
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);

                // Afficher le message de succès
                showPopup(`Fichier ${action === 'encrypt' ? 'chiffré' : 'déchiffré'} avec succès !`);

                // Nettoyer l'interface
                passphrase.value = '';
                fileInput.value = '';
                document.querySelector('.file-input-wrapper span').textContent = 'Choisir un fichier';
                output.textContent = `Fichier ${action === 'encrypt' ? 'chiffré' : 'déchiffré'} avec succès.`;
                
                // Cacher progressivement la barre de progression
                setTimeout(() => {
                    progressContainer.style.display = 'none';
                }, 1000);
                
                // Réinitialiser l'indicateur de force du mot de passe
                checkPasswordStrength();
            })
            .catch(error => {
                clearInterval(progressInterval);
                progressContainer.style.display = 'none';
                output.textContent = 'Erreur : ' + error.message;
                showPopup(error.message, true);
            });
        }

        // Fonction pour afficher les popups
        function showPopup(message, isError = false) {
            const popup = document.getElementById('popup');
            popup.textContent = message;
            popup.className = 'popup' + (isError ? ' error' : '');
            popup.style.display = 'block';
            
            setTimeout(() => {
                popup.style.display = 'none';
            }, 3000);
        }

        // Mise à jour du nom de fichier sélectionné
        document.getElementById('fileInput').addEventListener('change', function(e) {
            var fileName = e.target.files[0] ? e.target.files[0].name : 'Choisir un fichier';
            document.querySelector('.file-input-wrapper span').textContent = fileName;
        });
        
        // Initialisation
        checkPasswordStrength();
    </script>
</body>
</html>
