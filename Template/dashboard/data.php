<style>
.mainholderQ {
    width: 750px;
    font: 16px 'Helvetica Neue', Helvetica, Arial, sans-serif;
    color: #4d4d4d;
    -webkit-font-smoothing: antialiased;
    position: relative;
    font-weight: 300;
}

.mainholderMobileQ {
    width: auto;
    font: 16px 'Helvetica Neue', Helvetica, Arial, sans-serif;
    color: #4d4d4d;
    -webkit-font-smoothing: antialiased;
    position: relative;
    font-weight: 300;
}

.hideMe {
    display:none !important;
}
</style>

<script>

  function updateNotesTabs() {
      var tab_id = $( "#tab_id" ).attr('data');

      $( ".singleTab" ).removeClass( 'active' );
      $( "#singleTab" + tab_id ).addClass( 'active' );
      $( "#myNotesHeader h2" ).text( 'My notes > ' + $( "#singleTab" + tab_id ).text());

      var numTabs = $( "#tabs li" ).length;
      var tabHeight = $( "#tabs li:eq(0)" ).outerHeight();
      $( "#tabs" ).height(numTabs * tabHeight);
  }

  function prepareDocumentQ() {
    var isMobile = IsMobile();
    if(isMobile) {
      // choose mobile view
      $('#mainholderQ').removeClass('mainholderQ').addClass('mainholderMobileQ');
    }

    updateNotesTabs();
  }

</script>

<div id="myNotesHeader" class="page-header"><h2>My notes > All</h2></div>

<section id="mainholderQ" class="mainholderQ sidebar-container">

<?php

    $num = 0;
    $tab_id = 0; // by default => no tab selected => show all notes of all projects
    if (!empty($_GET['tab_id'])) {
        $tab_id = intval($_GET['tab_id']);
    }
?>

<div id="tabs" class="sidebar">
  <ul>

    <?php

        // Add a default tab that denotes none project and all notes
        print '<li class="singleTab" id="singleTab';
        print $num;
        print '" data-id="';
        print $num;
        print '" data-project="0"><a href="';
        print '/?controller=BoardNotesController&action=boardNotesShowAll&plugin=BoardNotes&user_id='.$user_id.'&tab_id='.$num;
        print '">All</a></li>';
        $num++;

        // Loop through all projects
        foreach($projectsAccess as $o){
            print '<li class="singleTab" id="singleTab';
            print $num;
            print '" data-id="';
            print $num;
            print '" data-project="';
            print $o['project_id'];
            print '"><a href="';
            print '/?controller=BoardNotesController&action=boardNotesShowAll&plugin=BoardNotes&user_id='.$user_id.'&tab_id='.$num;
            print '">';
            print $o['project_name'];
            print '</a></li>';
            $num++;
        }

    ?>

  </ul>
</div>

<div id="content" class="sidebar-content">

    <?php
        $project = array('id' => 0, 'name' => 'All');
        if ($tab_id > 0) {
            $projectAccess = $projectsAccess[$tab_id - 1];
            $project = array('id' => $projectAccess['project_id'], 'name' => $projectAccess['project_name']);
        }
    ?>

    <?= $this->render('BoardNotes:project/data', array(
        'project' => $project,
        'project_id' => $project['id'],
        'user' => $user,
        'user_id' => $user_id,
        'is_refresh' => False,
        'is_dashboard_view' => 1,
        'data' => $data,
        'categories' => $categories,
        'columns' => $columns,
        'swimlanes' => $swimlanes,
    )) ?>

</div>

</section>

<?php
    // tab_id (hidden, for reference) -->
    print '<div id="tab_id" class="hideMe" data="'.$tab_id.'">';
?>