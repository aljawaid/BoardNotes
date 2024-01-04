  // Adjust notePlaceholderDescription container
  function adjustNotePlaceholders(project_id, id) {
    var offsetCheck = $("#checkDone" + project_id + "-" + id).offset().top;
    var offsetDetails = $("#showDetails" + project_id + "-" + id).offset().top;
    if (offsetCheck == offsetDetails) {
      $("#notePlaceholderDescriptionP" + project_id + "-" + id).addClass( 'hideMe' );
    }
    else {
      $("#notePlaceholderDescriptionP" + project_id + "-" + id).removeClass( 'hideMe' );
    }
  }

  // Show details for existing notes (toggle class)
  function toggleDetails(project_id, id) {
    $("#noteDescriptionP" + project_id + "-" + id).toggleClass( "hideMe" );
    $("#singleNoteDeleteP" + project_id + "-" + id).toggleClass( "hideMe" );
    $("#singleNoteSaveP" + project_id + "-" + id).toggleClass( "hideMe" );
    $("#singleNoteTransferP" + project_id + "-" + id).toggleClass( "hideMe" );
    $("#singleNoteToTaskP" + project_id + "-" + id).toggleClass( "hideMe" );
    $("#noteCatLabelP" + project_id + "-" + id).toggleClass( "hideMe" );

    $("#showDetails" + project_id + "-" + id).find('i').toggleClass( "fa-angle-double-down" );
    $("#showDetails" + project_id + "-" + id).find('i').toggleClass( "fa-angle-double-up" );
    adjustNotePlaceholders(project_id, id);
  };

  // Show details menu for new note (toggle class)
  function toggleDetailsNew(project_id) {
    if ( !$('#noteDescriptionP' + project_id).hasClass( 'hideMe' ) ) {
        $("#newNote" + project_id).width( $('#textareaNewNote' + project_id).width() );
    }

    $("#noteDescriptionP" + project_id).toggleClass( "hideMe" );
    $("#saveNewNote").toggleClass( "hideMe" );

    if ( !$('#noteDescriptionP' + project_id).hasClass( 'hideMe' ) ) {
        $("#newNote" + project_id).width( $('#textareaNewNote' + project_id).width() );
    }

    $("#showDetailsNew").find('i').toggleClass( "fa-angle-double-down" );
    $("#showDetailsNew").find('i').toggleClass( "fa-angle-double-up" );

    setTimeout(function() { $( "#textareaNewNote" + project_id).focus(); }, 0);
  };

  // Blink note
  function blinkNote(project_id, id){
    var note_id = $('#note_idP' + project_id + "-" + id).attr('data-id');
    setTimeout(function() { $( "#item-" + note_id ).addClass( "blurMe" ); }, 0);
    setTimeout(function() { toggleDetails(project_id, id); }, 100);
    setTimeout(function() { toggleDetails(project_id, id); }, 200);
    setTimeout(function() { $( "#item-" + note_id ).removeClass( "blurMe" ); }, 300);
  };


  // Show details for note by dblclick the note
  $(function() {
    $( ".liNote" ).dblclick(function() {
      var project_id = $(this).attr('data-project');
      var id = $(this).attr('data-id');
      toggleDetails(project_id, id);
    });
  });

  // Show details for note by menu button
  $(function() {
    $( "button" + ".showDetails" ).click(function() {
      var project_id = $(this).attr('data-project');
      var id = $(this).attr('data-id');
      toggleDetails(project_id, id);
    });
  });

  // Show details for new note by dblclick the new note
  $(function() {
    $( ".liNewNote" ).dblclick(function() {
      var project_id = $(this).attr('data-project');
      toggleDetailsNew(project_id);
    });
  });

  // Show details for new note by menu button
  $(function() {
    $( "button" + ".showDetailsNew" ).click(function() {
      var project_id = $(this).attr('data-project');
      toggleDetailsNew(project_id);
    });
  });


  // On TAB key open detailed view
  $(function() {
    $('.inputNewNote').keydown(function(event) {
      if (event.keyCode == 9) {
        var project_id = $(this).attr('data-project');
        toggleDetailsNew(project_id);
      }
    });
  });

  // On TAB key in description update note
  $(function() {
    $('.textareaDescription').keydown(function(event) {
      if (event.keyCode == 9) {
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        var id = $(this).attr('data-id');
        showTitleInput(project_id, id, false);
        showDescriptionInput(project_id, id, false);
        sqlUpdateNote(project_id, user_id, id);
        blinkNote(project_id, id);
      }
    });
  });


  // Switch visuals for description update note
  $(function() {
    $('.textareaDescription').focus(function(event) {
      var project_id = $(this).attr('data-project');
      var id = $(this).attr('data-id');
      showDescriptionInput(project_id, id, true);
    });
  });


  // Switch note done state
  function switchNoteDoneState(project_id, id){
      $checkDone = $("#noteDoneCheckmarkP" + project_id + "-" + id);

      // cycle through states
      if ($checkDone.hasClass( "fa-circle-thin" )) {
        $checkDone.removeClass( "fa-circle-thin" );
        $checkDone.addClass( "fa-spinner fa-spin" );
        return;
      }
      if ($checkDone.hasClass( "fa-spinner fa-spin" )) {
        $checkDone.removeClass( "fa-spinner fa-spin" );
        $checkDone.addClass( "fa-check" );
        return;
      }
      if ($checkDone.hasClass( "fa-check" )) {
        $checkDone.removeClass( "fa-check" );
        $checkDone.addClass( "fa-circle-thin" );
        return;
      }
  }

  // Update note done checkmark
  function updateNoteDoneCheckmark(project_id, id){
    $noteDoneCheckmark = $("#noteDoneCheckmarkP" + project_id + "-" + id);
    $noteTitleLabel = $("#noteTitleLabelP" + project_id + "-" + id);
    $noteDescription = $("#noteTextareaDescriptionP" + project_id + "-" + id);

    if( $noteDoneCheckmark.hasClass( "fa-check" ) ){
      $noteTitleLabel.addClass( "noteDoneDesignText" );
      $noteDescription.addClass( "noteDoneDesignTextarea" );
      $noteDoneCheckmark.attr('data-id', '0');
    }
    if( $noteDoneCheckmark.hasClass( "fa-circle-thin" ) ){
      $noteTitleLabel.removeClass( "noteDoneDesignText" );
      $noteDescription.removeClass( "noteDoneDesignTextarea" );
      $noteDoneCheckmark.attr('data-id', '1');
    }
    if( $noteDoneCheckmark.hasClass( "fa-spinner fa-spin" ) ){
      $noteTitleLabel.removeClass( "noteDoneDesignText" );
      $noteDescription.removeClass( "noteDoneDesignTextarea" );
      $noteDoneCheckmark.attr('data-id', '2');
    }
  };

  //Checkmark done
  $(function() {
    $( "button" + ".checkDone" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      var id = $(this).attr('data-id');

      switchNoteDoneState(project_id, id);
      updateNoteDoneCheckmark(project_id, id);
      showTitleInput(project_id, id, false);
      showDescriptionInput(project_id, id, false);
      sqlUpdateNote(project_id, user_id, id);
      blinkNote(project_id, id);
    });
  });


  // SQL note transfer (to another project)
  function sqlTransferNote(project_id, user_id, note_id, target_project_id){
    $.ajaxSetup ({
        cache: false
    });
    $.ajax({
      type: "POST",
      url: '/kanboard/?controller=BoardNotesController&action=boardNotesTransferNote&plugin=BoardNotes'
            + '&project_cus_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&target_project_id=' + target_project_id,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
        alert(e);
      }
    });
    return false;
  }

  // SQL note update (title etc. and done)
  function sqlUpdateNote(project_id, user_id, id){
    var note_id = $('#note_idP' + project_id + "-" + id).attr('data-id');
    var is_active = $('#noteDoneCheckmarkP' + project_id + "-" + id).attr('data-id');
    var title = $('#noteTitleInputP' + project_id + "-" + id).val();
    var category = $('#catP' + project_id + "-" + id + ' option:selected').text();
    var description = $('#noteTextareaDescriptionP' + project_id + "-" + id).val().replace(/\n/g, '<br >');

    $.ajaxSetup ({
        cache: false
    });
    $.ajax({
      type: "POST",
      url: '/kanboard/?controller=BoardNotesController&action=boardNotesUpdateNote&plugin=BoardNotes'
            + '&project_cus_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&is_active=' + is_active
            + '&title=' + title
            + '&description=' + description
            + '&category=' + category,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
        alert(e);
      }
    });
    return false;
  }


  function sqlAddNote(project_id, user_id){
    var descriptionQ = $('#textareaNewNote' + project_id).val();
    if (descriptionQ) {
      var description = $('#textareaNewNote' + project_id).val().replace(/\n/g, '<br >');
    }
    if (!descriptionQ) {
      var description = "";
    }
    var is_active = "1";
    var title = $('#newNote' +  project_id).val();
    $('.inputNewNote').val("");
    var category = $('#catP' + project_id + ' option:selected').text();

    $.ajaxSetup ({
        cache: false
    });
    $.ajax({
      type: "POST",
      url: '/kanboard/?controller=BoardNotesController&action=boardNotesAddNote&plugin=BoardNotes'
            + '&project_cus_id=' + project_id
            + '&user_id=' + user_id
            + '&is_active=' + is_active
            + '&title=' + title
            + '&description=' + description
            + '&category=' + category,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
        alert(e);
      }
     });
    return false;
  }


  function sqlDeleteNote(project_id, user_id, note_id){
    $.ajaxSetup ({
        cache: false
    });
    $.ajax({
      type: "POST",
      url: '/kanboard/?controller=BoardNotesController&action=boardNotesDeleteNote&plugin=BoardNotes'
            + '&project_cus_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
        alert(e);
      }
    });
    return false;
  }


  function sqlDeleteAllDoneNotes(project_id, user_id){
    $.ajaxSetup ({
        cache: false
    });
    $.ajax({
      type: "POST",
      url: '/kanboard/?controller=BoardNotesController&action=boardNotesDeleteAllDoneNotes&plugin=BoardNotes'
            + '&project_cus_id=' + project_id
            + '&user_id=' + user_id,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
        alert(e);
      }
    });
    return false;
  }


  function sqlRefreshNotes(project_id, user_id){
    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
      cache: false
    });
    var ajax_load = '<img src="http://automobiles.honda.com/images/current-offers/small-loading.gif" alt="loading..." />';
    var loadUrl = '/kanboard/?controller=BoardNotesController&action=boardNotesRefreshProject&plugin=BoardNotes'
                + '&project_cus_id=' + project_id
                + '&user_id=' + user_id;
    setTimeout(function() {
        $("#result" + project_id).html(ajax_load).load(loadUrl);
    }, 100);
  }


  // Show input or label visuals for titles of existing notes
  function showTitleInput(project_id, id, show_input) {
    $noteTitleLabel = $("#noteTitleLabelP" + project_id + "-" + id);
    $noteTitleInput = $("#noteTitleInputP" + project_id + "-" + id);
    $noteDescription = $('#noteDescriptionP' + project_id + "-" + id);
    $textareaDescription = $('#noteTextareaDescriptionP' + project_id + "-" + id);

    if (show_input) {
      $noteTitleLabel.addClass( 'hideMe' );
      $noteTitleInput.removeClass( 'hideMe' );
      $noteTitleInput.focus();
      $noteTitleInput[0].selectionStart = 0;
      $noteTitleInput[0].selectionEnd = 0;


      // get current width of the description textarea
      var inputWidth = $textareaDescription.width();
      if ( $noteDescription.hasClass( 'hideMe' ) ) {
        $noteDescription.toggleClass( 'hideMe' );
        inputWidth = $textareaDescription.width();
        $noteDescription.toggleClass( 'hideMe' );
      }

      $noteTitleInput.width( inputWidth );
    }
    else {
      $noteTitleInput.blur();
      $noteTitleInput.addClass( 'hideMe' );
      $noteTitleLabel.removeClass( 'hideMe' );
    }
    adjustNotePlaceholders(project_id, id);
  };

  // Show input or textarea visuals for descriptions of existing notes
  function showDescriptionInput(project_id, id, show_input) {
    $textareaDescription = $('#noteTextareaDescriptionP' + project_id + "-" + id);
    if (show_input) {
      $textareaDescription.addClass( "textareaDescriptionSelected" );
      $textareaDescription.removeClass( "textareaDescription" );
      $textareaDescription[0].selectionStart = 0;
      $textareaDescription[0].selectionEnd = 0;

    }
    else {
      $textareaDescription.addClass( "textareaDescription" );
      $textareaDescription.removeClass( "textareaDescriptionSelected" );
    }
  };

  // Change from label to input on click
  $(function() {
    $( "label" + ".noteTitle" ).click(function() {
      if ($(this).attr('data-disabled')) return;
      var project_id = $(this).attr('data-project');
      var id = $(this).attr('data-id');
      showTitleInput(project_id, id, true);
    });
  });


  // POST UPDATE when enter on title
  $(function() {
    $(document).on('keypress', '.noteTitle', function(e) {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      var id = $(this).attr('data-id');
      if (event.keyCode == 13) {
        var title = $('#noteTitleInputP' + project_id + "-" + id).val(); //attr('value');
        $("#noteTitleLabelP" + project_id + "-" + id).html(title);
        showTitleInput(project_id, id, false);
        showDescriptionInput(project_id, id, false);
        sqlUpdateNote(project_id, user_id, id);
        blinkNote(project_id, id);
      }
    });
  });


  // POST ADD when enter on title
  $(function() {
    $('.inputNewNote').keypress(function(event) {
      if (event.keyCode == 13) {
	      var project_id = $(this).attr('data-project');
	      var user_id = $(this).attr('data-user');
	      $('.inputNewNote').blur();
	      sqlAddNote(project_id, user_id);
	      sqlRefreshNotes(project_id, user_id);
      }
    });
  });


  // POST ADD on save button for new notes
  $(function() {
    $( "button" + ".saveNewNote" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      $('.inputNewNote').blur();
      sqlAddNote(project_id, user_id);
      sqlRefreshNotes(project_id, user_id);
    });
  });


  // POST UPDATE on save button for existing notes
  $(function() {
    $( "button" + ".singleNoteSave" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      var id = $(this).attr('data-id');
      showTitleInput(project_id, id, false);
      showDescriptionInput(project_id, id, false);
      sqlUpdateNote(project_id, user_id, id);
      blinkNote(project_id, id);
    });
  });


  // POST delete on delete button
  $(function() {
    $( "button" + ".singleNoteDelete" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      var note_id = $(this).attr('data-id');
      sqlDeleteNote(project_id, user_id, note_id);
      sqlRefreshNotes(project_id, user_id);
    });
  });


  // POST transfer note to list
  $(function() {
    $( "button" + ".singleNoteTransfer" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      var note_id = $(this).attr('data-note');
      modalTransferNote(project_id, user_id, note_id);
    });
  });

  function modalTransferNote(project_id, user_id, note_id) {
    $("#dialogTransferP" + project_id).removeClass( 'hideMe' );
    $("#dialogTransferP" + project_id).dialog({
      buttons: {
        Move: function() {
          var target_project_id = $('#listNoteProjectP' + project_id + ' option:selected').val();
          sqlTransferNote(project_id, user_id, note_id, target_project_id);
          $( this ).dialog( "close" );
          sqlRefreshNotes(project_id, user_id);
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }
    });
    return false;
  };

  // POST export note as task
  $(function() {
    $( "button" + ".singleNoteToTask" ).click(function() {
      var id = $(this).attr('data-id');
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      var note_id = $(this).attr('data-note');
      var is_active = $('#noteDoneCheckmarkP' + project_id + "-" + id).attr('data-id');
      var title = encodeURIComponent($('#noteTitleLabelP' + project_id + "-" + id).text());
      var description = encodeURIComponent($('#noteTextareaDescriptionP' + project_id + "-" + id).val().replace(/\n/g, '<br >'));
      var category_val = $('#catP' + project_id + "-" + id + ' option:selected').val();
      modalNoteToTask(project_id, user_id, note_id, is_active, title, description, category_val);
    });
  });

  function modalNoteToTask(project_id, user_id, note_id, is_active, title, description, category_val) {
    $.ajaxSetup ({
      cache: false
    });
    $('#dialogToTaskParams').removeClass( 'hideMe' );
    $('#deadloading').addClass( 'hideMe' );
    $('#listCatToTaskP' + project_id).val(category_val).change();
    $("#dialogToTaskP" + project_id).removeClass( 'hideMe' );
    $("#dialogToTaskP" + project_id).dialog({
      title: 'Create task from note?',
      buttons: {
        Create: function() {
          var categoryToTask = $('#listCatToTaskP' + project_id + ' option:selected').val();
          var columnToTask = $('#listColToTaskP' + project_id + ' option:selected').val();
          var swimlaneToTask = $('#listSwimToTaskP' + project_id + ' option:selected').val();
          var removeNote = $('#removeNoteP' + project_id).is(":checked");

          var ajax_load = '<img src="http://automobiles.honda.com/images/current-offers/small-loading.gif" alt="loading..." />';
          var loadUrl = '/kanboard/?controller=BoardNotesController&action=boardNotesToTask&plugin=BoardNotes'
                        + '&project_cus_id=' + project_id
                        + '&user_id=' + user_id
                        + '&task_title=' + title
                        + '&task_description=' + description
                        + '&category_id=' + categoryToTask
                        + '&column_id=' + columnToTask
                        + '&swimlane_id=' + swimlaneToTask;

          $("#dialogToTaskP" + project_id).dialog({
            title: 'Result ...',
            buttons: {
              Close: function() { $( this ).dialog( "close" ); }
            }
          });
          $('#dialogToTaskParams').addClass( 'hideMe' );
          $('#deadloading').removeClass( 'hideMe' );
          $('#deadloading').html(ajax_load).load(loadUrl);
          if (removeNote) {
            sqlDeleteNote(project_id, user_id, note_id);
            sqlRefreshNotes(project_id, user_id);
          }
        },
        Cancel: function() { $( this ).dialog( "close" ); }
      }
    });
    return false;
  };


  // POST delete all done
  $(function() {
    $( "#settingsDeleteAllDone" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      modalDeleteAllDoneNotes(project_id, user_id);
    });
  });


  function modalDeleteAllDoneNotes(project_id, user_id) {
 	$( "#dialogDeleteAllDone" ).removeClass( 'hideMe' );
    $( "#dialogDeleteAllDone" ).dialog({
      resizable: false,
      height: "auto",
      modal: true,
      buttons: {
        "Delete all done notes!": function() {
          sqlDeleteAllDoneNotes(project_id, user_id);
          $( this ).dialog( "close" );
          sqlRefreshNotes(project_id, user_id);
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }
    });
  };


  // POST analytics
  $(function() {
    $( "#settingsAnalytics" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      modalAnalytics(project_id, user_id);
    });
  });


  function modalAnalytics(project_id, user_id) {
    $.ajaxSetup ({
        cache: false
    });
    var ajax_load = '<img src="http://automobiles.honda.com/images/current-offers/small-loading.gif" alt="loading..." />';
    var loadUrl = '/kanboard/?controller=BoardNotesController&action=boardNotesAnalytics&plugin=BoardNotes'
                + '&project_cus_id=' + project_id
                + '&user_id=' + user_id;
    $('#dialogAnalyticsInside').html(ajax_load).load(loadUrl);

    $( "#dialogAnalytics" ).removeClass( 'hideMe' );
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
    $( "#settingsReport" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      modalReport(project_id, user_id);
    });
  });


  $(function() {
    $( "#settingsCollapseAll" ).click(function() {
        $('.showDetails').each(function() {
            if ($(this).find('i').hasClass( "fa-angle-double-up" ))
            {
                var project_id = $(this).attr('data-project');
                var id = $(this).attr('data-id');
                toggleDetails(project_id, id);
            }
        });
    });
  });

  $(function() {
    $( "#settingsExpandAll" ).click(function() {
        $('.showDetails').each(function() {
            if ($(this).find('i').hasClass( "fa-angle-double-down" ))
            {
                var project_id = $(this).attr('data-project');
                var id = $(this).attr('data-id');
                toggleDetails(project_id, id);
            }
        });
    });
  });

  $(function() {
    $( "#settingsCategoryColors" ).click(function() {
        toggleCategoryColors();
    });
  });

  // Toggle category colors
  function toggleCategoryColors() {
    var showCategoryColors = sessionStorage.getItem('boardnotesShowCategoryColors');
    if (!showCategoryColors || showCategoryColors == "hide") {
        $( ".trReport" ).addClass( 'task-board' );
        $( ".liNote" ).addClass( 'task-board' );
        // avoid the ugly empty category label boxes
        $('.catLabel').each(function() {
            if ($(this).html())
                $(this).addClass( 'task-board-category' );
        });
        sessionStorage.setItem('boardnotesShowCategoryColors', "show");
    } else {
        $( ".trReport" ).removeClass( 'task-board' );
        $( ".liNote" ).removeClass( 'task-board' );
        $( ".catLabel" ).removeClass( 'task-board-category' );
        sessionStorage.setItem('boardnotesShowCategoryColors', "hide");
    }
  }

  // Update category colors
  function updateCategoryColors(project_id, id, old_category, new_category) {
    var note_id = $('#note_idP' + project_id + "-" + id).attr('data-id');
    $old_color = $( "#category-" + old_category ).attr('data-color');
    $new_color = $( "#category-" + new_category ).attr('data-color');

    $( "#trReportNr" + id ).removeClass( 'color-' + $old_color );
    $( "#item-" + note_id ).removeClass( 'color-' + $old_color );
    $("#noteCatLabelP" + project_id + "-" + id).removeClass( 'color-' + $old_color );

    $( "#trReportNr" + id ).addClass( 'color-' + $new_color );
    $( "#item-" + note_id ).addClass( 'color-' + $new_color );
    $("#noteCatLabelP" + project_id + "-" + id).addClass( 'color-' + $new_color);
  }

  // Selector category
  $(function() {
    $( ".catSelector" ).selectmenu({
      change: function( event, data ) {
        var id = $(this).attr('data-id');
        if (id > 0) { // exclude handling the category drop down for new note
            var project_id = $(this).attr('data-project');
            var user_id = $(this).attr('data-user');
            var old_category = $("#noteCatLabelP" + project_id + "-" + id).html();
            var new_category = $('#catP' + project_id + "-" + id + ' option:selected').text();
            $("#noteCatLabelP" + project_id + "-" + id).html(new_category);

            updateCategoryColors(project_id, id, old_category, new_category);
            // avoid the ugly empty category label boxes
            var showCategoryColors = sessionStorage.getItem('boardnotesShowCategoryColors');
            if (new_category && showCategoryColors == "show") {
                $("#noteCatLabelP" + project_id + "-" + id).addClass( 'task-board-category' );
            }
            if (!new_category || !showCategoryColors || showCategoryColors == "hide") {
                $("#noteCatLabelP" + project_id + "-" + id).removeClass( 'task-board-category' );
            }

            showTitleInput(project_id, id, false);
            showDescriptionInput(project_id, id, false);
            sqlUpdateNote(project_id, user_id, id);
            blinkNote(project_id, id);

        }
      }
    });
  });


  // Hide note in reporting
  $(function() {
    $( "button" + "#singleReportHide" ).click(function() {
      var id = $(this).attr('data-id');
      $( "#trReportId" + id ).addClass( "hideMe" );
    });
  });

  function modalReport(project_id, user_id) {
    $.ajaxSetup ({
        cache: false
    });
    $( "#dialogReportP" + project_id ).removeClass( 'hideMe' );
    $( "#dialogReportP" + project_id ).dialog({
      buttons: {
        Ok: function() {
          var category = $('#reportCatP' + project_id + ' option:selected').text();
          var ajax_load = '<img src="http://automobiles.honda.com/images/current-offers/small-loading.gif" alt="loading..." />';
          var loadUrl = "/kanboard/?controller=BoardNotesController&action=boardNotesReport&plugin=BoardNotes"
                        + "&project_cus_id=" + project_id
                        + "&user_id=" + user_id
                        + "&category=" + category;
          $("#result" + project_id).html(ajax_load).load(loadUrl);
          $( this ).dialog( "close" );
        }
      }
    });
    return true;
  };


  // SQL update positions
  function sqlNotesUpdatePosition(project_id, user_id, order, nrNotes){
    $.ajaxSetup ({
        cache: false
    });
    $.ajax({
      type: "POST",
      url: '/kanboard/?controller=BoardNotesController&action=boardNotesUpdatePosition&plugin=BoardNotes'
        + '&project_cus_id=' + project_id
        + '&user_id=' + user_id
        + '&order=' + order
        + '&nrNotes=' + nrNotes,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
        alert(e);
      }
    });
    return false;
  }
