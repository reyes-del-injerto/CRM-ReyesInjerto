const lead_id = $('#id').val()
const stages = [
  'Nuevo Lead',// ACT 0
  'En prospección',// ACT 1
  'Interesado',// ACT 2
  'Agendó valoración',// ACT3
  'Valorado',// ACT4
  'Dio anticipo',
  'Cerrado',
  'Canceló',
  'Reagendó',
  'Cliente',
  'No interesado'
]
const qualif = [
  'En conversación',
  'Negociación',
  'Fuera de su presupuesto',
  'Se encuentra lejos',
  'No es candidato',
  'Está comparando opciones',
  'No acudió a valoración',
  'Pendiente de anticipo',
  'Dejó de contestar',
  'No volver a contactar',
  'Seguimiento pre-proced.',
  'Seguimiento post-proced.',
]

let profile
let preventNoSave = false
let paymentsTable
Swal.fire({
  title: 'Cargando...',
  allowOutsideClick: false,
  showConfirmButton: false,
})

/* --------------- SEGUIMIENTO DEL LEAD --------------- */

$(document).ready(function () {
  console.log("lead id de load_lead", lead_id)
  $.ajax({
    data: {
      lead_id: lead_id,
    },
    cache: false,
    dataType: 'json',
    method: 'POST',
    url: 'scripts/sales/load_lead.php',
  })
    .done(async function (response) {
      console.log("scripts/sales/load_lead.php", response)
      Swal.close()
     // console.log("vista de cliente:",response)
      showAvailableTabs(response.lead_info.stage)

      setStageSelect(response.lead_info.stage)
      setQualifSelect(response.lead_info.stage)

      fillLeadInfo(response.lead_info)
      fillLeadTasks(response.lead_tasks)

      profile = await getProfileData()

      //Vista de Cliente
      $('#client_fullname').html(profile.first_name + ' ' + profile.last_name)
      $('#client_procedure_type').html(profile.procedure_type)
      $('#client_num_med_record').html(response.lead_num_med_record)

      const client_procedure_date =
        profile.procedure_date === '2030-01-01'
          ? 'Por definir'
          : profile.procedure_date

      $('#client_procedure_date').html('Fecha Proced: ' + client_procedure_date)
    })
    .fail(function (response) {
      Swal.close()
      console.log("fail scripts/sales/load_lead.php: ",response)
      showSweetAlert()
    })

  $('#stage').change(function (e) {
    e.preventDefault()
    //Assign qualifions to each Stage
    let change = {
      'Nuevo Lead': [qualif[0]],
      'No interesado': [qualif[0]],
      'En prospección': [
        qualif[1],
        qualif[2],
        qualif[3],
        qualif[5],
        qualif[8],
        qualif[9],
      ],
      Interesado: [
        qualif[1],
        qualif[2],
        qualif[3],
        qualif[5],
        qualif[8],
        qualif[9],
      ],
      'Agendó valoración': [
        qualif[1],
        qualif[2],
        qualif[3],
        qualif[4],
        qualif[5],
        qualif[6],
        qualif[8],
        qualif[9],
      ],
      Valorado: [
        qualif[1],
        qualif[2],
        qualif[3],
        qualif[4],
        qualif[5],
        qualif[6],
        qualif[8],
        qualif[9],
        qualif[7],
      ],
      'Dio anticipo': [qualif[10], qualif[8], qualif[9]],
      Cerrado: [qualif[10], qualif[8], qualif[9]],
      Canceló: [qualif[10], qualif[8], qualif[9]],
      Reagendó: [qualif[10], qualif[8], qualif[9]],
      Cliente: [qualif[11], qualif[8]],
    }

    //Load matching options
    let selectedValue = $(this).val()
    let selectedOptions = change[selectedValue]
    $('#qualif').empty()

    //Put the options in qualif select
    if (selectedOptions) {
      $.each(selectedOptions, function (index, optionText) {
        $('#qualif').append(
          $('<option></option>').attr('value', optionText).text(optionText)
        )
      })
    }
  })
})

