import 'package:flutter/material.dart';

import '../../widgets/baseLayoutSiswa.dart';

class JadwalSiswa extends StatefulWidget {

  const JadwalSiswa({super.key});

  @override
  State<JadwalSiswa> createState() => _JadwalSiswaState();
}

class _JadwalSiswaState extends State<JadwalSiswa> {

  String selectedHari = "Semua";

  // DUMMY DATA
  final List<Map<String, dynamic>> jadwal = [

    {
      "hari": "Senin",
      "mapel": "Matematika",
      "jam": "07:00 - 08:30",
      "ruang": "A1",
    },

    {
      "hari": "Senin",
      "mapel": "Bahasa Indonesia",
      "jam": "08:30 - 10:00",
      "ruang": "B2",
    },

    {
      "hari": "Selasa",
      "mapel": "Pemrograman",
      "jam": "10:15 - 12:00",
      "ruang": "Lab Komputer",
    },

    {
      "hari": "Rabu",
      "mapel": "Matematika",
      "jam": "13:00 - 14:30",
      "ruang": "A1",
    },

    {
      "hari": "Kamis",
      "mapel": "IPA",
      "jam": "09:00 - 11:00",
      "ruang": "A2",
    },

    {
      "hari": "Jumat",
      "mapel": "IPS",
      "jam": "09:00 - 11:00",
      "ruang": "B1",
    },

    {
      "hari": "Jumat",
      "mapel": "Bahasa Indonesia",
      "jam": "01:00 - 15:00",
      "ruang": "B2",
    },
  ];

  @override
  Widget build(BuildContext context) {

    // FILTER DATA
    final filteredJadwal =
        selectedHari == "Semua"
            ? jadwal
            : jadwal.where((item) {
                return item["hari"] == selectedHari;
              }).toList();

    return BaseLayoutSiswa(
      title: "Jadwal Pelajaran",
      selectedIndex: 1,
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [

            // TITLE
            const Text(
              "Jadwal Mingguan",
              style: TextStyle(
                fontSize: 28,
                fontWeight: FontWeight.bold,
              ),
            ),

            const SizedBox(height: 10),

            const Text(
              "Daftar jadwal mata pelajaran siswa",
              style: TextStyle(
                color: Colors.black54,
                fontSize: 16,
              ),
            ),

            const SizedBox(height: 25),

            // FILTER HARI
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