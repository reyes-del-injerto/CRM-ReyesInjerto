const clinic = $('#chosen_clinic').val()

let eventModal = new bootstrap.Modal($('#eventModal')[0])
let modalAddBtn = $('.btn-add-event')
let modalUpdtBtn = $('.btn-update-event')
let modalDelBtn = $('.btn-delete-event')

let currentDate = 0
let filters = 0
let calendar, calendarEventsList

$(document).ready(function () {
  /* ---- Calendar Custom ---- */
  /*  Calendar Header */
  const calendarHeaderToolbar = {
    left: 'prev,next',
    center: 'title',
    right: 'dayGridMonth,timeGridDay,listMonth',
  }

  /* Format 12 Hours like X:00 */
  const slotLabelContent = function (slotInfo) {
    let hour = slotInfo.date.getHours()
    let minute = slotInfo.date.getMinutes().toString().padStart(2, '0')
    let ampm = hour >= 12 ? '' : ''
    hour = hour % 12
    hour = hour ? hour : 12
    return hour + ':' + minute + ' ' + ampm
  }

  /* Custom Button Label */
  const buttonText = {
    today: 'Hoy',
    month: 'Mes',
    week: 'Semana',
    day: 'Día',
    list: 'Lista Mensual',
  }
  /* Remove custom styles in timeGridDay view*/
  const eventContent = function (arg) {
    if (arg.view.type === 'timeGridDay') {
      return {
        html: `<div style="white-space: nowrap; overflow: hidden;">${arg.event.title}</div>`,
      }
    }
    return true
  }
  /* ---- END Calendar Custom ---- */

  /* --- Calendar Actions & Functions --- */
  //Select an Existing Event
  let calendarEventClick = function (info) {
    let raw_name,
      raw_num_med_record,
      eventNumMedRecord,
      eventName,
      tab = ''

    const {
      event: {
        id: eventId,
        title: eventTitle,
        start: rawEventStart,
        end: rawEventEnd,
        extendedProps: {
          attendance_type: eventAttendanceType,
          event_type: eventType,
          description: eventDescription,
          clinic: eventClinic,
          seller: eventSeller,
          closer: eventCloser,
          uploaded_by: eventUploadedBy,
          revision_time: eventRevisionTime,
          status: eventStatus,
          qualy: eventQualy,
          clinic: clinic,
        },
      },
    } = info

    const eventStart = splitDate(rawEventStart)
    const eventEnd = splitDate(rawEventEnd)

    $('#event_type').val(eventType)

    modalDelBtn.data('fc-event-public-id', eventId)

    switch (eventType) {
      case 'revision':
        ;[raw_name, raw_num_med_record] = eventTitle.split('-')

        eventName = raw_name.trim()

        eventNumMedRecord = raw_num_med_record.split('[')
        eventNumMedRecord = eventNumMedRecord[0].trim()

        $('#revision_time').val(eventRevisionTime)
        $('#revision_')
        break
      case 'valoracion':
        eventName = eventTitle.split('[')
        eventName = eventName[0].trim()
        break
      case 'tratamiento':
        ;[raw_name, raw_num_med_record] = eventTitle.split('-')

        eventName = raw_name.trim()
        eventNumMedRecord = raw_num_med_record.split('[')
        eventNumMedRecord = eventNumMedRecord[0].trim()

        break
    }

    $('a.nav-item').addClass('disabled')
    $('a.nav-item').removeClass('active')
    $('.tab-pane').removeClass('active show')

    $(`#tab-${eventType}`).addClass('active')
    $(`#tab-${eventType}`).show('active')
    $(`#content-${eventType}`).addClass('active show')

    $('#uploaded_by').html(`Evento creado por: ${eventUploadedBy}`)
    $(`#${eventType}_event_date`).val(eventStart[0])
    $(`#${eventType}_start_date`).val(eventStart[1])
    $(`#${eventType}_end_date`).val(eventEnd[1])
    $(`#${eventType}_num_med_record`).val(eventNumMedRecord)
    $(`#${eventType}_patient_name`).val(eventName)
    $(`#${eventType}_attendance_type`).val(eventAttendanceType)
    $(`#${eventType}_notes`).val(eventDescription)
    $(`#${eventType}_seller`).val(eventSeller)
    $(`#${eventType}_status`).val(eventStatus)
    $(`#${eventType}_qualy`).val(eventQualy)
    $(`#${eventType}_clinic`).val(eventClinic)

    modalAddBtn.hide()
    modalUpdtBtn.data('fc-event-public-id', eventId)
    modalUpdtBtn.show()
    modalDelBtn.show()

    eventModal.show()
  }

  //Create a New Event
  let calendarSelect = function (info) {
    modalAddBtn.show()
    modalUpdtBtn.hide()
    modalDelBtn.hide()

    const startDate = info.startStr
    let [startRawDate, startRawTime] = startDate.split('T')
    $('.event_dates').val(startRawDate)

    let startTime, endTime

    if (startRawTime === undefined) {
      startTime = '07:00 AM'
      endTime = addMinutesToTime('07:00 AM', 30)
    } else {
      startTime = startRawTime.split('-')[0]
      startTime = startTime.substr(0, 5)
      endTime = addMinutesToTime(startTime, 30)
    }

    $('.start_times').val(startTime)
    $('.end_times').val(endTime)
    $('a.nav-item').removeClass('disabled')

    eventModal.show()
  }

  /* ---- Init Calendar and Persist it up-to-date --- */
  window.fetchCalendarEvents = async function fetchCalendarEvents(
    currentDate,
    filters
  ) {
    try {
      calendarEventsList = await getCalendarEventsList(filters)

      // Create a new FullCalendar instance
      calendar = new FullCalendar.Calendar($('#calendar')[0], {
        longPressDelay: 1,
        locale: 'es',
        selectable: true,
        editable: false,
        droppable: false,
        events: calendarEventsList,
        eventClick: calendarEventClick,
        headerToolbar: calendarHeaderToolbar,
        slotMinTime: '07:00:00',
        slotMaxTime: '19:00:00',
        height: $(window).height() - 10,
        select: calendarSelect,
        initialView: 'timeGridDay',
        dayMaxEvents: 4,
        allDayText: 'VAC.',
        buttonText: buttonText,
        eventContent: eventContent,
        eventDidMount: function (info) {
          info.el.setAttribute('data-event-id', info.event.id)
        },
        slotLabelContent: slotLabelContent,
        scrollTimeReset: false,
      })

      calendar.render()

      if (currentDate != 0) {
        calendar.gotoDate(currentDate)
      }
    } catch (error) {
      console.error(error)
    }
  }

  //Retrieve calendar events
  function getCalendarEventsList(filters) {
    return new Promise(function (resolve, reject) {
      console.log("clinica",clinic);
      console.log("filtros",filters);
      $.ajax({
        method: 'POST',
        url: 'scripts/calendar/get_events.php',
        data: {
          clinic: clinic,
          filters: filters,
        },
        async: true,
        dataType: 'json',
      })
        .done(function (response) {
          console.log("get_events",response)
          if (response.success) {
            let calendarEventsList = response.events
            resolve(calendarEventsList)
          } else {
            showSweetAlert(
              'Error',
              response.message,
              'error',
              true,
              true,
              false
            )
          }
        })
        .fail(function (response) {
          console.log(response)
        })
    })
  }

  //Update Events (refetch)
  async function updateEvents() {
    //Save Scroll
    const getScroll = $('.fc-scroller-liquid').scrollTop()
    const scroll = isNaN(getScroll)
      ? $('.fc-scroller-liquid-absolute').scrollTop()
      : $('.fc-scroller-liquid').scrollTop()

    const currentView = calendar.view.type
    const currentDate = calendar.getDate()

    //Save Filters
    let filters = []
    $('.cb_event_type:checked').each(function () {
      filters.push($(this).val())
    })

    //Update events and keep preferences
    await fetchCalendarEvents(currentDate, filters)
    calendar.refetchEvents()
    calendar.gotoDate(currentDate)
    calendar.changeView(currentView)
    $('.fc-scroller').scrollTop(scroll)
  }

  /* ---- Filter Actions ---- */

  //Filters Actions
  $(document).on('change', '.cb_event_type', function (e) {
    let filters = []
    $('.cb_event_type:checked').each(function () {
      filters.push($(this).val())
    })

    if (filters.length === 0) {
      $(this).prop('checked', true)
      e.stopPropagation()
    } else {
      currentView = calendar.view
      currentDate = currentView.currentStart
      fetchCalendarEvents(currentDate, filters)
    }
  })

  /* ---- END Filter Actions ---- */

  function splitDate(rawDate) {
    // Crear un objeto de fecha a partir de la cadena
    var date = new Date(rawDate)

    // Formatear la fecha a YYYY-MM-DD
    var formattedDate =
      date.getFullYear() +
      '-' +
      ('0' + (date.getMonth() + 1)).slice(-2) +
      '-' +
      ('0' + date.getDate()).slice(-2)

    // Formatear la hora a HH:MM
    var formattedHour =
      ('0' + date.getHours()).slice(-2) +
      ':' +
      ('0' + date.getMinutes()).slice(-2)

    return [formattedDate, formattedHour]
  }
  /* --- END Calendar Actions --- */

  //Load the calendar for the first time
  fetchCalendarEvents(currentDate, filters)

  //Update Events every 30sec
  setInterval(updateEvents, 30000)
  /*---- End ---- */
})

