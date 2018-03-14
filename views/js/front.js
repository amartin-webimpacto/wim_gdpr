$(document).ready(function () {
    createModal();
});

function createModal() {
    $('#wimgdpr_modal').modal({backdrop: 'static', keyboard: false});
}

function acceptCms() {
    var  id_gdpr_cms_version_list = [];

    $(".hidden_cms").each(function(){
        id_gdpr_cms_version_list.push($(this).val());
    });

    $.ajax({
        type: 'POST',
        url: baseDir + 'modules/wim_gdpr/ajax.php',
        data: {
            action: "acceptCms",
            id_gdpr_cms_version: id_gdpr_cms_version_list
        },
        dataType: 'json',
        success: function (json) {
            if(json["result"] == "ok"){
                $('#wimgdpr_modal').modal('hide');
            }else if(json["result"] == "error"){
                $('#wimgdpr_modal').find(".modal-footer").css("display", "none");
                $('#wimgdpr_modal').find(".modal-body").text("Ha ocurrido un error. Recargue la página y vuelva a intentarlo.")
            }else{
                console.log("¿?");
            }
        },
        error: function () {
            $('#wimgdpr_modal').find(".modal-footer").css("display", "none");
            $('#wimgdpr_modal').find(".modal-body").not(':first').remove();
            $('#wimgdpr_modal').find(".modal-body").text("Ha ocurrido un error. Recargue la página y vuelva a intentarlo.")
        }
    });
}

/**
 * Mostrar el popup con el texto del CMS seleccionado.
 * @param texto
 */
function showModalCms(id_gdpr_cms_version){
    $.ajax({
        type: 'POST',
        url: baseDir + 'modules/wim_gdpr/ajax.php',
        data: {
            action: "getCms",
            id_gdpr_cms_version: id_gdpr_cms_version
        },
        dataType: 'html',
        success: function (html) {
            $("#wimgdpr_cms_modal").find(".modal-body").html(html);
            $('#wimgdpr_cms_modal').modal();
        }
    });
}

