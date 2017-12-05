$(document).ready(function(){
    gridLoad();

    $(".pagination").on('click', '.page-link', function() {
        let page = $(this)[0].innerText;
        gridLoad(page);
    });

    $('.submit-form').click(function (e) {
        e.preventDefault();

        $('#preloader').show();

        removeFeedbacks();

        $.post('/api/message', $("form").serializeArray())
            .done(function(data) {
                if (data.errors) {
                    showErrors(data.errors);
                } else {
                    clearForm();
                    closeModal();
                    gridLoad();
                }
            })
            .fail(function(data) {
                console.log(data);
                clearForm();
            })
            .always(function() {
                $('#preloader').hide();
            });
    });

    $('.modal-close').click(function () {
        clearForm();
    })

    $("#phone").mask( '(000) 000-00-00');


    $('.grid-data').on('click', '.remove-grid-item', function(e) {
        e.preventDefault();

        $('#preloader').show();

        let id = $(this).parent().parent().find('.grid-id-item')[0].innerText;

        $.ajax({
                url: '/api/message/' + id,
                method: 'DELETE'
            })
            .done( function (data) {
                console.log('success');
                console.log(data);
                gridLoad();
            })
            .fail( function (data) {
                $('#preloader').hide();
                console.log('fail');
                console.log(data);
            });

    });

    $('.grid-data').on('click', '.edit-grid-item', function(e) {
        e.preventDefault();

        $('#preloader').show();

        let id = $(this).parent().parent().find('.grid-id-item')[0].innerText;

        $.ajax({
                url: '/api/message/' + id,
                method: 'GET'
            })
            .done( function (data) {
                $('#preloader').hide();
                fillModal(data.message);
                openModal();
            })
            .fail( function (data) {
                $('#preloader').hide();
                console.log('fail');
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
        .fail(function (data) {
            console.log(data);
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
            "            <div class='col-12 col-sm-4'>" +
            "                <div class='col-12 grid-control-item'>\n" +
            "                   <button class='btn btn-link remove-grid-item'><i class=\"fa fa-trash\"></i></button>" +
            "                   <button class='btn btn-link edit-grid-item'><i class=\"fa fa-pencil\"></i></button>" +
            "                </div>" +
            "                <div class='grid-id-item'>" +
                                item.id +
            "                </div>" +
            "                <div class='col-12 grid-name-item'>" +
                                item.name +
            "                </div>" +
            "                <div class='col-12 grid-phone-item'>" +
                                item.phone +
            "                </div>" +
            "                <div class='col-12 grid-email-item'>" +
                                item.email +
            "                </div>" +
            "            </div>" +
            "            <div class='col-12 col-sm-7'>" +
            "                <div class='col-12grid-text-item'>" +
                                item.text +
            "                </div>" +
            "            </div>" +
            "       </div>";
    });

    $(".grid-data").html(content);
    $(".grid-phone-item").mask( '(000) 000-00-00');
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
        console.log(errors[field]);

        errors[field].forEach(function (message) {
            $( "." + field ).append("<div class='form-control-feedback alert alert-danger'>" + message + "</div>");
        });
    }
}


var removeFeedbacks = function() {
    $( ".form-control-feedback" ).remove();
}


var clearForm = function () {
    $("form").trigger('reset');
    $("[name*='app_bundle_message_type[id]']").val("");
}

var closeModal = function () {
    clearForm();
    $('.modal-close').click();
}

var openModal = function () {
    $('.button-open-modal').click();
}

var fillModal = function (data) {
    for (let value in data) {
        let elementName = 'app_bundle_message_type[' + value + ']';
        let element = ($("[name*='" + elementName + "']"));

        if (element.length > 0){
            element.val(data[value]);
        }
    }
}
