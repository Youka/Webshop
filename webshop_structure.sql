/* Configure features */
SET storage_engine=INNODB;	/* Required for foreign key support */
/* For newer MySQL versions: SET default_storage_engine=INNODB; */

/* Begin transaction */
START TRANSACTION;


/* Create & switch to new database */
DROP DATABASE IF EXISTS webshop;
CREATE DATABASE webshop;
USE webshop;

/* Tables */
CREATE TABLE Roles (
	/* Columns */
	Name VARCHAR(64) /* PRIMARY KEY */,
	Description VARCHAR(256),
	Is_Premium BOOLEAN NOT NULL,
	Is_Superuser BOOLEAN NOT NULL,
	/* Constraints */
	PRIMARY KEY (Name)
);
CREATE TABLE Users (
	/* Columns */
	Id INT AUTO_INCREMENT /* PRIMARY KEY */,
	Name VARCHAR(64) NOT NULL UNIQUE,
	Password VARCHAR(128) NOT NULL,
	Role VARCHAR(64) NOT NULL /* FOREIGN KEY */,
	Email VARCHAR(64) NOT NULL UNIQUE,
	Creation_Datetime DATETIME NOT NULL,
	/* Constraints */
	PRIMARY KEY (Id),
	CONSTRAINT FK_USERS_ROLE FOREIGN KEY (Role) REFERENCES Roles(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);
CREATE TABLE Articles (
	/* Columns */
	Id INT AUTO_INCREMENT /* PRIMARY KEY */,
	Vendor VARCHAR(64) NOT NULL,
	Model VARCHAR(64) NOT NULL,
	Category VARCHAR(64),
	Description VARCHAR(256),
	Price DECIMAL(12,2) NOT NULL,
	Image_Url VARCHAR(256),
	/* Constraints */
	PRIMARY KEY (Id)
);
CREATE TABLE Storage (
	/* Columns */
	Article_Id INT /* PRIMARY+FOREIGN KEY */,
	Location VARCHAR(64),
	Amount INT NOT NULL,
	/* Constraints */
	PRIMARY KEY (Article_Id),
	CONSTRAINT FK_STORAGE_ARTICLE_ID FOREIGN KEY (Article_Id) REFERENCES Articles(Id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);
CREATE TABLE Discounts (
	/* Columns */
	Article_Id INT /* PRIMARY+FOREIGN KEY */,
	Reason VARCHAR(128),
	Percent TINYINT NOT NULL,
	Start_Date DATE NOT NULL,
	End_Date DATE NOT NULL,
	/* Constraints */
	PRIMARY KEY (Article_Id),
	CONSTRAINT FK_DISCOUNTS_ARTICLE_ID FOREIGN KEY (Article_Id) REFERENCES Articles(Id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);
CREATE TABLE ShoppingCard (
	/* Columns */
	User_Id INT /* PRIMARY+FOREIGN KEY */,
	Article_Id INT /* PRIMARY+FOREIGN KEY */,
	Amount INT NOT NULL,
	/* Constraints */
	PRIMARY KEY (User_Id, Article_Id),
	CONSTRAINT FK_SHOPPINGCARD_USER_ID FOREIGN KEY (User_Id) REFERENCES Users(Id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	CONSTRAINT FK_SHOPPINGCARD_ARTICLE_ID FOREIGN KEY (Article_Id) REFERENCES Articles(Id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);
CREATE TABLE Orders (
	/* Columns */
	Id INT AUTO_INCREMENT /* PRIMARY KEY */,
	User_Id INT NOT NULL /* FOREIGN KEY */,
	Pay_Method VARCHAR(256) NOT NULL,
	Delivery_Address VARCHAR(256) NOT NULL,
	Order_Datetime DATETIME NOT NULL,
	/* Constraints */
	PRIMARY KEY (Id),
	CONSTRAINT FK_ORDERS_USER_ID FOREIGN KEY (User_Id) REFERENCES Users(Id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);
CREATE TABLE OrderDetails (
	/* Columns */
	Order_Id INT NOT NULL /* FOREIGN KEY */,
	Article_Id INT NOT NULL /* FOREIGN KEY */,
	Amount INT NOT NULL,
	/* Constraints */
	CONSTRAINT FK_ORDER_DETAILS_ORDER_ID FOREIGN KEY (Order_Id) REFERENCES Orders(Id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	CONSTRAINT FK_ORDER_DETAILS_ARTICLE_ID FOREIGN KEY (Article_Id) REFERENCES Articles(Id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

/* Views */
CREATE VIEW V_AllArticles AS
	SELECT A.Id, A.Vendor, A.Model, A.Category, A.Description, A.Price, A.Image_Url, S.Location, S.Amount, D.Percent AS Discount
	FROM Articles AS A
	INNER JOIN Storage AS S
		ON A.Id = S.Article_Id
	LEFT JOIN Discounts AS D
		ON A.Id = D.Article_Id AND CURDATE() BETWEEN D.Start_Date AND D.End_Date;
CREATE VIEW V_ActiveDiscounts AS
	SELECT D.Article_Id, D.Reason, D.Percent, D.Start_Date, D.End_Date, A.Vendor, A.Model, A.Category, A.Description, A.Price, A.Image_Url
	FROM Discounts AS D
	INNER JOIN Articles AS A
		ON D.Article_Id = A.Id
	WHERE CURDATE() BETWEEN D.Start_Date AND D.End_Date;

/* Functions */
DELIMITER $$	/* Required for sub-statements in functions */
CREATE FUNCTION F_TotalPriceFromShoppingCard(v_user_id INT)
	RETURNS DECIMAL(12,2)
	BEGIN
		DECLARE total_price DECIMAL(12,2);
		SELECT SUM(S.Amount * A.Price * (CASE WHEN A.Discount IS NULL THEN 1 ELSE 1 - A.Discount / 100.0 END))
		INTO total_Price
		FROM ShoppingCard AS S
		INNER JOIN V_AllArticles AS A
			ON S.Article_Id = A.Id
		WHERE S.User_Id = v_user_id
		LIMIT 1;
		RETURN total_price;
	END$$

DELIMITER ;	/* Reset statement delimiter */

/* Procedures (table return; following CRUD) */
CREATE PROCEDURE P_CreateUser(IN v_name VARCHAR(64), IN v_password VARCHAR(256), IN v_email VARCHAR(64))
	/* Create user */
	INSERT INTO Users(Name, Password, Role, Email, Creation_Datetime)
	VALUES (v_name, v_password, 'User', v_email, NOW());
CREATE PROCEDURE P_GetUser(IN v_name VARCHAR(64))
	/* Read user */
	SELECT U.Id, U.Name, U.Password, U.Role, U.Email, U.Creation_Datetime, R.Description, R.Is_Premium, R.Is_Superuser
	FROM Users AS U
	INNER JOIN Roles AS R
		ON U.Role = R.Name
	WHERE U.Name = v_name
	LIMIT 1;
CREATE PROCEDURE P_UpdateUserPassword(IN v_id INT, IN v_password VARCHAR(256))
	/* Update user */
	UPDATE Users
	SET Password = v_password
	WHERE Id = v_id;
CREATE PROCEDURE P_DeleteUser(IN v_id INT)
	/* Delete user */
	DELETE FROM Users
	WHERE Id = v_id;
CREATE PROCEDURE P_IntoShoppingCard(IN v_user_id INT, IN v_article_id INT, IN v_amount INT)
	/* Create or update shopping card entry */
	INSERT INTO ShoppingCard(User_Id, Article_Id, Amount)
	VALUES (v_user_id, v_article_id, v_amount)
	ON DUPLICATE KEY
	UPDATE Amount=Amount + v_amount;
CREATE PROCEDURE P_GetShoppingCard(IN v_user_id INT)
	/* Read shopping card entries */
	SELECT S.User_Id, S.Article_Id, S.Amount, A.Vendor, A.Model, A.Category, A.Description, A.Price, A.Image_Url, A.Discount
	FROM ShoppingCard AS S
	INNER JOIN V_AllArticles AS A
		ON S.Article_Id = A.Id
	WHERE S.User_Id = v_user_id;
CREATE PROCEDURE P_OutOfShoppingCard(IN v_user_id INT, IN v_article_id INT)
	/* Delete shopping card entry */
	DELETE FROM ShoppingCard
	WHERE User_Id = v_user_id AND Article_Id = v_article_id;
DELIMITER $$	/* Required for sub-statements in procedures */
CREATE PROCEDURE P_OrderFromShoppingCard(IN v_user_id INT, IN v_pay_method VARCHAR(256), IN v_delivery_address VARCHAR(256))
	/* Create order */
	BEGIN
		/* Variables */
		DECLARE v_order_id INT;
		DECLARE v_article_id INT;
		DECLARE v_amount INT;
		/* Prepare shopping card cursor for user */
		DECLARE v_cursor_finished TINYINT DEFAULT 0;
		DECLARE shoppingcard_user_cursor CURSOR FOR
			SELECT Article_Id, Amount
			FROM ShoppingCard
			WHERE User_Id = v_user_id;
		DECLARE CONTINUE HANDLER FOR NOT FOUND
			SET v_cursor_finished = 1;
		/* Register order */
		INSERT INTO Orders(User_Id, Pay_Method, Delivery_Address, Order_Datetime)
		VALUES (v_user_id, v_pay_method, v_delivery_address, NOW());
		SET v_order_id = LAST_INSERT_ID();
		/* Iterate through shopping card entries of user */
		OPEN shoppingcard_user_cursor;
		shoppingcard_user_loop: LOOP
			/* Get entry (if possible) */
			FETCH shoppingcard_user_cursor INTO v_article_id, v_amount;
			IF v_cursor_finished = 1 THEN
				LEAVE shoppingcard_user_loop;
			END IF;
			/* Enough articles in storage? */
			IF v_amount <= (SELECT Amount FROM Storage WHERE Article_Id = v_article_id) THEN
				/* Decrease article amount in storage */
				UPDATE Storage
				SET Amount = Amount - v_amount
				WHERE Article_Id = v_article_id;
				/* Delete entry from shopping card */
				DELETE FROM ShoppingCard
				WHERE User_Id = v_user_id AND Article_Id = v_article_id;
				/* Register order details */
				INSERT INTO OrderDetails(Order_Id, Article_Id, Amount)
				VALUES (v_order_id, v_article_id, v_amount);
			END IF;
		END LOOP;
		CLOSE shoppingcard_user_cursor;
	END$$

DELIMITER ;	/* Reset statement delimiter */
CREATE PROCEDURE P_GetOrders(IN v_user_id INT)
	/* Read orders */
	SELECT O.Id, O.User_Id, O.Pay_Method, O.Delivery_Address, O.Order_Datetime, O.Article_Id, O.Amount, A.Vendor, A.Model, A.Category, A.Description, A.Price, A.Image_Url, D.Percent AS Discount
	FROM Articles AS A
	INNER JOIN (
		SELECT O.Id, O.User_Id, O.Pay_Method, O.Delivery_Address, O.Order_Datetime, OD.Article_Id, OD.Amount
		FROM Orders AS O
		INNER JOIN OrderDetails AS OD
			ON O.Id = OD.Order_Id
		WHERE O.User_Id = v_user_id
	) AS O
		ON O.Article_Id = A.Id
	LEFT JOIN Discounts AS D
		ON D.Article_Id = A.Id AND O.Order_Datetime BETWEEN D.Start_Date AND D.End_Date
	ORDER BY O.Order_Datetime DESC, O.Article_Id ASC;
CREATE PROCEDURE P_LastOrderSummary(IN v_user_id INT)
	/* Read order */
	SELECT O.Id, O.Pay_Method, O.Delivery_Address, O.Order_Datetime,
		GROUP_CONCAT(CONCAT('#', O.Article_Id, ' (', O.Amount, ')') SEPARATOR ' - ') AS Articles,
		SUM(O.Amount * A.Price * (CASE WHEN D.Percent IS NULL THEN 1 ELSE 1 - D.Percent / 100.0 END)) AS Total_Price
	FROM Articles AS A
	INNER JOIN (
		SELECT O.Id, O.Pay_Method, O.Delivery_Address, O.Order_Datetime, OD.Article_Id, OD.Amount
		FROM Orders AS O
		INNER JOIN OrderDetails AS OD
			ON O.Id = OD.Order_Id
		WHERE O.User_Id = v_user_id
	) AS O
		ON O.Article_Id = A.Id
	LEFT JOIN Discounts AS D
		ON D.Article_Id = A.Id AND O.Order_Datetime BETWEEN D.Start_Date AND D.End_Date
	GROUP BY O.Id, O.Pay_Method, O.Delivery_Address, O.Order_Datetime
	ORDER BY O.Order_Datetime DESC
	LIMIT 1;


/* End transaction / flush everything */
COMMIT;