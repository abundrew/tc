-- ---------------------------------------------------------
-- ADMIN
-- ---------------------------------------------------------
INSERT INTO admin (code, name, pwd_md5) VALUES ('99999900001', 'Angelica Y. Ashley', '827ccb0eea8a706c4c34a16891f84e7b');
INSERT INTO admin (code, name, pwd_md5) VALUES ('99999900002', 'Susan Y. Douglas', '827ccb0eea8a706c4c34a16891f84e7b');
INSERT INTO admin (code, name, pwd_md5) VALUES ('99999900003', 'Bell U. Holland', '827ccb0eea8a706c4c34a16891f84e7b');
-- ---------------------------------------------------------
-- JOB
-- ---------------------------------------------------------
INSERT INTO job (code, description, manager) VALUES ('A', 'Owner', 1);
INSERT INTO job (code, description, manager) VALUES ('B', 'District Manager', 1);
INSERT INTO job (code, description, manager) VALUES ('C', 'Manager', 1);
INSERT INTO job (code, description, manager) VALUES ('D', 'Assistant Manager', 1);
INSERT INTO job (code, description, manager) VALUES ('E', 'Cashier', 1);
INSERT INTO job (code, description, manager) VALUES ('S', 'Sales', 0);
INSERT INTO job (code, description, manager) VALUES ('T', 'Stock', 0);
-- ---------------------------------------------------------
-- STORE
-- ---------------------------------------------------------
INSERT INTO store (code, description) VALUES ('0008', 'Store 0008');
INSERT INTO store (code, description) VALUES ('0014', 'Store 0014');
INSERT INTO store (code, description) VALUES ('0100', 'Store 0100');
-- ---------------------------------------------------------
-- MANAGER
-- ---------------------------------------------------------
INSERT INTO employee (code, first_name, last_name, pwd_md5, store_store_id, job_job_id) VALUES (
    '99999900011', 'Sebastian', 'Armstrong', '827ccb0eea8a706c4c34a16891f84e7b', 1, 3
);
INSERT INTO employee (code, first_name, last_name, pwd_md5, store_store_id, job_job_id) VALUES (
    '99999900012', 'Quail', 'Frank', '827ccb0eea8a706c4c34a16891f84e7b', 1, 4
);
INSERT INTO employee (code, first_name, last_name, pwd_md5, store_store_id, job_job_id) VALUES (
    '99999900013', 'Tanya', 'Whitley', '827ccb0eea8a706c4c34a16891f84e7b', 2, 5
);
INSERT INTO employee (code, first_name, last_name, pwd_md5, store_store_id, job_job_id) VALUES (
    '99999900014', 'Melanie', 'Ford', '827ccb0eea8a706c4c34a16891f84e7b', 2, 3
);
INSERT INTO employee (code, first_name, last_name, pwd_md5, store_store_id, job_job_id) VALUES (
    '99999900015', 'Geoffrey', 'Butler', '827ccb0eea8a706c4c34a16891f84e7b', 3, 4
);
INSERT INTO employee (code, first_name, last_name, pwd_md5, store_store_id, job_job_id) VALUES (
    '99999900016', 'David', 'Warner', '827ccb0eea8a706c4c34a16891f84e7b', 3, 5
);
-- ---------------------------------------------------------
-- EMPLOYEE
-- ---------------------------------------------------------
INSERT INTO employee (code, first_name, last_name, pwd_md5, store_store_id, job_job_id) VALUES (
    '99999900111', 'Keaton', 'Zamora', '827ccb0eea8a706c4c34a16891f84e7b', 1, 6
);
INSERT INTO employee (code, first_name, last_name, pwd_md5, store_store_id, job_job_id) VALUES (
    '99999900112', 'Joelle', 'Stone', '827ccb0eea8a706c4c34a16891f84e7b', 1, 7
);
INSERT INTO employee (code, first_name, last_name, pwd_md5, store_store_id, job_job_id) VALUES (
    '99999900113', 'Igor', 'William', '827ccb0eea8a706c4c34a16891f84e7b', 2, 6
);
INSERT INTO employee (code, first_name, last_name, pwd_md5, store_store_id, job_job_id) VALUES (
    '99999900114', 'Connor', 'Sandoval', '827ccb0eea8a706c4c34a16891f84e7b', 2, 7
);
INSERT INTO employee (code, first_name, last_name, pwd_md5, store_store_id, job_job_id) VALUES (
    '99999900115', 'Orla', 'Mayo', '827ccb0eea8a706c4c34a16891f84e7b', 3, 6
);
INSERT INTO employee (code, first_name, last_name, pwd_md5, store_store_id, job_job_id) VALUES (
    '99999900116', 'Priscilla', 'James', '827ccb0eea8a706c4c34a16891f84e7b', 3, 7
);
-- ---------------------------------------------------------
-- PUNCH
-- ---------------------------------------------------------
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '09:00:00', 'I', 1, 1, 1);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '19:00:00', 'O', 1, 1, 1);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '09:00:00', 'I', 2, 2, 2);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '19:00:00', 'O', 2, 2, 2);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '09:00:00', 'I', 3, 3, 3);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '19:00:00', 'O', 3, 3, 3);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '09:00:00', 'I', 4, 1, 4);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '19:00:00', 'O', 4, 1, 4);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '09:00:00', 'I', 5, 2, 5);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '19:00:00', 'O', 5, 2, 5);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '09:00:00', 'I', 6, 3, 6);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '19:00:00', 'O', 6, 3, 6);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '09:00:00', 'I', 7, 1, 1);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '19:00:00', 'O', 7, 1, 1);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '09:00:00', 'I', 8, 2, 2);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '19:00:00', 'O', 8, 2, 2);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '09:00:00', 'I', 9, 3, 3);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '19:00:00', 'O', 9, 3, 3);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '09:00:00', 'I', 10, 1, 4);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '19:00:00', 'O', 10, 1, 4);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '09:00:00', 'I', 11, 2, 5);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '19:00:00', 'O', 11, 2, 5);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '09:00:00', 'I', 12, 3, 6);
INSERT INTO punch (punch_date, punch_time, punch_io, employee_employee_id, store_store_id, manager_employee_id) VALUES (
    '2010-06-26', '19:00:00', 'O', 12, 3, 6);
-- ---------------------------------------------------------
-- PERIOD
-- ---------------------------------------------------------
INSERT INTO period (period_number, started, ended) VALUES (1, '2010-06-03', '2010-06-16');
INSERT INTO period (period_number, started, ended) VALUES (2, '2010-06-17', '2010-06-30');
INSERT INTO period (period_number, started, ended) VALUES (3, '2010-07-01', '2010-07-14');
