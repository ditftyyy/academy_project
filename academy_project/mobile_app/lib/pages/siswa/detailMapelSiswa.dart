import 'package:flutter/material.dart';

import 'detailMateriSiswa.dart';

class DetailMapelSiswa extends StatelessWidget {

  final Map<String, dynamic> mapel;

  const DetailMapelSiswa({

    super.key,

    required this.mapel,
  });

  @override
  Widget build(BuildContext context) {

    // =========================
    // DATA MATERI DUMMY
    // =========================

    final List<Map<String, dynamic>>
        materiList = [

      {

        "minggu": "Minggu 1",

        "judul":
            "Pengenalan Materi",

        "tanggal":
            "12 Mei 2026",

        "selesai": true,
      },

      {

        "minggu": "Minggu 2",

        "judul":
            "Latihan Dasar",

        "tanggal":
            "19 Mei 2026",

        "selesai": false,
      },

      {

        "minggu": "Minggu 3",

        "judul":
            "Pembahasan Soal",

        "tanggal":
            "26 Mei 2026",

        "selesai": false,
      },

    ];

    return Scaffold(

      appBar: AppBar(

        title: Text(
          mapel["nama"],
        ),

        backgroundColor:
            mapel["color"],
      ),

      body: SingleChildScrollView(

        padding: const EdgeInsets.all(20),

        child: Column(

          crossAxisAlignment:
              CrossAxisAlignment.start,

          children: [

            // =========================
            // HEADER
            // =========================

            Container(

              width: double.infinity,

              padding:
                  const EdgeInsets.all(20),

              decoration: BoxDecoration(

                gradient: LinearGradient(

                  colors: [

                    mapel["color"],

                    mapel["color"]
                        .withOpacity(0.7),
                  ],
                ),

                borderRadius:
                    BorderRadius.circular(25),
              ),

              child: Row(

                children: [

                  CircleAvatar(

                    radius: 35,

                    backgroundColor:
                        Colors.white,

                    child: Icon(

                      mapel["icon"],

                      size: 40,

                      color:
                          mapel["color"],
                    ),
                  ),

                  const SizedBox(width: 20),

                  Expanded(

                    child: Column(

                      crossAxisAlignment:
                          CrossAxisAlignment
                              .start,

                      children: [

                        Text(

                          mapel["nama"],

                          style:
                              const TextStyle(

                            color:
                                Colors.white,

                            fontSize: 26,

                            fontWeight:
                                FontWeight
                                    .bold,
                          ),
                        ),

                        const SizedBox(
                          height: 5,
                        ),

                        const Text(

                          "4 Materi Pembelajaran",

                          style: TextStyle(
                            color:
                                Colors.white70,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 30),

            // =========================
            // TITLE
            // =========================

            const Text(

              "Daftar Materi",

              style: TextStyle(

                fontSize: 24,

                fontWeight:
                    FontWeight.bold,
              ),
            ),

            const SizedBox(height: 20),

            // =========================
            // LIST MATERI
            // =========================

            ListView.builder(

              itemCount:
                  materiList.length,

              shrinkWrap: true,

              physics:
                  const NeverScrollableScrollPhysics(),

              itemBuilder:
                  (context, index) {

                final materi =
                    materiList[index];

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

                        color:
                            Colors.black12,

                        blurRadius: 6,

                        offset:
                            const Offset(
                          0,
                          3,
                        ),
                      ),
                    ],
                  ),

                  child: ListTile(

                    contentPadding:
                        const EdgeInsets.all(
                      18,
                    ),

                    onTap: () {

                      Navigator.push(

                        context,

                        MaterialPageRoute(

                          builder: (_) =>
                              DetailMateriSiswa(

                            title:
                                materi["judul"],
                          ),
                        ),
                      );
                    },

                    leading: CircleAvatar(

                      radius: 28,

                      backgroundColor:

                          materi["selesai"]

                              ? Colors.green
                                  .shade100

                              : Colors.orange
                                  .shade100,

                      child: Icon(

                        materi["selesai"]

                            ? Icons.check

                            : Icons.menu_book,

                        color:

                            materi["selesai"]

                                ? Colors.green

                                : Colors.orange,
                      ),
                    ),

                    title: Text(

                      materi["judul"],

                      style:
                          const TextStyle(

                        fontWeight:
                            FontWeight.bold,

                        fontSize: 18,
                      ),
                    ),

                    subtitle: Padding(

                      padding:
                          const EdgeInsets.only(
                        top: 8,
                      ),

                      child: Column(

                        crossAxisAlignment:
                            CrossAxisAlignment
                                .start,

                        children: [

                          Text(
                            materi["minggu"],
                          ),

                          const SizedBox(
                            height: 3,
                          ),

                          Text(
                            materi["tanggal"],
                          ),
                        ],
                      ),
                    ),

                    trailing: const Icon(
                      Icons.arrow_forward_ios,
                      size: 18,
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
}