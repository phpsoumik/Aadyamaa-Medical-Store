-- THIS IS SOUMIK CODE - Add permission for Supp.Rate Comparison Tool
INSERT INTO permissions (name, guard_name, created_at, updated_at) 
VALUES ('Supp.Rate Comparison Tool', 'web', NOW(), NOW());

-- Assign permission to role_id 1 (Admin)
INSERT INTO role_has_permissions (permission_id, role_id) 
SELECT id, 1 FROM permissions WHERE name = 'Supp.Rate Comparison Tool';
