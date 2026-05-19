import 'package:flutter/material.dart';
import 'package:file_picker/file_picker.dart';

import 'pdfViewer.dart';
import 'videoPlayer.dart';

class DetailMateriGuru extends StatefulWidget {

  final String title;

  const DetailMateriGuru({

    super.key,

    required this.title,
  });

  @override
  State<DetailMateriGuru> createState() =>
      _DetailMateriGuruState();
}

class _DetailMateriGuruState
    extends State<DetailMateriGuru> {

  String? pdfName;
  String? pptName;
  String? videoName;

  // =========================
  // PICK FILE
  // =========================

  Future<void> pickFile(String type) async {

    FilePickerResult? result =
        await FilePicker.platform.pickFiles();

    if (result != null) {

      setState(() {

        if (type == "pdf") {

          pdfName = result.files.single.name;
        }

        else if (type == "ppt") {

          pptName = result.files.single.name;
        }

        else {

          videoName = result.files.single.name;
        }
      });

      ScaffoldMessenger.of(context).showSnackBar(

        SnackBar(

          content: Text(
            "$type berhasil dipilih",
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

        backgroundColor: Colors.indigo,
      ),

      floatingActionButton: FloatingActionButton(

        backgroundColor: Colors.indigo,

        child: const Icon(Icons.edit),

        onPressed: () {

          ScaffoldMessenger.of(context).showSnackBar(

            const SnackBar(

              content: Text(
                "Fitur edit materi",
              ),
            ),
          );
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

              padding: const EdgeInsets.all(24),

              decoration: BoxDecoration(

                gradient: const LinearGradient(

                  colors: [

                    Color(0xFF3949AB),

                    Color(0xFF5C6BC0),
                  ],
                ),

                borderRadius:
                    BorderRadius.circular(25),
              ),

              child: Column(

                crossAxisAlignment:
                    CrossAxisAlignment.start,

                children: [

                  const Icon(

                    Icons.school,

                    color: Colors.white,

                    size: 60,
                  ),

                  const SizedBox(height: 15),

                  Text(

                    widget.title,

                    style: const TextStyle(

                      color: Colors.white,

                      fontSize: 28,

                      fontWeight:
                          FontWeight.bold,
                    ),
                  ),

                  const SizedBox(height: 10),

                  const Text(

                    "Kelola materi pembelajaran, tugas, dan quiz siswa.",

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

            _guruFileCard(

              icon: Icons.picture_as_pdf,

              title: pdfName ??
                  "Upload Modul PDF",

              subtitle:
                  "Materi PDF",

              color: Colors.red,

              onUpload: () {

                pickFile("pdf");
              },

              onPreview: () {

                Navigator.push(

                  context,

                  MaterialPageRoute(

                    builder: (_) =>
                        const PdfViewerPage(

                      title: "Preview PDF",

                      assetPath:
                          "assets/pdf/matematika.pdf",
                    ),
                  ),
                );
              },
            ),

            _guruFileCard(

              icon: Icons.slideshow,

              title: pptName ??
                  "Upload Slide PPT",

              subtitle:
                  "Slide Presentasi",

              color: Colors.orange,

              onUpload: () {

                pickFile("ppt");
              },

              onPreview: () {

                ScaffoldMessenger.of(context)
                    .showSnackBar(

                  const SnackBar(

                    content: Text(
                      "Preview PPT belum tersedia",
                    ),
                  ),
                );
              },
            ),

            _guruFileCard(

              icon:
                  Icons.play_circle_fill,

              title: videoName ??
                  "Upload Video",

              subtitle:
                  "Video Pembelajaran",

              color: Colors.blue,

              onUpload: () {

                pickFile("video");
              },

              onPreview: () {

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
            // QUIZ MANAGEMENT
            // =========================

            const Text(

              "Kelola Quiz",

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
                  const EdgeInsets.all(20),

              decoration: BoxDecoration(

                color:
                    Colors.green.withOpacity(0.1),

                borderRadius:
                    BorderRadius.circular(20),
              ),

              child: Column(

                crossAxisAlignment:
                    CrossAxisAlignment.start,

                children: [

                  const Row(

                    children: [

                      Icon(

                        Icons.quiz,

                        color: Colors.green,
                      ),

                      SizedBox(width: 10),

                      Text(

                        "Quiz Materi",

                        style: TextStyle(

                          fontSize: 18,

                          fontWeight:
                              FontWeight.bold,
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 15),

                  const Text(

                    "Buat dan kelola soal latihan untuk siswa.",

                    style: TextStyle(
                      height: 1.5,
                    ),
                  ),

                  const SizedBox(height: 20),

                  Row(

                    children: [

                      Expanded(

                        child: ElevatedButton.icon(

                          onPressed: () {

                            ScaffoldMessenger.of(context)
                                .showSnackBar(

                              const SnackBar(

                                content: Text(
                                  "Tambah soal quiz",
                                ),
                              ),
                            );
                          },

                          icon:
                              const Icon(Icons.add),

                          label:
                              const Text("Tambah"),

                          style:
                              ElevatedButton.styleFrom(

                            backgroundColor:
                                Colors.green,

                            foregroundColor:
                                Colors.white,
                          ),
                        ),
                      ),

                      const SizedBox(width: 15),

                      Expanded(

                        child: ElevatedButton.icon(

                          onPressed: () {

                            ScaffoldMessenger.of(context)
                                .showSnackBar(

                              const SnackBar(

                                content: Text(
                                  "Lihat daftar quiz",
                                ),
                              ),
                            );
                          },

                          icon:
                              const Icon(Icons.list),

                          label:
                              const Text("Daftar"),

                          style:
                              ElevatedButton.styleFrom(

                            backgroundColor:
                                Colors.white,

                            foregroundColor:
                                Colors.green,
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            const SizedBox(height: 30),

            // =========================
            // TUGAS SISWA
            // =========================

            const Text(

              "Pengumpulan Tugas",

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
                  const EdgeInsets.all(20),

              decoration: BoxDecoration(

                color:
                    Colors.orange.withOpacity(0.1),

                borderRadius:
                    BorderRadius.circular(20),
              ),

              child: Column(

                crossAxisAlignment:
                    CrossAxisAlignment.start,

                children: [

                  const Row(

                    children: [

                      Icon(

                        Icons.assignment_turned_in,

                        color: Colors.orange,
                      ),

                      SizedBox(width: 10),

                      Text(

                        "Status Tugas",

                        style: TextStyle(

                          fontSize: 18,

                          fontWeight:
                              FontWeight.bold,
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 20),

                  _studentTask(
                    "Ahmad",
                    true,
                  ),

                  _studentTask(
                    "Budi",
                    false,
                  ),

                  _studentTask(
                    "Citra",
                    true,
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

  Widget _guruFileCard({

    required IconData icon,

    required String title,

    required String subtitle,

    required Color color,

    required VoidCallback onUpload,

    required VoidCallback onPreview,
  }) {

    return Container(

      margin:
          const EdgeInsets.only(bottom: 15),

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

      child: Padding(

        padding: const EdgeInsets.all(18),

        child: Column(

          children: [

            Row(

              children: [

                CircleAvatar(

                  radius: 26,

                  backgroundColor:
                      color.withOpacity(0.15),

                  child: Icon(
                    icon,
                    color: color,
                  ),
                ),

                const SizedBox(width: 15),

                Expanded(

                  child: Column(

                    crossAxisAlignment:
                        CrossAxisAlignment.start,

                    children: [

                      Text(

                        title,

                        style: const TextStyle(

                          fontWeight:
                              FontWeight.bold,

                          fontSize: 16,
                        ),
                      ),

                      const SizedBox(height: 5),

                      Text(subtitle),
                    ],
                  ),
                ),
              ],
            ),

            const SizedBox(height: 18),

            Row(

              children: [

                Expanded(

                  child: ElevatedButton.icon(

                    onPressed: onUpload,

                    icon: const Icon(
                      Icons.upload,
                    ),

                    label: const Text(
                      "Upload",
                    ),
                  ),
                ),

                const SizedBox(width: 15),

                Expanded(

                  child: ElevatedButton.icon(

                    onPressed: onPreview,

                    icon: const Icon(
                      Icons.visibility,
                    ),

                    label: const Text(
                      "Preview",
                    ),

                    style:
                        ElevatedButton.styleFrom(

                      backgroundColor:
                          Colors.indigo,

                      foregroundColor:
                          Colors.white,
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  // =========================
  // STUDENT TASK
  // =========================

  Widget _studentTask(

    String name,

    bool submitted,
  ) {

    return Container(

      margin:
          const EdgeInsets.only(bottom: 12),

      padding:
          const EdgeInsets.all(15),

      decoration: BoxDecoration(

        color: Colors.white,

        borderRadius:
            BorderRadius.circular(15),
      ),

      child: Row(

        children: [

          CircleAvatar(

            backgroundColor:

                submitted

                    ? Colors.green.shade100

                    : Colors.red.shade100,

            child: Icon(

              submitted
                  ? Icons.check
                  : Icons.close,

              color:

                  submitted
                      ? Colors.green
                      : Colors.red,
            ),
          ),

          const SizedBox(width: 15),

          Expanded(

            child: Text(

              name,

              style: const TextStyle(

                fontWeight:
                    FontWeight.w600,

                fontSize: 16,
              ),
            ),
          ),

          Text(

            submitted
                ? "Sudah Upload"
                : "Belum Upload",

            style: TextStyle(

              color:

                  submitted
                      ? Colors.green
                      : Colors.red,

              fontWeight:
                  FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }
}