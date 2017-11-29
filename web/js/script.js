$(document).ready(function(){
    gridLoad();


    $(".pagination").on('click', '.page-link', function() {
        let page = $(this)[0].innerText;
        gridLoad(page);
    });


    $("form").on("submit", function (e) {
        e.preventDefault();

        $('#preloader').show();

        removeFeedbacks();

        $.post('/api/grid/create', $("form").serializeArray())
            .done(function(data) {
                if (data.errors) {
                    showErrors(data.errors);
                    return;
                }
                clearForm();
                gridLoad();
            })
            .fail(function() {
            })
            .always(function() {
                $('#preloader').hide();
            });
    });

});

var gridLoad = function(page) {

    $('#preloader').show();

    let url = "/api/grid";
    if (typeof page != 'undefined') {
        url += '/' + page ;
    }

    $.get(url)
        .done(function (data) {
            fillGrid(data.messages);
            fillPagination(data.page, data.pages);
        })
        .fail(function () {
            alert('FAIL!');
        })
        .always(function() {
            $('#preloader').hide();
        });
}


var fillGrid = function (data) {
    let content = '';
    data.forEach(function(item, index) {
        content += "" +
            "       <div class='row mesasge-item'>" +
            "            <div class=\"col-4\">\n" +
            "                <div class=\"col-12\">\n" +
                                item.name +
            "                </div>\n" +
            "                <div class=\"col-12\">\n" +
                                item.phone +
            "                </div>\n" +
            "                <div class=\"col-12\">\n" +
                                 item.email +
            "                </div>\n" +
            "            </div>\n" +
            "            <div class=\"col-8\">\n" +
            "                <div class=\"col-12\">\n" +
                                item.text +
            "                </div>\n" +
            "            </div>" +
            "       </div>";
    });

    $(".grid-data").html(content);
}


var fillPagination = function(page, pages) {

    let element = $(".pagination");
    element.empty();

    for(let i = 1; i <= pages; i++) {
        let newLi = "<li class='page-item'><a class='page-link' href='#'>" + i + "</a></li>";
        $( element ).append( newLi );
    }

}


var showErrors = function(errors) {
    if (errors.length == 0) {
        return;
    }

    for (let field in errors) {
        errors[field].forEach(function (message) {
            $( "." + field ).append("<div class='form-control-feedback alert alert-danger'>" + message + "</div>");
        });
    }
}


var removeFeedbacks = function() {
    $( ".form-control-feedback" ).remove();
}


var clearForm = function () {
    $(".form-control").val("");
}
