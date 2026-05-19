import 'package:flutter/material.dart';

import '../../widgets/baseLayoutSiswa.dart';
import 'allMapel.dart';
import 'detailMapelSiswa.dart';

class ElearningSiswa extends StatefulWidget {

  const ElearningSiswa({super.key});

  @override
  State<ElearningSiswa> createState() =>
      _ElearningSiswaState();
}

class _ElearningSiswaState
    extends State<ElearningSiswa> {

  final TextEditingController searchController =
      TextEditingController();

  List<Map<String, dynamic>> allMapel = [

    {
      "nama": "Matematika",
      "icon": Icons.calculate,
      "materi": 12,
      "progress": 0.8,
      "tugas": 2,
      "color": Colors.blue,
    },

    {
      "nama": "Bahasa Indonesia",
      "icon": Icons.menu_book,
      "materi": 8,
      "progress": 0.6,
      "tugas": 1,
      "color": Colors.orange,
    },

    {
      "nama": "Bahasa Inggris",
      "icon": Icons.language,
      "materi": 10,
      "progress": 0.7,
      "tugas": 0,
      "color": Colors.green,
    },

    {
      "nama": "IPA",
      "icon": Icons.science,
      "materi": 7,
      "progress": 0.5,
      "tugas": 3,
      "color": Colors.purple,
    },

    {
      "nama": "IPS",
      "icon": Icons.public,
      "materi": 9,
      "progress": 0.9,
      "tugas": 0,
      "color": Colors.red,
    },

    {
      "nama": "Informatika",
      "icon": Icons.computer,
      "materi": 15,
      "progress": 0.75,
      "tugas": 4,
      "color": Colors.teal,
    },
  ];

  List<Map<String, dynamic>> filteredMapel = [];

  @override
  void initState() {

    super.initState();

    filteredMapel = allMapel;
  }

  // SEARCH MAPEL
  void searchMapel(String keyword) {

    final result = allMapel.where((mapel) {

      final nama =
          mapel["nama"].toString().toLowerCase();

      return nama.contains(
        keyword.toLowerCase(),
      );

    }).toList();

    setState(() {

      filteredMapel = result;

    });
  }

  // TOTAL TUGAS
  int get totalTugas {

    int total = 0;

    for (var item in allMapel) {

      total += item["tugas"] as int;
    }

    return total;
  }

  @override
  Widget build(BuildContext context) {

    // HANYA TAMPILKAN 2 MAPEL
    final displayedMapel =
        searchController.text.isEmpty
            ? filteredMapel.take(2).toList()
            : filteredMapel;

    return BaseLayoutSiswa(

      title: "E-Learning",

      selectedIndex: 3,

      body: Padding(

        padding: const EdgeInsets.all(20),

        child: Column(

          crossAxisAlignment:
              CrossAxisAlignment.start,

          children: [

            // GREETING
            const Text(

              "Halo, Siswa!",

              style: TextStyle(
                fontSize: 28,
                fontWeight: FontWeight.bold,
              ),
            ),

            const SizedBox(height: 8),

            const Text(

              "Yuk lanjutkan pembelajaran hari ini",

              style: TextStyle(
                color: Colors.black54,
                fontSize: 15,
              ),
            ),

            const SizedBox(height: 25),

            // SEARCH
            Container(

              decoration: BoxDecoration(

                color: Colors.white,

                borderRadius:
                    BorderRadius.circular(15),

                boxShadow: [

                  BoxShadow(

                    color: Colors.black12,

                    blurRadius: 8,

                    offset: const Offset(0, 4),
                  ),
                ],
              ),

              child: TextField(

                controller: searchController,

                onChanged: searchMapel,

                decoration: InputDecoration(

                  hintText:
                      "Cari mata pelajaran...",

                  prefixIcon:
                      const Icon(Icons.search),

                  border: OutlineInputBorder(

                    borderRadius:
                        BorderRadius.circular(15),

                    borderSide: BorderSide.none,
                  ),

                  contentPadding:
                      const EdgeInsets.symmetric(
                    vertical: 16,
                  ),
                ),
              ),
            ),

            const SizedBox(height: 25),

            // CARD TUGAS
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
                    BorderRadius.circular(20),

                boxShadow: [

                  BoxShadow(

                    color: Colors.blue.withOpacity(0.3),

                    blurRadius: 10,

                    offset: const Offset(0, 5),
                  ),
                ],
              ),

              child: Row(

                children: [

                  Container(

                    padding:
                        const EdgeInsets.all(15),

                    decoration: BoxDecoration(

                      color: Colors.white24,

                      borderRadius:
                          BorderRadius.circular(15),
                    ),

                    child: const Icon(

                      Icons.assignment,

                      color: Colors.white,

                      size: 35,
                    ),
                  ),

                  const SizedBox(width: 20),

                  Expanded(

                    child: Column(

                      crossAxisAlignment:
                          CrossAxisAlignment.start,

                      children: [

                        const Text(

                          "Tugas Belum Dikerjakan",

                          style: TextStyle(

                            color: Colors.white70,

                            fontSize: 14,
                          ),
                        ),

                        const SizedBox(height: 5),

                        Text(

                          "$totalTugas Tugas",

                          style: const TextStyle(

                            color: Colors.white,

                            fontSize: 28,

                            fontWeight:
                                FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                  ),

                  const Icon(

                    Icons.arrow_forward_ios,

                    color: Colors.white,
                  ),
                ],
              ),
            ),

            const SizedBox(height: 30),

            // TITLE
            const Text(

              "Mata Pelajaran",

              style: TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.bold,
              ),
            ),

            const SizedBox(height: 20),

            // GRID MAPEL
            Expanded(

              child: GridView.builder(

                itemCount: displayedMapel.length,

                gridDelegate:
                    SliverGridDelegateWithFixedCrossAxisCount(

                  crossAxisCount: 2,

                  crossAxisSpacing: 15,

                  mainAxisSpacing: 15,

                  childAspectRatio:
                      MediaQuery.of(context)
                                  .size
                                  .width <
                              400
                          ? 0.65
                          : 0.75,
                ),

                itemBuilder: (context, index) {

                  final mapel =
                      displayedMapel[index];

                  return InkWell(

                    borderRadius:
                        BorderRadius.circular(20),

                  onTap: () {

                    Navigator.push(

                      context,

                      MaterialPageRoute(

                        builder: (_) =>
                            DetailMapelSiswa(

                          mapel: mapel,
                        ),
                      ),
                    );
                  },
              

                    child: Container(

                      padding:
                          const EdgeInsets.all(16),

                      decoration: BoxDecoration(

                        gradient: LinearGradient(

                          colors: [

                            mapel["color"],

                            mapel["color"]
                                .withOpacity(0.8),
                          ],

                          begin:
                              Alignment.topLeft,

                          end:
                              Alignment.bottomRight,
                        ),

                        borderRadius:
                            BorderRadius.circular(20),

                        boxShadow: [

                          BoxShadow(

                            color: mapel["color"]
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

                          // BADGE
                          Align(

                            alignment:
                                Alignment.topRight,

                            child: Container(

                              padding:
                                  const EdgeInsets.symmetric(
                                horizontal: 10,
                                vertical: 5,
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

                                "${mapel["materi"]} Materi",

                                style:
                                    TextStyle(

                                  color:
                                      mapel["color"],

                                  fontWeight:
                                      FontWeight.bold,

                                  fontSize: 11,
                                ),
                              ),
                            ),
                          ),

                          const Spacer(),

                          // ICON
                          Icon(

                            mapel["icon"],

                            size: 42,

                            color: Colors.white,
                          ),

                          const SizedBox(height: 15),

                          // NAMA MAPEL
                          Text(

                            mapel["nama"],

                            maxLines: 2,

                            overflow:
                                TextOverflow.ellipsis,

                            style:
                                const TextStyle(

                              color: Colors.white,

                              fontWeight:
                                  FontWeight.bold,

                              fontSize: 16,
                            ),
                          ),

                          const SizedBox(height: 10),

                          // TUGAS
                          Text(

                            mapel["tugas"] == 0
                                ? "Tidak ada tugas"
                                : "${mapel["tugas"]} tugas belum selesai",

                            style:
                                const TextStyle(

                              color: Colors.white70,

                              fontSize: 13,
                            ),
                          ),

                          const SizedBox(height: 10),

                          // PROGRESS
                          ClipRRect(

                            borderRadius:
                                BorderRadius.circular(
                              10,
                            ),

                            child:
                                LinearProgressIndicator(

                              value:
                                  mapel["progress"],

                              minHeight: 8,

                              backgroundColor:
                                  Colors.white24,

                              valueColor:
                                  const AlwaysStoppedAnimation<Color>(
                                Colors.white,
                              ),
                            ),
                          ),

                          const SizedBox(height: 8),

                          Text(

                            "Progress ${(mapel["progress"] * 100).toInt()}%",

                            style:
                                const TextStyle(

                              color: Colors.white,

                              fontSize: 12,
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),

            const SizedBox(height: 10),

            // SHOW MORE BUTTON
            if (searchController.text.isEmpty)

              Center(

                child: ElevatedButton(

                  style: ElevatedButton.styleFrom(

                    padding:
                        const EdgeInsets.symmetric(
                      horizontal: 30,
                      vertical: 14,
                    ),

                    shape: RoundedRectangleBorder(

                      borderRadius:
                          BorderRadius.circular(15),
                    ),
                  ),

                  onPressed: () {

                    Navigator.push(

                      context,

                      MaterialPageRoute(

                        builder: (_) =>
                            AllMapelSiswa(
                          mapel: filteredMapel,
                        ),
                      ),
                    );
                  },

                  child: const Text(
                    "Show More",
                  ),
                ),
              ),

            const SizedBox(height: 10),
          ],
        ),
      ),
    );
  }
}