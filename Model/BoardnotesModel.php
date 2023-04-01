<?php

namespace Kanboard\Plugin\BoardNotes\Model;

//use PDO;
use Kanboard\Core\Base;
//use Kanboard\Controller\BaseController;


class BoardnNotesModel extends Base
{

	const TABLEnotes = 'boardnotes';
	const TABLEnotescus = 'boardnotes_cus';
	const TABLEaccess = 'project_has_users';
	const TABLEcategories = 'project_has_categories';
	const TABLEprojects = 'projects';
	const TABLEtasks = 'tasks';
	const TABLEcolumns = 'columns';
	const TABLEswimlanes = 'swimlanes';

	public function modelBoardnotesShowNote($note_id) // Show single note
    {

    return $this->db
                ->table(self::TABLEnotes)
                ->eq('id', $note_id)
                ->findAll();

    }

	public function modelBoardnotesShowProject($project_id, $user_id) // Show all notes related to project
    {

    return $this->db
	            ->table(self::TABLEnotes)
				->eq('user_id', $user_id)
				->eq('project_id', $project_id)
				->desc('is_active')
				->desc('position')
	            ->findAll();

    }

	public function modelBoardnotesShowReport($project_id, $user_id, $category) // Show report
    {

	if (empty($category)) {
        return $this->db
			        ->table(self::TABLEnotes)
			  		->eq('user_id', $user_id)
					->eq('project_id', $project_id)
					->desc('is_active')
					->desc('position')
		            ->findAll();
	} else {
        return $this->db
	                ->table(self::TABLEnotes)
					->eq('user_id', $user_id)
					->eq('project_id', $project_id)
					->eq('category', $category)
					->desc('is_active')
					->desc('position')
	                ->findAll();
	}
    }

	public function modelBoardnotesGetProjectid($user_id) // Get all project_id where user has access
    {

    return $this->db
                ->table(self::TABLEaccess)
				->columns(
					self::TABLEaccess.'.project_id',
					'tblPro.name AS project_name'
				)
				->eq('user_id', $user_id)
				->left(self::TABLEprojects, 'tblPro', 'id', self::TABLEaccess, 'project_id')
				->asc('project_name')
           		->findAll();
    }

    public function modelBoardnotesGetProjectidCustom() // Get all project_id where user has access
    {

    return $this->db
                ->table(self::TABLEnotescus)
           		->findAll();

    }

    public function modelBoardnotesGetCategories($project_id) // Get all project_id where user has access
    {

    return $this->db
                ->table(self::TABLEcategories)
                ->columns(
                        self::TABLEcategories.'.id',
                        self::TABLEcategories.'.name',
                        self::TABLEcategories.'.project_id'
                )
		 		//->in(self::TABLEcategories.'.project_id', $projectAccess)
				->eq('project_id', $project_id)
                ->asc('name')
                ->findAll();
    }


    public function modelBoardnotesGetCategoriesId($project_id, $category) // Get all project_id where user has access
    {

    return $this->db
                ->table(self::TABLEcategories)
               // ->columns(
               //         self::TABLEcategories.'.id',
               // )
                ->eq('project_id', $project_id)
                ->eq('name', $category)
                ->findOneColumn('id');


    }

	public function modelBoardnotesShowAll($projectAccess, $user_id) // Show all notes
    {

	foreach($projectAccess as $u) $uids[] = $u['project_id'];
	$projectAccess = implode(", ",$uids);
	substr_replace($projectAccess, "", -2);
	$projectAccess = explode(', ', $projectAccess);

	return $this->db
                ->table(self::TABLEnotes)
				->eq('user_id', $user_id)
				->in(self::TABLEnotes.'.project_id', $projectAccess)
				->desc('project_id')
				->desc('is_active')
				->desc('position')
                ->findAll();
    }

	public function modelBoardnotesDeleteNote($note_id, $user_id) // Delete note
    {

	return $this->db
				->table(self::TABLEnotes)
				->eq('id', $note_id)
				->eq('user_id', $user_id)
				->remove();
    }


	public function modelBoardnotesDeleteAllDone($project_id, $user_id) // Delete note
    {

	return $this->db
				->table(self::TABLEnotes)
				->eq('project_id', $project_id)
				->eq('user_id', $user_id)
				->eq('is_active', "0")
				->remove();
    }

	public function modelBoardnotesUpdateNote($user_id, $note_id, $is_active, $title, $description, $category) // Update note
    {

	// Get current unixtime
    $t = time();

    $values = array(
                    'is_active' => $is_active,
                    'title' => $title,
                    'description' => $description,
					'category' => $category,
                    'date_modified' => $t,
    				);

    return $this->db
                ->table(self::TABLEnotes)
                ->eq('id', $note_id)
				->eq('user_id', $user_id)
                ->update($values);
    }


	public function modelBoardnotesAddNote($project_id, $user_id, $is_active, $title, $description, $category) // Add note
    {

	// Get last position number
	$lastPosition = $this->db
						->table(self::TABLEnotes)
						->eq('project_id', $project_id)
						->desc('position')
						->findOneColumn('position');

	if (empty($lastPosition)){
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

        return $this->db
	                ->table(self::TABLEnotes)
	                ->insert($values);

    }



	public function modelBoardnotesUpdatePosition($notePositions, $nrNotes) // Update note positions
    {

	unset($num);
	unset($note_id);

	// Ser $num to nr of notes to max
	$num = $nrNotes;

	//  Explode all positions
	$note_id = explode(',', $notePositions);

	// Loop through all positions
	foreach ($note_id as $row){

        $values = array('position' => $num);

        $this->db
            ->table(self::TABLEnotes)
            ->eq('id', $row)
            ->update($values);

		$num--;
	}

    }


	public function modelBoardnotesToTaskSupplyDataSwi($project_id)
	{
	// Get swimlanes
    $swimlanes = $this->db
		            ->table(self::TABLEswimlanes)
					->columns(
						self::TABLEswimlanes.'.id',
						self::TABLEswimlanes.'.name'
					)
		            ->eq('project_id', $project_id)
					->asc('position')
		            ->findAll();

	return $swimlanes;
	}


	public function modelBoardnotesToTaskSupplyDataCol($project_id)
	{
    // Get first column_id
    return $this->db
	            ->table(self::TABLEcolumns)
				->columns(
					self::TABLEcolumns.'.id',
					self::TABLEcolumns.'.title'
				)
	            ->eq('project_id', $project_id)
	            ->asc('position')
	            ->findAll();

	}


	public function modelBoardnotesAnalytics($project_id, $user_id) // Delete note
    {

    return $this->db
	            ->table(self::TABLEnotes)
	            ->eq('project_id', $project_id)
	            ->eq('user_id', $user_id)
	            ->findAll();

    }

}

