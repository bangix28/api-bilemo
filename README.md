# Bilemo
## _La meilleur api de ventes de téléphones_

Bilemo est une api crée avec symfony est FOS REST bundle.
Ce projets a été crée dans le cadre du projets 7 d'Openclassrooms de la formation développeur d'application PHP/Symfony.

## Requis

- Docker
- Composer

## Installation

1- télécharger le repository.

2- allez dans le dossier api-bilemo avec votre invite de commande est builder l'image avec docker et monter cette image.

```sh
cd path/to/api-bilemo
docker build -t bilemo .
docker-composer up
```
3-Installez les dépendance est le fichier .env avec composer install.
```sh
composer install
```
4-Dans le fichier .env définissez la base de donnée.
```sh
DATABASE_URL="mysql://root:@mysql/bilemo"
```
5- Créer la base de données.
```sh
php bin/console doctrine:database:create
```
6-Importer les entité dans la base de donnée.
```sh
php bin/console doctrine:schema:create
```
7-Installez les fixtures.
```sh
php bin/console doctrine:fixture:load
```

Pour tester l'application est utilisez la doc allez a l'adresse suivantes : http://localhost/doc