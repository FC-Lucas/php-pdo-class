<?php

class Update extends Conn{
    private $Tabela;
    private $Dados;
    private $Termos;
    private $Places;
    private $Result;

    private $Update;

    private $Conn;

    public function ExeUpdate($Tabela, array $Dados, $Termos, $ParseString=null) {
        $this->Tabela = (string) $Tabela;
        $this->Dados = $Dados;
        $this->Termos = $Termos;

        parse_str($ParseString, $this->Places);
        $this->getSyntax();
        $this->Execute();
    }

    public function getResult() {
        return $this->Result;
    }

    public function getRowCount() {
        return $this->Update->rowCount();
    }

    public function setPlaces($ParseString) {
        parse_str($ParseString, $this->Places);
        $this->getSyntax();
        $this->Execute();
    }

    private function Connect(){
        $this->Conn = parent::getConn();
        $this->Update = $this->Conn->Prepare($this->Update);
    }
    private function getSyntax() {
        foreach($this->Dados as $Key => $Value) {
            $Places[] = $Key . ' = :' . $Key;
        }
        $Places = implode(', ', $Places);
        $this->Update = "UPDATE {$this->Tabela} SET {$Places} {$this->Termos}";
    }
    private function Execute(){
        $this->Connect();
        try {
            $this->Update->execute(array_merge($this->Dados, $this->Places));
            $this->Result = true;
        }catch(PDOException $e) {
            $this->Result = null;
            MSG("<b>Erro ao ler;</b> {$e->getMessage()}", $e->getCode());
        }
    }
}