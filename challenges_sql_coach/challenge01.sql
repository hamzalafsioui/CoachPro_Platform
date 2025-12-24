-- Challenge 1 — Top coach par taux de réservation
-- Afficher pour chaque coach :

-- nombre total de séances créées
-- nombre de séances réservées
-- taux de réservation (%)
-- seulement les coachs ayant ≥3 séances
-- À utiliser : JOIN, COUNT, GROUP BY, HAVING

SELECT * FROM seances;
SELECT * FROM reservations;
-- 01 nombre total de séances créées
SELECT COUNT(id) AS total_seances FROM seances ;

--02 nombre de séances réservées
SELECT COUNT(id) AS total_seances_reserve FROM seances
WHERE  statut = 'reservee';

SELECT COUNT(id) AS total_seances_reserve FROM seances
GROUP BY statut
HAVING statut = 'reservee';

-- 03 taux de réservation (%)
 -- Todo --------------
SELECT s.coach_id,
    COUNT(s.id) AS total_seances,
    COUNT(r.id) AS seances_reservees,
    (COUNT(r.id) * 100.0 / COUNT(s.id)) AS taux_reservation
FROM seances s
LEFT JOIN reservations r 
ON s.id = r.seance_id
GROUP BY s.coach_id;


--04 seulement les coachs ayant ≥3 séances
SELECT coach_id,COUNT(coach_id) as total_seances FROM seances 
GROUP BY coach_id HAVING total_seances >= 3;



