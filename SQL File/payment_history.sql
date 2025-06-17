CREATE TABLE tblpayment_history (
    ID int(11) NOT NULL AUTO_INCREMENT,
    UserID int(11) NOT NULL,
    BookingID varchar(200) NOT NULL,
    Amount decimal(10,2) NOT NULL,
    PaymentMethod varchar(50) NOT NULL,
    PaymentDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PaymentStatus varchar(50) NOT NULL,
    PRIMARY KEY (ID),
    KEY UserID (UserID),
    FOREIGN KEY (UserID) REFERENCES tbluser_login(ID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
