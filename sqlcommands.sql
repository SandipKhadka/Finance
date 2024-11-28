CREATE TABLE user_details
(
    user_id    INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(99),
    last_name  VARCHAR(99),
    user_name  VARCHAR(99),
    password   INT
);



CREATE TABLE income_category
(
    category_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(99),
    user_id       int,
    FOREIGN KEY (user_id) REFERENCES user_details (user_id)
);

CREATE TABLE expenses_category
(
    category_id   INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(99),
    user_id       int,
    FOREIGN KEY (user_id) REFERENCES user_details (user_id)
);

CREATE TABLE income
(
    income_id       INT PRIMARY KEY AUTO_INCREMENT,
    income_amount   int,
    income_category INT,
    user_id         INT,
    remarks         varchar(99),
    date            DATE,
    time            time(0),
    FOREIGN KEY (user_id) REFERENCES user_details (user_id),
    FOREIGN KEY (income_category) REFERENCES income_category (category_id)
);

CREATE TABLE expenses
(
    expenses_id       INT PRIMARY KEY AUTO_INCREMENT,
    expenses_amount   INT,
    expenses_category INT,
    user_id           INT,
    remarks           VARCHAR(99),
    date              DATE,
    time              TIME(0),
    FOREIGN KEY (user_id) REFERENCES user_details (user_id),
    FOREIGN KEY (expenses_category) REFERENCES expenses_category (category_id)
);

CREATE TABLE spending_limit
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    category_id int,
    user_id     int,
    amount      int,
    date        DATE NOT NULL,
    FOREIGN KEY (category_id) REFERENCES expenses_category (category_id),
    FOREIGN KEY (user_id) REFERENCES user_details (user_id)
);

# income backup table
CREATE TABLE income_backup
(
    backup_id       INT PRIMARY KEY AUTO_INCREMENT,
    income_amount   int,
    income_category INT,
    user_id         INT,
    remarks         varchar(99),
    date            DATE,
    time            time(0),
    income_id       INT,
    FOREIGN KEY (user_id) REFERENCES user_details (user_id),
    FOREIGN KEY (income_category) REFERENCES income_category (category_id),

    deleted_date    DATE
);

# trigger to create backup of income if user delete income record then insert into income_backup

DELIMITER $$

CREATE TRIGGER backup_income
    BEFORE DELETE
    ON income
    FOR EACH ROW
BEGIN
    INSERT INTO income_backup(income_id,income_amount, income_category, user_id, remarks, date, time, deleted_date)
        VALUE (OLD.income_id,OLD.income_amount, OLD.income_category, OLD.user_id, OLD.remarks, OLD.date, OLD.time, CURRENT_DATE);
end $$
DELIMITER ;


# trigger to restore the deleted income transaction
DELIMITER $$

CREATE TRIGGER restore_income_backup
    BEFORE DELETE
    ON income_backup
    FOR EACH ROW
BEGIN
    INSERT INTO income(income.income_id,income_amount, income_category, user_id, remarks, date, time)
        VALUE (OLD.income_id,OLD.income_amount, OLD.income_category, OLD.user_id, OLD.remarks, OLD.date, OLD.time);
end $$
DELIMITER ;


# expenses backup table

CREATE TABLE expenses_backup
(
    backup_id         INT PRIMARY KEY AUTO_INCREMENT,
    expenses_amount   int,
    expenses_category INT,
    user_id           INT,
    remarks           varchar(99),
    date              DATE,
    time              time(0),
    expenses_id         INT,
    FOREIGN KEY (user_id) REFERENCES user_details (user_id),
    FOREIGN KEY (expenses_category) REFERENCES expenses_category (category_id),

    deleted_date      DATE
);

# trigger to create backup of expenses if user delete income record then insert into expenses_backup
DELIMITER $$

CREATE TRIGGER backup_expenses
    BEFORE DELETE
    ON expenses
    FOR EACH ROW
BEGIN
    INSERT INTO expenses_backup(expenses_id,expenses_amount, expenses_category, user_id, remarks, date, time, deleted_date)
        VALUE (OLD.expenses_id,OLD.expenses_amount, OLD.expenses_category, OLD.user_id, OLD.remarks, OLD.date, OLD.time, CURRENT_DATE);
end $$
DELIMITER ;

# trigger to restore the deleted income transaction
DELIMITER $$

CREATE TRIGGER restore_expenses_backup
    BEFORE DELETE
    ON expenses_backup
    FOR EACH ROW
BEGIN
    INSERT INTO expenses(expenses_id,expenses_amount, expenses_category, user_id, remarks, date, time)
        VALUE (OLD.expenses_id,OLD.expenses_amount, OLD.expenses_category, OLD.user_id, OLD.remarks, OLD.date, OLD.time);
end $$
DELIMITER ;