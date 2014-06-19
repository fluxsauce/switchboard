<?php

namespace Fluxsauce\Brain\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Fluxsauce\Brain\Backup;
use Fluxsauce\Brain\BackupPeer;
use Fluxsauce\Brain\BackupQuery;
use Fluxsauce\Brain\Site;

/**
 * Base class that represents a query for the 'backup' table.
 *
 *
 *
 * @method BackupQuery orderById($order = Criteria::ASC) Order by the id column
 * @method BackupQuery orderBySiteid($order = Criteria::ASC) Order by the siteId column
 * @method BackupQuery orderByProjectid($order = Criteria::ASC) Order by the projectId column
 * @method BackupQuery orderByComponent($order = Criteria::ASC) Order by the component column
 * @method BackupQuery orderByPath($order = Criteria::ASC) Order by the path column
 * @method BackupQuery orderByCreatedon($order = Criteria::ASC) Order by the createdOn column
 * @method BackupQuery orderByUpdatedon($order = Criteria::ASC) Order by the updatedOn column
 *
 * @method BackupQuery groupById() Group by the id column
 * @method BackupQuery groupBySiteid() Group by the siteId column
 * @method BackupQuery groupByProjectid() Group by the projectId column
 * @method BackupQuery groupByComponent() Group by the component column
 * @method BackupQuery groupByPath() Group by the path column
 * @method BackupQuery groupByCreatedon() Group by the createdOn column
 * @method BackupQuery groupByUpdatedon() Group by the updatedOn column
 *
 * @method BackupQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method BackupQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method BackupQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method BackupQuery leftJoinSite($relationAlias = null) Adds a LEFT JOIN clause to the query using the Site relation
 * @method BackupQuery rightJoinSite($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Site relation
 * @method BackupQuery innerJoinSite($relationAlias = null) Adds a INNER JOIN clause to the query using the Site relation
 *
 * @method Backup findOne(PropelPDO $con = null) Return the first Backup matching the query
 * @method Backup findOneOrCreate(PropelPDO $con = null) Return the first Backup matching the query, or a new Backup object populated from the query conditions when no match is found
 *
 * @method Backup findOneBySiteid(int $siteId) Return the first Backup filtered by the siteId column
 * @method Backup findOneByProjectid(int $projectId) Return the first Backup filtered by the projectId column
 * @method Backup findOneByComponent(string $component) Return the first Backup filtered by the component column
 * @method Backup findOneByPath(string $path) Return the first Backup filtered by the path column
 * @method Backup findOneByCreatedon(string $createdOn) Return the first Backup filtered by the createdOn column
 * @method Backup findOneByUpdatedon(string $updatedOn) Return the first Backup filtered by the updatedOn column
 *
 * @method array findById(int $id) Return Backup objects filtered by the id column
 * @method array findBySiteid(int $siteId) Return Backup objects filtered by the siteId column
 * @method array findByProjectid(int $projectId) Return Backup objects filtered by the projectId column
 * @method array findByComponent(string $component) Return Backup objects filtered by the component column
 * @method array findByPath(string $path) Return Backup objects filtered by the path column
 * @method array findByCreatedon(string $createdOn) Return Backup objects filtered by the createdOn column
 * @method array findByUpdatedon(string $updatedOn) Return Backup objects filtered by the updatedOn column
 *
 * @package    propel.generator.brain.om
 */
