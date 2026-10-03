<?php

class AppDatabaseResult
{
    private static $legacyColumnNames = array(
        'categorydescription' => 'categoryDescription',
        'categoryname' => 'categoryName',
        'creationdate' => 'creationDate',
        'updationdate' => 'updationDate',
        'complaintnumber' => 'complaintNumber',
        'userid' => 'userId',
        'complainttype' => 'complaintType',
        'complaintdetails' => 'complaintDetails',
        'complaintfile' => 'complaintFile',
        'regdate' => 'regDate',
        'lastupdationdate' => 'lastUpdationDate',
        'remarkdate' => 'remarkDate',
        'statename' => 'stateName',
        'statedescription' => 'stateDescription',
        'postingdate' => 'postingDate',
        'useremail' => 'userEmail',
        'contactno' => 'contactNo',
        'fullname' => 'fullName',
        'userimage' => 'userImage',
        'logintime' => 'loginTime',
        'state' => 'State',
    );

    private $rows;
    private $position = 0;

    public function __construct(array $rows)
    {
        foreach ($rows as &$row) {
            foreach (self::$legacyColumnNames as $databaseName => $legacyName) {
                if (array_key_exists($databaseName, $row)) {
                    $row[$legacyName] = $row[$databaseName];
                }
            }
        }
        unset($row);

        $this->rows = $rows;
    }

    public function fetchArray()
    {
        if (!array_key_exists($this->position, $this->rows)) {
            return false;
        }

        return $this->rows[$this->position++];
    }

    public function rowCount()
    {
        return count($this->rows);
    }
}

function app_environment($name)
{
    $value = getenv($name);
    if ($value !== false && $value !== '') {
        return $value;
    }

    static $localEnvironment;
    if ($localEnvironment === null) {
        $environmentFile = __DIR__ . '/.env';
        $localEnvironment = is_file($environmentFile)
            ? parse_ini_file($environmentFile, false, INI_SCANNER_RAW)
            : array();
        if (!is_array($localEnvironment)) {
            $localEnvironment = array();
        }
    }

    return isset($localEnvironment[$name]) ? $localEnvironment[$name] : false;
}

function app_database()
{
    static $connection;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $databaseUrl = app_environment('SUPABASE_DB_URL') ?: app_environment('DATABASE_URL');
    if ($databaseUrl) {
        $parts = parse_url($databaseUrl);
        if ($parts === false || empty($parts['host']) || empty($parts['path'])) {
            throw new RuntimeException('The Supabase PostgreSQL connection URL is invalid.');
        }

        $query = array();
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $host = trim($parts['host'], '[]');
        $port = isset($parts['port']) ? (int) $parts['port'] : 5432;
        $database = ltrim($parts['path'], '/');
        $username = rawurldecode(isset($parts['user']) ? $parts['user'] : '');
        $password = rawurldecode(isset($parts['pass']) ? $parts['pass'] : '');
        $sslMode = isset($query['sslmode']) ? $query['sslmode'] : 'require';
    } else {
        $host = app_environment('SUPABASE_DB_HOST') ?: app_environment('DB_HOST') ?: 'localhost';
        $port = (int) (app_environment('SUPABASE_DB_PORT') ?: app_environment('DB_PORT') ?: 5432);
        $database = app_environment('SUPABASE_DB_NAME') ?: app_environment('DB_NAME') ?: 'postgres';
        $username = app_environment('SUPABASE_DB_USER') ?: app_environment('DB_USER') ?: 'postgres';
        $password = app_environment('SUPABASE_DB_PASSWORD') ?: app_environment('DB_PASS') ?: '';
        $sslMode = app_environment('SUPABASE_DB_SSLMODE') ?: 'require';
    }

    $dsn = 'pgsql:host=' . $host . ';port=' . $port . ';dbname=' . $database . ';sslmode=' . $sslMode;
    $connection = new PDO($dsn, $username, $password, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_BOTH,
        PDO::ATTR_EMULATE_PREPARES => false,
    ));

    return $connection;
}

function app_db_query(PDO $connection, $sql)
{
    try {
        $statement = $connection->query($sql);

        if ($statement->columnCount() > 0) {
            return new AppDatabaseResult($statement->fetchAll(PDO::FETCH_BOTH));
        }

        return true;
    } catch (PDOException $exception) {
        error_log('Database query failed: ' . $exception->getMessage());
        return false;
    }
}

function app_db_fetch_array(AppDatabaseResult $result)
{
    return $result->fetchArray();
}

function app_db_num_rows(AppDatabaseResult $result)
{
    return $result->rowCount();
}