# Karambol

Portail Web extensible

## Dépendances

- PHP >= 5.4
- php5-mysql

## Démarrer à partir des sources

```bash
cd karambol
./composer install
touch config/local.yml # Editer/ajouter vos paramètres locaux de configuration en vous basant sur le fichier default.yml
php vendor/bin/doctrine orm:schema-tool:create # Création du schema de la BDD
# ou
php vendor/bin/doctrine orm:schema-tool:update # Mise à jour du schema de la BDD
./start-dev-server
```

## Documentation et tutoriels

Voir [ici](./doc)
