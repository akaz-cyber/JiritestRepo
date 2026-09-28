import mysql.connector

def export_to_basket_orange():
    try:
        # Koneksi ke database jirinew
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="jirinew"
        )
        cursor = conn.cursor()

        # Query GROUP_CONCAT untuk menggabungkan nama produk berdasarkan OrderID
        # Hasilnya nanti: "Produk A, Produk B, Produk C" dalam 1 baris
        query = """
            SELECT
                c.OrderID,
                GROUP_CONCAT(p.title SEPARATOR ', ') as basket_items
            FROM clean_dataset_apriori c
            JOIN product_variants v ON c.product_variant_id = v.id
            JOIN products p ON v.product_id = p.id
            GROUP BY c.OrderID
            ORDER BY c.OrderID;
        """

        cursor.execute(query)
        transactions = cursor.fetchall()

        # Ekstensi file diubah menjadi .basket agar dibaca otomatis oleh Orange
        filename = "dataset_jirifarm.basket"

        # Proses pembuatan file .basket
        with open(filename, mode='w', encoding='utf-8') as file:
            for row in transactions:
                # row[1] berisi kumpulan produk yang dipisahkan koma
                file.write(f"{row[1]}\n")

        print(f"SUCCESS: Data transaksi berhasil diexport ke {filename}")

    except Exception as e:
        print("ERROR:", str(e))
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

if __name__ == "__main__":
    export_to_basket_orange()