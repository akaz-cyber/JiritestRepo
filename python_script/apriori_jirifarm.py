import pandas as pd
import mysql.connector
from mlxtend.frequent_patterns import apriori, association_rules
from itertools import product
import time

conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="jirinew"
)

cursor = conn.cursor()

cursor.execute("SELECT id, product_id FROM product_variants")
variant_mapping = cursor.fetchall()

prod_to_vars = {}
for var_id, prod_id in variant_mapping:
    if prod_id not in prod_to_vars:
        prod_to_vars[prod_id] = []
    prod_to_vars[prod_id].append(var_id)

cursor.execute("SELECT id, cat_id FROM products")
category_mapping = cursor.fetchall()
prod_to_cat = {row[0]: row[1] for row in category_mapping}

query = """
    SELECT d.OrderID, v.product_id
    FROM clean_dataset_apriori d
    JOIN product_variants v ON d.product_variant_id = v.id
"""
df = pd.read_sql(query, conn)

# TAHAP 3 & 4: PENGELOMPOKAN DATA & TRANSFORMASI DATA
# - Pengelompokan Data: Menggunakan fungsi `pivot_table` dengan parameter `index='OrderID'`
#   untuk mengelompokkan baris berdasarkan keranjang per satu kali transaksi[cite: 1].
# - Transformasi Data: Menggunakan `aggfunc=lambda x: 1` dan `fill_value=0` untuk mengubah
#   data mentah menjadi format biner (angka 1 jika dibeli, angka 0 jika tidak)[cite: 1].

basket = df.pivot_table(index='OrderID',
                        columns='product_id',
                        aggfunc=lambda x: 1,
                        fill_value=0)

basket = basket.astype(bool)

print("Sedang mencari parameter Apriori terbaik... (Mohon tunggu)")

print("Mencari aturan asosiasi dengan Support dan Confidence ... (Mohon tunggu)")

start_time = time.time()

frequent_itemsets = apriori(
    basket,
    min_support=0.001,
    use_colnames=True,
    max_len=2
)

if frequent_itemsets.empty:
    print("Tidak ada frequent itemset yang ditemukan.")
    cursor.close()
    conn.close()
    exit()


rules = association_rules(
    frequent_itemsets,
    metric="confidence",
    min_threshold=0.3
)

rules = rules[rules['lift'] > 1]

end_time = time.time()

if rules.empty:
    print("Tidak ada aturan asosiasi yang memenuhi kriteria Support, Confidence, dan Lift saat ini.")
    cursor.close()
    conn.close()
    exit()

runtime = end_time - start_time
rata_rata_lift = rules['lift'].mean()

print(f"\n[+] HASIL UNTUK TABEL EKSPERIMEN:")
print(f"[+] Total Rules Found (Number of rules): {len(rules)}")
print(f"[+] Average Lift (Quality): {rata_rata_lift:.4f}")
print(f"[+] Runtime: {runtime:.4f} detik")
print("-" * 40)

conn.ping(reconnect=True)
cursor = conn.cursor()

cursor.execute("DELETE FROM association_rules")

print("\n=== HASIL APRIORI (GABUNGAN LINTAS KATEGORI & SESAMA KATEGORI) ===")

data_to_insert = []
total_lintas_kategori = 0
total_kategori_sama = 0

for _, row in rules.iterrows():
    if len(row['antecedents']) == 1 and len(row['consequents']) == 1:
        ant_prod = int(list(row['antecedents'])[0])
        con_prod = int(list(row['consequents'])[0])

        if ant_prod != con_prod:
            cat_a = prod_to_cat.get(ant_prod)
            cat_b = prod_to_cat.get(con_prod)

            if cat_a != cat_b:
                label = "[LINTAS KATEGORI]"
                total_lintas_kategori += 1
            else:
                label = "[KATEGORI SAMA]"
                total_kategori_sama += 1

            print(f"{label} Produk {ant_prod} -> Produk {con_prod} | Supp: {row['support']:.3f} | Conf: {row['confidence']:.3f}")
            ant_variants = prod_to_vars.get(ant_prod, [])
            con_variants = prod_to_vars.get(con_prod, [])

            for v_ant in ant_variants:
                for v_con in con_variants:
                    data_to_insert.append((
                        int(v_ant),
                        int(v_con),
                        float(row['support']),
                        float(row['confidence']),
                        float(row['lift'])
                    ))

