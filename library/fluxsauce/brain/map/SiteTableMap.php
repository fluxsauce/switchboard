<?php

namespace Fluxsauce\Brain\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'site' table.
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
class SiteTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'brain.map.SiteTableMap';

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
        $this->setName('site');
        $this->setPhpName('Site');
        $this->setClassname('Fluxsauce\\Brain\\Site');
        $this->setPackage('brain');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('provider', 'Provider', 'VARCHAR', false, 255, null);
        $this->addColumn('uuid', 'Uuid', 'VARCHAR', false, 255, null);
        $this->addColumn('realm', 'Realm', 'VARCHAR', false, 255, null);
        $this->addColumn('name', 'Name', 'VARCHAR', false, 255, null);
        $this->addColumn('title', 'Title', 'VARCHAR', false, 255, null);
        $this->addColumn('vcsUrl', 'Vcsurl', 'VARCHAR', false, 255, null);
        $this->addColumn('vcsType', 'Vcstype', 'VARCHAR', false, 255, null);
        $this->addColumn('vcsProtocol', 'Vcsprotocol', 'VARCHAR', false, 255, null);
        $this->addColumn('sshPort', 'Sshport', 'INTEGER', false, null, null);
        $this->addColumn('createdOn', 'Createdon', 'TIMESTAMP', false, null, null);
        $this->addColumn('updatedOn', 'Updatedon', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
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

} // SiteTableMap
