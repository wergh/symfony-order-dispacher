docker compose up -d --build

docker-compose exec app php bin/console doctrine:migrations:migrate

docker-compose exec app php bin/console doctrine:schema:validate

docker-compose exec app php bin/console doctrine:fixtures:load
