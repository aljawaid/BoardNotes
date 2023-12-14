
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

.ui-tabs-vertical { width: 55em; }
.ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
.ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
.ui-tabs-vertical .ui-tabs-nav li a { display:block; }
.ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; }
.ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 40em;}

.tabs { padding-left: 20px; }

.mainholder {
  display: initial;
}
</style>

<script>

// Choose mobile view
function PrepareMobile() {
  $('.noteTitleInput').hide();
  var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? true : false;
  if(isMobile) {
    $('.sidebar').hide();
    $('#mainholderQ').removeClass('mainholderQ').addClass('mainholderMobileQ');
  }
};


window.onload = PrepareMobile;
$( document ).ready( PrepareMobile );


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


$( function() {
  $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
  $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
} );

</script>



<div class="page-header"><h2>My notes</h2></div>

<section class="mainholderQ" id="mainholderQ">

<div id="tabs" class="tabs">
  <ul>

    <?php

    $num = "1";
    $tab_id = "1";

    // Loop through all projects
    foreach($projectsAccess as $o){
        print '<li class="singleTab" id="singletab';
        print $num;
        print '" data-id="';
        print $num;
        print '" data-project="';
        print $o['project_id'];
        print '"><a href="';

        if ($o['is_custom'])
        {
            print '/kanboard/?controller=BoardNotesController&action=boardNotesShowProject&plugin=BoardNotes&project_cus_id='.$o['project_id'].'&user_id='.$user_id;
        }
        else
        {
            print '/kanboard/?controller=BoardNotesController&action=boardNotesShowProject&plugin=BoardNotes&project_id='.$o['project_id'].'&user_id='.$user_id;
        }

        print '">';
        print $o['project_name'];
        print '</a></li>';

        $num++;
    }

    ?>

  </ul>
</div>

</section>
