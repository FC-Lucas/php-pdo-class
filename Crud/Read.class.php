<?php
class Read extends Conn{
    private $Select;
    private $Places;
    public  $Result;
    private $Read;
    private $Conn;

    public function ExeRead($Tabela, $Termos, $ParseString = null) {
        if(!empty($ParseString)) {
            $this->Places = $ParseString;
            parse_str($ParseString, $this->Places);
        }
        $this->Select = "SELECT * FROM {$Tabela} {$Termos}";
        $this->Execute();
    }

    public function ReadQuery($Query, $ParseString = null) {
        if(!empty($ParseString)) {
            $this->Places = $ParseString;
            parse_str($ParseString, $this->Places);
        }
        $this->Select = $Query;
        $this->Execute();
    }

    public function ReadUpdate($Tabela, $id ,$ParseString = null) {
        if(!empty($ParseString)) {
            $this->Places = $ParseString;
            parse_str($ParseString, $this->Places);
        }
        $this->Select = "SELECT * FROM {$Tabela} WHERE Id= {$id}";
        $this->Execute();
    }

    public function ReadAtivo($Tabela, $Termos, $ParseString = null) {
        if(!empty($ParseString)) {
            $this->Places = $ParseString;
            parse_str($ParseString, $this->Places);
        }
        $this->Select = "SELECT Id FROM {$Tabela} WHERE ativo=1 ORDER  BY Id DESC LIMIT {$Termos}";
        $this->Execute();
    }
    public function ReadMax($Tabela, $Termos = null, $ParseString = null) {
        if(!empty($ParseString)) {
            $this->Places = $ParseString;
            parse_str($ParseString, $this->Places);
        }
        $this->Select = "SELECT LAST_INSERT_ID(Id) FROM {$Tabela} {$Termos}";
        $this->Execute();
    }

    public function getResult() {
        return $this->Result;
    }

    public function getRowCount() {
        return $this->Read->rowCount();
    }


    public function Fullread($Query, $ParseString = null) {
        $this->Select = (string) $Query;

        if(!empty($ParseString)) {
            $this->Places = $ParseString;
            parse_str($ParseString, $this->Places);
        }
        $this->Execute();
    }

    public function setPlaces($ParseString) {
        parse_str($ParseString, $this->Places);
        $this->Execute();
    }

    private function Connect(){
        $this->Conn = parent::getConn();
        $this->Read = $this->Conn->prepare($this->Select);
        $this->Read->setFetchMode(PDO::FETCH_ASSOC);
    }
    private function getSyntax() {
        if ($this->Places) {
            foreach($this->Places as $Vinculo => $Valor){
                if ($Vinculo == 'limit' || $Vinculo == 'offset'){
                    $Valor = (int) $Valor;
                }
                $this->Read->bindValue(":{$Vinculo}", $Valor, (is_int($Valor) ? PDO::PARAM_INT : PDO::PARAM_STR));
            }
        }
    }
    private function Execute() {
        $this->Connect();
        try{
            $this->getSyntax();
            $this->Read->execute();
            $this->Result = $this->Read->fetchAll();
        }catch (PDOException $e) {
            $this->Result = null;
            MSG("<b>Erro ao ler:</b> {$e->getMessage()}", $e->getCode());
        }
    }
}
