$(document).ready(function () {
  const clinic = $("#chosen_clinic").val();
  const o = "rtl" === $("html").attr("data-textdirection");

  $(".input-times").timepicker({
    timeFormat: "h:i A",
    interval: 30,
    minTime: "09:00am",
    maxTime: "18:00pm",
    dynamic: false,
    dropdown: true,
    scrollbar: true,
  });

  //Search Events
  $("#search_appointments").submit(function (e) {
    e.preventDefault();
    const clinic = $("#clinic").val();
    const search = $("#search").val();
    if (this.checkValidity()) {
      $.ajax({
        url: "scripts/calendar/search.php",
        method: "POST",
        dataType: "json",
        data: {
          clinic: clinic,
          search: search,
        },
        beforeSend: function () {
          Swal.fire({
            title: "Cargando...",
            text: "Por favor, espera",
            showConfirmButton: false,
            allowOutsideClick: false,
          });
        },
      })
        .done(function (response) {
          Swal.close();
          console.log(response);
          if (response.success) {
            showResults(response.coincidences);
          } else {
            showSweetAlert(
              "😟",
              "No encontré coincidencias",
              "error",
              2500,
              true,
              false
            );
          }
        })
        .fail(function (response) {
          console.log(response);
          showSweetAlert();
        });
    }
  });

  //Go to searched event
  $(document).on("click", ".goToEvent", function (e) {
    e.preventDefault();
    $("#searchModal").modal("hide");

    let date = $(this).data("date");
    let eventId = $(this).data("id");

    date = new Date(date);
    calendar.changeView("timeGridDay");
    calendar.gotoDate(date);

    let appointment = $("#calendar").find(
      '.fc-event[data-event-id="' + eventId + '"]'
    );
    let originalBgColor = appointment.css("background-color");

    appointment.css("background-color", "#2dbdb8");

    setTimeout(function () {
      appointment.css("background-color", originalBgColor);
    }, 5000);
  });
  //Copy Agenda for tomorrow
  $("#copy_agenda").click(function (e) {
    $.ajax({
      url: "scripts/calendar/get_daily_agenda.php",
      method: "POST",
      data: {
        clinic: clinic,
      },
      dataType: "json",
    })
      .done(function (response) {
        console.log(response);
        if (response.success) {
          toastr.success("Agenda copiada al portapapeles", "Listo!", {
            positionClass: "toast-top-left",
            rtl: o,
          });
          let messageCopy =
          "Hola buen día les confirmo la agenda del día de hoy. \n" +
          response.agenda.toUpperCase();
          console.log(messageCopy)
        copyToClipboard(messageCopy);
        } else {
          showSweetAlert("Error", response.message);
        }
      })
      .fail(function (response) {
        console.log(response);
        showSweetAlert(response);
      });
  });

  $("#copy_agenda_message").click(function (e) {
    $.ajax({
      url: "scripts/calendar/get_daily_agenda_with_message.php",
      method: "POST",
      data: {
        clinic: clinic,
      },
      dataType: "json",
    })
      .done(function (response) {
        console.log(response);
        if (response.success) {
          toastr.success("Plantilla copiada al portapapeles", "Listo!", {
            positionClass: "toast-top-left",
            rtl: o,
          });
          let messageC =
            "Hola buen día les comparto la agenda del dia de mañana \n" +
            response.agenda.toUpperCase();
          copyToClipboard(messageC);
          console.log("si");
          console.log(
            "Hola buen día les comparto la agenda del dia de mañana",
            response.agenda
          );
        } else {
          showSweetAlert("Error", response.message);
        }
      })
      .fail(function (response) {
        console.log(response.responseText);
        showSweetAlert(response);
      });
  });
});

function showResults(response) {
  let html = `
      <table border="1" class="table">
                  <tr>
                      <th>Cita</th>
                      <th>Fecha</th>
                      <th>Calif.</th>
                      <th>Ir</th>
                  </tr>`;

  response.forEach(function (event) {
    html += `<tr>
                  <td>${event.title}</td>
                  <td>${event.date}</td>
                  <td>${event.qualy}</td>
                  <td>
                      <button data-id="${event.id}" data-date="${event.start}" class="btn btn-xs btn-success goToEvent"><i class="fa fa-arrow-right"></i></button>
                  </td>
              </tr>`;
  });

  html += "</table>";

  $("#searchModalBody").html(html);
  $("#searchModal").modal("show");
}

