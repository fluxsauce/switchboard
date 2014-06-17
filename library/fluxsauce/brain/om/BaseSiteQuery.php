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
use Fluxsauce\Brain\Site;
use Fluxsauce\Brain\SitePeer;
use Fluxsauce\Brain\SiteQuery;

/**
 * Base class that represents a query for the 'site' table.
 *
 *
 *
 * @method SiteQuery orderById($order = Criteria::ASC) Order by the id column
 * @method SiteQuery orderByProvider($order = Criteria::ASC) Order by the provider column
 * @method SiteQuery orderByUuid($order = Criteria::ASC) Order by the uuid column
 * @method SiteQuery orderByRealm($order = Criteria::ASC) Order by the realm column
 * @method SiteQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method SiteQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method SiteQuery orderByVcsurl($order = Criteria::ASC) Order by the vcsUrl column
 * @method SiteQuery orderByVcstype($order = Criteria::ASC) Order by the vcsType column
 * @method SiteQuery orderByVcsprotocol($order = Criteria::ASC) Order by the vcsProtocol column
 * @method SiteQuery orderBySshport($order = Criteria::ASC) Order by the sshPort column
 * @method SiteQuery orderByCreatedon($order = Criteria::ASC) Order by the createdOn column
 * @method SiteQuery orderByUpdatedon($order = Criteria::ASC) Order by the updatedOn column
 *
 * @method SiteQuery groupById() Group by the id column
 * @method SiteQuery groupByProvider() Group by the provider column
 * @method SiteQuery groupByUuid() Group by the uuid column
 * @method SiteQuery groupByRealm() Group by the realm column
 * @method SiteQuery groupByName() Group by the name column
 * @method SiteQuery groupByTitle() Group by the title column
 * @method SiteQuery groupByVcsurl() Group by the vcsUrl column
 * @method SiteQuery groupByVcstype() Group by the vcsType column
 * @method SiteQuery groupByVcsprotocol() Group by the vcsProtocol column
 * @method SiteQuery groupBySshport() Group by the sshPort column
 * @method SiteQuery groupByCreatedon() Group by the createdOn column
 * @method SiteQuery groupByUpdatedon() Group by the updatedOn column
 *
 * @method SiteQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method SiteQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method SiteQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Site findOne(PropelPDO $con = null) Return the first Site matching the query
 * @method Site findOneOrCreate(PropelPDO $con = null) Return the first Site matching the query, or a new Site object populated from the query conditions when no match is found
 *
 * @method Site findOneByProvider(string $provider) Return the first Site filtered by the provider column
 * @method Site findOneByUuid(string $uuid) Return the first Site filtered by the uuid column
 * @method Site findOneByRealm(string $realm) Return the first Site filtered by the realm column
 * @method Site findOneByName(string $name) Return the first Site filtered by the name column
 * @method Site findOneByTitle(string $title) Return the first Site filtered by the title column
 * @method Site findOneByVcsurl(string $vcsUrl) Return the first Site filtered by the vcsUrl column
 * @method Site findOneByVcstype(string $vcsType) Return the first Site filtered by the vcsType column
 * @method Site findOneByVcsprotocol(string $vcsProtocol) Return the first Site filtered by the vcsProtocol column
 * @method Site findOneBySshport(int $sshPort) Return the first Site filtered by the sshPort column
 * @method Site findOneByCreatedon(string $createdOn) Return the first Site filtered by the createdOn column
 * @method Site findOneByUpdatedon(string $updatedOn) Return the first Site filtered by the updatedOn column
 *
 * @method array findById(int $id) Return Site objects filtered by the id column
 * @method array findByProvider(string $provider) Return Site objects filtered by the provider column
 * @method array findByUuid(string $uuid) Return Site objects filtered by the uuid column
 * @method array findByRealm(string $realm) Return Site objects filtered by the realm column
 * @method array findByName(string $name) Return Site objects filtered by the name column
 * @method array findByTitle(string $title) Return Site objects filtered by the title column
 * @method array findByVcsurl(string $vcsUrl) Return Site objects filtered by the vcsUrl column
 * @method array findByVcstype(string $vcsType) Return Site objects filtered by the vcsType column
 * @method array findByVcsprotocol(string $vcsProtocol) Return Site objects filtered by the vcsProtocol column
 * @method array findBySshport(int $sshPort) Return Site objects filtered by the sshPort column
 * @method array findByCreatedon(string $createdOn) Return Site objects filtered by the createdOn column
 * @method array findByUpdatedon(string $updatedOn) Return Site objects filtered by the updatedOn column
 *
 * @package    propel.generator.brain.om
 */
