<div class="sidebar" id="sidebar">
	<div class="sidebar-inner slimscroll">
		<div id="sidebar-menu" class="sidebar-menu">
			<ul>
				<li class="menu-title">Los Reyes del Injerto</li>
				<li>
					<a href="index.php"><span class="menu-side"><img src="assets/img/icons/menu-icon-01.svg" alt=""></span> <span>Inicio</span></a>
				</li>
				<li id="adminMenu">
					<a href="admin.php"><span class="menu-side"><img src="assets/img/svg/admin.svg" alt=""></span> <span>Administración</span></a>
				</li>

				<li id="adminInventary">
					<a href="inventarios.php"><span class="menu-side"><img src="assets/img/svg/stock.svg" alt=""></span> <span>Inventarios</span></a>
				</li>
				<li class="submenu">
					<a href="#"><i class="fa fa-calendar"></i> <span> Agenda </span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li><a href="calendar.php?clinic=Santafe">Santa Fé</a></li>
						<li><a href="calendar.php?clinic=Pedregal">Pedregal</a></li>
						<li><a href="calendar.php?clinic=Queretaro">Queretaro</a></li>
					</ul>
				</li>
				<li class="submenu">
					<a href="#"><span class="menu-side"><img src="assets/img/icons/menu-icon-02.svg" alt=""></span> <span> Proced.</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li><a href="view_procedures.php">Injerto</a></li>
						<li><a href="view_treatments.php">Tratamiento</a></li>
						<!-- <li><a href="view_protocol.php">Protocolo Dutasteride</a></li> -->
						<!-- <li><a href="design.php">Hoja de Diseño</a></li> -->
						<!-- <li><a href="#">Nueva órden de Inventario</a></li> -->
					</ul>
				</li>
				<li>
					<a href="view_clients.php"><span class="menu-side"><img src="assets/img/icons/menu-icon-01.svg" alt=""></span> <span>Clientes</span></a>
				</li>
				<li class="submenu">
					<a href="#"><span class="menu-side"><img src="assets/img/icons/menu-icon-09.svg" alt=""></span> <span> Ventas </span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li><a href="new_lead.php">Nuevo Lead</a></li>
						<li><a href="view_leads.php">Ver Leads</a></li>
					</ul>
				</li>
				<script>

				</script>
				<li class="submenu">
					<a href="#"><span class="menu-side"><img src="assets/img/icons/menu-icon-07.svg" alt=""></span> <span> Finanzas </span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li><a href="view_transactions.php">Gastos</a></li>
						<li><a href="view_transactions_daily.php">Cortes diarios</a></li>
						<li><a href="view_budget.php?clinic=" id="budget-link">Presupuestos</a></li>
						<li><a href="cash_closing.php">Corte de Caja</a></li>
						<!-- <li><a href="#">Métodos de Pago</a></li> -->
						<li><a href="layout.php">Layout BBVA</a></li>

					</ul>
				</li>
				<li>
					<a href="view_holidays.php"><span class="menu-side"><img src="assets/img/icons/menu-icon-02.svg" alt=""></span> <span>Vacaciones</span></a>
				</li>
				<li class="submenu">
					<a href="#"><span class="menu-side"><img src="assets/img/icons/menu-icon-10.svg" alt=""></span> <span> Marketing </span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li><a href="view_log.php">Ver Registro de Actividad</a></li>
						<li><a href="tracking_px.php">Seguimiento de px</a></li>
						<li><a href="plantilla_diseno.php">Diseño de plantilla</a></li>
					</ul>
				</li>
				<li class="submenu">
					<a href="#"><span class="menu-side"><img src="assets/img/icons/menu-icon-08.svg" alt=""></span> <span> Usuarios </span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li><a href="new_user.php">Añadir Usuario</a></li>
						<li><a href="view_users.php">Ver o Editar Usuarios</a></li>
						<li><a href="view_permissions.php">Administrar Roles y Permisos</a></li>
					</ul>
				</li>

			</ul>
			<div class="logout-btn">
				<a href="scripts/auth/logout.php"><span class="menu-side"><img src="assets/img/icons/logout.svg" alt=""></span> <span>Cerrar Sesión</span></a>
			</div>
		</div>
	</div>
</div>

<script>
	// Obtener el user_id de localStorage
	const userId = localStorage.getItem('user_id');
	var clinica = localStorage.getItem('clinica'); // Recupera el valor original
	console.log("Clinica original:", clinica); // Muestra el valor original
	clinica = clinica.replace(/\s+/g, ''); // Elimina todos los espacios, incluidos los del medio
	console.log("Clinica pa enviar en budget:", clinica); // Muestra el valor procesado
	document.getElementById('budget-link').href = `view_budget.php?clinic=${clinica}`;

	document.getElementById('budget-link').href = `view_budget.php?clinic=${clinica}`;

	// Verificar si el user_id es 1
	if (userId === '1' || userId === '20' || userId === '7' || userId === '41'|| userId === '18' || userId === '11' ) {
		// Mostrar el elemento <li> si user_id es 1
		document.getElementById('adminMenu').style.display = 'block';
		document.getElementById('adminInventary').style.display = 'block';
	} else {
		// Ocultar el elemento <li> si user_id no es 1
		document.getElementById('adminMenu').style.display = 'none';
		document.getElementById('adminInventary').style.display = 'none';
	}
</script>