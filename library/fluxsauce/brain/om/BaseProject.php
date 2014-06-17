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
use Fluxsauce\Brain\Project;
use Fluxsauce\Brain\ProjectPeer;
use Fluxsauce\Brain\ProjectQuery;

/**
 * Base class that represents a row from the 'project' table.
 *
 *
 *
 * @package    propel.generator.brain.om
 */
abstract class BaseProject extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Fluxsauce\\Brain\\ProjectPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ProjectPeer
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
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the uuid field.
     * @var        string
     */
    protected $uuid;

    /**
     * The value for the siteid field.
     * @var        int
     */
    protected $siteid;

    /**
     * The value for the hostname field.
     * @var        string
     */
    protected $hostname;

    /**
     * The value for the username field.
     * @var        string
     */
    protected $username;

    /**
     * The value for the sshport field.
     * @var        int
     */
    protected $sshport;

    /**
     * The value for the codepath field.
     * @var        string
     */
    protected $codepath;

    /**
     * The value for the filespath field.
     * @var        string
     */
    protected $filespath;

    /**
     * The value for the databasehost field.
     * @var        string
     */
    protected $databasehost;

    /**
     * The value for the databaseusername field.
     * @var        string
     */
    protected $databaseusername;

    /**
     * The value for the databasepassword field.
     * @var        string
     */
    protected $databasepassword;

    /**
     * The value for the databasename field.
     * @var        string
     */
    protected $databasename;

    /**
     * The value for the databaseport field.
     * @var        int
     */
    protected $databaseport;

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
     * Get the [name] column value.
     * Name of the Project.
     * @return string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Get the [uuid] column value.
     * The UUID of the Project.
     * @return string
     */
    public function getUuid()
    {

        return $this->uuid;
    }

    /**
     * Get the [siteid] column value.
     * External key to an associated Site.
     * @return int
     */
    public function getSiteid()
    {

        return $this->siteid;
    }

    /**
     * Get the [hostname] column value.
     * The hostname for the local Project.
     * @return string
     */
    public function getHostname()
    {

        return $this->hostname;
    }

    /**
     * Get the [username] column value.
     * The UNIX username for the local Project.
     * @return string
     */
    public function getUsername()
    {

        return $this->username;
    }

    /**
     * Get the [sshport] column value.
     * The SSH port for the local Project.
     * @return int
     */
    public function getSshport()
    {

        return $this->sshport;
    }

    /**
     * Get the [codepath] column value.
     * The path on disk to the code root.
     * @return string
     */
    public function getCodepath()
    {

        return $this->codepath;
    }

    /**
     * Get the [filespath] column value.
     * The path on disk to the files root.
     * @return string
     */
    public function getFilespath()
    {

        return $this->filespath;
    }

    /**
     * Get the [databasehost] column value.
     * The hostname for the Project database.
     * @return string
     */
    public function getDatabasehost()
    {

        return $this->databasehost;
    }

    /**
     * Get the [databaseusername] column value.
     * The username for the Project database.
     * @return string
     */
    public function getDatabaseusername()
    {

        return $this->databaseusername;
    }

    /**
     * Get the [databasepassword] column value.
     * The password for the Project database.
     * @return string
     */
    public function getDatabasepassword()
    {

        return $this->databasepassword;
    }

    /**
     * Get the [databasename] column value.
     * The name for the Project database.
     * @return string
     */
    public function getDatabasename()
    {

        return $this->databasename;
    }

    /**
     * Get the [databaseport] column value.
     * The port for the Project database.
     * @return int
     */
    public function getDatabaseport()
    {

        return $this->databaseport;
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
     * @return Project The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = ProjectPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     * Name of the Project.
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = ProjectPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [uuid] column.
     * The UUID of the Project.
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setUuid($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->uuid !== $v) {
            $this->uuid = $v;
            $this->modifiedColumns[] = ProjectPeer::UUID;
        }


        return $this;
    } // setUuid()

    /**
     * Set the value of [siteid] column.
     * External key to an associated Site.
     * @param  int $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setSiteid($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->siteid !== $v) {
            $this->siteid = $v;
            $this->modifiedColumns[] = ProjectPeer::SITEID;
        }


        return $this;
    } // setSiteid()

    /**
     * Set the value of [hostname] column.
     * The hostname for the local Project.
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setHostname($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->hostname !== $v) {
            $this->hostname = $v;
            $this->modifiedColumns[] = ProjectPeer::HOSTNAME;
        }


        return $this;
    } // setHostname()

    /**
     * Set the value of [username] column.
     * The UNIX username for the local Project.
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setUsername($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->username !== $v) {
            $this->username = $v;
            $this->modifiedColumns[] = ProjectPeer::USERNAME;
        }


        return $this;
    } // setUsername()

    /**
     * Set the value of [sshport] column.
     * The SSH port for the local Project.
     * @param  int $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setSshport($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sshport !== $v) {
            $this->sshport = $v;
            $this->modifiedColumns[] = ProjectPeer::SSHPORT;
        }


        return $this;
    } // setSshport()

    /**
     * Set the value of [codepath] column.
     * The path on disk to the code root.
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setCodepath($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->codepath !== $v) {
            $this->codepath = $v;
            $this->modifiedColumns[] = ProjectPeer::CODEPATH;
        }


        return $this;
    } // setCodepath()

    /**
     * Set the value of [filespath] column.
     * The path on disk to the files root.
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setFilespath($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->filespath !== $v) {
            $this->filespath = $v;
            $this->modifiedColumns[] = ProjectPeer::FILESPATH;
        }


        return $this;
    } // setFilespath()

    /**
     * Set the value of [databasehost] column.
     * The hostname for the Project database.
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setDatabasehost($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->databasehost !== $v) {
            $this->databasehost = $v;
            $this->modifiedColumns[] = ProjectPeer::DATABASEHOST;
        }


        return $this;
    } // setDatabasehost()

    /**
     * Set the value of [databaseusername] column.
     * The username for the Project database.
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setDatabaseusername($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->databaseusername !== $v) {
            $this->databaseusername = $v;
            $this->modifiedColumns[] = ProjectPeer::DATABASEUSERNAME;
        }


        return $this;
    } // setDatabaseusername()

    /**
     * Set the value of [databasepassword] column.
     * The password for the Project database.
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setDatabasepassword($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->databasepassword !== $v) {
            $this->databasepassword = $v;
            $this->modifiedColumns[] = ProjectPeer::DATABASEPASSWORD;
        }


        return $this;
    } // setDatabasepassword()

    /**
     * Set the value of [databasename] column.
     * The name for the Project database.
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setDatabasename($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->databasename !== $v) {
            $this->databasename = $v;
            $this->modifiedColumns[] = ProjectPeer::DATABASENAME;
        }


        return $this;
    } // setDatabasename()

    /**
     * Set the value of [databaseport] column.
     * The port for the Project database.
     * @param  int $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setDatabaseport($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->databaseport !== $v) {
            $this->databaseport = $v;
            $this->modifiedColumns[] = ProjectPeer::DATABASEPORT;
        }


        return $this;
    } // setDatabaseport()

    /**
     * Sets the value of [createdon] column to a normalized version of the date/time value specified.
     * The time the record was created.
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Project The current object (for fluent API support)
     */
    public function setCreatedon($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->createdon !== null || $dt !== null) {
            $currentDateAsString = ($this->createdon !== null && $tmpDt = new DateTime($this->createdon)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->createdon = $newDateAsString;
                $this->modifiedColumns[] = ProjectPeer::CREATEDON;
            }
        } // if either are not null


        return $this;
    } // setCreatedon()

    /**
     * Sets the value of [updatedon] column to a normalized version of the date/time value specified.
     * The time the record was updated.
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Project The current object (for fluent API support)
     */
    public function setUpdatedon($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updatedon !== null || $dt !== null) {
            $currentDateAsString = ($this->updatedon !== null && $tmpDt = new DateTime($this->updatedon)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updatedon = $newDateAsString;
                $this->modifiedColumns[] = ProjectPeer::UPDATEDON;
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
            $this->name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->uuid = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->siteid = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->hostname = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->username = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->sshport = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->codepath = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->filespath = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->databasehost = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->databaseusername = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->databasepassword = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->databasename = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->databaseport = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
            $this->createdon = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
            $this->updatedon = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 16; // 16 = ProjectPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Project object", $e);
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
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = ProjectPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ProjectQuery::create()
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
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(ProjectPeer::CREATEDON)) {
                    $this->setCreatedon(time());
                }
                if (!$this->isColumnModified(ProjectPeer::UPDATEDON)) {
                    $this->setUpdatedon(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(ProjectPeer::UPDATEDON)) {
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
                ProjectPeer::addInstanceToPool($this);
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

        $this->modifiedColumns[] = ProjectPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ProjectPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ProjectPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '[id]';
        }
        if ($this->isColumnModified(ProjectPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '[name]';
        }
        if ($this->isColumnModified(ProjectPeer::UUID)) {
            $modifiedColumns[':p' . $index++]  = '[uuid]';
        }
        if ($this->isColumnModified(ProjectPeer::SITEID)) {
            $modifiedColumns[':p' . $index++]  = '[siteId]';
        }
        if ($this->isColumnModified(ProjectPeer::HOSTNAME)) {
            $modifiedColumns[':p' . $index++]  = '[hostname]';
        }
        if ($this->isColumnModified(ProjectPeer::USERNAME)) {
            $modifiedColumns[':p' . $index++]  = '[username]';
        }
        if ($this->isColumnModified(ProjectPeer::SSHPORT)) {
            $modifiedColumns[':p' . $index++]  = '[sshPort]';
        }
        if ($this->isColumnModified(ProjectPeer::CODEPATH)) {
            $modifiedColumns[':p' . $index++]  = '[codePath]';
        }
        if ($this->isColumnModified(ProjectPeer::FILESPATH)) {
            $modifiedColumns[':p' . $index++]  = '[filesPath]';
        }
        if ($this->isColumnModified(ProjectPeer::DATABASEHOST)) {
            $modifiedColumns[':p' . $index++]  = '[databaseHost]';
        }
        if ($this->isColumnModified(ProjectPeer::DATABASEUSERNAME)) {
            $modifiedColumns[':p' . $index++]  = '[databaseUsername]';
        }
        if ($this->isColumnModified(ProjectPeer::DATABASEPASSWORD)) {
            $modifiedColumns[':p' . $index++]  = '[databasePassword]';
        }
        if ($this->isColumnModified(ProjectPeer::DATABASENAME)) {
            $modifiedColumns[':p' . $index++]  = '[databaseName]';
        }
        if ($this->isColumnModified(ProjectPeer::DATABASEPORT)) {
            $modifiedColumns[':p' . $index++]  = '[databasePort]';
        }
        if ($this->isColumnModified(ProjectPeer::CREATEDON)) {
            $modifiedColumns[':p' . $index++]  = '[createdOn]';
        }
        if ($this->isColumnModified(ProjectPeer::UPDATEDON)) {
            $modifiedColumns[':p' . $index++]  = '[updatedOn]';
        }

        $sql = sprintf(
            'INSERT INTO [project] (%s) VALUES (%s)',
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
                    case '[name]':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '[uuid]':
                        $stmt->bindValue($identifier, $this->uuid, PDO::PARAM_STR);
                        break;
                    case '[siteId]':
                        $stmt->bindValue($identifier, $this->siteid, PDO::PARAM_INT);
                        break;
                    case '[hostname]':
                        $stmt->bindValue($identifier, $this->hostname, PDO::PARAM_STR);
                        break;
                    case '[username]':
                        $stmt->bindValue($identifier, $this->username, PDO::PARAM_STR);
                        break;
                    case '[sshPort]':
                        $stmt->bindValue($identifier, $this->sshport, PDO::PARAM_INT);
                        break;
                    case '[codePath]':
                        $stmt->bindValue($identifier, $this->codepath, PDO::PARAM_STR);
                        break;
                    case '[filesPath]':
                        $stmt->bindValue($identifier, $this->filespath, PDO::PARAM_STR);
                        break;
                    case '[databaseHost]':
                        $stmt->bindValue($identifier, $this->databasehost, PDO::PARAM_STR);
                        break;
                    case '[databaseUsername]':
                        $stmt->bindValue($identifier, $this->databaseusername, PDO::PARAM_STR);
                        break;
                    case '[databasePassword]':
                        $stmt->bindValue($identifier, $this->databasepassword, PDO::PARAM_STR);
                        break;
                    case '[databaseName]':
                        $stmt->bindValue($identifier, $this->databasename, PDO::PARAM_STR);
                        break;
                    case '[databasePort]':
                        $stmt->bindValue($identifier, $this->databaseport, PDO::PARAM_INT);
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


            if (($retval = ProjectPeer::doValidate($this, $columns)) !== true) {
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
        $pos = ProjectPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getName();
                break;
            case 2:
                return $this->getUuid();
                break;
            case 3:
                return $this->getSiteid();
                break;
            case 4:
                return $this->getHostname();
                break;
            case 5:
                return $this->getUsername();
                break;
            case 6:
                return $this->getSshport();
                break;
            case 7:
                return $this->getCodepath();
                break;
            case 8:
                return $this->getFilespath();
                break;
            case 9:
                return $this->getDatabasehost();
                break;
            case 10:
                return $this->getDatabaseusername();
                break;
            case 11:
                return $this->getDatabasepassword();
                break;
            case 12:
                return $this->getDatabasename();
                break;
            case 13:
                return $this->getDatabaseport();
                break;
            case 14:
                return $this->getCreatedon();
                break;
            case 15:
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
        if (isset($alreadyDumpedObjects['Project'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Project'][$this->getPrimaryKey()] = true;
        $keys = ProjectPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getUuid(),
            $keys[3] => $this->getSiteid(),
            $keys[4] => $this->getHostname(),
            $keys[5] => $this->getUsername(),
            $keys[6] => $this->getSshport(),
            $keys[7] => $this->getCodepath(),
            $keys[8] => $this->getFilespath(),
            $keys[9] => $this->getDatabasehost(),
            $keys[10] => $this->getDatabaseusername(),
            $keys[11] => $this->getDatabasepassword(),
            $keys[12] => $this->getDatabasename(),
            $keys[13] => $this->getDatabaseport(),
            $keys[14] => $this->getCreatedon(),
            $keys[15] => $this->getUpdatedon(),
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
        $pos = ProjectPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setName($value);
                break;
            case 2:
                $this->setUuid($value);
                break;
            case 3:
                $this->setSiteid($value);
                break;
            case 4:
                $this->setHostname($value);
                break;
            case 5:
                $this->setUsername($value);
                break;
            case 6:
                $this->setSshport($value);
                break;
            case 7:
                $this->setCodepath($value);
                break;
            case 8:
                $this->setFilespath($value);
                break;
            case 9:
                $this->setDatabasehost($value);
                break;
            case 10:
                $this->setDatabaseusername($value);
                break;
            case 11:
                $this->setDatabasepassword($value);
                break;
            case 12:
                $this->setDatabasename($value);
                break;
            case 13:
                $this->setDatabaseport($value);
                break;
            case 14:
                $this->setCreatedon($value);
                break;
            case 15:
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
        $keys = ProjectPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setUuid($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setSiteid($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setHostname($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setUsername($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setSshport($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setCodepath($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setFilespath($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setDatabasehost($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setDatabaseusername($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setDatabasepassword($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setDatabasename($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setDatabaseport($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setCreatedon($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setUpdatedon($arr[$keys[15]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ProjectPeer::DATABASE_NAME);

        if ($this->isColumnModified(ProjectPeer::ID)) $criteria->add(ProjectPeer::ID, $this->id);
        if ($this->isColumnModified(ProjectPeer::NAME)) $criteria->add(ProjectPeer::NAME, $this->name);
        if ($this->isColumnModified(ProjectPeer::UUID)) $criteria->add(ProjectPeer::UUID, $this->uuid);
        if ($this->isColumnModified(ProjectPeer::SITEID)) $criteria->add(ProjectPeer::SITEID, $this->siteid);
        if ($this->isColumnModified(ProjectPeer::HOSTNAME)) $criteria->add(ProjectPeer::HOSTNAME, $this->hostname);
        if ($this->isColumnModified(ProjectPeer::USERNAME)) $criteria->add(ProjectPeer::USERNAME, $this->username);
        if ($this->isColumnModified(ProjectPeer::SSHPORT)) $criteria->add(ProjectPeer::SSHPORT, $this->sshport);
        if ($this->isColumnModified(ProjectPeer::CODEPATH)) $criteria->add(ProjectPeer::CODEPATH, $this->codepath);
        if ($this->isColumnModified(ProjectPeer::FILESPATH)) $criteria->add(ProjectPeer::FILESPATH, $this->filespath);
        if ($this->isColumnModified(ProjectPeer::DATABASEHOST)) $criteria->add(ProjectPeer::DATABASEHOST, $this->databasehost);
        if ($this->isColumnModified(ProjectPeer::DATABASEUSERNAME)) $criteria->add(ProjectPeer::DATABASEUSERNAME, $this->databaseusername);
        if ($this->isColumnModified(ProjectPeer::DATABASEPASSWORD)) $criteria->add(ProjectPeer::DATABASEPASSWORD, $this->databasepassword);
        if ($this->isColumnModified(ProjectPeer::DATABASENAME)) $criteria->add(ProjectPeer::DATABASENAME, $this->databasename);
        if ($this->isColumnModified(ProjectPeer::DATABASEPORT)) $criteria->add(ProjectPeer::DATABASEPORT, $this->databaseport);
        if ($this->isColumnModified(ProjectPeer::CREATEDON)) $criteria->add(ProjectPeer::CREATEDON, $this->createdon);
        if ($this->isColumnModified(ProjectPeer::UPDATEDON)) $criteria->add(ProjectPeer::UPDATEDON, $this->updatedon);

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
        $criteria = new Criteria(ProjectPeer::DATABASE_NAME);
        $criteria->add(ProjectPeer::ID, $this->id);

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
     * @param object $copyObj An object of Project (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setUuid($this->getUuid());
        $copyObj->setSiteid($this->getSiteid());
        $copyObj->setHostname($this->getHostname());
        $copyObj->setUsername($this->getUsername());
        $copyObj->setSshport($this->getSshport());
        $copyObj->setCodepath($this->getCodepath());
        $copyObj->setFilespath($this->getFilespath());
        $copyObj->setDatabasehost($this->getDatabasehost());
        $copyObj->setDatabaseusername($this->getDatabaseusername());
        $copyObj->setDatabasepassword($this->getDatabasepassword());
        $copyObj->setDatabasename($this->getDatabasename());
        $copyObj->setDatabaseport($this->getDatabaseport());
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
     * @return Project Clone of current object.
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
     * @return ProjectPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ProjectPeer();
        }

        return self::$peer;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->name = null;
        $this->uuid = null;
        $this->siteid = null;
        $this->hostname = null;
        $this->username = null;
        $this->sshport = null;
        $this->codepath = null;
        $this->filespath = null;
        $this->databasehost = null;
        $this->databaseusername = null;
        $this->databasepassword = null;
        $this->databasename = null;
        $this->databaseport = null;
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
        return (string) $this->exportTo(ProjectPeer::DEFAULT_STRING_FORMAT);
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
     * @return     Project The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = ProjectPeer::UPDATEDON;

        return $this;
    }

}
