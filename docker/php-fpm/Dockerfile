FROM composer as composer

FROM php:7.4-fpm
# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get install -y git bash wget curl gnupg libbz2-dev zlib1g-dev zip libzip-dev libpng-dev libicu-dev libtidy-dev wkhtmltopdf ghostscript xvfb

RUN docker-php-ext-install opcache pdo pdo_mysql bz2 gd intl tidy tokenizer zip
RUN pecl install redis && docker-php-ext-enable redis
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN apt-get install -y xvfb libfontconfig wkhtmltopdf mycli

RUN wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony/bin/symfony /usr/bin/symfony

# Install Nodejs and Yarn
RUN wget https://deb.nodesource.com/setup_12.x  | bash -
RUN apt-get -y install nodejs npm
RUN npm install yarn --global

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR "/anpad/events"
