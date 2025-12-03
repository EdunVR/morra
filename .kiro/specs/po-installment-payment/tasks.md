# Implementation Plan: Purchase Order Installment Payment

## Task List

-   [x] 1. Database Setup

    -   Create migrations for installment payment support
    -   _Requirements: 1.1, 2.1, 2.2_

-   [x] 1.1 Create migration to add payment columns to purchase_order table

    -   Add `total_dibayar` column (DECIMAL)
    -   Add `sisa_pembayaran` column (DECIMAL)
    -   Update existing records with default values
    -   _Requirements: 2.1, 2.2_

-   [x] 1.2 Create migration for po_payment_history table

    -   Create table with all required columns
    -   Add foreign key constraint
    -   Add indexes for performance
    -   _Requirements: 1.1, 1.2_

-   [x] 2. Models and Relationships

    -   Create POPaymentHistory model and update PurchaseOrder model
    -   _Requirements: 1.1, 5.1_

-   [x] 2.1 Create POPaymentHistory model

    -   Define fillable fields
    -   Define casts
    -   Define relationship with PurchaseOrder
    -   _Requirements: 1.1, 1.2_

-   [x] 2.2 Update PurchaseOrder model

    -   Add payment fields to fillable
    -   Add casts for decimal fields
    -   Add paymentHistory relationship
    -   _Requirements: 2.1, 5.1_

-   [x] 3. Backend Payment Processing

    -   Implement payment processing logic in controller
    -   _Requirements: 1.1, 2.1, 2.2, 2.3, 2.4, 2.5_

-   [x] 3.1 Add processPayment method

    -   Validate payment data
    -   Handle file upload and compression
    -   Create payment history record
    -   Update PO total_dibayar and sisa_pembayaran
    -   Update PO status (pending/partial/paid)
    -   Update hutang record
    -   Create journal entry
    -   Return success response
    -   _Requirements: 1.1, 1.2, 1.4, 1.5, 2.1, 2.2, 2.3, 2.4, 2.5, 5.1, 5.2, 5.3, 7.1, 7.2_

-   [x] 3.2 Add getPaymentHistory method

    -   Fetch payment history for PO
    -   Include payment details
    -   Return JSON response
    -   _Requirements: 4.1, 4.2, 4.3, 4.4_

-   [x] 3.3 Add downloadBuktiTransfer method

    -   Validate file exists
    -   Return file download response
    -   _Requirements: 4.3, 4.5_

-   [x] 4. Routes Configuration

    -   Add routes for payment endpoints
    -   _Requirements: 1.1, 4.1_

-   [x] 4.1 Add payment routes

    -   POST /purchase-order/payment
    -   GET /purchase-order/{id}/payment-history
    -   GET /purchase-order/{id}/download-bukti
    -   _Requirements: 1.1, 4.1_

-   [x] 5. Frontend Payment Modal

    -   Create payment modal UI
    -   _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

-   [x] 5.1 Create payment modal HTML

    -   Modal structure
    -   PO information display
    -   Payment summary (total, paid, remaining)
    -   Payment form inputs
    -   File upload with preview
    -   Submit button
    -   _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

-   [x] 5.2 Add payment modal JavaScript

    -   openPaymentModal function
    -   handlePaymentSubmit function
    -   File upload handling
    -   Image preview
    -   Form validation
    -   API call to processPayment
    -   Success/error handling
    -   _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 7.1, 7.2, 7.3, 7.4, 7.5, 8.1, 8.3, 8.4, 8.5_

-   [x] 6. Frontend Payment History Modal

    -   Create payment history modal UI
    -   _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

-   [x] 6.1 Create payment history modal HTML

    -   Modal structure
    -   Payment list table
    -   Total paid summary
    -   View bukti buttons
    -   _Requirements: 4.1, 4.2, 4.3, 4.4_

-   [x] 6.2 Add payment history JavaScript

    -   openPaymentHistoryModal function
    -   Fetch payment history from API
    -   Display payments in table
    -   Handle view bukti button
    -   _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

-   [x] 7. Frontend Bukti Modal

    -   Create bukti display modal
    -   _Requirements: 4.5_

-   [x] 7.1 Create bukti modal HTML

    -   Modal structure
    -   Image/PDF display
    -   Download button
    -   _Requirements: 4.5_

-   [x] 7.2 Add bukti modal JavaScript

    -   openBuktiModal function
    -   Display image or PDF
    -   Handle download
    -   _Requirements: 4.5_

-   [x] 8. Update Action Buttons and Status

    -   Update UI to show payment actions
    -   _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5_

-   [x] 8.1 Update action buttons

    -   Add "Bayar" button for pending/partial status
    -   Add "Lihat Pembayaran" button if has payments
    -   Conditional rendering based on status
    -   _Requirements: 6.3, 6.5_

-   [x] 8.2 Update status badges

    -   Add colors for all statuses
    -   Update status display logic
    -   _Requirements: 6.1, 6.2_

-   [x] 8.3 Update PO list display

    -   Show total_dibayar
    -   Show sisa_pembayaran
    -   Update status indicators
    -   _Requirements: 1.3, 6.1, 6.2_

-   [x] 9. Hutang Integration

    -   Update hutang when payment is made
    -   _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

-   [x] 9.1 Update hutang on payment

    -   Reduce sisa_hutang
    -   Update status if lunas
    -   Maintain data consistency
    -   _Requirements: 5.1, 5.2, 5.3, 5.4_

-   [x] 10. Testing and Validation

    -   Test all payment scenarios
    -   _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 8.1, 8.2, 8.3, 8.4, 8.5_

-   [ ]\* 10.1 Test payment flow

    -   Test single payment
    -   Test multiple payments (installments)
    -   Test full payment
    -   Test overpayment prevention
    -   _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 7.1, 7.2_

-   [ ]\* 10.2 Test file upload

    -   Test image upload
    -   Test PDF upload
    -   Test file size validation
    -   Test file type validation
    -   Test image compression
    -   _Requirements: 1.4, 1.5, 7.3, 7.4, 8.2_

-   [ ]\* 10.3 Test status updates

    -   Test pending → partial
    -   Test partial → paid
    -   Test status display
    -   _Requirements: 2.1, 2.2, 2.3, 6.1, 6.2_

-   [ ]\* 10.4 Test hutang integration

    -   Test hutang creation
    -   Test hutang update on payment
    -   Test hutang status update
    -   _Requirements: 5.1, 5.2, 5.3, 5.4_

-   [ ]\* 10.5 Test UI/UX
    -   Test modal interactions
    -   Test loading indicators
    -   Test toast notifications
    -   Test form validation messages
    -   _Requirements: 7.5, 8.1, 8.3, 8.4, 8.5_

## Notes

-   Tasks marked with \* are optional testing tasks
-   Each task references specific requirements from requirements.md
-   Implementation should follow the order listed
-   Test after each major component is complete
