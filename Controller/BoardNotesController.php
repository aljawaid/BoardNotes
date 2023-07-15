<?php

namespace Kanboard\Plugin\BoardNotes\Controller;

use Kanboard\Controller\BaseController;

class BoardNotesController extends BaseController
{
    public function boardNotesShowProject()
    {
        $project = $this->getProject();
        $user = $this->getUser();

        $data = $this->boardNotesModel->boardNotesShowProject($project['id'], $user['id']);
    	$projectAccess = $this->boardNotesModel->boardNotesGetProjectID($user['id']);

        $projectAccess[] = array("project_id" => "9998", "project_name" => "General");
        $projectAccess[] = array("project_id" => "9997", "project_name" => "Todo");
    	
        $categories = $this->boardNotesModel->boardNotesGetCategories($project['id']);
    	$columns = $this->boardNotesModel->boardNotesToTaskSupplyDataCol($project['id']);
    	$swimlanes = $this->boardNotesModel->boardNotesToTaskSupplyDataSwi($project['id']);


        return $this->response->html($this->helper->layout->project('boardNotes:project/show', array(
            'title' => t('BoardNotes'),
            'project' => $project,
            'data' => $data,
    	    'categories' => $categories,
    	    'columns' => $columns,
    	    'swimlanes' => $swimlanes,
        )));
    }

    public function boardNotesShowProjectRefresh()
    {
        $project = $this->request->getStringParam('project_id');
        $project = array("id" => $project);

        $user = $this->getUser();
        $data = $this->boardNotesModel->boardNotesShowProject($project['id'], $user['id']);
        $categories = $this->boardNotesModel->boardNotesGetCategories($project['id']);
        $columns = $this->boardNotesModel->boardNotesToTaskSupplyDataCol($project['id']);
        $swimlanes = $this->boardNotesModel->boardNotesToTaskSupplyDataSwi($project['id']);

        return $this->response->html($this->helper->layout->app('boardNotes:project/dataSingle', array(
            'title' => t('BoardNotes'),
            'project' => $project,
            'data' => $data,
    	    'categories' => $categories,
    	    'columns' => $columns,
    	    'swimlanes' => $swimlanes,
        )));
    }

    public function boardNotesShowAll()
    {
        //$project = $this->getProject();
        $user = $this->getUser();

	    $projectAccess = $this->boardNotesModel->boardNotesGetProjectID($user['id']);

        $projectAccess[] = array("project_id" => "9998", "project_name" => "General");
        $projectAccess[] = array("project_id" => "9997", "project_name" => "Todo");

        $data = $this->boardNotesModel->boardNotesShowAll($projectAccess, $user['id']);

        return $this->response->html($this->helper->layout->dashboard('boardNotes:boardnotes/showAll', array(
            'title' => t('BoardNotes'),
            'project' => 'Notes',
	        'projectAccess' => $projectAccess,
            'data' => $data,
	        'allProjects' => '1'
        )));
    }

    public function boardNotesShowAllRefresh()
    {
        $project = $this->getProject();
        $user = $this->getUser();

	    $projectAccess = $this->boardNotesModel->boardNotesGetProjectID($user['id']);
        $projectAccess[] = array("project_id" => "9998", "project_name" => "General");
        $projectAccess[] = array("project_id" => "9997", "project_name" => "Todo");
        $data = $this->boardNotesModel->boardNotesShowAll($projectAccess, $user['id']);

        return $this->response->html($this->helper->layout->app('boardNotes:project_overview/data', array(
            'title' => t('BoardNotes'),
            'project' => $project,
            'data' => $data,
        )));
    }

    public function boardNotesDelete()
    {
        //$project = $this->getProject();
    	$project = $this->request->getStringParam('project_id');

        $user = $this->getUser();
        $note_id = $this->request->getStringParam('note_id');


        $validation = $this->boardNotesModel->boardNotesDeleteNote($note_id, $user['id']);
    }

    public function boardNotesDeleteAllDone()
    {
        //$project = $this->getProject();
    	$project = $this->request->getStringParam('project_id');

        $user = $this->getUser();
        $note_id = $this->request->getStringParam('project_id');

        $validation = $this->boardNotesModel->boardNotesDeleteAllDone($project['id'], $user['id']);
    }

    public function boardNotesUpdate()
    {
    	$note_id = $this->request->getStringParam('note_id');
    	$is_active = $this->request->getStringParam('is_active');
    	$title = $this->request->getStringParam('title');
    	$description = $this->request->getStringParam('description');
    	$category = $this->request->getStringParam('category');
        
        //$project = $this->getProject();
    	$project = $this->request->getStringParam('project_id');

        $user = $this->getUser();

        $validation = $this->boardNotesModel->boardNotesUpdateNote($user['id'], $note_id, $is_active, $title, $description, $category);
    }

    public function boardNotesAdd()
    {
        //$project = $this->getProject();
        $project = $this->request->getStringParam('project_id');

        $user = $this->getUser();
    	$is_active = $this->request->getStringParam('is_active'); // Not needed when new is added
    	$title = $this->request->getStringParam('title');
    	$description = $this->request->getStringParam('description');
    	$category = $this->request->getStringParam('category');

    	$validation = $this->boardNotesModel->boardNotesAddNote($project, $user['id'], $is_active, $title, $description, $category);
    }

    public function boardNotesAnalytic()
    {
        //$project = $this->getProject();
    	$project = $this->request->getStringParam('project_id');

        $user = $this->getUser();

        $analyticData = $this->boardNotesModel->boardNotesAnalytics($project['id'], $user['id']);

        return $this->response->html($this->helper->layout->app('boardNotes:boardnotes/analytics', array(
            'title' => t('Analytics'),
            'project' => $project,
        	'analyticData' => $analyticData
        )));
    }

    public function boardNotesToTask()
    {
	    //$project = $this->getProject();
    	$project = $this->request->getStringParam('project_id');
        
        $user = $this->getUser();

        $title = $this->request->getStringParam('title');
        $description = $this->request->getStringParam('description');
        $columns = $this->request->getStringParam('columns');
        $swimlanes = $this->request->getStringParam('swimlanes');
        $category = $this->request->getStringParam('category');

    	return $this->response->html($this->helper->layout->app('boardNotes:boardnotes/post', array(
            'title' => t('Post'),
            'title' => $title,
    		'description' => $description,
    		'column_id' => $columns,
    		'swimlane_id' => $swimlanes,
    		'category_id' => $category,
    		'project_id' => $project['id']
        )));


     }

    public function boardNotesUpdatePosition()
    {
        $nrNotes = $this->request->getStringParam('nrNotes');
        $notePositions = $this->request->getStringParam('order');

        $validation = $this->boardNotesModel->boardNotesUpdatePosition($notePositions, $nrNotes);
    }

    public function boardNotesShowReport()
    {
        //$project = $this->getProject();
    	$project = $this->request->getStringParam('project_id');

        $user = $this->getUser();
        $category = $this->request->getStringParam('category');
        if (empty($category)) {
            $category = "";
        }

        $data = $this->boardNotesModel->boardNotesShowReport($project['id'], $user['id'], $category);
        $projectAccess = $this->boardNotesModel->boardNotesGetProjectID($user['id']);

        return $this->response->html($this->helper->layout->project('boardNotes:boardnotes/report', array(
            'title' => t('BoardNotes Report'),
            'project' => $project,
            'data' => $data,
        )));
    }
}