function fillLeadInfo(info) {
  const full_name = info.first_name + ' ' + info.last_name;
  const status = info.stage;

  // Header
  $('#lead_fullname').html('#' + info.id + ' ' + full_name);
  $('#lead_stage').html(status);
  $('#lead_qualif').html(info.quali);

  // Lead Info
  $('#id').val(info.id);
  $('#first_name').val(info.first_name);
  $('#last_name').val(info.last_name);
  $('#clinic').val(info.clinic);
  $('#origin').val(info.origin);
  $('#phone').val(info.phone);
  $('#interested_in').val(info.interested_in);
  $('#stage').val(info.stage); // Ensure the stage is set correctly
  $('#qualif').val(info.quali);
  $('#notes').val(info.notes);
  $('#seller').val(info.seller);
  $('#evaluator').val(info.evaluator);

  // Check if evaluator is null and hide the container if true
  if (info.evaluator === null) {
    $('#contenedor_evaluator').hide();
  } else {
    $('#contenedor_evaluator').show();
  }

  

  $('#respond_url').prop('href', info.link);
  $('#call_url').prop('href', 'tel:' + info.phone);
}


function fillLeadTasks(current_tasks) {
  let tasks = ''

  current_tasks.forEach(function (task) {
    const marker_style = task.status
      ? `<div class="marker" style="background-color:green;" data-task-id=2><i class="fa-solid fa-check" style="color:#fff;margin-top:5px;"></i></div>`
      : `<div class="marker" data-task-id=2></div>`

    tasks += `<div class="timeline-block timeline-block-right" data-task-id="${task.id}">
            ${marker_style}
            <div class="timeline-content" data-task-id="${task.id}">
                <h4>${task.subject}
                    <div class="actions" style="display:none;">
                        <button type="button" class="complete-task btn btn-rounded btn-outline-success"><i class="fa-solid fa-check"></i> </button>
                        <button type="button" class="delete-task btn btn-rounded btn-outline-danger"><i class="fa fa-trash"></i> </button>
                    
                    </div>
                </h4>
                <h5>${task.comments}</h5>
                <span>${task.end_date}</span>
            </div>
        </div>`
  })
  $('#timeline').html(tasks)
}

function setStageSelect(current_stage) {
  // Define all possible stages
  const allStages = [
    'Nuevo Lead', 
    'En prospección', 
    'Interesado', 
    'Agendó valoración', 
    'Valorado', 
    'Dio anticipo', 
    'Cerrado', 
    'Canceló', 
    'Reagendó', 
    'Cliente', 
    'No interesado'
  ];

  // Define stage options based on the current stage
  const change = {
    'Nuevo Lead': [allStages[0], allStages[1], allStages[2], allStages[3], allStages[4], allStages[10]],
    'En prospección': [allStages[0], allStages[1], allStages[2], allStages[3], allStages[4], allStages[10]],
    Interesado: [allStages[0], allStages[1], allStages[2], allStages[3], allStages[4], allStages[10]],
    'Agendó valoración': [allStages[3], allStages[4], allStages[5], allStages[10]],
    Valorado: [allStages[3], allStages[4], allStages[5], allStages[10]],
    'Dio anticipo': [allStages[5], allStages[6], allStages[7], allStages[8], allStages[10]],
    Cerrado: [allStages[5], allStages[6], allStages[7], allStages[8], allStages[10]],
    Canceló: [allStages[5], allStages[6], allStages[7], allStages[8], allStages[10]],
    Reagendó: [allStages[5], allStages[6], allStages[7], allStages[8], allStages[10]],
    Cliente: [allStages[9], allStages[10]],
    'No interesado': [allStages[0], allStages[1], allStages[2], allStages[3], allStages[4], allStages[10]] // Add "Nuevo Lead" option to "No interesado"
  }

  // Get the options for the current stage
  const stage_options = change[current_stage] || allStages; // Default to all stages if no match

  $('#stage').empty();
  // Populate the Stage select element
  $.each(stage_options, function (index, optionText) {
    $('#stage').append(
      $('<option></option>').attr('value', optionText).text(optionText)
    )
  });
}

