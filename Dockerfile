FROM php:latest
WORKDIR /app

RUN docker-php-ext-install mysqli

COPY src/ ./
CMD ["php", "-S", "0.0.0.0:80"]

