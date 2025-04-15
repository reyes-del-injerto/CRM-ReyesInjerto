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
}

/* Específicos para contenedorbadge */
#contenedorbadge {
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
<div class="header">
    <div class="header-left" style="display:none;">
        <a href="index.php" class="logo">
            <img src="assets/img/logo.webp" width="150px" height="auto" alt="">
        </a>
    </div>
    <a id="toggle_btn" href="javascript:void(0);"><img src="assets/img/icons/bar-icon.svg" alt=""></a>
    <a id="mobile_btn" class="mobile_btn float-start" href="#sidebar"><img src="assets/img/icons/bar-icon.svg" alt=""></a>
    <div class="top-nav-search mob-view"></div>
    <ul class="nav user-menu float-end">
        <li style="align-items: center;" class="d-none d-md-flex">
            <div id="DesktopBadge" class="badgeH">Nuevo CRM</div>
            
        </li>
        <li class="nav-item dropdown has-arrow user-profile-list">
            <a href="#" class="dropdown-toggle nav-link user-link" data-bs-toggle="dropdown">
                <div class="user-names">
                    <h5 id="username"></h5>
                    <span id="department"></span>
                </div>
                <span class="user-img">
                    <img src="assets/img/icons/dev.png" alt="Admin">
                </span>
            </a>
            <div class="dropdown-menu">
                <!-- <a class="dropdown-item" href="profile.php">Mi Perfil</a> -->
                <!-- <a class="dropdown-item" href="settings.php">Configuración Personal</a> -->
                <a class="dropdown-item" href="scripts/auth/logout.php">Cerrar Sesión</a>
            </div>
        </li>
    </ul>
    <div id="contenedorbadge" class="float-end">
        <div id="mobileBadge" class="badgeH">Nuevo CRM</div>
        
    </div>
</div>


<script>
    let usernmae_element =  document.getElementById("username");
    let department_element =  document.getElementById("department");
    
    let username = localStorage.getItem("user_name")
    let department = localStorage.getItem("department")

    console.log(username)
    console.log(department)
    usernmae_element.innerText = username;
    department_element.innerText = department;
</script>