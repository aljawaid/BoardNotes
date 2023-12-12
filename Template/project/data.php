<?= $this->asset->css('plugins/BoardNotes/Assets/css/style.css') ?>
<?= $this->asset->js('plugins/BoardNotes/Assets/js/boardnotes.js') ?>

<script>
 // On mobile: Hide InputTitle (show label), hide sidebar and define class for view (normal or mobile)
  $( document ).ready(function() {
    $('.noteTitleInput').hide();

    var isMobile = false; //initiate as false
    // device detection
    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
      || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;
    if(isMobile) {
      $('.sidebar').hide();
    }
  });


  // Sortable function
  $(document).ready(function($){
  var nrNotes = $('#nrNotes').attr('data-id');
  //var project_id = $('#projectidref').attr('data-project');
  var project_id = <?php print $project['id']; ?>;
  var isMobile = false; //initiate as false
  // device detection
  if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;

  if(!isMobile) { // If mobile disable sorting
    $('.sortableRef' + project_id).sortable({ items: 'li[id!=item-0]' });
    $(function() {
      $( ".sortableRef" + project_id ).sortable({
        placeholder: "ui-state-highlight",
        update: function (event, ui) {
          var order = $(this).sortable('toArray');
          order = order.join(",");
          var regex = new RegExp('item-', 'g');
          order = order.replace(regex, '');
          var order = order.split(',');
          sqlNoteSort(project_id, order, nrNotes);
          //console.log(order, nrNotes);
        }
      });
      $( "#sortable" ).disableSelection();
    });
   }
  });

</script>

<?= $this->projectHeader->render($project, 'BoardNotesController', 'boardNotesShowProject', false, 'BoardNotes') ?>

<?php
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

print '<li id="item-0" class="ui-state-default liNewNote" data-id="';
print $project_id;
print '">';
print '<label class="labelNewNote" for="textinput" style="font-weight: 700;">Create New Note</label>';

// Settings delete all done
print '<button id="settingsDeleteAllDone" class="settingsDeleteAllDone" data-id="0" data-project="';
print $project_id;
print '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';

// Settings analytics
print '<button id="settingsAnalytics" class="settingsAnalytics" data-id="0" data-project="';
print $project_id;
print '"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';

// Open report
print '<button id="settingsReport" class="settingsReport" data-id="0" data-project="';
print $project_id;
print '">';
print '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
print '</button>';

// Newline after heading and top settings
print '<br>';

// Input line
print '<input id="newNote';
print $project_id;
print '" name="newNote" type="text" placeholder="What needs to be done" class="inputNewNote" data-project="';
print $project_id;
print '">';

// Show details button
print '<button id="showDetailsNew" class="showDetailsNew" data-id="0" data-project="';
print $project_id;
print '"><i class="fa fa-angle-double-down" aria-hidden="true"></i></button>';

// Save button
print '<button class="hideMe saveNewNote" id="saveNewNote" data-project="';
print $project_id;
print '"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>';

// Detailed view
print '<div id="noteDescriptionP';
print $project_id;
print '" data-id="0" class="hideMe details noteDescriptionClass ui-corner-all">';
print '<textarea id="textareaNewNote';
print $project_id;
print '" class="textareaNewNote"></textarea>';

$listCat = '';
// Create category select menu as var
if(!empty($categories)) {
  foreach($categories as $cat) {
    $listCat .= '<option value="';
    $listCat .= $cat['name'];
    $listCat .= '">';
    $listCat .= $cat['name'];
    $listCat .= '</option>';
  }
}

// Print category select menu
print '<p class="categories">';
print '<label for="cat">Category</label><br>';
print '<select name="cat" id="catP';
print $project_id;
print '" data-id="0" data-project="';
print $project_id;
print '" class="catSelector ui-selectmenu-button ui-selectmenu-button-closed ui-corner-all ui-button ui-widget">';
print '<option selected="selected"></option>'; // Insert emptyline for keeping non category by default
print $listCat;
print '</select>';
print '</p>';
print '</div>';

print '</li>';