function showSweetAlert(
  title,
  text,
  icon,
  timer,
  timerProgressBar,
  showConfirmButton
) {
  return Swal.fire({
    title: title || 'Error',
    text: text || 'Contacta a administración',
    icon: icon || 'error',
    timer: timer || 2500,
    timerProgressBar: timerProgressBar || true,
    showConfirmButton: showConfirmButton || false,
  })
}

function copyToClipboard(txt) {
  navigator.clipboard.writeText(txt).catch(function (error) {
    alert('Error al copiar texto al portapapeles: ', error)
  })
}

/* Add Minutes to End Time in Modal */
function addMinutesToTime(timeStr, minutesToAdd) {
  const [time, modifier] = timeStr.split(' ')
  let [hours, minutes] = time.split(':').map(Number)

  if (modifier === 'PM' && hours < 12) {
    hours += 12
  } else if (modifier === 'AM' && hours === 12) {
    hours = 0
  }

  // Crear un objeto de fecha (la fecha no importa en este caso)
  const date = new Date(2000, 0, 1, hours, minutes)
  // Añadir 30 minutos
  date.setMinutes(date.getMinutes() + minutesToAdd)

  // Obtener las horas y minutos después de añadir
  let newHours = date.getHours()
  let newMinutes = date.getMinutes()

  // Convertir de nuevo a formato de 12 horas si es necesario
  const newModifier = newHours >= 12 ? 'PM' : 'AM'
  newHours = newHours % 12
  newHours = newHours ? newHours : 12 // Convertir 0 a 12 para las 12 AM
  newMinutes = newMinutes < 10 ? '0' + newMinutes : newMinutes // Añadir cero adelante si hace falta

  // Formatear la nueva hora
  return `${newHours}:${newMinutes} ${newModifier}`
}

/* Fix the issue that keeps the fcpopover in monthView */
$(document).on('click', '.fc-daygrid-event-harness', function (e) {
  $('.fc-popover').fadeOut('slow')
})
