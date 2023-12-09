<?php

namespace Kanboard\Plugin\BoardNotes\Controller;

use Kanboard\Controller\BaseController;

class BoardNotesController extends BaseController
{

    public function boardNotesShowProject()
    {
        $project = $this->getProject();
        $project_id = $project['id'];
        $user = $this->getUser();
        $user_id = $user['id'];

        $projectAccess[] = array("project_id" => "9998", "project_name" => "General");
        $projectAccess[] = array("project_id" => "9997", "project_name" => "Todo");

        $data = $this->boardNotesModel->boardNotesShowProject($project_id, $user_id);
        $categories = $this->boardNotesModel->boardNotesGetCategories($project_id);
    	$columns = $this->boardNotesModel->boardNotesToTaskSupplyDataCol($project_id);
    	$swimlanes = $this->boardNotesModel->boardNotesToTaskSupplyDataSwi($project_id);

        return $this->response->html($this->helper->layout->app('BoardNotes:project/data', array(
            'title' => $project['name'], // rather keep the project name as title
            'project' => $project,
            'project_id' => $project_id,
            'user' => $user,
            'data' => $data,
    	    'categories' => $categories,
    	    'columns' => $columns,
    	    'swimlanes' => $swimlanes,
        )));
    }

    public function boardNotesShowAll()
    {
        $user = $this->getUser();
        $user_id = $user['id'];

        $projectAccess = $this->boardNotesModel->boardNotesGetProjectIds($user_id);
        $projectAccess[] = array("project_id" => "9998", "project_name" => "General");
        $projectAccess[] = array("project_id" => "9997", "project_name" => "Todo");

        $data = $this->boardNotesModel->boardNotesShowAll($projectAccess, $user_id);

        return $this->response->html($this->helper->layout->dashboard('BoardNotes:dashboard/data', array(
            'title' => t('Notes overview for %s', $this->helper->user->getFullname($user)),
            'user' => $user,
            'projectAccess' => $projectAccess,
        )));
    }

    public function boardNotesDeleteNote()
    {
        $note_id = $this->request->getStringParam('note_id');
    	$project_id = $this->request->getStringParam('project_id');
        $user_id = $this->getUser()['id'];

        $validation = $this->boardNotesModel->boardNotesDeleteNote($note_id, $project_id, $user_id);
    }

    public function boardNotesDeleteAllDoneNotes()
    {
    	$project_id = $this->request->getStringParam('project_id');
        $user_id = $this->getUser()['id'];

        $validation = $this->boardNotesModel->boardNotesDeleteAllDoneNotes($project_id, $user_id);
    }

    public function boardNotesUpdateNote()
    {
    	$note_id = $this->request->getStringParam('note_id');
    	$is_active = $this->request->getStringParam('is_active');
    	$title = $this->request->getStringParam('title');
    	$description = $this->request->getStringParam('description');
    	$category = $this->request->getStringParam('category');
        
    	$project_id = $this->request->getStringParam('project_id');
        $user_id = $this->getUser()['id'];

        $validation = $this->boardNotesModel->boardNotesUpdateNote($project_id, $user_id, $note_id, $is_active, $title, $description, $category);
    }

    public function boardNotesAddNote()
    {
        $project_id = $this->request->getStringParam('project_id');
        $user_id = $this->getUser()['id'];
    	$is_active = $this->request->getStringParam('is_active'); // Not needed when new is added
    	$title = $this->request->getStringParam('title');
    	$description = $this->request->getStringParam('description');
    	$category = $this->request->getStringParam('category');

    	$validation = $this->boardNotesModel->boardNotesAddNote($project_id, $user_id, $is_active, $title, $description, $category);
    }

    public function boardNotesAnalytics()
    {
    	$project_id = $this->request->getStringParam('project_id');
        $user_id = $this->getUser()['id'];

        $analyticsData = $this->boardNotesModel->boardNotesAnalytics($project_id, $user_id);

        return $this->response->html($this->helper->layout->app('BoardNotes:project/analytics', array(
            //'title' => t('Analytics'),
            'analyticsData' => $analyticsData
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

    	return $this->response->html($this->helper->layout->app('BoardNotes:project/post', array(
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

    public function boardNotesReport()
    {
        //$project = $this->getProject();
    	$project = $this->request->getStringParam('project_id');

        $user = $this->getUser();
        $category = $this->request->getStringParam('category');
        if (empty($category)) {
            $category = "";
        }

        $data = $this->boardNotesModel->boardNotesReport($project['id'], $user['id'], $category);
        $projectAccess = $this->boardNotesModel->boardNotesGetProjectID($user['id']);

        return $this->response->html($this->helper->layout->project('BoardNotes:project/report', array(
            'title' => t('BoardNotes Report'),
            'project' => $project,
            'data' => $data,
        )));
    }
}
