<?php

declare(strict_types=1);

namespace Oloma\Php\Composer;

use RuntimeException;
use Composer\Installer\InstallerEvent;

/**
 * @author Oloma <support@oloma.dev>
 *
 * (c) 2023
 */
final class LicenseChecker
{
    const SERVER = 'https://license.oloma.dev';
    const PATH = '/check.php?key=';

    public static function check(InstallerEvent $event)
    {
        $ROOT = dirname(dirname(__DIR__));
        if (! file_exists($ROOT."/config/autoload/mezzio.global.php")) {
            throw new RuntimeException("Oloma.dev: We could not verify your license key because of the file '/config/autoload/mezzio.global.php' is missed.");
        }
        $config = require_once $ROOT."/config/autoload/mezzio.global.php";

        if (empty($config['license_key'])) {
            throw new RuntimeException("Oloma.dev: We could't verify your license key because of the license key is not defined in your '/config/autoload/mezzio.global.php' file.");   
        }
        $key = trim($config['license_key']);
        $headers = "Accept-language: en\r\n";

        // Create a stream
        $opts = array(
          'http' => array(
            'method' => "GET",
            'header' => $headers,
          )
        );
        $context = stream_context_create($opts);
        $response = file_get_contents(Self::SERVER.Self::PATH.$key, false, $context);

        if (is_string($response)) {
            $data = json_decode($response, true);
            if (! empty($data['success'])) {
                return true;
            } else {
                throw new RuntimeException((string)$data['error']);
                return false;
            }
        }
        throw new RuntimeException("Oloma.dev: We are unable to establish a connection with the license verification server. Please check your internet connection.");
    }


}
