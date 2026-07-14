USE rokola_ritmopty;

ALTER TABLE entradas
    ADD COLUMN metodo_pago ENUM(
        'yappy',
        'tarjeta',
        'transferencia'
    ) NULL AFTER estado,
    ADD COLUMN numero_factura VARCHAR(40) NULL AFTER metodo_pago,
    ADD COLUMN qr_token CHAR(48) NULL AFTER numero_factura,
    ADD UNIQUE INDEX uk_entradas_factura (numero_factura),
    ADD UNIQUE INDEX uk_entradas_qr (qr_token);

UPDATE entradas
SET metodo_pago = COALESCE(metodo_pago, 'transferencia'),
    numero_factura = COALESCE(
        numero_factura,
        CONCAT('RRP-LEGACY-', LPAD(id, 8, '0'))
    ),
    qr_token = COALESCE(
        qr_token,
        LEFT(SHA2(CONCAT('rokola-', id, '-', fecha_hora), 256), 48)
    );

CREATE TABLE entrada_asientos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entrada_id BIGINT UNSIGNED NOT NULL,
    evento_id INT UNSIGNED NOT NULL,
    asiento VARCHAR(10) NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_entrada_asientos_entrada
        FOREIGN KEY (entrada_id)
        REFERENCES entradas(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_entrada_asientos_evento
        FOREIGN KEY (evento_id)
        REFERENCES eventos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT uk_evento_asiento UNIQUE (evento_id, asiento),
    INDEX idx_entrada_asientos_entrada (entrada_id)
) ENGINE=InnoDB;
