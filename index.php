<?php 

//declaracion variables para conexion a bbdd
$host = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "api";

$conexion = new mysqli($host, $usuario, $password, $baseDatos);

//verificar si la conexion con la bbdd se establecio
if ($conexion -> connect_error) {
    die ("Conexion no establecida".$conexion->connect_error);
}

/**
 * vamos a recibir informacion a traves de una solicitud con diferentes verbos o formas
 * para hacer todo esto tenemos que devolver la respuesta en un formato: json
 * json puede ser interpretado o consultado por diferentes aplicaciones
 */
header("Content-Type: application/json");
$metodo = $_SERVER['REQUEST_METHOD'];
// $_SERVER contiene toda la informacion que se esta enviando
// REQUEST_METHOD nos va a mostrar que metodos se estan utilizando en este momento


// BUSCAR ID
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
// retorna el path
$buscarId = explode('/', $path);
// busca el id en todo el path o url que se nos envio
$id = ($path !== '/') ? end($buscarId) : null;
// utilizamos ese id recuperandolo de la url


switch ($metodo) {
    case 'GET':
        obtenerRegistros($conexion, $id);
        break;
    case 'POST':
        altaRegistro($conexion);
        break;
    case 'PUT':
        modificarRegistro($conexion, $id);
        break;
    case 'DELETE':
        bajaRegistro($conexion, $id);
        break;
    default:
        echo "Método no permitido";
        break;
}


// funcion para consulta SELECT
function obtenerRegistros($conexion, $id) {
    $consulta = ($id === null) ? "SELECT * FROM usuarios" : "SELECT * FROM usuarios WHERE id = $id";
    $resultado = $conexion -> query($consulta);

    if ($resultado) {
        $datos = array();
        while ($fila = $resultado -> fetch_assoc()) {
            $datos[] = $fila;
        }
        echo json_encode($datos);   //convierte los resultados en un formato json
    }
}

// funcion para consulta INSERT
function altaRegistro($conexion) {
    $dato = json_decode(file_get_contents('php://input'), true);    //captura el dato en formato json y decodifica
    $nombre = $dato['nombre'];  // captura el dato llamado nombre
    print_r($nombre);

    $consulta = "INSERT INTO usuarios(nombre) VALUES ('$nombre')";
    $resultado = $conexion -> query($consulta);

    if ($resultado) {
        $dato['id'] = $conexion -> insert_id;
        echo json_encode(($dato));
    }
    else {
        echo json_encode(array('error' => 'Error al crear usuario'));
    }
}

// funcion para consulta DELETE
function bajaRegistro($conexion, $id) {
    $consulta = "DELETE FROM usuarios WHERE id = $id";
    $resultado = $conexion -> query($consulta);

    if ($resultado) {
        echo json_encode(array('mensaje' => 'Usuario eliminado'));
    }
    else {
        echo json_encode(array('error' => 'Error al eliminar el usuario'));
    }
}

// funcion para consulta UPDATE
function modificarRegistro($conexion, $id) {
    $dato = json_decode(file_get_contents('php://input'), true);    //captura el dato en formato json y decodifica
    $nombre = $dato['nombre'];  // captura el dato llamado nombre

    echo "el id a modificar es ".$id." con el dato ".$nombre;

    $consulta = "UPDATE usuarios SET nombre = '$nombre' WHERE id = $id";
    $resultado = $conexion -> query($consulta);

    if ($resultado) {
        echo json_encode(array('mensaje' => 'Usuario actualizado'));
    }
    else {
        echo json_encode(array('error' => 'Error al actualizar el usuario'));
    }
}

?>