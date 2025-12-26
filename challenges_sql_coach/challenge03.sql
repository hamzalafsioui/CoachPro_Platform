--  Challenge 3 — Détection de séances conflictuelles
-- Trouver les séances du même coach qui se chevauchent dans le temps :

-- afficher coach, date, heure début, heure fin, id séance
-- inclure toutes les séances conflictuelles
-- À utiliser : SELF JOIN, calcul heure + duree

SELECT * FROM seances;
SELECT * FROM coachs;


SELECT 
    s1.* 
FROM seances s1
JOIN seances s2
ON s1.coach_id = s2.coach_id
WHERE s1.date_seance = s2.date_seance
AND s1.id <> s2.id

