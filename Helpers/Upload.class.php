<?php

class Upload {
    private $File;
    private $Name;
    private $Send;
    private $Width;
    private $Height;
    private $Image;
    private $Result;
    private $Error;
    private $Folder;
    private static $BaseDir;

    function __construct($BaseDir = null) {
        self::$BaseDir = ((string) $BaseDir ? $BaseDir : './uploads/');
        if(!file_exists(self::$BaseDir) && !is_dir(self::$BaseDir)) {
            mkdir(self::$BaseDir, 0777);
        }

    }

    public function delTree($directory){
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory,FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $file){
            $file->isFile() ? unlink($file->getPathname()) : rmdir($file->getPathname());
        }
        rmdir($directory);
    }


    public function Image($Image, $Name, $Width = null, $Folder = null) {
        $this->File     =   $Image;
        $this->Name     =   $Name;
        $this->Width    =   ((int) $Width ? $Width : 100 );
        $this->Folder   =   ((string) $Folder ? $Folder : 'capas/');



        $this->CheckFolder($this->Folder);
        $this->setFileName();
        $this->UploadImageWidth();
    }

    public function ImageGalleyThumbs($Image, $Name, $PostId,  $Width = null, $Folder = null) {
        $this->File     =   $Image;
        $this->Name     =   $Name;
        $this->Width    =   ((int) $Width ? $Width : 77 );
        $this->Folder   =   ((string) $Folder ? $Folder : "galerias/{$PostId}/_thumbs/");



        $this->CheckFolder($this->Folder);
        $this->setFileName();
        $this->UploadImageWidth();
    }

    public function ImageGalley($Image, $Name, $PostId, $Width = null, $Folder = null) {
        $this->File     =   $Image;
        $this->Name     =   $Name;
        $this->Width    =   ((int) $Width ? $Width : 1024 );
        $this->Folder   =   ((string) $Folder ? $Folder : "galerias/{$PostId}/");



        $this->CheckFolder($this->Folder);
        $this->setFileName();
        $this->UploadImage();
    }


    public function gallery(array $Image, $PostId) {
        $this->Post = (int) $PostId;
        $this->Data = $Image;

        $ImageName = new Read();
        $ImageName->ExeRead('cadastro',"WHERE Id = $PostId");
        if(!$ImageName->getResult()){
            $this->Error = array("erro ao enviar galeria", MSG_ERROR);
            $this->Result = false;
        }else {
            $ImageName = $ImageName->Result[0]['Id'];
            $gallery = array();
            $galleryCount = count($this->Data['tmp_name']);
            $galleryKeys = array_keys($this->Data);

            for($gb = 0; $gb < $galleryCount; $gb++){
                foreach($galleryKeys as $Keys){
                    $galleryFiles[$gb][$Keys] = $this->Data[$Keys][$gb];
                }
            }
            $gallerySend = new Upload();
            $i = 0;
            $u = 0;

            foreach($galleryFiles as $galleryUpload){
                $i++;
                $ImgName = "{$ImageName}-gb-{$this->Post}-" . (substr(md5(time() + $i), 0,5));
                $gallerySend->ImageGalleyThumbs($galleryUpload, $ImgName, $PostId);
                $gallerySend->ImageGalley($galleryUpload, $ImgName, $PostId);
            }
        }
    }


    public function Multiple($maxid, $folder){
        if(!file_exists($folder.$maxid)){
            mkdir($folder.$maxid, 777, true);
            if(file_exists($folder.$maxid)) {
                mkdir($folder . $maxid . "/" . "_thumbs", 0777);
            }
        }
    }

    public function getResult() {
        return $this->Result;
    }

    public function getError() {
        return $this->Error;
    }

    private function CheckFolder($Folder) {
        $this->CreateFolder("{$Folder}");
        $this->Send = ("{$Folder}");
    }

    private function CreateFolder($Folder) {
        if(!file_exists(self::$BaseDir . $Folder) && !is_dir(self::$BaseDir . $Folder)) {
            mkdir(self::$BaseDir . $Folder, 0777, true);
        }
    }

    private function setFileName() {
        $FileName = Check::Name($this->Name) . strrchr($this->File['name'], '.');
        if(file_exists(self::$BaseDir . $this->Send . $FileName)){
            $FileName = Check::Name($this->Name) . '-' . strrchr($this->File['name'], '.');
            echo $FileName;
        }
        $this->Name = $FileName;
    }

    private function UploadImage() {
        switch($this->File['type']):
            case 'image/jpg';
            case 'image/jpeg';
            case 'image/pjpeg';
                $this->Image = imagecreatefromjpeg($this->File['tmp_name']);
                break;
            case 'image/png';
            case 'image/x-png';
                $this->Image = imagecreatefrompng($this->File['tmp_name']);
                break;
        endswitch;

        if(!$this->Image) {
            $this->Result = false;
            $this->Error = 'Tipo de arquivo inválido, envie imagens JPG ou PNG!';
        }else {
            $x = imagesx($this->Image);
            $y = imagesy($this->Image);
            $ImageX = ($this->Width < $x ? $this->Width : $x);
            $ImageH = $y;
            $ImageX = $x;
            $NewImage = imagecreatetruecolor($ImageX, $ImageH);
            imagealphablending($NewImage, false);
            imagesavealpha($NewImage, true);
            imagecopyresampled($NewImage, $this->Image, 0, 0, 0, 0, $ImageX, $ImageH, $x, $y);

            switch($this->File['type']):
                case 'image/jpg';
                case 'image/jpeg';
                case 'image/pjpeg';
                    imagejpeg($NewImage, self::$BaseDir . $this->Send . $this->Name);
                    break;
                case 'image/png';
                case 'image/x-png';
                    imagepng($NewImage, self::$BaseDir . $this->Send . $this->Name);
                    break;
            endswitch;

            if(!$NewImage){
                $this->Result = false;
                $this->Error = 'Tipo de arquivo inválido, envie imagens JPG ou PNG!';
            }else {
                $this->Result = $this->Send . $this->Name;
                $this->Error = null;
            }

            imagedestroy($this->Image);
            imagedestroy($NewImage);
        }
    }

    public function MoveFile() {
        if(move_uploaded_file($this->File['tmp_name'], self::$BaseDir . $this->Send . $this->Name)){
            $this->Result = $this->Send . $this->Name;
            $this->Error = null;
        }else {
            $this->Result = false;
            $this->Error = 'Erro ao mover o arquivo. Favor tente outra vez!';
        }
    }

    public function DelGallery($Imagem){
        if(isset($Imagem)){
            unlink($Imagem);
        }
    }
}
