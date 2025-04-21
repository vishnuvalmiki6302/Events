<?php

class StudentManagementSystem {
    
    private $studentName;
    private $studentID;
    private $course;
    private $email;

   
    public function __construct($name, $id, $course, $email) {
        $this->studentName = $name;
        $this->studentID = $id;
        $this->course = $course;
        $this->email = $email;
    }

  
    public function setStudentName($name) {
        $this->studentName = $name;
    }

    public function setCourse($course) {
        $this->course = $course;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getStudentName() {
        return $this->studentName;
    }

    public function getStudentID() {
        return $this->studentID;
    }

    public function getCourse() {
        return $this->course;
    }

    public function getEmail() {
        return $this->email;
    }

        
    public function displayStudentInfo() {
        echo "Student Name: " . $this->studentName . "<br>";
        echo "Student ID: " . $this->studentID . "<br>";
        echo "Course: " . $this->course . "<br>";
        echo "Email: " . $this->email . "<br>";
    }
}



$student1 = new StudentManagementSystem("John Doe", "LPU123", "B.Tech CSE", "john@example.com");
$student1->displayStudentInfo();

?>