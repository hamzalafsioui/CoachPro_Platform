<?php

class Stats
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

   
    public function getCoachStats($coachId)
    {
        $stats = [
            'total_sessions' => 0,
            'total_clients' => 0,
            'rating' => 0.0
        ];

        try {
            // Total Sessions (Completed or Confirmed)
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM reservations r
                JOIN statuses s ON r.status_id = s.id
                WHERE r.coach_id = ? AND s.name IN ('confirmed', 'completed')
            ");
            $stmt->execute([$coachId]);
            $row = $stmt->fetch();
            if ($row) {
                $stats['total_sessions'] = $row['total'];
            }

            // Total Unique Clients
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT sportif_id) as total 
                FROM reservations 
                WHERE coach_id = ?
            ");
            $stmt->execute([$coachId]);
            $row = $stmt->fetch();
            if ($row) {
                $stats['total_clients'] = $row['total'];
            }

            // Average Rating 
            $stmt = $this->db->prepare("SELECT rating_avg FROM coach_profiles WHERE id = ?");
            $stmt->execute([$coachId]);
            $row = $stmt->fetch();
            if ($row) {
                $stats['rating'] = number_format((float)$row['rating_avg'], 1);
            }
        } catch (PDOException $e) {
            error_log("Error retrieving coach stats: " . $e->getMessage());
        }

        return $stats;
    }
}
