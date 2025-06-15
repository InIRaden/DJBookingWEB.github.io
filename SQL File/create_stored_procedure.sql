/*
 * Stored Procedure untuk membuat booking dan pembayaran secara atomic (dalam satu transaksi).
 * 
 * Parameter Input:
 * - p_booking_id: ID unik untuk booking
 * - p_service_id: ID layanan yang dipesan
 * - p_name, p_mobile_number, p_email: Data kontak pemesan
 * - p_event_date, p_event_start_time, p_event_end_time: Waktu acara
 * - p_venue_address: Alamat tempat acara
 * - p_event_type: Jenis acara
 * - p_additional_info: Informasi tambahan
 * - p_payment_method: Metode pembayaran (cash/transfer/cicilan)
 * - p_amount: Total biaya
 * - p_transfer_bank: Bank yang dipilih untuk transfer
 * - p_va_number: Nomor Virtual Account
 * - p_payment_status: Status pembayaran
 * - p_completed_date: Tanggal selesai pembayaran
 * - p_installment_count: Jumlah cicilan (jika metode cicilan)
 * - p_user_pay: Jumlah yang dibayarkan user
 * 
 * Cara Kerja:
 * 1. Mulai transaksi database
 * 2. Insert data ke tabel booking
 * 3. Insert data ke tabel payment
 * 4. Jika semua berhasil, commit transaksi
 * 5. Jika ada error, rollback semua perubahan
 */

DELIMITER //

CREATE PROCEDURE CreateBookingAndPayment(
    IN p_booking_id VARCHAR(50),
    IN p_service_id INT,
    IN p_name VARCHAR(200),
    IN p_mobile_number VARCHAR(20),
    IN p_email VARCHAR(200),
    IN p_event_date DATE,
    IN p_event_start_time TIME,
    IN p_event_end_time TIME,
    IN p_venue_address TEXT,
    IN p_event_type VARCHAR(200),
    IN p_additional_info TEXT,
    IN p_payment_method VARCHAR(50),
    IN p_amount DECIMAL(10,2),
    IN p_transfer_bank VARCHAR(50),
    IN p_va_number VARCHAR(50),
    IN p_payment_status VARCHAR(50),
    IN p_completed_date DATETIME,
    IN p_installment_count INT,
    IN p_user_pay DECIMAL(10,2)
)
BEGIN
    DECLARE exit handler for sqlexception
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'An error occurred during booking creation';
    END;

    START TRANSACTION;

    -- Insert into tblbooking
    INSERT INTO tblbooking (
        BookingID,
        ServiceID,
        Name,
        MobileNumber,
        Email,
        EventDate,
        EventStartingtime,
        EventEndingtime,
        VenueAddress,
        EventType,
        AdditionalInformation,
        BookingDate,
        Status
    ) VALUES (
        p_booking_id,
        p_service_id,
        p_name,
        p_mobile_number,
        p_email,
        p_event_date,
        p_event_start_time,
        p_event_end_time,
        p_venue_address,
        p_event_type,
        p_additional_info,
        NOW(),
        NULL
    );

    -- Insert into tblpayment
    INSERT INTO tblpayment (
        BookingID,
        PaymentMethod,
        Amount,
        TransferBank,
        VirtualAccountNumber,
        PaymentStatus,
        PaymentDate,
        CompletedDate,
        InstallmentCount,
        UserPay
    ) VALUES (
        p_booking_id,
        p_payment_method,
        p_amount,
        p_transfer_bank,
        p_va_number,
        p_payment_status,
        NOW(),
        p_completed_date,
        p_installment_count,
        p_user_pay
    );

    COMMIT;
END //

DELIMITER ;
