#!/bin/bash
# Script Setup Server IDCloudHost (Ubuntu 24.04 LTS)
# Jalankan menggunakan sudo

echo "Memulai setup server untuk Asta Dasa Parwa..."

# 1. Update system
apt update && apt upgrade -y

# 2. Install Nginx & Dependensi
apt install -y nginx curl wget git unzip software-properties-common

# 3. Install PHP 8.2 + Extensions
add-apt-repository -y ppa:ondrej/php
apt update
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
  php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath \
  php8.2-intl php8.2-tokenizer php8.2-cli

# 4. Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# 5. Install Node.js 20 (LTS)
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# 6. Install PM2 (Node.js Process Manager)
npm install -g pm2

# 7. Install MySQL 8.0
apt install -y mysql-server

# 8. Install Certbot (SSL)
apt install -y certbot python3-certbot-nginx

echo "Setup software dasar selesai!"
echo "Selanjutnya, atur MySQL dan upload database backup Anda."
