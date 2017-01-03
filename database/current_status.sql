SELECT
    p.employee_employee_id, e.first_name, e.last_name, j.description, p.punch_io, p.punch_date, p.punch_time
FROM punch p JOIN (
    SELECT employee_employee_id, MAX(punch_time) AS max_punch_time
    FROM punch
    WHERE punch_date = '2010-06-26' AND store_store_id = '1'
    GROUP BY employee_employee_id
) x ON (
    x.employee_employee_id = p.employee_employee_id AND
    x.max_punch_time = p.punch_time
) JOIN employee e ON (
    e.employee_id = p.employee_employee_id
) JOIN job j ON (
    j.job_id = e.job_job_id
)
ORDER BY
    e.last_name, e.first_name
