<?= $this->asset->css('plugins/BoardNotes/Assets/css/style.css') ?>
<?= $this->asset->js('plugins/BoardNotes/Assets/js/boardnotes.js') ?>

<script>

  function IsMobile(){
    // device detection
    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))){
      return true;
    }
    return false;
  }

  function adjustAllNotesPlaceholders(){
    // adjust notePlaceholderDescription containers where not needed
    $('button' + '.checkDone').each(function() {
      var project_id = $(this).attr('data-project');
      var id = $(this).attr('data-id');
      adjustNotePlaceholders(project_id, id);
    })
  }

  function prepareDocument(){
    var nrNotes = $('#nrNotes').attr('data-id');
    var project_id = <?php print $project_id; ?>;
    var user_id = <?php print $user_id; ?>;
    var isMobile = IsMobile();

    if (project_id != 0) {
        // handle notes reordering
        function updateNotesOrder(event, ui) {
            var order = $(this).sortable('toArray');
            order = order.join(",");
            var regex = new RegExp('item-', 'g');
            order = order.replace(regex, '');
            var order = order.split(',');
            sqlNotesUpdatePosition(project_id, user_id, order, nrNotes);
        }

        if (isMobile){
          // show explicit reorder handles for mobile
          $( '.sortableHandle').removeClass( "hideMe" );
          $(function() {
            $( '#sortable').sortable({
              handle: '.sortableHandle',
              placeholder: "ui-state-highlight",
              update: updateNotesOrder
            });
            $( "#sortable" ).disableSelection();
          });
        }
        else{
          // drag entire notes for non-mobile
          $( '.sortableRef' + project_id ).sortable({ items: 'li[id!=item-0]' });
          $(function() {
            $( '.sortableRef' + project_id ).sortable({
              placeholder: "ui-state-highlight",
              update: updateNotesOrder
            });
            $( "#sortable" ).disableSelection();
          });
        }
    }

    if(isMobile) {
      // choose mobile view
      $('#mainholderP' + project_id).removeClass('mainholder').addClass('mainholderMobile');
    }

    adjustAllNotesPlaceholders();

    if ( $.isFunction(prepareDocumentQ) ) {
        prepareDocumentQ();
    }
  }

  window.onresize = adjustAllNotesPlaceholders;
  window.onload = prepareDocument;
  $( document ).ready( prepareDocument );

</script>

<?= ($is_refresh || $is_dashboard_view) ? '' : $this->projectHeader->render($project, 'BoardNotesController', 'boardNotesShowProject', false, 'BoardNotes') ?>

<?php

//----------------------------------------

$readonlyNotes = ($project_id == 0);
$projectsTabsById = array();
if ($is_dashboard_view) {
    $tab_id = 1;
    foreach($projectsAccess as $projectAccess) {
        $projectsTabsById[ $projectAccess['project_id'] ] = array('tab_id' => $tab_id, 'name' => $projectAccess['project_name']);
        $tab_id++;
    }
}

//----------------------------------------

$listCategoriesById = '';
if(!empty($categories)) {
  foreach($categories as $cat) {
    $listCategoriesById .= '<option value="';
    $listCategoriesById .= $cat['id'];
    $listCategoriesById .= '">';
    $listCategoriesById .= $cat['name'];
    $listCategoriesById .= '</option>';
  }
}

$listColumnsById = '';
if(!empty($columns)) {
  foreach($columns as $col) {
    $listColumnsById .= '<option value="';
    $listColumnsById .= $col['id'];
    $listColumnsById .= '">';
    $listColumnsById .= $col['title'];
    $listColumnsById .= '</option>';
  }
}

$listSwimlanesById = '';
if(!empty($swimlanes)) {
  foreach($swimlanes as $swim) {
    $listSwimlanesById .= '<option value="';
    $listSwimlanesById .= $swim['id'];
    $listSwimlanesById .= '">';
    $listSwimlanesById .= $swim['name'];
    $listSwimlanesById .= '</option>';
  }
}

