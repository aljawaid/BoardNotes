
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



</style>

<script>

  // Choose mobile view
  $( document ).ready(function() {
    $('.noteTitleInput').hide();
    var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? true : false;
    if(isMobile) {
      $('.sidebar').hide();
      $('#mainholderQ').removeClass('mainholderQ').addClass('mainholderMobileQ');
    }
  });


  $(function() {
    $( "#tabs" ).tabs({
      beforeLoad: function( event, ui ) {
        ui.jqXHR.fail(function() {
          ui.panel.html(
            "Couldn't load this tab. We'll try to fix this as soon as possible. " +
            "If this wouldn't be a demo." );
        });
      }
    });
  });


 $(function() {
    $( ".singleTab" ).click(function() {
      var tab_id = $(this).attr('data-id');
    });
  });



</script>

<p class="page-headerName">Boardnotes</p>
<br>
<section class="mainholderQ" id="mainholderQ">

<div id="tabs">
  <ul>

<?php

$num = "1";
$tab_id = "1";

foreach($projectAccess as $o){

print '<li class="singleTab" id="singletab';
print $num;
print '" data-id="';
print $num;
print '" data-project="';
print $o['project_id'];
print '"><a href="';

print '/kanboard/?controller=BoardnotesController&action=BoardnotesShowProjectRefresh&plugin=boardnotes&project_id=' . $o['project_id'];

print '">';
print $o['project_name'];
print '</a></li>';

$num++;
}
?>

</ul>
</div>

</section>
