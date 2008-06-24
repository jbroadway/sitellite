alter table siteinvoice_invoice change column status status enum('unpaid','paid','cancelled') not null;
