<?php
/**
 * Mitchell Robles, Jr.
 *
 * @package    Veralidity
 * @category   SecureVault
 * @copyright  Copyright © 2023 Mitchell Robles, Jr.
 * @license    https://www.veralidity.com/license/
 * @author     Mitchell Robles, Jr. <mitchroblesjr@gmail.com>
 */

namespace Veralidity\SecureVault\Model\Db\Adapter;

use Magento\Framework\DB\Adapter\Pdo\Mysql as MagentoMysql;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\DB\LoggerInterface as DbLoggerInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\DB\SelectFactory;
use Magento\Framework\Serialize\SerializerInterface;

class PdoMysql extends MagentoMysql
{
    /**
     * @var EncryptorInterface
     * The encryptor instance used for encrypting and decrypting sensitive data.
     */
    protected $encryptor;

    /**
     * @var DbLoggerInterface
     * The logger instance used for logging errors and important information.
     */
    protected $dbLogger;

    /**
     * @var array
     * An associative array mapping table names to their respective sensitive fields.
     * This array is used to determine which fields need to be encrypted or decrypted.
     * Each key is a table name, and its value is an array of sensitive field names in that table.
     */
    protected $sensitiveFields = [
        'customer_entity' => [
            'firstname',
            'middlename',
            'lastname',
            //'email',
            'dob',
            'telephone'
        ],
        'customer_address_entity' => [
            'firstname',
            'middlename',
            'lastname',
            'telephone',
            'company',
            'street',
            'city',
            'postcode',
            'region',
            //'country_id',
            'vat_id'
        ]
    ];

    /**
     * CustomPdoMysql constructor.
     * Initializes the custom PDO adapter with necessary dependencies.
     *
     * @param EncryptorInterface $encryptor Encryption service instance.
     * @param LoggerInterface $logger Logger service instance.
     * @param array $config Database configuration array.
     * @param null|DbLoggerInterface $logger Optional logger for additional logging.
     * @param null|Zend_Db_Profiler $profiler Optional profiler instance.
     * @param string $varName Optional variable name for profiler.
     * @param array $configOptions Optional configuration options.
     * @param array $driverOptions Optional driver options.
     */
    public function __construct(
        StringUtils $string,
        DateTime $dateTime,
        DbLoggerInterface $dbLogger,
        SelectFactory $selectFactory,
        EncryptorInterface $encryptor,
        array $config = [],
        SerializerInterface $serializer = null
    ) {
        $this->encryptor = $encryptor;
        $this->dbLogger = $dbLogger;
        parent::__construct($string, $dateTime, $dbLogger, $selectFactory, $config, $serializer);
    }

    /**
     * Fetches a single row from the database, decrypting sensitive fields if necessary.
     *
     * @param string|Zend_Db_Select $sql The SQL query or Zend_Db_Select object.
     * @param array $bind An array of data to bind to the placeholders in the SQL query.
     * @param null|int $fetchMode The fetch mode to use.
     * @return array|false The resulting data array or false on failure.
     */
    public function fetchRow($sql, $bind = [], $fetchMode = null)
    {
        $row = parent::fetchRow($sql, $bind, $fetchMode);
        if ($row && $this->isSensitiveQuery($sql)) {
            $row = $this->decryptSensitiveData($row);
        }
        return $row;
    }

    /**
     * Inserts data into a table, encrypting sensitive fields if necessary.
     *
     * @param string $table The name of the table to insert data into.
     * @param array $bind Associative array of data to insert.
     * @return int The number of affected rows.
     */
    public function insert($table, array $bind)
    {
        if ($this->isSensitiveTable($table)) {
            $bind = $this->encryptSensitiveData($table, $bind);
        }
        return parent::insert($table, $bind);
    }

    /**
     * Updates data in a table, encrypting sensitive fields if necessary.
     *
     * @param string $table The name of the table to update data in.
     * @param array $bind Associative array of data to update.
     * @param string|array $where SQL WHERE clause(s).
     * @return int The number of affected rows.
     */
    public function update($table, array $bind, $where = '')
    {
        if ($this->isSensitiveTable($table)) {
            $bind = $this->encryptSensitiveData($table, $bind);
        }
        return parent::update($table, $bind, $where);
    }

