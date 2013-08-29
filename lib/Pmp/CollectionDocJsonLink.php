<?php
namespace Pmp;

require_once('CollectionDocJson.php');

class CollectionDocJsonLink
{
    /**
     * @param string $link
     *    the raw link data
     * @param CollectionDocJsonLinks $parent
     *    the links object that contains this link object
     */
    public function __construct($link, $parent) {
        $this->_links = $parent;

        // Access token must come from the main document that contains this link
        $this->accessToken = $this->_links->_document->accessToken;

        // Map the link properties to this object's properties
        $properties = get_object_vars($link);
        foreach($properties as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Follows the link href to retrieve a document
     * @return CollectionDocJson
     */
    public function follow() {
        $url = $this->href;

        // Retrieve the document at the other end of this URL
        $document = new CollectionDocJson($url, $this->accessToken);
        return $document;
    }

    /**
     * Follows the link href-template to retrieve a document
     * @param array $options
     *    the mapping of template parameter values
     * @return CollectionDocJson
     */
    public function submit(array $options) {
        $url = null;
        $template = $this->{'href-template'};

        /** @todo construct the URL from the template and given options */

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
}