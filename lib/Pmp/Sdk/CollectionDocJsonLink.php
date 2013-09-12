<?php
namespace Pmp\Sdk;

require_once('CollectionDocJson.php');

require_once(dirname(__FILE__) . '/../../guzzle.phar');

use Guzzle\Parser\UriTemplate\UriTemplate as UriTemplate;

class CollectionDocJsonLink
{
    private $accessToken;

    /**
     * @param string $link
     *    the raw link data
     * @param string $accessToken
     *    access token for the API
     */
    public function __construct($link, $accessToken) {
        $this->accessToken = $accessToken;

        // Map the link properties to this object's properties
        if (is_object($link)) {
            $properties = get_object_vars($link);
        } else {
            $properties = array();
        }

        foreach($properties as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Follows the link href to retrieve a document
     * @return CollectionDocJson
     */
    public function follow() {
        // Retrieve the document at the other end of this URL
        $document = new CollectionDocJson($this->href, $this->accessToken);
        return $document;
    }

    /**
     * Follows the link href-template to retrieve a document
     * @param array $options
     *    the mapping of template parameter values
     * @return CollectionDocJson
     */
    public function submit(array $options) {
        // Generate the URL from the template
        $parser = new UriTemplate();
        $url = $parser->expand($this->{'href-template'}, $this->convertOptions($options));

        // Retrieve the document at the other end of this constructed URL
        $document = new CollectionDocJson($url, $this->accessToken);
        return $document;
    }

    /**
     * Converts the given option set into API-compatible query string form
     * @param array $option
     *    the mapping of a template parameter to values
     * @return string
     */
    private function convertOption(array $option) {
        if (!empty($option['AND'])) {
            return implode(',', $option['AND']);
        } else if (!empty($option['OR'])) {
            return implode(';', $option['OR']);
        } else {
            return '';
        }
    }

    /**
     * Converts the set of options into API-compatible query string forms
     * @param array $options
     * @return array
     */
    private function convertOptions(array $options) {
        $converted = array();
        foreach ($options as $name => $value) {
            if (is_array($value)) {
                $converted[$name] = $this->convertOption($value);
            } else {
                $converted[$name] = $value;
            }
        }
        return $converted;
    }
}