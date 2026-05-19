import 'package:flutter/material.dart';

import '../../widgets/baseLayoutGuru.dart';

class DashboardGuru extends StatelessWidget {

  const DashboardGuru({super.key});

  @override
  Widget build(BuildContext context) {

    // =========================
    // DATA JADWAL MENGAJAR
    // =========================

    final List<Map<String, dynamic>>
        jadwalMengajar = [

      {
        "kelas": "X RPL 1",
        "jam": "07:00 - 08:30",
        "materi": "Pemrograman Dasar",
        "warna": Colors.blue,
      },

      {
        "kelas": "XI RPL 2",
        "jam": "09:00 - 10:30",
        "materi": "Basis Data",
        "warna": Colors.green,
      },

      {
        "kelas": "XII RPL 1",
        "jam": "10:45 - 12:00",
        "materi": "Flutter Mobile",
        "warna": Colors.orange,
      },
    ];

    return BaseLayoutGuru(

      title: "Dashboard Guru",

      selectedIndex: 0,

      body: SingleChildScrollView(

        padding: const EdgeInsets.all(20),

        child: Column(

          crossAxisAlignment:
              CrossAxisAlignment.start,

          children: [

            // =========================
            // GREETING
            // =========================

            Text(

              _getGreeting(),

              style: const TextStyle(

                fontSize: 30,

                fontWeight: FontWeight.bold,
              ),
            ),

            const SizedBox(height: 8),

            const Text(

              "Selamat datang kembali Guru!",

              style: TextStyle(

                color: Colors.black54,

                fontSize: 16,
              ),
            ),

            const SizedBox(height: 25),

            // =========================
            // PROFILE CARD
            // =========================

            Container(

              width: double.infinity,

              padding: const EdgeInsets.all(20),

              decoration: BoxDecoration(

                gradient: const LinearGradient(

                  colors: [
                    Color(0xFF1565C0),
                    Color(0xFF42A5F5),
                  ],
                ),

                borderRadius:
                    BorderRadius.circular(25),

                boxShadow: [

                  BoxShadow(

                    color:
                        Colors.blue.withOpacity(0.3),

                    blurRadius: 12,

                    offset: const Offset(0, 5),
                  ),
                ],
              ),

              child: Row(

                children: [

                  // FOTO GURU
                  const CircleAvatar(

                    radius: 38,

                    backgroundColor: Colors.white,

                    child: Icon(

                      Icons.person,

                      size: 40,

                      color: Colors.blue,
                    ),
                  ),

                  const SizedBox(width: 20),

                  // INFO GURU
                  const Expanded(

                    child: Column(

                      crossAxisAlignment:
                          CrossAxisAlignment.start,

                      children: [

                        Text(

                          "Pak Budi",

                          style: TextStyle(

                            color: Colors.white,

                            fontSize: 22,

                            fontWeight:
                                FontWeight.bold,
                          ),
                        ),

                        SizedBox(height: 5),

                        Text(

                          "Guru Informatika",

                          style: TextStyle(

                            color: Colors.white70,

                            fontSize: 15,
                          ),
                        ),

                        SizedBox(height: 3),

                        Text(

                          "NIP: 1987654321",

                          style: TextStyle(

                            color: Colors.white70,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 25),

            // =========================
            // QUICK INFO
            // =========================

            Row(

              children: [

                Expanded(

                  child: _buildInfoCard(

                    title: "Total Kelas",

                    value: "6",

                    icon: Icons.class_,

                    color: Colors.green,
                  ),
                ),

                const SizedBox(width: 15),

                Expanded(

                  child: _buildInfoCard(

                    title: "Materi",

                    value: "18",

                    icon: Icons.menu_book,

                    color: Colors.orange,
                  ),
                ),
              ],
            ),

            const SizedBox(height: 15),

            Row(

              children: [

                Expanded(

                  child: _buildInfoCard(

                    title: "Tugas",

                    value: "12",

                    icon: Icons.assignment,

                    color: Colors.blue,
                  ),
                ),

                const SizedBox(width: 15),

                Expanded(

                  child: _buildInfoCard(

                    title: "Siswa",

                    value: "120",

                    icon: Icons.people,

                    color: Colors.purple,
                  ),
                ),
              ],
            ),

            const SizedBox(height: 30),

            // =========================
            // TITLE JADWAL
            // =========================

            const Text(

              "Jadwal Mengajar Hari Ini",

              style: TextStyle(

                fontSize: 22,

                fontWeight: FontWeight.bold,
              ),
            ),

            const SizedBox(height: 18),

            // =========================
            // LIST JADWAL
            // =========================

            Column(

              children:
                  jadwalMengajar.map((jadwal) {

                return Container(

                  margin:
                      const EdgeInsets.only(
                    bottom: 15,
                  ),

                  padding:
                      const EdgeInsets.all(18),

                  decoration: BoxDecoration(

                    color: Colors.white,

                    borderRadius:
                        BorderRadius.circular(20),

                    boxShadow: [

                      BoxShadow(

                        color: Colors.black12,

                        blurRadius: 8,

                        offset:
                            const Offset(0, 4),
                      ),
                    ],
                  ),

                  child: Row(

                    children: [

                      // ICON
                      Container(

                        padding:
                            const EdgeInsets.all(
                          14,
                        ),

                        decoration:
                            BoxDecoration(

                          color: jadwal["warna"]
                              .withOpacity(0.15),

                          borderRadius:
                              BorderRadius.circular(
                            15,
                          ),
                        ),

                        child: Icon(

                          Icons.school,

                          color:
                              jadwal["warna"],

                          size: 30,
                        ),
                      ),

                      const SizedBox(width: 18),

                      // INFO
                      Expanded(

                        child: Column(

                          crossAxisAlignment:
                              CrossAxisAlignment
                                  .start,

                          children: [

                            Text(

                              jadwal["kelas"],

                              style:
                                  const TextStyle(

                                fontSize: 17,

                                fontWeight:
                                    FontWeight.bold,
                              ),
                            ),

                            const SizedBox(
                              height: 5,
                            ),

                            Text(

                              jadwal["materi"],

                              style:
                                  const TextStyle(

                                color:
                                    Colors
                                        .black54,
                              ),
                            ),

                            const SizedBox(
                              height: 3,
                            ),

                            Text(

                              jadwal["jam"],

                              style:
                                  TextStyle(

                                color:
                                    jadwal[
                                        "warna"],

                                fontWeight:
                                    FontWeight
                                        .bold,
                              ),
                            ),
                          ],
                        ),
                      ),

                      const Icon(
                        Icons.arrow_forward_ios,
                        size: 18,
                      ),
                    ],
                  ),
                );
              }).toList(),
            ),

            const SizedBox(height: 25),

            // =========================
            // PENGUMUMAN
            // =========================

            const Text(

              "Pengumuman",

              style: TextStyle(

                fontSize: 22,

                fontWeight: FontWeight.bold,
              ),
            ),

            const SizedBox(height: 18),

            Container(

              width: double.infinity,

              padding: const EdgeInsets.all(20),

              decoration: BoxDecoration(

                color: Colors.orange.shade50,

                borderRadius:
                    BorderRadius.circular(20),

                border: Border.all(

                  color: Colors.orange.shade200,
                ),
              ),

              child: Row(

                crossAxisAlignment:
                    CrossAxisAlignment.start,

                children: [

                  Icon(

                    Icons.campaign,

                    color: Colors.orange.shade700,

                    size: 32,
                  ),

                  const SizedBox(width: 15),

                  const Expanded(

                    child: Column(

                      crossAxisAlignment:
                          CrossAxisAlignment
                              .start,

                      children: [

                        Text(

                          "Rapat Guru",

                          style: TextStyle(

                            fontWeight:
                                FontWeight.bold,

                            fontSize: 16,
                          ),
                        ),

                        SizedBox(height: 8),

                        Text(

                          "Rapat evaluasi pembelajaran akan dilaksanakan pada hari Jumat pukul 13:00 WIB.",
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 30),
          ],
        ),
      ),
    );
  }

  // =========================
  // GREETING
  // =========================

  String _getGreeting() {

    final hour = DateTime.now().hour;

    if (hour < 12) {
      return "Selamat Pagi!";
    }

    if (hour < 15) {
      return "Selamat Siang!";
    }

    if (hour < 18) {
      return "Selamat Sore!";
    }

    return "Selamat Malam!";
  }

  // =========================
  // INFO CARD
  // =========================

  Widget _buildInfoCard({

    required String title,
    required String value,
    required IconData icon,
    required Color color,
  }) {

    return Container(

      padding: const EdgeInsets.all(18),

      decoration: BoxDecoration(

        color: Colors.white,

        borderRadius:
            BorderRadius.circular(20),

        boxShadow: [

          BoxShadow(

            color: Colors.black12,

            blurRadius: 8,

            offset: const Offset(0, 4),
          ),
        ],
      ),

      child: Column(

        crossAxisAlignment:
            CrossAxisAlignment.start,

        children: [

          Container(

            padding: const EdgeInsets.all(12),

            decoration: BoxDecoration(

              color: color.withOpacity(0.12),

              borderRadius:
                  BorderRadius.circular(15),
            ),

            child: Icon(

              icon,

              color: color,
            ),
          ),

          const SizedBox(height: 15),

          Text(

            value,

            style: TextStyle(

              fontSize: 26,

              fontWeight: FontWeight.bold,

              color: color,
            ),
          ),

          const SizedBox(height: 5),

          Text(

            title,

            style: const TextStyle(

              color: Colors.black54,
            ),
          ),
        ],
      ),
    );
  }
}