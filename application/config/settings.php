<?php
//Collections
define('ARTEFACT_COLLECTION', 'artefacts');
define('ARTEFACT_KEYS_COLLECTION', 'artefacts_keys');
define('FOREIGN_KEY_COLLECTION', 'foreignKeys');
define('FULLTEXT_COLLECTION', 'fulltext');
define('USER_COLLECTION', 'userdetails');

//Default Values
define('SHOW_ONLY_IF_DATA_EXISTS', True);
define('SHOW_PDF', True);
define('DEFAULT_TYPE', 'Letter');
define('MISCELLANEOUS_NAME', 'Miscellaneous');
define('FOREIGN_KEY_TYPE', 'ForeignKeyType');

// Lazy loading setting
define('PER_PAGE', 10);
define('FULLTEXT_SNIPPET_SIZE', 8);
define('PHOTO_FILE_EXT', '.JPG');

// External resource setting
define('EXTERNAL_RESOURCE', 'external.html');
define('EXTERNAL_RESOURCE_NOT_EXISTS', 'application/views/error/noExternalResource.php');

// user settings (login and registration)
define('REQUIRE_EMAIL_VALIDATION', False);//Set these values to True only
define('REQUIRE_RESET_PASSWORD', False);//if outbound mails can be sent from the server
define('REQUIRE_GIT_TRACKING', True);
define('REQUIRE_GITHUB_SYNC', False);

?>