//----------------------------------------

print '<div align="center">';
print '<section class="mainholder" id="mainholderP';
print $project_id;
print '">';

print '<div align="left" id="result';
print $project_id;
print '">';

print '<ul id="sortable" class="sortableRef';
print $project_id;
print '">';

//----------------------------------------

if (!$readonlyNotes) {
    print '<li id="item-0" class="ui-state-default liNewNote" data-id="0" data-project="';
    print $project_id;
    print '">';
    print '<label class="labelNewNote" for="textinput" style="font-weight: 700;">Create New Note</label>';

    // Settings delete all done
    print '<button id="settingsDeleteAllDone" class="settingsDeleteAllDone" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';

    // Settings analytics
    print '<button id="settingsAnalytics" class="settingsAnalytics" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';

    // Open report
    print '<button id="settingsReport" class="settingsReport" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-file-text-o" aria-hidden="true"></i>';
    print '</button>';

    // Newline after heading and top settings
    print '<br>';

    print '<div class="containerNoWrap containerFloatRight">';

    // Show details button
    print '<button id="showDetailsNew" class="showDetailsNew" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-angle-double-down" aria-hidden="true"></i></button>';

    // Save button
    print '<button class="hideMe saveNewNote" id="saveNewNote" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>';

    print '</div>';

    // Input line
    print '<input id="newNote';
    print $project_id;
    print '" name="newNote" type="text" placeholder="What needs to be done" class="inputNewNote" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '">';

    // Detailed view
    print '<div id="noteDescriptionP';
    print $project_id;
    print '" data-id="0" class="hideMe details containerFloatClear noteDescriptionClass ui-corner-all">';
    print '<textarea id="textareaNewNote';
    print $project_id;
    print '" class="textareaNewNote"></textarea>';

    // Print category select menu
    print '<p class="categories">';
    print '<label for="cat">Category</label><br>';
    print '<select name="cat" id="catP';
    print $project_id;
    print '" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '" class="catSelector ui-selectmenu-button ui-selectmenu-button-closed ui-corner-all ui-button ui-widget">';
    print '<option selected="selected"></option>'; // Insert empty line for keeping non category by default
    print $listCategoriesById;
    print '</select>';
    print '</p>';

    print '</div>';

    print '</li>';
}

//----------------------------------------

