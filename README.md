# WAC-api-test

Test technique pour la WEB ACADEMY de IONIS

C'est une API REST en Symfony 4.

## Usage
Pour mettre en place le projet clonez le dépot puis à la racine
```
composer install
```

Puis modifiez le ```.env``` l'attribut ```DATABASE_URL``` 
```DATABASE_URL=mysql://db_user:db_password@db_ip:db_port/wac-test```
Remplacez ```db_user``` par votre nom d'utilisateur mysql, ```db_password``` par votre mot de passe sql, ```db_ip``` l'addresse ip de votre mysql et ```db_port``` par le port de votre mysql

Puis executez les commandes suivante :
```
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```
Puis pour lancer le server executez :

```
php bin/console server:run
```

Pour connaître toutes les routes de l'API :
```
php bin/console debug:router
```