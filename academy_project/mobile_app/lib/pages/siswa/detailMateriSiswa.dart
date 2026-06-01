import 'package:flutter/material.dart';

import 'package:file_picker/file_picker.dart';

import 'latsolSiswa.dart';

import 'pdfViewer.dart';
import 'videoPlayer.dart';

class DetailMateriSiswa
    extends StatefulWidget {

  final String title;

  const DetailMateriSiswa({

    super.key,

    required this.title,
  });

  @override
  State<DetailMateriSiswa>
      createState() =>
          _DetailMateriSiswaState();
}

class _DetailMateriSiswaState
    extends State<DetailMateriSiswa> {

  String? selectedFileName;

  bool uploaded = false;

  // =========================
  // PICK FILE
  // =========================

  Future<void> pickFile() async {

    FilePickerResult? result =
        await FilePicker.platform
            .pickFiles();

    if (result != null) {

      setState(() {

        selectedFileName =
            result.files.single.name;

        uploaded = true;
      });

      ScaffoldMessenger.of(context)
          .showSnackBar(

        const SnackBar(

          content: Text(
            "Tugas berhasil dipilih",
          ),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {

    return Scaffold(

      appBar: AppBar(

        title: Text(widget.title),

        backgroundColor: Colors.blue,
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

                gradient:
                    const LinearGradient(

                  colors: [

                    Color(0xFF1565C0),

                    Color(0xFF42A5F5),
                  ],
                ),

                borderRadius:
                    BorderRadius.circular(
                  25,
                ),
              ),

              child: Column(

                crossAxisAlignment:
                    CrossAxisAlignment.start,

                children: [

                  const Icon(

                    Icons.menu_book,

                    size: 60,

                    color: Colors.white,
                  ),

                  const SizedBox(
                    height: 15,
                  ),

                  Text(

                    widget.title,

                    style:
                        const TextStyle(

                      color: Colors.white,

                      fontSize: 28,

                      fontWeight:
                          FontWeight.bold,
                    ),
                  ),

                  const SizedBox(
                    height: 8,
                  ),

                  const Text(

                    "Pelajari materi berikut dengan baik sebelum mengerjakan tugas.",

                    style: TextStyle(
                      color: Colors.white70,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 30),

            // =========================
            // FILE PEMBELAJARAN
            // =========================

            const Text(

              "File Pembelajaran",

              style: TextStyle(

                fontSize: 23,

                fontWeight:
                    FontWeight.bold,
              ),
            ),

            const SizedBox(height: 18),

            // PDF
            _fileCard(

              icon:
                  Icons.picture_as_pdf,

              title:
                  "Modul Pembelajaran.pdf",

              subtitle:
                  "PDF Materi",

              color: Colors.red,

              onTap: () {

                Navigator.push(

                  context,

                  MaterialPageRoute(

                    builder: (_) =>
                        const PdfViewerPage(

                      title:
                          "Modul PDF",

                      assetPath:
                          "assets/pdf/matematika.pdf",
                    ),
                  ),
                );
              },
            ),

            // PPT
            _fileCard(

              icon:
                  Icons.slideshow,

              title:
                  "Slide Presentasi.ppt",

              subtitle:
                  "PPT Pembelajaran",

              color: Colors.orange,

              onTap: () {

                ScaffoldMessenger.of(
                  context,
                ).showSnackBar(

                  const SnackBar(

                    content: Text(
                      "Fitur PPT sementara belum tersedia.\nGunakan PDF sebagai pengganti.",
                    ),
                  ),
                );
              },
            ),

            // VIDEO
            _fileCard(

              icon:
                  Icons.play_circle_fill,

              title:
                  "Video Pembelajaran",

              subtitle:
                  "Video Penjelasan",

              color: Colors.blue,

              onTap: () {

                Navigator.push(

                  context,

                  MaterialPageRoute(

                    builder: (_) =>
                        const VideoPlayerPage(

                      title:
                          "Video Pembelajaran",

                      videoPath:
                          "assets/video/pembelajaran.mp4",
                    ),
                  ),
                );
              },
            ),

            const SizedBox(height: 30),

            // =========================
            // QUIZ
            // =========================

            const Text(

              "Latihan Soal",

              style: TextStyle(

                fontSize: 23,

                fontWeight:
                    FontWeight.bold,
              ),
            ),

            const SizedBox(height: 15),

            Container(

              width: double.infinity,

              padding:
                  const EdgeInsets.all(
                18,
              ),

              decoration: BoxDecoration(

                color:
                    Colors.green
                        .withOpacity(
                  0.1,
                ),

                borderRadius:
                    BorderRadius.circular(
                  20,
                ),
              ),

              child: Column(

                children: [

                  const Row(

                    children: [

                      Icon(

                        Icons.quiz,

                        color:
                            Colors.green,
                      ),

                      SizedBox(width: 10),

                      Text(

                        "Quiz Materi",

                        style: TextStyle(

                          fontWeight:
                              FontWeight
                                  .bold,

                          fontSize: 18,
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(
                    height: 15,
                  ),

                  const Text(

                    "Kerjakan latihan soal untuk menguji pemahaman materi.",

                    style: TextStyle(
                      height: 1.5,
                    ),
                  ),

                  const SizedBox(
                    height: 20,
                  ),

                  SizedBox(

                    width:
                        double.infinity,

                    child:
                        ElevatedButton(

                      onPressed: () {

                        Navigator.push(

                          context,

                          MaterialPageRoute(

                            builder: (_) =>
                                const LatsolSiswa(),
                          ),
                        );
                      },

                      style:
                          ElevatedButton.styleFrom(

                        backgroundColor:
                            Colors.green,

                        foregroundColor:
                            Colors.white,

                        padding:
                            const EdgeInsets.symmetric(
                          vertical: 14,
                        ),
                      ),

                      child: const Text(
                        "Mulai Quiz",
                      ),
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 30),

            // =========================
            // UPLOAD TUGAS
            // =========================

            const Text(

              "Upload Tugas",

              style: TextStyle(

                fontSize: 23,

                fontWeight:
                    FontWeight.bold,
              ),
            ),

            const SizedBox(height: 15),

            Container(

              width: double.infinity,

              padding:
                  const EdgeInsets.all(
                20,
              ),

              decoration: BoxDecoration(

                color:
                    Colors.orange
                        .withOpacity(
                  0.1,
                ),

                borderRadius:
                    BorderRadius.circular(
                  22,
                ),
              ),

              child: Column(

                crossAxisAlignment:
                    CrossAxisAlignment
                        .start,

                children: [

                  const Row(

                    children: [

                      Icon(

                        Icons.assignment,

                        color:
                            Colors.orange,
                      ),

                      SizedBox(width: 10),

                      Text(

                        "Tugas Mingguan",

                        style: TextStyle(

                          fontWeight:
                              FontWeight
                                  .bold,

                          fontSize: 18,
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(
                    height: 15,
                  ),

                  const Text(

                    "Upload file jawaban tugas sebelum deadline.",

                    style: TextStyle(
                      height: 1.5,
                    ),
                  ),

                  const SizedBox(
                    height: 15,
                  ),

                  if (selectedFileName !=
                      null)

                    Container(

                      padding:
                          const EdgeInsets
                              .all(14),

                      decoration:
                          BoxDecoration(

                        color:
                            Colors.white,

                        borderRadius:
                            BorderRadius.circular(
                          15,
                        ),
                      ),

                      child: Row(

                        children: [

                          const Icon(
                            Icons.insert_drive_file,
                          ),

                          const SizedBox(
                            width: 10,
                          ),

                          Expanded(

                            child: Text(
                              selectedFileName!,
                            ),
                          ),
                        ],
                      ),
                    ),

                  const SizedBox(
                    height: 20,
                  ),

                  SizedBox(

                    width:
                        double.infinity,

                    child:
                        ElevatedButton.icon(

                      onPressed:
                          pickFile,

                      icon: const Icon(
                        Icons.upload_file,
                      ),

                      label: Text(

                        uploaded

                            ? "Ganti File"

                            : "Pilih File",
                      ),

                      style:
                          ElevatedButton.styleFrom(

                        backgroundColor:
                            Colors.orange,

                        foregroundColor:
                            Colors.white,

                        padding:
                            const EdgeInsets.symmetric(
                          vertical: 15,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }

  // =========================
  // FILE CARD
  // =========================

  Widget _fileCard({

    required IconData icon,

    required String title,

    required String subtitle,

    required Color color,

    required VoidCallback onTap,
  }) {

    return Container(

      margin:
          const EdgeInsets.only(
        bottom: 15,
      ),

      decoration: BoxDecoration(

        color: Colors.white,

        borderRadius:
            BorderRadius.circular(
          20,
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
            const EdgeInsets.all(
          15,
        ),

        leading: CircleAvatar(

          backgroundColor:
              color.withOpacity(
            0.15,
          ),

          radius: 25,

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