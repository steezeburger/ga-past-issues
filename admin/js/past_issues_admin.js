jQuery(document).ready(function ($) {
    
  $(".column").sortable({
    connectWith: ".column",
    handle: ".portlet-header",
    cancel: ".portlet-toggle",
    placeholder: "portlet-placeholder ui-corner-all",
    // Changes input name attribute to contain proper location in list
    stop: function(event, ui) {
      console.log($('input[name="gazette1_hidden"]').attr('val'));
      console.log(ui.item.index());
      $('.portlet-content input').each(function(idx, item) {
        // Update input names according to position in sortable list
        // Update input names according to position in sortable list
        var sith = $(this).attr('name');
        // Replaces the X in video[vidX][$key] with proper video number
        var newSith = sith.substr(0, 9) + Math.floor(idx/3) + sith.substr(10);
        $(this).attr('name', newSith);
      });      
    }
  });
  
  $(".portlet")
    .addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
    .find(".portlet-header")
    .addClass("ui-widget-header ui-corner-all")
    .prepend("<span class='ui-icon ui-icon-plusthick portlet-toggle'></span><span class='ui-icon ui-icon-trash portlet-delete'></span>");
  
  $(".portlet-toggle").click(function () {
    var icon = $(this);
    icon.toggleClass("ui-icon-minusthick ui-icon-plusthick");
    icon.closest(".portlet").find(".portlet-content").toggle();
  });
  
  $(".portlet-delete").click(function () {
    var trash = $(this);
    // Confirm before deleting
    var confirmation = confirm("Are you sure you want to delete this entry?");
    if (confirmation == true) {
      trash.closest(".portlet").remove();
    }
  });
  
  $("#create-entry").click(function() {
    var portletCount = String($('.portlet').length);
    // Create new <div class="portlet">
    $('form[name="gazette1_form"]').prepend(
      $('<div>').addClass('portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'));
    // Select last portlet and add necessary HTML
    
    
    $('.portlet').first().html(
        '<div class="portlet-header ui-sortable-handle ui-widget-header ui-corner-all">' +
        '<span class="ui-icon ui-icon-plusthick portlet-toggle"></span>New Entry</div>' +
         '<div class="portlet-content" style="display: block;">' +
          '<p>Date:' +                         
            '<input type="text" name="video[vid'+portletCount+'][title]" size="50" placeholder="ex: 1/2/2013">' +
          '</p>' +
          '<p>Cover URL:' +
            '<input type="text" name="video[vid'+portletCount+'][iconURL]" size="50" placeholder="ex: http://okgazette.com/wp-content/uploads/picture.jpg">' +
          '</p>' +
          '<p>Issuu URL:' +
            '<input type="text" name="video[vid'+portletCount+'][youTubeURL]" size="50" placeholder="ex: http://issuu.com/okgazette/docs/okgazette_4-8-15lr?e=11698495/12232508">' +
          '</p>' +
          '</div>'
                  );
  });
  
});