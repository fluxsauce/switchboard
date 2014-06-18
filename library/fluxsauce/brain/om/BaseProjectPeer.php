<?php

namespace Fluxsauce\Brain\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use Fluxsauce\Brain\Project;
use Fluxsauce\Brain\ProjectPeer;
use Fluxsauce\Brain\SitePeer;
use Fluxsauce\Brain\map\ProjectTableMap;

/**
 * Base static class for performing query and update operations on the 'project' table.
 *
 *
 *
 * @package propel.generator.brain.om
 */
abstract class BaseProjectPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'brain';

    /** the table name for this class */
    const TABLE_NAME = 'project';

    /** the related Propel class for this table */
    const OM_CLASS = 'Fluxsauce\\Brain\\Project';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Fluxsauce\\Brain\\map\\ProjectTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 16;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 16;

    /** the column name for the id field */
    const ID = 'project.id';

    /** the column name for the name field */
    const NAME = 'project.name';

    /** the column name for the uuid field */
    const UUID = 'project.uuid';

    /** the column name for the siteId field */
    const SITEID = 'project.siteId';

    /** the column name for the hostname field */
    const HOSTNAME = 'project.hostname';

    /** the column name for the username field */
    const USERNAME = 'project.username';

    /** the column name for the sshPort field */
    const SSHPORT = 'project.sshPort';

    /** the column name for the codePath field */
    const CODEPATH = 'project.codePath';

    /** the column name for the filesPath field */
    const FILESPATH = 'project.filesPath';

    /** the column name for the databaseHost field */
    const DATABASEHOST = 'project.databaseHost';

    /** the column name for the databaseUsername field */
    const DATABASEUSERNAME = 'project.databaseUsername';

    /** the column name for the databasePassword field */
    const DATABASEPASSWORD = 'project.databasePassword';

    /** the column name for the databaseName field */
    const DATABASENAME = 'project.databaseName';

    /** the column name for the databasePort field */
    const DATABASEPORT = 'project.databasePort';

    /** the column name for the createdOn field */
    const CREATEDON = 'project.createdOn';

    /** the column name for the updatedOn field */
    const UPDATEDON = 'project.updatedOn';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of Project objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Project[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. ProjectPeer::$fieldNames[ProjectPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'Name', 'Uuid', 'Siteid', 'Hostname', 'Username', 'Sshport', 'Codepath', 'Filespath', 'Databasehost', 'Databaseusername', 'Databasepassword', 'Databasename', 'Databaseport', 'Createdon', 'Updatedon', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'name', 'uuid', 'siteid', 'hostname', 'username', 'sshport', 'codepath', 'filespath', 'databasehost', 'databaseusername', 'databasepassword', 'databasename', 'databaseport', 'createdon', 'updatedon', ),
        BasePeer::TYPE_COLNAME => array (ProjectPeer::ID, ProjectPeer::NAME, ProjectPeer::UUID, ProjectPeer::SITEID, ProjectPeer::HOSTNAME, ProjectPeer::USERNAME, ProjectPeer::SSHPORT, ProjectPeer::CODEPATH, ProjectPeer::FILESPATH, ProjectPeer::DATABASEHOST, ProjectPeer::DATABASEUSERNAME, ProjectPeer::DATABASEPASSWORD, ProjectPeer::DATABASENAME, ProjectPeer::DATABASEPORT, ProjectPeer::CREATEDON, ProjectPeer::UPDATEDON, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'NAME', 'UUID', 'SITEID', 'HOSTNAME', 'USERNAME', 'SSHPORT', 'CODEPATH', 'FILESPATH', 'DATABASEHOST', 'DATABASEUSERNAME', 'DATABASEPASSWORD', 'DATABASENAME', 'DATABASEPORT', 'CREATEDON', 'UPDATEDON', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'name', 'uuid', 'siteId', 'hostname', 'username', 'sshPort', 'codePath', 'filesPath', 'databaseHost', 'databaseUsername', 'databasePassword', 'databaseName', 'databasePort', 'createdOn', 'updatedOn', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. ProjectPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Name' => 1, 'Uuid' => 2, 'Siteid' => 3, 'Hostname' => 4, 'Username' => 5, 'Sshport' => 6, 'Codepath' => 7, 'Filespath' => 8, 'Databasehost' => 9, 'Databaseusername' => 10, 'Databasepassword' => 11, 'Databasename' => 12, 'Databaseport' => 13, 'Createdon' => 14, 'Updatedon' => 15, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'name' => 1, 'uuid' => 2, 'siteid' => 3, 'hostname' => 4, 'username' => 5, 'sshport' => 6, 'codepath' => 7, 'filespath' => 8, 'databasehost' => 9, 'databaseusername' => 10, 'databasepassword' => 11, 'databasename' => 12, 'databaseport' => 13, 'createdon' => 14, 'updatedon' => 15, ),
        BasePeer::TYPE_COLNAME => array (ProjectPeer::ID => 0, ProjectPeer::NAME => 1, ProjectPeer::UUID => 2, ProjectPeer::SITEID => 3, ProjectPeer::HOSTNAME => 4, ProjectPeer::USERNAME => 5, ProjectPeer::SSHPORT => 6, ProjectPeer::CODEPATH => 7, ProjectPeer::FILESPATH => 8, ProjectPeer::DATABASEHOST => 9, ProjectPeer::DATABASEUSERNAME => 10, ProjectPeer::DATABASEPASSWORD => 11, ProjectPeer::DATABASENAME => 12, ProjectPeer::DATABASEPORT => 13, ProjectPeer::CREATEDON => 14, ProjectPeer::UPDATEDON => 15, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'NAME' => 1, 'UUID' => 2, 'SITEID' => 3, 'HOSTNAME' => 4, 'USERNAME' => 5, 'SSHPORT' => 6, 'CODEPATH' => 7, 'FILESPATH' => 8, 'DATABASEHOST' => 9, 'DATABASEUSERNAME' => 10, 'DATABASEPASSWORD' => 11, 'DATABASENAME' => 12, 'DATABASEPORT' => 13, 'CREATEDON' => 14, 'UPDATEDON' => 15, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'name' => 1, 'uuid' => 2, 'siteId' => 3, 'hostname' => 4, 'username' => 5, 'sshPort' => 6, 'codePath' => 7, 'filesPath' => 8, 'databaseHost' => 9, 'databaseUsername' => 10, 'databasePassword' => 11, 'databaseName' => 12, 'databasePort' => 13, 'createdOn' => 14, 'updatedOn' => 15, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
    );

    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return string          translated name of the field.
     * @throws PropelException - if the specified name could not be found in the fieldname mappings.
     */
    public static function translateFieldName($name, $fromType, $toType)
    {
        $toNames = ProjectPeer::getFieldNames($toType);
        $key = isset(ProjectPeer::$fieldKeys[$fromType][$name]) ? ProjectPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(ProjectPeer::$fieldKeys[$fromType], true));
        }

        return $toNames[$key];
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws PropelException - if the type is not valid.
     */
    public static function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, ProjectPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return ProjectPeer::$fieldNames[$type];
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
     *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. ProjectPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(ProjectPeer::TABLE_NAME.'.', $alias.'.', $column);
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param      Criteria $criteria object containing the columns to add.
     * @param      string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(ProjectPeer::ID);
            $criteria->addSelectColumn(ProjectPeer::NAME);
            $criteria->addSelectColumn(ProjectPeer::UUID);
            $criteria->addSelectColumn(ProjectPeer::SITEID);
            $criteria->addSelectColumn(ProjectPeer::HOSTNAME);
            $criteria->addSelectColumn(ProjectPeer::USERNAME);
            $criteria->addSelectColumn(ProjectPeer::SSHPORT);
            $criteria->addSelectColumn(ProjectPeer::CODEPATH);
            $criteria->addSelectColumn(ProjectPeer::FILESPATH);
            $criteria->addSelectColumn(ProjectPeer::DATABASEHOST);
            $criteria->addSelectColumn(ProjectPeer::DATABASEUSERNAME);
            $criteria->addSelectColumn(ProjectPeer::DATABASEPASSWORD);
            $criteria->addSelectColumn(ProjectPeer::DATABASENAME);
            $criteria->addSelectColumn(ProjectPeer::DATABASEPORT);
            $criteria->addSelectColumn(ProjectPeer::CREATEDON);
            $criteria->addSelectColumn(ProjectPeer::UPDATEDON);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.name');
            $criteria->addSelectColumn($alias . '.uuid');
            $criteria->addSelectColumn($alias . '.siteId');
            $criteria->addSelectColumn($alias . '.hostname');
            $criteria->addSelectColumn($alias . '.username');
            $criteria->addSelectColumn($alias . '.sshPort');
            $criteria->addSelectColumn($alias . '.codePath');
            $criteria->addSelectColumn($alias . '.filesPath');
            $criteria->addSelectColumn($alias . '.databaseHost');
            $criteria->addSelectColumn($alias . '.databaseUsername');
            $criteria->addSelectColumn($alias . '.databasePassword');
            $criteria->addSelectColumn($alias . '.databaseName');
            $criteria->addSelectColumn($alias . '.databasePort');
            $criteria->addSelectColumn($alias . '.createdOn');
            $criteria->addSelectColumn($alias . '.updatedOn');
        }
    }

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @return int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
    {
        // we may modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(ProjectPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            ProjectPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(ProjectPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        // BasePeer returns a PDOStatement
        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }
    /**
     * Selects one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      PropelPDO $con
     * @return Project
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = ProjectPeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }

        return null;
    }
    /**
     * Selects several row from the DB.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con
     * @return array           Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        return ProjectPeer::populateObjects(ProjectPeer::doSelectStmt($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
     *
     * Use this method directly if you want to work with an executed statement directly (for example
     * to perform your own object hydration).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con The connection to use
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return PDOStatement The executed PDOStatement object.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            ProjectPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(ProjectPeer::DATABASE_NAME);

        // BasePeer returns a PDOStatement
        return BasePeer::doSelect($criteria, $con);
    }
    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doSelect*()
     * methods in your stub classes -- you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by doSelect*()
     * and retrieveByPK*() calls.
     *
     * @param Project $obj A Project object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            ProjectPeer::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param      mixed $value A Project object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Project) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Project object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(ProjectPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return Project Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(ProjectPeer::$instances[$key])) {
                return ProjectPeer::$instances[$key];
            }
        }

        return null; // just to be explicit
    }

    /**
     * Clear the instance pool.
     *
     * @return void
     */
    public static function clearInstancePool($and_clear_all_references = false)
    {
      if ($and_clear_all_references) {
        foreach (ProjectPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        ProjectPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to project
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return string A string version of PK or null if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return null.
        if ($row[$startcol] === null) {
            return null;
        }

        return (string) $row[$startcol];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $startcol = 0)
    {

        return (int) $row[$startcol];
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function populateObjects(PDOStatement $stmt)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = ProjectPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = ProjectPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = ProjectPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ProjectPeer::addInstanceToPool($obj, $key);
            } // if key exists
        }
        $stmt->closeCursor();

        return $results;
    }
    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return array (Project object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = ProjectPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = ProjectPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + ProjectPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ProjectPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            ProjectPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related Site table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinSite(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(ProjectPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            ProjectPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(ProjectPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(ProjectPeer::SITEID, SitePeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of Project objects pre-filled with their Site objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Project objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinSite(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(ProjectPeer::DATABASE_NAME);
        }

        ProjectPeer::addSelectColumns($criteria);
        $startcol = ProjectPeer::NUM_HYDRATE_COLUMNS;
        SitePeer::addSelectColumns($criteria);

        $criteria->addJoin(ProjectPeer::SITEID, SitePeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = ProjectPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = ProjectPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = ProjectPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                ProjectPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = SitePeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = SitePeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = SitePeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    SitePeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Project) to $obj2 (Site)
                $obj2->addProject($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining all related tables
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(ProjectPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            ProjectPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(ProjectPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(ProjectPeer::SITEID, SitePeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }

    /**
     * Selects a collection of Project objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Project objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(ProjectPeer::DATABASE_NAME);
        }

        ProjectPeer::addSelectColumns($criteria);
        $startcol2 = ProjectPeer::NUM_HYDRATE_COLUMNS;

        SitePeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + SitePeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(ProjectPeer::SITEID, SitePeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = ProjectPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = ProjectPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = ProjectPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                ProjectPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined Site rows

            $key2 = SitePeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = SitePeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = SitePeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    SitePeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (Project) to the collection in $obj2 (Site)
                $obj2->addProject($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(ProjectPeer::DATABASE_NAME)->getTable(ProjectPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseProjectPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseProjectPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Fluxsauce\Brain\map\ProjectTableMap());
      }
    }

    /**
     * The class that the Peer will make instances of.
     *
     *
     * @return string ClassName
     */
    public static function getOMClass($row = 0, $colnum = 0)
    {
        return ProjectPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Project or Criteria object.
     *
     * @param      mixed $values Criteria or Project object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Project object
        }

        if ($criteria->containsKey(ProjectPeer::ID) && $criteria->keyContainsValue(ProjectPeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ProjectPeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(ProjectPeer::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

    /**
     * Performs an UPDATE on the database, given a Project or Criteria object.
     *
     * @param      mixed $values Criteria or Project object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(ProjectPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(ProjectPeer::ID);
            $value = $criteria->remove(ProjectPeer::ID);
            if ($value) {
                $selectCriteria->add(ProjectPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(ProjectPeer::TABLE_NAME);
            }

        } else { // $values is Project object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(ProjectPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the project table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(ProjectPeer::TABLE_NAME, $con, ProjectPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ProjectPeer::clearInstancePool();
            ProjectPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Project or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Project object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      PropelPDO $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *				if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, PropelPDO $con = null)
     {
        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            ProjectPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Project) { // it's a model object
            // invalidate the cache for this single object
            ProjectPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ProjectPeer::DATABASE_NAME);
            $criteria->add(ProjectPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                ProjectPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(ProjectPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            ProjectPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Project object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param Project $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(ProjectPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(ProjectPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->hasColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(ProjectPeer::DATABASE_NAME, ProjectPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Project
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = ProjectPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(ProjectPeer::DATABASE_NAME);
        $criteria->add(ProjectPeer::ID, $pk);

        $v = ProjectPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Project[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(ProjectPeer::DATABASE_NAME);
            $criteria->add(ProjectPeer::ID, $pks, Criteria::IN);
            $objs = ProjectPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseProjectPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseProjectPeer::buildTableMap();

