jQuery(document).ready(function ($) {
    var form =  $('#av-atum-import-csv');
    form.submit(function (e) {
        e.preventDefault();
       var file_data = $('#imports_csv').prop('files')[0];
       var form_data = new FormData();
        form_data.append('imports_csv', file_data);
        form_data.append('action', 'upload_file_handler');
        $.ajax({
            url: ajaxurl,
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
        });
        alert('Finished');
    });
});