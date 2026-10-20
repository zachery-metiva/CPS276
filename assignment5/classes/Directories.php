<?php
/**
 * Directories class
 * Handles creating a folder under directories/ and writing a readme.txt file inside it.
 */
class Directories
{
    /** Absolute path to the base "directories" folder on the server */
    private $basePath;

    /** Path used to build the link shown to the user (relative to index.php) */
    private $baseUrl;

    /** Permission for new folders. 0755 = owner read/write/execute, everyone else read/execute. */
    private $permissions = 0755;

    public function __construct($basePath = null, $baseUrl = 'directories')
    {
        // Default to the "directories" folder that sits next to the "classes" folder
        $this->basePath = $basePath ?? dirname(__DIR__) . '/directories';
        $this->baseUrl  = $baseUrl;
    }

    /**
     * Creates the folder and readme.txt file.
     * Returns an array: ['success' => bool, 'message' => string, 'link' => string|null]
     */
    public function createDirectoryAndFile($folderName, $content)
    {
        $folderName = trim($folderName);

        // 1. Validate the folder name (letters and numbers only)
        if ($folderName === '' || !ctype_alnum($folderName)) {
            return $this->result(false, 'Folder names must contain alphanumeric characters only.');
        }

        $dirPath = $this->basePath . '/' . $folderName;

        // 2. Check whether the directory already exists before creating it
        if (file_exists($dirPath)) {
            return $this->result(false, 'A directory already exists with that name.');
        }

        // 3. Create the directory with a safe permission
        if (!@mkdir($dirPath, $this->permissions)) {
            return $this->result(false, 'The directory could not be created.');
        }

        // 4. Open the file, write the content, and close the file
        $filePath = $dirPath . '/readme.txt';
        $handle = @fopen($filePath, 'w');

        if ($handle === false) {
            return $this->result(false, 'The file could not be created.');
        }

        $written = fwrite($handle, $content);
        fclose($handle);

        if ($written === false) {
            return $this->result(false, 'The file could not be written.');
        }

        $link = $this->baseUrl . '/' . $folderName . '/readme.txt';
        return $this->result(true, 'File and directory were created', $link);
    }

    private function result($success, $message, $link = null)
    {
        return ['success' => $success, 'message' => $message, 'link' => $link];
    }
}
