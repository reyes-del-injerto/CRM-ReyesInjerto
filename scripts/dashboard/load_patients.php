<?php

ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once "../common/utilities.php";
require_once "../common/connection_db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $clinic = isset($_POST['clinic']) ? $_POST['clinic'] : 'Santa Fe';
        $date = date("Y-m-d");
        $string_date = date('d ') . $months[date('m')];

        // Construye la consulta SQL condicionalmente
        $sql = "SELECT DISTINCT
                    leads.id AS lead_id, 
                    CONCAT(sla.first_name, ' ', sla.last_name) AS name, 
                    sla.procedure_date, 
                    sla.procedure_type, 
                    ep.id AS procedure_id,
                    ep.num_med_record, 
                    ep.room, 
                    ep.specialist,
                    sla.status,
                    sla.enfermedades
                FROM sa_leads AS leads 
                INNER JOIN sa_leads_assessment sla ON leads.id = sla.lead_id 
                LEFT JOIN enf_procedures AS ep ON leads.id = ep.lead_id 
                WHERE sla.procedure_date = ? 
                  AND sla.status = 1";

        // Agrega el filtro de clínica solo si clinic es 'Queretaro'
        if ($clinic === 'Queretaro') {
            $sql .= " AND sla.clinic = ?";
        } else {
            $sql .= " AND sla.clinic != 'Queretaro'";
        }

        // Prepara la consulta
        $stmt = $conn->prepare($sql);

        // Asigna parámetros según corresponda
        if ($clinic === 'Queretaro') {
            $stmt->bind_param("ss", $date, $clinic); // Fecha y clínica
        } else {
            $stmt->bind_param("s", $date); // Solo fecha
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            throw new Exception("No hay procedimientos programados para hoy en la clínica seleccionada");
        }

        $cards = [];
        while ($row = $result->fetch_object()) {
            $cards[] = createCard($string_date, $row);
        }
        echo json_encode(["success" => true, "cards" => $cards]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}

function createCard($string_date, $row)
{
    $actions = '';

    if ($row->procedure_id === null) {
        $actions = "<span><strong>No se ha asignado número de expediente.</strong></span>";
    } elseif ($row->room == 0) {
        $actions = "<span><strong>No se ha asignado sala/especialista</strong></span>";
    } else {
        $actions = "<a type='button' class='btn btn-primary' href='view_photos.php?px=1&type=procedure&id={$row->lead_id}'>Enviar fotos</a>
                    <a type='button' class='btn btn-warning' href='view_notifications.php?id={$row->lead_id}'>Enviar notificación</a>";
    }

    $num_med_record_display = $row->num_med_record ? "#{$row->num_med_record}" : '<br> No asignado';

    $card = "<div class='blog-slider__item swiper-slide'>
          <div class='blog-slider__img'>
              <img src='assets/img/profile.jpg' alt='' />
          </div>
          <div class='blog-slider__content'>
              <span class='blog-slider__code'>Procedimientos del : {$string_date}</span>
              <div class='blog-slider__title'>{$row->name} {$num_med_record_display}</div>
              <div class='blog-slider__text'>
                  Tipo: {$row->procedure_type}<br>
                  Enfermedades: {$row->enfermedades}<br>
                  Sala: {$row->room}<br>
                  Especialista: {$row->specialist}<br>
              </div>
              {$actions}
          </div>
      </div>";

    return $card;
}
