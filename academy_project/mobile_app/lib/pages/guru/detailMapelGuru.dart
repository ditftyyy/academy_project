import 'package:flutter/material.dart';

import 'detailMateriGuru.dart';

class DetailMapelGuru extends StatefulWidget {

  final Map<String, dynamic> mapel;

  const DetailMapelGuru({

    super.key,

    required this.mapel,
  });

  @override
  State<DetailMapelGuru> createState() =>
      _DetailMapelGuruState();
}

class _DetailMapelGuruState
    extends State<DetailMapelGuru> {

  // =========================
  // DATA MATERI
  // =========================

  final List<Map<String, dynamic>>
      materiList = [

    {

      "minggu": "Minggu 1",

      "judul":
          "Pengenalan Materi",

      "tanggal":
          "12 Mei 2026",

      "status":
          "Sudah Upload",
    },

    {

      "minggu": "Minggu 2",

      "judul":
          "Latihan Dasar",

      "tanggal":
          "19 Mei 2026",

      "status":
          "Belum Upload",
    },

    {

      "minggu": "Minggu 3",

      "judul":
          "Pembahasan Soal",

      "tanggal":
          "26 Mei 2026",

      "status":
          "Sudah Upload",
    },
  ];

  @override
  Widget build(BuildContext context) {

    return Scaffold(

      appBar: AppBar(

        title: Text(
          widget.mapel["nama"],
        ),

        backgroundColor:
            widget.mapel["color"],
      ),

      floatingActionButton:
          FloatingActionButton(

        backgroundColor:
            widget.mapel["color"],

        child: const Icon(
          Icons.add,
          color: Colors.white,
        ),

        onPressed: () {

          _showTambahMateriDialog();
        },
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
                  const EdgeInsets.all(22),

              decoration: BoxDecoration(

                gradient: LinearGradient(

                  colors: [

                    widget.mapel["color"],

                    widget.mapel["color"]
                        .withOpacity(0.7),
                  ],
                ),

                borderRadius:
                    BorderRadius.circular(
                  25,
                ),
              ),

              child: Row(

                children: [

                  CircleAvatar(

                    radius: 35,

                    backgroundColor:
                        Colors.white,

                    child: Icon(

                      widget.mapel["icon"],

                      size: 40,

                      color:
                          widget.mapel["color"],
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

                          widget.mapel["nama"],

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

                          "Kelola materi dan tugas siswa",

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
            // STATISTIK
            // =========================

            Row(

              children: [

                Expanded(

                  child: _statCard(

                    title:
                        "Total Materi",

                    value:
                        "${materiList.length}",

                    icon:
                        Icons.menu_book,

                    color:
                        Colors.blue,
                  ),
                ),

                const SizedBox(width: 15),

                Expanded(

                  child: _statCard(

                    title:
                        "Tugas",

                    value: "5",

                    icon:
                        Icons.assignment,

                    color:
                        Colors.orange,
                  ),
                ),
              ],
            ),

            const SizedBox(height: 30),

            // =========================
            // TITLE
            // =========================

            Row(

              mainAxisAlignment:
                  MainAxisAlignment
                      .spaceBetween,

              children: [

                const Text(

                  "Daftar Materi",

                  style: TextStyle(

                    fontSize: 24,

                    fontWeight:
                        FontWeight.bold,
                  ),
                ),

                TextButton.icon(

                  onPressed: () {

                    _showTambahMateriDialog();
                  },

                  icon: const Icon(
                    Icons.add,
                  ),

                  label: const Text(
                    "Tambah",
                  ),
                ),
              ],
            ),

            const SizedBox(height: 15),

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

                bool uploaded =
                    materi["status"] ==
                        "Sudah Upload";

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
                              DetailMateriGuru(

                            title:
                                materi["judul"],
                          ),
                        ),
                      );
                    },

                    leading: CircleAvatar(

                      radius: 28,

                      backgroundColor:

                          uploaded

                              ? Colors.green
                                  .shade100

                              : Colors.orange
                                  .shade100,

                      child: Icon(

                        uploaded

                            ? Icons.check

                            : Icons.upload_file,

                        color:

                            uploaded

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

                          const SizedBox(
                            height: 8,
                          ),

                          Container(

                            padding:
                                const EdgeInsets.symmetric(

                              horizontal: 10,

                              vertical: 5,
                            ),

                            decoration:
                                BoxDecoration(

                              color:

                                  uploaded

                                      ? Colors.green
                                          .shade100

                                      : Colors.orange
                                          .shade100,

                              borderRadius:
                                  BorderRadius.circular(
                                20,
                              ),
                            ),

                            child: Text(

                              materi["status"],

                              style: TextStyle(

                                color:

                                    uploaded

                                        ? Colors.green

                                        : Colors.orange,

                                fontWeight:
                                    FontWeight.bold,

                                fontSize: 12,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),

                    trailing: PopupMenuButton(

                      itemBuilder:
                          (context) => [

                        const PopupMenuItem(

                          value: "edit",

                          child: Text(
                            "Edit",
                          ),
                        ),

                        const PopupMenuItem(

                          value: "delete",

                          child: Text(
                            "Hapus",
                          ),
                        ),
                      ],

                      onSelected: (value) {

                        if (value ==
                            "delete") {

                          setState(() {

                            materiList.removeAt(
                              index,
                            );
                          });

                          ScaffoldMessenger.of(
                            context,
                          ).showSnackBar(

                            const SnackBar(

                              content: Text(
                                "Materi berhasil dihapus",
                              ),
                            ),
                          );
                        }
                      },
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
  // CARD STATISTIK
  // =========================

  Widget _statCard({

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

            blurRadius: 6,

            offset: const Offset(0, 3),
          ),
        ],
      ),

      child: Column(

        children: [

          CircleAvatar(

            backgroundColor:
                color.withOpacity(0.15),

            child: Icon(
              icon,
              color: color,
            ),
          ),

          const SizedBox(height: 12),

          Text(

            value,

            style: const TextStyle(

              fontSize: 24,

              fontWeight:
                  FontWeight.bold,
            ),
          ),

          const SizedBox(height: 5),

          Text(

            title,

            style: const TextStyle(
              color: Colors.grey,
            ),
          ),
        ],
      ),
    );
  }

  // =========================
  // DIALOG TAMBAH MATERI
  // =========================

  void _showTambahMateriDialog() {

    final TextEditingController
        judulController =
            TextEditingController();

    final TextEditingController
        mingguController =
            TextEditingController();

    showDialog(

      context: context,

      builder: (_) {

        return AlertDialog(

          title: const Text(
            "Tambah Materi",
          ),

          content: Column(

            mainAxisSize:
                MainAxisSize.min,

            children: [

              TextField(

                controller:
                    judulController,

                decoration:
                    const InputDecoration(

                  labelText:
                      "Judul Materi",
                ),
              ),

              const SizedBox(height: 15),

              TextField(

                controller:
                    mingguController,

                decoration:
                    const InputDecoration(

                  labelText:
                      "Minggu Ke",
                ),
              ),
            ],
          ),

          actions: [

            TextButton(

              onPressed: () {

                Navigator.pop(
                  context,
                );
              },

              child: const Text(
                "Batal",
              ),
            ),

            ElevatedButton(

              onPressed: () {

                setState(() {

                  materiList.add({

                    "minggu":
                        mingguController
                            .text,

                    "judul":
                        judulController
                            .text,

                    "tanggal":
                        "Hari Ini",

                    "status":
                        "Belum Upload",
                  });
                });

                Navigator.pop(
                  context,
                );

                ScaffoldMessenger.of(
                  context,
                ).showSnackBar(

                  const SnackBar(

                    content: Text(
                      "Materi berhasil ditambahkan",
                    ),
                  ),
                );
              },

              child: const Text(
                "Simpan",
              ),
            ),
          ],
        );
      },
    );
  }
}