/* ----- Modal Event Actions ---- */

//Choose Event Type
$(document).on("click", ".nav-item", function (e) {
  let id = $(this).attr("id");
  event_type = id.split("-")[1];
  $("#event_type").val(event_type);
});

// Search Number Medical Record
$(document).on("change", "#revision_num_med_record", function () {
  const num_med_record = $(this).val();

  if (num_med_record.length > 0) {
    $.ajax({
      data: {
        clinic: clinic,
        num_med_record: num_med_record,
      },
      dataType: "json",
      method: "POST",
      url: "scripts/calendar/get_patient_name.php",
      beforeSend: function () {
        Swal.fire({
          title: "Buscando...",
          text: "Por favor, espera",
          showConfirmButton: false,
          allowOutsideClick: false,
        });
      },
    })
      .done(function (response) {
        Swal.close();
        console.log(response);
        if (response.success) {
          $("#revision_patient_name").attr("readonly", "true");
          $("#revision_patient_name").val(response.fullname);
        } else {
          Swal.fire({
            title: "🙁",
            text: response.message,
            icon: "warning",
            showConfirmButton: false,
            timer: 2300,
            timerProgressBar: true,
          });
          $("#revision_patient_name").removeAttr("readonly");
          $("#revision_patient_name").val("");
        }
      })
      .fail(function (response) {
        console.log(response.responseText);
      });
  } else {
    $("#revision_patient_name").removeAttr("readonly");
    $("#revision_patient_name").val("");
  }
});

$(document).on("change", "#tratamiento_num_med_record", function () {
  const num_med_record = $(this).val();

  if (num_med_record.length > 0) {
    $.ajax({
      data: {
        clinic: clinic,
        num_med_record: num_med_record,
      },
      dataType: "json",
      method: "POST",
      url: "scripts/calendar/get_patient_name.php",
      beforeSend: function () {
        Swal.fire({
          title: "Buscando...",
          text: "Por favor, espera",
          showConfirmButton: false,
          allowOutsideClick: false,
        });
      },
    })
      .done(function (response) {
        Swal.close();
        console.log(response);
        if (response.success) {
          $("#tratamiento_patient_name").attr("readonly", "true");
          $("#tratamiento_patient_name").val(response.fullname);
        } else {
          Swal.fire({
            title: "🙁",
            text: response.message,
            icon: "warning",
            showConfirmButton: false,
            timer: 2300,
            timerProgressBar: true,
          });
          $("#tratamiento_patient_name").removeAttr("readonly");
          $("#tratamiento_patient_name").val("");
        }
      })
      .fail(function (response) {
        console.log(response.responseText);
      });
  } else {
    $("#tratamiento_patient_name").removeAttr("readonly");
    $("#tratamiento_patient_name").val("");
  }
});



// Reset all Input Forms when modal is closed
$("#eventModal").on("hidden.bs.modal", function () {
  $("#formRevision")[0].reset();
  $("#formValoracion")[0].reset();
  $("#formEvento")[0].reset();
  $("#formTratamiento")[0].reset();
  $("textarea").val("");
  $(".tab-pane").removeClass("active show");
  $(".nav-item").removeClass("active");
});

//Change end-time when user changes start-time
$(document).on("change", ".start_times", function (e) {
  e.preventDefault();
  startTime = $(this).val();
  endTime = addMinutesToTime(startTime, 30);
  $(".start_times").val(startTime);
  $(".end_times").val(endTime);
});

//Send Form to create new event
modalAddBtn.on("click", function (e) {
  e.preventDefault();
  sendForm("add", e);
});

//Send Form to update an event
modalUpdtBtn.on("click", function (e) {
  e.preventDefault();
  sendForm("update", e);
});

//Delete an existing event
modalDelBtn.on("click", function (e) {
  let event_id = $(this).data("fc-event-public-id");

  Swal.fire({
    title: "¿Estás seguro(a) de eliminar este evento?",
    text: "Esta acción no se puede revertir.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar evento",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        method: "POST",
        url: "scripts/calendar/delete_event.php",
        data: {
          event_id: event_id,
        },
        dataType: "json",
      })
        .done(function (response) {
          $("#eventModal").modal("hide");
          const currentDate = calendar.getDate();
          fetchCalendarEvents(currentDate, filters);

          Swal.fire(
            "Evento eliminado",
            "El evento ha sido eliminado correctamente.",
            "success"
          );
        })
        .fail(function (response) {
          console.log(response);
        });
    }
  });
}); 
/* ----- END Modal Event Actions ---- */

