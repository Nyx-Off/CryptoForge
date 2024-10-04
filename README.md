# CryptoForge

![Static Badge](https://img.shields.io/badge/Contributeur-1-brightgreen?style=flat&logo=clubhouse&logoColor=white&logoSize=auto) ![License](https://img.shields.io/github/license/Nyx-Off/AceVenturaTheGame) 
![Static Badge](https://img.shields.io/badge/JavaScript-black?style=plastic&logo=javascript&logoColor=white&logoSize=auto&color=purple)
![Static Badge](https://img.shields.io/badge/HTML-black?style=plastic&logo=html5&logoColor=white&logoSize=auto&color=orange)
![Static Badge](https://img.shields.io/badge/CSS-black?style=plastic&logo=css3&logoColor=white&logoSize=auto&color=blue)
![Static Badge](https://img.shields.io/badge/PHP-black?style=plastic&logo=php&logoColor=white&logoSize=auto&color=green)
![Static Badge](https://img.shields.io/badge/Cryptographie-grey?style=plastic&logo=letsencrypt&logoSize=auto&color=darkred)


## Description
CryptoForge est un outil de chiffrement et de déchiffrement de fichiers basé sur une méthode de chiffrement maison qui offre une sécurité robuste grâce à l'utilisation de transformations arithmétiques et d'un sel dynamique. L'application fournit une interface web simple et intuitive permettant de chiffrer et déchiffrer des fichiers directement depuis votre navigateur.

## Fonctionnalités
- **Chiffrement des fichiers** : Protégez vos fichiers à l'aide d'une passphrase, en utilisant une méthode de chiffrement complexe avec un sel dynamique.
- **Déchiffrement des fichiers** : Restaurez facilement vos fichiers chiffrés en utilisant la même passphrase.
- **Interface web conviviale** : Interface élégante et minimaliste permettant un usage rapide et facile.

## Installation
Pour utiliser CryptoForge, vous devez disposer d'un serveur web avec PHP (au moins la version 7.4) installé. Voici les étapes pour installer le projet :

1. Clonez le dépôt GitHub :
   ```sh
   git clone https://github.com/username/cryptoforge.git
   ```

2. Placez les fichiers dans un dossier accessible par votre serveur web (par exemple, `/var/www/html/cryptoforge`).

3. Assurez-vous que PHP est correctement installé et configuré sur votre serveur.

## Utilisation
### Chiffrement et Déchiffrement via Interface Web
1. Accédez à l'interface web de CryptoForge via l'URL de votre serveur (par exemple, `http://localhost/cryptoforge`).
2. **Choisir un fichier** : Cliquez sur "Choisir un fichier" et sélectionnez le fichier à chiffrer ou déchiffrer.
3. **Entrer une passphrase** : Entrez une passphrase qui sera utilisée pour sécuriser le fichier.
4. Cliquez sur **Chiffrer** ou **Déchiffrer** selon l'action souhaitée.
5. Le fichier chiffré ou déchiffré sera téléchargé automatiquement.

### Fonctionnement Interne
- L'algorithme de chiffrement génère une clé à partir de la passphrase, en utilisant une combinaison d'opérations arithmétiques complexes et d'un sel dynamique pour chaque opération.
- Le sel est intégré à la sortie chiffrée, permettant de sécuriser le processus même si la passphrase est la même pour plusieurs fichiers.
- L'interface web permet une interaction utilisateur simple, avec un arrière-plan animé de style "matrix" pour une touche visuelle distinctive.

## Détails Techniques
- **Chiffrement personnalisé** : L'algorithme utilise des transformations arithmétiques impliquant la passphrase, un sel dynamique, et des opérations modulaires pour assurer une bonne complexité.

  ### Étapes du Chiffrement :
  1. **Génération de la Clé** :
     - Une clé est générée à partir de la passphrase fournie par l'utilisateur.
     - Chaque caractère de la passphrase est multiplié par sa position (`$i + 1`) et un sel dynamique aléatoire compris entre 100 et 999 est ajouté. Le tout est réduit modulo 256 pour garantir une valeur dans l'intervalle des caractères ASCII étendus.
     - Cela donne une clé unique pour chaque chiffrement grâce à l'ajout du sel dynamique.
  
  2. **Transformation du Contenu** :
     - Le contenu du fichier est transformé caractère par caractère.
     - Chaque caractère du contenu est modifié en utilisant :
       - La valeur de la clé correspondante (`$keyChar`).
       - Un facteur d'offset basé sur la position du caractère dans le contenu (`$index % 17`).
       - Un terme supplémentaire basé sur la multiplication de l'indice par le sel (`$index * $salt % 23`).
     - La formule finale pour le chiffrement est :
       
       ```
       $transformedChar = ($charCode + $keyChar + ($index % 17) + ($index * $salt) % 23) % 256;
       ```
     - Le résultat est converti en caractère et ajouté à la chaîne de sortie chiffrée.

  3. **Ajout du Sel** :
     - Le sel utilisé pour générer la clé est ajouté au début de la chaîne chiffrée pour permettre le déchiffrement ultérieur.
     - La sortie est ensuite encodée en Base64 pour un stockage et un transfert sûrs.

  ### Étapes du Déchiffrement :
  1. **Extraction du Sel et du Contenu Chiffré** :
     - Le contenu est d'abord décodé depuis Base64.
     - Le sel est extrait de la chaîne, suivi du contenu chiffré restant.
  
  2. **Reconstruction de la Clé** :
     - La même procédure de génération de clé est suivie en utilisant la passphrase et le sel extrait.
  
  3. **Transformation Inverse** :
     - Pour chaque caractère chiffré, l'algorithme applique la transformation inverse :
       
       ```
       $originalChar = ($charCode - $keyChar - ($index % 17) - ($index * $salt) % 23 + 256) % 256;
       ```
     - Cela permet de retrouver le caractère original.

- **Sel dynamique** : Un sel aléatoire est généré à chaque chiffrement, rendant chaque opération unique et augmentant la résistance aux attaques par cryptanalyse. Ce sel est essentiel pour garantir qu'un même fichier chiffré deux fois avec la même passphrase donnera deux résultats différents.

- **Sécurité** : L'algorithme utilise des transformations arithmétiques complexes pour renforcer la sécurité, notamment :
  - Des opérations modulaires pour limiter les valeurs dans un certain intervalle, rendant plus difficile la prédiction des résultats.
  - Un mélange de transformations basées sur la position des caractères, la passphrase et le sel.
  - Cependant, cet algorithme n'a pas été audité par des experts en cryptographie et ne doit pas être considéré comme aussi sécurisé que les standards comme AES.

  ### Résistance au Brute Force
  - **Complexité de la Passphrase** : La sécurité du chiffrement dépend fortement de la longueur et de la complexité de la passphrase.
    - Pour une passphrase de longueur `n` composée de lettres majuscules, minuscules et de chiffres (`62` possibilités par caractère), le nombre de combinaisons possibles est `62^n`.
    - Par exemple, pour une passphrase de 8 caractères, cela donne environ `218 340 105 584 896` combinaisons possibles.
  - **Temps de Brute Force Estimé** :
    - Si un attaquant peut tester **un million de passphrases par seconde** (ce qui est une vitesse élevée mais possible avec des GPU modernes), le temps nécessaire pour brute-forcer une passphrase de 8 caractères serait :
      
      ```
      218 340 105 584 896 / 1 000 000 ≈ 218 340 105 secondes ≈ 6 934 ans
      ```
    - En augmentant la longueur de la passphrase à 12 caractères, le nombre de combinaisons possibles devient astronomique (`62^12`), rendant le brute-force extrêmement difficile, même avec une grande puissance de calcul.
  - **Impact du Sel Dynamique** : L'utilisation d'un sel aléatoire pour chaque chiffrement signifie qu'une même passphrase appliquée à deux fichiers différents génèrera des sorties distinctes. Cela empêche les attaques par pré-calcul (comme les attaques par rainbow table), augmentant ainsi la résistance globale à la cryptanalyse.

## Exemple de Configuration
- Placez le fichier `process.php` (le script PHP de traitement du chiffrement/déchiffrement) dans le répertoire principal.
- Le fichier HTML inclus (`index.html`) est utilisé pour offrir une interface utilisateur agréable.

## Contributions
Les contributions sont les bienvenues ! N'hésitez pas à soumettre des *issues* ou des *pull requests* pour améliorer CryptoForge.

## Licence
Ce projet est sous licence MIT - voir le fichier `LICENSE` pour plus de détails.
