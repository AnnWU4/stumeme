FROM php:7.4-apache

# 安装 PHP 扩展和系统库
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd mbstring

# 设置工作目录
WORKDIR /www

# 拷贝代码到容器中
COPY . /www

# 安装 Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 安装 PHP 依赖
RUN composer install --no-dev --optimize-autoloader

# 公开端口
EXPOSE 80
