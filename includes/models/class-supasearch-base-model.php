<?php

/**
 * Base model class.
 *
 * This class defines all functions and properties to be shared with all model classes.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/includes/models
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
abstract class Supasearch_Base_Model {
    /**
     * The unique identifier of the model.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $table_name The string used to uniquely identify the model.
     */
    private static $table_name = '';

    /**
     * The unique identifier of the tables primary key.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $primary_key The string used to uniquely identify the tables primary key.
     */
    private static $primary_key = '';

    /**
     * Defines the data format for each column being inserted.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $column_formats The format for the columns being inserted.
     */
    private static $column_formats = null;

    /**
     * Static function which returns the name of the table.
     *
     * @since    0.1.0
     *
     * @return   string       The name of the table.
     */
    public static function get_table_name() {
        global $wpdb;

        return $wpdb->prefix . str_replace( '-', '_', Supasearch::get_plugin_name() ) . '_' . static::$table_name;
    }

    /**
     * Static function which returns the primary key of the table.
     *
     * @since    0.1.0
     *
     * @return   string       The primary key of the table.
     */
    private static function get_primary_key() {
        return static::$primary_key;
    }

    /**
     * Static function which returns the column formats of the table.
     *
     * @since    0.1.0
     *
     * @return   string       The column formats of the table.
     */
    private static function get_column_formats() {
        return static::$column_formats;
    }

    /**
     * Static function which returns all table rows from the table.
     *
     * @since    0.1.0
     *
     * @param    string $output The output type of the row (OBJECT, ARRAY_A, ARRAY_N).
     *
     * @return   array          The array of results from the search query.
     */
    public static function get_all( $output = OBJECT ) {
        return self::get( null, null, $output );
    }

    /**
     * Static function which returns table rows from a queried value.
     *
     * @since    0.1.0
     *
     * @param    string $value  The value to search for.
     * @param    string $where  The conditional parameters of the query.
     * @param    string $output The output type of the row (OBJECT, ARRAY_A, ARRAY_N).
     *
     * @return   array|object   The array of results from the search query.
     */
    public static function get( $value, $where = '%s = %%s', $output = OBJECT ) {
        global $wpdb;

        $where = $where !== null ? ' WHERE ' . $where : '';
        $sql = sprintf( 'SELECT * FROM %s' . $where, self::get_table_name(), self::get_primary_key() );
        $results = $wpdb->get_results( $wpdb->prepare( $sql, $value ), $output );

        return $wpdb->num_rows === 1 ? $results[0] : $results;
    }

    /**
     * Static function which inserts a new row into the table.
     *
     * @since    0.1.0
     *
     * @param    array $data The data values to be inserted into the table.
     */
    public static function insert( $data ) {
        global $wpdb;

        $wpdb->insert( self::get_table_name(), $data, self::get_column_formats() );
    }

    /**
     * Static function which inserts a new row and updates on duplicate key.
     *
     * @since    0.1.0
     *
     * @param    array $data The data values to be inserted or updated into the table.
     */
    public static function replace( $data ) {
        global $wpdb;

        $wpdb->replace( self::get_table_name(), $data, self::get_column_formats() );
    }

    /**
     * Static function which inserts a new row and updates on duplicate key but allow operations e.g. for making
     * incremental updates.
     *
     * @since    0.1.0
     *
     * @param    array $data   The data values to be inserted or updated into the table.
     * @param    array $update The update condition of the query.
     *
     * @return   int           The number of rows updated.
     */
    public static function insert_on_duplicate_key( $data, $update ) {
        global $wpdb;

        $sql = sprintf( 'INSERT INTO %s VALUES (%' . implode( ', %', self::get_column_formats() ) . ') ON DUPLICATE KEY UPDATE ' . $update, self::get_table_name() );

        return $wpdb->query( $wpdb->prepare( $sql, $data ) );
    }

    /**
     * Static function which updates rows in the table.
     *
     * @since    0.1.0
     *
     * @param    array $data  The data values to be updated on the table.
     * @param    array $where The conditions on which to apply the update.
     */
    public static function update( $data, $where ) {
        global $wpdb;

        $wpdb->update( self::get_table_name(), $data, $where );
    }

    /**
     * Static function which copies values from one column to another.
     *
     * @since    0.1.0
     *
     * @param    array $to   The column to copy values to.
     * @param    array $from The column to copy values from.
     */
    public static function copy_column( $to, $from ) {
        global $wpdb;

        $wpdb->query( sprintf( 'UPDATE %s SET %s = %s', self::get_table_name(), $to, $from ) );
    }

    /**
     * Static function which deletes rows from the table.
     *
     * @since    0.1.0
     *
     * @param    string $value The value to deleted on.
     *
     * @return   int           The number of rows deleted.
     */
    public static function delete( $value ) {
        global $wpdb;

        $sql = sprintf( 'DELETE FROM %s WHERE %s = %%s', self::get_table_name(), self::get_primary_key() );

        return $wpdb->query( $wpdb->prepare( $sql, $value ) );
    }

    /**
     * Static function which truncates a table.
     *
     * @since    0.1.0
     */
    public static function truncate() {
        global $wpdb;

        return $wpdb->query( sprintf( 'TRUNCATE %s', self::get_table_name() ) );
    }

    /**
     * Static function which drops a table.
     *
     * @since    0.1.0
     */
    public static function drop() {
        global $wpdb;

        return $wpdb->query( sprintf( 'DROP TABLE IF EXISTS %s', self::get_table_name() ) );
    }

    /**
     * Static function which returns the id for the last inserted row.
     *
     * @since    0.1.0
     *
     * @return   int       The ID for the last inserted row.
     */
    public static function insert_id() {
        global $wpdb;

        return $wpdb->insert_id;
    }

    /**
     * Static function which converts a timestamp to a date string in GMT.
     *
     * @since    0.1.0
     *
     * @param    string $time The timestamp to be converted to a date.
     *
     * @return   string       The converted time.
     */
    public static function time_to_date( $time ) {
        return gmdate( 'Y-m-d H:i:s', $time );
    }

    /**
     * Static function which returns the current date / time.
     *
     * @since    0.1.0
     *
     * @return   string       The current date / time.
     */
    public static function now() {
        return self::time_to_date( time() );
    }

    /**
     * Static function which converts a date to a timestamp in GMT.
     *
     * @since    0.1.0
     *
     * @param    string $date The date to be converted.
     *
     * @return   string       The converted time.
     */
    public static function date_to_time( $date ) {
        return strtotime( $date . ' GMT' );
    }
}