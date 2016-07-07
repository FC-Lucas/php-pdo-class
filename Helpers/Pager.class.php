<?php


class Pager {

    
    private $Page;
    private $Limit;
    private $Offset;

    private $Tabela;
    private $Termos;
    private $Places;
    private $Pro;
    private $Ant;

    private $Rows;
    private $Link;
    private $MaxLinks;
    private $First;
    private $Last;
    private $Atual;

    private $Paginator;

    function __construct($Link, $First = null,$Last = null, $MaxLinks = null,$Atual) {
        $this->Link = (string) $Link;
        $this->First = ( (string) $First ? $First : 'Primeira Página' );
        $this->Last = ( (string) $Last ? $Last : 'Última Página' );
        $this->MaxLinks = ( (int) $MaxLinks ? $MaxLinks : 5);
        $this->Atual = (string) $Atual;
    }

    public function ExePager($Page,$Limit) {
        $this->Page = ( (int) $Page ? $Page : 1 );
        $this->Limit = (int) $Limit;
        $this->Offset = ($this->Page * $this->Limit) - $this->Limit;

    }

    public function ReturnPage() {
        if ($this->Page > 1):
            $nPage = $this->Page - 1;
            header("Location: {$this->Link}{$nPage}");
        endif;
    }

    public function getPage() {
        return $this->Page;
    }

    public function getLimit() {
        return $this->Limit;
    }

    public function getOffset() {
        return $this->Offset;
    }
    //Pega o conteúdo do getSyntex(a paginação)
    public function ExePaginator($Tabela, $Termos, $ParseString = null) {
        $this->Tabela = (string) $Tabela;
        $this->Termos = (string) $Termos;
        $this->Places = (string) $ParseString;
        $this->getSyntax();
    }
    //Retorna a página selecionada
    public function getPaginator() {
        return $this->Paginator;
    }
    //Faz a páginação
       private function getSyntax() {
        $read = new Read;
        $read->ExeRead($this->Tabela, $this->Termos, $this->Places);
        $this->Rows = $read->getRowCount();

           $this->Ant = $this->Page -1;
           $this->Pro = $this->Page +1;

        if ($this->Rows > $this->Limit):
            $Paginas = ceil($this->Rows / $this->Limit);
            $MaxLinks = $this->MaxLinks;

            $this->Paginator = "<ul class=\"paginator\">";
            $this->Paginator .= "<li class='list'><a class='links' title=\"{$this->First}\" href=\"{$this->Atual}\">{$this->First}</a></li>";
        if ($this->Page <= 1) {

        }else {
            $this->Paginator .="<a href=\"{$this->Link}{$this->Ant}\"><img class='btn'  src='imagens/botao-anterior.jpg'></a> ";
        }
            for ($iPag = $this->Page - $MaxLinks; $iPag <= $this->Page - 1; $iPag ++):
                if ($iPag >= 1) {
                    $this->Paginator .= "<li class='list'><a class='links' title=\"Página {$iPag}\" href=\"{$this->Link}{$iPag}\">{$iPag}</a></li>";
                }
            endfor;

            $this->Paginator .= "<li class='list'><span class=\"active\">{$this->Page}</span></li>";

            for ($dPag = $this->Page + 1; $dPag <= $this->Page + $MaxLinks; $dPag ++):
                if ($dPag <= $Paginas):
                    $this->Paginator .= "<li class='list'><a class='links' title=\"Página {$dPag}\" href=\"{$this->Link}{$dPag}\">{$dPag}</a></li>";
                endif;
            endfor;

            if ($this->Page >= $Paginas){

            }else {
                $this->Paginator .="<a href=\"{$this->Link}{$this->Pro}\"><img class='btn' id='btn2' src='imagens/botao-proximo.jpg'></a> ";
            }
            $this->Paginator .= "<li class='list'><a class='links' title=\"{$this->Last}\" href=\"{$this->Link}{$Paginas}\">{$this->Last}</a></li>";
            $this->Paginator .= "</ul>";
        endif;
    }

}
