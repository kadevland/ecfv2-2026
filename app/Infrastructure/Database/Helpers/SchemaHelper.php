<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Helpers;

use BackedEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Connection;

final class SchemaHelper
{
    private Connection $db;

    public function __construct(?Connection $connection = null)
    {
        $this->db = $connection ?? DB::connection();
    }

    /**
     * Add CHECK constraint for enum values
     *
     * @param string $tableName Full table name (schema.table)
     * @param string $columnName Column name
     * @param array<BackedEnum> $enumCases Enum cases
     * @param string $constraintName Constraint name
     */
    public function addEnumCheckConstraint(
        string $tableName,
        string $columnName,
        array $enumCases,
        string $constraintName
    ): void {
        $values     = array_map(fn ($case) => $case->value, $enumCases);
        $valuesList = "'" . implode("','", $values) . "'";

        $sql = "ALTER TABLE {$tableName} " .
               "ADD CONSTRAINT {$constraintName} " .
               "CHECK ({$columnName} IN ({$valuesList}))";

        $this->db->statement($sql);
    }

    /**
     * Create index with custom name
     *
     * @param string $tableName Full table name (schema.table)
     * @param string $columnName Column name
     * @param string $indexName Index name
     */
    public function addIndex(
        string $tableName,
        string $columnName,
        string $indexName
    ): void {
        $this->db->statement("CREATE INDEX {$indexName} ON {$tableName} ({$columnName})");
    }

    /**
     * Add multiple indexes at once
     *
     * @param string $tableName Full table name (schema.table)
     * @param array<string, string> $indexes ['column_name' => 'index_name']
     */
    public function addIndexes(string $tableName, array $indexes): void
    {
        foreach ($indexes as $columnName => $indexName) {
            $this->addIndex($tableName, $columnName, $indexName);
        }
    }

    /**
     * Crée un schéma PostgreSQL s'il n'existe pas
     */
    public function createSchemaIfNotExists(string $schema): void
    {
        $this->db->statement("CREATE SCHEMA IF NOT EXISTS {$schema}");
    }

    /**
     * Supprime un schéma PostgreSQL s'il existe (CASCADE)
     */
    public function dropSchemaIfExists(string $schema): void
    {
        $this->db->statement("DROP SCHEMA IF EXISTS {$schema} CASCADE");
    }

    /**
     * Ajoute un index GIN sur une colonne JSONB
     */
    public function addGinIndex(string $tableName, string $column, ?string $indexName = null): void
    {
        $indexName = $indexName ?: "{$tableName}_{$column}_gin_idx";
        $this->db->statement("CREATE INDEX {$indexName} ON {$tableName} USING gin ({$column})");
    }

    /**
     * Ajoute un commentaire sur une table
     */
    public function addTableComment(string $table, string $comment): void
    {
        $this->db->statement("COMMENT ON TABLE {$table} IS '{$comment}'");
    }

    /**
     * Ajoute un index sur un champ spécifique d'une colonne JSONB
     *
     * @param string $tableName Nom complet table (schema.table)
     * @param string $column Nom colonne JSONB
     * @param string $field Champ JSON à indexer
     * @param string|null $indexName Nom index (optionnel)
     */
    public function addJsonbFieldIndex(
        string $tableName,
        string $column,
        string $field,
        ?string $indexName = null
    ): void {
        $indexName = $indexName ?: str_replace('.', '_', $tableName) . "_{$column}_{$field}_idx";
        $this->db->statement("CREATE INDEX {$indexName} ON {$tableName} (({$column}->>'{$field}'))");
    }

    /**
     * Ajoute une contrainte de clé étrangère
     */
    public function addForeignKey(
        string $table,
        string $column,
        string $referencedTable,
        string $referencedColumn,
        string $constraintName,
        string $onDelete = 'CASCADE'
    ): void {
        $sql = "ALTER TABLE {$table} " .
               "ADD CONSTRAINT {$constraintName} " .
               "FOREIGN KEY ({$column}) " .
               "REFERENCES {$referencedTable} ({$referencedColumn}) " .
               "ON DELETE {$onDelete}";

        $this->db->statement($sql);
    }
}
