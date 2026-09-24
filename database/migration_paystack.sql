USE eleven8;

ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'client' AFTER password_hash;

ALTER TABLE bookings
  ADD COLUMN paid_at DATETIME NULL AFTER payment_reference,
  ADD COLUMN paystack_transaction_id BIGINT UNSIGNED NULL AFTER paid_at,
  ADD COLUMN payment_channel VARCHAR(40) NULL AFTER paystack_transaction_id,
  ADD COLUMN payment_payload JSON NULL AFTER payment_channel;

-- After creating your account, promote the intended administrator manually:
-- UPDATE users SET role='admin' WHERE email='your-admin-email@example.com';
