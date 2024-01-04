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
