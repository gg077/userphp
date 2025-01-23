<?php

class Session {
    // Belangrijke informatie die we bijhouden
    private $signed_in;     // Houdt bij of iemand ingelogd is (ja/nee)
    public $user_id;        // Bewaart het ID van de ingelogde gebruiker
    public $message;        // Bewaart berichten die we willen tonen

    // Wanneer iemand inlogt
    public function login($user) {
        if($user) {
            // Bewaar gebruiker-ID in de sessie (zoals een stempel in je paspoort)
            $this->user_id = $_SESSION['user_id'] = $user->id;
            $this->signed_in = true;
        }
    }

    // Haalt informatie op van de ingelogde gebruiker
    public function get_logged_in_user() {
        if ($this->user_id) {
            return User::find_user_by_id($this->user_id);
        }
        return null;    // Niemand is ingelogd
    }

    // Wanneer iemand uitlogt
    public function logout() {
        // Verwijder alle inlog-informatie (zoals je stempel uitgummen)
        unset($_SESSION['user_id']);
        unset($this->user_id);
        $this->signed_in = false;
    }

    // Controleert of iemand is ingelogd
    private function check_the_login() {
        if(isset($_SESSION['user_id'])) {
            // Als er een gebruiker-ID is, dan is iemand ingelogd
            $this->user_id = $_SESSION['user_id'];
            $this->signed_in = true;
        } else {
            // Anders niet
            unset($this->user_id);
            $this->signed_in = false;
        }
    }

    // Vertelt aan andere delen van de website of iemand ingelogd is
    public function is_signed_in() {
        return $this->signed_in;
    }

    // Voor het opslaan en tonen van berichten aan gebruikers
    public function message($msg="") {
        if(!empty($msg)) {
            // Bericht opslaan
            $_SESSION['message'] = $msg;
        } else {
            // Bericht ophalen
            return $this->message;
        }
    }

    // Controleert of er berichten zijn om te tonen
    private function check_message() {
        if(isset($_SESSION['message'])) {
            // Als er een bericht is, bewaar het en verwijder het daarna
            $this->message = $_SESSION['message'];
            unset($_SESSION['message']);
        } else {
            // Anders, geen bericht
            $this->message = "";
        }
    }

    // Dit gebeurt automatisch als we een nieuwe sessie starten
    function __construct() {
        session_start();            // Start een nieuwe sessie
        $this->check_the_login();   // Kijk of iemand ingelogd is
        $this->check_message();     // Kijk of er berichten zijn
    }
}

// Maak één sessie die we overal kunnen gebruiken
$session = new Session();