function setQualifSelect(stage) {
  //Assign options to each Stage
  let change = {
    'Nuevo Lead': [qualif[0]],
    'En prospección': [
      qualif[0],
      qualif[1],
      qualif[2],
      qualif[3],
      qualif[5],
      qualif[8],
      qualif[9],
    ],
    Interesado: [
      qualif[1],
      qualif[2],
      qualif[3],
      qualif[5],
      qualif[8],
      qualif[9],
    ],
    'Agendó valoración': [
      qualif[1],
      qualif[2],
      qualif[3],
      qualif[4],
      qualif[5],
      qualif[6],
      qualif[8],
      qualif[9],
    ],
    Valorado: [
      qualif[1],
      qualif[2],
      qualif[3],
      qualif[4],
      qualif[5],
      qualif[6],
      qualif[8],
      qualif[9],
      qualif[7],
    ],
    'Dio anticipo': [qualif[10], qualif[8], qualif[9]],
    Cerrado: [qualif[10], qualif[8], qualif[9]],
    Canceló: [qualif[10], qualif[8], qualif[9]],
    Reagendó: [qualif[10], qualif[8], qualif[9]],
    Cliente: [qualif[11], qualif[8]],
  }

  //Load matching options
  let selectedOptions = change[stage]
  $('#qualif').empty()

  //Put the options in qualif select
  if (selectedOptions) {
    $.each(selectedOptions, function (index, optionText) {
      $('#qualif').append(
        $('<option></option>').attr('value', optionText).text(optionText)
      )
    })
  }
}
function showAvailableTabs(current_stage) {
  console.log("current stage", current_stage);

  // Hide all tabs initially
 // $('.invoices-tabs li a').css('display', 'none');

  if (current_stage === 'No interesado') {
    // Do not show any additional tabs
    return;
  }

  if (stages.indexOf(current_stage) === 3 || stages.indexOf(current_stage) === 4) {
    $('.invoices-tabs li a[data-tab="assessment"]').css('display', 'block');
  }

  if (stages.indexOf(current_stage) > 4) {
    $('.invoices-tabs li a[data-tab="assessment"]').css('display', 'block');
    $('.invoices-tabs li a[data-tab="profile"]').css('display', 'block');
    $('.invoices-tabs li a[data-tab="invoices"]').css('display', 'block');
    $('.invoices-tabs li a[data-tab="payments"]').css('display', 'block');
  }
}

$('#editLeadForm').click(function (e) {
  e.preventDefault()
  $('#update_lead input, #update_lead select, #update_lead textarea').prop(
    'disabled',
    false
  )
  $('#btn_update_lead').fadeIn('slow')
})

/* ----- INICIO TAREAS ----- */

//Add New Task
$('#new_task').submit(function (e) {
  e.preventDefault()
  if (this.checkValidity()) {
    e.preventDefault()
    const form = $(this).serialize()
    console.log("🚀 ~ newtask:", form)
    const url = $(this).attr('action')
    const method = $(this).attr('method')

    $.ajax({
      data: form,
      cache: false,
      dataType: 'json',
      method: method,
      url: url,
    })
      .done(function (response) {
        if (response.success) {
          showSweetAlert(
            'Listo!',
            response.message,
            'success',
            1500,
            true,
            false
          ).then(function () {
            location.reload()
          })
        } else if (response.success == false) {
          console.error(response)
          showSweetAlert('Error!', response.message, 'error', 2300, true, false)
        }
      })
      .fail(function (response) {
        console.error(response)
        showSweetAlert('Error!', response, 'error', 2300, true, false)
      })
  }
})

//TASKS Functions and Actions
$('#time').change(function (e) {
  $('#in_the_morning').attr('checked', 'checked')
})

$(document).on('mouseover', '.timeline-block-right', function (e) {
  e.preventDefault()
  const actions = $(this).find('.actions')
  actions.fadeIn('slow')
})

$(document).on('mouseleave', '.timeline-block-right', function (e) {
  e.preventDefault()
  $('.actions').fadeOut('fast')
})

