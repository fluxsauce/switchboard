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
use Fluxsauce\Brain\Environment;
use Fluxsauce\Brain\EnvironmentPeer;
use Fluxsauce\Brain\EnvironmentQuery;
use Fluxsauce\Brain\Site;
use Fluxsauce\Brain\SiteQuery;

/**
 * Base class that represents a row from the 'environment' table.
 *
 *
 *
 * @package    propel.generator.brain.om
 */
abstract class BaseEnvironment extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Fluxsauce\\Brain\\EnvironmentPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        EnvironmentPeer
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
     * The value for the siteid field.
     * @var        int
     */
    protected $siteid;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the host field.
     * @var        string
     */
    protected $host;

    /**
     * The value for the username field.
     * @var        string
     */
    protected $username;

    /**
     * The value for the branch field.
     * @var        string
     */
    protected $branch;

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
     * @var        Site
     */
    protected $aSite;

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
     * Get the [siteid] column value.
     * External key to an associated Site.
     * @return int
     */
    public function getSiteid()
    {

        return $this->siteid;
    }

    /**
     * Get the [name] column value.
     * Name of the Environment.
     * @return string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Get the [host] column value.
     * The hostname for the Environment.
     * @return string
     */
    public function getHost()
    {

        return $this->host;
    }

    /**
     * Get the [username] column value.
     * The UNIX username for the Environment.
     * @return string
     */
    public function getUsername()
    {

        return $this->username;
    }

    /**
     * Get the [branch] column value.
     * The default VCS branch for the Environment.
     * @return string
     */
    public function getBranch()
    {

        return $this->branch;
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
     * @return Environment The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = EnvironmentPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [siteid] column.
     * External key to an associated Site.
     * @param  int $v new value
     * @return Environment The current object (for fluent API support)
     */
    public function setSiteid($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->siteid !== $v) {
            $this->siteid = $v;
            $this->modifiedColumns[] = EnvironmentPeer::SITEID;
        }

        if ($this->aSite !== null && $this->aSite->getId() !== $v) {
            $this->aSite = null;
        }


        return $this;
    } // setSiteid()

    /**
     * Set the value of [name] column.
     * Name of the Environment.
     * @param  string $v new value
     * @return Environment The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = EnvironmentPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [host] column.
     * The hostname for the Environment.
     * @param  string $v new value
     * @return Environment The current object (for fluent API support)
     */
    public function setHost($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->host !== $v) {
            $this->host = $v;
            $this->modifiedColumns[] = EnvironmentPeer::HOST;
        }


        return $this;
    } // setHost()

    /**
     * Set the value of [username] column.
     * The UNIX username for the Environment.
     * @param  string $v new value
     * @return Environment The current object (for fluent API support)
     */
    public function setUsername($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->username !== $v) {
            $this->username = $v;
            $this->modifiedColumns[] = EnvironmentPeer::USERNAME;
        }


        return $this;
    } // setUsername()

    /**
     * Set the value of [branch] column.
     * The default VCS branch for the Environment.
     * @param  string $v new value
     * @return Environment The current object (for fluent API support)
     */
    public function setBranch($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->branch !== $v) {
            $this->branch = $v;
            $this->modifiedColumns[] = EnvironmentPeer::BRANCH;
        }


        return $this;
    } // setBranch()

    /**
     * Sets the value of [createdon] column to a normalized version of the date/time value specified.
     * The time the record was created.
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Environment The current object (for fluent API support)
     */
    public function setCreatedon($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->createdon !== null || $dt !== null) {
            $currentDateAsString = ($this->createdon !== null && $tmpDt = new DateTime($this->createdon)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->createdon = $newDateAsString;
                $this->modifiedColumns[] = EnvironmentPeer::CREATEDON;
            }
        } // if either are not null


        return $this;
    } // setCreatedon()

    /**
     * Sets the value of [updatedon] column to a normalized version of the date/time value specified.
     * The time the record was updated.
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Environment The current object (for fluent API support)
     */
    public function setUpdatedon($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updatedon !== null || $dt !== null) {
            $currentDateAsString = ($this->updatedon !== null && $tmpDt = new DateTime($this->updatedon)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updatedon = $newDateAsString;
                $this->modifiedColumns[] = EnvironmentPeer::UPDATEDON;
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
            $this->siteid = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->host = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->username = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->branch = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->createdon = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->updatedon = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 8; // 8 = EnvironmentPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Environment object", $e);
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

        if ($this->aSite !== null && $this->siteid !== $this->aSite->getId()) {
            $this->aSite = null;
        }
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
            $con = Propel::getConnection(EnvironmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = EnvironmentPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aSite = null;
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
            $con = Propel::getConnection(EnvironmentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = EnvironmentQuery::create()
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
            $con = Propel::getConnection(EnvironmentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(EnvironmentPeer::CREATEDON)) {
                    $this->setCreatedon(time());
                }
                if (!$this->isColumnModified(EnvironmentPeer::UPDATEDON)) {
                    $this->setUpdatedon(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(EnvironmentPeer::UPDATEDON)) {
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
                EnvironmentPeer::addInstanceToPool($this);
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

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aSite !== null) {
                if ($this->aSite->isModified() || $this->aSite->isNew()) {
                    $affectedRows += $this->aSite->save($con);
                }
                $this->setSite($this->aSite);
            }

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

        $this->modifiedColumns[] = EnvironmentPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . EnvironmentPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(EnvironmentPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '[id]';
        }
        if ($this->isColumnModified(EnvironmentPeer::SITEID)) {
            $modifiedColumns[':p' . $index++]  = '[siteId]';
        }
        if ($this->isColumnModified(EnvironmentPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '[name]';
        }
        if ($this->isColumnModified(EnvironmentPeer::HOST)) {
            $modifiedColumns[':p' . $index++]  = '[host]';
        }
        if ($this->isColumnModified(EnvironmentPeer::USERNAME)) {
            $modifiedColumns[':p' . $index++]  = '[username]';
        }
        if ($this->isColumnModified(EnvironmentPeer::BRANCH)) {
            $modifiedColumns[':p' . $index++]  = '[branch]';
        }
        if ($this->isColumnModified(EnvironmentPeer::CREATEDON)) {
            $modifiedColumns[':p' . $index++]  = '[createdOn]';
        }
        if ($this->isColumnModified(EnvironmentPeer::UPDATEDON)) {
            $modifiedColumns[':p' . $index++]  = '[updatedOn]';
        }

        $sql = sprintf(
            'INSERT INTO [environment] (%s) VALUES (%s)',
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
                    case '[siteId]':
                        $stmt->bindValue($identifier, $this->siteid, PDO::PARAM_INT);
                        break;
                    case '[name]':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '[host]':
                        $stmt->bindValue($identifier, $this->host, PDO::PARAM_STR);
                        break;
                    case '[username]':
                        $stmt->bindValue($identifier, $this->username, PDO::PARAM_STR);
                        break;
                    case '[branch]':
                        $stmt->bindValue($identifier, $this->branch, PDO::PARAM_STR);
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


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aSite !== null) {
                if (!$this->aSite->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aSite->getValidationFailures());
                }
            }


            if (($retval = EnvironmentPeer::doValidate($this, $columns)) !== true) {
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
        $pos = EnvironmentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getSiteid();
                break;
            case 2:
                return $this->getName();
                break;
            case 3:
                return $this->getHost();
                break;
            case 4:
                return $this->getUsername();
                break;
            case 5:
                return $this->getBranch();
                break;
            case 6:
                return $this->getCreatedon();
                break;
            case 7:
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
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Environment'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Environment'][$this->getPrimaryKey()] = true;
        $keys = EnvironmentPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getSiteid(),
            $keys[2] => $this->getName(),
            $keys[3] => $this->getHost(),
            $keys[4] => $this->getUsername(),
            $keys[5] => $this->getBranch(),
            $keys[6] => $this->getCreatedon(),
            $keys[7] => $this->getUpdatedon(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aSite) {
                $result['Site'] = $this->aSite->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
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
        $pos = EnvironmentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setSiteid($value);
                break;
            case 2:
                $this->setName($value);
                break;
            case 3:
                $this->setHost($value);
                break;
            case 4:
                $this->setUsername($value);
                break;
            case 5:
                $this->setBranch($value);
                break;
            case 6:
                $this->setCreatedon($value);
                break;
            case 7:
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
        $keys = EnvironmentPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setSiteid($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setName($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setHost($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setUsername($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setBranch($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCreatedon($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setUpdatedon($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(EnvironmentPeer::DATABASE_NAME);

        if ($this->isColumnModified(EnvironmentPeer::ID)) $criteria->add(EnvironmentPeer::ID, $this->id);
        if ($this->isColumnModified(EnvironmentPeer::SITEID)) $criteria->add(EnvironmentPeer::SITEID, $this->siteid);
        if ($this->isColumnModified(EnvironmentPeer::NAME)) $criteria->add(EnvironmentPeer::NAME, $this->name);
        if ($this->isColumnModified(EnvironmentPeer::HOST)) $criteria->add(EnvironmentPeer::HOST, $this->host);
        if ($this->isColumnModified(EnvironmentPeer::USERNAME)) $criteria->add(EnvironmentPeer::USERNAME, $this->username);
        if ($this->isColumnModified(EnvironmentPeer::BRANCH)) $criteria->add(EnvironmentPeer::BRANCH, $this->branch);
        if ($this->isColumnModified(EnvironmentPeer::CREATEDON)) $criteria->add(EnvironmentPeer::CREATEDON, $this->createdon);
        if ($this->isColumnModified(EnvironmentPeer::UPDATEDON)) $criteria->add(EnvironmentPeer::UPDATEDON, $this->updatedon);

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
        $criteria = new Criteria(EnvironmentPeer::DATABASE_NAME);
        $criteria->add(EnvironmentPeer::ID, $this->id);

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
     * @param object $copyObj An object of Environment (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setSiteid($this->getSiteid());
        $copyObj->setName($this->getName());
        $copyObj->setHost($this->getHost());
        $copyObj->setUsername($this->getUsername());
        $copyObj->setBranch($this->getBranch());
        $copyObj->setCreatedon($this->getCreatedon());
        $copyObj->setUpdatedon($this->getUpdatedon());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

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
     * @return Environment Clone of current object.
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
     * @return EnvironmentPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new EnvironmentPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Site object.
     *
     * @param                  Site $v
     * @return Environment The current object (for fluent API support)
     * @throws PropelException
     */
    public function setSite(Site $v = null)
    {
        if ($v === null) {
            $this->setSiteid(NULL);
        } else {
            $this->setSiteid($v->getId());
        }

        $this->aSite = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Site object, it will not be re-added.
        if ($v !== null) {
            $v->addEnvironment($this);
        }


        return $this;
    }


    /**
     * Get the associated Site object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Site The associated Site object.
     * @throws PropelException
     */
    public function getSite(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aSite === null && ($this->siteid !== null) && $doQuery) {
            $this->aSite = SiteQuery::create()->findPk($this->siteid, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aSite->addEnvironments($this);
             */
        }

        return $this->aSite;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->siteid = null;
        $this->name = null;
        $this->host = null;
        $this->username = null;
        $this->branch = null;
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
            if ($this->aSite instanceof Persistent) {
              $this->aSite->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aSite = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(EnvironmentPeer::DEFAULT_STRING_FORMAT);
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
     * @return     Environment The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = EnvironmentPeer::UPDATEDON;

        return $this;
    }

}
