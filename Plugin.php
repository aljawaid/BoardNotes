<?php

namespace Kanboard\Plugin\Boardnotes;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
    $this->setContentSecurityPolicy(array('default-src' => '* \'unsafe-inline\' \'unsafe-eval\''));
	$this->template->hook->attach('template:dashboard:sidebar', 'Boardnotes:boardnotes/dashboardsidebar');
	$this->template->hook->attach('template:project:dropdown', 'Boardnotes:boardnotes/dropdown');
	$this->template->hook->attach('template:project:sidebar', 'Boardnotes:boardnotes/sidebar');

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
        return 'Boardnotes';
    }
    public function getPluginAuthor()
    {
        return 'TTJ';
    }
    public function getPluginVersion()
    {
        return '0.0.5';
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
