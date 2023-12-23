<?php

namespace Kanboard\Plugin\BoardNotes\Model;

use Kanboard\Core\Base;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\CategoryModel;

class BoardNotesModel extends Base
{
    const TABLEnotes = 'boardnotes';
    const TABLEnotescus = 'boardnotes_cus';
    const TABLEprojects = ProjectModel::TABLE;
    const TABLEtasks = TaskModel::TABLE;
    const TABLEcolumns = ColumnModel::TABLE;
    const TABLEswimlanes = SwimlaneModel::TABLE;
    const TABLEaccess = ProjectUserRoleModel::TABLE;
    const TABLEcategories = CategoryModel::TABLE;

    // Show single note
    public function boardNotesShowNote($note_id)
    {
        return $this->db->table(self::TABLEnotes)->eq('id', $note_id)->findAll();
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
        $projectIds = $this->db->table(self::TABLEaccess)
            ->columns(self::TABLEaccess.'.project_id', 'alias_projects_table.name AS project_name')
            ->eq('user_id', $user_id)
            ->left(self::TABLEprojects, 'alias_projects_table', 'id', self::TABLEaccess, 'project_id')
            ->asc('project_name')
            ->findAll();
        foreach($projectIds as &$projectId){ $projectId['is_custom'] = False; }
        return $projectIds;
    }

    // Get all project_id where user has custom access
    public function boardNotesGetCustomProjectIds()
    {
        $projectIds = $this->db->table(self::TABLEnotescus)
            ->columns(self::TABLEnotescus.'.project_id', self::TABLEnotescus.'.project_name')
            ->findAll();
        foreach($projectIds as &$projectId){ $projectId['is_custom'] = True; }
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

    // Get a list of all categories in project
    public function boardNotesGetCategories($project_id)
    {
        return $this->db->table(self::TABLEcategories)
            ->columns(self::TABLEcategories.'.id', self::TABLEcategories.'.name', self::TABLEcategories.'.project_id', self::TABLEcategories.'.color_id')
            ->eq('project_id', $project_id)
            ->asc('name')
            ->findAll();
    }

    // Get a list of ALL categories
    public function boardNotesGetAllCategories()
    {
        return $this->db->table(self::TABLEcategories)
            ->columns(self::TABLEcategories.'.id', self::TABLEcategories.'.name', self::TABLEcategories.'.project_id', self::TABLEcategories.'.color_id')
            ->asc('name')
            ->findAll();
    }

    // Get a list of all columns in project
    public function boardNotesGetColumns($project_id)
    {
        return $this->db->table(self::TABLEcolumns)
            ->columns(self::TABLEcolumns.'.id', self::TABLEcolumns.'.title')
            ->eq('project_id', $project_id)
            ->asc('position')
            ->findAll();
    }

    // Get a list of all swimlanes in project
    public function boardNotesGetSwimlanes($project_id)
    {
        $swimlanes = $this->db->table(self::TABLEswimlanes)
            ->columns(self::TABLEswimlanes.'.id', self::TABLEswimlanes.'.name')
            ->eq('project_id', $project_id)
            ->asc('position')
            ->findAll();

        return $swimlanes;
    }

    // Show all notes
    public function boardNotesShowAll($projectsAccess, $user_id)
    {
        foreach ($projectsAccess as $u) $uids[] = $u['project_id'];
        $projectsAccess = implode(", ", $uids);
        substr_replace($projectsAccess, "", -2);
        $projectsAccess = explode(', ', $projectsAccess);

        return $this->db->table(self::TABLEnotes)
            ->eq('user_id', $user_id)
            ->in(self::TABLEnotes.'.project_id', $projectsAccess)
            ->desc('project_id')
            ->desc('is_active')
            ->desc('position')
            ->findAll();
    }

    // Delete note
    public function boardNotesDeleteNote($note_id, $project_id, $user_id)
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

    // Add note
    public function boardNotesAddNote($project_id, $user_id, $is_active, $title, $description, $category)
    {
        // Get last position number
        $lastPosition = $this->db->table(self::TABLEnotes)
            ->eq('project_id', $project_id)
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

    // Update note positions
    public function boardNotesUpdatePosition($project_id, $user_id, $notePositions, $nrNotes)
    {
        unset($num);
        unset($note_id);

        // Ser $num to nr of notes to max
        $num = $nrNotes;

        //  Explode all positions
        $note_ids = explode(',', $notePositions);


        // Loop through all positions
        foreach ($note_ids as $row) {
            $values = array('position' => $num);

            $this->db->table(self::TABLEnotes)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->eq('id', $row)
            ->update($values);
            $num--;
        }
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
