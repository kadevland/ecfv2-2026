-- PostgreSQL Initialization Script for Cinéphoria ECF
-- Extensions et configuration pour optimiser pour CQRS write-side

-- Enable UUID generation
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Enable performance monitoring
CREATE EXTENSION IF NOT EXISTS "pg_stat_statements";

-- Enable full-text search (pour recherche films)
CREATE EXTENSION IF NOT EXISTS "unaccent";

-- Enable trigram similarity for fuzzy search (tags, titles)
CREATE EXTENSION IF NOT EXISTS "pg_trgm";

-- Enable array operations for tags/genres
CREATE EXTENSION IF NOT EXISTS "intarray";

-- Configuration optimisée pour CQRS write-side
-- Extensions prêtes pour les migrations Laravel

-- Fonction utilitaire pour nettoyage des logs (utilisée par les migrations)
CREATE OR REPLACE FUNCTION cleanup_old_logs(table_name TEXT, days_to_keep INTEGER DEFAULT 30)
RETURNS void AS $$
BEGIN
    EXECUTE format('DELETE FROM %I WHERE created_at < NOW() - INTERVAL ''%s days''', 
                   table_name, days_to_keep);
END;
$$ LANGUAGE plpgsql;

-- Vue pour statistics rapides
CREATE OR REPLACE VIEW db_stats AS
SELECT 
    schemaname,
    tablename,
    attname,
    n_distinct,
    correlation
FROM pg_stats
WHERE schemaname = 'public'
ORDER BY tablename, attname;

-- Exemple d'index optimisé pour recherche de tags (sera créé par les migrations Laravel)
-- CREATE INDEX CONCURRENTLY idx_movies_tags_gin ON movies USING gin(tags);
-- CREATE INDEX CONCURRENTLY idx_movies_title_trgm ON movies USING gin(title gin_trgm_ops);

-- Configuration pour recherche performante
-- Recherche floue : SELECT * FROM movies WHERE title % 'Avengers' ORDER BY similarity(title, 'Avengers') DESC;
-- Tags array : SELECT * FROM movies WHERE tags @> ARRAY['action', 'sci-fi'];
-- Combiné : SELECT * FROM movies WHERE tags @> ARRAY['action'] AND title % 'super';