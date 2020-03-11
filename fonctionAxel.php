<?php    
//Création d'un objet assuré
class Assure {
    private $CodeA;
    private $NirA;
    private $NomA;
    private $PrenomA;
    private $TelA;
    private $MailA;
    
    public function __construct($NirA) {
        $query = "SELECT * FROM assure WHERE NirA = ".$NirA;
        $link = connexionMySQL();
        $result = mysqli_query($link, $query);
        
        if($result != NULL) {            
            $row = mysql_fetch_array($result);

            $this->CodeA = $row["CodeA"];
            $this->NirA = $row["NirA"];
            $this->NomA = $row["NomA"];
            $this->PrenomA = $row["PrenomA"];
            $this->TelA = $row["TElA"];
            $this->MailA = $row["MailA"];
        }
    }
}

//Enregistre le dossier d'un assuré et son contenu
function EnregistrerDossier($file) {
    
}
?>