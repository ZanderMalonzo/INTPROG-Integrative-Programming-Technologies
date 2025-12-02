
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `phone_number`, `role`) 
SELECT 'admin', 'admin@cafejava.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', '09000000000', 'admin'
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `username` = 'admin' OR `email` = 'admin@cafejava.com');


