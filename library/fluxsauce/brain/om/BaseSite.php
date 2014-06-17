<?php

namespace Fluxsauce\Brain\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelDateTime;
use \PropelException;
use \PropelPDO;
use Fluxsauce\Brain\Site;
use Fluxsauce\Brain\SitePeer;
use Fluxsauce\Brain\SiteQuery;

/**
 * Base class that represents a row from the 'site' table.
 *
 *
 *
 * @package    propel.generator.brain.om
 */
abstract class BaseSite extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Fluxsauce\\Brain\\SitePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        SitePeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the provider field.
     * @var        string
     */
    protected $provider;

    /**
     * The value for the uuid field.
     * @var        string
     */
    protected $uuid;

    /**
     * The value for the realm field.
     * @var        string
     */
    protected $realm;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the vcsurl field.
     * @var        string
     */
    protected $vcsurl;

    /**
     * The value for the vcstype field.
     * @var        string
     */
    protected $vcstype;

    /**
     * The value for the vcsprotocol field.
     * @var        string
     */
    protected $vcsprotocol;

    /**
     * The value for the sshport field.
     * @var        int
     */
    protected $sshport;

    /**
     * The value for the createdon field.
     * @var        string
     */
    protected $createdon;

    /**
     * The value for the updatedon field.
     * @var        string
     */
    protected $updatedon;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [provider] column value.
     * The machine name of the Provider.
     * @return string
     */
    public function getProvider()
    {

        return $this->provider;
    }

    /**
     * Get the [uuid] column value.
     * The UUID of the Site.
     * @return string
     */
    public function getUuid()
    {

        return $this->uuid;
    }

    /**
     * Get the [realm] column value.
     * The realm of the site, like devcloud for Acquia.
     * @return string
     */
    public function getRealm()
    {

        return $this->realm;
    }

    /**
     * Get the [name] column value.
     * The machine name of the site.
     * @return string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Get the [title] column value.
     * The human-readable name of the site.
     * @return string
     */
    public function getTitle()
    {

        return $this->title;
    }

    /**
     * Get the [vcsurl] column value.
     * The Version Control System URL for the site.
     * @return string
     */
    public function getVcsurl()
    {

        return $this->vcsurl;
    }

    /**
     * Get the [vcstype] column value.
     * The Version Control System type, such as git or svn.
     * @return string
     */
    public function getVcstype()
    {

        return $this->vcstype;
    }

    /**
     * Get the [vcsprotocol] column value.
     * The Version Control System protocol, such as git or ssh.
     * @return string
     */
    public function getVcsprotocol()
    {

        return $this->vcsprotocol;
    }

    /**
     * Get the [sshport] column value.
     * The target port for SSH.
     * @return int
     */
    public function getSshport()
    {

        return $this->sshport;
    }

    /**
     * Get the [optionally formatted] temporal [createdon] column value.
     * The time the record was created.
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedon($format = 'Y-m-d H:i:s')
    {
        if ($this->createdon === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->createdon);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->createdon, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [updatedon] column value.
     * The time the record was updated.
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedon($format = 'Y-m-d H:i:s')
    {
        if ($this->updatedon === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->updatedon);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updatedon, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Site The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = SitePeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [provider] column.
     * The machine name of the Provider.
     * @param  string $v new value
     * @return Site The current object (for fluent API support)
     */
    public function setProvider($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->provider !== $v) {
            $this->provider = $v;
            $this->modifiedColumns[] = SitePeer::PROVIDER;
        }


        return $this;
    } // setProvider()

    /**
     * Set the value of [uuid] column.
     * The UUID of the Site.
     * @param  string $v new value
     * @return Site The current object (for fluent API support)
     */
    public function setUuid($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->uuid !== $v) {
            $this->uuid = $v;
            $this->modifiedColumns[] = SitePeer::UUID;
        }


        return $this;
    } // setUuid()

    /**
     * Set the value of [realm] column.
     * The realm of the site, like devcloud for Acquia.
     * @param  string $v new value
     * @return Site The current object (for fluent API support)
     */
    public function setRealm($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->realm !== $v) {
            $this->realm = $v;
            $this->modifiedColumns[] = SitePeer::REALM;
        }


        return $this;
    } // setRealm()

    /**
     * Set the value of [name] column.
     * The machine name of the site.
     * @param  string $v new value
     * @return Site The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = SitePeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [title] column.
     * The human-readable name of the site.
     * @param  string $v new value
     * @return Site The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = SitePeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [vcsurl] column.
     * The Version Control System URL for the site.
     * @param  string $v new value
     * @return Site The current object (for fluent API support)
     */
    public function setVcsurl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->vcsurl !== $v) {
            $this->vcsurl = $v;
            $this->modifiedColumns[] = SitePeer::VCSURL;
        }


        return $this;
    } // setVcsurl()

    /**
     * Set the value of [vcstype] column.
     * The Version Control System type, such as git or svn.
     * @param  string $v new value
     * @return Site The current object (for fluent API support)
     */
    public function setVcstype($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->vcstype !== $v) {
            $this->vcstype = $v;
            $this->modifiedColumns[] = SitePeer::VCSTYPE;
        }


        return $this;
    } // setVcstype()

    /**
     * Set the value of [vcsprotocol] column.
     * The Version Control System protocol, such as git or ssh.
     * @param  string $v new value
     * @return Site The current object (for fluent API support)
     */
    public function setVcsprotocol($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->vcsprotocol !== $v) {
            $this->vcsprotocol = $v;
            $this->modifiedColumns[] = SitePeer::VCSPROTOCOL;
        }


        return $this;
    } // setVcsprotocol()

    /**
     * Set the value of [sshport] column.
     * The target port for SSH.
     * @param  int $v new value
     * @return Site The current object (for fluent API support)
     */
    public function setSshport($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sshport !== $v) {
            $this->sshport = $v;
            $this->modifiedColumns[] = SitePeer::SSHPORT;
        }


        return $this;
    } // setSshport()

    /**
     * Sets the value of [createdon] column to a normalized version of the date/time value specified.
     * The time the record was created.
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Site The current object (for fluent API support)
     */
    public function setCreatedon($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->createdon !== null || $dt !== null) {
            $currentDateAsString = ($this->createdon !== null && $tmpDt = new DateTime($this->createdon)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->createdon = $newDateAsString;
                $this->modifiedColumns[] = SitePeer::CREATEDON;
            }
        } // if either are not null


        return $this;
    } // setCreatedon()

    /**
     * Sets the value of [updatedon] column to a normalized version of the date/time value specified.
     * The time the record was updated.
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Site The current object (for fluent API support)
     */
    public function setUpdatedon($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updatedon !== null || $dt !== null) {
            $currentDateAsString = ($this->updatedon !== null && $tmpDt = new DateTime($this->updatedon)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updatedon = $newDateAsString;
                $this->modifiedColumns[] = SitePeer::UPDATEDON;
            }
        } // if either are not null


        return $this;
    } // setUpdatedon()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->provider = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->uuid = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->realm = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->name = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->title = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->vcsurl = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->vcstype = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->vcsprotocol = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->sshport = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
            $this->createdon = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->updatedon = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 12; // 12 = SitePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Site object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(SitePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = SitePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(SitePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = SiteQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(SitePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(SitePeer::CREATEDON)) {
                    $this->setCreatedon(time());
                }
                if (!$this->isColumnModified(SitePeer::UPDATEDON)) {
                    $this->setUpdatedon(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(SitePeer::UPDATEDON)) {
                    $this->setUpdatedon(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                SitePeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = SitePeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . SitePeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(SitePeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '[id]';
        }
        if ($this->isColumnModified(SitePeer::PROVIDER)) {
            $modifiedColumns[':p' . $index++]  = '[provider]';
        }
        if ($this->isColumnModified(SitePeer::UUID)) {
            $modifiedColumns[':p' . $index++]  = '[uuid]';
        }
        if ($this->isColumnModified(SitePeer::REALM)) {
            $modifiedColumns[':p' . $index++]  = '[realm]';
        }
        if ($this->isColumnModified(SitePeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '[name]';
        }
        if ($this->isColumnModified(SitePeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '[title]';
        }
        if ($this->isColumnModified(SitePeer::VCSURL)) {
            $modifiedColumns[':p' . $index++]  = '[vcsUrl]';
        }
        if ($this->isColumnModified(SitePeer::VCSTYPE)) {
            $modifiedColumns[':p' . $index++]  = '[vcsType]';
        }
        if ($this->isColumnModified(SitePeer::VCSPROTOCOL)) {
            $modifiedColumns[':p' . $index++]  = '[vcsProtocol]';
        }
        if ($this->isColumnModified(SitePeer::SSHPORT)) {
            $modifiedColumns[':p' . $index++]  = '[sshPort]';
        }
        if ($this->isColumnModified(SitePeer::CREATEDON)) {
            $modifiedColumns[':p' . $index++]  = '[createdOn]';
        }
        if ($this->isColumnModified(SitePeer::UPDATEDON)) {
            $modifiedColumns[':p' . $index++]  = '[updatedOn]';
        }

        $sql = sprintf(
            'INSERT INTO [site] (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '[id]':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '[provider]':
                        $stmt->bindValue($identifier, $this->provider, PDO::PARAM_STR);
                        break;
                    case '[uuid]':
                        $stmt->bindValue($identifier, $this->uuid, PDO::PARAM_STR);
                        break;
                    case '[realm]':
                        $stmt->bindValue($identifier, $this->realm, PDO::PARAM_STR);
                        break;
                    case '[name]':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '[title]':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case '[vcsUrl]':
                        $stmt->bindValue($identifier, $this->vcsurl, PDO::PARAM_STR);
                        break;
                    case '[vcsType]':
                        $stmt->bindValue($identifier, $this->vcstype, PDO::PARAM_STR);
                        break;
                    case '[vcsProtocol]':
                        $stmt->bindValue($identifier, $this->vcsprotocol, PDO::PARAM_STR);
                        break;
                    case '[sshPort]':
                        $stmt->bindValue($identifier, $this->sshport, PDO::PARAM_INT);
                        break;
                    case '[createdOn]':
                        $stmt->bindValue($identifier, $this->createdon, PDO::PARAM_STR);
                        break;
                    case '[updatedOn]':
                        $stmt->bindValue($identifier, $this->updatedon, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            if (($retval = SitePeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }



            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = SitePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getProvider();
                break;
            case 2:
                return $this->getUuid();
                break;
            case 3:
                return $this->getRealm();
                break;
            case 4:
                return $this->getName();
                break;
            case 5:
                return $this->getTitle();
                break;
            case 6:
                return $this->getVcsurl();
                break;
            case 7:
                return $this->getVcstype();
                break;
            case 8:
                return $this->getVcsprotocol();
                break;
            case 9:
                return $this->getSshport();
                break;
            case 10:
                return $this->getCreatedon();
                break;
            case 11:
                return $this->getUpdatedon();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array())
    {
        if (isset($alreadyDumpedObjects['Site'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Site'][$this->getPrimaryKey()] = true;
        $keys = SitePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getProvider(),
            $keys[2] => $this->getUuid(),
            $keys[3] => $this->getRealm(),
            $keys[4] => $this->getName(),
            $keys[5] => $this->getTitle(),
            $keys[6] => $this->getVcsurl(),
            $keys[7] => $this->getVcstype(),
            $keys[8] => $this->getVcsprotocol(),
            $keys[9] => $this->getSshport(),
            $keys[10] => $this->getCreatedon(),
            $keys[11] => $this->getUpdatedon(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }


        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = SitePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setProvider($value);
                break;
            case 2:
                $this->setUuid($value);
                break;
            case 3:
                $this->setRealm($value);
                break;
            case 4:
                $this->setName($value);
                break;
            case 5:
                $this->setTitle($value);
                break;
            case 6:
                $this->setVcsurl($value);
                break;
            case 7:
                $this->setVcstype($value);
                break;
            case 8:
                $this->setVcsprotocol($value);
                break;
            case 9:
                $this->setSshport($value);
                break;
            case 10:
                $this->setCreatedon($value);
                break;
            case 11:
                $this->setUpdatedon($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = SitePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setProvider($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setUuid($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setRealm($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setName($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setTitle($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setVcsurl($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setVcstype($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setVcsprotocol($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setSshport($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setCreatedon($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setUpdatedon($arr[$keys[11]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(SitePeer::DATABASE_NAME);

        if ($this->isColumnModified(SitePeer::ID)) $criteria->add(SitePeer::ID, $this->id);
        if ($this->isColumnModified(SitePeer::PROVIDER)) $criteria->add(SitePeer::PROVIDER, $this->provider);
        if ($this->isColumnModified(SitePeer::UUID)) $criteria->add(SitePeer::UUID, $this->uuid);
        if ($this->isColumnModified(SitePeer::REALM)) $criteria->add(SitePeer::REALM, $this->realm);
        if ($this->isColumnModified(SitePeer::NAME)) $criteria->add(SitePeer::NAME, $this->name);
        if ($this->isColumnModified(SitePeer::TITLE)) $criteria->add(SitePeer::TITLE, $this->title);
        if ($this->isColumnModified(SitePeer::VCSURL)) $criteria->add(SitePeer::VCSURL, $this->vcsurl);
        if ($this->isColumnModified(SitePeer::VCSTYPE)) $criteria->add(SitePeer::VCSTYPE, $this->vcstype);
        if ($this->isColumnModified(SitePeer::VCSPROTOCOL)) $criteria->add(SitePeer::VCSPROTOCOL, $this->vcsprotocol);
        if ($this->isColumnModified(SitePeer::SSHPORT)) $criteria->add(SitePeer::SSHPORT, $this->sshport);
        if ($this->isColumnModified(SitePeer::CREATEDON)) $criteria->add(SitePeer::CREATEDON, $this->createdon);
        if ($this->isColumnModified(SitePeer::UPDATEDON)) $criteria->add(SitePeer::UPDATEDON, $this->updatedon);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(SitePeer::DATABASE_NAME);
        $criteria->add(SitePeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Site (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setProvider($this->getProvider());
        $copyObj->setUuid($this->getUuid());
        $copyObj->setRealm($this->getRealm());
        $copyObj->setName($this->getName());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setVcsurl($this->getVcsurl());
        $copyObj->setVcstype($this->getVcstype());
        $copyObj->setVcsprotocol($this->getVcsprotocol());
        $copyObj->setSshport($this->getSshport());
        $copyObj->setCreatedon($this->getCreatedon());
        $copyObj->setUpdatedon($this->getUpdatedon());
        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return Site Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return SitePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new SitePeer();
        }

        return self::$peer;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->provider = null;
        $this->uuid = null;
        $this->realm = null;
        $this->name = null;
        $this->title = null;
        $this->vcsurl = null;
        $this->vcstype = null;
        $this->vcsprotocol = null;
        $this->sshport = null;
        $this->createdon = null;
        $this->updatedon = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(SitePeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     Site The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = SitePeer::UPDATEDON;

        return $this;
    }

}
