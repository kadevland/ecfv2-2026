#!/bin/sh
set -e

#echo "=== Generating PgBouncer userlist.txt from environment variables ==="

# Generate MD5 hash for PgBouncer: md5(password + username)
# PG_MD5_HASH=$(echo -n "${POSTGRES_PASSWORD}${POSTGRES_USER}" | md5sum | cut -d' ' -f1)
# PGBOUNCER_PASSWORD_HASH="md5${PG_MD5_HASH}"
# cat > /etc/pgbouncer/userlist.txt << EOF
# "${POSTGRES_USER}" "${PGBOUNCER_PASSWORD_HASH}"
# EOF

# For SCRAM-SHA-256, PgBouncer can use plain text passwords
# Generate userlist.txt dynamically
cat > /etc/pgbouncer/userlist.txt << EOF
"${POSTGRES_USER}" "${POSTGRES_PASSWORD}"
EOF



#echo "Generated userlist.txt:"
#cat /etc/pgbouncer/userlist.txt

#echo "=== Starting PgBouncer ==="
exec pgbouncer /etc/pgbouncer/pgbouncer.ini
