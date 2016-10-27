# <img src="https://cdn.rawgit.com/Bornholm/karambol/develop/public/img/logo.svg" width="50" /> Karambol

Portail web extensible

## Dépendances

- PHP >= 5.4
- php5-mysql

## Démarrer à partir des sources

```bash
# Vérification/installation des dépendances et génération du fichier de configuration local
bin/install
# Vérification de la connexion à la base de données et mise à jour si nécessaire
bin/migrate
# Au besoin, vous pouvez importer des règles d'amorçage de votre portail
bin/cli karambol:rules:load resources/seed.yml --cleanup=all
# Lancement du serveur de développement, vous pouvez ajouter l'argument <port> si vous voulez modifier le port d'écoute par défaut (8080)
bin/server
```

## Lancer les tests

```bash
./script/test
```

## Documentation et tutoriels

Voir [ici](./doc/index.md)

## Licence

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).
