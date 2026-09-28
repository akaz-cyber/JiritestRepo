import pandas as pd
import mysql.connector
from mlxtend.frequent_patterns import apriori, fpgrowth, association_rules
import time
from sklearn.model_selection import train_test_split

def get_data():
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="jirinew"
    )

    query = """
        SELECT d.OrderID, v.product_id
        FROM clean_dataset_apriori d
        JOIN product_variants v ON d.product_variant_id = v.id
    """
    df = pd.read_sql(query, conn)
    conn.close()
    return df

def evaluate_rules(rules, test_baskets, k=5):
    """
    Fungsi untuk menghitung Precision@K, Recall@K, dan F1-Score
    berdasarkan data testing.
    """
    hits = 0
    total_recommended = 0
    total_actual = 0

    for basket in test_baskets:
        if len(basket) < 2:
            continue # Abaikan transaksi yang hanya beli 1 barang (tidak bisa di-cross-sell)

        # Pisahkan 1 barang sebagai "target tebakan" (Actual), sisanya sebagai "input"
        basket_list = list(basket)
        input_items = set(basket_list[:-1])
        target_item = basket_list[-1]

        # Cari rekomendasi berdasarkan aturan yang antecedent-nya ada di input_items
        recommendations = set()
        for _, rule in rules.iterrows():
            if rule['antecedents'].issubset(input_items):
                recommendations.update(rule['consequents'])
                if len(recommendations) >= k:
                    break

        # Ambil Top-K rekomendasi
        recommendations = list(recommendations)[:k]

        if recommendations:
            total_recommended += len(recommendations)
            total_actual += 1
            if target_item in recommendations:
                hits += 1

    precision = hits / total_recommended if total_recommended > 0 else 0
    recall = hits / total_actual if total_actual > 0 else 0
    f1 = (2 * precision * recall) / (precision + recall) if (precision + recall) > 0 else 0

    return precision, recall, f1

def main():
    print("Membaca data dari database jirinew...")
    df = get_data()

    # Kelompokkan data per OrderID menjadi list of products
    transactions = df.groupby('OrderID')['product_id'].apply(set).reset_index()

    # Bagi data: 80% Training (untuk cari rule), 20% Testing (untuk uji tebakan)
    train_data, test_data = train_test_split(transactions, test_size=0.2, random_state=42)

    print(f"Total transaksi Training: {len(train_data)}")
    print(f"Total transaksi Testing: {len(test_data)}")

    # Siapkan data Training ke format biner untuk mlxtend
    basket_train = df[df['OrderID'].isin(train_data['OrderID'])].pivot_table(
        index='OrderID', columns='product_id', aggfunc=lambda x: 1, fill_value=0
    ).astype(bool)

    test_baskets = test_data['product_id'].tolist()

    print("\n" + "="*50)
    print("1. EVALUASI APRIORI (PROPOSED)")
    print("="*50)
    start_time = time.time()

    apriori_frequent = apriori(basket_train, min_support=0.001, use_colnames=True, max_len=2)
    apriori_rules = association_rules(apriori_frequent, metric="confidence", min_threshold=0.3)
    apriori_rules = apriori_rules[apriori_rules['lift'] > 1]

    apriori_runtime = time.time() - start_time
    apriori_p, apriori_r, apriori_f1 = evaluate_rules(apriori_rules, test_baskets, k=5)

    print(f"Runtime: {apriori_runtime:.4f} detik")
    print(f"Total Rules: {len(apriori_rules)}")
    print(f"Precision@5: {apriori_p:.4f}")
    print(f"Recall@5: {apriori_r:.4f}")
    print(f"F1-Score: {apriori_f1:.4f}")

    print("\n" + "="*50)
    print("2. EVALUASI FP-GROWTH (BASELINE)")
    print("="*50)
    start_time = time.time()

    fp_frequent = fpgrowth(basket_train, min_support=0.001, use_colnames=True, max_len=2)
    fp_rules = association_rules(fp_frequent, metric="confidence", min_threshold=0.3)
    fp_rules = fp_rules[fp_rules['lift'] > 1]

    fp_runtime = time.time() - start_time
    fp_p, fp_r, fp_f1 = evaluate_rules(fp_rules, test_baskets, k=5)

    print(f"Runtime: {fp_runtime:.4f} detik")
    print(f"Total Rules: {len(fp_rules)}")
    print(f"Precision@5: {fp_p:.4f}")
    print(f"Recall@5: {fp_r:.4f}")
    print(f"F1-Score: {fp_f1:.4f}")

if __name__ == "__main__":
    main()