<?php

namespace Kanboard\Plugin\BoardNotes\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Controller\PageNotFoundException;

/**
 * Serve personal BoardNotes views and mutations within authorised projects.
 *
 * Related test: tests/AuthorizationContractTest.php.
 */
class BoardNotesController extends BaseController
{
    private function resolveUserId()
    {
        // Note ownership is always derived from the authenticated session.
        return (int) $this->userSession->getId();
    }

    private function resolveProject($user_id)
    {
        $project_id = $this->request->getIntegerParam('project_cus_id');
        if (empty($project_id)) {
            $project_id = $this->request->getIntegerParam('project_id');
        }
        $projectAccess = $this->boardNotesModel->boardNotesGetAccessibleProject($project_id, $user_id);

        if (empty($projectAccess)) {
            throw new AccessForbiddenException();
        }

        if ($projectAccess['is_custom']) {
            // assemble a fake custom project
            return array('id' => $project_id, 'name' => $projectAccess['project_name'], 'is_custom' => true);
        } else {
            // get all the data of existing project and mark it as NOT custom
            $project = $this->boardNotesModel->boardNotesGetProjectById($project_id);
            if (empty($project)) {
                throw new PageNotFoundException();
            }
            $project['is_custom'] = false;
            return $project;
        }
    }

    private function boardNotesShowProjectInternal($is_refresh)
    {
        $user = $this->getUser();
        $user_id = $this->resolveUserId();

        if ($is_refresh) {
            $project = $this->resolveProject($user_id);
        } else {
            $project = $this->getProject();
            $project['is_custom'] = false;
        }
        $project_id = $project['id'];

        $projectsAccess = $this->boardNotesModel->boardNotesGetAllProjectIds($user_id);

        if ($project['is_custom']) {
            $categories = array();
        } else {
            $categories = $this->boardNotesModel->boardNotesGetCategories($project_id);
        }
        $columns = $this->boardNotesModel->boardNotesGetColumns($project_id);
        $swimlanes = $this->boardNotesModel->boardNotesGetSwimlanes($project_id);
        $data = $this->boardNotesModel->boardNotesShowProject($project_id, $user_id);

        return $this->response->html($this->helper->layout->app('BoardNotes:project/data', array(
            'title' => $project['name'], // rather keep the project name as title
            'project' => $project,
            'project_id' => $project_id,
            'user' => $user,
            'user_id' => $user_id,
            'projectsAccess' => $projectsAccess,

            'categories' => $categories,
            'columns' => $columns,
            'swimlanes' => $swimlanes,
            'data' => $data,

            'is_refresh' => $is_refresh,
            'is_dashboard_view' => 0,
        )));
    }

    public function boardNotesShowProject()
    {
        return $this->boardNotesShowProjectInternal(false);
    }

    public function boardNotesRefreshProject()
    {
        return $this->boardNotesShowProjectInternal(true);
    }

    public function boardNotesShowAll()
    {
        $user = $this->getUser();
        $user_id = $this->resolveUserId();

        $tab_id = $this->request->getIntegerParam('tab_id');
        if (empty($tab_id)) {
            $tab_id = 0;
        }

        $projectsAccess = $this->boardNotesModel->boardNotesGetAllProjectIds($user_id);

        if ($tab_id > 0 && !$projectsAccess[$tab_id - 1]['is_custom']) {
            $project_id = $projectsAccess[$tab_id - 1]['project_id'];
            $categories = $this->boardNotesModel->boardNotesGetCategories($project_id);
            $columns  = $this->boardNotesModel->boardNotesGetColumns($project_id);
            $swimlanes  = $this->boardNotesModel->boardNotesGetSwimlanes($project_id);
        } else {
            $categories = array();
            $columns  = array();
            $swimlanes  = array();
        }
        $data = $this->boardNotesModel->boardNotesShowAll($projectsAccess, $user_id);

        return $this->response->html($this->helper->layout->dashboard('BoardNotes:dashboard/data', array(
            'title' => t('Notes overview for %s', $this->helper->user->getFullname($user)),
            'user' => $user,
            'user_id' => $user_id,
            'projectsAccess' => $projectsAccess,

            'categories' => $categories,
            'columns' => $columns,
            'swimlanes' => $swimlanes,
            'data' => $data,
        )));
    }

    public function boardNotesDeleteNote()
    {
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->request->getStringParam('note_id');

        $validation = $this->boardNotesModel->boardNotesDeleteNote($project_id, $user_id, $note_id);
        return $validation;
    }