$(document).on('click', '.complete-task', function (e) {
  const task_id = $(this).closest('.timeline-block-right').data('task-id')
  console.log(task_id)
  const marker = $(this).closest('.timeline-block-right').find('.marker')
  console.log("mark_complete")
  $.ajax({
    data: {
      task_id: task_id,
    },
    cache: false,
    dataType: 'json',
    method: 'POST',
    url: 'scripts/sales/lead_tasks/mark_complete.php',
  })
    .done(function (response) {
      if (response.success) {
        window.location.reload();
         //showSweetAlert()
      /*   marker.css('background-color', 'green')
        marker.html(
          `<i class="fa-solid fa-check" style="color:#fff;margin-top:5px;"></i>`
        ) */
      } else {
        console.log("no success",response)
        //showSweetAlert()
      }
    })
    .fail(function (response) {
      console.error("fail",response)
      //showSweetAlert()
    })
})

$(document).on('click', '.delete-task', function (e) {
  const task_id = $(this).closest('.timeline-block-right').data('task-id')

  Swal.fire({
    title: '¿Estás segura/o?',
    text: 'Borrarás la tarea permanentemente',
    icon: 'error',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        data: {
          task_id: task_id,
        },
        cache: false,
        dataType: 'json',
        method: 'POST',
        url: 'scripts/sales/delete_lead_task.php',
      })
        .done(function (response) {
          console.log(response)
          if (response.success) {
            showSweetAlert(
              'Listo!',
              response.message,
              'success',
              1500,
              true,
              false
            ).then(function () {
              location.reload()
            })
          } else {
            console.log(response)
            showSweetAlert()
          }
        })
        .fail(function (response) {
          console.error(response)
          showSweetAlert()
        })
    }
  })
})

/* ----- FIN TAREAS ----- */

//Update Lead Info
$('#update_lead').submit(function (e) {
  e.preventDefault()
  const formData = $(this).serialize()
  console.log(formData)
  $.ajax({
    data: formData,
    cache: false,
    dataType: 'json',
    method: 'POST',
    url: 'scripts/sales/update_lead.php',
  })
    .done(function (response) {
      console.log(response)
      if (response.success) {
        showSweetAlert(
          'Listo!',
          response.message,
          'success',
          1500,
          true,
          false
        ).then(function () {
          location.reload()
        })
      } else {
        console.error(response)
        showSweetAlert('Error!', response.message, 'error', 2300, true, false)
      }
    })
    .fail(function (response) {
      console.error(response)
      showSweetAlert('Error!', response.message, 'error', 2300, true, false)
    })
})

/* --------------- FIN SEGUIMIENTO DEL LEAD --------------- */

// Change between Main Tabs
$('.invoices-tabs ul a').click(function (e) {
  console.log("change tab", $(this))
  e.preventDefault()
  changeTab($(this))
})

function changeTab(current_tab) {
  const selected_tab = current_tab.data('tab')
  //Hide all tabs
  $('.tab').css('display', 'none').removeClass('tab-active')

  //Show selected tab
  const element_tab = $('#tab-' + selected_tab)
  console.log("elementab ~ :", element_tab)

  element_tab.css('display', 'block')
  element_tab.addClass('tab-active')
  $('.invoices-tabs ul a').removeClass('active')
  current_tab.addClass('active')

   // Reset formContainer if the selected tab is "invoices"
   if (selected_tab === 'invoices') {
    $('#formContainer').html('') // Vaciar el contenido de formContainer
  }
}

/* --------------- VALORACIÓN --------------- */

$('#editAssessmentForm').click(function (e) {
  e.preventDefault()
  $(
    '#assessment_document input, #assessment_document select, #assessment_document textarea'
  ).prop('disabled', false)
  if ($('#open_date').is(':checked')) {
    $('#e_procedure_date').attr('disabled', 'true')
  }
})

//Enable "Fecha abierta" instead of Fix date.
$('#open_date').change(function (e) {
  let today = new Date()
  let todayDate = today.toISOString().split('T')[0] // Formatear la fecha como 'AAAA-MM-DD'

  e.preventDefault()
  if ($(this).is(':checked')) {
    $('#e_procedure_date').val('2030-01-01')
    $('#e_procedure_date').attr('disabled', 'true')
  } else {
    $('#e_procedure_date').val(todayDate)
    $('#e_procedure_date').removeAttr('disabled')
  }
})

