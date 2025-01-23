<?php
require_once("config.php");
class Database {
    // Dit is zoals een telefoon waarmee we met de database kunnen praten
    public $connection;

    // Deze functie maakt verbinding met de database, net zoals je een telefoontje start
    public function open_db_connection() {
        // We maken verbinding met de database door het adres (HOST),
        // gebruikersnaam (USER), wachtwoord (PASS) en database naam (NAME) te gebruiken
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Als de verbinding niet lukt (zoals wanneer je telefoon geen bereik heeft)
        if (mysqli_connect_errno()) {
            // Dan vertellen we wat er mis ging en stoppen we het programma
            printf("Connectie is mislukt: %s\n", mysqli_connect_error());
            exit();
        }
    }

    // Deze functie controleert of onze vraag aan de database goed ging
    // Net zoals controleren of je bericht wel verstuurd is
    public function confirm_query($result) {
        if (!$result) {
            die("Query kan niet worden uitgevoerd" . $this->connection->error);
        }
    }

    // Deze functie maakt tekst veilig voordat we het naar de database sturen
    // Het is als het controleren of er geen verkeerde dingen in een brief staan
    public function escape_string($string) {
        $escaped_string = $this->connection->real_escape_string($string);
        return $escaped_string;
    }

    // Deze functie stuurt vragen naar de database op een veilige manier
    // Het is als het invullen van een formulier waar je precies aangeeft wat waar moet komen
    public function query($sql, $params = []) {
        // We maken een speciaal formulier klaar
        $stmt = $this->connection->prepare($sql);

        // Als we extra informatie hebben om in te vullen
        if (!empty($params)) {
            $types = "";    // Hier schrijven we op wat voor soort informatie het is
            $values = [];   // Hier bewaren we de informatie

            // Voor elk stukje informatie kijken we wat voor soort het is:
            // - Is het een gewoon getal? Dan schrijven we 'i' op
            // - Is het een kommagetal? Dan schrijven we 'd' op
            // - Is het tekst? Dan schrijven we 's' op
            foreach ($params as $param) {
                if (is_int($param)) $types .= "i";
                elseif (is_float($param)) $types .= "d";
                else $types .= "s";
                $values[] = $param;
            }

            // We zetten alle informatie op de juiste plek in het formulier
            array_unshift($values, $types);
            call_user_func_array([$stmt, "bind_param"], $this->ref_values($values));
        }

        // We sturen het formulier op
        $stmt->execute();
        // We krijgen een antwoord terug
        $result = $stmt->get_result();
        // We ruimen netjes op
        $stmt->close();
        // We geven het antwoord terug
        return $result;
    }

    // Deze helper-functie zorgt dat de informatie op de juiste manier wordt doorgegeven
    // Het is als het netjes op volgorde leggen van papieren voordat je ze in een map stopt
    private function ref_values($array) {
        $refs = [];
        foreach ($array as $key => $value) {
            if ($key === 0) $refs[$key] = $value;  // Het eerste papier is speciaal
            else $refs[$key] = &$array[$key];      // De rest van de papieren komen erna
        }
        return $refs;
    }

    // Dit gebeurt automatisch wanneer we een nieuwe Database maken
    // Net zoals wanneer je een nieuwe telefoon voor het eerst aanzet
    function __construct() {
        $this->open_db_connection();
    }
}

// We maken één database-telefoon die we kunnen gebruiken in ons hele programma
$database = new Database();

?>