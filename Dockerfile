FROM php:8.2-fpm

# Gerekli sistem bağımlılıklarını yükleme
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    curl \
    gnupg \
    nginx \
    && docker-php-ext-install pdo_pgsql zip

# Node.js 18.x ve npm Kurulumu
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Yarn'ı Kurma
RUN npm install -g yarn

# Composer'ı indirme ve kurma
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Symfony CLI'yi kurma
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Çalışma dizinini ayarlama
WORKDIR /var/www/pm

# Proje dosyalarını kopyalama
COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1
# Proje bağımlılıklarını kurma
RUN composer install --no-interaction

# Yarn bağımlılıklarını kurma
RUN yarn install

# Nginx yapılandırmasını kopyalama
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Gerekli portları açma
EXPOSE 80

# PHP-FPM ve Nginx'i başlatma
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