$num = "1";
$last_project_id = 0;
foreach($data as $u){
    if (!empty($project_id) && $u['project_id'] != $project_id) continue;

    if ($readonlyNotes && $last_project_id != $u['project_id']){
        $last_project_id = $u['project_id'];
        print '<h2><a href="';
        print '/?controller=BoardNotesController&action=boardNotesShowAll&plugin=BoardNotes&user_id='.$user_id.'&tab_id='.$projectsTabsById[ $last_project_id ]['tab_id'];
        print '">'.$projectsTabsById[ $last_project_id ]['name'].'</a></h2>';
    }

    print '<li id="item';
    print '-';
    print $u['id']; 
    print '" class="ui-state-default liNote" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '">';

    // Here goes the icon bar for all note buttons
    print '<div class="containerNoWrap containerFloatRight">';

    // explicit reorder handle for mobile
    print '<div class="hideMe sortableHandle"><i class="fa fa-arrows-alt" aria-hidden="true"></i></div>';

    // Show details button
    print '<button id="showDetails';
    print $u['project_id'];
    print '-';
    print $num;
    print '" class="showDetails" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-angle-double-down" aria-hidden="true"></i></button>';

    // hide all the utility buttons when viewing notes as readonly
    // just allow for check/uncheck note
    if (!$readonlyNotes){
        // Delete button viewed (in detailed view)
        print '<button id="singleNoteDeleteP';
        print $u['project_id'];
        print '-';
        print $num;
        print '" class="hideMe singleNoteDelete" data-id="';
        print $u['id'];
        print '" data-project="';
        print $u['project_id'];
        print '" data-user="';
        print $user_id;
        print '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';

        // custom notes projects obviously CANNOT create tasks from notes
        if (!$project['is_custom']) {
            // Add note to tasks table (in detailed view)
            print '<button id="singleNoteToTaskP';
            print $u['project_id'];
            print '-';
            print $num;
            print '" class="hideMe singleNoteToTask" data-id="';
            print $num;
            print '" data-note="';
            print $u['id'];
            print '" data-project="';
            print $u['project_id'];
            print '" data-user="';
            print $user_id;
            print '"><i class="fa fa-share-square-o" aria-hidden="true"></i></button>';
        }

        // Save button (in detailed view)
        print '<button id="singleNoteSaveP';
        print $u['project_id'];
        print '-';
        print $num;
        print '" class="hideMe singleNoteSave" data-id="';
        print $num;
        print '" data-project="';
        print $u['project_id'];
        print '" data-user="';
        print $user_id;
        print '"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>';
    }

    // Category label (in simple view)
    print '<label class="catLabel" id="noteCatLabelP';
    print $u['project_id'];
    print '-';
    print $num;
    print '">';
    print $u['category'];
    print '</label>';

    print '</div>';

    // Here goes the title row with checkbox
    print '<div class="containerNoWrap containerFloatLeft">';

    // Checkbox for done note
    print '<button id="checkDone';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '" class="checkDone"><i id="noteDoneCheckmarkP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $u['is_active'];
    print '" ';
        if($u['is_active'] == "2"){
            print 'class="fa fa-spinner fa-spin" aria-hidden="true"></i>';
        }
        if($u['is_active'] == "1"){
            print 'class="fa fa-circle-thin" aria-hidden="true"></i>';
        }
        if($u['is_active'] == "0"){
            print 'class="fa fa-check" aria-hidden="true"></i>';
        }
    print '</button>';

    // Note title input - typing. Changes after submit to label below.
    print '<input ';
    if ($readonlyNotes) print 'disabled ';
    print 'id="noteTitleInputP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" type="text" placeholder="" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '" name="noteTitle';
    print $num;
    if($u['is_active'] == "0"){
        print '" class="hideMe noteTitle noteDoneDesignText" value="';
    } else {
        print '" class="hideMe noteTitle" value="';
    }
    print $u['title'];
    print '">';

    // Note title label - visual. Changes on click to input
    print '<label ';
    if ($readonlyNotes) print 'data-disabled="true" ';
    print 'id="noteTitleLabelP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" type="text" placeholder="" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '" name="noteTitleLabel';
    print $num;
    if($u['is_active'] == "0"){
        print '" class="noteTitleLabel noteTitle noteDoneDesignText" value="">';
    } else {
        print '" class="noteTitleLabel noteTitle" value="">';
    }
    print $u['title'];
    print '</label>';

    print '</div>';

    // Here goes the detailed view
    print '<div id="notePlaceholderDescriptionP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" class="containerFloatClear hideMe">';
    print '&nbsp';
    print '</div>';

    print '<div id="noteDescriptionP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '" ';
    if($u['is_active'] == "0"){
        print 'class="hideMe details containerFloatClear noteDescriptionClass ui-corner-all noteDoneDesignText">';
    } else {
        print 'class="hideMe details containerFloatClear noteDescriptionClass ui-corner-all">';
    }
    print '<textarea ';
    if ($readonlyNotes) print 'disabled ';
    print 'title="Press tab to save changes" class="textareaDescription" id="textareaDescriptionP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '">';
    $description = str_ireplace("<br >", "\r\n", $u['description']); 
    print $description;
    print '</textarea>';

    print '<p class="categories">';
    print '<label for="cat">Category</label><br>';
    print '<select ';
    if ($readonlyNotes) print 'data-disabled="true" ';
    print 'name="cat" class="catSelector ui-selectmenu-button ui-selectmenu-button-closed ui-corner-all ui-button ui-widget"';
    print ' id="catP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '">';

    if ($readonlyNotes){
        // just preserve the existing category data from the note
        print '<option selected="selected">'.$u['category'].'</option>';
    }
    else{
        $emptyCatList = empty($listCategoriesById);
        $emptyCat = empty($u['category']);

        if ($emptyCatList || $emptyCat){ // If no categories available or none selected
            print '<option selected="selected"></option>'; // None category selected
        }
        if (!$emptyCat && !$emptyCatList){
            print '<option></option>'; // add an empty category option
            foreach($categories as $cat) { // detect the selected category
                if ($cat['name'] == $u['category']){
                    print '<option value="'.$cat['id'].'" selected="selected">';
                }else{
                    print '<option value="'.$cat['id'].'">';
                }
                print $cat['name'];
                print '</option>';
            }
        }
        if ($emptyCat && !$emptyCatList){
            print $listCategoriesById;
        }
    }

    print '</select>';
    print '</p>';

    print '</div>';

    // Project_id (hidden, for reference)
    print '<div id="project_id';
    print $num;
    print '" data-id="';
    print $u['project_id'];
    print '" class="hideMe">';
    print '</div>';

    // Note_id (hidden, for reference)
    print '<div id="note_idP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $u['id'];
    print '" class="hideMe">';
    print '</div>';

    print '</li>';

    // Id
    $num++;
}

