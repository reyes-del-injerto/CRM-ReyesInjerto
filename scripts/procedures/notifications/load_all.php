<?php

//require_once "scripts/connection_db.php";
require_once __DIR__ . "/../../common/connection_db.php";

header('Content-Type: application/json'); // Asegúrate de que el contenido sea JSON

$px_sales_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$procedure_type = isset($_GET['procedure_type']) ? intval($_GET['procedure_type']) : 0;

// Mapear los tipos de procedimientos a descripciones
$procedureDescriptions = [
    1 => 'procedimiento',
    2 => '2do procedimiento',
    3 => 'micro'
];

// Inicializamos el array para el JSON de respuesta
$response = [
    'success' => false,
    'message' => '',
    'data' => [],
    'debug' => [
        'received_data' => [
            'px_sales_id' => $px_sales_id,
            'user_id' => $user_id,
            'procedure_type' => $procedure_type
        ],
        'sql_query' => '',
        'num_rows' => 0
    ]
];

// Consulta para obtener los datos del procedimiento
$sql = "SELECT sla.lead_id, ep.num_med_record, ep.room, ep.specialist, ep.notes, 
            CONCAT(sla.first_name, ' ', sla.last_name) AS name, 
            DATE_FORMAT(sla.procedure_date, '%d/%m/%Y') AS procedure_date, 
            sla.procedure_type 
            
            FROM enf_procedures ep 
            INNER JOIN sa_leads_assessment sla ON ep.lead_id = sla.lead_id 
            WHERE ep.lead_id = $px_sales_id AND sla.status != 0;";

$query = mysqli_query($conn, $sql);

$response['debug']['sql_query'] = $sql;

if (mysqli_num_rows($query) > 0) {
    $notificationsFound = false;
    while ($row = mysqli_fetch_assoc($query)) {
        $num_med_record = $row['num_med_record'];
        $patient_name = $row['name'];
        $specialist = $row['specialist'];
        $room = $row['room'];
        
        $sql = "SELECT * FROM notifications WHERE lead_id = $px_sales_id AND procedure_type = $procedure_type ORDER BY datetime DESC;";
        $notification_query = mysqli_query($conn, $sql);

        $response['debug']['sql_query'] = $sql;
        $response['debug']['num_rows'] = mysqli_num_rows($notification_query);

        if (mysqli_num_rows($notification_query) > 0) {
            $notificationsFound = true;
            while ($row = mysqli_fetch_assoc($notification_query)) {
                $img_data = "";
                $uploaded_datetime = $row['uploaded_datetime'];
                $ext_image = $row['ext_image'];

                if ($ext_image) {
                    $img_url_local = "https://lrdi.online/CDMX3/temporal_storage/rdi-enf-cdmx/{$num_med_record}/proced/{$uploaded_datetime}.{$ext_image}";
                    $img_url = $img_url_local;
                    $img_data = "
                        <ul class='nav activity-sub-list mt-2'>
                            <li><img class='img-fluid' width=400px; src='{$img_url}'></li>
                        </ul>";
                }

                $datetimeObj = new DateTime($row['datetime']);
                $message_id = $row['telegram_msg_id'];
                $message = $row['message'];
                $date = $datetimeObj->format('d-m-Y');
                $time = $datetimeObj->format('H:i A');
                $procedure_title = "";
                if ($procedure_type==1){
                 $procedure_title = "Notif. de primer procedimiento";
                }elseif ($procedure_type=="2"){
                    $procedure_title = "Notif. de segundo procedimiento";
                }elseif ($procedure_type=="3"){
                    $procedure_title = "Notif. de Micro ";
                }else{
                    $procedure_title = "Notif sin asignar";
                }

                $delButton = ($user_id == 15 || $user_id == 1) ? 
                    "<br> <br><button class='btn btn-xs btn-danger delNotif' data-pxid={$px_sales_id} data-room={$room} data-messageid={$message_id}><i class='fa fa-times'></i></button>" : '';

                $notification = "
               
             
                <li class='text-left'>
                    <div class='activity-user'>
                        <a href='profile.html' title='RDI' data-bs-toggle='tooltip' class='avatsar'>
                            <img alt='RDI' src='../../../assets/img/leon-footer.webp' class='img-fluid rounded-circle'>
                        </a>
                    </div>
                    <div class='activity-content timeline-group-blk'>
                        <div class='timeline-group flex-shrink-0'>
                            <h4>{$time}</h4>
                            <span class='time'>{$date}</span>
                        </div>
                        <div class='comman-activitys flex-grow-1 '>
                            <h4 style='font-size:17px;color:#000;'>{$message}</h4>
                            {$img_data}
                            <span class='badge bg-success'>{$procedure_title}</span>
                            {$delButton}
                            
                        </div>
                    </div>
                </li>";

                $response['data'][] = $notification;
            }
            $response['success'] = true;
            $response['message'] = 'Notificaciones encontradas';
        }
    }
    if (!$notificationsFound) {
        // Determinar la descripción del tipo de procedimiento
        $procedureDescription = isset($procedureDescriptions[$procedure_type]) ? $procedureDescriptions[$procedure_type] : 'desconocido';
        $response['message'] = "Sin notificaciones de: {$procedureDescription}.";
    }
} else {
    $response['message'] = 'No se encontró información del procedimiento';
}

// Enviar la respuesta JSON
echo json_encode($response);

?>
