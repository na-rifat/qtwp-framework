<?php

namespace qtwp\lib;

defined('ABSPATH') or exit;

class DB
{
    public static function prefix($table_name = '')
    {
        return self::instance()->prefix . $table_name;
    }

    public static function charset_collate()
    {
        return self::instance()->charset_collate;
    }

    public function instance()
    {
        global $wpdb;

        return $wpdb;
    }

    public static function create()
    {

    }

    public static function retrieve($table_name, $id = null, $indentity_col = 'id')
    {
        $table_name = self::prefix($table_name);

        if ($id != null) {
            return self::instance()->get_results(
                self::instance()->prepare(
                    sprintf("SELECT * FROM $table_name WHERE $indentity_col=%s", $id)
                )
            );
        }

        return self::instance()->get_results(
            "SELECT * FROM $table_name"
        );
    }

    public static function update()
    {

    }

    public static function delete($table_name, $id, $id_col_name = 'id')
    {
        $table_name = self::prefix($table_name);

        return self::instance()->delete(
            $table_name,
            [$id_col_name => $id]
        );
    }
}
