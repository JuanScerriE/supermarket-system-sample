-- NOTE: sqlite does not support CREATE SCHEMA it is always created in main
-- "IF NOT EXISTS" makes it safe to run more than once.

CREATE TABLE IF NOT EXISTS supermarkets (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    name          TEXT    NOT NULL UNIQUE
    -- TODO(juan): add location as a separate table in the future
);

CREATE TABLE IF NOT EXISTS products (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    -- products can definitely have the same name
    name          TEXT    NOT NULL,
    image         BLOB,
    image_md5     VARCHAR(34) -- md5 is 128bit = 128/8 = 16bytes = 2 character per byte * 16 = 32, adding +2 for things like null character
    -- TODO(juan): double check if we want this to be hexadecimal or base64 (due to big or little endian)
);

CREATE TABLE IF NOT EXISTS supermarket_products (
    supermarket_id  INTEGER NOT NULL REFERENCES supermarkets(id) ON DELETE CASCADE,
    product_id      INTEGER NOT NULL REFERENCES products(id)     ON DELETE CASCADE,
    stock           INTEGER NOT NULL CHECK (stock >= 0),
    price_cents     INTEGER NOT NULL CHECK (price_cents >= 0),

    PRIMARY KEY (supermarket_id, product_id)
);

-- TODO(juan): test if we need to create an index