abstract class BaseSiteQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseSiteQuery object.
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
            $modelName = 'Fluxsauce\\Brain\\Site';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SiteQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   SiteQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SiteQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SiteQuery) {
            return $criteria;
        }
        $query = new SiteQuery(null, null, $modelAlias);

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
     * @return   Site|Site[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SitePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SitePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Site A model object, or null if the key is not found
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
     * @return                 Site A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT [id], [provider], [uuid], [realm], [name], [title], [vcsUrl], [vcsType], [vcsProtocol], [sshPort], [createdOn], [updatedOn] FROM [site] WHERE [id] = :p0';
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
            $obj = new Site();
            $obj->hydrate($row);
            SitePeer::addInstanceToPool($obj, (string) $key);
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
     * @return Site|Site[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Site[]|mixed the list of results, formatted by the current formatter
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
     * @return SiteQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SitePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SiteQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SitePeer::ID, $keys, Criteria::IN);
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
     * @return SiteQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SitePeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SitePeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SitePeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the provider column
     *
     * Example usage:
     * <code>
     * $query->filterByProvider('fooValue');   // WHERE provider = 'fooValue'
     * $query->filterByProvider('%fooValue%'); // WHERE provider LIKE '%fooValue%'
     * </code>
     *
     * @param     string $provider The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SiteQuery The current query, for fluid interface
     */
    public function filterByProvider($provider = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($provider)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $provider)) {
                $provider = str_replace('*', '%', $provider);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitePeer::PROVIDER, $provider, $comparison);
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
     * @return SiteQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SitePeer::UUID, $uuid, $comparison);
    }

    /**
     * Filter the query on the realm column
     *
     * Example usage:
     * <code>
     * $query->filterByRealm('fooValue');   // WHERE realm = 'fooValue'
     * $query->filterByRealm('%fooValue%'); // WHERE realm LIKE '%fooValue%'
     * </code>
     *
     * @param     string $realm The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SiteQuery The current query, for fluid interface
     */
    public function filterByRealm($realm = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($realm)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $realm)) {
                $realm = str_replace('*', '%', $realm);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitePeer::REALM, $realm, $comparison);
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
     * @return SiteQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SitePeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SiteQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $title)) {
                $title = str_replace('*', '%', $title);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitePeer::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the vcsUrl column
     *
     * Example usage:
     * <code>
     * $query->filterByVcsurl('fooValue');   // WHERE vcsUrl = 'fooValue'
     * $query->filterByVcsurl('%fooValue%'); // WHERE vcsUrl LIKE '%fooValue%'
     * </code>
     *
     * @param     string $vcsurl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SiteQuery The current query, for fluid interface
     */
    public function filterByVcsurl($vcsurl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($vcsurl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $vcsurl)) {
                $vcsurl = str_replace('*', '%', $vcsurl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitePeer::VCSURL, $vcsurl, $comparison);
    }

    /**
     * Filter the query on the vcsType column
     *
     * Example usage:
     * <code>
     * $query->filterByVcstype('fooValue');   // WHERE vcsType = 'fooValue'
     * $query->filterByVcstype('%fooValue%'); // WHERE vcsType LIKE '%fooValue%'
     * </code>
     *
     * @param     string $vcstype The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SiteQuery The current query, for fluid interface
     */
    public function filterByVcstype($vcstype = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($vcstype)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $vcstype)) {
                $vcstype = str_replace('*', '%', $vcstype);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitePeer::VCSTYPE, $vcstype, $comparison);
    }

    /**
     * Filter the query on the vcsProtocol column
     *
     * Example usage:
     * <code>
     * $query->filterByVcsprotocol('fooValue');   // WHERE vcsProtocol = 'fooValue'
     * $query->filterByVcsprotocol('%fooValue%'); // WHERE vcsProtocol LIKE '%fooValue%'
     * </code>
     *
     * @param     string $vcsprotocol The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SiteQuery The current query, for fluid interface
     */
    public function filterByVcsprotocol($vcsprotocol = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($vcsprotocol)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $vcsprotocol)) {
                $vcsprotocol = str_replace('*', '%', $vcsprotocol);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitePeer::VCSPROTOCOL, $vcsprotocol, $comparison);
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
     * @return SiteQuery The current query, for fluid interface
     */
    public function filterBySshport($sshport = null, $comparison = null)
    {
        if (is_array($sshport)) {
            $useMinMax = false;
            if (isset($sshport['min'])) {
                $this->addUsingAlias(SitePeer::SSHPORT, $sshport['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sshport['max'])) {
                $this->addUsingAlias(SitePeer::SSHPORT, $sshport['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SitePeer::SSHPORT, $sshport, $comparison);
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
     * @return SiteQuery The current query, for fluid interface
     */
    public function filterByCreatedon($createdon = null, $comparison = null)
    {
        if (is_array($createdon)) {
            $useMinMax = false;
            if (isset($createdon['min'])) {
                $this->addUsingAlias(SitePeer::CREATEDON, $createdon['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdon['max'])) {
                $this->addUsingAlias(SitePeer::CREATEDON, $createdon['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SitePeer::CREATEDON, $createdon, $comparison);
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
     * @return SiteQuery The current query, for fluid interface
     */
    public function filterByUpdatedon($updatedon = null, $comparison = null)
    {
        if (is_array($updatedon)) {
            $useMinMax = false;
            if (isset($updatedon['min'])) {
                $this->addUsingAlias(SitePeer::UPDATEDON, $updatedon['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedon['max'])) {
                $this->addUsingAlias(SitePeer::UPDATEDON, $updatedon['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SitePeer::UPDATEDON, $updatedon, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Site $site Object to remove from the list of results
     *
     * @return SiteQuery The current query, for fluid interface
     */
    public function prune($site = null)
    {
        if ($site) {
            $this->addUsingAlias(SitePeer::ID, $site->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     SiteQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(SitePeer::UPDATEDON, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     SiteQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(SitePeer::UPDATEDON);
    }

    /**
     * Order by update date asc
     *
     * @return     SiteQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(SitePeer::UPDATEDON);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     SiteQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(SitePeer::CREATEDON, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     SiteQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(SitePeer::CREATEDON);
    }

    /**
     * Order by create date asc
     *
     * @return     SiteQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(SitePeer::CREATEDON);
    }
}
