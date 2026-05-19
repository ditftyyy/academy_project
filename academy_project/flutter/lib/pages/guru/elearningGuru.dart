import 'package:flutter/material.dart';

import '../../widgets/baseLayoutGuru.dart';

import 'detailMapelGuru.dart';

class ElearningGuru extends StatefulWidget {

  const ElearningGuru({super.key});

  @override
  State<ElearningGuru> createState() =>
      _ElearningGuruState();
}

class _ElearningGuruState
    extends State<ElearningGuru> {

  // =========================
  // DATA MAPEL GURU
  // =========================

  final Map<String, dynamic> mapelGuru = {

    "nama": "Matematika",

    "icon": Icons.calculate,

    "materi": 12,

    "kelas": "X IPA 1",

    "tugas": 5,

    "siswa": 32,

    "color": Colors.blue,
  };

  @override
  Widget build(BuildContext context) {

    return BaseLayoutGuru(

      title: "E-Learning Guru",

      selectedIndex: 3,

      body: SingleChildScrollView(

        padding: const EdgeInsets.all(20),

        child: Column(

          crossAxisAlignment:
              CrossAxisAlignment.start,

          children: [

            // =========================
            // HEADER
            // =========================

            const Text(

              "Halo, Guru!",

              style: TextStyle(

                fontSize: 28,

                fontWeight:
                    FontWeight.bold,
              ),
            ),

            const SizedBox(height: 8),

            const Text(

              "Kelola materi dan tugas pembelajaran",

              style: TextStyle(

                color: Colors.black54,

                fontSize: 15,
              ),
            ),

            const SizedBox(height: 30),

            // =========================
            // CARD INFO
            // =========================

            Container(

              width: double.infinity,

              padding:
                  const EdgeInsets.all(22),

              decoration: BoxDecoration(

                gradient: LinearGradient(

                  colors: [

                    mapelGuru["color"],

                    mapelGuru["color"]
                        .withOpacity(0.8),
                  ],
                ),

                borderRadius:
                    BorderRadius.circular(
                  25,
                ),

                boxShadow: [

                  BoxShadow(

                    color:
                        mapelGuru["color"]
                            .withOpacity(0.3),

                    blurRadius: 10,

                    offset:
                        const Offset(0, 5),
                  ),
                ],
              ),

              child: Column(

                crossAxisAlignment:
                    CrossAxisAlignment.start,

                children: [

                  Row(

                    children: [

                      Container(

                        padding:
                            const EdgeInsets
                                .all(16),

                        decoration:
                            BoxDecoration(

                          color: Colors.white24,

                          borderRadius:
                              BorderRadius.circular(
                            18,
                          ),
                        ),

                        child: Icon(

                          mapelGuru["icon"],

                          color:
                              Colors.white,

                          size: 40,
                        ),
                      ),

                      const Spacer(),

                      Container(

                        padding:
                            const EdgeInsets.symmetric(

                          horizontal: 14,

                          vertical: 8,
                        ),

                        decoration:
                            BoxDecoration(

                          color: Colors.white,

                          borderRadius:
                              BorderRadius.circular(
                            20,
                          ),
                        ),

                        child: Text(

                          mapelGuru["kelas"],

                          style: TextStyle(

                            color:
                                mapelGuru["color"],

                            fontWeight:
                                FontWeight.bold,
                          ),
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 25),

                  Text(

                    mapelGuru["nama"],

                    style: const TextStyle(

                      color: Colors.white,

                      fontSize: 30,

                      fontWeight:
                          FontWeight.bold,
                    ),
                  ),

                  const SizedBox(height: 10),

                  const Text(

                    "Kelola seluruh materi, tugas, dan pembelajaran siswa.",

                    style: TextStyle(
                      color: Colors.white70,
                    ),
                  ),

                  const SizedBox(height: 25),

                  Row(

                    mainAxisAlignment:
                        MainAxisAlignment
                            .spaceBetween,

                    children: [

                      _infoItem(
                        "Materi",
                        "${mapelGuru["materi"]}",
                      ),

                      _infoItem(
                        "Tugas",
                        "${mapelGuru["tugas"]}",
                      ),

                      _infoItem(
                        "Siswa",
                        "${mapelGuru["siswa"]}",
                      ),
                    ],
                  ),
                ],
              ),
            ),

            const SizedBox(height: 35),

            // =========================
            // MENU MANAGEMENT
            // =========================

            const Text(

              "Menu Pembelajaran",

              style: TextStyle(

                fontSize: 22,

                fontWeight:
                    FontWeight.bold,
              ),
            ),

            const SizedBox(height: 20),

            _menuCard(

              icon: Icons.menu_book,

              title: "Kelola Materi",

              subtitle:
                  "Tambah dan edit materi pembelajaran",

              color: Colors.blue,

              onTap: () {

                Navigator.push(

                  context,

                  MaterialPageRoute(

                    builder: (_) =>
                        DetailMapelGuru(

                      mapel: mapelGuru,
                    ),
                  ),
                );
              },
            ),

            _menuCard(

              icon: Icons.assignment,

              title: "Kelola Tugas",

              subtitle:
                  "Upload dan periksa tugas siswa",

              color: Colors.orange,

              onTap: () {

                ScaffoldMessenger.of(
                  context,
                ).showSnackBar(

                  const SnackBar(

                    content: Text(
                      "Fitur kelola tugas",
                    ),
                  ),
                );
              },
            ),

            _menuCard(

              icon: Icons.quiz,

              title: "Kelola Quiz",

              subtitle:
                  "Buat latihan soal untuk siswa",

              color: Colors.green,

              onTap: () {

                ScaffoldMessenger.of(
                  context,
                ).showSnackBar(

                  const SnackBar(

                    content: Text(
                      "Fitur kelola quiz",
                    ),
                  ),
                );
              },
            ),
          ],
        ),
      ),
    );
  }

  // =========================
  // INFO ITEM
  // =========================

  Widget _infoItem(
    String title,
    String value,
  ) {

    return Column(

      children: [

        Text(

          value,

          style: const TextStyle(

            color: Colors.white,

            fontSize: 24,

            fontWeight:
                FontWeight.bold,
          ),
        ),

        const SizedBox(height: 5),

        Text(

          title,

          style: const TextStyle(
            color: Colors.white70,
          ),
        ),
      ],
    );
  }

  // =========================
  // MENU CARD
  // =========================

  Widget _menuCard({

    required IconData icon,

    required String title,

    required String subtitle,

    required Color color,

    required VoidCallback onTap,
  }) {

    return Container(

      margin:
          const EdgeInsets.only(
        bottom: 18,
      ),

      decoration: BoxDecoration(

        color: Colors.white,

        borderRadius:
            BorderRadius.circular(
          22,
        ),

        boxShadow: [

          BoxShadow(

            color: Colors.black12,

            blurRadius: 6,

            offset:
                const Offset(0, 3),
          ),
        ],
      ),

      child: ListTile(

        onTap: onTap,

        contentPadding:
            const EdgeInsets.all(18),

        leading: CircleAvatar(

          radius: 28,

          backgroundColor:
              color.withOpacity(0.15),

          child: Icon(
            icon,
            color: color,
          ),
        ),

        title: Text(

          title,

          style: const TextStyle(

            fontWeight:
                FontWeight.bold,

            fontSize: 17,
          ),
        ),

        subtitle: Padding(

          padding:
              const EdgeInsets.only(
            top: 5,
          ),

          child: Text(subtitle),
        ),

        trailing: const Icon(
          Icons.arrow_forward_ios,
          size: 18,
        ),
      ),
    );
  }
}