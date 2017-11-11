
$(document).ready(function () {
    $('.highlight_from').datepicker({
        format: 'dd-mm-yyyy',
        startDate: "today",
        todayBtn: "linked",
        clearBtn: true,
        language: "es",
        daysOfWeekHighlighted: "0,6",
        todayHighlight: true,
        autoclose: true
    }).on('changeDate', function (selected) {
        if(selected.date){
            var startDate = new Date(selected.date.valueOf());
            $('.highlight_to').datepicker('setStartDate', startDate);
        }
    }).on('clearDate', function () {
        var startDate = new Date();
        $('.highlight_to').datepicker('setStartDate', startDate);
    });
    $('.highlight_to').datepicker({
        format: 'dd-mm-yyyy',
        startDate: "today",
        todayBtn: "linked",
        clearBtn: true,
        language: "es",
        daysOfWeekHighlighted: "0,6",
        todayHighlight: true,
        autoclose: true
    }).on('changeDate', function (selected) {
        if(selected.date){
            var endDate = new Date(selected.date.valueOf());
            $('.highlight_from').datepicker('setEndDate', endDate);
        }
    }).on('clearDate', function () {
        $('.highlight_from').datepicker('setEndDate', null);
    });
});