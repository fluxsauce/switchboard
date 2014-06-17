<?php

namespace Fluxsauce\Brain\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \PDO;
use \Propel;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Fluxsauce\Brain\Project;
use Fluxsauce\Brain\ProjectPeer;
use Fluxsauce\Brain\ProjectQuery;

/**
 * Base class that represents a query for the 'project' table.
 *
 *
 *
 * @method ProjectQuery orderById($order = Criteria::ASC) Order by the id column
 * @method ProjectQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method ProjectQuery orderByUuid($order = Criteria::ASC) Order by the uuid column
 * @method ProjectQuery orderBySiteid($order = Criteria::ASC) Order by the siteId column
 * @method ProjectQuery orderByHostname($order = Criteria::ASC) Order by the hostname column
 * @method ProjectQuery orderByUsername($order = Criteria::ASC) Order by the username column
 * @method ProjectQuery orderBySshport($order = Criteria::ASC) Order by the sshPort column
 * @method ProjectQuery orderByCodepath($order = Criteria::ASC) Order by the codePath column
 * @method ProjectQuery orderByFilespath($order = Criteria::ASC) Order by the filesPath column
 * @method ProjectQuery orderByDatabasehost($order = Criteria::ASC) Order by the databaseHost column
 * @method ProjectQuery orderByDatabaseusername($order = Criteria::ASC) Order by the databaseUsername column
 * @method ProjectQuery orderByDatabasepassword($order = Criteria::ASC) Order by the databasePassword column
 * @method ProjectQuery orderByDatabasename($order = Criteria::ASC) Order by the databaseName column
 * @method ProjectQuery orderByDatabaseport($order = Criteria::ASC) Order by the databasePort column
 * @method ProjectQuery orderByCreatedon($order = Criteria::ASC) Order by the createdOn column
 * @method ProjectQuery orderByUpdatedon($order = Criteria::ASC) Order by the updatedOn column
 *
 * @method ProjectQuery groupById() Group by the id column
 * @method ProjectQuery groupByName() Group by the name column
 * @method ProjectQuery groupByUuid() Group by the uuid column
 * @method ProjectQuery groupBySiteid() Group by the siteId column
 * @method ProjectQuery groupByHostname() Group by the hostname column
 * @method ProjectQuery groupByUsername() Group by the username column
 * @method ProjectQuery groupBySshport() Group by the sshPort column
 * @method ProjectQuery groupByCodepath() Group by the codePath column
 * @method ProjectQuery groupByFilespath() Group by the filesPath column
 * @method ProjectQuery groupByDatabasehost() Group by the databaseHost column
 * @method ProjectQuery groupByDatabaseusername() Group by the databaseUsername column
 * @method ProjectQuery groupByDatabasepassword() Group by the databasePassword column
 * @method ProjectQuery groupByDatabasename() Group by the databaseName column
 * @method ProjectQuery groupByDatabaseport() Group by the databasePort column
 * @method ProjectQuery groupByCreatedon() Group by the createdOn column
 * @method ProjectQuery groupByUpdatedon() Group by the updatedOn column
 *
 * @method ProjectQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ProjectQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ProjectQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Project findOne(PropelPDO $con = null) Return the first Project matching the query
 * @method Project findOneOrCreate(PropelPDO $con = null) Return the first Project matching the query, or a new Project object populated from the query conditions when no match is found
 *
 * @method Project findOneByName(string $name) Return the first Project filtered by the name column
 * @method Project findOneByUuid(string $uuid) Return the first Project filtered by the uuid column
 * @method Project findOneBySiteid(int $siteId) Return the first Project filtered by the siteId column
 * @method Project findOneByHostname(string $hostname) Return the first Project filtered by the hostname column
 * @method Project findOneByUsername(string $username) Return the first Project filtered by the username column
 * @method Project findOneBySshport(int $sshPort) Return the first Project filtered by the sshPort column
 * @method Project findOneByCodepath(string $codePath) Return the first Project filtered by the codePath column
 * @method Project findOneByFilespath(string $filesPath) Return the first Project filtered by the filesPath column
 * @method Project findOneByDatabasehost(string $databaseHost) Return the first Project filtered by the databaseHost column
 * @method Project findOneByDatabaseusername(string $databaseUsername) Return the first Project filtered by the databaseUsername column
 * @method Project findOneByDatabasepassword(string $databasePassword) Return the first Project filtered by the databasePassword column
 * @method Project findOneByDatabasename(string $databaseName) Return the first Project filtered by the databaseName column
 * @method Project findOneByDatabaseport(int $databasePort) Return the first Project filtered by the databasePort column
 * @method Project findOneByCreatedon(string $createdOn) Return the first Project filtered by the createdOn column
 * @method Project findOneByUpdatedon(string $updatedOn) Return the first Project filtered by the updatedOn column
 *
 * @method array findById(int $id) Return Project objects filtered by the id column
 * @method array findByName(string $name) Return Project objects filtered by the name column
 * @method array findByUuid(string $uuid) Return Project objects filtered by the uuid column
 * @method array findBySiteid(int $siteId) Return Project objects filtered by the siteId column
 * @method array findByHostname(string $hostname) Return Project objects filtered by the hostname column
 * @method array findByUsername(string $username) Return Project objects filtered by the username column
 * @method array findBySshport(int $sshPort) Return Project objects filtered by the sshPort column
 * @method array findByCodepath(string $codePath) Return Project objects filtered by the codePath column
 * @method array findByFilespath(string $filesPath) Return Project objects filtered by the filesPath column
 * @method array findByDatabasehost(string $databaseHost) Return Project objects filtered by the databaseHost column
 * @method array findByDatabaseusername(string $databaseUsername) Return Project objects filtered by the databaseUsername column
 * @method array findByDatabasepassword(string $databasePassword) Return Project objects filtered by the databasePassword column
 * @method array findByDatabasename(string $databaseName) Return Project objects filtered by the databaseName column
 * @method array findByDatabaseport(int $databasePort) Return Project objects filtered by the databasePort column
 * @method array findByCreatedon(string $createdOn) Return Project objects filtered by the createdOn column
 * @method array findByUpdatedon(string $updatedOn) Return Project objects filtered by the updatedOn column
 *
 * @package    propel.generator.brain.om
 */