if data_to_insert:
    insert_query = """
        INSERT INTO association_rules
        (antecedent_variant_id, consequent_variant_id, support, confidence, lift)
        VALUES (%s, %s, %s, %s, %s)
    """
    cursor.executemany(insert_query, data_to_insert)

conn.commit()
cursor.close()
conn.close()

print(f"\n=== REKAPITULASI APRIORI ===")
print(f"Total Kombinasi Lintas Kategori: {total_lintas_kategori} aturan")
print(f"Total Kombinasi Kategori Sama (Beda Produk): {total_kategori_sama} aturan")
print(f"Berhasil menyimpan {len(data_to_insert)} baris aturan (sudah disebar ke varian) ke dalam database website!")

# support_values = [0.001, 0.005, 0.01]
# confidence_values = [0.1, 0.2, 0.3]

# best_rules = pd.DataFrame()
# best_params = None
# max_rules = 0
# frequent_itemsets = apriori(
#     basket,
#     min_support=0.001,
#     use_colnames=True,
#     max_len=2
# )

# for min_sup, min_conf in product(support_values, confidence_values):
#     frequent_itemsets = apriori(
#         basket,
#         min_support=min_sup,
#         use_colnames=True,
#         max_len=2
#     )

# if frequent_itemsets.empty:
#     print("Tidak ada frequent itemset yang ditemukan.")
# else:
#     rules = association_rules(
#         frequent_itemsets,
#         metric="confidence",
#         min_threshold=0.3
#     )

#     rules = rules[rules['lift'] > 1]

# if len(rules) > max_rules:
#         best_rules = rules
#         best_params = (min_sup, min_conf)
#         max_rules = len(rules)

# if max_rules == 0 or best_rules.empty:
#     print("Tidak ada aturan asosiasi yang ditemukan dengan pilihan parameter saat ini.")
#     cursor.close()
#     conn.close()
#     exit()

# print(f"\n[+] Best Parameters: min_support={best_params[0]}, min_confidence={best_params[1]}")
# print(f"[+] Total Rules Found: {max_rules}")

# rules = best_rules

# conn.ping(reconnect=True)
# cursor = conn.cursor()

# cursor.execute("DELETE FROM association_rules")

# print("\n=== HASIL APRIORI (GABUNGAN LINTAS KATEGORI & SESAMA KATEGORI) ===")

# data_to_insert = []
# total_lintas_kategori = 0
# total_kategori_sama = 0

# for _, row in rules.iterrows():
#     if len(row['antecedents']) == 1 and len(row['consequents']) == 1:
#         ant_prod = int(list(row['antecedents'])[0])
#         con_prod = int(list(row['consequents'])[0])

#         if ant_prod != con_prod:
#             cat_a = prod_to_cat.get(ant_prod)
#             cat_b = prod_to_cat.get(con_prod)

#             if cat_a != cat_b:
#                 label = "[LINTAS KATEGORI]"
#                 total_lintas_kategori += 1
#             else:
#                 label = "[KATEGORI SAMA]"
#                 total_kategori_sama += 1

#             print(f"{label} Produk {ant_prod} -> Produk {con_prod} | Supp: {row['support']:.3f} | Conf: {row['confidence']:.3f}")
#             ant_variants = prod_to_vars.get(ant_prod, [])
#             con_variants = prod_to_vars.get(con_prod, [])

#             for v_ant in ant_variants:
#                 for v_con in con_variants:
#                     data_to_insert.append((
#                         int(v_ant),
#                         int(v_con),
#                         float(row['support']),
#                         float(row['confidence']),
#                         float(row['lift'])
#                     ))

# if data_to_insert:
#     insert_query = """
#         INSERT INTO association_rules
#         (antecedent_variant_id, consequent_variant_id, support, confidence, lift)
#         VALUES (%s, %s, %s, %s, %s)
#     """
#     cursor.executemany(insert_query, data_to_insert)

# conn.commit()
# cursor.close()
# conn.close()

# print(f"\n=== REKAPITULASI APRIORI ===")
# print(f"Total Kombinasi Lintas Kategori: {total_lintas_kategori} aturan")
# print(f"Total Kombinasi Kategori Sama (Beda Produk): {total_kategori_sama} aturan")
# print(f"Berhasil menyimpan {len(data_to_insert)} baris aturan (sudah disebar ke varian) ke dalam database website!")