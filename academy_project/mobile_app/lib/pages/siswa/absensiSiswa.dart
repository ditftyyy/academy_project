import 'package:flutter/material.dart';

import '../../widgets/baseLayoutSiswa.dart';

class AbsensiSiswa extends StatefulWidget {

  const AbsensiSiswa({super.key});

  @override
  State<AbsensiSiswa> createState() => _AbsensiSiswaState();
}

class _AbsensiSiswaState extends State<AbsensiSiswa> {

  String selectedBulan = "Mei";

  // DUMMY DATA
  final List<Map<String, dynamic>> absensi = [

    {
      "tanggal": "12 Mei 2026",
      "mapel": "Matematika",
      "status": "Hadir",
    },

    {
      "tanggal": "12 Mei 2026",
      "mapel": "Bahasa Indonesia",
      "status": "Hadir",
    },

    {
      "tanggal": "13 Mei 2026",
      "mapel": "Pemrograman",
      "status": "Izin",
    },

    {
      "tanggal": "14 Mei 2026",
      "mapel": "Matematika",
      "status": "Alpha",
    },

    {
      "tanggal": "15 Mei 2026",
      "mapel": "IPA",
      "status": "Hadir",
    },

    {
      "tanggal": "16 Januari 2026",
      "mapel": "IPS",
      "status": "Hadir",
    },

    {
      "tanggal": "16 Januari 2026",
      "mapel": "Bahasa Indonesia",
      "status": "Hadir",
    },
  ];

  @override
  Widget build(BuildContext context) {

    // FILTER BERDASARKAN BULAN
    final filteredAbsensi = absensi.where((item) {

      return item["tanggal"].contains(selectedBulan);

    }).toList();

    return BaseLayoutSiswa(
      title: "Absensi",
      selectedIndex: 2,
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [

            // TITLE
            const Text(
              "Riwayat Absensi",
              style: TextStyle(
                fontSize: 28,
                fontWeight: FontWeight.bold,
              ),
            ),

            const SizedBox(height: 10),

            const Text(
              "Daftar kehadiran siswa",
              style: TextStyle(
                fontSize: 16,
                color: Colors.black54,
              ),
            ),

            const SizedBox(height: 25),

            // LEGEND
            Wrap(

              spacing: 20,
              runSpacing: 10,

              children: [

                _buildLegend(
                  color: Colors.green,
                  text: "Hadir",
                ),

                _buildLegend(
                  color: Colors.orange,
                  text: "Izin",
                ),

                _buildLegend(
                  color: Colors.red,
                  text: "Alpha",
                ),
              ],
            ),

            const SizedBox(height: 25),

            // SUMMARY CARD
            SizedBox(
              height: 120,
              child: Row(
                children: [

                  Expanded(
                    child: _buildSummaryCard(
                      title: "Hadir",
                      total: "5",
                      color: Colors.green,

                      icon: Icons.check_circle,
                    ),
                  ),

                  const SizedBox(width: 12),

                  Expanded(
                    child: _buildSummaryCard(
                      title: "Izin",
                      total: "1",
                      color: Colors.orange,
                      icon: Icons.info,
                    ),
                  ),

                  const SizedBox(width: 12),

                  Expanded(
                    child: _buildSummaryCard(
                      title: "Alpha",
                      total: "1",
                      color: Colors.red,
                      icon: Icons.cancel,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 25),

            // PERSENTASE
            Container(
              padding: const EdgeInsets.all(20),
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

              child: Column(

                crossAxisAlignment: CrossAxisAlignment.start,

                children: [

                  const Text(
                    "Persentase Kehadiran",
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),

                  const SizedBox(height: 15),

                  ClipRRect(
                    borderRadius: BorderRadius.circular(10),
                    child: LinearProgressIndicator(
                      value: 0.71,
                      minHeight: 15,
                      backgroundColor: Colors.grey.shade300,
                      valueColor:
                          const AlwaysStoppedAnimation<Color>(
                            Colors.green,
                          ),
                    ),
                  ),
                  
                  const SizedBox(height: 10),

                  const Text(
                    "71% Kehadiran",
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      color: Colors.green,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 25),

            // FILTER BULAN
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
                    offset: const Offset(0, 4),
                  ),
                ],
              ),

              child: DropdownButtonHideUnderline(
                child: DropdownButton<String>(
                  value: selectedBulan,
                  isExpanded: true,

                  items: [
                    "Januari",
                    "Februari",
                    "Maret",
                    "April",
                    "Mei",
                    "Juni",
                  ].map((bulan) {

                    return DropdownMenuItem(
                      value: bulan,
                      child: Text(bulan),
                    );
                  }).toList(),

                  onChanged: (value) {
                    setState(() {
                      selectedBulan = value!;
                    });
                  },
                ),
              ),
            ),

            const SizedBox(height: 25),

            // TABLE
            Expanded(
              flex: 2,
              child: Container(
                width: double.infinity,
                padding: const EdgeInsets.only(bottom: 15),
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

                child: SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: SingleChildScrollView(
                    scrollDirection: Axis.vertical,
                    child: DataTable(
                      columnSpacing: 40,
                      headingRowColor:
                          MaterialStateProperty.all(
                            Colors.blue.shade100,
                          ),

                      columns: const [

                        DataColumn(
                          label: Text(
                            "Tanggal",
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
                            "Status",
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ],

                      rows:
                          filteredAbsensi.map((item) {
                            Color statusColor;
                            IconData statusIcon;

                            switch (item["status"]) {

                              case "Hadir":
                                statusColor = Colors.green;
                                statusIcon =
                                    Icons.check_circle;
                                break;

                              case "Izin":
                                statusColor = Colors.orange;
                                statusIcon = Icons.info;
                                break;

                              default:
                                statusColor = Colors.red;
                                statusIcon = Icons.cancel;
                            }

                            return DataRow(

                              cells: [

                                DataCell(
                                  Text(item["tanggal"]),
                                ),

                                DataCell(
                                  Text(item["mapel"]),
                                ),

                                DataCell(
                                  Center(
                                    child: Icon(
                                      statusIcon,
                                      color: statusColor,
                                      size: 28,
                                    ),
                                  ),
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

  // LEGEND
  Widget _buildLegend({

    required Color color,
    required String text,
  }) {

    return Row(

      mainAxisSize: MainAxisSize.min,

      children: [

        Icon(
          Icons.circle,
          color: color,
          size: 16,
        ),

        const SizedBox(width: 8),

        Text(
          text,
          style: const TextStyle(
            fontWeight: FontWeight.bold,
          ),
        ),
      ],
    );
  }

  // SUMMARY CARD
  Widget _buildSummaryCard({

    required String title,
    required String total,
    required Color color,
    required IconData icon,
  }) {

    return Container(

      padding: const EdgeInsets.symmetric(
        vertical: 12,
        horizontal: 10,
      ),

      decoration: BoxDecoration(

        color: Colors.white,

        borderRadius: BorderRadius.circular(18),

        boxShadow: [

          BoxShadow(

            color: Colors.black12,

            blurRadius: 8,

            offset: const Offset(0, 4),
          ),
        ],
      ),

      child: Column(

        mainAxisAlignment: MainAxisAlignment.center,

        children: [

          Icon(
            icon,
            color: color,
            size: 28,
          ),

          const SizedBox(height: 8),

          Text(

            total,

            style: TextStyle(
              fontSize: 22,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),

          const SizedBox(height: 4),

          Text(
            title,
            style: const TextStyle(
              fontSize: 13,
            ),
          ),
        ],
      ),
    );
  }
}