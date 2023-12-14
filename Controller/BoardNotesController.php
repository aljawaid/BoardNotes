<?php

namespace Kanboard\Plugin\BoardNotes\Controller;

use Kanboard\Controller\BaseController;

class BoardNotesController extends BaseController
{

    private function resolveUserId()
    {
        $user_id = ''; // init empty string
        $use_cached = $this->request->getStringParam('use_cached');

        if (!empty($use_cached) && isset($_SESSION['cached_user_id'])) // use cached
        {
            $user_id = $_SESSION['cached_user_id'];
        }

        if (empty($user_id)) // try get param from URL
        {
            $user_id = $this->request->getStringParam('user_id');
        }

        if (empty($user_id)) // as last resort get the current user
        {
            $user_id = $this->getUser()['id'];
        }

        $_SESSION['cached_user_id'] = $user_id;

        return $user_id;
    }

    private function resolveProject($user_id)
    {
        $project_id = $this->request->getIntegerParam('project_cus_id');
        if (empty($project_id))
        {
            $project_id = $this->request->getIntegerParam('project_id');
        }
        $projectsAccess = $this->boardNotesModel->boardNotesGetAllProjectIds($user_id);

        // search requested project VS access
        foreach($projectsAccess as $projectAccess)
        {
            if ($projectAccess['project_id'] == $project_id) break;
        }

        // if we didn't find the requested project, switch by default to the first one (i.e. General custom)
        if ($projectAccess['project_id'] != $project_id)
        {
            $projectAccess = $projectsAccess[0];
        }

        if ($projectAccess['is_custom'])
        {
            // assemble a fake custom project
            return array("id" => $project_id, "name" => $projectAccess['project_name'], "is_custom" => True);
        }
        else
        {
            // get all the data of existing project and mark it as NOT custom
            $project = $this->getProject();
            $project['is_custom'] = False;
            return $project;
        }
    }

    private function boardNotesShowProject_Internal($is_refresh)
    {
        $user = $this->getUser();
        $user_id = $this->resolveUserId();

        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $data = $this->boardNotesModel->boardNotesShowProject($project_id, $user_id);
        $categories = $this->boardNotesModel->boardNotesGetCategories($project_id);
    	$columns = $this->boardNotesModel->boardNotesToTaskSupplyDataCol($project_id);
    	$swimlanes = $this->boardNotesModel->boardNotesToTaskSupplyDataSwi($project_id);

        $params = array(
                'project' => $project,
                'project_id' => $project_id,
                'user' => $user,
                'user_id' => $user_id,
                'is_refresh' => $is_refresh,
                'is_custom' => $project['is_custom'],
                'data' => $data,
                'categories' => $categories,
                'columns' => $columns,
                'swimlanes' => $swimlanes,
        );

        if ($project['is_custom'])
        {
            // show notes for regular projects at dashboard level
            $params['title'] = t('My notes > %s', $project['name']);
            $params['description'] = $this->helper->text->markdown('Predefined custom notes list for <strong>'.$this->helper->user->getFullname($user).'</strong>');
            return $this->response->html($this->helper->layout->dashboard('BoardNotes:project/data', $params));
        }
        else
        {
            // show notes for regular projects at project level
            $params['title'] = $project['name']; // rather keep the project name as title
            $params['description'] = $this->helper->projectHeader->getDescription($project);
            return $this->response->html($this->helper->layout->app('BoardNotes:project/data', $params));
        }
    }

    public function boardNotesShowProject()
    {
        $this->boardNotesShowProject_Internal(False);
    }

    public function boardNotesRefreshProject()
    {
        $this->boardNotesShowProject_Internal(True);
    }

    public function boardNotesShowAll()
    {
        $user = $this->getUser();
        $user_id = $this->resolveUserId();

        $projectsAccess = $this->boardNotesModel->boardNotesGetAllProjectIds($user_id);

        $data = $this->boardNotesModel->boardNotesShowAll($projectsAccess, $user_id);

        return $this->response->html($this->helper->layout->dashboard('BoardNotes:dashboard/data', array(
            'title' => t('Notes overview for %s', $this->helper->user->getFullname($user)),
            'user' => $user,
            'user_id' => $user_id,
            'projectsAccess' => $projectsAccess,
        )));
    }

