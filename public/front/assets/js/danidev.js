function toast(msg, title, type, timer){
    var opts = {
        title: title,
        html: msg,
        type: type,
        confirmButtonClass: "btn btn-confirm mt-2"
    };
    if(timer !== undefined){
        opts.timer = timer;
        opts.showConfirmButton = false;
    }
    swal.fire(opts);
}


function logout(e){
    e.preventDefault();
    Swal.fire({
        title: "Are you sure?",
        text: "By this action you will be logged out are you sure you want to continue!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, log me out!"
    }).then(function (t) {
        if (t.value){
            document.getElementById('logout-form').submit();
        }
    })
}

function getAjaxRequests(url, params, method, loader=true,callback) {
    if(loader){
        page_loader('show');
    }

    var params = (!params && params != '') ? {} : params;
    var method = (!method && method != '') ? "POST" : method;

    $.ajax({
        url: url,
        method: method,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: params,
        dataType: "json",
        complete: function () {
            page_loader('hide');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            page_loader('hide');
            ajaxErrorHandling(jqXHR, errorThrown);
        },
        success: function (data) {
            var timer = 3000;
            if (data['reload'] !== undefined) {
                toast(data['success'], "Success!", 'success', timer);
                setTimeout(function () {
                    window.location.reload(true);
                }, 600);
                return false;
            }
            if (data['redirect'] !== undefined) {
                toast(data['success'], "Success!", 'success', timer);
                setTimeout(function () {
                    window.location = data['redirect'];
                }, 600);
                return false;
            }
            if (data['error'] !== undefined) {
                toast(data['error'], "Error!", 'error');
                return false;
            }

            if (data['errors'] !== undefined) {
                multiple_errors_ajax_handling(data['errors']);
            }
            callback(data);
        }
    });
}


function my_ajax(url,param,method,callback,loader=true) {
    if(loader){
        page_loader('show');
        btnloader('show');
    }
    $.ajax({
        url: url,
        method: method,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: param,
        contentType: false,
            processData: false,
        dataType: "json",
        complete: function () {
            page_loader('hide');
            btnloader('hide');

        },
        error: function (jqXHR, textStatus, errorThrown) {
            page_loader('hide');
            btnloader('hide');
            ajaxErrorHandling(jqXHR, errorThrown);
        },
        success: function (data) {
            var timer = 3000;

            if (data['reload'] !== undefined) {
                toast(data['success'], "Success!", 'success', timer);
                setTimeout(function () {
                    window.location.reload(true);
                }, 3000);
                return false;
            }

            if (data['redirect'] !== undefined) {
                toast(data['success'], "Success!", 'success', timer);
                setTimeout(function () {
                    window.location = data['redirect'];
                }, 3000);
                return false;
            }

            if (data['error'] !== undefined) {
                toast(data['error'], "Error!", 'error');
                return false;
            }

            if (data['errors'] !== undefined) {
                multiple_errors_ajax_handling(data['errors']);
            }

            if(data['next'] !== undefined){
                $('#formend').trigger('click');
                return false;
            }

            if (data['success'] !== undefined) {
                toast(data['success'], "Success!", 'success', timer);
            }

            callback(data);
        }
    });
 }

function multiple_errors_ajax_handling(errors){
    $_html = "";
    for (error in errors) {
        $_html += "<p class='m-1 text-danger'><strong>" + errors[error][0] + "</strong></p>";
    }
    toast($_html, "Error!", 'error');
    return false;
}

function page_loader(status) {
    if (status == 'hide') {
        $('.preloader').hide();
        $('#status').hide();
        return;
    }

    $('.preloader').show();
    $('#status').show();
    return;
}

function ajaxRequest(_self) {
    var href = $(_self).data('url');
    var nopopup = $(_self).hasClass('nopopup');
    var btn_txt = $(_self).data("btnText");
    var data_msg = $(_self).data("msg");
    var data_titlemsg = $(_self).data("titlemsg");
    if (!nopopup) {
        Swal.fire({
            title: (data_titlemsg && data_titlemsg != '') ? data_titlemsg : "Are you sure?",
            text: (data_msg && data_msg != '') ? data_msg : "You won't be able to revert this!",
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: (btn_txt && btn_txt != '') ? btn_txt : "Yes, confirm it!"
        }).then(function (t) {
            if (t.value){
                run_ajax(href, _self);
            }
        });
    }else{
        run_ajax(href, _self);
    }
}


function run_ajax(href, ele){
    page_loader('show');
    $.ajax({
        url: href,
        dataType: "json",
        complete: function () {
            page_loader('hide');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            ajaxErrorHandling(jqXHR, errorThrown);
        },
        success: function (data) {
            if (data['error'] !== undefined) {
                toast(data['error'], "Error!", 'error');
            } else if (data['success'] !== undefined) {
                toast(data['success'], "Success!", 'success', 3000);
            } else if (data['info'] !== undefined) {
                toast(data['info'], "Info", 'info');
            }

            if (data['errors'] !== undefined) {
                multiple_errors_ajax_handling(data['errors']);
            }

            if (data['reload'] !== undefined) {
                setTimeout(function () {
                    window.location.reload(true);
                }, 400);
            }

            if (data['redirect'] !== undefined) {
                setTimeout(function () {
                    window.location = data['redirect'];
                }, 400);
            }

            if (data['remove_tr'] !== undefined) {
                $(ele).closest('tr').remove();
            }

            if (data['remove_row'] !== undefined) {
                $(ele).closest(data['remove_row']).remove();
            }
        }
    });
}

function ajaxErrorHandling(data, msg){
    if (data.hasOwnProperty("responseJSON")) {
        var resp = data.responseJSON;

        if (resp.message == 'CSRF token mismatch.') {
            toast("Page has been expired and will reload in 2 seconds", "Page Expired!", "error");
            setTimeout(function () {
                window.location.reload();
            }, 2000);
            return;
        }

        if (resp.error) {
            var msg = (resp.error == '') ? 'Something went wrong!' : resp.error;
            toast(msg, "Error!", "error");
            return;
        }

        if (resp.message != 'The given data was invalid.') {
            toast(resp.message, "Error!", "error");
            return;
        }

        multiple_errors_ajax_handling(resp.errors);
    } else {
        toast(msg + "!", "Error!", 'error');
    }
    return;
}

function multiple_errors_ajax_handling(errors){
    $_html = "";
    for (error in errors) {
        $_html += "<p class='m-1 text-danger'><strong>" + errors[error][0] + "</strong></p>";
    }
    toast($_html, "Error!", 'error');
    return false;
}


function btnloader(status){
    var submitBtn = $('.form-submit-btn');

    if (status == 'hide') {
        submitBtn.prop('disabled',false);
        submitBtn.html('Save');
        return;
    }

    submitBtn.prop('disabled',true);
    submitBtn.html('<span class="d-flex align-items-center"><div class="spinner-border text-dark" role="status"><span class="sr-only">Loading...</span> </div> Saving...</span>');
    return
}
