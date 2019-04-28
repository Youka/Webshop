/* Switch to belonging database */
USE webshop;

/* Roles */
INSERT INTO Roles(Name, Description, Is_Premium, Is_Superuser)
VALUES ('User', 'Normal user', FALSE, FALSE),
	('Premium', 'User with premium advantages', TRUE, FALSE),
	('Administrator', 'User with all privileges', TRUE, TRUE);
/* Users */
call P_CreateUser('Admin', '$1$474.Yx2.$gPNXRlKw91FP0MgHM1dN9.' /* 123 */, 'admin@webshop.com');
UPDATE Users
SET Role='Administrator', Creation_Datetime='1970-01-01 00:00:00'
WHERE Name='Admin';
call P_CreateUser('Christoph', '$1$h31.M73.$w98FroD2uTBNUH6IOzkbE/' /* abc */, 'cs@eis.de');
/* Articles + Storage */
INSERT INTO Articles(Vendor, Model, Category, Description, Price, Image_Url)
VALUES ('Nokia', '8910i', 'Handy', '300 hours standy, 4.5 hours talking', 319.95, 'https://www.inside-handy.de/img/nokia-8910i.jpg'),
	('Pink Lady', 'Green sour', 'Apple', 'Red apple with sweet taste', 0.62, 'https://pbs.twimg.com/profile_images/744811780073914368/0bafSZhe_400x400.jpg'),
	('Lindt', 'Choco balls (Milk)', 'Chocolate', 'Tasty chocolate in form of balls', 2.99, 'https://www.lindt.co.uk/shop/media/catalog/product/cache/1/thumbnail/405x400/9df78eab33525d08d6e5fb8d27136e95/m/a/maxi_ball_2_405x400px.png');
INSERT INTO Storage(Article_Id, Location, Amount)
VALUES (1, 'E1B4', 48),
	(2, 'A2X8', 763),
	(3, 'Z9F7', 200);
/* Discounts */
INSERT INTO Discounts(Article_Id, Reason, Percent, Start_Date, End_Date)
VALUES (3, 'Chocolate week', 25.00, CURDATE(), CURDATE()+INTERVAL 7 DAY);
/* Shopping Card */
call P_IntoShoppingCard(2, 1, 50);
call P_IntoShoppingCard(2, 2, 30);
call P_IntoShoppingCard(2, 3, 1);