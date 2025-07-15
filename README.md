# biblioconnect

## Auteurs : Khalid Zaim, Ayoub Chaoui, Youssef Kaddouhi

### Installation

composer install

php bin/console doctrine:database:create

php bin/console make:migration

php bin/console doctrine:migration:migrate

php bin/console doctrine:fixtures:load

##### Test
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
php bin/console doctrine:fixtures:load --env=test
php bin/phpunit

#### Accounts
ADMIN:
- Login : admin@biblioconnect.fr
- Mot de passe : password
LIBRARIEN:
- Login : librarien@biblioconnect.fr
- Mot de passe : password
USER:
- Login : user@biblioconnect.fr
- Mot de passe : password


🧪 Test de charge avec Locust

pip install locust
locust -f locustfile.py --host=https://localhost:8000

Résultats des tests de performance
Test avec 170 utilisateurs

Moyenne temps de réponse : 15s

0% d’erreurs HTTP dans la dernière simulation

Fichiers de résultats dans /docs/ ou racine du projet

📎 Liens utiles
Documentation Locust: https://locust.io/
