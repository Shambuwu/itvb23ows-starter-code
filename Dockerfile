# Use an official Composer image to create a build artifact
FROM composer:latest as composer

# Set the working directory to /app
WORKDIR /app

# Copy the composer.json and composer.lock files into the container
COPY ./composer.json ./composer.lock ./

# Install project dependencies
RUN composer install --no-dev --no-scripts --prefer-dist --no-autoloader && rm -rf /root/.composer

# Copy the rest of the code
COPY src/ ./

# Dump the autoloader
RUN composer dump-autoload --no-scripts --optimize


# Use the official PHP image to create a runtime container
FROM php:latest

# Set the working directory in the container
WORKDIR /app

# Install the mysqli extension
RUN docker-php-ext-install mysqli

# Copy the Composer dependencies (vendor folder) from the Composer container
COPY --from=composer /app/vendor ./vendor

# Copy the rest of the application code
COPY src/ ./

# Start the PHP built-in server
CMD ["php", "-S", "0.0.0.0:80"]
