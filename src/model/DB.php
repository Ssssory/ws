<?php
namespace MyApp;

class DataBase {

    public      $isConn;
    protected   $datab;

    #   Подключение
    public function __construct( $userName = "root", $password = "", $host = "localhost", $dbName = "ws", $options = array()) {
    #Проверка подключения
		$this->isConn = TRUE;
        try {
            $this->datab = new PDO( "mysql:host={$host};dbname={$dbName};charset=utf8", $userName, $password, $options );
            $this->datab->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            #Атрибут для первоначальной настройки
            $this->datab->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
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
    #   Запись
    #-------------------------------------------------------------------------------------------------------------------
    public function insertRow( $query, $params = array() ) {
		try {
    			$stmt = $this->datab->prepare( $query );
                $stmt->execute( $params );
                return TRUE;
    		} catch ( PDOException $e ) {
    			throw new Exception( $e->getMessage() );
    		}
	}


    #-------------------------------------------------------------------------------------------------------------------
    #   Обновление данных
    #-------------------------------------------------------------------------------------------------------------------
    public function updateRow( $query, $params = array() ) {
        $this->insertRow( $query, $params );
    }

    #-------------------------------------------------------------------------------------------------------------------
    #   Удаление
    #-------------------------------------------------------------------------------------------------------------------
    public function deleteRow( $query, $params = array() ) {
        $this->insertRow( $query, $params );
    }

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
