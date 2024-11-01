<?php

/**
 * Dictionary model class.
 *
 * This class defines a model for accessing the custom dictionary table.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/includes/models
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_Dictionary extends Supasearch_Base_Model {
    /**
     * The unique identifier of the model.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $table_name The string used to uniquely identify the model.
     */
    protected static $table_name = 'dictionary';

    /**
     * The unique identifier of the tables primary key.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $primary_key The string used to uniquely identify the tables primary key.
     */
    protected static $primary_key = 'word';

    /**
     * Defines the data format for each column being inserted.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $column_formats The formats for the columns being inserted.
     */
    protected static $column_formats = array( '%s', '%d', '%d', '%d' );
}