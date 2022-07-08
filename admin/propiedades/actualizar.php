<?php 
//validar la url por id válido
$id=$_GET['id'];
$id=filter_var($id,FILTER_VALIDATE_INT);//VALIDAR QUE ES UN ENTERO

if(!$id){
    header('Location: /admin');
}
// var_dump($id);
// BD 
require '../../includes/config/database.php';
$db=conectarDB();

//obtener datos de propiedad 
$consulta= "SELECT * FROM propiedades WHERE id=${'id'}";
$resultado= mysqli_query($db,$consulta);
$propiedad=mysqli_fetch_assoc($resultado);
// echo "<pre>";
// var_dump($propiedad);
// echo "</pre>";
//var_dump($db);
$consulta="SELECT * FROM vendedores";
$resultado=mysqli_query($db,$consulta);

// Arreglo con mensajes de errores 
$errores=[];

    $titulo=$propiedad['titulo'];
    $precio=$propiedad['precio'];
    $descripcion=$propiedad['descripcion'];
    $habitaciones=$propiedad['habitaciones'];
    $wc=$propiedad['wc'];
    $estacionamiento=$propiedad['estacionamiento'];
    $vendedorId=$propiedad['vendedorId'];
    $imagenPropiedad = $propiedad['imagen'];
// Ejecuta el codigo despues de que el usuario envia el formulario 
if($_SERVER['REQUEST_METHOD']=='POST'){
    
    //  echo "<pre>";
    //  var_dump($_POST);
    //  echo "</pre>";
    //  echo "<pre>";
    //  var_dump($_FILES);
    //  echo "</pre>";
    //  exit; 
    $titulo=mysqli_real_escape_string($db,$_POST['titulo']);
    $precio=mysqli_real_escape_string($db,$_POST['precio']);
    $descripcion=mysqli_real_escape_string($db,$_POST['descripcion']);
    $habitaciones=mysqli_real_escape_string($db,$_POST['habitaciones']);
    $wc=mysqli_real_escape_string($db,$_POST['wc']);
    $estacionamiento=mysqli_real_escape_string($db,$_POST['estacionamiento']);
    $vendedorId=mysqli_real_escape_string($db,$_POST['vendedor']);
    $creado= date('Y/m/d');
    // Asignar files hacía una variable 
    $imagen = $_FILES['imagen'];
    

    if(!$titulo){
        $errores[]="Debes añadir un titulo";
    }
    if(!$precio){
        $errores[]="El precio es obligatorio";
    }
    if(strlen($descripcion) < 50){
        $errores[]="La descripción es obligatoria y debe tener al menos 50 caracteres";
    }
    if(!$habitaciones){
        $errores[]="El numero de habitaciones es obligatorio";
    }
    if(!$wc){
        $errores[]="El número de baños es obligatorio";
    }
    if(!$estacionamiento){
        $errores[]="El número de estacionamientos es obligatorio";
    }
    if(!$vendedorId){
        $errores[]="Elije un vendedor";
    }
 
    // Validar tamaño de imagen 
    $medida=1000*1000; 
    if($imagen['size'] > $medida | $imagen['error']){
        $errores[]="La imagen es muy pesada";
    }
   
    // Revisar que el arreglo de errores este vacio
    if(empty($errores)){
        
        // Crear carpeta
        $carpetaImagenes='../../imagenes/'; 
        if(!is_dir($carpetaImagenes)){
            mkdir($carpetaImagenes);
        }
        $nombreImagen= ''; 
        // Subida de archivos
        if($imagen['name']){
            // Eliminar imagen previa 
            unlink($carpetaImagenes . $propiedad['imagen']);//funcion para elimianr archivos
            // // Generar nombre único
            $nombreImagen=md5(uniqid(rand(),true)). '.jpg';
            //  // Subir imagen
            move_uploaded_file($imagen['tmp_name'],$carpetaImagenes . $nombreImagen );
        }else{
            $nombreImagen = $propiedad['imagen'];
        }

        
    
       

        // insertar en la db
        $query="UPDATE propiedades SET titulo='${titulo}', precio='${precio}', imagen='${nombreImagen}', descripcion='${descripcion}', habitaciones=${habitaciones}, wc=${wc}, estacionamiento=${estacionamiento}, vendedorId=${vendedorId} WHERE id=${id}";
        echo    $query;
        
            $resultado=mysqli_query($db,$query);
            if($resultado){
                // echo "Insertado correctamente";
                // Redireccionar al usuario 
                header('Location:/admin?resultado=2'); 
            }
    }
   
}

require '../../includes/funciones.php';
incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Actualizar propiedad</h1>
        <a href="/admin" class="boton boton-verde">Volver</a>
        <?php   foreach($errores as $error): ?>
        <div class="alerta error">
            <?php echo $error;?>   
        </div>
        <?php endforeach;?>
        <form class="formulario" method="POST"  enctype="multipart/form-data">
            <fieldset>
                <legend>Información General</legend>
                <label for="titulo">Titulo:</label>
                <input name="titulo" type="text" id="titulo" placeholder="Titulo propiedad" value="<?php echo $titulo?>">
                <label for="precio">Precio:</label>
                <input type="number" name="precio" id="precio" placeholder="Precio propiedad" value="<?php echo $precio?>">
                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">
                <img src="/imagenes/<?php echo $imagenPropiedad?>" class="imagen-small">
                <label for="descripcion">Descripcion:</label>
                <textarea name="descripcion" id="descripcion">
                <?php echo $descripcion?>
                </textarea>

            </fieldset>
            <fieldset>
                <legend>Información propiedad</legend>
                <label for="habitaciones">Habitaciones:</label>
                <input name="habitaciones" 
                type="number" 
                id="habitaciones"
                 placeholder="Ej 3" min="1" max="9" 
                 value="<?php echo $habitaciones?>">
                <label for="wc">Baños:</label>
                <input name="wc"  type="number" id="wc" placeholder="Ej 3" min="1" max="9" value="<?php echo $wc?>">
                <label for="estacionamiento">Estacionamiento:</label>
                <input name="estacionamiento" type="number" id="estacionamiento" placeholder="Ej 3" min="1" max="9" value="<?php echo $estacionamiento?>">
            </fieldset>
            <fieldset>
                <legend>Vendedor</legend>
                <select  name="vendedor">
                    <option   value="">--Seleccione--</option>
                    <?php while($vendedor= mysqli_fetch_assoc($resultado) ):?>
                        <option  <?php echo $vendedorId === $vendedor['id'] ? 'selected' : ''; ?> value="<?php echo $vendedor['id'];?>"><?php echo $vendedor['nombre']." ". $vendedor['apellido'];?></option>
                    <?php endwhile?>
                </select>
            </fieldset>
            <input type="submit" value="Actualizar propiedad" class="boton boton-verde">
        </form>
    </main>
    <?php incluirTemplate('footer');?>
   