//
$('#assessment_document').submit(function (e) {
  if (this.checkValidity()) {
    e.preventDefault()
    let formData = new FormData(this)
    const action = $(this).attr('action')
    const method = $(this).attr('method')
    sendAssessment(formData, action, method)
  }
})

$('li[data-tab="assessment"]').click(function (e) {
  e.preventDefault()

  $.ajax({
    data: {
      lead_id: lead_id,
    },
    cache: false,
    dataType: 'json',
    method: 'POST',
    url: 'scripts/sales/load_assessment_lead.php',
  })
    .done(function (response) {
      console.log("done scripts/sales/load_assessment_lead.php",response)
      if (response.assessment != false) {
        const e = JSON.parse(response.assessment)
        fillAssessmentInfo(e)
        loadAssessmentPDF(e.lead_id, e.timestamp)
        $(
          '#assessment_document input, #assessment_document select, #assessment_document textarea'
        ).prop('disabled', true)
      }
    })
    .fail(function (response) {
      console.log("fail scripts/sales/load_assessment_lead.php ",response)
      showSweetAlert()
    })
})

function sendAssessment(formData, action, method) {
  Swal.fire({
    title: 'Generando Hoja ...',
    allowOutsideClick: false,
    showConfirmButton: false,
  })

  $.ajax({
    data: formData,
    contentType: false,
    processData: false,
    method: method,
    url: action,
  })
    .done(function (response) {
      console.log(response)
      getProfileData();
    
      let url = response.path
      url = url.replace('../../', '', url)

      $('#pdfViewer').attr('src', url)
      $('#pdfInvoiceDownloadAssesment').attr('src', url)
      $('#divPdfAssessment').fadeIn('slow')
      $('html, body').animate(
        {
          scrollTop: $(document).height() - $(window).height(),
        },
        'slow'
      )
      Swal.close()
    })
    .fail(function (response) {
      Swal.close()
      console.log(response.responseText)
      showSweetAlert()
    })
}

function fillAssessmentInfo(e) {
  $('#e_assessment_date').val(e.date)
  $('#e_client_firstname').val(e.first_name)
  $('#e_client_lastname').val(e.last_name)
  e.procedure_date == '2030-01-01'
    ? $('#open_date').prop('checked', true)
    : $('#e_procedure_date').val(e.procedure_date)
  $('#e_procedure_type').val(e.procedure_type)
  $('#e_closer').val(e.closer)
  $('#e_first_meet_type').val(e.first_meet_type)
  $('#e_description').val(e.notes)
  $('#e_clinic').val(e.clinic)
  $('#e_assessment_type').val(e.type)

  $('#e_current_photo_name').val(e.timestamp)
}

function loadAssessmentPDF(lead_id, timestamp) {
  const url = `storage/leads/${lead_id}/assessment/valoracion_${timestamp}.pdf`
  fetch(url, {
    method: 'HEAD',
  })
    .then((response) => {
      if (
        response.status === 200 &&
        response.headers.get('Content-Type') === 'application/pdf'
      ) {
        $('#pdfViewer').attr('src', url)
        $('#pdfInvoiceDownloadAssesment').attr('href', url)
        $('#divPdfAssessment').fadeIn('slow')
      } else {
        showSweetAlert(
          '😕😕😕',
          'Valoración no encontrada debido a que es un px importado.',
          'error',
          2000,
          true,
          false
        )
      }
    })
    .catch((error) => {
      console.error('Error al verificar la URL:', error)
    })
}

/* --------------- FIN VALORACIÓN --------------- */

/* --------------- PERFIL DEL PX --------------- */

//Select Secondary Tabs
$('#files_tabs a').click(function (e) {
  e.preventDefault()
  const a = $(this)
  const tab = a.data('tab')

  showProfileTabs(tab)
  if (tab == 'summary') {
    fillSummaryInfo()
  }
  $('#files_tabs a').removeClass('active')
  a.addClass('active')
})

