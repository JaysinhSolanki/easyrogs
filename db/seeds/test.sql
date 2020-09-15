INSERT INTO system_groups
  (pkgroupid, groupname, fkaddressbookid, updated_at, updated_by)
  VALUES
  (1, 'Developer', 1, NOW(), 0),
  (2, 'Administrator', 0, NOW(), 0),
  (3, 'Attorney', 1, NOW(), 1),
  (4, 'Support', 1, NOW(), 1);