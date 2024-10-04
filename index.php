<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chiffrement/Déchiffrement de Fichiers</title>
    <style>
        /* Styles existants inchangés */
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
            max-width: 400px;
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
        input, button, .file-input-wrapper {
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
        button {
            background-color: #ff9800;
            color: #2c2c2c;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #ffa726;
        }
        #output {
            margin-top: 20px;
            padding: 15px;
            background-color: #3d3d3d;
            border-radius: 5px;
        }
        #matrix-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
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
        }
    </style>
</head>
<body>
    <canvas id="matrix-background"></canvas>
    <div class="container">
        <h1>Chiffrement/Déchiffrement de Fichiers</h1>
        <form id="cryptForm">
            <div class="file-input-wrapper">
                <span>Choisir un fichier</span>
                <input type="file" name="fileInput" id="fileInput">
            </div>
            <input type="password" name="passphrase" id="passphrase" placeholder="Entrez votre passphrase">
            <button type="button" onclick="processFile('encrypt')">Chiffrer</button>
            <button type="button" onclick="processFile('decrypt')">Déchiffrer</button>
        </form>
        <div id="output"></div>
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

        // Fonction pour traiter le fichier (chiffrement ou déchiffrement)
        function processFile(action) {
            const fileInput = document.getElementById('fileInput');
            const passphrase = document.getElementById('passphrase');
            const output = document.getElementById('output');

            if (!fileInput.files.length || !passphrase.value) {
                output.textContent = 'Veuillez sélectionner un fichier et entrer une passphrase.';
                return;
            }

            const formData = new FormData();
            formData.append('fileInput', fileInput.files[0]);
            formData.append('passphrase', passphrase.value);
            formData.append('action', action);

            fetch('process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.blob())
            .then(blob => {
                let originalFileName = fileInput.files[0].name;

                if (action === 'decrypt') {
                    // Si le fichier se termine par ".encrypted", on le retire pour retourner au nom original
                    originalFileName = originalFileName.replace(/\.encrypted$/, '');
                }

                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = originalFileName + (action === 'encrypt' ? '.encrypted' : '');
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);

                // Afficher le popup
                const popup = document.getElementById('popup');
                popup.style.display = 'block';
                setTimeout(() => { popup.style.display = 'none'; }, 3000);

                // Effacer le mot de passe
                passphrase.value = '';

                // Réinitialiser le champ de fichier
                fileInput.value = '';
                document.querySelector('.file-input-wrapper span').textContent = 'Choisir un fichier';

                output.textContent = 'Fichier ' + (action === 'encrypt' ? 'chiffré' : 'déchiffré') + ' avec succès.';
            })
            .catch(error => {
                output.textContent = 'Une erreur est survenue : ' + error.message;
            });
        }

        // Mise à jour du nom de fichier sélectionné
        document.getElementById('fileInput').addEventListener('change', function(e) {
            var fileName = e.target.files[0] ? e.target.files[0].name : 'Choisir un fichier';
            document.querySelector('.file-input-wrapper span').textContent = fileName;
        });
    </script>
</body>
</html>