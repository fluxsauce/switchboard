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
use Fluxsauce\Brain\Environment;
use Fluxsauce\Brain\EnvironmentPeer;
use Fluxsauce\Brain\EnvironmentQuery;
use Fluxsauce\Brain\Site;

/**
 * Base class that represents a query for the 'environment' table.
 *
 *
 *
 * @method EnvironmentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method EnvironmentQuery orderBySiteid($order = Criteria::ASC) Order by the siteId column
 * @method EnvironmentQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method EnvironmentQuery orderByHost($order = Criteria::ASC) Order by the host column
 * @method EnvironmentQuery orderByUsername($order = Criteria::ASC) Order by the username column
 * @method EnvironmentQuery orderByBranch($order = Criteria::ASC) Order by the branch column
 * @method EnvironmentQuery orderByCreatedon($order = Criteria::ASC) Order by the createdOn column
 * @method EnvironmentQuery orderByUpdatedon($order = Criteria::ASC) Order by the updatedOn column
 *
 * @method EnvironmentQuery groupById() Group by the id column
 * @method EnvironmentQuery groupBySiteid() Group by the siteId column
 * @method EnvironmentQuery groupByName() Group by the name column
 * @method EnvironmentQuery groupByHost() Group by the host column
 * @method EnvironmentQuery groupByUsername() Group by the username column
 * @method EnvironmentQuery groupByBranch() Group by the branch column
 * @method EnvironmentQuery groupByCreatedon() Group by the createdOn column
 * @method EnvironmentQuery groupByUpdatedon() Group by the updatedOn column
 *
 * @method EnvironmentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method EnvironmentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method EnvironmentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method EnvironmentQuery leftJoinSite($relationAlias = null) Adds a LEFT JOIN clause to the query using the Site relation
 * @method EnvironmentQuery rightJoinSite($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Site relation
 * @method EnvironmentQuery innerJoinSite($relationAlias = null) Adds a INNER JOIN clause to the query using the Site relation
 *
 * @method Environment findOne(PropelPDO $con = null) Return the first Environment matching the query
 * @method Environment findOneOrCreate(PropelPDO $con = null) Return the first Environment matching the query, or a new Environment object populated from the query conditions when no match is found
 *
 * @method Environment findOneBySiteid(int $siteId) Return the first Environment filtered by the siteId column
 * @method Environment findOneByName(string $name) Return the first Environment filtered by the name column
 * @method Environment findOneByHost(string $host) Return the first Environment filtered by the host column
 * @method Environment findOneByUsername(string $username) Return the first Environment filtered by the username column
 * @method Environment findOneByBranch(string $branch) Return the first Environment filtered by the branch column
 * @method Environment findOneByCreatedon(string $createdOn) Return the first Environment filtered by the createdOn column
 * @method Environment findOneByUpdatedon(string $updatedOn) Return the first Environment filtered by the updatedOn column
 *
 * @method array findById(int $id) Return Environment objects filtered by the id column
 * @method array findBySiteid(int $siteId) Return Environment objects filtered by the siteId column
 * @method array findByName(string $name) Return Environment objects filtered by the name column
 * @method array findByHost(string $host) Return Environment objects filtered by the host column
 * @method array findByUsername(string $username) Return Environment objects filtered by the username column
 * @method array findByBranch(string $branch) Return Environment objects filtered by the branch column
 * @method array findByCreatedon(string $createdOn) Return Environment objects filtered by the createdOn column
 * @method array findByUpdatedon(string $updatedOn) Return Environment objects filtered by the updatedOn column
 *
 * @package    propel.generator.brain.om
 */