$num = "1";
foreach($data as $u){
    print '<li id="item';
    print '-';
    print $u['id']; 
    print '" class="ui-state-default liNote">';

    // Checkbox for done note
    print '<button id="checkDone" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" class="checkDone"><i id="noteDoneCheckmarkP';
    print $u['project_id'];
    print '-';
    print $num;
    if($u['is_active'] == "1"){
        print '" data-id="';
        print $u['is_active'];
        print '" class="fa fa-circle-thin" aria-hidden="true"></i></button>';
    } else {
        print '" data-id="';
        print $u['is_active'];
        print '" class="fa fa-check" aria-hidden="true"></i></button>';
    }

    // Show details button
    print '<button id="showDetails';
    print '" class="showDetails" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '"><i class="fa fa-plus" aria-hidden="true"></i></button>';

    // Note title input - typing. Changes after submit to label below.
    print '<input id="noteTitleInputP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" type="text" placeholder="" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" name="noteTitle';
    print $num;
    if($u['is_active'] == "1"){
        print '" class="hideMe noteTitle" value="';
    } else {
        print '" class="hideMe noteTitle noteDoneDesignText" value="';
    }
    print $u['title'];
    print '">';

    // Note title label - visual. Changes on click to input
    print '<label id="noteTitleLabelP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" type="text" placeholder="" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" name="noteTitleLabel';
    print $num;
    if($u['is_active'] == "1"){
        print '" class="noteTitleLabel noteTitle" value="">';
    } else {
        print '" class="noteTitleLabel noteTitle noteDoneDesignText" value="">';
    }
    print $u['title'];
    print '</label>';

    // Delete button viewed (in detailed view)
    print '<button id="singleNoteDeleteP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" class="hideMe singleNoteDelete" data-id="';
    print $u['id'];
    print '" data-project="';
    print $u['project_id'];
    print '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';

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
    print '"><i class="fa fa-share-square-o" aria-hidden="true"></i></button>';

    // Save button (in detailed view)
    print '<button id="singleNoteSaveP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" class="hideMe singleNoteSave" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>';

    // Category label (in simple view)
    print '<label class="catLabel" id="noteCatLabelP';
    print $u['project_id'];
    print '-';
    print $num;
    print '"">';
    print $u['category'];
    print '</label>';

    // Detailed view
    print '<div id="noteDescriptionP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" ';
    if($u['is_active'] == "1"){
        print 'class="hideMe details noteDescriptionClass ui-corner-all">';
    } else {
        print 'class="hideMe details noteDescriptionClass ui-corner-all noteDoneDesignText">';
    }
    print '<textarea title="Press tab to save changes" class="textareaDescription" id="textareaDescriptionP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '">';
    $description = str_ireplace("<br >", "\r\n", $u['description']); 
    print $description;
    print '</textarea>';

	//print '<br>';
    print '<p class="categories">';
    print '<label for="cat">Category</label><br>';
    print '<select name="cat" class="catSelector ui-selectmenu-button ui-selectmenu-button-closed ui-corner-all ui-button ui-widget"';
    print ' id="catP';
    print $project_id;
    print '-';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $project_id;
    print '">';
    
    $emptyCatList = empty($listCat);
    $emptyCat = empty($u['category']);
    
    if ($emptyCatList || $emptyCat){ // If no categories available or none selected
        print '<option selected="selected"></option>'; // None category selected
    }
    if (!$emptyCat && !$emptyCatList){
        print '<option></option>'; // add an empty category option
        foreach($categories as $cat) { // detect the selected category
        	if ($cat['name'] == $u['category']){
        		print '<option selected="selected">';
        	}else{
        		print '<option>';
        	}
	        print $cat['name'];
	        print '</option>';
        }
    }
    if ($emptyCat && !$emptyCatList){
        print $listCat;
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

print '<div id="nrNotes" class="hideMe" data-id="';
$num = --$num;
print $num;
print '"></div>';

print '<div id="projectidref" class="hideMe" data-project="';
print $project_id;
print '"></div>';

$listSwim = '';
foreach($swimlanes as $swim) {
  $listSwim .= '<option value="';
  $listSwim .= $swim['id'];
  $listSwim .= '">';
  $listSwim .= $swim['name'];
  $listSwim .= '</option>';
}

$listCol = '';
foreach($columns as $col) {
  $listCol .= '<option value="';
  $listCol .= $col['id'];
  $listCol .= '">';
  $listCol .= $col['title'];
  $listCol .= '</option>';
}

// Create category select menu as var
$listCatToTask = '';
if(!empty($categories)) {
  foreach($categories as $a) {
    $listCatToTask .= '<option value="';
    $listCatToTask .= $a['id'];
    $listCatToTask .= '">';
    $listCatToTask .= $a['name'];
    $listCatToTask .= '</option>';
  }
}

print '</div>';
?>

<?php
/* id=resultX ending. This is the refresh zone */
?>

<div class="hideMe" id="dialogDeleteAllDone" title="Delete all done notes?">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>These items will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

<div class="hideMe" id="dialogAnalytics" title="Notes Analytics">
  <div id="dialogAnalyticsInside"></div>
</div>

<div class="hideMe" id="dialogToTaskP<?php print $project_id; ?>" title="Data for creating task">
  <div id="">
  <label for="listCatToTask">Category</label>
  <?php
  print '<select name="listCatToTask" id="listCatToTask';
  print $project_id;
  print '">';
  // Only allow blank select if there's other selectable options
  if (!empty($listCatToTask)){
    print '<option></option>';
  }
  print $listCatToTask;
  ?>
  </select>
  <br>

  <label for="listSwim">Swimlane</label>
  <?php
  print '<select name="listSwim" id="listSwim';
  print $project_id;
  print '">';
  // Only allow blank select if there's other selectable options
  if (!empty($listSwim)){
    print '<option></option>';
  }
  print $listSwim;
  ?>
  </select>
  <br>

  <label for="listCol">Column</label>
  <?php
  print '<select name="listCol" id="listCol';
  print $project_id;
  print '">';
  print $listCol;
  ?>
  </select>
  </div>
  <div id="deadloading"></div>
</div>


<div class="hideMe" id="dialogReportP<?php print $project_id; ?>" title="Sorting and filter for reports">
  <div id="">
  <label for="reportCat">Category</label><br>
  <?php
  print '<select name="reportCat" id="reportCatP';
  print $project_id;
  print '" data-project="';
  print $project_id;
  print '">';
  
  print '<option></option>'; // add an empty category option
  if (!empty($listCat)){
      print $listCat;
  }
  ?>
  </select>
  </div>
</div>

</section>
</div>