/* ----- Modal Event Functions ---- */

//Send Form to Create or Update an Event
function sendForm(action, e) {
  console.log("send form",action , "e-> ",e)
  const url = `scripts/calendar/${action}_event.php`;
  const event_type = $("#event_type").val();
  console.log("eventype: ",event_type)
  const event_form = event_type.charAt(0).toUpperCase() + event_type.slice(1);
  console.log("event form", event_form)
  let formData = $(`#form${event_form}`).serialize();
  console.log("data del form",formData)
  const form = $(`#form${event_form}`)[0];
  console.log(form)

  const event_id =
    action === "update" ? modalUpdtBtn.data("fc-event-public-id") : null;

  // Función mejorada para obtener los campos faltantes
  const getInvalidFields = (form) => {
    const invalidFields = [];

    $(form)
      .find("input[required], select[required], textarea[required]")
      .each(function () {
        if (!this.value.trim()) {
          const label = $(this).closest('.input-block').find('label').text().trim();
          invalidFields.push(label || $(this).attr("name") || $(this).attr("id"));
        }
      });

    return invalidFields;
  };

  // Validar si el formulario es válido
  if (form.checkValidity()) {
    e.preventDefault();

    modalAddBtn.attr("disabled", "true");
    formData += "&event_type=" + encodeURIComponent(event_type);
    formData +=
      action == "update" ? "&event_id=" + encodeURIComponent(event_id) : "";

    // Agregar user_id a los datos del formulario
    const user_id = localStorage.getItem("user_id");
    formData += "&user_id=" + encodeURIComponent(user_id);

    $.ajax({
      url: url,
      method: "POST",
      data: formData,
      dataType: "json",
    })
      .done(function (response) {
        if (response.success) {
          showSweetAlert(
            "Listo!",
            response.message,
            "success",
            1500,
            true,
            false
          ).then(function () {
            const currentView = calendar.view;
            currentDate = currentView.currentStart;
            fetchCalendarEvents(currentDate, filters);
            form.reset();
            eventModal.hide();
          });
        } else {
          showSweetAlert("Error", response.message);
        }
      })
      .fail(function (response) {
        console.log(response);
        showSweetAlert("Error", response.message);
      });
  } else {
    // Obtener los campos faltantes
    const invalidFields = getInvalidFields(form);

    // Mostrar en la consola los campos faltantes
    console.log("Campos faltantes:", invalidFields);

    // Mostrar los campos faltantes en el mensaje de advertencia
    const missingFieldsMessage =
      invalidFields.length > 0
        ? `Faltan los siguientes campos: ${invalidFields.join(", ")}`
        : "Por favor, complete todos los campos obligatorios.";

    showSweetAlert("Faltan datos del evento", missingFieldsMessage, "warning", 2000, true, false);
  }
  modalAddBtn.removeAttr("disabled");
}


/* ---- END Modal Event Functions ---- */

/* ---- MiniCalendar Actions ---- */

//Mini Calendar -> Today Button
$(document).on("click", ".calendar-today-button", function (e) {
  e.preventDefault();
  const year = parseInt($("#year-selected").val(), 10);
  const month = parseInt($("#month-selected").val(), 10);

  let txt = $(this).html().split(":");
  const day = txt[1].trim();

  const date = new Date(year, month, day);
  calendar.changeView("timeGridDay");
  calendar.gotoDate(date);
});

//Mini Calendar -> Days
$(document).on("click", ".calendar-day", function (e) {
  e.preventDefault();
  $(".calendar-day:not(.calendar-day-active)").css({
    background: "white",
  });
  $(this).css({
    background: "#e0ac44",
    "border-radius": "15px",
  });
  const year = parseInt($("#year-selected").val(), 10);
  const month = parseInt($("#month-selected").val(), 10);
  const day = parseInt($(this).html(), 10);

  const date = new Date(year, month, day);
  calendar.changeView("timeGridDay");
  calendar.gotoDate(date);
});

/* ---- END MiniCalendar Actions ---- */