abstract class BaseEnvironmentQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseEnvironmentQuery object.
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
            $modelName = 'Fluxsauce\\Brain\\Environment';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new EnvironmentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   EnvironmentQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return EnvironmentQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof EnvironmentQuery) {
            return $criteria;
        }
        $query = new EnvironmentQuery(null, null, $modelAlias);

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
     * @return   Environment|Environment[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = EnvironmentPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(EnvironmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Environment A model object, or null if the key is not found
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
     * @return                 Environment A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT [id], [siteId], [name], [host], [username], [branch], [createdOn], [updatedOn] FROM [environment] WHERE [id] = :p0';
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
            $obj = new Environment();
            $obj->hydrate($row);
            EnvironmentPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Environment|Environment[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Environment[]|mixed the list of results, formatted by the current formatter
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
     * @return EnvironmentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(EnvironmentPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return EnvironmentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(EnvironmentPeer::ID, $keys, Criteria::IN);
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
     * @return EnvironmentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(EnvironmentPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(EnvironmentPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EnvironmentPeer::ID, $id, $comparison);
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
     * @return EnvironmentQuery The current query, for fluid interface
     */
    public function filterBySiteid($siteid = null, $comparison = null)
    {
        if (is_array($siteid)) {
            $useMinMax = false;
            if (isset($siteid['min'])) {
                $this->addUsingAlias(EnvironmentPeer::SITEID, $siteid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($siteid['max'])) {
                $this->addUsingAlias(EnvironmentPeer::SITEID, $siteid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EnvironmentPeer::SITEID, $siteid, $comparison);
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
     * @return EnvironmentQuery The current query, for fluid interface
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

        return $this->addUsingAlias(EnvironmentPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the host column
     *
     * Example usage:
     * <code>
     * $query->filterByHost('fooValue');   // WHERE host = 'fooValue'
     * $query->filterByHost('%fooValue%'); // WHERE host LIKE '%fooValue%'
     * </code>
     *
     * @param     string $host The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EnvironmentQuery The current query, for fluid interface
     */
    public function filterByHost($host = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($host)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $host)) {
                $host = str_replace('*', '%', $host);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EnvironmentPeer::HOST, $host, $comparison);
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
     * @return EnvironmentQuery The current query, for fluid interface
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

        return $this->addUsingAlias(EnvironmentPeer::USERNAME, $username, $comparison);
    }

    /**
     * Filter the query on the branch column
     *
     * Example usage:
     * <code>
     * $query->filterByBranch('fooValue');   // WHERE branch = 'fooValue'
     * $query->filterByBranch('%fooValue%'); // WHERE branch LIKE '%fooValue%'
     * </code>
     *
     * @param     string $branch The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EnvironmentQuery The current query, for fluid interface
     */
    public function filterByBranch($branch = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($branch)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $branch)) {
                $branch = str_replace('*', '%', $branch);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EnvironmentPeer::BRANCH, $branch, $comparison);
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
     * @return EnvironmentQuery The current query, for fluid interface
     */
    public function filterByCreatedon($createdon = null, $comparison = null)
    {
        if (is_array($createdon)) {
            $useMinMax = false;
            if (isset($createdon['min'])) {
                $this->addUsingAlias(EnvironmentPeer::CREATEDON, $createdon['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdon['max'])) {
                $this->addUsingAlias(EnvironmentPeer::CREATEDON, $createdon['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EnvironmentPeer::CREATEDON, $createdon, $comparison);
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
     * @return EnvironmentQuery The current query, for fluid interface
     */
    public function filterByUpdatedon($updatedon = null, $comparison = null)
    {
        if (is_array($updatedon)) {
            $useMinMax = false;
            if (isset($updatedon['min'])) {
                $this->addUsingAlias(EnvironmentPeer::UPDATEDON, $updatedon['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedon['max'])) {
                $this->addUsingAlias(EnvironmentPeer::UPDATEDON, $updatedon['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EnvironmentPeer::UPDATEDON, $updatedon, $comparison);
    }

    /**
     * Filter the query by a related Site object
     *
     * @param   Site|PropelObjectCollection $site The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 EnvironmentQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterBySite($site, $comparison = null)
    {
        if ($site instanceof Site) {
            return $this
                ->addUsingAlias(EnvironmentPeer::SITEID, $site->getId(), $comparison);
        } elseif ($site instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(EnvironmentPeer::SITEID, $site->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return EnvironmentQuery The current query, for fluid interface
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
     * @param   Environment $environment Object to remove from the list of results
     *
     * @return EnvironmentQuery The current query, for fluid interface
     */
    public function prune($environment = null)
    {
        if ($environment) {
            $this->addUsingAlias(EnvironmentPeer::ID, $environment->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     EnvironmentQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(EnvironmentPeer::UPDATEDON, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     EnvironmentQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(EnvironmentPeer::UPDATEDON);
    }

    /**
     * Order by update date asc
     *
     * @return     EnvironmentQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(EnvironmentPeer::UPDATEDON);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     EnvironmentQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(EnvironmentPeer::CREATEDON, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     EnvironmentQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(EnvironmentPeer::CREATEDON);
    }

    /**
     * Order by create date asc
     *
     * @return     EnvironmentQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(EnvironmentPeer::CREATEDON);
    }
}
