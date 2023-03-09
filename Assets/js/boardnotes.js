
  // Show details menu
  $(function() {
    $( "button" + ".showDetails" ).click(function() {
      var id = $(this).attr('data-id');
      var project_id = $(this).attr('data-project');
      $( "#noteDescriptionP" + project_id + "-" + id ).toggleClass( "hideMe", 10 );
      $( "#singleNoteDeleteP" + project_id + "-" + id).toggleClass( "hideMe", 10);
      $( "#singleNoteToTaskP" + project_id + "-" + id).toggleClass( "hideMe", 10);
    });
  });


  // Show details menu for new note
  $(function() {
    $( "button" + ".showDetailsNew" ).click(function() {
      var project_id = $(this).attr('data-project');
      showDetailsNew(project_id);
    });
  });


  // Show details menu for new note (toggle class)
  function showDetailsNew(project_id) {
    $( "#noteDescriptionP" + project_id).toggleClass( "hideMe", 10 );
    setTimeout(function() {
      $( "#textareaNewNote" + project_id).focus();
    }, 0);
  };


  // On TAB key open detailed view
  $(function() {
    $('.newNoteInput').keydown(function(event) {
    if (event.keyCode == 9) {
      var project_id = $(this).attr('data-project');
      showDetailsNew(project_id);
      }
    });
  });


  // On TAB key in description update note
  $(function() {
    $('.textareaDescription').keydown(function(event) {
      if (event.keyCode == 9) {
        var project_id = $(this).attr('data-project');
        var id = $(this).attr('data-id');
        sqlNoteUpdate(project_id, id);
      }
    });
  });


  //Checkmark done
  $(function() {
    $( "button" + ".checkDone" ).click(function() {
      var project_id = $(this).attr('data-project');
      var id = $(this).attr('data-id');
      $( "#noteDoneCheckmarkP" + project_id + "-" + id).toggleClass( "fa-circle-thin", 10 );
      $( "#noteDoneCheckmarkP" + project_id + "-" + id).toggleClass( "fa-check", 10 );
      noteIsDone(project_id, id);
    });
  });


  // Note is done
  function noteIsDone(project_id, id){
    if( $( "#noteDoneCheckmarkP" + project_id + "-" + id).hasClass( "fa-circle-thin" ) ){
      $( "#noteTitleLabelP" + project_id + "-" + id).addClass( "noteDoneDesignText", 10 );
      $( "#noteDescriptionP" + project_id + "-" + id).addClass( "noteDoneDesignText", 10 );
      $( "#noteDoneCheckmarkP" + project_id + "-" + id).attr('data-id', '0');
      sqlNoteUpdate(project_id, id);
    } else {
      $( "#noteTitleLabelP" + project_id + "-" + id).removeClass( "noteDoneDesignText", 10 );
      $( "#noteDescriptionP" + project_id + "-" + id).removeClass( "noteDoneDesignText", 10 );
      $( "#noteDoneCheckmarkP" + project_id + "-" + id).attr('data-id', '1');
      sqlNoteUpdate(project_id, id);
    }
  };


  // SQL note update (title etc. and done)
  function sqlNoteUpdate(project_id, id){
    var project_id = project_id;
    var note_id = $('#note_idP' + project_id + "-" + id).attr('data-id');
    var is_active = $('#noteDoneCheckmarkP' + project_id + "-" + id).attr('data-id');
    var title = $('#noteTitleInputP' + project_id + "-" + id).val();
    var category = $('#catP' + project_id + "-" + id + ' option:selected').text();
    var description = $('#textareaDescriptionP' + project_id + "-" + id).val().replace(/\n/g, '<br >');

    $.ajax({
      type: "POST",
      url: '/kanboard/?controller=BoardnotesController&action=BoardnotesUpdate&plugin=boardnotes' + '&project_id=' + project_id + '&note_id=' + note_id + '&is_active=' + is_active + '&title=' + title + '&description=' + description + '&category=' + category,
      success: function(data) { // Implement failure procedure
      }
    });
     return false;
  }


  function sqlNoteAdd(project_id){
    var descriptionQ = $('#textareaNewNote' + project_id).val();
    if (descriptionQ) {
      var description = $('#textareaNewNote' + project_id).val().replace(/\n/g, '<br >');
    }
    if (!descriptionQ) {
      var description = "";
    }
    var is_active = "1";
    var title = $('#newNote' +  project_id).val();
    $('.newNoteInput').val("");
    var category = $('#catP' + project_id + ' option:selected').text();

    $.ajax({
      type: "POST",
      url: '/kanboard/?controller=BoardnotesController&action=BoardnotesAdd&plugin=boardnotes' + '&project_id=' + project_id + '&is_active=' + is_active + '&title=' + title + '&description=' + description + '&category=' + category,
      success: function(data) {  // Implement failure procedure
      }
     });
    sqlRefreshNotesProject(project_id);
    return false;
  }


  function sqlNoteDeleteSingle(project_id, note_id){
    $.ajax({
      type: "POST",
      url: '/kanboard/?controller=BoardnotesController&action=BoardnotesDelete&plugin=boardnotes' + '&project_id=' + project_id + '&note_id=' + note_id,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
      }
    });
    sqlRefreshNotesProject(project_id);
    return false;
  }


  function sqlDeleteAllDone(project_id){
    $.ajax({
      type: "POST",
      url: '/kanboard/?controller=BoardnotesController&action=BoardnotesDeleteAllDone&plugin=boardnotes' + '&project_id=' + project_id,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
        alert(e);
      }
    });
    sqlRefreshNotesProject(project_id);
    return false;
  }


  function sqlRefreshNotesProject(project_id){
    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
      cache: false
    });
    var ajax_load = "<img src='http://automobiles.honda.com/images/current-offers/small-loading.gif' alt='loading...' />";
    var loadUrl = "/kanboard/?controller=BoardnotesController&action=BoardnotesShowProjectRefresh&plugin=boardnotes&project_id=" + project_id;
    $("#result" + project_id).html(ajax_load).load(loadUrl);

  }


  // Change from label to input on click
  $(function() {
    $( "label" + ".noteTitle" ).click(function() {
      var project_id = $(this).attr('data-project');
      var id = $(this).attr('data-id');
      $("#noteTitleInputP" + project_id + "-" + id).show();
      $("#noteTitleLabelP" + project_id + "-" + id).hide();
      $("#noteTitleInputP" + project_id + "-" + id).focus();
    });
  });


  // POST UPDATE when enter on title
  $(function() {
    $(document).on('keypress', '.noteTitle', function(e) {
      var project_id = $(this).attr('data-project');
      var id = $(this).attr('data-id');
      if (event.keyCode == 13) {
      var title = $('#noteTitleInputP' + project_id + "-" + id).val(); //attr('value');
    $( "#noteTitleInputP" + project_id + "-" + id).blur();
        sqlNoteUpdate(project_id, id);
        $("#noteTitleInputP" + project_id + "-" + id).hide();
        $("#noteTitleLabelP" + project_id + "-" + id).html(title);
        $("#noteTitleLabelP" + project_id + "-" + id).show();
      }
    });
  });


  // POST ADD when enter on title
  $(function() {
    $('.newNoteInput').keypress(function(event) {
      if (event.keyCode == 13) {
        $('.newNoteInput').blur();
        var project_id = $(this).attr('data-project');
        sqlNoteAdd(project_id);
    if (!$('#noteDescription0').hasClass('hideMe') ) {
          $('#noteDescription0').toggleClass('hideMe', 10 );
    }
      }
    });
  });


  // POST ADD on save button
  $(function() {
    $( "button" + ".newNoteSave" ).click(function() {
      var project_id = $(this).attr('data-project');
      $('.newNoteInput').blur();
      sqlNoteAdd(project_id);
      if (!$('#noteDescription0').hasClass('hideMe') ) {
        $('#noteDescription0').toggleClass('hideMe', 10 );
      }
    });
  });


  // POST delete on delete button
  $(function() {
    $( "button" + ".singleNoteDelete" ).click(function() {
      var note_id = $(this).attr('data-id');
      var project_id = $(this).attr('data-project');
      sqlNoteDeleteSingle(project_id, note_id);
    });
  });


  // POST export note to tasks
  $(function() {
    $( "button" + ".singleNoteToTask" ).click(function() {
      var csrf_token = $('[name=csrf_token]').val();
      var id = $(this).attr('data-id');
      if (!id) {
        alert ("You need to refresh the page before exporting");
      }
      var note_id = $(this).attr('data-note');
      var project_id = $(this).attr('data-project');
      var is_active = $('#noteDoneCheckmarkP' + project_id + "-" + id).attr('data-id');
      var title = encodeURIComponent($('#noteTitleLabelP' + project_id + "-" + id).text());
      var description = encodeURIComponent($('#textareaDescriptionP' + project_id + "-" + id).val().replace(/\n/g, '<br >'));
      modalNoteToTask(csrf_token, note_id, project_id, is_active, title, description);
    });
  });

  function modalNoteToTask(csrf_token, note_id, project_id, is_active, title, description) {
    $.ajaxSetup ({
      cache: false
    });
    $( "#dialogToTaskP" + project_id).dialog({
      buttons: {
        Ok: function() {
          var category = $('#listCatToTask' + project_id + ' option:selected').val();
          var columns = $('#listCol' + project_id +' option:selected').val();
          var swimlanes = $('#listSwim' + project_id +' option:selected').val();
          $('#listCatToTask' + project_id).remove();
          $('#listCol' + project_id).remove();
          $('#listSwim' + project_id).remove();
          var ajax_load = "<img src='http://automobiles.honda.com/images/current-offers/small-loading.gif' alt='loading...' />";
// Category is missing
          var loadUrl = '/kanboard/?controller=BoardnotesController&action=BoardnotesToTask&plugin=boardnotes' + '&project_id=' + project_id + '&title=' + title + '&description=' + description + '&swimlanes=' + swimlanes + '&columns=' + columns + '&category_id=' + category;
          $('#deadloading').html(ajax_load).load(loadUrl);
          sqlNoteDeleteSingle(project_id, note_id);   
          $( this ).dialog( "destroy" ).remove();
        }
      }
    });
    return false;
  };


  // POST delete all done
  $(function() {
    $( "button" + ".settingsDeleteAllDone" ).click(function() {
      var project_id = $(this).attr('data-project');
      modalDeleteAllDone(project_id);
    });
  });


 function modalDeleteAllDone(project_id) {
    $( "#dialogDeleteAllDone" ).dialog({
      resizable: false,
      height: "auto",
      modal: true,
      buttons: {
        "Delete all done notes": function() {
          sqlDeleteAllDone(project_id);
          $( this ).dialog( "close" );
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }
    });
  };


  // POST analytics
  $(function() {
    $( "button" + ".settingsAnalytics" ).click(function() {
      var project_id = $(this).attr('data-project');
      modalAnalytics(project_id);
    });
  });


  function modalAnalytics(project_id) {
    $.ajaxSetup ({
        cache: false
    });
    var ajax_load = "<img src='http://automobiles.honda.com/images/current-offers/small-loading.gif' alt='loading...' />";
    var loadUrl = '/kanboard/?controller=BoardnotesController&action=BoardnotesAnalytic&plugin=boardnotes' + '&project_id=' + project_id;
    $('#dialogAnalyticsInside').html(ajax_load).load(loadUrl);

    $( "#dialogAnalytics" ).dialog({
      buttons: {
        Ok: function() {
          $( this ).dialog( "close" );
        }
      }
    });
  };


  // Sort and filter for report
  $(function() {
    $( "button" + ".settingsReport" ).click(function() {
      var project_id = $(this).attr('data-project');
      modalReport(project_id);
    });
  });


  function modalReport(project_id) {
    $( "#dialogReportP" + project_id ).dialog({
      buttons: {
        Ok: function() {
      var category = $('#reportCatP' + project_id + ' option:selected').text();
var url = '/kanboard/?controller=BoardnotesController&action=BoardnotesShowReport&plugin=boardnotes' + '&project_id=' + project_id + '&category=' + category;
window.location = url;
/*
          $.ajax({
            type: "POST",
            url: '/kanboard/?controller=BoardnotesController&action=BoardnotesShowReport&plugin=boardnotes' + '&project_id=' + project_id + '&category=' + category,
            success: function(response) {
            },
              error: function(xhr,textStatus,e) {
              }
            });*/
            $( this ).dialog( "close" );
        }
      }
    });
return true;
  };


  // SQL update positions
  function sqlNoteSort(project_id, order, nrNotes){
    $.ajax({
      type: "POST",
      url: '/kanboard/?controller=BoardnotesController&action=BoardnotesUpdatePosition&plugin=boardnotes' + '&project_id=' + project_id + '&order=' + order + '&nrNotes=' + nrNotes,
      success: function(data) {
      }
    });
    return false;
  }


  // Selector category
  $(function() {
    $( ".catSelector" ).selectmenu({
      change: function( event, data ) {
        var id = $(this).attr('data-id');
        var project_id = $(this).attr('data-project');
        sqlNoteUpdate(project_id, id);
      }
    });
  });


  // Hide note in reporting
  $(function() {
    $( "button" + "#singleReportHide" ).click(function() {
      var id = $(this).attr('data-id');
      $( "#trReportId" + id ).addClass( "hideMe", 10 );
    });
  }); 
