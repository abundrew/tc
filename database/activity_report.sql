SELECT
    p.employee_employee_id,
    e.first_name,
    e.last_name,
    s.code AS store_code,
    j.description,
    p.punch_time,
    p.punch_io
FROM
    punch p
    JOIN employee e ON (e.employee_id = p.employee_employee_id)
    JOIN job j ON (j.job_id = e.job_job_id)
    JOIN store s ON (s.store_id = e.store_store_id)
WHERE
    p.store_store_id = 1 AND
    p.punch_date = '2010-06-28'
ORDER BY
    e.last_name,
    e.first_name,
    p.employee_employee_id,
    p.punch_time


SELECT
    SEC_TO_TIME(MIN(wt4.itm)) AS begin_time,
    SEC_TO_TIME(MAX(wt4.otm)) AS end_time,
    SEC_TO_TIME(SUM(wt4.otm - wt4.itm)) AS work_time
FROM (
    SELECT wt3.dt, MIN(wt3.itm) AS itm, IFNULL(wt3.otm, TIME_TO_SEC('14:00:00')) AS otm FROM (
	SELECT wt1.dt, TIME_TO_SEC(wt1.tm) AS itm, MIN(TIME_TO_SEC(wt2.tm)) AS otm
	FROM
	    (SELECT punch_date AS dt, punch_time AS tm, punch_io AS io FROM punch WHERE employee_employee_id = 7 AND store_store_id = 1) wt1 LEFT JOIN (SELECT punch_date AS dt, punch_time AS tm, punch_io AS io FROM punch WHERE employee_employee_id = 7 AND store_store_id = 1) wt2 ON (
		wt1.dt = wt2.dt AND wt1.io = 'I' AND wt2.io = 'O' AND wt1.tm < wt2.tm
	    )
	WHERE (
	    wt1.io = 'I'
	)
	GROUP BY wt1.dt, wt1.tm
    ) wt3
    GROUP BY wt3.dt, wt3.otm
) wt4
WHERE wt4.dt = '2010-06-28'


SELECT
    wt4.st,
    wt4.ei,
    SEC_TO_TIME(MIN(wt4.itm)) AS begin_time,
    SEC_TO_TIME(MAX(wt4.otm)) AS end_time,
    SEC_TO_TIME(SUM(wt4.otm - wt4.itm)) AS work_time
FROM (
    SELECT wt3.st, wt3.ei, wt3.dt, MIN(wt3.itm) AS itm, IFNULL(wt3.otm, TIME_TO_SEC('14:00:00')) AS otm FROM (
	SELECT wt1.st, wt1.ei, wt1.dt, TIME_TO_SEC(wt1.tm) AS itm, MIN(TIME_TO_SEC(wt2.tm)) AS otm
	FROM
	    (SELECT store_store_id AS st, employee_employee_id AS ei, punch_date AS dt, punch_time AS tm, punch_io AS io FROM punch) wt1 LEFT JOIN (SELECT store_store_id AS st, employee_employee_id AS ei, punch_date AS dt, punch_time AS tm, punch_io AS io FROM punch) wt2 ON (
		wt1.st = wt2.st AND wt1.ei = wt2.ei AND wt1.dt = wt2.dt AND wt1.io = 'I' AND wt2.io = 'O' AND wt1.tm < wt2.tm
	    )
	WHERE (
	    wt1.io = 'I'
	)
	GROUP BY wt1.st, wt1.ei, wt1.dt, wt1.tm
    ) wt3
    GROUP BY wt3.st, wt3.ei, wt3.dt, wt3.otm
) wt4
WHERE wt4.st = 1 AND wt4.dt = '2010-06-28'
GROUP BY wt4.ei