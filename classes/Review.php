<?php

class Review
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    
    public function getCoachReviews(int $coachId)
    {
        
        return [];
    }
}