async function getProfileData() {
  try {
    console.log("get profile data id : ", lead_id)
    const response = await $.ajax({
      data: {
        lead_id: lead_id,
      },
      cache: false,
      dataType: 'json',
      method: 'POST',
      url: 'scripts/sales/load_info_profile_px.php',
    })

    if (response.success) {
      console.log("getProfileData", response.profile)
      return response.profile[0]
    } else {
      throw new Error(response.message)
    }
  } catch (error) {
    console.log("getProfileData error", response)

    showSweetAlert('Error', error.message, 'error', 2300, true, false)
    console.error('Ocurrió un error:', error)
    throw error
  }
}

function showProfileTabs(tab) {
  // Ocultar todas las tabs
  $('.tab-files').css('display', 'none').removeClass('tab-active')

  // Mostrar la tab seleccionada
  const chosenTab = $('#tab-' + tab)
  chosenTab.css('display', 'block')

  chosenTab.addClass('tab-active')

  if (tab != 'summary' && tab != 'procedure') {
    loadPatientFiles(tab)
  }
}

function loadPatientFiles(tab) {
  console.log("funcion: loadPatientFiles recibiendo:", tab )
  Swal.fire({
    title: 'Cargando...',
    allowOutsideClick: false,
    showConfirmButton: false,
  })

  $.ajax({
    data: {
      lead_id: lead_id,
      type: tab,
      clinic: 'CDMX',
    },
    dataType: 'json',
    method: 'POST',
    url: 'scripts/sales/load_patient_files.php',
  })
  .done(function (response) {
    console.log("mostrandoooresponse de load_patient_files.php ", response)
    
    $('.inputfile-container').fadeIn('slow')

    
    //$("#file" + tab).fileinput("destroy");
    $('#file' + tab)
      .off(
        'filebatchuploadcomplete filebatchpreupload fileuploaded filepredelete'
      )
      .fileinput('destroy')
    $('#file' + tab)
      .fileinput({
        allowedFileExtensions: ['jpg', 'png', 'jpeg', 'docx', 'doc', 'pdf'],
        language: 'es',
        uploadUrl: `scripts/sales/upload_patient_files.php?lead_id=${lead_id}&type=${tab}`,
        showRemove: false,
        showCancel: false,
        initialPreview: response.initialPreview,
        initialPreviewConfig: response.initialPreviewConfig,
        initialPreviewAsData: true,
        overwriteInitial: false,
        showDownload: true,
      }) .on('fileuploaded', function(event, previewId, index, fileId) {
        console.log("si jala");
        loadPatientFiles(tab); // Llamar a la función para recargar los archivos y ver la miniatura
      })
      .on('filepredelete', function (event, key, jqXHR, data) {
        var abort = true
        if (
          confirm('El archivo se borrará permanentemente, ¿estás seguro/a?')
        ) {
          abort = false
        }
        return abort
      })
    $('.kv-file-rotate,.file-drag-handle,.kv-file-upload').css(
      'display',
      'none'
    )
    $(document).on('click', '.kv-file-zoom', function() {
      console.log("iamgen ")
      // Obtener el elemento padre más cercano con la clase .file-preview-frame
      var parentElement = $(this).closest('.file-preview-frame');

      // Buscar el elemento de la imagen dentro del elemento padre
      var imageUrl = parentElement.find('img').attr('src');

      console.log(imageUrl);

      //	$('.file-zoom-detail').attr('src', imageUrl);


      // nuevo codigo

      // Modificar la URL para eliminar "thumb"
      var modifiedUrl = imageUrl.replace('/thumb/', '/');

      console.log(modifiedUrl);

      // Actualizar el atributo src de .file-zoom-detail con la URL modificada
      $('.file-zoom-detail').attr('src', modifiedUrl);


    });
  })
    .fail(function (response) {
      console.log("error load_patient_files.php",response.responseText)
      showSweetAlert()
    })
    .always(function () {
      Swal.close()
    })
}

