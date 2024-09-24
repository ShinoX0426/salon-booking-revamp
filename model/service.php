<?php

require_once '../config.php';

class Service
{
    private $conn;
    private $table_name = "Services";

    public $service_id;
    public $service_name;
    public $duration_minutes;
    public $price;
    public $description;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create a new service
    public function add()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET service_name=:service_name, duration_minutes=:duration_minutes, price=:price, description=:description";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->service_name = htmlspecialchars(strip_tags($this->service_name));
        $this->duration_minutes = htmlspecialchars(strip_tags($this->duration_minutes));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Bind parameters
        $stmt->bindParam(":service_name", $this->service_name);
        $stmt->bindParam(":duration_minutes", $this->duration_minutes);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":description", $this->description);

        return $stmt->execute();
    }

    // Update a service
    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET service_name=:service_name, duration_minutes=:duration_minutes, price=:price, description=:description
                  WHERE service_id=:service_id";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->service_name = htmlspecialchars(strip_tags($this->service_name));
        $this->duration_minutes = htmlspecialchars(strip_tags($this->duration_minutes));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->service_id = htmlspecialchars(strip_tags($this->service_id));

        // Bind parameters
        $stmt->bindParam(":service_name", $this->service_name);
        $stmt->bindParam(":duration_minutes", $this->duration_minutes);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":service_id", $this->service_id);

        return $stmt->execute();
    }

    // Delete a service
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE service_id = ?";
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->service_id = htmlspecialchars(strip_tags($this->service_id));

        // Bind the service ID
        $stmt->bindParam(1, $this->service_id);

        return $stmt->execute();
    }

    // Fetch services
    public function fetch($id = null)
    {
        $query = "SELECT * FROM " . $this->table_name;
        if ($id) {
            $query .= " WHERE service_id = ?";
        }

        $stmt = $this->conn->prepare($query);

        if ($id) {
            $stmt->bindParam(1, $id);
        }

        $stmt->execute();
        return $stmt;
    }
}
?>
