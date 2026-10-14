<?php

// Translation loading is covered by tests/TranslationContractTest.php.
$translations = require __DIR__ . '/../en_US/translations.php';
$translations['boardnotes.empty_analytics'] = 'There are no notes to analyse.';

return $translations;
