jQuery(document).ready(function(){
    var button = jQuery('.page-title-action').first(),
        importButton = button.clone();
    importButton.text('Import from CSV').attr('href', aifc.link);
    button.after(importButton);
});