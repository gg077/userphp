<?php

class User {
    // Basis informatie over een gebruiker
    public $id;            // Uniek nummer van de gebruiker
    public $username;      // Inlognaam
    public $password;      // Wachtwoord
    public $first_name;    // Voornaam
    public $last_name;     // Achternaam
    public $email;

    // Voert een database zoekopdracht uit en maakt er User objecten van
    public static function find_this_query($sql, $values = []) {
        global $database;
        $result = $database->query($sql, $values);
        $the_object_array = [];
        while($row = mysqli_fetch_assoc($result)) {
            $the_object_array[] = self::instantie($row);
        }
        return $the_object_array;
    }

    // Maakt een User object van database resultaten
    public static function instantie($result) {
        $the_object = new self();
        foreach($result as $the_attribute => $value) {
            if($the_object->has_the_attribute($the_attribute)) {
                $the_object->$the_attribute = $value;
            }
        }
        return $the_object;
    }

    // Controleert of een eigenschap bestaat in het User object
    public function has_the_attribute($the_attribute) {
        $object_properties = get_object_vars($this);
        return array_key_exists($the_attribute, $object_properties);
    }

    // Haalt alle gebruikers op uit de database
    public static function find_all_users() {
        return self::find_this_query("SELECT * FROM users ORDER BY id DESC");
    }

    // Zoekt een specifieke gebruiker op ID
    public static function find_user_by_id($user_id) {
        $result = self::find_this_query("SELECT * FROM users WHERE id=?",[$user_id]);
        return !empty($result) ? array_shift($result): false;
    }

    // Controleert of gebruikersnaam en wachtwoord kloppen
    public static function verify_user($username, $password) {
        global $database;
        $username = $database->escape_string($username);
        $password = $database->escape_string($password);

        $sql = "SELECT * FROM users WHERE username = ? AND password = ? LIMIT 1";
        $the_result_array = self::find_this_query($sql,[$username,$password]);
        return !empty($the_result_array) ? array_shift($the_result_array) : false;
    }

    // Naam van de database tabel
    protected static $table_name = 'users';

    // Verzamelt alle eigenschappen in een array
    public function get_properties() {
        return[
            'id'=> $this->id,
            'username'=>$this->username,
            'password'=>$this->password,
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'email'=>$this->email
        ];
    }

    // Maakt een nieuwe gebruiker aan in de database
    public function create() {
        global $database;
        $table = static::$table_name;
        $properties = $this->get_properties();

        // ID weghalen want die maakt database zelf aan
        if(array_key_exists('id',$properties)) {
            unset($properties['id']);
        }

        // Bescherm tegen SQL injectie
        $escaped_values = array_map([$database,'escape_string'], $properties);

        // Maak vraagtekens voor prepared statement
        $placeholders = array_fill(0,count($properties), '?');

        // Maak lijst van veldnamen
        $fields_string = implode(',',array_keys($properties));

        // Bepaal type van elke waarde (tekst/nummer/kommagetal)
        $types_string = "";
        foreach($properties as $value) {
            if(is_int($value)) $types_string .= "i";
            elseif(is_float($value)) $types_string .= "d";
            else $types_string .= "s";
        }

        // Maak en voer de SQL opdracht uit
        $sql = "INSERT INTO $table ($fields_string) VALUES (".implode(',',$placeholders).")";
        $database->query($sql, $escaped_values);
    }

    // Werkt een bestaande gebruiker bij in de database
    public function update() {
        global $database;
        $table = static::$table_name;
        $properties = $this->get_properties();
        unset($properties['id']);

        // Bescherm tegen SQL injectie
        $escaped_values = array_map([$database,'escape_string'], $properties);
        $escaped_values[] = $this->id;

        // Maak vraagtekens voor prepared statement
        $placeholders = array_fill(0,count($properties), '?');

        // Maak lijst van veldnamen
        $fields_string = implode(',',array_keys($properties));

        // Bepaal type van elke waarde
        $types_string = "";
        foreach($properties as $value) {
            if(is_int($value)) $types_string .= "i";
            elseif(is_float($value)) $types_string .= "d";
            else $types_string .= "s";
        }

        // Maak en voer SQL opdracht uit
        $sql = "UPDATE $table SET " . implode(', ', array_map(fn($field) => "$field = ?", array_keys($properties))) . " WHERE id = ?";
        $database->query($sql,$escaped_values);
    }

    // Verwijdert een gebruiker uit de database
    public function delete() {
        global $database;
        $table = static::$table_name;
        $escaped_id = $database->escape_string($this->id);

        $sql = "DELETE FROM $table WHERE id = ?";
        $params = [$escaped_id];
        $database->query($sql,$params);
    }
}