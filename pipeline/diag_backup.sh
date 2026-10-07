# Read-only diagnostic: why did mysqldump produce an empty backup?
DB=$(sudo docker ps --format "{{.Names}}" </dev/null | grep -i -E "db|mysql" | grep -v -i phpmyadmin | head -1)
echo "--- small backup file content:"; zcat "$HOME"/crimewiki-backups/posts-before-*.sql.gz | head -12 | cut -c1-200
echo "--- mysqldump stderr (dump discarded):"
sudo docker exec "$DB" sh -c 'mysqldump --single-transaction --no-tablespaces --default-character-set=utf8mb4 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" posts 2>&1 >/dev/null | grep -v "Using a password" | head -5' </dev/null
echo "--- which:"; sudo docker exec "$DB" sh -c 'which mysqldump mysql; mysqldump --version' </dev/null
echo "--- grants:"; sudo docker exec "$DB" sh -c 'mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -N -e "show grants" 2>/dev/null' </dev/null | sed -E "s/IDENTIFIED.*//" | cut -c1-200
echo "--- disk:"; df -h "$HOME" | tail -1
