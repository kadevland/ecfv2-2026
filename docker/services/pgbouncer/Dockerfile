FROM alpine:3.20

# Installation de PgBouncer et création utilisateur
RUN apk add --no-cache \
    pgbouncer \
    && adduser -D -s /bin/sh pgbouncer \
    && mkdir -p /etc/pgbouncer \
    && chown -R pgbouncer:pgbouncer /etc/pgbouncer

# Script de génération dynamique de l'utilisateur
COPY start-pgbouncer.sh /usr/local/bin/start-pgbouncer.sh
COPY pgbouncer.ini /etc/pgbouncer/pgbouncer.ini

# Permissions
RUN chmod +x /usr/local/bin/start-pgbouncer.sh \
    && chown pgbouncer:pgbouncer /etc/pgbouncer/pgbouncer.ini

# Utilisateur pgbouncer
USER pgbouncer

# Port PgBouncer
EXPOSE 6432

# Démarrage avec génération dynamique
CMD ["/usr/local/bin/start-pgbouncer.sh"]
