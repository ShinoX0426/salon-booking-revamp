<?php

class User
{
    private $conn;
    private $table_name = "Users";

    public $user_id;
    public $FullName;
    public $Username;
    public $PasswordHash;
    public $Email;
    public $Phone;
    public $UserType;
    public $LastLogin;
    public $IsActive;
    public $CreatedAt;
    public $UpdatedAt;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create a new user
    public function add()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET FullName=:full_name, Username=:username, PasswordHash=:passwordhash, 
                      Email=:email, Phone=:phone, UserType=:usertype, IsActive=:isactive";

        $stmt = $this->conn->prepare($query);

        // Clean and sanitize input
        $this->FullName = htmlspecialchars(strip_tags($this->FullName));
        $this->Username = htmlspecialchars(strip_tags($this->Username));
        $this->PasswordHash = htmlspecialchars(strip_tags($this->PasswordHash));
        $this->Email = htmlspecialchars(strip_tags($this->Email));
        $this->Phone = htmlspecialchars(strip_tags($this->Phone));
        $this->UserType = htmlspecialchars(strip_tags($this->UserType));
        $this->IsActive = $this->IsActive ? 1 : 0;

        // Bind values
        $stmt->bindParam(":full_name", $this->FullName);
        $stmt->bindParam(":username", $this->Username);
        $stmt->bindParam(":passwordhash", $this->PasswordHash);
        $stmt->bindParam(":email", $this->Email);
        $stmt->bindParam(":phone", $this->Phone);
        $stmt->bindParam(":usertype", $this->UserType);
        $stmt->bindParam(":isactive", $this->IsActive);

        return $stmt->execute();
    }

    // Update user
    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                  SET FullName=:full_name, Username=:username, 
                      Email=:email, Phone=:phone, UserType=:usertype, 
                      IsActive=:isactive, LastLogin=:lastlogin
                  WHERE user_id=:userid";

        $stmt = $this->conn->prepare($query);

        // Clean and sanitize input
        $this->FullName = htmlspecialchars(strip_tags($this->FullName));
        $this->Username = htmlspecialchars(strip_tags($this->Username));
        $this->Email = htmlspecialchars(strip_tags($this->Email));
        $this->Phone = htmlspecialchars(strip_tags($this->Phone));
        $this->UserType = htmlspecialchars(strip_tags($this->UserType));
        $this->IsActive = $this->IsActive ? 1 : 0;
        $this->LastLogin = htmlspecialchars(strip_tags($this->LastLogin));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        // Bind values
        $stmt->bindParam(":full_name", $this->FullName);
        $stmt->bindParam(":username", $this->Username);
        $stmt->bindParam(":email", $this->Email);
        $stmt->bindParam(":phone", $this->Phone);
        $stmt->bindParam(":usertype", $this->UserType);
        $stmt->bindParam(":isactive", $this->IsActive);
        $stmt->bindParam(":lastlogin", $this->LastLogin);
        $stmt->bindParam(":userid", $this->user_id);

        return $stmt->execute();
    }

    // Delete user
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :userid";
        $stmt = $this->conn->prepare($query);
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $stmt->bindParam(':userid', $this->user_id);

        return $stmt->execute();
    }

    public function fetchAllUsers($search = '', $orderBy = 'user_id', $orderDir = 'ASC', $userTypeFilter = 'all'){
    $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";

    // Add search condition
    if (!empty($search)) {
        $query .= " AND (Username LIKE :search OR Email LIKE :search)";
    }

    // Add user type filter
    if ($userTypeFilter !== 'all') {
        $query .= " AND UserType = :user_type";
    }

    // Add sorting
    $query .= " ORDER BY " . $orderBy . " " . $orderDir;

    $stmt = $this->conn->prepare($query);

    // Bind search parameter
    if (!empty($search)) {
        $searchParam = '%' . htmlspecialchars(strip_tags($search)) . '%';
        $stmt->bindParam(':search', $searchParam);
    }

    // Bind user type filter parameter
    if ($userTypeFilter !== 'all') {
        $stmt->bindParam(':user_type', $userTypeFilter);
    }

    $stmt->execute();
    return $stmt;
    }


    // Read user(s)
    public function fetch($id = null)
    {
        $query = "SELECT user_id, FullName, Username, Email, Phone, UserType, IsActive, LastLogin 
                  FROM " . $this->table_name;
    
        if ($id) {
            $query .= " WHERE user_id = :userid";
        }
    
        $stmt = $this->conn->prepare($query);
    
        if ($id) {
            $stmt->bindParam(':userid', $id);
        }
    
        $stmt->execute();
        return $stmt;
    }

    // Fetch user by username
    public function fetchByUsername($username)
    {
        $query = "SELECT user_id, FullName, Username, PasswordHash, Email, Phone, UserType, IsActive FROM " . $this->table_name . " WHERE Username = :username";

        $stmt = $this->conn->prepare($query);
        $username = htmlspecialchars(strip_tags($username));
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt;
    }

    // Check if email is unique
    public function isUniqueEmail($email)
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE Email = :email";
        $stmt = $this->conn->prepare($query);

        $email = htmlspecialchars(strip_tags($email));
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['count'] == 0);
    }

    public function updateLastLogin()
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET LastLogin = :lastlogin 
                  WHERE user_id = :userid";
    
        $stmt = $this->conn->prepare($query);
    
        // Sanitize input
        $this->LastLogin = htmlspecialchars(strip_tags($this->LastLogin));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
    
        // Bind parameters
        $stmt->bindParam(':lastlogin', $this->LastLogin);
        $stmt->bindParam(':userid', $this->user_id);
    
        // Execute the query
        return $stmt->execute();
    }    

    public function isUniqueUsername($username){
    $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE Username = :username";
    $stmt = $this->conn->prepare($query);

    $username = htmlspecialchars(strip_tags($username));
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($row['count'] == 0);
    }
}
?>
