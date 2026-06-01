import 'package:flutter/material.dart';

import '../../services/session_service.dart';
import '../../widgets/baseLayoutSiswa.dart';

import '../../models/siswa_model.dart';
import '../../services/api_service.dart';

class DashboardSiswa extends StatelessWidget {

  const DashboardSiswa({super.key});

  @override
  Widget build(BuildContext context) {

    final user = SessionService.getUser();

    // DUMMY JADWAL
    final List<Map<String, dynamic>> jadwalHariIni = [

      {
        "mapel": "Matematika",
        "jam": "07:00 - 08:30",
        "guru": "Bu Rina",
        "warna": Colors.blue,
      },

      {
        "mapel": "Pemrograman",
        "jam": "08:30 - 10:00",
        "guru": "Pak Budi",
        "warna": Colors.green,
      },

      {
        "mapel": "Basis Data",
        "jam": "10:15 - 11:45",
        "guru": "Pak Andi",
        "warna": Colors.orange,
      },
    ];

    return BaseLayoutSiswa(

      title: "Dashboard",

      selectedIndex: 0,

      body: SingleChildScrollView(

        padding: const EdgeInsets.all(20),

        child: Column(

          crossAxisAlignment:
              CrossAxisAlignment.start,

          children: [

            // GREETING
            Text(

              _getGreeting(),

              style: const TextStyle(

                fontSize: 30,

                fontWeight: FontWeight.bold,
              ),
            ),

            const SizedBox(height: 8),

            const Text(

              "Selamat datang kembali!",

              style: TextStyle(

                color: Colors.black54,

                fontSize: 16,
              ),
            ),

            const SizedBox(height: 25),

            // PROFILE CARD
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

                  // FOTO
                  CircleAvatar(

                    radius: 38,

                    backgroundColor: Colors.white,

                    backgroundImage:
                        user?.foto != null
                            ? NetworkImage(
                                user!.foto!,
                              )
                            : null,

                    child: user?.foto == null
                        ? const Icon(

                            Icons.person,

                            size: 40,

                            color: Colors.blue,
                          )
                        : null,
                  ),

                  const SizedBox(width: 20),

                  // INFO SISWA
                  Expanded(

                    child: Column(

                      crossAxisAlignment:
                          CrossAxisAlignment.start,

                      children: [

                        Text(

                          user?.nama ??
                              "Nama Siswa",

                          style: const TextStyle(

                            color: Colors.white,

                            fontSize: 22,

                            fontWeight:
                                FontWeight.bold,
                          ),
                        ),

                        const SizedBox(height: 5),

                        Text(

                          user?.kelas ??
                              "Kelas",

                          style: const TextStyle(

                            color: Colors.white70,

                            fontSize: 15,
                          ),
                        ),

                        const SizedBox(height: 3),

                        Text(

                          "NIS: ${user?.nis ?? '-'}",

                          style: const TextStyle(

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

            // QUICK INFO
            // Row(

            //   children: [

            //     Expanded(

            //       child: _buildInfoCard(

            //         title: "Kehadiran",

            //         value: "95%",

            //         icon: Icons.check_circle,

            //         color: Colors.green,
            //       ),
            //     ),

            //     const SizedBox(width: 15),

            //     Expanded(

            //       child: _buildInfoCard(

            //         title: "Tugas",

            //         value: "4",

            //         icon: Icons.assignment,

            //         color: Colors.orange,
            //       ),
            //     ),
            //   ],
            // ),

            // const SizedBox(height: 15),

            // Row(

            //   children: [

            //     Expanded(

            //       child: _buildInfoCard(

            //         title: "Materi",

            //         value: "28",

            //         icon: Icons.menu_book,

            //         color: Colors.blue,
            //       ),
            //     ),

            //     const SizedBox(width: 15),

            //     Expanded(

            //       child: _buildInfoCard(

            //         title: "Nilai",

            //         value: "88",

            //         icon: Icons.star,

            //         color: Colors.purple,
            //       ),
            //     ),
            //   ],
            // ),

            const SizedBox(height: 30),

            // TITLE JADWAL
            const Text(

              "Jadwal Hari Ini",

              style: TextStyle(

                fontSize: 22,

                fontWeight: FontWeight.bold,
              ),
            ),

            const SizedBox(height: 18),

            // LIST JADWAL
            Column(

              children:
                  jadwalHariIni.map((jadwal) {

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

                          Icons.menu_book,

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

                              jadwal["mapel"],

                              style:
                                  const TextStyle(

                                fontSize: 17,

                                fontWeight:
                                    FontWeight
                                        .bold,
                              ),
                            ),

                            const SizedBox(
                              height: 5,
                            ),

                            Text(

                              jadwal["guru"],

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

            // PENGUMUMAN
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

                          "Ujian Tengah Semester",

                          style: TextStyle(

                            fontWeight:
                                FontWeight.bold,

                            fontSize: 16,
                          ),
                        ),

                        SizedBox(height: 8),

                        Text(

                          "UTS akan dimulai pada tanggal 20 Mei 2026. "
                          "Pastikan seluruh tugas sudah dikumpulkan.",
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

  // GREETING BERDASARKAN JAM
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

  // CARD INFO
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