function fillSummaryInfo() {
  console.log("profile: ",profile)
  $('#i_first_name').val(profile.first_name)
  $('#i_last_name').val(profile.last_name)
  $('#i_clinic').val(profile.clinic)
  $('#g_Hombre').prop('checked', true)
  $('#i_phone_1').val(profile.phone)
  $('#i_assessment_date').val(profile.assessment_date)
  $('#i_first_meet_type').val(profile.first_meet_type)
  $('#i_assessment_type').val(profile.assessment_type)
  $('#i_closer').val(profile.closer)
  $('#i_procedure_date').val(profile.procedure_date)
  $('#i_procedure_type').val(profile.procedure_type)
  $('#i_purpose').val(profile.notes)
  $('#i_quoted_cash_amount').val(profile.quoted_cash_amount)
  $('#i_quoted_cc_amount').val(profile.quoted_cc_amount)
  $('#i_installments').val(profile.installments)
}

$(document).ready(function (e) {
  // Update Patient Profile

  $('#update_summary').submit(function (e) {
    e.preventDefault()
    if (this.checkValidity()) {
      const formData = $(this).serialize()
      const action = $(this).attr('action')
      const method = $(this).attr('method')
      $.ajax({
        data: formData,
        cache: false,
        dataType: 'json',
        method: method,
        url: action,
      })
        .done(function (response) {
          console.log(response)
          if (response.success) {
            showSweetAlert(
              'Listo!',
              response.message,
              'success',
              1500,
              true,
              false
            )
          } else {
            showSweetAlert(
              'Error',
              response.message,
              'error',
              1800,
              true,
              false
            )
          }
        })
        .fail(function (response) {
          console.log(response)
          showSweetAlert()
        })
    }
  })

  // View Procedure Payment.

  $(document).on('click', '#procedure-payment', async function (e) {
    let [infoCompleted, message_2] = await verifyInfoCompleted('anticipo')
    error_message = !infoCompleted ? message_2 : true
    if (error_message === true) {
      loadProcedurePayment()
    } else {
      showSweetAlert('Error', error_message, 'error', 2500, true, false)
    }
  })

  // Create new receipt

  $('.widget .nav-link').click(async function (e) {
    e.preventDefault()
    const tab = $(this).data('tab')
    console.log("object")
    let error_message

    let [infoPayment, message_1] = await verifyInfoPayment(tab)
    let [infoCompleted, message_2] = await verifyInfoCompleted(tab)

    error_message = !infoCompleted ? message_2 : true
    error_message = infoPayment ? message_1 : error_message
    console.log(error_message)
    if (error_message === true) {
      const url_form = `templates/invoices_forms/form_${tab}.php`
      setProfileInfo(tab, url_form)
    } else {
      showSweetAlert('Error', error_message, 'error', 2500, true, false)
    }

    $('#divInvoicePdf').fadeOut('slow')
  })

  // Load receipts
  $(document).on('click', 'li[data-tab="payments"]', function (e) {
    loadAllReceipts()
  })

  // Delete receipt

  $(document).on('click', '.delete_receipt', function (e) {
    const invoice_id = $(this).data('id')

    Swal.fire({
      title: '¿Estás seguro/a?',
      text: 'Esta acción no se puede deshacer',
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          data: {
            invoice_id: invoice_id,
          },
          cache: false,
          dataType: 'json',
          method: 'POST',
          url: 'scripts/sales/delete_patient_receipt.php',
        })
          .done(function (response) {
            if (response.success) {
              paymentsTable.ajax.reload()
              showSweetAlert('Listo!', response.message, 'success')
            } else {
              console.log(response)
              showSweetAlert('Error', response.message, 'error')
            }
          })
          .fail(function (response) {
            console.error(response)
            showSweetAlert('Error', response.message, 'error')
          })
      }
    })
  })
})

async function verifyInfoCompleted(tab) {
  if (tab == 'producto' || tab == 'tratamiento') {
    return [true, '']
  } else {
    try {
      const response = await $.ajax({
        data: {
          lead_id: lead_id,
        },
        cache: false,
        dataType: 'json',
        method: 'POST',
        url: 'scripts/sales/verify_info_completed.php',
      })

      if (response.success) {
        return [response.exist, response.message]
      } else {
        throw new Error(response.message)
      }
    } catch (error) {
      showSweetAlert('Error', error.message, 'error', 2300, true, false)
      console.error('Ocurrió un error:', error)
      throw error
    }
  }
}

