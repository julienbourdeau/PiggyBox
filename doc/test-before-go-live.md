# Test Before Go Live


## Step 1: Backup

Backup the database
```bash
mysqldump --add-drop-table -u piggyboxer2 -p66ln8^VeOwnLD8 piggybox_prod_v2 > "/root/Dropbox/piggybox/database/"`date +%Y%m%d`_piggyboxprod_v2.sql 2> /root/Dropbox/piggybox/logs/database_error.log
```

Backup the web directory
```bash
tar -zcvf /root/Dropbox/piggybox/web/web_v2.tar --exclude="bundles" /var/www/piggybox-v2/PiggyBox/web/
```

## Step 2: Update the source code

```bash
git pull origin master
```

If necessary update app/config/parameters.yml. 
Simply copy app/config/parameters.yml.dist2 into app/config/parameters.yml

## Step 3: Update Composer and Dependencies

```bash
php composer.phar self-update
```

```bash
php composer.phar update
```

## Step 4: Update the Database Schema

Check if everything seems normal
```bash
php app/console doctrine:schema:update --dump-sql
```

Then update the schema
```bash
php app/console doctrine:schema:update --force
```

## Step 5: Launch Piggybox commands

Generate the product categories:
```bash
php app/console piggybox:generate:htmlcategories
```

If required launch the rest: piggybox:generate:XXX

## Step 6: Install assets
```bash
php app/console assets:install web
```

Check web/.htaccess is redirecting to app.php

## Step 7: Clear cache
```bash
php app/console cache:clear --env=prod
```

Restore file permission
```bash
sudo chmod 777 -R app/cache
```

## Step 8: Check

* http://www.cotelettes-tarteauxfraises.com
* http://www.universail.fr