    public function boardNotesDeleteAllDoneNotes()
    {
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $validation = $this->boardNotesModel->boardNotesDeleteAllDoneNotes($project_id, $user_id);
        return $validation;
    }

    public function boardNotesAddNote()
    {
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $is_active = $this->request->getStringParam('is_active'); // Not needed when new is added
        $title = $this->request->getStringParam('title');
        $description = $this->request->getStringParam('description');
        $category = $this->request->getStringParam('category');

        $validation = $this->boardNotesModel->boardNotesAddNote($project_id, $user_id, $is_active, $title, $description, $category);
        return $validation;
    }

    public function boardNotesTransferNote()
    {
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->request->getStringParam('note_id');
        $target_project_id = $this->request->getIntegerParam('target_project_id');

        if (empty($this->boardNotesModel->boardNotesGetAccessibleProject($target_project_id, $user_id))) {
            throw new AccessForbiddenException();
        }

        $validation = $this->boardNotesModel->boardNotesTransferNote($project_id, $user_id, $note_id, $target_project_id);
        return $validation;
    }

    public function boardNotesUpdateNote()
    {
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->request->getStringParam('note_id');

        $is_active = $this->request->getStringParam('is_active');
        $title = $this->request->getStringParam('title');
        $description = $this->request->getStringParam('description');
        $category = $this->request->getStringParam('category');

        $validation = $this->boardNotesModel->boardNotesUpdateNote($project_id, $user_id, $note_id, $is_active, $title, $description, $category);
        return $validation;
    }

    public function boardNotesAnalytics()
    {
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $analyticsData = $this->boardNotesModel->boardNotesAnalytics($project_id, $user_id);

        return $this->response->html($this->helper->layout->app('BoardNotes:project/analytics', array(
            //'title' => t('Analytics'),
            'analyticsData' => $analyticsData
        )));
    }

    public function boardNotesToTask()
    {
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        if ($project['is_custom'] || ! $this->helper->user->hasProjectAccess('TaskCreationController', 'show', $project_id)) {
            throw new AccessForbiddenException();
        }

        $note_id = $this->request->getIntegerParam('note_id');
        $note = $this->boardNotesModel->boardNotesGetNote($project_id, $user_id, $note_id);
        if (empty($note)) {
            throw new PageNotFoundException();
        }

        $category_id = $this->request->getIntegerParam('category_id');
        $column_id = $this->request->getIntegerParam('column_id');
        $swimlane_id = $this->request->getIntegerParam('swimlane_id');

        $category = $category_id > 0 ? $this->categoryModel->getById($category_id) : array();
        $column = $this->columnModel->getById($column_id);
        $swimlane = $swimlane_id > 0 ? $this->swimlaneModel->getById($swimlane_id) : array();

        if (empty($column) || (int) $column['project_id'] !== (int) $project_id) {
            throw new AccessForbiddenException();
        }
        if ($category_id > 0 && (empty($category) || (int) $category['project_id'] !== (int) $project_id)) {
            throw new AccessForbiddenException();
        }
        if ($swimlane_id > 0 && (empty($swimlane) || (int) $swimlane['project_id'] !== (int) $project_id)) {
            throw new AccessForbiddenException();
        }

        $task_title = trim($note['title']);
        $task_description = str_ireplace('<br >', "\n", $note['description']);
        if ($task_title === '') {
            throw new PageNotFoundException();
        }

        $task_id = $this->taskCreationModel->create(array(
            'project_id'  => $project_id,
            'creator_id'  => $user_id,
            'owner_id'    => $user_id,
            'title'       => $task_title,
            'description' => $task_description,
            'category_id' => $category_id,
            'column_id' => $column_id,
            'swimlane_id' => $swimlane_id,
        ));

        return $this->response->html($this->helper->layout->app('BoardNotes:project/post', array(
            //'title' => t('Post'),
            'task_id' => $task_id,
            'project_name' => $this->projectModel->getById($project_id)["name"],
            'user_name' => $this->userModel->getById($user_id)["username"],
            'task_title' => $task_title,
            'task_description' => $task_description,
            'category' => $category_id > 0 ? $category['name'] : '',
            'column' => $column['title'],
            'swimlane' => $swimlane_id > 0 ? $swimlane['name'] : '',
        )));
    }

    public function boardNotesUpdatePosition()
    {
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $notePositions = $this->request->getStringParam('order');
        $nrNotes = $this->request->getStringParam('nrNotes');

        $validation = $this->boardNotesModel->boardNotesUpdatePosition($project_id, $user_id, $notePositions, $nrNotes);
        return $validation;
    }

    public function boardNotesReport()
    {
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

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
