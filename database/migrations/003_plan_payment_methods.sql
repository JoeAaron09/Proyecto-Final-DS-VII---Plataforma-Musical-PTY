USE rokola_ritmopty;

ALTER TABLE compras
    ADD COLUMN metodo_pago ENUM(
        'yappy',
        'tarjeta',
        'transferencia'
    ) NULL AFTER estado;

UPDATE compras
SET metodo_pago = 'transferencia'
WHERE metodo_pago IS NULL;
