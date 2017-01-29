<?php

namespace Kanboard\Plugin\Boardnotes\Controller;

use Kanboard\Controller\BaseController;

class BoardnotesController extends BaseController
{

    public function BoardnotesShowProject()
    {
        $project = $this->getProject();
        $user = $this->getUser();

        $data = $this->boardnotesModel->modelBoardnotesShowProject($project['id'], $user['id']);
    	$projectAccess = $this->boardnotesModel->modelBoardnotesGetProjectid($user['id']);

        $projectAccess[] = array("project_id" => "9998", "project_name" => "General");
        $projectAccess[] = array("project_id" => "9997", "project_name" => "Todo");
    	
        $categories = $this->boardnotesModel->modelBoardnotesGetCategories($project['id']);
    	$columns = $this->boardnotesModel->modelBoardnotesToTaskSupplyDataCol($project['id']);
    	$swimlanes = $this->boardnotesModel->modelBoardnotesToTaskSupplyDataSwi($project['id']);


        return $this->response->html($this->helper->layout->project('Boardnotes:boardnotes/show', array('title' => t('Boardnotes'),
            'project' => $project,
            'data' => $data,
    	    'categories' => $categories,
    	    'columns' => $columns,
    	    'swimlanes' => $swimlanes,
        )));
    }


    public function BoardnotesShowProjectRefresh()
    {
        $project = $this->request->getStringParam('project_id');
        $project = array("id" => $project);

        $user = $this->getUser();
        $data = $this->boardnotesModel->modelBoardnotesShowProject($project['id'], $user['id']);
        $categories = $this->boardnotesModel->modelBoardnotesGetCategories($project['id']);
        $columns = $this->boardnotesModel->modelBoardnotesToTaskSupplyDataCol($project['id']);
        $swimlanes = $this->boardnotesModel->modelBoardnotesToTaskSupplyDataSwi($project['id']);

        return $this->response->html($this->helper->layout->app('Boardnotes:boardnotes/dataSingle', array('title' => t('Boardnotes'),
            'project' => $project,
            'data' => $data,
    	    'categories' => $categories,
    	    'columns' => $columns,
    	    'swimlanes' => $swimlanes,
        )));
    }


    public function BoardnotesShowAll()
    {
        //$project = $this->getProject();
        $user = $this->getUser();

	    $projectAccess = $this->boardnotesModel->modelBoardnotesGetProjectid($user['id']);

        $projectAccess[] = array("project_id" => "9998", "project_name" => "General");
        $projectAccess[] = array("project_id" => "9997", "project_name" => "Todo");

        $data = $this->boardnotesModel->modelBoardnotesShowAll($projectAccess, $user['id']);

        return $this->response->html($this->helper->layout->dashboard('Boardnotes:boardnotes/showAll', array('title' => t('Boardnotes'),
            'project' => 'Notes',
	        'projectAccess' => $projectAccess,
            'data' => $data,
	        'allProjects' => '1',
            'custom' => $custom,
        )));
    }


    public function BoardnotesShowAllRefresh()
    {
        $project = $this->getProject();
        $user = $this->getUser();

	    $projectAccess = $this->boardnotesModel->modelBoardnotesGetProjectid($user['id']);
        $projectAccess[] = array("project_id" => "9998", "project_name" => "General");
        $projectAccess[] = array("project_id" => "9997", "project_name" => "Todo");
        $data = $this->boardnotesModel->modelBoardnotesShowAll($projectAccess, $user['id']);

        return $this->response->html($this->helper->layout->app('Boardnotes:project_overview/data', array('title' => t('Boardnotes'),
            'project' => $project,
            'data' => $data,
        )));
    }



    public function BoardnotesDelete()
    {
        $project = $this->getProject();
        $user = $this->getUser();
        $note_id = $this->request->getStringParam('note_id');


        $validation = $this->boardnotesModel->modelBoardnotesDeleteNote($note_id, $user['id']);
    }


    public function BoardnotesDeleteAllDone()
    {
        $project = $this->getProject();
        $user = $this->getUser();
        $note_id = $this->request->getStringParam('project_id');


        $validation = $this->boardnotesModel->modelBoardnotesDeleteAllDone($project['id'], $user['id']);
    }


    public function BoardnotesUpdate()
    {
    	$note_id = $this->request->getStringParam('note_id');
    	$is_active = $this->request->getStringParam('is_active');
    	$title = $this->request->getStringParam('title');
    	$description = $this->request->getStringParam('description');
    	$category = $this->request->getStringParam('category');
        $project = $this->getProject();
        $user = $this->getUser();

        $validation = $this->boardnotesModel->modelBoardnotesUpdateNote($user['id'], $note_id, $is_active, $title, $description, $category); 
    }


    public function BoardnotesAdd()
    {
    	$project = $this->request->getStringParam('project_id');
        //$project = $this->getProject();
        $user = $this->getUser();
    	$is_active = $this->request->getStringParam('is_active'); // Not needed when new is added
    	$title = $this->request->getStringParam('title');
    	$description = $this->request->getStringParam('description');
    	$category = $this->request->getStringParam('category');


    	$validation = $this->boardnotesModel->modelBoardnotesAddNote($project, $user['id'], $is_active, $title, $description, $category);
    }

    public function BoardnotesAnalytic()
    {
        $project = $this->getProject();
        $user = $this->getUser();

        $analyticData = $this->boardnotesModel->modelBoardnotesAnalytics($project['id'], $user['id']);

        return $this->response->html($this->helper->layout->app('Boardnotes:boardnotes/analytics', array('title' => t('Analytics'),
            'project' => $project,
        	'analyticData' => $analyticData
        )));
    }


    public function BoardnotesToTask()
    {
	    $project = $this->getProject();
        $user = $this->getUser();

        $title = $this->request->getStringParam('title');
        $description = $this->request->getStringParam('description');
        $columns = $this->request->getStringParam('columns');
        $swimlanes = $this->request->getStringParam('swimlanes');
        $category = $this->request->getStringParam('category');

    	return $this->response->html($this->helper->layout->app('Boardnotes:boardnotes/post', array('title' => t('post'),
            'title' => $title,
    		'description' => $description,
    		'column_id' => $columns,
    		'swimlane_id' => $swimlanes,
    		'category_id' => $category,
    		'project_id' => $project['id']
        )));


     }

    public function BoardnotesUpdatePosition()
    {
        $nrNotes = $this->request->getStringParam('nrNotes');
        $notePositions = $this->request->getStringParam('order');

        $validation = $this->boardnotesModel->modelBoardnotesUpdatePosition($notePositions, $nrNotes);

    }


    public function BoardnotesShowReport()
    {
        $project = $this->getProject();
        $user = $this->getUser();
        $category = $this->request->getStringParam('category');
        if (empty($category)) {
            $category = "";
        }

        $data = $this->boardnotesModel->modelBoardnotesShowReport($project['id'], $user['id'], $category);
        $projectAccess = $this->boardnotesModel->modelBoardnotesGetProjectid($user['id']);


        return $this->response->html($this->helper->layout->project('Boardnotes:boardnotes/report', array('title' => t('Boardnotes report'),
            'project' => $project,
            'data' => $data,
        )));
    }



}
