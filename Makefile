build:
	docker-compose build
start:
	docker-compose up -d
stop:
	docker-compose stop
npm-dev:
	docker-compose run --rm npm run dev
npm-install:
	docker-compose run --rm npm install
migrate:
	docker-compose run --rm artisan migrate
