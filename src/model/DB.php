<?php
namespace MyApp;

class DataBase {

    public      $isConn;
    protected   $datab;

    #   Подключение
    public function __construct( $userName = "root", $password = "", $host = "localhost", $dbName = "ws", $options = array(\PDO::ATTR_PERSISTENT => true)) {
    #Проверка подключения
		$this->isConn = TRUE;
        try {
            $this->datab = new \PDO( "mysql:host={$host};dbname={$dbName};charset=utf8", $userName, $password, $options );
            $this->datab->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
            #Атрибут для первоначальной настройки
            $this->datab->setAttribute( \PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC );
        } catch ( PDOException $e ) {
			#Показать ошибку, если нет подключения
            throw new Exception( $e->getMessage() );
        }
    }

    #-------------------------------------------------------------------------------------------------------------------
    #   Разрыв соединения
    #-------------------------------------------------------------------------------------------------------------------
    public function Disconnect() {
        $this->datab   =   NULL;
        $this->isConn  =   FALSE;
    }    
    
    #-------------------------------------------------------------------------------------------------------------------
    #   Чек соединения с базой
    #-------------------------------------------------------------------------------------------------------------------
    public function isConnect() {
        if (!is_null($this->datab)){
//             try {
//                $testArray = $this->getRow('SELECT 1+2+3');
//                 $testRes = $this->datab->query( 'SELECT 1+2+3' );
//                 $testArray = $testRes->fetch();
//                 var_export($testArray);
//                if ($testArray['1+2+3'] == 6) {
//                    return true;
//                }
//            }
//            catch (PDOException $e) {
//                return false;
//            }
            try {
                $this->datab->query("SHOW STATUS;")->execute();
                return true;
            } catch(\PDOException $e) {
                if($e->getCode() != 'HY000' || !stristr($e->getMessage(), 'server has gone away') ) {
                    throw $e;
                }
                return false;
            }
        }
        return false;
    }

    #-------------------------------------------------------------------------------------------------------------------
    #   Получение 1 записи
    #-------------------------------------------------------------------------------------------------------------------
    public function getRow( $query, $params = array() ) {
        try {
			         $stm = $this->datab->prepare( $query );
			         $stm->execute( $params );
			         #Получаю массив результата запроса
			         return $stm->fetch();
             } catch ( PDOException $e ) {
			            #Запрос отрицательный. Вывожу ошибку.
                  throw new Exception( $e->getMessage() );
            }
    }

    #-------------------------------------------------------------------------------------------------------------------
    #   Получение нескольких записей
    #-------------------------------------------------------------------------------------------------------------------
    public function getRows( $query, $params = array() ) {
        try {
        			$stm = $this->datab->prepare( $query );
        			$stm->execute( $params );
        			#Получаю массив результата запроса
        			return $stm->fetchAll();
        		} catch ( PDOException $e ) {
        			#Запрос отрицательный. Вывожу ошибку.
        			throw new Exception( $e->getMessage() );
        		}
    }

    #-------------------------------------------------------------------------------------------------------------------
    #   Выполнение произвольного запроса
    #-------------------------------------------------------------------------------------------------------------------
    public function putQuery( $query, $params = array() ) {
		try {
    			$stmt = $this->datab->prepare( $query );
                $stmt->execute( $params );
                return TRUE;
    		} catch ( PDOException $e ) {
    			throw new Exception( $e->getMessage() );
    		}
	}


    
    
    #-------------------------------------------------------------------------------------------------------------------
    #   Выполнение произвольного запроса без экранирования
    #-------------------------------------------------------------------------------------------------------------------



    public function execQuery( $query) {
    try {
          $stmt = $this->datab->prepare( $query );
                $stmt->execute();
                // return TRUE;
        } catch ( PDOException $e ) {
          throw new Exception( $e->getMessage() );
        }
  }
}
?>
