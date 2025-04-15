<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

session_start();

if (isset($_SESSION['user_name'])) {
  header('Location: index.php');
  exit();
}
?>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <title>Iniciar Sesión | ERP | Los Reyes del Injerto</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Sweet Alert -->
  <link rel="stylesheet" href="assets/plugins/sweetalert/sweetalert2.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/login-style.css" />

  <style>
    /* Estilos comunes para mobileBadge y DesktopBadge */
.badgeH {
    display: block;
    background-color: #4CAF50;
    color: white;
    border-radius: 12px;
    font-size: 14px;
    font-weight: bold;
    padding: .4rem;
    text-align: center;
}

/* Específicos para contenedorbadge */
#contenedorbadge {
    display: none;
}

.rememberme{
  display: none;
}

@media (max-width: 637px) {
    #contenedorbadge {
        min-width: 200px;
        display: flex;
        justify-content: space-evenly;
        align-items: stretch;
        padding: 0;
        margin-top: 1.2rem;
    }
}

</style>
</head>

<body>
  <section>
    <div class="form-box">
      <div class="form-value">
        <form method="POST" id="login_form" action="scripts/auth/login.php">
          <h2>Login</h2>
          <div id="DesktopBadge" class="badgeH">Nuevo CRM</div>
          <div class="inputbox">
            <ion-icon name="person-outline"></ion-icon>
            <input type="text" name="username" id="username" required />
            <label for="">Usuario</label>
          </div>
          <div class="inputbox">
            <ion-icon name="lock-closed-outline"></ion-icon>
            <input type="password" name="password" id="password" required />
            <label for="">Password</label>
          </div>
          <div class="rememberme">
            <label for=""> <input type="checkbox" value="rememberme" name="rememberme" id="rememberme" checked readonly />Recordarme</label>
          </div>
          <button type="submit">Entrar</button>
        </form>
      </div>
    </div>
  </section>

  <!-- Ionic Icons -->
  <script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <!-- JQuery -->
  <script src="assets/js/jquery.min.js"></script>
  <!--  Sweet Alert -->
  <script src="assets/plugins/sweetalert/sweetalert.11.10.min.js"></script>

  <script>
    $(document).ready(function() {
      $("#login_form").submit(function(e) {
        e.preventDefault();

        if (this.checkValidity()) {
          const action = $(this).attr('action');
          const method = $(this).attr('method');
          const data = $(this).serialize();

          $.ajax({
              url: action,
              method: method,
              data: data,
              dataType: "json",
            })
            .done(function(response) {
              console.log(response)
              if (response.success) {
                console.log("response login true",response)
                localStorage.setItem('user_id', response.user_id);
                localStorage.setItem('user_name', response.user_name);
                localStorage.setItem('clinica', response.clinica);
                localStorage.setItem('department', response.user_department);
                showSweetAlert("Hola!", "👑 Bienvenido 🦁", "success", 1800, false, false).then(function() {
                location.reload();
               
                });
              } else {
                showSweetAlert("Error", response.message, "error", 1400, true, false);
              }
            })
            .fail(function(response) {
              console.log(response);
              showSweetAlert();
            });
        }
      });
    });

    function showSweetAlert(title, text, icon, timer, timerProgressBar, showConfirmButton) {
      return Swal.fire({
        title: title || "Error",
        text: text || "Contacta a administración",
        icon: icon || "error",
        timer: timer || 2500,
        timerProgressBar: timerProgressBar || true,
        showConfirmButton: showConfirmButton || false,
      });
    }
  </script>
</body>

</html>