# Process Backup


## Piggybox

### Database
```bash
mysqldump --add-drop-table -u piggyboxer2 -p66ln8^VeOwnLD8 piggybox_prod_v2 > "/root/Dropbox/piggybox/database/"`date +%Y%m%d`_piggyboxprod_v2.sql 2> /root/Dropbox/piggybox/logs/database_error.log
```

### Files
```bash
tar -zcvf /root/Dropbox/piggybox/web/web_v2.tar --exclude="bundles" /var/www/piggybox-v2/PiggyBox/web/
```

## Universail

### Database
```bash
mysqldump --add-drop-table -u universaildb -p02T*xsJYz1YI universailapp > "/root/Dropbox/universail/database/"`date +%Y%m%d`_universailapp.sql 2> /root/Dropbox/universail/logs/database_error_app.log
```

```bash
mysqldump --add-drop-table -u uni_blogger -p303o6!^3GVl6 universail_blog > "/root/Dropbox/universail/database/"`date +%Y%m%d`_universailblog.sql 2> /root/Dropbox/universail/logs/database_error_blog.log
```

### Files
```bash
tar -zcvf /root/Dropbox/universail/www.tar /var/www/universail/www
```

## Bouger En Famille

to do…


## All Databases
```bash
mysqldump --add-drop-table -u root -p46Q%W^4*j63oK3 --all-databases > "/root/Dropbox/root/"`date +%Y%m%d`_ALL_DATABASES.sql 2> /root/Dropbox/root/logs/all_databases_error.log
```

