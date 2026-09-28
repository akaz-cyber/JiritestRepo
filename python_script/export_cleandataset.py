import mysql.connector
import csv # Tambahkan library csv

def export_clean_data_to_csv():
    try:
        # Koneksi ke database jirinew
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="jirinew"
        )
        cursor = conn.cursor()

        # Query JOIN untuk mengambil OrderID, ID Variant, dan Nama Produk
        # Catatan: Sesuaikan 'p.name' jika nama kolom di tabel products kamu berbeda (misal: p.title atau p.product_name)
        export_query = """
            SELECT
                c.OrderID,
                c.product_variant_id,
                p.title AS product_name
            FROM clean_dataset_apriori c
            JOIN product_variants v ON c.product_variant_id = v.id
            JOIN products p ON v.product_id = p.id
            ORDER BY c.OrderID;
        """

        cursor.execute(export_query)
        dataset = cursor.fetchall()

        # Tentukan nama file output
        filename = "clean_dataset_apriori_jirifarm.csv"

        # Proses pembuatan file CSV
        with open(filename, mode='w', newline='', encoding='utf-8') as file:
            writer = csv.writer(file)

            # 1. Tulis Header (Baris pertama di Excel/CSV)
            writer.writerow(['OrderID', 'Product Variant ID', 'Nama Produk'])

            # 2. Tulis seluruh baris data
            writer.writerows(dataset)

        print(f"SUCCESS: Data berhasil diexport ke file {filename}")

    except Exception as e:
        print("ERROR:", str(e))
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

if __name__ == "__main__":
    export_clean_data_to_csv()