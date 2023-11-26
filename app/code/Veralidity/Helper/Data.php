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

namespace SecureVault\Helper;

use Magento\Framework\Encryption\EncryptorInterface;
use Psr\Log\LoggerInterface;

class Data
{
    /**
     * @var EncryptorInterface
     * The EncryptorInterface instance for encryption and decryption.
     */
    protected $encryptor;

    /**
     * @var LoggerInterface
     * The logger instance for logging errors and information.
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param EncryptorInterface $encryptor
     * @param LoggerInterface $logger
     */
    public function __construct(
        EncryptorInterface $encryptor,
        LoggerInterface $logger
    ) {
        $this->encryptor = $encryptor;
        $this->logger = $logger;
    }

    /**
     * Encrypt data
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        try {
            return $this->encryptor->encrypt($data);
        } catch (\Exception $e) {
            $this->logger->error('Encryption error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Decrypt data
     *
     * @param string $encryptedData
     * @return string
     */
    public function decrypt($encryptedData)
    {
        try {
            return $this->encryptor->decrypt($encryptedData);
        } catch (\Exception $e) {
            $this->logger->error('Decryption error: ' . $e->getMessage());
            return '';
        }
    }
}