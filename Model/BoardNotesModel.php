<?php

namespace Kanboard\Plugin\BoardNotes\Model;

use Kanboard\Core\Base;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\CategoryModel;

/**
 * Store and query personal notes within the current user's accessible projects.
 *
 * Related test: tests/AuthorizationContractTest.php.
 */
class BoardNotesModel extends Base
{
    // phpcs:disable Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase -- Preserve the established public constant API.
    public const TABLEnotes = 'boardnotes';
    public const TABLEnotescus = 'boardnotes_cus';
    public const TABLEprojects = ProjectModel::TABLE;
    public const TABLEtasks = TaskModel::TABLE;
    public const TABLEcolumns = ColumnModel::TABLE;
    public const TABLEswimlanes = SwimlaneModel::TABLE;
    public const TABLEaccess = ProjectUserRoleModel::TABLE;
    public const TABLEcategories = CategoryModel::TABLE;
    // phpcs:enable Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase

    // Return one note only when its owner and project scope both match.
    public function boardNotesGetNote($project_id, $user_id, $note_id)
    {
        return $this->db->table(self::TABLEnotes)
            ->eq('id', $note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->findOne();
    }

    // Show all notes related to project
    public function boardNotesShowProject($project_id, $user_id)
    {
        return $this->db->table(self::TABLEnotes)
            ->eq('user_id', $user_id)
            ->eq('project_id', $project_id)
            ->desc('is_active')
            ->desc('position')
            ->findAll();
    }

    // Show report
    public function boardNotesReport($project_id, $user_id, $category)
    {
        if (empty($category)) {
            return $this->db->table(self::TABLEnotes)
                ->eq('user_id', $user_id)
                ->eq('project_id', $project_id)
                ->desc('is_active')
                ->desc('position')
                ->findAll();
        } else {
            return $this->db->table(self::TABLEnotes)
                ->eq('user_id', $user_id)
                ->eq('project_id', $project_id)
                ->eq('category', $category)
                ->desc('is_active')
                ->desc('position')
                ->findAll();
        }
    }

    // Get project data by project_id
    public function boardNotesGetProjectById($project_id)
    {
        return $this->db->table(self::TABLEprojects)
            ->eq('id', $project_id)
            ->findOne();
    }

    // Get all project_id where user has assigned access
    public function boardNotesGetProjectIds($user_id)
    {
        if ($this->userSession->isAdmin()) {
            $projects = $this->projectModel->getListByStatus(ProjectModel::ACTIVE);
        } else {
            // Kanboard combines direct and group membership in this model method.
            $projects = $this->projectUserRoleModel->getActiveProjectsByUser($user_id);
        }

        $projectIds = array();
        foreach ($projects as $project_id => $project_name) {
            $projectIds[] = array(
                'project_id' => (int) $project_id,
                'project_name' => $project_name,
                'is_custom' => false,
            );
        }

        return $projectIds;
    }

    // Get all project_id where user has custom access
    public function boardNotesGetCustomProjectIds()
    {
        $projectIds = $this->db->table(self::TABLEnotescus)
            ->columns(self::TABLEnotescus . '.project_id', self::TABLEnotescus . '.project_name')
            ->findAll();
        foreach ($projectIds as &$projectId) {
            $projectId['is_custom'] = true;
        }
        return $projectIds;
    }

    // Get all project_id where user has assigned or custom access
    public function boardNotesGetAllProjectIds($user_id)
    {
        $projectCustomAccess = $this->boardNotesGetCustomProjectIds();
        $projectAssignedAccess = $this->boardNotesGetProjectIds($user_id);
        $projectsAccess = array_merge($projectCustomAccess, $projectAssignedAccess);
        return $projectsAccess;
    }

    // Resolve a project only from the current user's complete access list.
    public function boardNotesGetAccessibleProject($project_id, $user_id)
    {
        foreach ($this->boardNotesGetAllProjectIds($user_id) as $project) {
            if ((int) $project['project_id'] === (int) $project_id) {
                return $project;
            }
        }

        return array();
    }

    // Get a list of all categories in project
    public function boardNotesGetCategories($project_id)
    {
        return $this->db->table(self::TABLEcategories)
            ->columns(self::TABLEcategories . '.id', self::TABLEcategories . '.name', self::TABLEcategories . '.project_id', self::TABLEcategories . '.color_id')
            ->eq('project_id', $project_id)
            ->asc('name')
            ->findAll();
    }

    // Get a list of ALL categories
    public function boardNotesGetAllCategories()
    {
        return $this->db->table(self::TABLEcategories)
            ->columns(self::TABLEcategories . '.id', self::TABLEcategories . '.name', self::TABLEcategories . '.project_id', self::TABLEcategories . '.color_id')
            ->asc('name')
            ->findAll();
    }

    // Get a list of all columns in project
    public function boardNotesGetColumns($project_id)
    {
        return $this->db->table(self::TABLEcolumns)
            ->columns(self::TABLEcolumns . '.id', self::TABLEcolumns . '.title')
            ->eq('project_id', $project_id)
            ->asc('position')
            ->findAll();
    }

    // Get a list of all swimlanes in project
    public function boardNotesGetSwimlanes($project_id)
    {
        $swimlanes = $this->db->table(self::TABLEswimlanes)
            ->columns(self::TABLEswimlanes . '.id', self::TABLEswimlanes . '.name')
            ->eq('project_id', $project_id)
            ->asc('position')
            ->findAll();

        return $swimlanes;
    }

    // Show all notes
    public function boardNotesShowAll($projectsAccess, $user_id)
    {
        $uids = array();
        foreach ($projectsAccess as $u) {
            $uids[] = (int) $u['project_id'];
        }
        if ($uids === array()) {
            return array();
        }

        return $this->db->table(self::TABLEnotes)
            ->eq('user_id', $user_id)
            ->in(self::TABLEnotes . '.project_id', $uids)
            ->desc('project_id')
            ->desc('is_active')
            ->desc('position')
            ->findAll();
    }

    // Delete note
    public function boardNotesDeleteNote($project_id, $user_id, $note_id)
    {
        return $this->db->table(self::TABLEnotes)
            ->eq('id', $note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->remove();
    }

    // Delete note
    public function boardNotesDeleteAllDoneNotes($project_id, $user_id)
    {
        return $this->db->table(self::TABLEnotes)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->eq('is_active', "0")
            ->remove();
    }

    // Add note
    public function boardNotesAddNote($project_id, $user_id, $is_active, $title, $description, $category)
    {
        // Get last position number
        $lastPosition = $this->db->table(self::TABLEnotes)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->desc('position')
            ->findOneColumn('position');

        if (empty($lastPosition)) {
            $lastPosition = 0;
        }

        // Add 1 to position
        $lastPosition++;

        // Get current unixtime
        $t = time();

        // Define values
        $values = array(
            'project_id' => $project_id,
            'user_id' => $user_id,
            'position' => $lastPosition,
            'is_active' => $is_active,
            'title' => $title,
            'description' => $description,
            'date_created' => $t,
            'date_modified' => $t,
            'category' => $category,
        );

        return $this->db->table(self::TABLEnotes)->insert($values);
    }

    // Transfer note
    public function boardNotesTransferNote($project_id, $user_id, $note_id, $target_project_id)
    {
        // Get current unixtime
        $t = time();
        $values = array('project_id' => $target_project_id, 'date_modified' => $t,);

        return $this->db->table(self::TABLEnotes)
            ->eq('id', $note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->update($values);
    }

    // Update note
    public function boardNotesUpdateNote($project_id, $user_id, $note_id, $is_active, $title, $description, $category)
    {
        // Get current unixtime
        $t = time();
        $values = array('is_active' => $is_active, 'title' => $title, 'description' => $description, 'category' => $category, 'date_modified' => $t,);

        return $this->db->table(self::TABLEnotes)
            ->eq('id', $note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->update($values);
    }

    // Update note positions
    public function boardNotesUpdatePosition($project_id, $user_id, $notePositions, $nrNotes)
    {
        $note_ids = array_values(array_unique(array_filter(array_map('intval', explode(',', $notePositions)))));
        $ordered_ids = $note_ids;
        $owned_ids = $this->db->table(self::TABLEnotes)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->in('id', $note_ids)
            ->findAllByColumn('id');

        sort($note_ids);
        $owned_ids = array_map('intval', $owned_ids);
        sort($owned_ids);
        if ($note_ids !== $owned_ids) {
            return false;
        }

        $num = count($note_ids);
        foreach ($ordered_ids as $row) {
            $values = array('position' => $num);

            $this->db->table(self::TABLEnotes)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->eq('id', $row)
            ->update($values);
            $num--;
        }

        return true;
    }

    // Delete note ???
    public function boardNotesAnalytics($project_id, $user_id)
    {
        return $this->db->table(self::TABLEnotes)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->findAll();
    }
}
