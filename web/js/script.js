$(document).ready(function(){
    console.log('hello');
    ajaxRequest();


    $(".pagination").on('click', '.page-link', function() {
        let page = $(this)[0].innerText;
        ajaxRequest(page);
    });


    $("form").on("submit", function (e) {
        e.preventDefault();

        $.post('/api/grid/create', $("form").serializeArray())
            .done(function() {
                console.log( "success" );
            })
            .fail(function() {
                console.log( "error" );
            })
    });

});

var ajaxRequest = function(page) {
    console.log(page);
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
        });
}

var fillGrid = function (data) {
    let content = '';
    data.forEach(function(item, index) {
        content +=
            "        <div class='row'>" +
            "            <div class='col-sm-4'>" +
            item.name +
            "            </div>" +
            "            <div class='col-sm-4'>" +
            item.phone +
            "            </div>" +
            "            <div class='col-sm-4'>" +
            item.email +
            "            </div>" +
            "            <div class='col-sm-12'>" +
            item.text +
            "            </div>" +
            "        </div>"
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