abstract class BaseBackupQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseBackupQuery object.
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
            $modelName = 'Fluxsauce\\Brain\\Backup';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new BackupQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   BackupQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return BackupQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof BackupQuery) {
            return $criteria;
        }
        $query = new BackupQuery(null, null, $modelAlias);

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
     * @return   Backup|Backup[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = BackupPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(BackupPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Backup A model object, or null if the key is not found
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
     * @return                 Backup A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT [id], [siteId], [projectId], [component], [path], [createdOn], [updatedOn] FROM [backup] WHERE [id] = :p0';
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
            $obj = new Backup();
            $obj->hydrate($row);
            BackupPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Backup|Backup[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Backup[]|mixed the list of results, formatted by the current formatter
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
     * @return BackupQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(BackupPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return BackupQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(BackupPeer::ID, $keys, Criteria::IN);
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
     * @return BackupQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(BackupPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(BackupPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BackupPeer::ID, $id, $comparison);
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
     * @see       filterBySite()
     *
     * @param     mixed $siteid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BackupQuery The current query, for fluid interface
     */
    public function filterBySiteid($siteid = null, $comparison = null)
    {
        if (is_array($siteid)) {
            $useMinMax = false;
            if (isset($siteid['min'])) {
                $this->addUsingAlias(BackupPeer::SITEID, $siteid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($siteid['max'])) {
                $this->addUsingAlias(BackupPeer::SITEID, $siteid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BackupPeer::SITEID, $siteid, $comparison);
    }

    /**
     * Filter the query on the projectId column
     *
     * Example usage:
     * <code>
     * $query->filterByProjectid(1234); // WHERE projectId = 1234
     * $query->filterByProjectid(array(12, 34)); // WHERE projectId IN (12, 34)
     * $query->filterByProjectid(array('min' => 12)); // WHERE projectId >= 12
     * $query->filterByProjectid(array('max' => 12)); // WHERE projectId <= 12
     * </code>
     *
     * @param     mixed $projectid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BackupQuery The current query, for fluid interface
     */
    public function filterByProjectid($projectid = null, $comparison = null)
    {
        if (is_array($projectid)) {
            $useMinMax = false;
            if (isset($projectid['min'])) {
                $this->addUsingAlias(BackupPeer::PROJECTID, $projectid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($projectid['max'])) {
                $this->addUsingAlias(BackupPeer::PROJECTID, $projectid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BackupPeer::PROJECTID, $projectid, $comparison);
    }

    /**
     * Filter the query on the component column
     *
     * Example usage:
     * <code>
     * $query->filterByComponent('fooValue');   // WHERE component = 'fooValue'
     * $query->filterByComponent('%fooValue%'); // WHERE component LIKE '%fooValue%'
     * </code>
     *
     * @param     string $component The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BackupQuery The current query, for fluid interface
     */
    public function filterByComponent($component = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($component)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $component)) {
                $component = str_replace('*', '%', $component);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(BackupPeer::COMPONENT, $component, $comparison);
    }

    /**
     * Filter the query on the path column
     *
     * Example usage:
     * <code>
     * $query->filterByPath('fooValue');   // WHERE path = 'fooValue'
     * $query->filterByPath('%fooValue%'); // WHERE path LIKE '%fooValue%'
     * </code>
     *
     * @param     string $path The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BackupQuery The current query, for fluid interface
     */
    public function filterByPath($path = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($path)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $path)) {
                $path = str_replace('*', '%', $path);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(BackupPeer::PATH, $path, $comparison);
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
     * @return BackupQuery The current query, for fluid interface
     */
    public function filterByCreatedon($createdon = null, $comparison = null)
    {
        if (is_array($createdon)) {
            $useMinMax = false;
            if (isset($createdon['min'])) {
                $this->addUsingAlias(BackupPeer::CREATEDON, $createdon['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdon['max'])) {
                $this->addUsingAlias(BackupPeer::CREATEDON, $createdon['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BackupPeer::CREATEDON, $createdon, $comparison);
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
     * @return BackupQuery The current query, for fluid interface
     */
    public function filterByUpdatedon($updatedon = null, $comparison = null)
    {
        if (is_array($updatedon)) {
            $useMinMax = false;
            if (isset($updatedon['min'])) {
                $this->addUsingAlias(BackupPeer::UPDATEDON, $updatedon['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedon['max'])) {
                $this->addUsingAlias(BackupPeer::UPDATEDON, $updatedon['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BackupPeer::UPDATEDON, $updatedon, $comparison);
    }

    /**
     * Filter the query by a related Site object
     *
     * @param   Site|PropelObjectCollection $site The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 BackupQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterBySite($site, $comparison = null)
    {
        if ($site instanceof Site) {
            return $this
                ->addUsingAlias(BackupPeer::SITEID, $site->getId(), $comparison);
        } elseif ($site instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(BackupPeer::SITEID, $site->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySite() only accepts arguments of type Site or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Site relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return BackupQuery The current query, for fluid interface
     */
    public function joinSite($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Site');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Site');
        }

        return $this;
    }

    /**
     * Use the Site relation Site object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Fluxsauce\Brain\SiteQuery A secondary query class using the current class as primary query
     */
    public function useSiteQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinSite($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Site', '\Fluxsauce\Brain\SiteQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Backup $backup Object to remove from the list of results
     *
     * @return BackupQuery The current query, for fluid interface
     */
    public function prune($backup = null)
    {
        if ($backup) {
            $this->addUsingAlias(BackupPeer::ID, $backup->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     BackupQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(BackupPeer::UPDATEDON, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     BackupQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(BackupPeer::UPDATEDON);
    }

    /**
     * Order by update date asc
     *
     * @return     BackupQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(BackupPeer::UPDATEDON);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     BackupQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(BackupPeer::CREATEDON, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     BackupQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(BackupPeer::CREATEDON);
    }

    /**
     * Order by create date asc
     *
     * @return     BackupQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(BackupPeer::CREATEDON);
    }
}
