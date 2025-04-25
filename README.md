# CryptoForge

![Static Badge](https://img.shields.io/badge/Contributeur-1-brightgreen?style=flat&logo=clubhouse&logoColor=white&logoSize=auto) ![License](https://img.shields.io/github/license/Nyx-Off/AceVenturaTheGame) 
![Static Badge](https://img.shields.io/badge/JavaScript-black?style=plastic&logo=javascript&logoColor=white&logoSize=auto&color=purple)
![Static Badge](https://img.shields.io/badge/HTML-black?style=plastic&logo=html5&logoColor=white&logoSize=auto&color=orange)
![Static Badge](https://img.shields.io/badge/CSS-black?style=plastic&logo=css3&logoColor=white&logoSize=auto&color=blue)
![Static Badge](https://img.shields.io/badge/PHP-black?style=plastic&logo=php&logoColor=white&logoSize=auto&color=green)
![Static Badge](https://img.shields.io/badge/Cryptographie-grey?style=plastic&logo=letsencrypt&logoSize=auto&color=darkred)


## Description
CryptoForge est un outil de chiffrement et de déchiffrement de fichiers basé sur des algorithmes cryptographiques standards (AES-256-GCM), qui offre une sécurité robuste pour la protection de vos données. L'application fournit une interface web simple et intuitive permettant de chiffrer et déchiffrer des fichiers directement depuis votre navigateur.

## Fonctionnalités
- **Chiffrement des fichiers** : Protégez vos fichiers à l'aide d'une passphrase, en utilisant l'algorithme AES-256-GCM avec une dérivation de clé sécurisée (PBKDF2).
- **Déchiffrement des fichiers** : Restaurez facilement vos fichiers chiffrés en utilisant la même passphrase.
- **Interface web conviviale** : Interface élégante et minimaliste permettant un usage rapide et facile.
- **Vérification d'intégrité** : Détection automatique de toute altération des fichiers grâce au mode GCM authentifié.
- **Traitement par blocs** : Prise en charge de fichiers volumineux grâce au traitement par blocs.

## Installation
Pour utiliser CryptoForge, vous devez disposer d'un serveur web avec PHP (au moins la version 7.4) installé. Voici les étapes pour installer le projet :

1. Clonez le dépôt GitHub :
   ```sh
   git clone https://github.com/username/cryptoforge.git
   ```

2. Placez les fichiers dans un dossier accessible par votre serveur web (par exemple, `/var/www/html/cryptoforge`).

3. Assurez-vous que PHP est correctement installé et configuré sur votre serveur, avec l'extension OpenSSL activée.

## Utilisation
### Chiffrement et Déchiffrement via Interface Web
1. Accédez à l'interface web de CryptoForge via l'URL de votre serveur (par exemple, `http://localhost/cryptoforge`).
2. **Choisir un fichier** : Cliquez sur "Choisir un fichier" et sélectionnez le fichier à chiffrer ou déchiffrer.
3. **Entrer une passphrase** : Entrez une passphrase qui sera utilisée pour sécuriser le fichier.
4. Cliquez sur **Chiffrer** ou **Déchiffrer** selon l'action souhaitée.
5. Le fichier chiffré ou déchiffré sera téléchargé automatiquement.

### Fonctionnement Interne
- Le chiffrement utilise AES-256-GCM, un algorithme de chiffrement authentifié standard de l'industrie.
- Une fonction de dérivation de clé sécurisée (PBKDF2) avec 100 000 itérations est utilisée pour transformer la passphrase en clé.
- Un sel aléatoire cryptographiquement sécurisé est généré pour chaque opération de chiffrement.
- Le traitement par blocs permet de gérer des fichiers de grande taille sans consommer trop de mémoire.
- L'interface web permet une interaction utilisateur simple, avec un arrière-plan animé de style "matrix" pour une touche visuelle distinctive.

## Détails Techniques
- **Chiffrement standard** : L'algorithme utilise AES-256-GCM, un mode de chiffrement authentifié qui garantit à la fois la confidentialité et l'intégrité des données.

  ### Étapes du Chiffrement :
  1. **Génération du Matériel Cryptographique** :
     - Un vecteur d'initialisation (IV) aléatoire de 16 octets est généré.
     - Un sel cryptographiquement sécurisé de 32 octets est créé.
     - Ces éléments garantissent que chaque opération de chiffrement est unique.
  
  2. **Dérivation de Clé** :
     - La passphrase de l'utilisateur est transformée en clé cryptographique via PBKDF2.
     - 100 000 itérations sont appliquées pour ralentir les attaques par force brute.
  
  3. **Chiffrement Authentifié** :
     - Le contenu est chiffré avec AES-256-GCM qui produit à la fois le texte chiffré et un tag d'authentification.
     - Ce tag permet de vérifier l'intégrité du fichier lors du déchiffrement.
  
  4. **Stockage des Métadonnées** :
     - L'IV, le sel et le tag d'authentification sont ajoutés au fichier chiffré.
     - Ces métadonnées sont nécessaires pour le déchiffrement et la vérification d'intégrité.

  ### Étapes du Déchiffrement :
  1. **Extraction des Métadonnées** :
     - L'IV, le sel et le tag d'authentification sont extraits du fichier chiffré.
  
  2. **Reconstruction de la Clé** :
     - La même procédure PBKDF2 est appliquée pour dériver la clé à partir de la passphrase et du sel.
  
  3. **Déchiffrement et Vérification** :
     - AES-256-GCM déchiffre le contenu et vérifie simultanément son intégrité via le tag d'authentification.
     - Si le fichier a été modifié, l'opération échoue, protégeant ainsi contre les altérations.

- **Traitement par Blocs** : Le système traite les fichiers par blocs de 1 Mo, ce qui permet de gérer efficacement des fichiers volumineux sans surcharger la mémoire du serveur.

- **Sécurité** : L'utilisation d'algorithmes cryptographiques standards et éprouvés garantit un niveau de sécurité conforme aux meilleures pratiques actuelles.

  ### Résistance aux Attaques
  - **Protection Contre le Brute Force** : La dérivation de clé avec 100 000 itérations ralentit considérablement les tentatives de force brute.
  - **Résistance à la Cryptanalyse** : AES est résistant à toutes les formes connues de cryptanalyse lorsqu'il est correctement implémenté.
  - **Défense Contre les Attaques par Table Arc-en-Ciel** : L'utilisation d'un sel unique pour chaque fichier rend les attaques par précalcul inefficaces.
  - **Protection de l'Intégrité** : Le mode GCM authentifié détecte toute modification non autorisée du contenu chiffré.

## Exemple de Configuration
- Placez le fichier `process.php` (le script PHP de traitement du chiffrement/déchiffrement) dans le répertoire principal.
- Le fichier `index.php` est utilisé pour offrir une interface utilisateur agréable.
- Assurez-vous que l'extension OpenSSL de PHP est activée dans votre configuration PHP.

## Contributions
Les contributions sont les bienvenues ! N'hésitez pas à soumettre des *issues* ou des *pull requests* pour améliorer CryptoForge.

## Licence
Ce projet est sous licence MIT - voir le fichier `LICENSE` pour plus de détails.