    public function boardNotesDeleteNote()
    {
    	$project_id = $this->request->getStringParam('project_id');
    	$user_id = $this->request->getStringParam('user_id');
        $note_id = $this->request->getStringParam('note_id');

        $validation = $this->boardNotesModel->boardNotesDeleteNote($note_id, $project_id, $user_id);
    }

    public function boardNotesDeleteAllDoneNotes()
    {
    	$project_id = $this->request->getStringParam('project_id');
    	$user_id = $this->request->getStringParam('user_id');

        $validation = $this->boardNotesModel->boardNotesDeleteAllDoneNotes($project_id, $user_id);
    }

    public function boardNotesUpdateNote()
    {
    	$project_id = $this->request->getStringParam('project_id');
    	$user_id = $this->request->getStringParam('user_id');
    	$note_id = $this->request->getStringParam('note_id');

    	$is_active = $this->request->getStringParam('is_active');
    	$title = $this->request->getStringParam('title');
    	$description = $this->request->getStringParam('description');
    	$category = $this->request->getStringParam('category');
        
        $validation = $this->boardNotesModel->boardNotesUpdateNote($project_id, $user_id, $note_id, $is_active, $title, $description, $category);
    }

    public function boardNotesAddNote()
    {
        $project_id = $this->request->getStringParam('project_id');
    	$user_id = $this->request->getStringParam('user_id');

    	$is_active = $this->request->getStringParam('is_active'); // Not needed when new is added
    	$title = $this->request->getStringParam('title');
    	$description = $this->request->getStringParam('description');
    	$category = $this->request->getStringParam('category');

    	$validation = $this->boardNotesModel->boardNotesAddNote($project_id, $user_id, $is_active, $title, $description, $category);
    }

    public function boardNotesAnalytics()
    {
    	$project_id = $this->request->getStringParam('project_id');
    	$user_id = $this->request->getStringParam('user_id');

        $analyticsData = $this->boardNotesModel->boardNotesAnalytics($project_id, $user_id);

        return $this->response->html($this->helper->layout->app('BoardNotes:project/analytics', array(
            //'title' => t('Analytics'),
            'analyticsData' => $analyticsData
        )));
    }

    public function boardNotesToTask()
    {
    	$project_id = $this->request->getStringParam('project_id');
        
        $task_title = $this->request->getStringParam('task_title');
        $task_description = $this->request->getStringParam('task_description');
        $column = $this->request->getStringParam('column');
        $swimlane = $this->request->getStringParam('swimlane');
        $category = $this->request->getStringParam('category');

    	return $this->response->html($this->helper->layout->app('BoardNotes:project/post', array(
            'title' => t('Post'),
            'task_title' => $task_title,
    		'task_description' => $task_description,
    		'column_id' => $column,
    		'swimlane_id' => $swimlane,
    		'category_id' => $category,
    		'project_id' => $project_id
        )));
    }

    public function boardNotesUpdatePosition()
    {
    	$project_id = $this->request->getStringParam('project_id');
    	$user_id = $this->request->getStringParam('user_id');

        $notePositions = $this->request->getStringParam('order');
        $nrNotes = $this->request->getStringParam('nrNotes');

        $validation = $this->boardNotesModel->boardNotesUpdatePosition($project_id, $user_id, $notePositions, $nrNotes);
    }

    public function boardNotesReport()
    {
        $project = $this->resolveProject();
        $project_id = $project['id'];
        $user_id = $this->resolveUserId();

        $category = $this->request->getStringParam('category');
        if (empty($category)) {
            $category = "";
        }

        $data = $this->boardNotesModel->boardNotesReport($project_id, $user_id, $category);

        return $this->response->html($this->helper->layout->app('BoardNotes:project/report', array(
            'title' => $project['name'], // rather keep the project name as title
            'project' => $project,
            'project_id' => $project_id,
            'user_id' => $user_id,
            'data' => $data,
        )));
    }
}
