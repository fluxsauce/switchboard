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
        $this->addForeignKey('siteId', 'Siteid', 'INTEGER', 'site', 'id', false, null, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 255, null);
        $this->addColumn('host', 'Host', 'VARCHAR', false, 255, null);
        $this->addColumn('username', 'Username', 'VARCHAR', false, 255, null);
        $this->addColumn('branch', 'Branch', 'VARCHAR', false, 255, null);
        $this->addColumn('createdOn', 'Createdon', 'TIMESTAMP', false, null, null);
        $this->addColumn('updatedOn', 'Updatedon', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Site', 'Fluxsauce\\Brain\\Site', RelationMap::MANY_TO_ONE, array('siteId' => 'id', ), null, null);
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'timestampable' =>  array (
  'create_column' => 'createdOn',
  'update_column' => 'updatedOn',
  'disable_updated_at' => 'false',
),
        );
    } // getBehaviors()

} // EnvironmentTableMap
