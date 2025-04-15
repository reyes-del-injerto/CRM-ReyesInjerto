// export_reporte_leads.js
document.addEventListener("DOMContentLoaded", function () {
  const btnExport = document.getElementById("export_reporte_leads");
  if (!btnExport) return;

  btnExport.addEventListener("click", function () {
    let params = "";
    // Se recopilan los parámetros de filtros que usa load_leads.php:
    params += $("#seller_filter").serialize() + "&";
    params += $("#clinic_filter").serialize() + "&";
    params += $("#stages_filter").serialize() + "&";
    params += $("#qualis_filter").serialize() + "&";
    params += $("#semaforo_filter").serialize() + "&";

    // Si se usó un botón rápido de fecha, se envía chosen_time; de lo contrario, se envía date_range.
    if (typeof chosen_time !== "undefined" && chosen_time !== null && chosen_time !== "") {
      params += "chosen_time=" + encodeURIComponent(chosen_time) + "&";
    } else {
      params += "date_range=" + encodeURIComponent($("#dates").val()) + "&";
    }

    // Quitar el ampersand final
    params = params.replace(/&+$/, "");
    window.location.href = "../../../CDMX3/scripts/download/export_reporte_leads.php?" + params;
  });
});
