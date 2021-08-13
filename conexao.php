<?php
class ConexaoBd
{
    public static function conexao()
    {
        $servername = "192.185.176.242";
        $database = "globomod_ecommerce";
        $username = "globomod_nayron";
        $password = "Globo@Modas2021";
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $database);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // echo "Connected successo";
        return $conn;
    }
}
