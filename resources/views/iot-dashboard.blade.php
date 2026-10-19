<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IoT Monitoring & Control Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">IoT Monitoring & Control Dashboard</h2>
        
        <div class="row">
            <!-- Panel Sensor (Real-time dari Firebase) -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">Data Sensor (Real-time)</div>
                    <div class="card-body">
                        <p class="fs-5">Suhu: <span id="suhu" class="fw-bold">Menunggu data...</span> °C</p>
                        <p class="fs-5">Kelembapan: <span id="kelembapan" class="fw-bold">Menunggu data...</span> %</p>
                    </div>
                </div>
            </div>

            <!-- Panel Kontrol Perangkat -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">Kontrol Perangkat (Slave 2)</div>
                    <div class="card-body">
                        <button onclick="sendControl('KIPAS_ON')" class="btn btn-success m-2">Nyalakan Kipas</button>
                        <button onclick="sendControl('KIPAS_OFF')" class="btn btn-danger m-2">Matikan Kipas</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Firebase Web SDK -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-app.js";
        import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-database.js";

        const firebaseConfig = {
            apiKey: "AIzaSyBs74RmGZkWejJFAF52rlXXOaQiFCdE6DE",
            authDomain: "utss-bfa6d.firebaseapp.com",
            databaseURL: "https://utss-bfa6d-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "utss-bfa6d",
            storageBucket: "utss-bfa6d.firebasestorage.app",
            messagingSenderId: "99983472535",
            appId: "1:99983472535:web:5f4dc1bda37f49079c36d2"
        };

        const app = initializeApp(firebaseConfig);
        const db = getDatabase(app);

        // Mendengarkan perubahan data di node 'live_data' secara real-time
        const liveDataRef = ref(db, 'live_data');
        onValue(liveDataRef, (snapshot) => {
            const data = snapshot.val();
            if (data) {
                document.getElementById('suhu').innerText = data.suhu ?? '--';
                document.getElementById('kelembapan').innerText = data.kelembapan ?? '--';
            }
        });
    </script>

    <script>
        function sendControl(command) {
            fetch('/control', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ command: command })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Perintah terkirim:', data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>