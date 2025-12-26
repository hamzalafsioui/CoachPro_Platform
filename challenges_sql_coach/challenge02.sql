-- Challenge 2 — Sportifs les plus actifs
-- Lister les sportifs qui ont réservé le plus de séances par mois, avec :

-- nom, prénom
-- nombre de réservations par mois
-- mois et année
-- ordre décroissant par nombre de réservations
-- À utiliser : JOIN, GROUP BY, DATE_FORMAT, ORDER BY

SELECT * FROM reservations;
SELECT * FROM seances;
SELECT * FROM sportifs;
SELECT * FROM users


SELECT 
    u.nom,
    u.prenom,
    COUNT(r.id) as nbr_res,
    DATE_FORMAT(r.reserved_at,'%m-%Y') as month_year

FROM reservations r
INNER JOIN users u ON u.id = r.sportif_id
WHERE u.role = 'sportif'
GROUP BY u.id,month_year
ORDER BY nbr_res DESC;

