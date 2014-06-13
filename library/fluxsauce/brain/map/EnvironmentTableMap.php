<?php

namespace Fluxsauce\Brain\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'environment' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.brain.map
 */
class EnvironmentTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'brain.map.EnvironmentTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('environment');
        $this->setPhpName('Environment');
        $this->setClassname('Fluxsauce\\Brain\\Environment');
        $this->setPackage('brain');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('siteId', 'Siteid', 'INTEGER', false, null, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 255, null);
        $this->addColumn('host', 'Host', 'VARCHAR', false, 255, null);
        $this->addColumn('username', 'Username', 'VARCHAR', false, 255, null);
        $this->addColumn('branch', 'Branch', 'VARCHAR', false, 255, null);
        $this->addColumn('updated', 'Updated', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // EnvironmentTableMap
