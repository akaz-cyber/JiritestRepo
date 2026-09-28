import mysql.connector

def clean_data():
    try:
        # Koneksi langsung ke database tanpa file .env
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="jirinew"
        )
        cursor = conn.cursor()

        # Kosongkan tabel tujuan terlebih dahulu (TRUNCATE)
        cursor.execute("TRUNCATE TABLE clean_dataset_apriori")

        # TAHAP 1 & 2: SELEKSI DATA DAN CLEANING DATA
        insert_query = """
            INSERT INTO clean_dataset_apriori (OrderID, product_variant_id, created_at, updated_at)
            SELECT
                d.order_id AS OrderID,
                v.id AS product_variant_id,
                NOW(),
                NOW()
            FROM apriori_datasets d
            JOIN product_variants v
                ON TRIM(d.index_id) = TRIM(v.sku)
            JOIN products p
                ON v.product_id = p.id
            WHERE d.order_id IS NOT NULL AND d.order_id != '';
        """

        cursor.execute(insert_query)
        conn.commit()

        print("SUCCESS")

    except Exception as e:
        print("ERROR:", str(e))
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

if __name__ == "__main__":
    clean_data()