$(document).ready(function () {
  const o = "rtl" === $("html").attr("data-textdirection");

  toastr.options = {
    extendedTimeOut: 0,
    timeOut: 0,
    tapToDismiss: true,
  };

  const notifications = true;

  function appointmentReminder() {
    const now = new Date();
    const minutes = now.getMinutes();

    if (minutes % 10 === 0) {
      $.ajax({
        data: {
          clinic: "Santafe",
        },
        dataType: "json",
        method: "POST",
        url: "scripts/notifications/toast_appointment.php",
      })
        .done(function (response) {
          console.log(response);
          if (response.success == true) {
            if (response.appointments.length > 0) {
              response.appointments.forEach(function (app) {
                toastr.warning(app.body, app.title, {
                  positionClass: "toast-top-left",
                  rtl: o,
                });
              });
            }
          } else {
            notifications = false;
          }
        })
        .fail(function (response) {
          console.log(response.responseText);
        });
    }
  }

  notifications
    ? appointmentReminder()
    : Swal.fire({
        title: "🙁🙁",
        text: "Las notificaciones no están disponibles en este momento.",
        icon: "error",
        confirmButtonColor: "#3085d6",
      });
  notifications ? setInterval(appointmentReminder, 60000) : "";
});
