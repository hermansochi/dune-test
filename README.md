# dune-test

Для сборки проекта в системе должны быть установлены: Make, docker и docker compose CLI plugin.
Если используете традиционный docker-compose, замените все вхождения docker compose на docker-compose
в корне проекта в Makefile.

1. git clone

2. make init в корне проекта. Соберется 4 контейнера: контейнер с приложением, реверс прокси Traefik (просто для удобства), php-fpm и php-cli (вызывается по требованию).

3. make test  - unit и functional тесты с традиционным выводом PHPUnit.

4. make tests - unit и functional тесты с выводом в Laravel стиле.

5. make analyze - стат. анализ Psalm

## Tests:


## Stat analyze:

