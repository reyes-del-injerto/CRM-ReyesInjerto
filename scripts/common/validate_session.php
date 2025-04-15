<?php

if (isset($_COOKIE['recordar_token'])) {
    $token = $_COOKIE['recordar_token'];

    $sql = "SELECT user_id, user_name, user_department FROM u_tokens WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        $user_name = $row['user_name'];
        $user_department = $row['user_department'];

        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $user_name;
        $_SESSION['user_department'] = $user_department;

        $sql = "SELECT permission_id FROM u_permission_assignment WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $user_permissions = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $user_permissions[] = $row['permission_id'];
            }
        }
        $_SESSION['user_permissions'] = $user_permissions;
    } else {
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