    /**
     * Fetches all SQL query results, decrypting sensitive fields if necessary.
     *
     * @param string|Zend_Db_Select $sql The SQL query or Zend_Db_Select object.
     * @param array $bind An array of data to bind to the placeholders in the SQL query.
     * @param null|int $fetchMode The fetch mode to use.
     * @return array The resulting data array.
     */
    public function fetchAll($sql, $bind = [], $fetchMode = null)
    {
        $result = parent::fetchAll($sql, $bind, $fetchMode);
        if ($this->isSensitiveQuery($sql)) {
            $result = array_map([$this, 'decryptSensitiveData'], $result);
        }
        return $result;
    }

    /**
     * Encrypts sensitive data fields based on the table and field configuration.
     *
     * @param string $table The name of the table containing sensitive fields.
     * @param array $data The data array where sensitive fields need to be encrypted.
     * @return array The data array with encrypted fields.
     */
    protected function encryptSensitiveData($table, array $data)
    {
        try {
            foreach ($this->sensitiveFields[$table] as $field) {
                if (isset($data[$field]) && is_string($data[$field])) {
                    $data[$field] = $this->encryptor->encrypt($data[$field]);
                } elseif ($data instanceof Zend_Db_Expr) {
                    // Handle Zend_Db_Expr data type
                    // Convert or process the expression as needed
                    $processedData = $this->processZendDbExpr($data);
                    return $this->encryptor->encrypt($processedData);
                }
            }
        } catch (\Exception $e) {
            $this->logError($e, $data, 'encrypt');
            // Return original data to proceed with unencrypted saving
            return $data;
        }
        return $data;
    }

    private function processZendDbExpr($expr)
    {
        // Implement the logic to process Zend_Db_Expr
        // For example, convert it to a string or evaluate the expression
        // Return the processed data
        return $expr->toString();
    }

    /**
     * Decrypts sensitive data fields.
     *
     * @param array $data The data array containing encrypted fields.
     * @return array The data array with decrypted fields.
     */
    protected function decryptSensitiveData(array $data)
    {
        try {
            foreach ($data as $key => $value) {
                $this->dbLogger->log($value);
                if ($this->isFieldSensitive($key, $data)) {
                    $data[$key] = $this->encryptor->decrypt($value);
                }
            }
        } catch (\Exception $e) {
            $this->logError($e, $data, 'decrypt');
            // Return original data to proceed with unencrypted fetching
            return $data;
        }
        return $data;
    }

    /**
     * Logs an error encountered during encryption or decryption.
     *
     * @param \Exception $e The exception object containing error details.
     * @param mixed $data The data involved in the operation that caused the error.
     * @param string $operation The type of operation ('encrypt' or 'decrypt').
     */
    protected function logError(\Exception $e, $data, $operation)
    {
        $errorMessage = "Error during {$operation}: " . $e->getMessage();
        $this->logger->error($errorMessage, ['data' => $data]);

        // Save error details to a custom database table for admin review
        // Example: $this->saveErrorDetailsToDatabase($errorMessage, $data);
    }

    /**
     * Checks if a given table contains sensitive fields.
     *
     * @param string $table The name of the table to check.
     * @return bool True if the table contains sensitive fields, false otherwise.
     */
    protected function isSensitiveTable($table)
    {
        return in_array($table, array_keys($this->sensitiveFields));
    }

    /**
     * Determines if the SQL query involves a table with sensitive fields.
     *
     * @param string $sql The SQL query string.
     * @return bool True if the query involves a sensitive table, false otherwise.
     */
    protected function isSensitiveQuery($sql)
    {
        foreach (array_keys($this->sensitiveFields) as $table) {
            if (strpos($sql, $table) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if a given field is sensitive based on the table and field configuration.
     *
     * @param string $field The field name to check.
     * @param array $data The data array to use for determining the table.
     * @return bool True if the field is sensitive, false otherwise.
     */
    protected function isFieldSensitive($field, $data)
    {
        $table = $this->getTableFromData($data);
        return $table && in_array($field, $this->sensitiveFields[$table]);
    }

    /**
     * Determines the table name based on the data array.
     *
     * @param array $data The data array used to infer the table name.
     * @return string|null The inferred table name, or null if not identifiable.
     */
    protected function getTableFromData($data)
    {
        foreach ($this->sensitiveFields as $table => $fields) {
            if (!empty(array_intersect(array_keys($data), $fields))) {
                return $table;
            }
        }
        // foreach ($this->sensitiveFields as $table => $fields) {
        //     foreach ($fields as $field) {
        //         if (isset($data[$field])) {
        //             return $table;
        //         }
        //     }
        // }
        return null; // Return null if the table is not identified
    }
}
