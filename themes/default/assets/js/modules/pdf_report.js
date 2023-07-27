$(function() {
    $(document).delegate('.btnExportPDF', 'click', function() {
        var url = urlbase + "pdf_report/generate_report";
        var data = $('.checkbox:checked').map(function() { return $(this).attr("id"); }).get();
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: url
        });
    });

});



