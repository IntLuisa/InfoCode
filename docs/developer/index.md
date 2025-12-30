# Developer

## Main Topics

- [Dependencies](#dependencies)
- [Install](#install)
    - [Clone Project](#clone-project)
    - [Smart Start](#smart-start)
- [Run Project](#run-project)
    - [Without Docker](#without-docker)
    - [With Docker](#with-docker)
    - [Mysql with Docker](#mysql-with-docker)
- [Build Project](#build-project)
    - [Development](#development)
    - [Production](#production)
- [Database Production](#database-production)
- [CloudFlare](#cloudflare)
    - [Log in](#log-in)
    - [Purge Cache](#purge-cache)
- [Server](#server)
    - [Change Group](#change-group)
- [Add New User](#add-new-user)
    - [Cpanel](#cpanel)
    - [Office System](#office-system)
    - [Ponto Liberação](#ponto-liberação)
    - [Address (New Office)](#address-(new-office))
    - [Create Password](#create-password)
- [Deploy New Project](#deploy-new-project)
    - [Create Subdomain](#create-subdomain)
    - [Create Database](#create-database)
    - [User FTP](#user-ftp)
    - [Cpanel](#cpanel-1)
        - [Create Database](#create-database)
    - [User FTP](#user-ftp)
        - [Create User](#create-user)
        - [Change Permissions User](#change-permissions-user)
    - [Configure Apache2](#configure-apache2)
        - [Add Subdomain](#add-subdomain)
        - [Enable Subdomain](#enable-subdomain)
        - [Test Apache2 Configuration](#test-apache2-configuration)
        - [Reload Apache2](#reload-apache2)
    - [CloudFlare](#cloudflare-1)
        - [Add DNS CloudFlare](#add-dns-cloudflare)
    - [Project](#project)
        - [Git Workflow File](#git-workflow-file)
        - [Github add Secrets](#github-add-secrets)
        - [Up Production](#up-production)
    - [Server](#server-1)
        - [Install Dependencies](#install-dependencies)
        - [Configure Environment](#configure-environment)
        - [Build Project](#build-project-1)

---

## Dependencies

- Mysql (Required)
- Docker (Optional)

## Install

### Clone Project

```sh
git clone git@github.com:SystemSaude/office.git office \ncd office
```

### Smart Start

Run below command and follow the instructions.

```sh
php smart start
```

---

## Run Project

### Without Docker

```sh
php smart serve
```

---

### With Docker

```sh
docker-compose up -d
```

---

## Mysql with Docker

Run into terminal

```sh
NETWORK_NAME=$(grep DOCKER_NETWORK .env | cut -d '=' -f 2 | xargs)
DATABASE_NAME=$(grep DB_DATABASE .env | cut -d '=' -f 2 | xargs)
DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f 2 | xargs)
docker run -d --name mysql -e MYSQL_ROOT_PASSWORD=${DB_PASSWORD} -e MYSQL_DATABASE=${DATABASE_NAME} \n-v "$(pwd)"/database/schema.sql:/docker-entrypoint-initdb.d/schema.sql \n-v "$(pwd)"/database/seeder.sql:/docker-entrypoint-initdb.d/seeder.sql \n--network=${NETWORK_NAME} \n-p 3306:3306 mysql:latest
```

---

## Build Project

### .htaccess

Create file `.htaccess` and into public_html folder add below content

```sh
RewriteEngine On
RewriteRule ^$ public/ [L]
RewriteRule (.*) public/$1 [L]
```

### Artisan

```sh
 /opt/alt/php82/usr/bin/php artisan key:generate
 /opt/alt/php82/usr/bin/php artisan migrate
 ```

### Development

```sh
npm run serve
```

### Production

```sh
npm run build
```

## Database Production

1. Access database link [here](http://167.99.11.213/phpmyadmin)
2. Type database user. Ex.: root
3. Type database password. Ex.: AVNS_******

## CloudFlare

### Log in

1. Access [CloudFlare](http://cloudflare.com)
2. Click in `Log in`
3. Type email `systemsaude@hotmail.com`
4. Type password. Ex.: `@senha*****`

### Purge Cache

1. [Log in](#log-in)
2. Click in `systemsaude.com.br`
3. Click in `Purge Cache`
4. Click in `Purge Everything` and again in `Purge Everything` 

---

## Server

### Change Group

```sh
chown www-data:www-data production -R
```

--- 

## Add New User

### Cpanel

- Create Professional E-mail (Cpanel) - [Cpanel](https://cpanel.systemsaude.com.br/unprotected/redirect.html?goto_uri=)
- Create User into table **user** (phpMyAdmin)
- Create Office Bank Account into table **fin_contas** (phpMyAdmin)
- Create Personal Bank Account into table **fin_contas** (phpMyAdmin)
- If new Office -> insert data into table **end_escritorios** (phpMyAdmin)

### Office System

- Insert into *UserInfos* - [Here](../../resourses/constants/UserInfos.php)
- Insert into *config* -> **CONF** - [Here](../../public/js/config.js)
- Insert into *office.constants* -> **OFFICES** - [Here](../../public/constants/office.constants.js)
- Insert into *finance.constants* -> **USER_ACCOUNTS** - [Here](../../public/modules/finance/finance.constants.js)
- Insert into *caixasEscritorios* -> **$CAIXAS** and **switch** - [Here](../../app/Http/Cronjobs/caixasEscritorios.php)

### Ponto Liberação

- Insert into /recebimento/new.txt

### Address (New Office)

- Site
- Google Maps (My Business)

### Create Password

- Send Recover e-mail by Login Page

## Deploy New Project

### Create Subdomain

- Go to [Hostinger](https://hpanel.hostinger.com)
- Click in `Sites` > `Lista de sites`
- Click in `Adicionar site`
- Select `Site PHP/Vazio`
- Type `<subdomain>.devsmt.com`

### Create Database

- Click in `Sites` > `Lista de sites`
- Click in `Painel de controle`

### User FTP

- Click in `Sites` > `Lista de sites`
- Click in `Painel de controle`
- Click in `Arquivos` > `Contas FTP`
- Create new FTP
- Add to `GitHub`

### Configure Server

- Access server by ssh
- Into `.env` configure `database credentials` and `email credentials`
- Download Composer `/opt/alt/php82/usr/bin/php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"`
- Install Composer `/opt/alt/php82/usr/bin/php composer-setup.php --filename=composer`
- Remove Composer `/opt/alt/php82/usr/bin/php -r "unlink('composer-setup.php');"`
- Install Dependencies `/opt/alt/php82/usr/bin/php composer install --no-dev --optimize-autoloader`
- Generate Key `/opt/alt/php82/usr/bin/php artisan key:generate`
- Migrate `/opt/alt/php82/usr/bin/php artisan migrate`
- Add .htaccess 

```sh
RewriteEngine On
RewriteRule ^$ public/ [L]
RewriteRule (.*) public/$1 [L]
```

#### Linux

##### Create User
adduser <user_name>

##### Change Permissions User

```sh
chown <user_name>:<user_name> /var/www/system/production/<project_name>
```

### Configure Apache2

#### Add Subdomain

- Go to `/etc/apache2/sites-available/`
- Create `<subdomain>.systemsaude.com.br.conf` and add below content

```sh
<VirtualHost *:80>
    ServerName <subdomain>.systemsaude.com.br

    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/system/production/<project_name>/public

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

#### Enable Subdomain

```sh
sudo a2ensite <subdomain>.systemsaude.com.br.config
```

#### Test Apache2 Configuration

```sh
apachectl configtest
```

#### Reload Apache2

```sh
systemctl reload apache2
```

### CloudFlare

#### Add DNS CloudFlare

- Go to [CloudFlare](#links)
- Select domain
- Go to `DNS Records`
- Add new `CNAME` record

```sh
CNAME <subdomain> <domain or IP>
```

### Project

#### Git Workflow File

- Go to [Workflow Folder](../../.github/workflows)
- Create new file `<branch-name>.yml`
- Add below content

```yaml
name: 🚀 Deploy Church
on:
  push:
    branches:
      - main
jobs:
  web-deploy:
    name: 🎉 Deploy
    runs-on: ubuntu-latest
    steps:
      - name: 🚚 Get latest code
        uses: actions/checkout@v3

      - name: Set up PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          extensions: composer

      - name: Install PHP dependencies
        run: composer install --no-dev --prefer-dist --optimize-autoloader --ignore-platform-reqs

      - name: Set up Node.js
        uses: actions/setup-node@v3
        with:
          node-version: 22

      - name: Install Node.js dependencies
        run: npm install

      - name: Build assets
        run: npm run build

      - name: 📂 Sync files
        uses: SamKirkland/FTP-Deploy-Action@4.3.2
        with:
          server: ${{ secrets.HOSTINGER_KRENKE_FTP_SERVER }}
          username: ${{ secrets.HOSTINGER_KRENKE_FTP_USER }}
          password: ${{ secrets.HOSTINGER_KRENKE_FTP_PASSWORD }}
          server-dir: /
          exclude: |
            **/.git*
            **/.git*/**
            **/node_modules/**
            **/vendor/**
```

#### Github add Secrets

- Go to Github Repository
- Go to Settings
- Go to Secrets
- Add new Secrets

#### Up Production

- Commit changes
- Push changes

### Server

#### Install Dependencies

- Change php version to 8.1

```sh
composer update --ignore-platform-reqs
composer install --no-dev --optimize-autoloader && npm install
```

#### Configure Environment

- Open [.env](../../.env)
- Add database credentials

#### Build Project

```sh
npm run build
```

[Back](../index.MD)
