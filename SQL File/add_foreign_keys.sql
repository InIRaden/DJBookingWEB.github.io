-- Add UserID column to tblbooking if it doesn't exist
ALTER TABLE tblbooking
ADD COLUMN IF NOT EXISTS UserID int;

-- Add UserID column to tblpayment if it doesn't exist
ALTER TABLE tblpayment
ADD COLUMN IF NOT EXISTS UserID int;

-- Add foreign key constraint to tblbooking
ALTER TABLE tblbooking
ADD CONSTRAINT fk_booking_user
FOREIGN KEY (UserID) REFERENCES tbluser_login(ID)
ON DELETE RESTRICT
ON UPDATE CASCADE;

-- Add foreign key constraint to tblpayment
ALTER TABLE tblpayment
ADD CONSTRAINT fk_payment_user
FOREIGN KEY (UserID) REFERENCES tbluser_login(ID)
ON DELETE RESTRICT
ON UPDATE CASCADE;

-- Create or modify stored procedure for booking and payment
DELIMITER //

DROP PROCEDURE IF EXISTS CreateBookingAndPayment//

CREATE PROCEDURE CreateBookingAndPayment(
    IN p_bookingid VARCHAR(200),
    IN p_serviceid INT,
    IN p_userid INT,
    IN p_name VARCHAR(200),
    IN p_mobilenumber VARCHAR(20),
    IN p_email VARCHAR(200),
    IN p_eventdate VARCHAR(200),
    IN p_eventstartingtime VARCHAR(200),
    IN p_eventendingtime VARCHAR(200),
    IN p_venueaddress MEDIUMTEXT,
    IN p_eventtype VARCHAR(200),
    IN p_additionalinformation MEDIUMTEXT,
    IN p_paymentmethod VARCHAR(50),
    IN p_amount DECIMAL(10,2),
    IN p_transferbank VARCHAR(50),
    IN p_va_number VARCHAR(50),
    IN p_paymentstatus VARCHAR(50),
    IN p_completeddate VARCHAR(200),
    IN p_installmentcount INT,
    IN p_userpay DECIMAL(10,2)
)
BEGIN
    -- Insert into booking table with UserID
    INSERT INTO tblbooking (
        BookingID, ServiceID, UserID, Name, MobileNumber, 
        Email, EventDate, EventStartingtime, EventEndingtime,
        VenueAddress, EventType, AdditionalInformation, 
        BookingDate, Status
    ) VALUES (
        p_bookingid, p_serviceid, p_userid, p_name, p_mobilenumber,
        p_email, p_eventdate, p_eventstartingtime, p_eventendingtime,
        p_venueaddress, p_eventtype, p_additionalinformation,
        NOW(), 'Pending'
    );

    -- Insert into payment table with UserID
    INSERT INTO tblpayment (
        BookingID, UserID, PaymentMethod, Bank, InstallmentCount,
        AmountPaid, VANumber, PaymentDate, PaymentStatus
    ) VALUES (
        p_bookingid, p_userid, p_paymentmethod, p_transferbank, p_installmentcount,
        p_userpay, p_va_number, NOW(), p_paymentstatus
    );
END//

DELIMITER ;
