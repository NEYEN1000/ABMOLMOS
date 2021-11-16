<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('error_reporting', E_ALL);

if (file_exists("archivo.txt")) {
    //Leer el archivo y almacenar su contenido json en  $jSonClientes
    $jsonClientes = file_get_contents("archivo.txt");

    //Convertir el json en array $aClientes
    $aClientes = json_decode($jsonClientes, true);
} else {
    $aClientes = array();
}



$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : "";

if ($_POST) {
    $dni = trim($_REQUEST["txtDni"]);
    $nombre = trim($_REQUEST["txtNombre"]);
    $telefono = trim($_REQUEST["txtTelefono"]);
    $correo = trim($_REQUEST["txtCorreo"]);
    $imagen = "";


    if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
        $nombreAleatorio = date("Ymdhmsi"); //2021010420453710
        $archivo_tmp = $_FILES["archivo"]["tmp_name"];
        $nombreArchivo = $_FILES["archivo"]["name"];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $imagen = "$nombreAleatorio.$extension";
        move_uploaded_file($archivo_tmp, "imagenes/$imagen");
    }

    if ($id != "") {
        //Actualizar cliente
        if ($_FILES["archivo"]["error"] !== UPLOAD_ERR_OK) {
            $imagen = $aClientes[$id]["imagen"];
        } else {
            //Si está subiendo una nueva imagen, debe eliminar la imagen anterior
            unlink("imagenes/". $aClientes[$id]["imagen"]);
        }

        $aClientes[$id] = array("dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $imagen
        
        );
    } else {
        //Insertar nuevo cliente
        $aClientes[] = array("dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $imagen
        );
    }
    //Convertir el array a json y almacenarlo en una variable $jSonClientes
    $jsonClientes = json_encode($aClientes);

    //Almacenar el contenido de la variable json en el archivo.txt
    file_put_contents("archivo.txt", $jsonClientes);
    header("Location: index.php");
}

if($id != "" && isset($_REQUEST["do"]) && $_REQUEST["do"] == "eliminar"){
    unset($aClientes[$id]);
    $jsonClientes = json_encode($aClientes);
    file_put_contents("archivo.txt", $jsonClientes);
    header("Location: index.php");
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="css/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="css/estilosABM.css">
</head>
<body>
<header>
        <div class="row">
          <div class="col-12">
            <h1 class="display-1 text-center text-danger ">REGISTRO DE USUARIOS</h1>
          </div>
        </div>
      </header>
        <div class="row">
            <div class="col-6">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <?php if (isset($msg) && $msg != ""): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $msg; ?>
                        </div>
                        <?php endif;?>
                        <div class="col-4 form-group p-5 mt-6">
                            <label for="txtDni">DNI: *</label>
                            <input type="text" id="txtDni" name="txtDni" class="form-control" required value="<?php echo isset($aClientes[$id]["dni"]) ? $aClientes[$id]["dni"] : ""; ?>">
                        </div>
                        <div class="col-8 form-group p-5 mt-6">
                            <label for="txtNombre">Nombre: *</label>
                            <input type="text" id="txtNombre" name="txtNombre" class="form-control" required value="<?php echo isset($aClientes[$id]["nombre"]) ? $aClientes[$id]["nombre"] : ""; ?>">

                        </div>
                        <div class="col-4 form-group p-5 mt-6">
                            <label for="txtTelefono">Teléfono: *</label>
                            <input type="text" id="txtTelefono" name="txtTelefono" class="form-control" value="<?php echo isset($aClientes[$id]["telefono"]) ? $aClientes[$id]["telefono"] : ""; ?>">

                        </div>
                        <div class="col-8 form-group p-5 mt-6">
                            <label for="txtCorreo">Correo: *</label>
                            <input type="text" id="txtCorreo" name="txtCorreo" class="form-control" required value="<?php echo isset($aClientes[$id]["correo"]) ? $aClientes[$id]["correo"] : ""; ?>">

                        </div>
                        <div class="col-12 form-group p-5">
                            <label for="txtCorreo"><em> Archivo adjunto:</em></label>
                            <input type="file" id="archivo" name="archivo" class="form-control-file" accept=".jpg, .jpeg, .png">
                            <small class="d-block">Archivos admitidos: .jpg, .jpeg, .png</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-6 p-5">
                            <button type="submit" id="btnGuardar" name="btnGuardar" class=" text-center text-light bg-dark  ">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-6">
                <table class="table table-hover border-bold">
                    <tr>
                        <th>Imagen</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                       
                    </tr>

                    <?php foreach ($aClientes as $pos => $cliente): ?>
                    <tr>
                        <td><img src="imagenes/<?php echo $cliente["imagen"]; ?>" class="img-thumbnail"></td>
                        <td><?php echo $cliente["dni"]; ?></td>
                        <td><?php echo $cliente["nombre"]; ?></td>
                        <td><?php echo $cliente["correo"]; ?></td>
                        <td style="width: 110px;">
                            
                        </td>
                    </tr>
                    <?php endforeach;?>
                </table>

                <div class="bg-dark">
                    <p class="text-light text-center">Gracias Por Registrarte</p>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>
