
# Projet 5 - Openclassrooms

Ce projet est destiné à développer un blog professionnel.


## Installation

Suivez les étapes ci-dessous pour installer et configurer le projet sur votre machine locale.

### 1. Clonez le dépôt Git

```bash
git clone https://github.com/cactuseure/Openclassrooms_projet5.git
```

### 2. Installez les dépendances Composer

Assurez-vous d'avoir Composer installé. Si ce n'est pas le cas, vous pouvez le télécharger sur https://getcomposer.org/.

```bash
cd votre-projet
composer install
```

### 3. Configurez le fichier .env

Créez un fichier `.env` à la racine de votre projet et ajoutez les variables d'environnement suivantes :
```
DB_HOST=adresse_de_votre_base_de_donnees
DB_NAME=nom_de_votre_base_de_donnees
DB_USER=utilisateur_de_la_base_de_donnees
DB_PASSWORD=mot_de_passe_de_la_base_de_donnees
CONTACT_EMAIL=adresse_email_de_contact
```

Assurez-vous de remplir les valeurs appropriées pour votre configuration.

### 4. Initialisez la base de données
Importez le fichier SQL `install.sql` situé dans le répertoire `sql/` pour initialiser la base de données avec des données de test.

### 5. Lancer l'application
Vous pouvez maintenant lancer votre application. Assurez-vous que votre serveur web est configuré pour pointer vers le répertoire de votre projet.
## Contribution

Si vous souhaitez contribuer à ce projet, veuillez suivre les étapes suivantes :

Fork du dépôt sur GitHub.
Clonez votre fork sur votre machine locale.
Créez une branche pour votre contribution :
- git checkout -b ma-contribution.
- Faites vos modifications et committez-les : git commit -m "Ajout de fonctionnalité X".
- Poussez votre branche vers votre fork : git push origin ma-contribution.
- Créez une pull request depuis votre fork vers ce dépôt principal.

## Licence

Ce projet est sous licence GNU GPLv3. Pour plus de détails, veuillez consulter le fichier `LICENSE.md`.

[![GNU License](https://img.shields.io/badge/License-GNU%20GPL-blue)](https://choosealicense.com/licenses/gpl-3.0/)