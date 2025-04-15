<?php

$months = [
    '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
];


function spanishWeekDay() {
    // Si el día de mañana es domingo, entonces cambia al lunes
    $weekday = (date("l", strtotime("+1 day")) === "Sunday") ? date("l", strtotime("+2 day")) : date("l", strtotime("+1 day"));

    // Mapeo de días en inglés a español
    $weekdayMapping = array(
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    );

    // Retorna el día en español
    return isset($weekdayMapping[$weekday]) ? $weekdayMapping[$weekday] : $weekday;
}

$dayLabel = (date("l", strtotime("+1 day")) === "Sunday") ? "del" : "de mañana";
$nextDay = spanishWeekDay(true);


function spanishToday() {
    // Obtener el día de hoy
    $weekday = date("l");

    // Mapeo de los días de la semana en inglés a español
    $weekdayMapping = array(
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    );

    // Retornar el nombre del día de hoy en español
    return isset($weekdayMapping[$weekday]) ? $weekdayMapping[$weekday] : $weekday;
}