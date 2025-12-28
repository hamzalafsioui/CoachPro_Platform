<?php

declare(strict_types=1);

class Review
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    private function tableExists(string $tableName): bool
    {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE '{$tableName}'");
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

  
    public function getCoachReviews(int $coachId): array
    {
        $hasRepliesTable = $this->tableExists('review_replies');
        
        if ($hasRepliesTable) {
            $sql = "
                SELECT 
                    r.id,
                    r.rating,
                    r.comment,
                    r.created_at,
                    u.firstname,
                    u.lastname,
                    res.id as reservation_id,
                    GROUP_CONCAT(s.name SEPARATOR ', ') as session_types,
                    rr.id as reply_id,
                    rr.reply_text,
                    rr.created_at as reply_date
                FROM reviews r
                JOIN reservations res ON r.reservation_id = res.id
                JOIN users u ON r.author_id = u.id
                LEFT JOIN coach_sports cs ON cs.coach_id = res.coach_id
                LEFT JOIN sports s ON s.id = cs.sport_id
                LEFT JOIN review_replies rr ON rr.review_id = r.id
                WHERE res.coach_id = ?
                GROUP BY r.id, rr.id
                ORDER BY r.created_at DESC
            ";
        } else {

            $sql = "
                SELECT 
                    r.id,
                    r.rating,
                    r.comment,
                    r.created_at,
                    u.firstname,
                    u.lastname,
                    res.id as reservation_id,
                    GROUP_CONCAT(s.name SEPARATOR ', ') as session_types,
                    NULL as reply_id,
                    NULL as reply_text,
                    NULL as reply_date
                FROM reviews r
                JOIN reservations res ON r.reservation_id = res.id
                JOIN users u ON r.author_id = u.id
                LEFT JOIN coach_sports cs ON cs.coach_id = res.coach_id
                LEFT JOIN sports s ON s.id = cs.sport_id
                WHERE res.coach_id = ?
                GROUP BY r.id
                ORDER BY r.created_at DESC
            ";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$coachId]);

        $reviews = [];
        $reviewMap = [];

        while ($row = $stmt->fetch()) {
            $reviewId = (int)$row['id'];
            
            if (!isset($reviewMap[$reviewId])) {
                // Format date
                $createdAt = strtotime((string)$row['created_at']);
                $today = strtotime(date('Y-m-d'));
                $diffDays = floor(($today - $createdAt) / (60 * 60 * 24));

                if ($diffDays == 0) {
                    $dateDisplay = 'Today';
                } elseif ($diffDays == 1) {
                    $dateDisplay = 'Yesterday';
                } elseif ($diffDays < 7) {
                    $dateDisplay = $diffDays . ' days ago';
                } elseif ($diffDays < 30) {
                    $weeks = floor($diffDays / 7);
                    $dateDisplay = $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
                } else {
                    $dateDisplay = date('M j, Y', $createdAt);
                }

                $reviewMap[$reviewId] = [
                    'id' => $reviewId,
                    'client' => $row['firstname'] . ' ' . $row['lastname'],
                    'avatar' => strtoupper(substr((string)$row['firstname'], 0, 1) . substr((string)$row['lastname'], 0, 1)),
                    'date' => $dateDisplay,
                    'rating' => (int)$row['rating'],
                    'comment' => $row['comment'] ?? '',
                    'session_type' => $row['session_types'] ?: 'Training',
                    'has_reply' => !empty($row['reply_id']),
                    'reply' => null
                ];
            }

            // Add reply if exists
            if (!empty($row['reply_id']) && !$reviewMap[$reviewId]['has_reply']) {
                $replyDate = strtotime((string)$row['reply_date']);
                $diffDays = floor(($today - $replyDate) / (60 * 60 * 24));
                
                if ($diffDays == 0) {
                    $replyDateDisplay = 'Today';
                } elseif ($diffDays == 1) {
                    $replyDateDisplay = 'Yesterday';
                } elseif ($diffDays < 7) {
                    $replyDateDisplay = $diffDays . ' days ago';
                } else {
                    $replyDateDisplay = date('M j, Y', $replyDate);
                }

                $reviewMap[$reviewId]['reply'] = [
                    'id' => (int)$row['reply_id'],
                    'text' => $row['reply_text'],
                    'date' => $replyDateDisplay
                ];
            }
        }

        return array_values($reviewMap);
    }

   
    public function create(int $reservationId, int $authorId, int $rating, string $comment): bool
    {
        try {
            $this->db->beginTransaction();

            $checkStmt = $this->db->prepare("
                SELECT r.id, s.name as status_name
                FROM reservations r
                JOIN statuses s ON r.status_id = s.id
                WHERE r.id = ? AND r.sportif_id = ?
            ");
            $checkStmt->execute([$reservationId, $authorId]);
            $reservation = $checkStmt->fetch();

            if (!$reservation) {
                $this->db->rollBack();
                error_log("Review create failed: Reservation $reservationId not found or doesn't belong to sportif $authorId");
                return false;
            }

            if ($reservation['status_name'] !== 'completed') {
                $this->db->rollBack();
                error_log("Review create failed: Reservation $reservationId status is '{$reservation['status_name']}', not 'completed'");
                return false;
            }

            $existingStmt = $this->db->prepare("SELECT id FROM reviews WHERE reservation_id = ?");
            $existingStmt->execute([$reservationId]);
            if ($existingStmt->fetch()) {
                $this->db->rollBack();
                error_log("Review create failed: Reservation $reservationId already has a review");
                return false;
            }

            $stmt = $this->db->prepare("
                INSERT INTO reviews (reservation_id, author_id, rating, comment)
                VALUES (?, ?, ?, ?)
            ");
            $success = $stmt->execute([$reservationId, $authorId, $rating, $comment]);

            if (!$success) {
                $this->db->rollBack();
                error_log("Review create failed: INSERT statement failed for reservation $reservationId");
                return false;
            }

            $this->db->commit();
            error_log("Review created successfully: Reservation $reservationId, Author $authorId, Rating $rating");
            return true;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("PDO Exception in Review::create - " . $e->getMessage());
            return false;
        }
    }

   
    public function hasReview(int $reservationId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM reviews WHERE reservation_id = ?");
        $stmt->execute([$reservationId]);
        return $stmt->fetch() !== false;
    }

   
    private function createRepliesTable(): bool
    {
        try {
            $sql = "
                CREATE TABLE IF NOT EXISTS review_replies (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    review_id INT NOT NULL,
                    coach_id INT NOT NULL,
                    reply_text TEXT NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                    CONSTRAINT fk_reply_review
                        FOREIGN KEY (review_id) REFERENCES reviews(id)
                        ON DELETE CASCADE,

                    CONSTRAINT fk_reply_coach
                        FOREIGN KEY (coach_id) REFERENCES coach_profiles(id)
                        ON DELETE CASCADE,

                    UNIQUE KEY idx_review_reply (review_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";
            
            $this->db->exec($sql);
            
            // Create indexes
            try {
                $this->db->exec("CREATE INDEX idx_review_replies_review ON review_replies(review_id)");
            } catch (PDOException $e) {
                
            }
            
            try {
                $this->db->exec("CREATE INDEX idx_review_replies_coach ON review_replies(coach_id)");
            } catch (PDOException $e) {
               
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error creating review_replies table: " . $e->getMessage());
            return false;
        }
    }

    
    public function addReply(int $reviewId, int $coachId, string $replyText): bool
    {
        try {
            // Create table if it doesn't exist
            if (!$this->tableExists('review_replies')) {
                if (!$this->createRepliesTable()) {
                    error_log("Failed to create review_replies table");
                    return false;
                }
            }

            // Verify the review belongs to this coach
            $checkStmt = $this->db->prepare("
                SELECT r.id 
                FROM reviews r
                JOIN reservations res ON r.reservation_id = res.id
                WHERE r.id = ? AND res.coach_id = ?
            ");
            $checkStmt->execute([$reviewId, $coachId]);
            
            if (!$checkStmt->fetch()) {
                error_log("Review $reviewId does not belong to coach $coachId");
                return false;
            }

            // Check if reply already exists
            $existingStmt = $this->db->prepare("SELECT id FROM review_replies WHERE review_id = ?");
            $existingStmt->execute([$reviewId]);
            
            if ($existingStmt->fetch()) {
                // Update existing reply
                $stmt = $this->db->prepare("
                    UPDATE review_replies 
                    SET reply_text = ?, updated_at = NOW() 
                    WHERE review_id = ? AND coach_id = ?
                ");
                $result = $stmt->execute([$replyText, $reviewId, $coachId]);
                if (!$result) {
                    error_log("Failed to update reply for review $reviewId");
                }
                return $result;
            } else {
                // Create new reply
                $stmt = $this->db->prepare("
                    INSERT INTO review_replies (review_id, coach_id, reply_text) 
                    VALUES (?, ?, ?)
                ");
                $result = $stmt->execute([$reviewId, $coachId, $replyText]);
                if (!$result) {
                    error_log("Failed to insert reply for review $reviewId");
                }
                return $result;
            }
        } catch (PDOException $e) {
            error_log("PDO Exception in addReply: " . $e->getMessage());
            return false;
        }
    }

   
    public function getCoachReviewStats(int $coachId): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as avg_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as rating_5,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as rating_4,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as rating_3,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as rating_2,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as rating_1
            FROM reviews r
            JOIN reservations res ON r.reservation_id = res.id
            WHERE res.coach_id = ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$coachId]);
        $row = $stmt->fetch();

        if (!$row || (int)$row['total_reviews'] == 0) {
            return [
                'total_reviews' => 0,
                'avg_rating' => 0.0,
                'rating_breakdown' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0]
            ];
        }

        return [
            'total_reviews' => (int)$row['total_reviews'],
            'avg_rating' => round((float)$row['avg_rating'], 1),
            'rating_breakdown' => [
                5 => (int)$row['rating_5'],
                4 => (int)$row['rating_4'],
                3 => (int)$row['rating_3'],
                2 => (int)$row['rating_2'],
                1 => (int)$row['rating_1']
            ]
        ];
    }
}
