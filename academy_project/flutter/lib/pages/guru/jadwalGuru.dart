import 'package:flutter/material.dart';

import '../../widgets/baseLayoutGuru.dart';

class JadwalGuru extends StatefulWidget {

  const JadwalGuru({super.key});

  @override
  State<JadwalGuru> createState() => _JadwalGuruState();
}

class _JadwalGuruState extends State<JadwalGuru> {

  String selectedHari = "Semua";

  // DUMMY DATA
  final List<Map<String, dynamic>> jadwal = [

    {
      "hari": "Senin",
      "mapel": "Pemrograman",
      "kelas": "XI RPL 1",
      "jam": "07:00 - 08:30",
      "ruang": "Lab Komputer",
    },

    {
      "hari": "Senin",
      "mapel": "Basis Data",
      "kelas": "XI RPL 2",
      "jam": "08:30 - 10:00",
      "ruang": "Lab Database",
    },

    {
      "hari": "Selasa",
      "mapel": "Jaringan Komputer",
      "kelas": "X TKJ 1",
      "jam": "10:15 - 12:00",
      "ruang": "Lab Jaringan",
    },

    {
      "hari": "Rabu",
      "mapel": "Pemrograman Mobile",
      "kelas": "XII RPL 1",
      "jam": "13:00 - 14:30",
      "ruang": "Lab Mobile",
    },

    {
      "hari": "Kamis",
      "mapel": "UI/UX",
      "kelas": "XI RPL 3",
      "jam": "09:00 - 11:00",
      "ruang": "Multimedia",
    },
  ];

  @override
  Widget build(BuildContext context) {

    // FILTER
    final filteredJadwal =
        selectedHari == "Semua"

            ? jadwal

            : jadwal.where((item) {

                return item["hari"] == selectedHari;
              }).toList();

    return BaseLayoutGuru(

      title: "Jadwal Mengajar",

      selectedIndex: 1,

      body: Padding(

        padding: const EdgeInsets.all(20),

        child: Column(

          crossAxisAlignment: CrossAxisAlignment.start,

          children: [

            // TITLE
            const Text(

              "Jadwal Mengajar Guru",

              style: TextStyle(
                fontSize: 28,
                fontWeight: FontWeight.bold,
              ),
            ),

            const SizedBox(height: 10),

            const Text(

              "Daftar jadwal mengajar mingguan",

              style: TextStyle(
                color: Colors.black54,
                fontSize: 16,
              ),
            ),

            const SizedBox(height: 25),

            // FILTER
            Container(

              padding: const EdgeInsets.symmetric(
                horizontal: 15,
              ),

              decoration: BoxDecoration(

                color: Colors.white,

                borderRadius: BorderRadius.circular(15),

                boxShadow: [

                  BoxShadow(

                    color: Colors.black12,

                    blurRadius: 8,

                    offset: Offset(0, 4),
                  ),
                ],
              ),

              child: DropdownButtonHideUnderline(

                child: DropdownButton<String>(

                  value: selectedHari,

                  isExpanded: true,

                  items: [

                    "Semua",
                    "Senin",
                    "Selasa",
                    "Rabu",
                    "Kamis",
                    "Jumat",

                  ].map((hari) {

                    return DropdownMenuItem(

                      value: hari,

                      child: Text(hari),
                    );
                  }).toList(),

                  onChanged: (value) {

                    setState(() {

                      selectedHari = value!;
                    });
                  },
                ),
              ),
            ),

            const SizedBox(height: 25),

            // TABLE
            Expanded(

              child: Container(

                width: double.infinity,

                decoration: BoxDecoration(

                  color: Colors.white,

                  borderRadius: BorderRadius.circular(20),

                  boxShadow: [

                    BoxShadow(

                      color: Colors.black12,

                      blurRadius: 10,

                      offset: Offset(0, 5),
                    ),
                  ],
                ),

                child: SingleChildScrollView(

                  scrollDirection: Axis.horizontal,

                  child: SingleChildScrollView(

                    scrollDirection: Axis.vertical,

                    child: DataTable(

                      columnSpacing: 30,

                      headingRowColor:
                          MaterialStateProperty.all(
                            Colors.blue.shade100,
                          ),

                      columns: const [

                        DataColumn(
                          label: Text(
                            "Hari",
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),

                        DataColumn(
                          label: Text(
                            "Mata Pelajaran",
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),

                        DataColumn(
                          label: Text(
                            "Kelas",
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),

                        DataColumn(
                          label: Text(
                            "Jam",
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),

                        DataColumn(
                          label: Text(
                            "Ruangan",
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ],

                      rows:

                          filteredJadwal.map((item) {

                            return DataRow(

                              color:
                                  MaterialStateProperty.all(
                                    item["color"],
                                  ),

                              cells: [

                                DataCell(
                                  Text(item["hari"]),
                                ),

                                DataCell(
                                  Text(item["mapel"]),
                                ),

                                DataCell(
                                  Text(item["kelas"]),
                                ),

                                DataCell(
                                  Text(item["jam"]),
                                ),

                                DataCell(
                                  Text(item["ruang"]),
                                ),
                              ],
                            );
                          }).toList(),
                    ),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}