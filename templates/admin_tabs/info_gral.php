<div id="titles">
    <div id="period" class="dropdown">
        <form id="dateForm">
            <label for="start_date">Fecha de inicio:</label>
            <input type="date" id="start_date" name="start_date">

            <label for="end_date">Fecha de fin:</label>
            <input type="date" id="end_date" name="end_date">

            <button class="btn btn-primary" id="submit_dates">Aplicar</button>
        </form>
    </div>

    <h4 id="period-title">
        <!-- Título del periodo vacío -->
    </h4>
</div>

<div class="contenedor_principal">
    <div class="card_gral">
        <h4>Leads agregados</h4>
        <div class="medium">
            <p>0</p>
            <img src="./assets/img/lead_preview.png" alt="Imagen de lead">
        </div>
        <div class="card_footer">
            <p><span>Sin datos</span> Respecto al anterior periodo</p>
        </div>
    </div>

    <div class="card_gral">
        <h4>Valoraciones realizadas</h4>
        <div class="medium">
            <p>0</p>
            <img src="./assets/img/lead_preview.png" alt="Imagen de valoración">
        </div>
        <div class="card_footer">
            <p><span>Sin datos</span> Respecto al anterior periodo</p>
        </div>
    </div>

    <div class="card_gral">
        <h4>Cierres realizados</h4>
        <div class="medium">
            <p>0</p>
            <img src="./assets/img/lead_preview.png" alt="Imagen de cierre">
        </div>
        <div class="card_footer">
            <p><span>Sin datos</span> Respecto al anterior periodo</p>
        </div>
    </div>
</div>

<div class="contendor_sub_info">

    <div class="card_gral">

        <h5 class="leads_titulo">Informacion gral</h5>

        <h5 class="leads_titulo">Leads asignados</h5>

        <div class="vendedoras">
            <div>
                <h4>Adriana Silva</h4>

            </div>
            <p class="lead_amount">0</p>
        </div>
        <div class="vendedoras">
            <div>
                <h4>Janeth Ruiz</h4>

            </div>
            <p class="lead_amount">0</p>
        </div>
        <div class="vendedoras">
            <div>
                <h4>Marisol Olmos</h4>

            </div>
            <p class="lead_amount">0</p>
        </div>

        <h5 class="leads_titulo">Valoraciones agendadas</h5>

        <div class="info_valoraciones">
            <div class="vendedoras">
                <div>
                    <h4>Adriana Silva</h4>

                </div>
                <p class="valoraciones_amount">0</p>
            </div>
            <div class="vendedoras">
                <div>
                    <h4>Janeth Ruiz</h4>

                </div>
                <p class="valoraciones_amount">0</p>
            </div>
            <div class="vendedoras">
                <div>
                    <h4>Marisol Olmos</h4>

                </div>
                <p class="valoraciones_amount">0</p>
            </div>
        </div>

    </div>

    <div class="contenedor_grafica">
        <canvas id="myChart"></canvas>
    </div>

    <div class="options">
        <div class="card_gral" id="by_clinic">

            <h4>Valoraciones por clínica</h4>

            <div class="row_style">
                <h5>Santa Fe</h5>
                <p>0</p>
            </div>
            <div class="row_style">
                <h5>Pedregal</h5>
                <p>0</p>
            </div>
            <div class="row_style">
                <h5>Queretaro</h5>
                <p>0</p>
            </div>



        </div>

        <h4>Interesados en:</h4>
        <div class="mainoptions" id="forgrid">
            <div class="card_gral">
                <img src="./assets/img/beard.png" alt="">
                <div class="minioptions">
                    <h4>0</h4>
                    <span>Barba</span>
                </div>
            </div>

            <div class="card_gral">
                <img src="./assets/img/beard.png" alt="">
                <div class="minioptions">
                    <h4>0</h4>
                    <span>Capilar</span>
                </div>
            </div>
            <div class="card_gral">
                <img src="./assets/img/beard.png" alt="">
                <div class="minioptions">
                    <h4>0</h4>
                    <span>Ambos</span>
                </div>
            </div>
            <div class="card_gral">
                <img src="./assets/img/beard.png" alt="">
                <div class="minioptions">
                    <h4>0</h4>
                    <span>Micro</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="conteiner_table">






</div>





