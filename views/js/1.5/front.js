$(document).ready(function () {
    createModal();
});

function createModal() {
    // Adaptar estilos para usar tingle en lugar de intentar bootstrap(que no existe en prestashop1.5)
    $("#wimgdpr_modal").find("button").addClass("tingle-btn");

    // instanciate new modal
    var modal = new tingle.modal({
        footer: true,
        stickyFooter: false,
        closeMethods: [],
        closeLabel: "Close"
    });

    var content = "";
    ($("#wimgdpr_modal").find(".modal-body").each(function(){
        content+= $(this).html()+"<hr></br>";
    }));
    modal.setContent(content);

    modal.addFooterBtn('Aceptar', 'tingle-btn tingle-btn--primary', function () {
        acceptCms();
        modal.close();
    });

    modal.open();
}

function modalError() {
    var modal = new tingle.modal({
        footer: false,
        stickyFooter: false,
        closeMethods: [],
        closeLabel: "Close"
    });

    modal.setContent("<h3>Ha ocurrido un error. Recargue la página y vuelva a intentarlo.</h3>");

    modal.open();
}

function acceptCms() {
    var id_gdpr_cms_version_list = [];

    $(".hidden_cms").each(function () {
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
            if (json["result"] == "error") {
                modalError();
            }
        },
        error: function () {
            modalError();
        }
    });
}

/**
 * Mostrar el popup con el texto del CMS seleccionado.
 * @param texto
 */
function showModalCms(id_gdpr_cms_version) {
    $.ajax({
        type: 'POST',
        url: baseDir + 'modules/wim_gdpr/ajax.php',
        data: {
            action: "getCms",
            id_gdpr_cms_version: id_gdpr_cms_version
        },
        dataType: 'html',
        success: function (html) {
            var modal = new tingle.modal({
                footer: true,
                stickyFooter: false,
                closeMethods: ['overlay', 'button', 'escape'],
                closeLabel: "Close",
                cssClass: ['tingle-2']
            });

            modal.setContent(html);

            modal.addFooterBtn('Cerrar', 'tingle-btn', function () {
                modal.close();
            });

            modal.open();
        }
    });
}

