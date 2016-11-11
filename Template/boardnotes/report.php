<?= $this->asset->css('plugins/Boardnotes/assets/css/style.css') ?>
<?= $this->asset->js('plugins/Boardnotes/assets/js/boardnotes.js') ?>
<link rel="stylesheet" href="/kanboard/plugins/Boardnotes/assets/css/style.css" media="print">

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


</script>

<p class="page-headerName"><?= t('Reporting') ?></p>
  <br>

<?php

print '<section class="mainholder" id="mainholderP';
print $project['id'];
print '">';

print '<div id="result';
print $project['id'];
print '">';

print '<br>';

?>


<table class="tableReport">
<thead class="theadReport">
<tr>
<th class="thReport thReportNr">Nr</th>
<th class="thReport">Info</th>
<th class="thReport thReportResponsible">Resp</th>
<th class="thReport thReportStatus">Status</th>
</tr>
</thead>
<tbody>
<?php
$num = "1";

foreach($data as $u){
    print '<tr id="trReportId';
    print $num;
    print '">';

    print '<td class="tdReportId">';
//    print '<span class="fa-stack fa-lg">';
//    print '<i class="fa fa-circle-thin fa-stack-2x"></i>';
//    print '<i class="fa fa-inverse fa-stack-1x">';
    print $num;
//    print '</i>';
//    print '</span>';

    print '</td>';

    // Category label
    print '<td class="tdReportInfo">';
    if(!empty($u['category'])){
        print '<label class="catLabel">';
        print $u['category'];
        print '</label>';
    }

    // Note title label - visual. Changes on click to input
    print '<label id="reportTitleLabelP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" type="text" placeholder="" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" name="reportTitleLabel';
    print $num;
    if($u['is_active'] == "1"){
        print '" class="reportTitleLabel reportTitle" value="">';
    } else {
        print '" class="reportTitleLabel reportTitle" value="">';
    }
    print $u['title'];
    print '</label>';

    // Detailed view
    if(!empty($u['description'])) {

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
        print 'class="details reportDescriptionClass ui-corner-all">';
    } else {
        print 'class="details reportDescriptionClass ui-corner-all noteDoneDesignText">';
    }

      print '<span title="Press tab to save changes" class="textareaDescription" id="textareaDescriptionP';
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
      print '</span>';
    }

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


    print '</td><td class="tdReportResponsible">';
    print '</td><td class="tdReportStatus">';

    print '</td>';

    // Delete button viewed in detailed view
    print '<td class="noprint tdReportButton">';
    print '<button id="singleReportHide" class="singleReportHide" data-id="';
    print $num;
    print '"><i class="fa fa-minus-square-o" aria-hidden="true"></i></button>';
    print '</td>';

    print '</tr>';

    // Id
    $num++;
}
?>
</tbody>
</table>

</div>

</section>
