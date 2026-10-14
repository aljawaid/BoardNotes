<?php

namespace Kanboard\Plugin\BoardNotes;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Security\Role;
use Kanboard\Core\Translator;

/**
 * Register BoardNotes models, views and access rules.
 *
 * Related test: tests/AuthorizationContractTest.php.
 */
class Plugin extends Base
{
    public function initialize()
    {
        $this->template->hook->attach('template:dashboard:sidebar', 'BoardNotes:dashboard/sidebar');
        $this->template->hook->attach('template:project:dropdown', 'BoardNotes:project/dropdown');
        $this->template->hook->attach('template:project-header:view-switcher', 'BoardNotes:project/header');

        $this->projectAccessMap->add('BoardNotesController', '*', Role::PROJECT_VIEWER);
        $this->projectAccessMap->add('BoardNotesController', 'boardNotesToTask', Role::PROJECT_MEMBER);
    }

    public function onStartup()
    {
        // Namespaced English strings provide a safe fallback without replacing Kanboard translations.
        Translator::load('en_US', __DIR__ . '/Locale');

        $language = $this->languageModel->getCurrentLanguage();
        if ($language !== 'en_US') {
            Translator::load($language, __DIR__ . '/Locale');
        }
    }

    public function getClasses()
    {
        return array(
            'Plugin\BoardNotes\Model' => array(
                'BoardNotesModel'
             )
         );
    }

    public function getPluginName()
    {
        return 'BoardNotes';
    }
    public function getPluginAuthor()
    {
        return 'TTJ';
    }
    public function getPluginVersion()
    {
        return '0.1.0';
    }
    public function getCompatibleVersion()
    {
        return '>=1.2.54';
    }
    public function getPluginDescription()
    {
        return t('boardnotes.description');
    }
    public function getPluginHomepage()
    {
        return 'https://github.com/aljawaid/BoardNotes';
    }
}
