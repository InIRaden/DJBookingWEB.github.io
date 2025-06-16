-- Create the event type table if it doesn't exist
CREATE TABLE IF NOT EXISTS tbleventtype (
    ID int(10) NOT NULL AUTO_INCREMENT,
    EventType varchar(200) DEFAULT NULL,
    CreationDate timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ID)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DELIMITER //

CREATE TRIGGER before_eventtype_insert
BEFORE INSERT ON tbleventtype
FOR EACH ROW
BEGIN
    IF NEW.CreationDate IS NULL THEN
        SET NEW.CreationDate = NOW();
    END IF;
END;
//

DELIMITER ;