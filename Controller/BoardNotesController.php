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
    private function getPostedString($name)
    {
        return trim((string) $this->request->getRawValue($name));
    }

    private function getPostedInteger($name)
    {
        $value = $this->request->getRawValue($name);
        return ctype_digit((string) $value) ? (int) $value : 0;
    }

    private function getPostedCategoryName($project_id)
    {
        $category_id = $this->getPostedInteger('category_id');
        if ($category_id === 0) {
            return '';
        }

        $category = $this->categoryModel->getById($category_id);
        if (empty($category) || (int) $category['project_id'] !== (int) $project_id) {
            throw new AccessForbiddenException();
        }

        return $category['name'];
    }

    private function getValidatedNoteInput($project)
    {
        $title = $this->getPostedString('title');
        $description = (string) $this->request->getRawValue('description');
        $is_active = $this->getPostedInteger('is_active');

        if ($title === '' || mb_strlen($title) > 255 || mb_strlen($description) > 65535) {
            return array();
        }

        if (! in_array($is_active, array(0, 1, 2), true)) {
            return array();
        }

        return array(
            'title' => $title,
            'description' => $description,
            'is_active' => $is_active,
            'category' => $project['is_custom'] ? '' : $this->getPostedCategoryName($project['id']),
        );
    }

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

        $projectsAccess = $this->boardNotesModel->boardNotesGetAllProjectIds($user_id);

        $tab_id = $this->request->getIntegerParam('tab_id');
        if ($tab_id < 1 || ! isset($projectsAccess[$tab_id - 1])) {
            $tab_id = 0;
        }

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
            'title' => t('boardnotes.my_notes'),
            'user' => $user,
            'user_id' => $user_id,
            'projectsAccess' => $projectsAccess,
            'tab_id' => $tab_id,

            'categories' => $categories,
            'columns' => $columns,
            'swimlanes' => $swimlanes,
            'data' => $data,
        )));
    }

    public function boardNotesDeleteNote()
    {
        $this->checkReusableCSRFParam();
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->getPostedInteger('note_id');

        $validation = $this->boardNotesModel->boardNotesDeleteNote($project_id, $user_id, $note_id);
        return $this->response->json(array('success' => (bool) $validation), $validation ? 200 : 404);
    }

    public function boardNotesDeleteAllDoneNotes()
    {
        $this->checkReusableCSRFParam();
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $validation = $this->boardNotesModel->boardNotesDeleteAllDoneNotes($project_id, $user_id);
        return $this->response->json(array('success' => (bool) $validation));
    }

    public function boardNotesAddNote()
    {
        $this->checkReusableCSRFParam();
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $input = $this->getValidatedNoteInput(array_merge($project, array('is_custom' => $project['is_custom'])));
        if (empty($input)) {
            return $this->response->json(array('success' => false, 'message' => t('boardnotes.validation_error')), 422);
        }

        $validation = $this->boardNotesModel->boardNotesAddNote($project_id, $user_id, 1, $input['title'], $input['description'], $input['category']);
        return $this->response->json(array('success' => (bool) $validation), $validation ? 201 : 400);
    }

    public function boardNotesTransferNote()
    {
        $this->checkReusableCSRFParam();
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->getPostedInteger('note_id');
        $target_project_id = $this->getPostedInteger('target_project_id');

        if (empty($this->boardNotesModel->boardNotesGetAccessibleProject($target_project_id, $user_id))) {
            throw new AccessForbiddenException();
        }

        $validation = $this->boardNotesModel->boardNotesTransferNote($project_id, $user_id, $note_id, $target_project_id);
        return $this->response->json(array('success' => (bool) $validation), $validation ? 200 : 404);
    }

    public function boardNotesUpdateNote()
    {
        $this->checkReusableCSRFParam();
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->getPostedInteger('note_id');
        $input = $this->getValidatedNoteInput($project);
        if (empty($input)) {
            return $this->response->json(array('success' => false, 'message' => t('boardnotes.validation_error')), 422);
        }

        $validation = $this->boardNotesModel->boardNotesUpdateNote($project_id, $user_id, $note_id, $input['is_active'], $input['title'], $input['description'], $input['category']);
        return $this->response->json(array('success' => (bool) $validation), $validation ? 200 : 404);
    }

    public function boardNotesToggleNote()
    {
        $this->checkReusableCSRFParam();
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $note_id = $this->getPostedInteger('note_id');
        $is_active = $this->getPostedInteger('is_active');

        if (! in_array($is_active, array(0, 1), true)) {
            return $this->response->json(array('success' => false), 422);
        }

        $validation = $this->boardNotesModel->boardNotesToggleNote($project['id'], $user_id, $note_id, $is_active);
        return $this->response->json(array('success' => (bool) $validation), $validation ? 200 : 404);
    }

    public function boardNotesAnalytics()
    {
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $analyticsData = $this->boardNotesModel->boardNotesAnalytics($project_id, $user_id);

        return $this->response->html($this->helper->layout->app('BoardNotes:project/analytics', array(
            'title' => t('boardnotes.analytics_title'),
            'project' => $project,
            'analyticsData' => $analyticsData
        )));
    }

    public function boardNotesToTask()
    {
        $this->checkReusableCSRFParam();
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        if ($project['is_custom'] || ! $this->helper->user->hasProjectAccess('TaskCreationController', 'show', $project_id)) {
            throw new AccessForbiddenException();
        }

        $note_id = $this->getPostedInteger('note_id');
        $note = $this->boardNotesModel->boardNotesGetNote($project_id, $user_id, $note_id);
        if (empty($note)) {
            throw new PageNotFoundException();
        }

        $category_id = $this->getPostedInteger('category_id');
        $column_id = $this->getPostedInteger('column_id');
        $swimlane_id = $this->getPostedInteger('swimlane_id');

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
        $task_description = $note['description'];
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

        if (! $task_id) {
            return $this->response->json(array('success' => false, 'message' => t('boardnotes.task_failed')), 422);
        }

        // A source note is removed only after Kanboard confirms task creation.
        $remove_note = $this->request->getRawValue('remove_note') === '1';
        $note_removed = false;
        if ($remove_note) {
            $note_removed = (bool) $this->boardNotesModel->boardNotesDeleteNote($project_id, $user_id, $note_id);
        }

        return $this->response->json(array(
            'success' => true,
            'task_id' => (int) $task_id,
            'task_url' => $this->helper->url->href('TaskViewController', 'show', array('task_id' => $task_id)),
            'note_removed' => $note_removed,
            'message' => $remove_note && ! $note_removed
                ? t('boardnotes.task_partial')
                : t('boardnotes.task_created'),
        ), $remove_note && ! $note_removed ? 207 : 201);
    }

    public function boardNotesUpdatePosition()
    {
        $this->checkReusableCSRFParam();
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $notePositions = $this->getPostedString('order');
        $nrNotes = $this->getPostedInteger('nrNotes');

        $validation = $this->boardNotesModel->boardNotesUpdatePosition($project_id, $user_id, $notePositions, $nrNotes);
        return $this->response->json(array('success' => (bool) $validation), $validation ? 200 : 400);
    }

    public function boardNotesReport()
    {
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $category = trim($this->request->getStringParam('category'));
        $categories = $project['is_custom'] ? array() : $this->boardNotesModel->boardNotesGetCategories($project_id);
        if ($category !== '') {
            $category_names = array_column($categories, 'name');
            if (! in_array($category, $category_names, true)) {
                $category = '';
            }
        }

        $data = $this->boardNotesModel->boardNotesReport($project_id, $user_id, $category);

        return $this->response->html($this->helper->layout->app('BoardNotes:project/report', array(
            'title' => $project['name'], // rather keep the project name as title
            'project' => $project,
            'project_id' => $project_id,
            'user_id' => $user_id,
            'data' => $data,
            'categories' => $categories,
            'selected_category' => $category,
        )));
    }
}
