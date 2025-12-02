
DELETE FROM cafe_java.orders;

ALTER TABLE cafe_java.orders AUTO_INCREMENT = 1;

SELECT COUNT(*) as total_orders FROM cafe_java.orders;
SELECT 'Orders table reset successfully!' as message;
