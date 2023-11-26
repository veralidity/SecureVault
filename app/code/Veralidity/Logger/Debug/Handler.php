<?php
/**
 * Veralidity, LLC
 *
 * @package    Veralidity
 * @category   SecureVault
 * @copyright  Copyright © 2023 Veralidity, LLC
 * @license    https://www.veralidity.com/license/
 * @author     Veralidity, LLC <veralidity@protonmail.com>
 */

namespace Veralidity\SecureVault\Logger\Debug;

/**
 * Handler for logging Whitelisted IPs
 */
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     *
     * @var string
     */
    protected $fileName = '/var/log/veralidity-securevault-debug.log';
}