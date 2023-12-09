<?php

namespace Kanboard\Plugin\BoardNotes\Model;

use Kanboard\Core\Base;

class BoardNotesModel extends Base
{
    const TABLEnotes = 'boardnotes';
    const TABLEnotescus = 'boardnotes_cus';
    const TABLEaccess = 'project_has_users';
    const TABLEcategories = 'project_has_categories';
    const TABLEprojects = 'projects';
    const TABLEtasks = 'tasks';
    const TABLEcolumns = 'columns';
    const TABLEswimlanes = 'swimlanes';

    // Show all notes related to project
    public function boardNotesShowProject($project_id, $user_id)
    {
        return $this->db->table(self::TABLEnotes)->eq('user_id', $user_id)->eq('project_id', $project_id)->desc('is_active')->desc('position')->findAll();
    }

    // Show report
    public function boardNotesShowReport($project_id, $user_id, $category)
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

    // Get all project_id where user has access
    public function boardNotesGetProjectID($user_id)
    {
        return $this->db->table(self::TABLEaccess)
            ->columns(self::TABLEaccess.'.project_id', 'tblPro.name AS project_name')
            ->eq('user_id', $user_id)
            ->left(self::TABLEprojects, 'tblPro', 'id', self::TABLEaccess, 'project_id')
            ->asc('project_name')
            ->findAll();
    }

    // Get all project_id where user has access
    public function boardNotesGetProjectIDCustom()
    {
        return $this->db->table(self::TABLEnotescus)->findAll();
    }

    // Get all project_id where user has access
    public function boardNotesGetCategories($project_id)
    {
        return $this->db->table(self::TABLEcategories)
            ->columns(self::TABLEcategories.'.id', self::TABLEcategories.'.name', self::TABLEcategories.'.project_id')
            //->in(self::TABLEcategories.'.project_id', $projectAccess)
            ->eq('project_id', $project_id)
            ->asc('name')
            ->findAll();
    }

    // Get all project_id where user has access
    public function boardNotesGetCategoriesID($project_id, $category)
    {
        return $this->db->table(self::TABLEcategories)
            // ->columns(
            //         self::TABLEcategories.'.id',
            // )
            ->eq('project_id', $project_id)
            ->eq('name', $category)
            ->findOneColumn('id');
    }

    // Show all notes
    public function boardNotesShowAll($projectAccess, $user_id)
    {
        foreach ($projectAccess as $u) $uids[] = $u['project_id'];
        $projectAccess = implode(", ", $uids);
        substr_replace($projectAccess, "", -2);
        $projectAccess = explode(', ', $projectAccess);

        return $this->db->table(self::TABLEnotes)
            ->eq('user_id', $user_id)
            ->in(self::TABLEnotes.'.project_id', $projectAccess)
            ->desc('project_id')
            ->desc('is_active')
            ->desc('position')
            ->findAll();
    }

    // Delete note
    public function boardNotesDeleteNote($note_id, $user_id)
    {
        return $this->db->table(self::TABLEnotes)->eq('id', $note_id)->eq('user_id', $user_id)->remove();
    }

    // Delete note
    public function boardNotesDeleteAllDone($project_id, $user_id)
    {
        return $this->db->table(self::TABLEnotes)->eq('project_id', $project_id)->eq('user_id', $user_id)->eq('is_active', "0")->remove();
    }

    // Update note
    public function boardNotesUpdateNote($user_id, $note_id, $is_active, $title, $description, $category)
    {
        // Get current unixtime
        $t = time();
        $values = array('is_active' => $is_active, 'title' => $title, 'description' => $description, 'category' => $category, 'date_modified' => $t,);

        return $this->db->table(self::TABLEnotes)->eq('id', $note_id)->eq('user_id', $user_id)->update($values);
    }

    // Add note
    public function boardNotesAddNote($project_id, $user_id, $is_active, $title, $description, $category)
    {
        // Get last position number
        $lastPosition = $this->db->table(self::TABLEnotes)->eq('project_id', $project_id)->desc('position')->findOneColumn('position');

        if (empty($lastPosition)) {
            $lastPosition = 1;
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
    public function boardNotesUpdatePosition($notePositions, $nrNotes)
    {
        unset($num);
        unset($note_id);

        // Ser $num to nr of notes to max
        $num = $nrNotes;

        //  Explode all positions
        $note_id = explode(',', $notePositions);

        // Loop through all positions
        foreach ($note_id as $row) {
            $values = array('position' => $num);

            $this->db->table(self::TABLEnotes)->eq('id', $row)->update($values);
            $num--;
        }
    }

    public function boardNotesToTaskSupplyDataSwi($project_id)
    {
        // Get swimlanes
        $swimlanes = $this->db->table(self::TABLEswimlanes)->columns(self::TABLEswimlanes.'.id', self::TABLEswimlanes.'.name')->eq('project_id', $project_id)->asc('position')->findAll();

        return $swimlanes;
    }

    public function boardNotesToTaskSupplyDataCol($project_id)
    {
        // Get first column_id
        return $this->db->table(self::TABLEcolumns)->columns(self::TABLEcolumns.'.id', self::TABLEcolumns.'.title')->eq('project_id', $project_id)->asc('position')->findAll();
    }

    // Delete note ???
    public function boardNotesAnalytics($project_id, $user_id)
    {
        return $this->db->table(self::TABLEnotes)->eq('project_id', $project_id)->eq('user_id', $user_id)->findAll();
    }
}
