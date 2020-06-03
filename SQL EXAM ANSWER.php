1. Write a query to display the ff columns ID (should start with T + 11 digits of trn_teacher.id with leading zeros like 'T00000088424'), Nickname, Status and Roles (like Trainer/Assessor/Staff) using table trn_teacher and
trn_teacher_role. 

ANSWER: 

SELECT CONCAT('T000000', t1.id) as 'ID', t1.nickname as 'NICKNAME', CASE WHEN t1.status = 0 THEN 'Discontinued' WHEN t1.status = 1 THEN 'Active' WHEN t1.status = 2 THEN 'Deactivated' ELSE 'Undefined Status' END AS STATUS, CASE WHEN t2.role = 1 THEN 'Trainer' WHEN t2.role = 2 THEN 'Assessor' WHEN t2.role = 3 THEN 'Staff' ELSE 'Undefined Role' END AS ROLES FROM trn_teacher t1 LEFT JOIN trn_teacher_role t2 ON t1.id = t2.teacher_id


2. Write a query to display the ff columns ID (from teacher.id), Nickname, Open (total open slots from trn_teacher_time_table), Reserved (total reserved slots from trn_teacher_time_table), Taught (total taught from trn_evaluation) and NoShow (total no_show from trn_evaluation) using all tables above. Should show only those who are active (trn_teacher.status = 1 or 2) and those who have both Trainer and Assessor role.

ANSWER:
SELECT 
	t1.id as 'ID',
	t1.nickname as 'NICKNAME',
    SUM(
        	CASE 
        		WHEN t2.status = 1
        		THEN 1
        		ELSE 0
        	END
    	) as 'OPEN',
   SUM(
   			CASE
       			WHEN t2.status = 3
       			THEN 1
       			ELSE 0
       		END
   ) AS 'RESERVED',
   t3_2.TAUGHT,
   t3_2.NO_SHOW
FROM 
	(SELECT aggr_tbl_1.teacher_id FROM 
		(SELECT DISTINCT aggr_tbl_1_2.teacher_id FROM trn_teacher_role aggr_tbl_1_2 WHERE aggr_tbl_1_2.role = '1') as aggr_tbl_1
	INNER JOIN 
     	(SELECT DISTINCT aggr_tbl_2_2.teacher_id FROM trn_teacher_role aggr_tbl_2_2 WHERE aggr_tbl_2_2.role = '2') aggr_tbl_2
		ON aggr_tbl_1.teacher_id = aggr_tbl_2.teacher_id) aggr_teacher_role_tbl
	LEFT JOIN trn_teacher t1 
		ON t1.id = aggr_teacher_role_tbl.teacher_id
    LEFT JOIN trn_time_table t2
        ON t1.id = t2.teacher_id
    LEFT JOIN 
        (SELECT 
            SUM(
                CASE
                    WHEN t3.result = 1
                    THEN 1
                    ELSE 0
                END
               ) AS 'TAUGHT', 
             SUM(
                    CASE
                        WHEN t3.result = 2
                        THEN 1
                        ELSE 0
                    END
                   ) AS 'NO_SHOW', 
            t3.teacher_id
         FROM trn_evaluation t3 GROUP BY t3.teacher_id) AS t3_2
         ON t3_2.teacher_id = t1.id
WHERE t1.status IN ('1', '2')
GROUP BY t1.id
