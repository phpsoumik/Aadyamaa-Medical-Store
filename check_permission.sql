-- THIS IS SOUMIK CODE - Check permission setup

-- Check if permission exists
SELECT * FROM permissions WHERE name = 'Supp.Rate Comparison Tool';

-- Check role_has_permissions
SELECT * FROM role_has_permissions 
WHERE permission_id = (SELECT id FROM permissions WHERE name = 'Supp.Rate Comparison Tool');

-- Check your user's role
SELECT u.id, u.name, u.role, r.name as role_name 
FROM users u 
LEFT JOIN roles r ON u.role = r.id 
LIMIT 5;

-- Check all permissions for role 1
SELECT p.name 
FROM permissions p
INNER JOIN role_has_permissions rhp ON p.id = rhp.permission_id
WHERE rhp.role_id = 1
ORDER BY p.name;
