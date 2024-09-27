<?php

require_once '../config.php';

class Appointment
{
    private $conn;
    private $table_name = "Appointments";

    public $appointment_id;
    public $customer_id;
    public $staff_id;
    public $service_id;
    public $appointment_date;
    public $status;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create a new appointment
    public function add()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET customer_id=:customer_id, staff_id=:staff_id, service_id=:service_id, appointment_date=:appointment_date, status=:status";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->customer_id = htmlspecialchars(strip_tags($this->customer_id));
        $this->staff_id = htmlspecialchars(strip_tags($this->staff_id));
        $this->service_id = htmlspecialchars(strip_tags($this->service_id));
        $this->appointment_date = htmlspecialchars(strip_tags($this->appointment_date));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind parameters
        $stmt->bindParam(":customer_id", $this->customer_id);
        $stmt->bindParam(":staff_id", $this->staff_id);
        $stmt->bindParam(":service_id", $this->service_id);
        $stmt->bindParam(":appointment_date", $this->appointment_date);
        $stmt->bindParam(":status", $this->status);

        return $stmt->execute();
    }

    // Update an appointment
    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET staff_id=:staff_id, service_id=:service_id, appointment_date=:appointment_date, status=:status
                  WHERE appointment_id=:appointment_id";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->staff_id = htmlspecialchars(strip_tags($this->staff_id));
        $this->service_id = htmlspecialchars(strip_tags($this->service_id));
        $this->appointment_date = htmlspecialchars(strip_tags($this->appointment_date));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->appointment_id = htmlspecialchars(strip_tags($this->appointment_id));

        // Bind parameters
        $stmt->bindParam(":staff_id", $this->staff_id);
        $stmt->bindParam(":service_id", $this->service_id);
        $stmt->bindParam(":appointment_date", $this->appointment_date);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":appointment_id", $this->appointment_id);

        return $stmt->execute();
    }

    // Delete an appointment
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE appointment_id = ?";
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->appointment_id = htmlspecialchars(strip_tags($this->appointment_id));

        // Bind the appointment ID
        $stmt->bindParam(1, $this->appointment_id);

        return $stmt->execute();
    }

    // Fetch appointments
    public function fetch($id = null)
    {
        $query = "SELECT * FROM " . $this->table_name;
        if ($id) {
            $query .= " WHERE appointment_id = ?";
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