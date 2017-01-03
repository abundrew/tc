
------------------------------------

CREATE TABLE wt (
	id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	dt DATE NOT NULL,
	tm TIME NOT NULL,
	io CHAR(1) NOT NULL,
    PRIMARY KEY(id)
);

------------------------------------

DELETE FROM wt;

------------------------------------

INSERT INTO wt (dt, tm, io) VALUES ('2010-06-02', '10:00:00', 'I');
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-02', '11:00:00', 'O'); --
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-02', '12:00:00', 'I');
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-02', '13:00:00', 'O'); --
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-02', '14:00:00', 'I');
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-02', '15:00:00', 'O'); --
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-02', '16:00:00', 'O'); --
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-03', '10:00:00', 'I');
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-03', '11:00:00', 'O'); --
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-03', '12:00:00', 'I');
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-03', '12:00:00', 'I');
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-03', '12:10:00', 'I');
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-03', '13:00:00', 'O'); --
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-03', '13:10:00', 'O'); --
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-03', '14:00:00', 'I');
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-03', '15:00:00', 'O'); --
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-03', '16:00:00', 'I');

INSERT INTO wt (dt, tm, io) VALUES ('2010-06-04', '12:00:00', 'I');
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-05', '12:00:00', 'O');
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-06', '10:00:00', 'I');
INSERT INTO wt (dt, tm, io) VALUES ('2010-06-06', '19:00:00', 'O'); --

------------------------------------

-- working time in detail --

SELECT wt3.dt, MIN(wt3.itm) AS itm, wt3.otm FROM (
	SELECT wt1.dt, wt1.tm AS itm, MIN(wt2.tm) AS otm
	FROM
	wt wt1 LEFT JOIN wt wt2 ON (
		wt1.dt = wt2.dt AND wt1.io = 'I' AND wt2.io = 'O' AND wt1.tm < wt2.tm
	)
	WHERE (
		wt1.io = 'I'
	)
	GROUP BY wt1.dt, wt1.tm
) wt3
GROUP BY wt3.dt, wt3.otm
ORDER BY wt3.dt, wt3.itm;

------------------------------------

-- working time in total --

SELECT
	wt4.dt AS date,
	SEC_TO_TIME(MIN(itm)) AS begin_time,
	SEC_TO_TIME(MAX(wt4.otm)) AS end_time,
	SEC_TO_TIME(SUM(wt4.otm - wt4.itm)) AS working_time,
	SEC_TO_TIME(MAX(wt4.otm) - MIN(itm) - SUM(wt4.otm - wt4.itm)) AS break_time
FROM (
	SELECT wt3.dt, MIN(wt3.itm) AS itm, IFNULL(wt3.otm, 86400) AS otm FROM (
		SELECT wt1.dt, TIME_TO_SEC(wt1.tm) AS itm, MIN(TIME_TO_SEC(wt2.tm)) AS otm
		FROM
		wt wt1 LEFT JOIN wt wt2 ON (
			wt1.dt = wt2.dt AND wt1.io = 'I' AND wt2.io = 'O' AND wt1.tm < wt2.tm
		)
		WHERE (
			wt1.io = 'I'
		)
		GROUP BY wt1.dt, wt1.tm
	) wt3
	GROUP BY wt3.dt, wt3.otm
) wt4
GROUP BY wt4.dt
ORDER BY wt4.dt

------------------------------------

	    "SELECT ".
		"SEC_TO_TIME(MIN(wt4.itm)) AS begin_time, ".
		"SEC_TO_TIME(MAX(wt4.otm)) AS end_time, ".
		"SEC_TO_TIME(SUM(wt4.otm - wt4.itm)) AS work_time, ".
		"SEC_TO_TIME(MAX(wt4.otm) - MIN(wt4.itm) - SUM(wt4.otm - wt4.itm)) AS break_time ".
	    "FROM ( ".
		"SELECT wt3.dt, MIN(wt3.itm) AS itm, IFNULL(wt3.otm, ".(isset($time) ? "TIME_TO_SEC('".$time->format('H:i:s')."')" : '86400').") AS otm FROM ( ".
		    "SELECT wt1.dt, TIME_TO_SEC(wt1.tm) AS itm, MIN(TIME_TO_SEC(wt2.tm)) AS otm ".
		    "FROM ".
			"(SELECT punch_date AS dt, punch_time AS tm, punch_io AS io FROM punch WHERE employee_id = $employee_id) wt1 LEFT JOIN (SELECT punch_date AS dt, punch_time AS tm, punch_io AS io FROM punch WHERE employee_id = $employee_id) wt2 ON ( ".
			    "wt1.dt = wt2.dt AND wt1.io = 'I' AND wt2.io = 'O' AND wt1.tm < wt2.tm ".
			") ".
		    "WHERE ( ".
			"wt1.io = 'I' ".
		    ") ".
		    "GROUP BY wt1.dt, wt1.tm ".
		") wt3 ".
		"GROUP BY wt3.dt, wt3.otm ".
	    ") wt4 ".
	    "WHERE wt4.dt = '".$date->format('Y-m-d')."'";
