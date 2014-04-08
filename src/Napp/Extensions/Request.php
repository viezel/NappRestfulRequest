<?php namespace Napp\Extensions;

use \Illuminate\Http\Request as LaravelRequest;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\FileBag;

class Request extends LaravelRequest {

	/**
     * Creates a new request with values from PHP's super globals, or by analyzing the request content if the
     * request method is PUT, DELETE or PATCH.
     *
     * @return  Request A new request
     */
    public static function createFromGlobals()
    {
        $request = new static($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);

        if (in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))) {
            $contentType = $request->headers->get('CONTENT_TYPE');

            if (0 === strpos($contentType, 'application/x-www-form-urlencoded')) {
                parse_str($request->getContent(), $data);
                $request->request = new ParameterBag($data);
            } elseif (0 === strpos($contentType, 'multipart/form-data')) {
                $request->decodeMultiPartFormData();
            }
        }

        return $request;
    }

    /**
     * Decodes a multipart/form-data request, and injects the data into the request object. This method is used
     * whenever a multipart/form-data request is submitted with a PUT, DELETE or PATCH method.
     *
     * This implementation is based on an example by netcoder at http://stackoverflow.com/a/9469615.
     * 
     */
    protected function decodeMultiPartFormData()
    {
        /**
         * The files array is structured such that it mimics PHP's $_FILES superglobal, allowing it to be passed on
         * to the Request constructor.
         * @var array
         */
        $files = array();

        /**
         * Key/value pairs of decoded form-data
         * @var array
         */
        $data = array();

        // Fetch content and determine boundary
        $rawData = $this->getContent();
        if ($rawData) {
            $boundary = substr($rawData, 0, strpos($rawData, "\r\n"));

            // Fetch and process each part
            $parts = array_slice(explode($boundary, $rawData), 1);
            foreach ($parts as $part) {
                // If this is the last part, break
                if ($part == "--\r\n") {
                    break;
                }

                // Separate content from headers
                $part = ltrim($part, "\r\n");
                list($rawHeaders, $content) = explode("\r\n\r\n", $part, 2);
                $content = substr($content, 0, strlen($content) - 2);

                // Parse the headers list
                $rawHeaders = explode("\r\n", $rawHeaders);
                $headers = array();
                foreach ($rawHeaders as $header) {
                    list($name, $value) = explode(':', $header, 2);
                    $headers[strtolower($name)] = ltrim($value, ' ');
                }

                // Parse the Content-Disposition to get the field name, etc.
                if (isset($headers['content-disposition'])) {
                    $filename = null;
                    preg_match(
                        '/^form-data; *name="([^"]+)"(?:; *filename="([^"]+)")?/',
                        $headers['content-disposition'],
                        $matches
                    );

                    $fieldName = $matches[1];
                    $fileName = (isset($matches[2]) ? $matches[2] : null);

                    // If we have no filename, save the data. Otherwise, save the file.
                    if ($fileName === null) {
                        $data[$fieldName] = $content;
                    } else {
                        $localFileName = tempnam(sys_get_temp_dir(), 'sfy');
                        file_put_contents($localFileName, $content);

                        $files[$fieldName] = array(
                            'name' => $fileName,
                            'type' => $headers['content-type'],
                            'tmp_name' => $localFileName,
                            'error' => 0,
                            'size' => filesize($localFileName)
                        );

                        // Record the file path so that it can be verified as an uploaded file later
                        UploadedFile::$files[] = $localFileName;

                        // If the uploaded file is not moved, we need to delete it. To do that, we
                        // register a shutdown function to cleanup the temporary file
                        register_shutdown_function(function () use ($localFileName) {
                           @unlink($localFileName);
                        });
                    }
                }
            }
        }

        $this->request = new ParameterBag($data);
        $this->files = new FileBag($files);
    }

}