print '</ul>';

//----------------------------------------

print '<div id="nrNotes" class="hideMe" data-id="';
$num = --$num;
print $num;
print '"></div>';

print '<div id="projectidref" class="hideMe" data-project="';
print $project_id;
print '" data-user="';
print $user_id;
print '"></div>';


print '</div>';

//----------------------------------------
// id=result ending. This is the refresh zone
?>


<div class="hideMe" id="dialogDeleteAllDone" title="Delete all done notes?">
  <p>
    <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
    These items will be permanently deleted and cannot be recovered. Are you sure?
  </p>
</div>

<div class="hideMe" id="dialogAnalytics" title="Notes Analytics">
  <div id="dialogAnalyticsInside"></div>
</div>

<?php
  //---------------------------------------------

  print '<div class="hideMe" id="dialogToTaskP'.$project_id.'" title="Data for creating task">';

  print '<div id="dialogToTaskParams">';

  print '<label for="listCatToTask">Category : &nbsp;</label>';
  print '<select name="listCatToTask" id="listCatToTask';
  print $project_id;
  print '">';
  // Only allow blank select if there's other selectable options
  if (!empty($listCategoriesById)){
    print '<option></option>';
  }
  print $listCategoriesById;
  print '</select>';
  print '<br>';

  print '<label for="listColToTask">Column : &nbsp;</label>';
  print '<select name="listColToTask" id="listColToTask';
  print $project_id;
  print '">';
  print $listColumnsById;
  print '</select>';
  print '<br>';

  print '<label for="listSwimToTask">Swimlane : &nbsp;</label>';
  print '<select name="listSwimToTask" id="listSwimToTask';
  print $project_id;
  print '">';
  print $listSwimlanesById;
  print '</select>';
  print '<br>';

  print '<input type="checkbox" checked name="removeNote" id="removeNote';
  print $project_id;
  print '">';
  print '<label for="removeNote"> Remove the note</label>';

  print '</div>';

  print '<div id="deadloading" class="hideMe"></div>';
  print '</div>';

  //---------------------------------------------

  print '<div class="hideMe" id="dialogReportP'.$project_id.'" title="Sorting and filter for reports">';
  print '<div id="">';
  print '<label for="reportCat">Category</label><br>';
  print '<select name="reportCat" id="reportCatP';
  print $project_id;
  print '" data-project="';
  print $project_id;
  print '" data-user="';
  print $user_id;
  print '">';
  
  print '<option></option>'; // add an empty category option
  if (!empty($listCategoriesById)){
      print $listCategoriesById;
  }

  print '</select>';
  print '</div>';
  print '</div>';

  //---------------------------------------------

  print '</section>';
  print '</div>';

?>
