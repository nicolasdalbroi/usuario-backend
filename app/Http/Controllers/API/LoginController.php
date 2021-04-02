<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Login; 
use Illuminate\Http\Request;
use App\Clases\respuestas;
use App\clases\generarToken;





class LoginController extends Controller
{
    
    public function index()
    {
        //Instancio clase de respuestas http
        $respuestas = new respuestas; 
        $token = new generarToken; 

       //Recibo datos del Frontend (pass y CI)
        $postLogin = file_get_contents("php://input");
        
         //Agrego cabeceras HTTP
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        $datos = json_decode($postLogin,true);
        //Guardo en variables
        $usuario_ci = $datos['usuario'];
        $usuario_password = $datos["password"];
    if($usuario_ci == "" || $usuario_password == ""){
        return $respuestas->error_400();
    }

////////////////////////////////////////////////////////////////

 //error_reporting(0);
 //error_reporting(E_ALL); //activar los errores (en modo depuración)
 
 include(app_path() . '\functions\parametrosLdap.php');//parametros de conexion con el servidor LDAP

//Conexion a LDAP
$conectado_LDAP = @ldap_connect($servidor_LDAP, 389 );
ldap_set_option($conectado_LDAP, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($conectado_LDAP, LDAP_OPT_REFERRALS, 0);
    
if($autenticado_LDAP = @ldap_bind($conectado_LDAP, $usuario_LDAP . "@" . $servidor_dominio, $contrasena_LDAP)){

 

 //Busco la cedula (usuario) en el AD
   $filter="(|(samaccountname=$usuario_ci))"; 
   $fields = array("displayname","mail","samaccountname","telephonenumber","givenname"); 
   $sr = @ldap_search($conectado_LDAP, $ldap_dn, $filter, $fields);  
   $entries = @ldap_get_entries($conectado_LDAP, $sr);  
   if ($entries["count"] > 0){ //En caso de encontrar el usuario, verificar password y setear cookies
        for ($i=0; $i<$entries["count"]; $i++){
                   $usuario_nombre = $entries[$i]["displayname"][0];
                    //echo "Email: ".$entries[$i]["mail"][0];
                    //$email = $entries[$i]["mail"][0];
                   $usuario_ci=$entries[$i]["samaccountname"][0];
                  // echo "Telefono: ".$entries[$i]["telephonenumber"][0];
                }
               // echo $usuario_password;
                if($verificar_password = @ldap_bind($conectado_LDAP, $usuario_ci . "@" . $servidor_dominio, $usuario_password)){
                    //$verificar  = $this->insertarToken();
                    $token = $token->token();
                    
                        //Seteo las cookies que se van a guardar en el explorador
                        $_SESSION['token'] = $token;
                        setcookie("token",$token,time()+(60*60),"/");
                        setcookie("user",$usuario_ci,time()+(60*60),"/");
                        setcookie("nombre",$usuario_nombre,time()+(60*60),"/");
                        $result = $respuestas->response;
                        $result["result"] = array(
                            "token" => $token,
                            "user" => $usuario_ci,
                            "user_name" => $usuario_nombre,
                        );
                        return $result;
                        
                }else{
                    return $respuestas->error_200("Error al autenticar, verifique Password"); //Error al autenticar
                }
    } else {
            return $respuestas->error_200("Error al autenticar, verifique CI: ". $usuario_ci); //Error al autenticar
    }
 

}else{
    return $respuestas->error_500("Error de autenticación con el servidor"); //Error al autenticar
}

    }

    
    function encriptar($string){
        return md5($string);
    }




   
}