function loadProcedurePayment() {
  $.ajax({
    data: {
      lead_id: lead_id,
    },
    cache: false,
    dataType: 'json',
    method: 'POST',
    url: 'scripts/sales/load_procedure_payment.php',
  })
    .done(function (response) {
      console.log(response)
      if (response.success) {
        let total_paid_amount = 0
        let quoted_cash_amount = 0
        let quoted_cc_amount = 0
        let payments = ''

        quoted_cash_amount = parseFloat(
          response.info_payment.quoted_cash_amount
        )
        quoted_cc_amount = parseFloat(response.info_payment.quoted_cc_amount)

        response.payments.forEach(function (payment) {
          const amount = parseFloat(payment.amount)

          const parsed_amount = amount.toLocaleString('es-MX', {
            style: 'currency',
            currency: 'MXN',
          })

          const receipt_type =
            payment.type.charAt(0).toUpperCase() + payment.type.slice(1)
          payments += `<tr>
                                    <td>${payment.date}</td>
                                    <td>${receipt_type}</td>
                                    <td class="add">${parsed_amount}</td>
                                </tr>`

          total_paid_amount += parseFloat(amount)
        })

        let pending_cc_amount = quoted_cc_amount - total_paid_amount
        let pending_cash_amount = quoted_cash_amount - total_paid_amount

        total_paid_amount = total_paid_amount.toLocaleString('es-MX', {
          style: 'currency',
          currency: 'MXN',
        })

        quoted_cash_amount = quoted_cash_amount.toLocaleString('es-MX', {
          style: 'currency',
          currency: 'MXN',
        })

        quoted_cc_amount = quoted_cc_amount.toLocaleString('es-MX', {
          style: 'currency',
          currency: 'MXN',
        })

        pending_cc_amount = pending_cc_amount.toLocaleString('es-MX', {
          style: 'currency',
          currency: 'MXN',
        })

        pending_cash_amount = pending_cash_amount.toLocaleString('es-MX', {
          style: 'currency',
          currency: 'MXN',
        })

        $('#tbody_payments_table').html(payments)
        $('#quoted_cc_amount').html(quoted_cc_amount)
        $('#quoted_cash_amount').html(quoted_cash_amount)
        $('#installments').html(response.info_payment.installments)
        $('#h2_pending_amount').html(`
                    ${pending_cc_amount}
                    <span>Resta por pagar en tarjeta</span>
                    <span class="text-dark">
                        <strong>${pending_cash_amount} si paga en efectivo</strong>
                    </span>`)
        /*$("#p_collection_notes").val(response.collection_notes);*/
      } else {
        showSweetAlert()
      }
    })
    .fail(function (response) {
      console.log(response.responseText)
      showSweetAlert()
    })
}

function loadAllReceipts() {
  DataTable.datetime('DD/MM/YYYY')
  $('#paymentsTable').DataTable().destroy()
  paymentsTable = $('#paymentsTable').DataTable({
    ajax: {
      url: `scripts/sales/load_lead_receipts.php`,
      type: 'POST',
      data: function (d) {
        d.lead_id = lead_id
      },
    },
    autoWidth: false,
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json',
    },
    scrollX: true,
    order: [[2, 'desc']],
  })
}

async function verifyInfoPayment(type) {
  if (type == 'producto' || type == 'tratamiento' || type == 'abono') {
    return [false, false]
  } else {
    try {
      const response = await $.ajax({
        data: {
          lead_id: lead_id,
          type: type,
        },
        cache: false,
        dataType: 'json',
        method: 'POST',
        url: 'scripts/sales/verify_payment.php',
      })
      if (response.success) {
        console.log(response)
        return [response.exist, response.message]
      } else {
        console.log(response)
        showSweetAlert('Error', response.message, 'error')
        throw new Error(response)
      }
    } catch (error) {
      showSweetAlert('Error', error.message, 'error', 2300, true, false)
      console.error('Ocurrió un error:', error)
      throw error
    }
  }
}

function setProfileInfo(tab, url_form) {
  console.log(url_form)
  $('#formContainer').load(url_form, function () {
    $(this).hide().fadeIn(1000) // Efecto de fadeIn para el formulario
  })
}
