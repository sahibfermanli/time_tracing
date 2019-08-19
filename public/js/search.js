var show_date_area = false;
var short_type = '1';
var row_id = 0;

$(document).ready(function(){
    var url = window.location.href;
    var url_arr = url.split('search');
    var where_url = 'search' + url_arr[1];

    if (url_arr.length > 1) {
        $('.pagination').each(function(){
            $(this).find('a').each(function(){
                var current = $(this);
                var old_url = current.attr('href');
                var new_url = old_url + '&' + where_url;
                current.prop('href', new_url);
            });
        });
    }

    var short_arr = url.split('shortType');
    if (short_arr.length > 1) {
        short_type = short_arr[1].substr(1, 1);
    }

    var short_arr_for_link = url.split('shortBy');
    var short_url = 'shortBy' + short_arr_for_link[1];

    if (short_arr_for_link.length > 1) {
        $('.pagination').each(function(){
            $(this).find('a').each(function(){
                var current = $(this);
                var old_url_for_short = current.attr('href');
                var new_url_for_short = old_url_for_short + '&' + short_url;
                current.prop('href', new_url_for_short);
            });
        });
    }
});

function get_current_date() {
    var currentDate;
    var fullDate = new Date();
    var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
    currentDate = fullDate.getFullYear() + "-" + twoDigitMonth + "-" + fullDate.getDate();

    return currentDate;
}

//show date area for search
function date_area() {
    if (show_date_area) {
        show_date_area = false;
        $('#search-date-area').css('display', 'none');
    } else {
        show_date_area = true;
        $('#search-date-area').css('display', 'block');
    }
}

function today_for_date_area() {
    show_date_area = true;
    $('#search-date-area').css('display', 'block');
    $("#date_search").prop('checked', true);

    var currentDate = get_current_date();

    $(".start_date_search").val(currentDate);
    $(".end_date_search").val(currentDate);
}

//short by
function sort_by(column, first_search_column="search") {
    var url_for_short = window.location.href;
    var url_arr_for_short = url_for_short.split(first_search_column);
    var link_for_short = '';

    if (url_arr_for_short.length > 1) {
        var new_url_for_short = url_arr_for_short[1].split('shortBy');
        if (new_url_for_short.length > 1) {
            link_for_short = '?' + first_search_column + new_url_for_short[0];
        } else {
            link_for_short = '?' + first_search_column + new_url_for_short[0] + '&';
        }

    } else {
        link_for_short = '?';
    }

    var shortType = '';
    if (short_type == 1) {
        shortType = '2';
    } else {
        shortType = '1';
    }
    link_for_short += 'shortBy=' + column + '&shortType=' + shortType;

    location.href = link_for_short;
}

function search_data() {
    var link = '?search=1';

    $('[id=search_values]').each(function() {
        var column = $(this).attr("column_name");
        var value = $(this).val();

        link += '&' + column + '=' + value;
    });

    location.href = link;
}