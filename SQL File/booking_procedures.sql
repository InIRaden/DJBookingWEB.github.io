DELIMITER //

-- Stored Procedure untuk mengambil semua booking dengan status yang terformat
CREATE PROCEDURE sp_get_all_bookings()
BEGIN
    -- Variabel untuk loop
    DECLARE done INT DEFAULT FALSE;
    DECLARE booking_id INT;
    DECLARE booking_status VARCHAR(50);
    
    -- Cursor untuk loop melalui booking
    DECLARE booking_cursor CURSOR FOR 
        SELECT ID, Status FROM tblbooking;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Temporary table untuk menyimpan hasil
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_booking_status (
        booking_id INT,
        status_text VARCHAR(50),
        status_class VARCHAR(50)
    );

    -- Buka cursor
    OPEN booking_cursor;
    
    -- Loop melalui semua booking
    read_loop: LOOP
        FETCH booking_cursor INTO booking_id, booking_status;
        
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Conditional untuk menentukan status dan class
        IF booking_status = '' OR booking_status IS NULL THEN
            INSERT INTO temp_booking_status 
            VALUES (booking_id, 'Not Processed Yet', 'badge-warning');
        ELSEIF booking_status = 'Approved' THEN
            INSERT INTO temp_booking_status 
            VALUES (booking_id, booking_status, 'badge-success');
        ELSEIF booking_status = 'Rejected' THEN
            INSERT INTO temp_booking_status 
            VALUES (booking_id, booking_status, 'badge-warning');
        ELSEIF LOWER(booking_status) = 'cancelled' THEN
            INSERT INTO temp_booking_status 
            VALUES (booking_id, booking_status, 'badge-danger');
        END IF;
    END LOOP;

    -- Tutup cursor
    CLOSE booking_cursor;

    -- Return hasil final dengan join
    SELECT 
        b.*,
        COALESCE(bs.status_text, 'Unknown') as formatted_status,
        COALESCE(bs.status_class, 'badge-secondary') as status_class
    FROM tblbooking b
    LEFT JOIN temp_booking_status bs ON b.ID = bs.booking_id
    ORDER BY b.BookingDate DESC;

    -- Bersihkan temporary table
    DROP TEMPORARY TABLE IF EXISTS temp_booking_status;
END //

-- Stored Procedure untuk mendapatkan ringkasan booking
CREATE PROCEDURE sp_get_booking_summary()
BEGIN
    DECLARE total_bookings INT;
    DECLARE approved_bookings INT;
    DECLARE pending_bookings INT;
    DECLARE cancelled_bookings INT;

    -- Hitung total booking
    SELECT COUNT(*) INTO total_bookings FROM tblbooking;
    
    -- Hitung booking yang disetujui
    SELECT COUNT(*) INTO approved_bookings 
    FROM tblbooking WHERE Status = 'Approved';
    
    -- Hitung booking yang pending
    SELECT COUNT(*) INTO pending_bookings 
    FROM tblbooking WHERE Status = '' OR Status IS NULL;
    
    -- Hitung booking yang dibatalkan
    SELECT COUNT(*) INTO cancelled_bookings 
    FROM tblbooking WHERE LOWER(Status) = 'cancelled';

    -- Return ringkasan
    SELECT 
        total_bookings as 'Total',
        approved_bookings as 'Approved',
        pending_bookings as 'Pending',
        cancelled_bookings as 'Cancelled';
END //

DELIMITER ;
