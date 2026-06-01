import 'package:flutter/material.dart';

import '../../widgets/baseLayoutGuru.dart';
import '../../services/session_service.dart';

class AbsensiGuru extends StatefulWidget {

  const AbsensiGuru({super.key});

  @override
  State<AbsensiGuru> createState() =>
      _AbsensiGuruState();
}

class _AbsensiGuruState
    extends State<AbsensiGuru> {

  // DUMMY DATA SISWA
  final List<Map<String, dynamic>> siswa = [

    {
      "nama": "Andi",
      "status": "Hadir",
    },

    {
      "nama": "Budi",
      "status": "Alpha",
    },

    {
      "nama": "Citra",
      "status": "Izin",
    },

    {
      "nama": "Dinda",
      "status": "Hadir",
    },
  ];

  final List<String> statusList = [

    "Hadir",
    "Izin",
    "Alpha",
  ];

  @override
  Widget build(BuildContext context) {

    // USER LOGIN
    final user =
        SessionService.getUser();

    // MAPEL GURU LOGIN
    String mapelGuru =
    user?.mapel?.first ?? "-";

    return BaseLayoutGuru(

      title: "Absensi Guru",

      selectedIndex: 2,

      body: SafeArea(

        child: SingleChildScrollView(

          padding:
              const EdgeInsets.all(16),

          child: Column(

            crossAxisAlignment:
                CrossAxisAlignment.start,

            children: [

              // =====================
              // TITLE
              // =====================
              const Text(

                "Input Absensi Siswa",

                style: TextStyle(
                  fontSize: 26,
                  fontWeight: FontWeight.bold,
                ),
              ),

              const SizedBox(height: 8),

              const Text(

                "Kelola kehadiran siswa",

                style: TextStyle(
                  fontSize: 14,
                  color: Colors.black54,
                ),
              ),

              const SizedBox(height: 20),

              // =====================
              // MAPEL GURU
              // =====================
              Container(

                padding:
                    const EdgeInsets.all(
                  15,
                ),

                decoration: BoxDecoration(

                  color:
                      Colors.blue.shade50,

                  borderRadius:
                      BorderRadius.circular(
                    15,
                  ),
                ),

                child: Row(

                  children: [

                    const Icon(

                      Icons.menu_book,

                      color: Colors.blue,
                    ),

                    const SizedBox(
                      width: 10,
                    ),

                    Expanded(

                      child: Text(

                        "Mata Pelajaran: "
                        "$mapelGuru",

                        style:
                            const TextStyle(

                          fontSize: 16,

                          fontWeight:
                              FontWeight.bold,
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 25),

              // =====================
              // TABLE ABSENSI
              // =====================
              Container(

                width: double.infinity,

                padding: const EdgeInsets.all(12),

                decoration: BoxDecoration(

                  color: Colors.white,

                  borderRadius: BorderRadius.circular(20),

                  boxShadow: [

                    BoxShadow(

                      color: Colors.black12,

                      blurRadius: 10,

                      offset: const Offset(0, 5),
                    ),
                  ],
                ),

                child: DataTable(

                  headingRowHeight: 45,

                  dataRowHeight: 60,

                  columnSpacing: 15,

                  horizontalMargin: 10,

                  headingRowColor:
                      MaterialStateProperty.all(
                    Colors.blue.shade100,
                  ),

                  columns: const [

                    // NO
                    DataColumn(

                      label: Text(

                        "No",

                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 13,
                        ),
                      ),
                    ),

                    // NAMA SISWA
                    DataColumn(

                      label: Text(

                        "Nama Siswa",

                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 13,
                        ),
                      ),
                    ),

                    // STATUS
                    DataColumn(

                      label: Text(

                        "Status",

                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 13,
                        ),
                      ),
                    ),
                  ],

                  rows:
                      siswa
                          .asMap()
                          .entries
                          .map(
                    (entry) {

                      int index = entry.key;

                      Map<String, dynamic>
                          item = entry.value;

                      return DataRow(

                        cells: [

                          // NOMOR
                          DataCell(

                            Text(

                              "${index + 1}",

                              style: const TextStyle(
                                fontSize: 13,
                              ),
                            ),
                          ),

                          // NAMA SISWA
                          DataCell(

                            Text(

                              item["nama"],

                              style: const TextStyle(
                                fontSize: 13,
                              ),
                            ),
                          ),

                          // STATUS
                          DataCell(

                            DropdownButton<String>(

                              value: item["status"],

                              isDense: true,

                              underline:
                                  const SizedBox(),

                              items:
                                  statusList.map(
                                (status) {

                                  Color color;

                                  switch (status) {

                                    case "Hadir":

                                      color = Colors.green;
                                      break;

                                    case "Izin":

                                      color = Colors.orange;
                                      break;

                                    default:

                                      color = Colors.red;
                                  }

                                  return DropdownMenuItem(

                                    value: status,

                                    child: Text(

                                      status,

                                      style: TextStyle(

                                        fontSize: 13,

                                        color: color,

                                        fontWeight:
                                            FontWeight.bold,
                                      ),
                                    ),
                                  );
                                },
                              ).toList(),

                              onChanged: (value) {

                                setState(() {

                                  item["status"] =
                                      value!;
                                });
                              },
                            ),
                          ),
                        ],
                      );
                    },
                  ).toList(),
                ),
              ),

              const SizedBox(height: 25),

              // =====================
              // BUTTON SIMPAN
              // =====================
              SizedBox(

                width: double.infinity,

                child:
                    ElevatedButton.icon(

                  onPressed: () {

                    ScaffoldMessenger.of(
                      context,
                    ).showSnackBar(

                      SnackBar(

                        content: Text(

                          "Absensi "
                          "$mapelGuru "
                          "berhasil disimpan",
                        ),
                      ),
                    );
                  },

                  icon:
                      const Icon(Icons.save),

                  label: const Text(
                    "Simpan Absensi",
                  ),

                  style:
                      ElevatedButton.styleFrom(

                    backgroundColor:
                        Colors.blue,

                    foregroundColor:
                        Colors.white,

                    padding:
                        const EdgeInsets.symmetric(
                      vertical: 15,
                    ),

                    shape:
                        RoundedRectangleBorder(

                      borderRadius:
                          BorderRadius.circular(
                        14,
                      ),
                    ),
                  ),
                ),
              ),

              const SizedBox(height: 30),
            ],
          ),
        ),
      ),
    );
  }
}