<script>
    var myChart = null; // Variable para almacenar la instancia del gráfico
    function setDefaultDates() {
        // Obtener el primer día del mes actual
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);

        // Obtener el último día del mes actual
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

        // Convertir a formato de fecha (por ejemplo, 'YYYY-MM-DD')
        const start_date = firstDay.toISOString().split('T')[0]; // '2024-11-01'
        const end_date = lastDay.toISOString().split('T')[0]; // '2024-11-30'

        // Asignar las fechas calculadas a los inputs de fecha
        document.getElementById('start_date').value = start_date;
        document.getElementById('end_date').value = end_date;

        // Llamar a la función load_dataAdmin con las fechas predeterminadas
        load_dataAdmin(start_date, end_date);
    }

    // Llamar a setDefaultDates al cargar la página

    setDefaultDates();




    document.getElementById("dateForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Previene el envío del formulario y la recarga de la página

        // Obtén los valores de las fechas
        const startDate = document.getElementById("start_date").value;
        const endDate = document.getElementById("end_date").value;

        // Imprime las fechas en la consola
        console.log("Fecha de inicio:", startDate);
        console.log("Fecha de fin:", endDate);

        // Llama a la función AJAX con las fechas seleccionadas
        load_dataAdmin(startDate, endDate);
    });

    function showChart(data, tags) {
        console.log("Mostrando gráfico");
        const ctx = document.getElementById('myChart').getContext('2d');

        // Crear nuevo gráfico
        myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: tags,
                datasets: [{
                    label: '# leads',
                    data: data,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function load_dataAdmin(start_date, end_date) {
        $.ajax({
            url: './scripts/Admin/panel/data_admin.php',
            method: 'POST',
            data: {
                first_day_of_period: start_date + ' 00:00:00',
                last_day_of_period: end_date + ' 23:59:59'
            },
            dataType: 'json',
            success: function(response) {
                if (response) {
                    // Destruir el gráfico existente si lo hay
                    if (myChart) {
                        myChart.destroy();
                    }

                    showChart(response.etapas.data, response.etapas.tags);

                    // Actualizar el título del periodo
                    document.getElementById("period-title").innerText = `Periodo: ${response.startDate} a ${response.endDate}`;

                    // Actualizar los contadores en las tarjetas principales
                    document.querySelector(".card_gral:nth-child(1) .medium p").innerText = response.total_leads;
                    document.querySelector(".card_gral:nth-child(2) .medium p").innerText = response.total_valoraciones;
                    document.querySelector(".card_gral:nth-child(3) .medium p").innerText = response.total_cierres;

                    // Inyectar valores en las tarjetas "Barba", "Capilar", "Ambos", "Micro"
                    document.querySelector(".mainoptions .card_gral:nth-child(1) .minioptions h4").innerText = response.total_barba || 0;
                    document.querySelector(".mainoptions .card_gral:nth-child(2) .minioptions h4").innerText = response.total_capilar || 0;
                    document.querySelector(".mainoptions .card_gral:nth-child(3) .minioptions h4").innerText = response.total_ambos || 0;
                    document.querySelector(".mainoptions .card_gral:nth-child(4) .minioptions h4").innerText = response.total_micro || 0;

                    // Asignar total_leads a cada vendedora
                    if (response.vendedoras && response.vendedoras.length > 0) {

                        response.vendedoras.forEach(function(vendedora) {
                            console.log("por vend", vendedora.leads_val);

                            // Buscar todos los elementos con la clase 'valoraciones_amount' y asignarles el valor de 'leads_valoracion'
                            const valoracionesElements = document.getElementsByClassName("valoraciones_amount");

                            // Buscar el índice correspondiente a la vendedora, para asignarle su valor
                            const vendedoraName = vendedora.seller;

                            for (let i = 0; i < valoracionesElements.length; i++) {
                                const element = valoracionesElements[i];
                                // Si el nombre de la vendedora coincide con el título del h4 dentro del elemento
                                if (element.previousElementSibling && element.previousElementSibling.innerText === vendedoraName) {
                                    element.innerText = vendedora.leads_val; // Asigna el valor de leads_valoracion
                                }
                            }

                            const totalLeads = vendedora.total_leads;

                            // Buscar el div correspondiente a la vendedora por su nombre y asignar el total_leads
                            const vendedoraDiv = Array.from(document.querySelectorAll('.vendedoras')).find(function(item) {
                                return item.querySelector('h4').innerText === vendedoraName;
                            });

                            if (vendedoraDiv) {
                                const leadAmountElement = vendedoraDiv.querySelector('.lead_amount');
                                if (leadAmountElement) {
                                    leadAmountElement.innerText = totalLeads;
                                }
                            }
                        });

                    }

                    // Actualizar la sección de Leads por Vendedoras
                    const leadsContainer = document.querySelector(".conteiner_table");
                    leadsContainer.innerHTML = `<h3>Informacion por vendedoras:</h3>`; // Título de la sección

                    if (response.vendedoras && response.vendedoras.length > 0) {
                        response.vendedoras.forEach(function(vendedora) {
                            // Generar solo las etapas que tienen datos mayores a 0
                            const etapas = [];
                            if (vendedora.leads_conversacion > 0) etapas.push(`<th>En Conversación</th><td>${vendedora.leads_conversacion}</td>`);
                            if (vendedora.leads_interesado > 0) etapas.push(`<th>Interesado</th><td>${vendedora.leads_interesado}</td>`);
                            if (vendedora.leads_seguimiento > 0) etapas.push(`<th>Seguimiento</th><td>${vendedora.leads_seguimiento}</td>`);
                            if (vendedora.leads_negociacion > 0) etapas.push(`<th>Negociación</th><td>${vendedora.leads_negociacion}</td>`);
                            if (vendedora.leads_pre_proced > 0) etapas.push(`<th>Pre Procedimiento</th><td>${vendedora.leads_pre_proced}</td>`);
                            if (vendedora.leads_comparando > 0) etapas.push(`<th>Comparando</th><td>${vendedora.leads_comparando}</td>`);
                            if (vendedora.leads_fuera > 0) etapas.push(`<th>Fuera</th><td>${vendedora.leads_fuera}</td>`);

                            // Calcular la tasa de conversión
                            const tasaConversion = ((vendedora.leads_convertidos / vendedora.total_leads) * 100).toFixed(2);


                            // console.log("El contenido de etapas:", etapas);

                            // Generación de encabezados y valores de las etapas sin celdas vacías
                            const headers = etapas.map(etapa => `<th>${etapa.split('<td>')[0].replace(/<.*?>/g, '')}</th>`).join('');
                            const values = etapas.map(etapa => `<td>${etapa.split('<td>')[1] || ''}</td>`).join('');

                            // Estructura de la tabla para cada vendedora
                            leadsContainer.innerHTML += `
    <h4>Resumen de: ${vendedora.seller}</h4>
    <div class="contenedor_tabla">
        <table class="table table-hover table-bordered" cellpadding="5" cellspacing="0">
            <tbody>
                <tr>
                    <th>Leads Agregados</th>
                    <th>Leads Convertidos</th>
                    <th>Tasa de Conversión (%)</th>
                    ${headers}
                </tr>
                <tr>
                    <td>${vendedora.total_leads}</td>
                    <td>${vendedora.leads_convertidos}</td>
                    <td>${tasaConversion}%</td>
                    ${values}
                </tr>
            </tbody>
        </table>
    </div><br>
`;


                        });
                    } else {
                        leadsContainer.innerHTML += "<p>No hay leads registrados en este periodo.</p>";
                    }

                    // Actualizar valoraciones por clínica
                    const clinicInfoContainer = document.querySelector("#by_clinic");
                    clinicInfoContainer.innerHTML = "<h4>Valoraciones por clínica</h4>";

                    if (response.valoraciones && response.valoraciones.length > 0) {
                        response.valoraciones.forEach(function(clinica) {
                            const rowDiv = document.createElement("div");
                            rowDiv.classList.add("row_style");

                            rowDiv.innerHTML = `
                <h5>${clinica.clinic}</h5>
                <p>${clinica.total_valoraciones}</p>
            `;

                            clinicInfoContainer.appendChild(rowDiv);
                        });
                    } else {
                        clinicInfoContainer.innerHTML += "<p>No hay valoraciones disponibles por clínica.</p>";
                    }

                } else {
                    console.error('Error en la consulta:', response.message);
                    $('#view').html('<p>Error: ' + response.message + '</p>');
                }
            },

            error: function(xhr, status, error) {
                console.error('Error en la petición:', status, error);
                $('#view').html('<p>Error en la solicitud.</p>');
            }
        });
    }
</script>







<!-- <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20.125 4.79167H16.2917C16.0375 4.79167 15.7938 4.89264 15.614 5.07236C15.4343 5.25208 15.3334 5.49584 15.3334 5.75C15.3334 6.00417 15.4343 6.24792 15.614 6.42765C15.7938 6.60737 16.0375 6.70833 16.2917 6.70833H17.8154L13.4167 11.1071L11.2221 8.90292C11.133 8.81309 11.027 8.7418 10.9102 8.69315C10.7935 8.64449 10.6682 8.61944 10.5417 8.61944C10.4152 8.61944 10.2899 8.64449 10.1731 8.69315C10.0564 8.7418 9.95036 8.81309 9.86127 8.90292L2.19461 16.5696C2.10478 16.6587 2.03349 16.7647 1.98484 16.8814C1.93618 16.9982 1.91113 17.1235 1.91113 17.25C1.91113 17.3765 1.93618 17.5018 1.98484 17.6186C2.03349 17.7353 2.10478 17.8413 2.19461 17.9304C2.2837 18.0202 2.38969 18.0915 2.50647 18.1402C2.62325 18.1888 2.74851 18.2139 2.87502 18.2139C3.00153 18.2139 3.12679 18.1888 3.24358 18.1402C3.36036 18.0915 3.46635 18.0202 3.55544 17.9304L10.5417 10.9346L12.7363 13.1388C12.8254 13.2286 12.9314 13.2999 13.0481 13.3485C13.1649 13.3972 13.2902 13.4222 13.4167 13.4222C13.5432 13.4222 13.6685 13.3972 13.7852 13.3485C13.902 13.2999 14.008 13.2286 14.0971 13.1388L19.1667 8.05958V9.58333C19.1667 9.8375 19.2677 10.0813 19.4474 10.261C19.6271 10.4407 19.8709 10.5417 20.125 10.5417C20.3792 10.5417 20.6229 10.4407 20.8027 10.261C20.9824 10.0813 21.0834 9.8375 21.0834 9.58333V5.75C21.0834 5.49584 20.9824 5.25208 20.8027 5.07236C20.6229 4.89264 20.3792 4.79167 20.125 4.79167Z" fill="#32B139" />
            </svg> -->


<!--   <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20.125 4.79167H16.2917C16.0375 4.79167 15.7938 4.89264 15.614 5.07236C15.4343 5.25208 15.3334 5.49584 15.3334 5.75C15.3334 6.00417 15.4343 6.24792 15.614 6.42765C15.7938 6.60737 16.0375 6.70833 16.2917 6.70833H17.8154L13.4167 11.1071L11.2221 8.90292C11.133 8.81309 11.027 8.7418 10.9102 8.69315C10.7935 8.64449 10.6682 8.61944 10.5417 8.61944C10.4152 8.61944 10.2899 8.64449 10.1731 8.69315C10.0564 8.7418 9.95036 8.81309 9.86127 8.90292L2.19461 16.5696C2.10478 16.6587 2.03349 16.7647 1.98484 16.8814C1.93618 16.9982 1.91113 17.1235 1.91113 17.25C1.91113 17.3765 1.93618 17.5018 1.98484 17.6186C2.03349 17.7353 2.10478 17.8413 2.19461 17.9304C2.2837 18.0202 2.38969 18.0915 2.50647 18.1402C2.62325 18.1888 2.74851 18.2139 2.87502 18.2139C3.00153 18.2139 3.12679 18.1888 3.24358 18.1402C3.36036 18.0915 3.46635 18.0202 3.55544 17.9304L10.5417 10.9346L12.7363 13.1388C12.8254 13.2286 12.9314 13.2999 13.0481 13.3485C13.1649 13.3972 13.2902 13.4222 13.4167 13.4222C13.5432 13.4222 13.6685 13.3972 13.7852 13.3485C13.902 13.2999 14.008 13.2286 14.0971 13.1388L19.1667 8.05958V9.58333C19.1667 9.8375 19.2677 10.0813 19.4474 10.261C19.6271 10.4407 19.8709 10.5417 20.125 10.5417C20.3792 10.5417 20.6229 10.4407 20.8027 10.261C20.9824 10.0813 21.0834 9.8375 21.0834 9.58333V5.75C21.0834 5.49584 20.9824 5.25208 20.8027 5.07236C20.6229 4.89264 20.3792 4.79167 20.125 4.79167Z" fill="#32B139" />
            </svg> -->