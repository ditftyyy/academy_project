from flask import Flask, request, jsonify
from flask_cors import CORS
import numpy as np
import pickle
import os
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler

app = Flask(__name__)
CORS(app)

MODEL_PATH = 'kmeans_model.pkl'
SCALER_PATH = 'scaler.pkl'
ORDER_PATH = 'cluster_order.pkl'

def load_or_train_model(students_data=None):
    global model, scaler, cluster_order
    if os.path.exists(MODEL_PATH) and os.path.exists(SCALER_PATH) and os.path.exists(ORDER_PATH):
        with open(MODEL_PATH, 'rb') as f:
            model = pickle.load(f)
        with open(SCALER_PATH, 'rb') as f:
            scaler = pickle.load(f)
        with open(ORDER_PATH, 'rb') as f:
            cluster_order = pickle.load(f)
        print("Model loaded from disk.")
        return True
    else:
        if not students_data:
            return False
        scores = [[float(s['math_score']), float(s['reading_score']), float(s['writing_score'])] for s in students_data]
        X = np.array(scores)
        scaler = StandardScaler()
        X_scaled = scaler.fit_transform(X)
        model = KMeans(n_clusters=3, random_state=42, n_init=10)
        model.fit(X_scaled)
        # Hitung rata-rata nilai per cluster center
        centers = model.cluster_centers_
        means = np.mean(centers, axis=1)  # total rata-rata math+reading+writing
        # Urutkan dari tertinggi ke terendah: [prestasi, sedang, butuh]
        cluster_order = np.argsort(means)[::-1].tolist()
        with open(MODEL_PATH, 'wb') as f:
            pickle.dump(model, f)
        with open(SCALER_PATH, 'wb') as f:
            pickle.dump(scaler, f)
        with open(ORDER_PATH, 'wb') as f:
            pickle.dump(cluster_order, f)
        print(f"Model trained. Cluster order (prestasi->sedang->butuh): {cluster_order}")
        return True

@app.route('/cluster', methods=['POST'])
def cluster_students():
    try:
        data = request.get_json()
        students = data.get('students', [])
        if not students:
            return jsonify({'error': 'Data siswa kosong'}), 400

        load_or_train_model(students)

        scores = [[float(s['math_score']), float(s['reading_score']), float(s['writing_score'])] for s in students]
        X = np.array(scores)
        X_scaled = scaler.transform(X)
        raw_labels = model.predict(X_scaled)

        # Mapping raw_label ke final_label (0=prestasi, 1=sedang, 2=butuh)
        mapping = {raw: final for final, raw in enumerate(cluster_order)}
        final_labels = [mapping[label] for label in raw_labels]

        cluster_names = {0: "Siswa Berprestasi", 1: "Siswa Rata-rata/Cukup", 2: "Siswa Butuh Bimbingan"}

        result = []
        for i, s in enumerate(students):
            result.append({
                'student_id': s.get('id'),
                'name': s.get('name', f'Siswa_{i+1}'),
                'cluster': int(final_labels[i]),
                'cluster_name': cluster_names[final_labels[i]]
            })

        summary = {}
        for cid in [0,1,2]:
            indices = [i for i, lab in enumerate(final_labels) if lab == cid]
            if indices:
                avg_math = np.mean([students[i]['math_score'] for i in indices])
                avg_read = np.mean([students[i]['reading_score'] for i in indices])
                avg_write = np.mean([students[i]['writing_score'] for i in indices])
                summary[cluster_names[cid]] = {
                    'jumlah': len(indices),
                    'rata_rata_math': round(avg_math, 2),
                    'rata_rata_reading': round(avg_read, 2),
                    'rata_rata_writing': round(avg_write, 2)
                }
            else:
                summary[cluster_names[cid]] = {'jumlah': 0, 'rata_rata_math': 0, 'rata_rata_reading': 0, 'rata_rata_writing': 0}

        return jsonify({'status': 'success', 'students': result, 'summary': summary})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/predict', methods=['POST'])
def predict_single():
    try:
        if not os.path.exists(MODEL_PATH) or not os.path.exists(SCALER_PATH) or not os.path.exists(ORDER_PATH):
            return jsonify({'error': 'Model belum dilatih. Jalankan /cluster dulu.'}), 400

        with open(MODEL_PATH, 'rb') as f:
            model = pickle.load(f)
        with open(SCALER_PATH, 'rb') as f:
            scaler = pickle.load(f)
        with open(ORDER_PATH, 'rb') as f:
            order = pickle.load(f)  # [prestasi, sedang, butuh]

        data = request.get_json()
        math = float(data['math_score'])
        reading = float(data['reading_score'])
        writing = float(data['writing_score'])

        X = np.array([[math, reading, writing]])
        X_scaled = scaler.transform(X)
        raw_label = model.predict(X_scaled)[0]

        # Cari posisi raw_label dalam order
        try:
            final_label = order.index(raw_label)
        except ValueError:
            final_label = 2  # fallback

        cluster_names = {0: "Siswa Berprestasi", 1: "Siswa Rata-rata/Cukup", 2: "Siswa Butuh Bimbingan"}

        return jsonify({
            'status': 'success',
            'prediction': {
                'raw_cluster': int(raw_label),
                'cluster': final_label,
                'cluster_name': cluster_names[final_label]
            }
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/', methods=['GET'])
def home():
    return jsonify({'message': 'AI Server Academy+ berjalan', 'status': 'ok'})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)