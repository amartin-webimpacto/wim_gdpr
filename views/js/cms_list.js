$(document).ready(function () {
    $("#table-cms").children("tbody").find(".btn-group-action").find(".delete").each(function () {
        $(this).attr("elonclick", $(this).attr("onclick"));
        $(this).attr("onclick", "preDelete(this);event.stopPropagation(); event.preventDefault();");
    })
})

function preDelete(element) {
    var a = $(element);
    var tr = $(a).parent().parent().parent().parent().parent().parent();
    var id = $(tr).children("td:eq( 1 )").text().trim();

    $.ajax({
        type: 'POST',
        async: false,
        url: '../modules/wim_gdpr/ajax.php',
        data: {
            action: "canDeleteCms",
            cms: id
        },
        dataType: 'json',
        success: function (json) {
            // Obtener el mensaje de confirmación
            var mensaje = a.attr("elonclick");
            var start = mensaje.indexOf("'") + 1;
            mensaje = mensaje.substring(start);
            var finish = mensaje.indexOf("'");
            mensaje = mensaje.substring(0, finish);

            if (confirm(mensaje)) {
                if (json["result"] == "true") {
                    // Se elimina (no está protegido por wimgdpr)
                    window.location.href = a.attr("href");
                } else {
                    // no se pude eliminar, mostrar errror
                    showError("No se puede eliminar el CMS porque stá protegido por WebImpacto GDPR");
                }
            } else {
                event.stopPropagation();
                event.preventDefault();
            }
        },
        error: function () {
            // error en el servidor
            showError("Ha ocurrido un error inesperado en el servidor.");
        }
    });
}

function showError(mensaje){
    var container = $("<div>").addClass("bootstrap");
    var divMsg = $("<div>").addClass("alert alert-danger").text(mensaje);
    var buttonClose = $("<button>").attr({
        type: "button",
        "data-dismiss": "alert"
    }).addClass("close").text("×")
    divMsg.append(buttonClose);
    container.append(divMsg);

    $(container).insertAfter("#content>.bootstrap:eq(0)")
}
