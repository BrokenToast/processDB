<?php
require_once('DBexception.php');
/**
 * precessDB
 * 
 * Clase que nos permite hacer un manteimmiento a un base de datos
 * 
 * @author Luispatb luispatb@gmail.com
 * @version 1.0.0
 */
class processDB{
    /**
     * Summary of dsn
     * 
     * @var string
     */
    public readonly string $dsn;
    /**
     * Summary of user
     * 
     * Usuario de la base de datos
     * 
     * @var string
     */
    public readonly string $user;
    /**
     * Summary of password
     * 
     * Contraseña del usuario.
     * 
     * @var string
     */
    public readonly string $password;
    private PDO $oConexionDB;
    /**
     * Summary of __construct
     * 
     * @param mixed $dsn
     * @param mixed $user Usuario de la base de datos
     * @param mixed $password Contraseña del usuario.
     */
    public function __construct($dsn,$user,$password){
        $this->dsn=$dsn;
        $this->user=$user;
        $this->password = $password;
    }
    /**
     * Metodo que no permite lanzar un quiery y nos devuelve la informacion en una array
     * @param string $query Query que se va a ejecutar.
     * @throws DBexception Se lanza cuando hay un error con el query
     * @return array devuelve una array con la informacion del Quer(Formato: [[tupla1],[tupla..],[tupla..]])y si no hay resultado devuelve false. 
     */
    public function executeQuery(string $query){
        $this->__wakeup();
        try{
            $oResultado=$this->oConexionDB->query($query);
            $aresultado = [];
            $tupla=$oResultado->fetch(PDO::FETCH_ASSOC);
            while(!is_bool($tupla)){
                array_push($aresultado, $tupla);
                $tupla=$oResultado->fetch(PDO::FETCH_ASSOC);
            }
            return $aresultado;
        }catch(PDOException $error){
            throw new DBexception($error);
        }finally{
            unset($oConexionDB);
        }
    }
    /**
     * executeUDI: Sirve para ejecutar un inserción o update o delete:
     * En la inserción podemos hacer un query paremetizado esto nos abre la posibilidad para pasarle una array que contenga datos sobre la inserción
     * pero debe respetar uno de los siguientes formatos la array de datos:
     * Requisitos: la array de datos contiene otras arraya que almacenan las tuplas.
     * 1º forma
     * Insert:
     * insert into prueba1 VALUES(:codigo,:nombre,:apellidos,:edad)
     * 
     * Datos:
     * [[
     * ":codigo"=>"kjfh",
     * ":nombre"=>"ernesto",
     * ":apellidos"=>"algo",
     * ":edad"=>"54455"
     * ],
     * [
     * ":codigo"=>"hjj",
     * ":nombre"=>"erpepe",
     * ":apellidos"=>"nose",
     * ":edad"=>"22"
     * ]
     * ];
     * 
     * 2º Forma
     * Insert:
     * insert into prueba1 VALUES(?,?,?,?)
     * 
     * Datos:
     * [
     *     [
     *         "cdfg",
     *         "Alex",
     *         "Prieto",
     *         20
     *     ],
     *     [
     *         "kkk",
     *         "Raul",
     *         "nose",
     *         21
     *     ]
     * ];
     * @param string $UDI Inserto update o delete 
     * @param array|null $datos Datos del insert.
     * @return bool|int Devuelve el numero de tuplas afectadas.
     */
    public function executeUDI(string $UDI,array $datos=null){
        $this->__wakeup();
        if(is_null($datos)){
            try{
                return $oResultado=$this->oConexionDB->exec($UDI);
            }catch(PDOException $error){
                throw new DBexception($error->getMessage());
            }
        }else{
            if(is_array($datos[0])){
                $prepareUDI=$this->oConexionDB->prepare($UDI);
                foreach($datos as $tupla){
                    $prepareUDI->execute($tupla);
                }
            }
        }
    }
    public function __wakeup(){
        $this->oConexionDB = new PDO($this->dsn,$this->user,$this->password);
    }
}