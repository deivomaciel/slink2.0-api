# Use uma imagem base do PHP com Apache
FROM php:8.0-apache

# Instale as dependências necessárias
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Copie os arquivos do projeto para o contêiner
COPY . /var/www/html

# Exponha a porta 80
EXPOSE 80

# Comando padrão do contêiner
CMD ["apache2-foreground"]
