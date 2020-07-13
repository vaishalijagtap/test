<?php


class campaignAPI
{
    private $connect = '';
    
    function __construct()
    {
        $this->connectToDb();
    }
    
    function connectToDb()
    {
        // Connect to Database
        $this->connect = new PDO("mysql:host=localhost;dbname=campaign", "test", "");
    }
    
    function read()
    {
        $query = "SELECT * FROM users ORDER BY id";
        $statement = $this->connect->prepare($query);
        $data = array();
        if($statement->execute())
        {
            while($row = $statement->fetch(PDO::FETCH_ASSOC))
            {
                $data[] = $row;
            }
            return $data;
        }
    }
    
   function getDetails($id)
    {
        $query = "SELECT * FROM users WHERE id='".$id."'";
        $statement = $this->connect->prepare($query);
        $data = array();
        if($statement->execute()) {
            foreach($statement->fetchAll() as $row) {
                $data['first_name'] = $row['first_name'];
                $data['last_name'] = $row['last_name'];
                $data['email'] = $row['email'];
                $data['isOptIn'] = $row['isOptIn']==1? 'Yes' : 'No';
                $data['optInAt'] = $row['optInAt'];
                
            }
            return $data;
        }
    }
    
    function createUser($postArr=array())
    {
        $data = array('message' => 'Unable to create user.');
        if(isset($postArr["first_name"])) {
            // Check if user with same emailid already in database
            $query = "SELECT * FROM users WHERE email='".$postArr['email']."'";
            $statement = $this->connect->prepare($query);
            if($statement->execute()) {
                if(count($statement->fetchAll()) > 0) {
                    $data = array('message' => 'User Already Exists.');
                } else {
                    // Add to Databse otherwise
                    $form_data = array(
                        ':first_name' => $postArr['first_name'],
                        ':last_name' => $postArr['last_name'],
                        ':email'   => $postArr['email'],
                        ':isOptIn' => '1',
                        ':optInAt' => date('Y-m-d H:i:s')
                    );
                    $query = "INSERT INTO users(first_name, last_name, email, isOptIn, optInAt)
                        VALUES (:first_name, :last_name, :email, :isOptIn, :optInAt)";
                    $statement = $this->connect->prepare($query);
                    if($statement->execute($form_data))
                    {
                        $data = array('message' => 'User Created Successfully');
                    }
                }
            }
        }
        
        return $data;
    }
    
    function optOut($postArr)
    {
        if(isset($postArr["email"]))
        {
            
            // Check if user with emailid exists in database
            $query = "SELECT * FROM users WHERE email='".$postArr['email']."'";
            $statement = $this->connect->prepare($query);
            if($statement->execute()) {
                foreach($statement->fetchAll() as $row) {
                    $userId = $row['id'];
                }
                if(empty($userId)) {
                    $data = array('message' => 'User Does not exists.');
                } else {
                    // OptOut user
                    $query = "UPDATE users
                           SET isOptIn = '0'
                           WHERE id = '".$userId."'
                           ";
                    $statement = $this->connect->prepare($query);
                    if($statement->execute())
                    {
                        $data = array('message' => 'User OptOut Successfully');
                    }
                }
            }
        }
        return $data;
    }
}
?>