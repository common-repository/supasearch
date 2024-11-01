<?php

/**
 * Log model class.
 *
 * This class defines a model for tracking searches on the site.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/includes/models
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_Log extends Supasearch_Base_Model {
    /**
     * The unique identifier of the model.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $table_name The string used to uniquely identify the model.
     */
    protected static $table_name = 'log';

    /**
     * The unique identifier of the tables primary key.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $primary_key The string used to uniquely identify the tables primary key.
     */
    protected static $primary_key = 'identifier';

    /**
     * Defines the data format for each column being inserted.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $column_formats The formats for the columns being inserted.
     */
    protected static $column_formats = array( '%s', '%s', '%d', '%d' );

    /**
     * Static function which inserts a new row and updates on duplicate key but allow operations e.g. for making
     * incremental updates.
     *
     * @since    0.2.0
     *
     * @param    array $search The search query to save.
     * @param    array $update The update condition of the query.
     *
     * @return   int           The number of rows updated.
     */
    public static function insert_on_duplicate_key( $search, $update ) {
        return parent::insert_on_duplicate_key( array(
            'identifier'  => md5( $search ),
            'search'      => $search,
            'count'       => 1,
            'has_results' => 0
        ), $update );
    }

}