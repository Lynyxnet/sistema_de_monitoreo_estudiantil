<?php
    include_once "db/conexion_pdo.php";
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Pagina Principal</title>

    <style>
      .navbar-brand {
        margin-left: 25px;
      }

      form > button {
        margin-top: 15px;
      }

      footer > div {
        color: white;
      }

      .space {
        margin-top: 9px;
      }
    </style>
    
</head>
<body>
<div class="login-error"></div>

<nav class="navbar navbar-light bg-body-tertiary">
    <div container-fluid>
        <a class="navbar-brand">Sistema de monitoreo estudiantil</a>
    </div>

    <div class="d-flex align-items-center">
        <button type="button" class="btn btn-outline-primary px-3 me-2" data-bs-toggle="modal" data-bs-target="#login">Login</button>
        <button type="button" class="btn btn-success me-3" data-bs-toggle="modal" data-bs-target="#register">Sign up</button>
    </div>
</nav>

<!-- Login -->
<div class="modal fade was-validated" id="login" role="dialog" >
    <div class="modal-dialog">
        <!-- Modal content -->
        <div class="modal-content">
          <!-- Modal-header -->
          <div class="modal-header" style="padding:20px 50px;">
            <h4><span class="glyphicon glyphicon-lock">Iniciar sesion</span></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <!-- Modal body -->
          <div class="modal-body" style="40px 50px;">
            
          <form action="pages/iniciar_sesion.php" method="post" role="form">

              <div class="form-group">
                <label><span class="glyphicon glyphicon-user"></span>Correo</label>
                <input type="text" class="form-control" id="correo" name="correo" pattern="\S+@zapopan\.tecmm\.edu\.mx" title="Escribe tu correo de la universidad" placeholder="Ingresa tu correo" required>
                <!-- <div class="valid-feedback">Valido</div>
                <div class="invalid-feedback">Por favor llena este campo</div> -->
              </div>

              <div class="form-group">
                <label><span class="glyphicon glyphicon-eye-close"></span>Password</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Ingresa tu password">
                <!-- <div class="valid-feedback">Valido</div>
                <div class="invalid-feedback">Por favor llena este campo</div> -->
              </div>

              <button type="submit" value="submit" class="btn btn-success btn-block"><span class="glyphicon glyphicon-off"></span>Login</button>
              
            </form>

          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          </div>

        </div>
    </div>
</div>

<!-- Registro -->
<div class="modal fade" id="register" roles="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        
        <!-- Modal content -->
        <div class="modal-content">

          <!-- Modal-header -->
          <div class="modal-header" style="padding:20px 50px;">
            <h4><span class="glyphicon glyphicon-lock">Crear cuenta</span></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">

            <form action="pages/crear_cuenta.php" method="post">
            <div class="row">
              <div class="col-12">
                <input type="text" class="form-control" name="nombre" pattern="[a-zA-Z]+(\s+[a-zA-Z]+)*" placeholder="Nombre">
              </div>
              <div class="space col-6">
                <input type="text" class="form-control" name="apellidoPaterno" placeholder="Apellido paterno">
              </div>
              <div class="space col-6">
                <input type="text" class="form-control" name="apellidoMaterno" placeholder="Apellido materno">
              </div>
              <div class="space col-6">
                <input type="text" class="form-control" name="correo" pattern="\S+@zapopan\.tecmm\.edu\.mx" title="Escribe tu correo de la universidad" placeholder="Correo electronico">
              </div>
              <div class="space col-3">
                <input type="password" class="form-control" name="password_1" pattern=".{8,}" title="Escribe 8 o mas caracteres" placeholder="Contrasena">
              </div>
              <div class="space col-3">
                <input type="password" class="form-control" name="password_2" placeholder="Repite contrasena">
              </div>
              <div class="space col-4">
                <input type="text" class="form-control" name="matricula" placeholder="Matricula">
              </div>
              <div class="space col-2">
                <select class="form-select" name="roles">
                <option selected>Roles</option>
                <option value=1>Maestro</option>
                <option value=2>Alumno</option>
                </select>
              </div>
              <div id="error"></div>
              <div class="space col-12">
                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
            </form>


          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          </div>

        </div>
    </div>
</div>

<footer class="bg-primary text-center text-while fixed-bottom">
  <div class="text-center p-3">TSJZ - 2024 Copyright</div>
</footer>

</body>
</html>