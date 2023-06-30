<?php

namespace Oloma\Php;

use Exception;
use RuntimeException;
use Laminas\I18n\Translator\TranslatorInterface;

/**
 * @author Oloma <support@oloma.dev>
 *
 * (c) 2023
 */
final class License
{
    /**
     * Configuration variables
     * @var array
     */
    private $config;

    /**
     * Laminas Translator
     * 
     * @var object
     */
    private $translator;

    /**
     * License verification server
     */
    const SERVER = 'https://license.oloma.dev';

    /**
     * Software protected version id
     * it can be change for newer versions
     */
    private const VERSION_ID = "942964805408537";

    /**
     * Constructor
     * 
     * @param  array  $config mezzio.global.php configuration array
     * @return void
     */
    public function __constructor(array $config)
    {
        $this->config = $config;
    }

    /**
     * Set translator object 
     * 
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns to translator object
     * 
     * @return TranslatorInterface
     */
    private function getTranslator() : TranslatorInterface
    {
        return $this->translator;
    }

    /**
     * Check hosted server is activated
     *
     * Don't worry about performance, this function works just once only
     * script executed
     * 
     * @return 
     */
    public function check()
    {
        try {
            // get id for name of cache
            $id = shmop_open(Self::getVersionId(), "a", 0, 0);  
            if ($id) {
                $val = shmop_read($id, 0, shmop_size($id));
            } else {
                return false;         // failed to load data
            }
            if ($val) {               // array retrieved
                shmop_close($id);
                return $val;
            } else {
                return false;         // failed to load data
            }

        } catch (Exception $e) {

        }
    }

    /**
     * Activate the license
     *
     * Don't worry about performance, this function only works once per server
     * 
     * @return int|bool
     */
	public function activate()
    {   
        $translator = $this->getTranslator();
        $lang = $translator->getLocale();
        var_dump($this->config);
        die;
        if (empty($this->config['license_key'])) {
            throw new RuntimeException(
                $translator->translate("License key cannot be empty")
            );
        }
        $data = array();
        $key = trim($this->config['license_key']);
        $headers = "Accept-language: $lang\r\n";

        // Create a stream
        $opts = array(
          'http' => array(
            'method' => "GET",
            'header' => $headers,
          )
        );
        $context = stream_context_create($opts);
        $response = file_get_contents(Self::SERVER."/?key=".$key.'&lang='.$lang, false, $context);

        if (is_string($response)) {
            $data = json_decode($response, true);
            if (! empty($data['success'])) {
                $id = shmop_open(Self::getVersionId(), "c", 0644, strlen(1)); // get id for name of cache
                if ($id) { // return int for data size or boolean false for fail
                    // shmop_write($id, 1, 0);
                    shmop_close($id);
                    return true;
                } else {
                    return false;
                }
            } else {
                throw new RuntimeException((string)$data['error']);
            }
        }
        throw new RuntimeException(
            $translator->translate("We are unable to establish a connection with the license verification server")
        );
	}

    /**
     * Returns to version id
     * 
     * @return string
     */
	private static function getVersionId()
    {
        return intval(Self::VERSION_ID);
	}

}