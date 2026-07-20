#!/bin/bash

set -e

echo "Starting PostgreSQL + PgBouncer optimized setup..."

# Démarrer PostgreSQL avec paramètres optimisés en background
/usr/local/bin/docker-entrypoint.sh postgres \
  -c shared_preload_libraries=pg_stat_statements \
  -c pg_stat_statements.track=all \
  -c max_connections=200 \
  -c shared_buffers=256MB \
  -c effective_cache_size=1GB \
  -c maintenance_work_mem=64MB \
  -c checkpoint_completion_target=0.9 \
  -c wal_buffers=16MB \
  -c default_statistics_target=100 \
  -c random_page_cost=1.1 \
  -c effective_io_concurrency=200 &

# Attendre que PostgreSQL soit disponible
until pg_isready -h 127.0.0.1 -p 5432 -U "${POSTGRES_USER}" -d "${POSTGRES_DB}" -q; do
  sleep 1
done

echo "PostgreSQL ready - starting PgBouncer with Unix socket..."

# PgBouncer au premier plan (devient processus principal Docker)
exec su - postgres -c "/usr/bin/pgbouncer /etc/pgbouncer/pgbouncer.ini"