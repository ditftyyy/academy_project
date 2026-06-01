import 'package:flutter/material.dart';

class AllMapelSiswa extends StatelessWidget {

  final List<Map<String, dynamic>> mapel;

  const AllMapelSiswa({
    super.key,
    required this.mapel,
  });

  @override
  Widget build(BuildContext context) {

    return Scaffold(

      appBar: AppBar(

        title: const Text("Semua Mata Pelajaran"),
      ),

      body: Padding(

        padding: const EdgeInsets.all(20),

        child: GridView.builder(

          itemCount: mapel.length,

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

            final item = mapel[index];

            return Container(

              padding: const EdgeInsets.all(16),

              decoration: BoxDecoration(

                gradient: LinearGradient(

                  colors: [

                    item["color"],

                    item["color"]
                        .withOpacity(0.8),
                  ],
                ),

                borderRadius:
                    BorderRadius.circular(20),
              ),

              child: Column(

                crossAxisAlignment:
                    CrossAxisAlignment.start,

                children: [

                  Align(

                    alignment: Alignment.topRight,

                    child: Container(

                      padding:
                          const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 5,
                      ),

                      decoration: BoxDecoration(

                        color: Colors.white,

                        borderRadius:
                            BorderRadius.circular(
                          20,
                        ),
                      ),

                      child: Text(

                        "${item["materi"]} Materi",

                        style: TextStyle(

                          color: item["color"],

                          fontWeight:
                              FontWeight.bold,

                          fontSize: 11,
                        ),
                      ),
                    ),
                  ),

                  const Spacer(),

                  Icon(

                    item["icon"],

                    size: 42,

                    color: Colors.white,
                  ),

                  const SizedBox(height: 15),

                  Text(

                    item["nama"],

                    maxLines: 2,

                    overflow:
                        TextOverflow.ellipsis,

                    style: const TextStyle(

                      color: Colors.white,

                      fontWeight:
                          FontWeight.bold,

                      fontSize: 16,
                    ),
                  ),

                  const SizedBox(height: 10),

                  Text(

                    item["tugas"] == 0
                        ? "Tidak ada tugas"
                        : "${item["tugas"]} tugas belum selesai",

                    style: const TextStyle(

                      color: Colors.white70,

                      fontSize: 13,
                    ),
                  ),

                  const SizedBox(height: 10),

                  ClipRRect(

                    borderRadius:
                        BorderRadius.circular(10),

                    child: LinearProgressIndicator(

                      value: item["progress"],

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

                    "Progress ${(item["progress"] * 100).toInt()}%",

                    style: const TextStyle(

                      color: Colors.white,

                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            );
          },
        ),
      ),
    );
  }
}