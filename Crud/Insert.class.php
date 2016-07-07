<?php
class novaNoticia extends Conn{
    private $Tabela;
    private $Dados;
    private $Result;
    private $Create;
    private $Conn;

    public function Create($Tabela, array $Dados) {
        $this->Tabela = (string) $Tabela;
        $this->Dados = $Dados;
        $this->getSyntax();
        $this->Execute();
    }

    public function getResult() {
        return $this->Result;
    }

    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Create = $this->Conn->prepare($this->Create);
    }

    private function getSyntax() {
        $Fileds = implode(', ', array_keys($this->Dados));
        $Places = ':' . implode(', :', array_keys($this->Dados));
        $this->Create = "INSERT INTO {$this->Tabela} ({$Fileds}) VALUES ({$Places})";
    }

    private function Execute(){
        $this->Connect();
        try{
            $this->Create->Execute($this->Dados);
            $this->Result = $this->Conn->lastInsertId();
        }catch (PDOException $e) {
            $this->Result = null;
            $_SESSION['msge'] = "Erro ao cadastrar!";
        }
    }
}