abstract class BaseProjectQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseProjectQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'brain';
        }
        if (null === $modelName) {
            $modelName = 'Fluxsauce\\Brain\\Project';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ProjectQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ProjectQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ProjectQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ProjectQuery) {
            return $criteria;
        }
        $query = new ProjectQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Project|Project[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProjectPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Project A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Project A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT [id], [name], [uuid], [siteId], [hostname], [username], [sshPort], [codePath], [filesPath], [databaseHost], [databaseUsername], [databasePassword], [databaseName], [databasePort], [createdOn], [updatedOn] FROM [project] WHERE [id] = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Project();
            $obj->hydrate($row);
            ProjectPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return Project|Project[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|Project[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ProjectPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ProjectPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ProjectPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ProjectPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProjectPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProjectPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the uuid column
     *
     * Example usage:
     * <code>
     * $query->filterByUuid('fooValue');   // WHERE uuid = 'fooValue'
     * $query->filterByUuid('%fooValue%'); // WHERE uuid LIKE '%fooValue%'
     * </code>
     *
     * @param     string $uuid The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByUuid($uuid = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($uuid)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $uuid)) {
                $uuid = str_replace('*', '%', $uuid);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProjectPeer::UUID, $uuid, $comparison);
    }

    /**
     * Filter the query on the siteId column
     *
     * Example usage:
     * <code>
     * $query->filterBySiteid(1234); // WHERE siteId = 1234
     * $query->filterBySiteid(array(12, 34)); // WHERE siteId IN (12, 34)
     * $query->filterBySiteid(array('min' => 12)); // WHERE siteId >= 12
     * $query->filterBySiteid(array('max' => 12)); // WHERE siteId <= 12
     * </code>
     *
     * @param     mixed $siteid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterBySiteid($siteid = null, $comparison = null)
    {
        if (is_array($siteid)) {
            $useMinMax = false;
            if (isset($siteid['min'])) {
                $this->addUsingAlias(ProjectPeer::SITEID, $siteid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($siteid['max'])) {
                $this->addUsingAlias(ProjectPeer::SITEID, $siteid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProjectPeer::SITEID, $siteid, $comparison);
    }

    /**
     * Filter the query on the hostname column
     *
     * Example usage:
     * <code>
     * $query->filterByHostname('fooValue');   // WHERE hostname = 'fooValue'
     * $query->filterByHostname('%fooValue%'); // WHERE hostname LIKE '%fooValue%'
     * </code>
     *
     * @param     string $hostname The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByHostname($hostname = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($hostname)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $hostname)) {
                $hostname = str_replace('*', '%', $hostname);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProjectPeer::HOSTNAME, $hostname, $comparison);
    }

    /**
     * Filter the query on the username column
     *
     * Example usage:
     * <code>
     * $query->filterByUsername('fooValue');   // WHERE username = 'fooValue'
     * $query->filterByUsername('%fooValue%'); // WHERE username LIKE '%fooValue%'
     * </code>
     *
     * @param     string $username The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByUsername($username = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($username)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $username)) {
                $username = str_replace('*', '%', $username);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProjectPeer::USERNAME, $username, $comparison);
    }

    /**
     * Filter the query on the sshPort column
     *
     * Example usage:
     * <code>
     * $query->filterBySshport(1234); // WHERE sshPort = 1234
     * $query->filterBySshport(array(12, 34)); // WHERE sshPort IN (12, 34)
     * $query->filterBySshport(array('min' => 12)); // WHERE sshPort >= 12
     * $query->filterBySshport(array('max' => 12)); // WHERE sshPort <= 12
     * </code>
     *
     * @param     mixed $sshport The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterBySshport($sshport = null, $comparison = null)
    {
        if (is_array($sshport)) {
            $useMinMax = false;
            if (isset($sshport['min'])) {
                $this->addUsingAlias(ProjectPeer::SSHPORT, $sshport['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sshport['max'])) {
                $this->addUsingAlias(ProjectPeer::SSHPORT, $sshport['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProjectPeer::SSHPORT, $sshport, $comparison);
    }

    /**
     * Filter the query on the codePath column
     *
     * Example usage:
     * <code>
     * $query->filterByCodepath('fooValue');   // WHERE codePath = 'fooValue'
     * $query->filterByCodepath('%fooValue%'); // WHERE codePath LIKE '%fooValue%'
     * </code>
     *
     * @param     string $codepath The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByCodepath($codepath = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($codepath)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $codepath)) {
                $codepath = str_replace('*', '%', $codepath);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProjectPeer::CODEPATH, $codepath, $comparison);
    }

    /**
     * Filter the query on the filesPath column
     *
     * Example usage:
     * <code>
     * $query->filterByFilespath('fooValue');   // WHERE filesPath = 'fooValue'
     * $query->filterByFilespath('%fooValue%'); // WHERE filesPath LIKE '%fooValue%'
     * </code>
     *
     * @param     string $filespath The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByFilespath($filespath = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($filespath)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $filespath)) {
                $filespath = str_replace('*', '%', $filespath);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProjectPeer::FILESPATH, $filespath, $comparison);
    }

    /**
     * Filter the query on the databaseHost column
     *
     * Example usage:
     * <code>
     * $query->filterByDatabasehost('fooValue');   // WHERE databaseHost = 'fooValue'
     * $query->filterByDatabasehost('%fooValue%'); // WHERE databaseHost LIKE '%fooValue%'
     * </code>
     *
     * @param     string $databasehost The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByDatabasehost($databasehost = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($databasehost)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $databasehost)) {
                $databasehost = str_replace('*', '%', $databasehost);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProjectPeer::DATABASEHOST, $databasehost, $comparison);
    }

    /**
     * Filter the query on the databaseUsername column
     *
     * Example usage:
     * <code>
     * $query->filterByDatabaseusername('fooValue');   // WHERE databaseUsername = 'fooValue'
     * $query->filterByDatabaseusername('%fooValue%'); // WHERE databaseUsername LIKE '%fooValue%'
     * </code>
     *
     * @param     string $databaseusername The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByDatabaseusername($databaseusername = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($databaseusername)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $databaseusername)) {
                $databaseusername = str_replace('*', '%', $databaseusername);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProjectPeer::DATABASEUSERNAME, $databaseusername, $comparison);
    }

    /**
     * Filter the query on the databasePassword column
     *
     * Example usage:
     * <code>
     * $query->filterByDatabasepassword('fooValue');   // WHERE databasePassword = 'fooValue'
     * $query->filterByDatabasepassword('%fooValue%'); // WHERE databasePassword LIKE '%fooValue%'
     * </code>
     *
     * @param     string $databasepassword The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByDatabasepassword($databasepassword = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($databasepassword)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $databasepassword)) {
                $databasepassword = str_replace('*', '%', $databasepassword);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProjectPeer::DATABASEPASSWORD, $databasepassword, $comparison);
    }

    /**
     * Filter the query on the databaseName column
     *
     * Example usage:
     * <code>
     * $query->filterByDatabasename('fooValue');   // WHERE databaseName = 'fooValue'
     * $query->filterByDatabasename('%fooValue%'); // WHERE databaseName LIKE '%fooValue%'
     * </code>
     *
     * @param     string $databasename The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByDatabasename($databasename = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($databasename)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $databasename)) {
                $databasename = str_replace('*', '%', $databasename);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProjectPeer::DATABASENAME, $databasename, $comparison);
    }

    /**
     * Filter the query on the databasePort column
     *
     * Example usage:
     * <code>
     * $query->filterByDatabaseport(1234); // WHERE databasePort = 1234
     * $query->filterByDatabaseport(array(12, 34)); // WHERE databasePort IN (12, 34)
     * $query->filterByDatabaseport(array('min' => 12)); // WHERE databasePort >= 12
     * $query->filterByDatabaseport(array('max' => 12)); // WHERE databasePort <= 12
     * </code>
     *
     * @param     mixed $databaseport The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByDatabaseport($databaseport = null, $comparison = null)
    {
        if (is_array($databaseport)) {
            $useMinMax = false;
            if (isset($databaseport['min'])) {
                $this->addUsingAlias(ProjectPeer::DATABASEPORT, $databaseport['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($databaseport['max'])) {
                $this->addUsingAlias(ProjectPeer::DATABASEPORT, $databaseport['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProjectPeer::DATABASEPORT, $databaseport, $comparison);
    }

    /**
     * Filter the query on the createdOn column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedon('2011-03-14'); // WHERE createdOn = '2011-03-14'
     * $query->filterByCreatedon('now'); // WHERE createdOn = '2011-03-14'
     * $query->filterByCreatedon(array('max' => 'yesterday')); // WHERE createdOn < '2011-03-13'
     * </code>
     *
     * @param     mixed $createdon The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByCreatedon($createdon = null, $comparison = null)
    {
        if (is_array($createdon)) {
            $useMinMax = false;
            if (isset($createdon['min'])) {
                $this->addUsingAlias(ProjectPeer::CREATEDON, $createdon['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdon['max'])) {
                $this->addUsingAlias(ProjectPeer::CREATEDON, $createdon['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProjectPeer::CREATEDON, $createdon, $comparison);
    }

    /**
     * Filter the query on the updatedOn column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedon('2011-03-14'); // WHERE updatedOn = '2011-03-14'
     * $query->filterByUpdatedon('now'); // WHERE updatedOn = '2011-03-14'
     * $query->filterByUpdatedon(array('max' => 'yesterday')); // WHERE updatedOn < '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedon The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function filterByUpdatedon($updatedon = null, $comparison = null)
    {
        if (is_array($updatedon)) {
            $useMinMax = false;
            if (isset($updatedon['min'])) {
                $this->addUsingAlias(ProjectPeer::UPDATEDON, $updatedon['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedon['max'])) {
                $this->addUsingAlias(ProjectPeer::UPDATEDON, $updatedon['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProjectPeer::UPDATEDON, $updatedon, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Project $project Object to remove from the list of results
     *
     * @return ProjectQuery The current query, for fluid interface
     */
    public function prune($project = null)
    {
        if ($project) {
            $this->addUsingAlias(ProjectPeer::ID, $project->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ProjectQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(ProjectPeer::UPDATEDON, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ProjectQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(ProjectPeer::UPDATEDON);
    }

    /**
     * Order by update date asc
     *
     * @return     ProjectQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(ProjectPeer::UPDATEDON);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ProjectQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(ProjectPeer::CREATEDON, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     ProjectQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(ProjectPeer::CREATEDON);
    }

    /**
     * Order by create date asc
     *
     * @return     ProjectQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(ProjectPeer::CREATEDON);
    }
}
