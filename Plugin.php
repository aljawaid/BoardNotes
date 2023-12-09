<?php

namespace Kanboard\Plugin\BoardNotes;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
    $this->setContentSecurityPolicy(array('default-src' => '* \'unsafe-inline\' \'unsafe-eval\''));
    $this->template->hook->attach('template:dashboard:sidebar', 'BoardNotes:dashboard/sidebar');
	$this->template->hook->attach('template:project:dropdown', 'BoardNotes:project/dropdown');
	$this->template->hook->attach('template:project-header:view-switcher', 'BoardNotes:project/header');
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
        return '0.0.6';
    }
    public function getPluginDescription()
    {
        return 'Keep notes on every single projects. Notes which is not suitable for creating board tasks.';
    }
    public function getPluginHomepage()
    {
        return '';
    }
}
