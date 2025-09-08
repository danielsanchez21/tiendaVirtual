<?php
namespace app\controllers;
use app\helpers\datatables;
use yii\web\Controller;
use app\models\Usuario;
use yii\web\Response;
use Exception;
require_once(\Yii::getAlias('@app/components/SSP.php'));
class UsuarioController extends Controller {
    public $enableCsrfValidation = false;
    public function actionCrearusuario() {
        $id= filter_input(INPUT_POST,"id_user", FILTER_SANITIZE_NUMBER_INT);;
        $usuario = empty($id)?new Usuario():Usuario::findOne($id);
        $usuario->nombre = filter_input(INPUT_POST,"usuario-nombre", FILTER_SANITIZE_STRING);
        $usuario->correo = filter_input(INPUT_POST,"usuario-correo", FILTER_SANITIZE_STRING);
        $usuario->contrasena = filter_input(INPUT_POST,"usuario-contrasena", FILTER_SANITIZE_STRING);

        if (!$usuario->validate()) {
            throw new Exception('Error al crear usuario');
        }

        $usuario->save();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['message' => 'Usuario creado correctamente'];
    }

    public function actionListarusuarios() {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return Usuario::find()->all();
    }
    public function actionLogin(){
        \Yii::$app->response->format = Response::FORMAT_JSON;

        // Obtener y validar credenciales
        $post = \Yii::$app->request->post();
        $username = isset($post['typeEmailX']) ? trim($post['typeEmailX']) : '';
        $password = isset($post['typePasswordX']) ? trim($post['typePasswordX']) : '';

        if ($username === '' || $password === '') {
            return ['success' => false, 'message' => 'Credenciales inválidas'];
        }

        // Autenticar
        $validacion = Usuario::find()->where(['correo' => $username, 'contrasena' => $password])->one();
        if (!$validacion) {
            return ['success' => false, 'message' => 'Usuario o contraseña incorrectos'];
        }

        // Obtener datos y rol del usuario
        $datosToken = Usuario::find()
            ->select([
                'u.id_usuario id_user',
                'u.nombre nombre',
                'r.id_rol',
                'r.nombre rol_nombre'
            ])
            ->alias('u')
            ->innerJoin('usuario_rol ur', 'u.id_usuario=ur.id_usuario')
            ->innerJoin('rol r', 'r.id_rol=ur.id_rol')
            ->where(['u.id_usuario' => $validacion->id_usuario])
            ->asArray()
            ->one();

        // Generar token de sesión
        $token = bin2hex(random_bytes(32));
        $rolNombre = isset($datosToken['rol_nombre']) ? strtolower($datosToken['rol_nombre']) : '';
        $isAdmin = ($rolNombre === 'admin');

        // Guardar en sesión para validaciones del backend si se requiere
        $session = \Yii::$app->session;
        $session->open();
        $session->set('auth', [
            'token'      => $token,
            'user_id'    => $datosToken['id_user'] ?? $validacion->id_usuario,
            'user_name'  => $datosToken['nombre'] ?? '',
            'role_id'    => $datosToken['id_rol'] ?? null,
            'role_name'  => $datosToken['rol_nombre'] ?? '',
            'is_admin'   => $isAdmin,
            'created_at' => time(),
        ]);

        // allowed indica al frontend qué permitir
        return [
            'success'  => true,
            'message'  => 'Usuario válido',
            'token'    => $token,
            'data'     => $datosToken,
            'is_admin' => $isAdmin,
            'allowed'  => $isAdmin ? 'all' : 'producto', // si no es admin, solo Producto.html
        ];
    }
    public function actionListausuarios() {
        $columns = array(
            array('db' => 'id_usuario', 'dt' => 0),
            array('db' => 'nombre', 'dt' => 1),
            array('db' => 'correo', 'dt' => 2),
            array(
                'db' => 'id_usuario',
                'dt' => 3,
                'formatter' => function ($d, $row) {
                    // Botón Editar
                    $buts = '<button type="button" id="editar_usuario_' . $row['id_usuario'] . '" 
                    title="Editar" data-bs-toggle="modal" data-bs-target="#modalusuario"
                    class="btn btn-sm btn-primary"
                    data-id="' . $row['id_usuario'] . '">
                   <i class="fa-sharp fa-regular fa-pen-to-square" style="color: #74C0FC;"></i>
                 </button> ';


                    return $buts;
                }),

        );
        //Indice
        $primaryKey = "id_usuario";
        //Tabla
        $table = "usuario";

        $result = datatables::simple($_POST, $table, $primaryKey, $columns);
        echo json_encode($result);
    }

    public function actionObtenerusuario()
    {
        $id= $_POST['idusuario'];
        $data = Usuario::findOne($id);
        echo json_encode($data->attributes);

    }
}
