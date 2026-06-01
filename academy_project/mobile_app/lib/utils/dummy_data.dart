import 'package:flutter/material.dart';

import '../models/user_model.dart';

class DummyData {

  // =========================
  // USER
  // =========================

  static List<UserModel> users = [

    // GURU 1
    UserModel(

      username: "guru1",

      password: "123456",

      role: "guru",

      nama: "Herlambang Nugroho",

      kelas: "-",

      foto:
          "https://i.pravatar.cc/300?img=12",

      mapel: [
        "Bahasa Indonesia",
      ],
    ),

    // GURU 2
    UserModel(

      username: "guru2",

      password: "123456",

      role: "guru",

      nama: "Yuli Desi",

      kelas: "-",

      foto:
          "https://i.pravatar.cc/300?img=32",

      mapel: [
        "Matematika",
      ],
    ),

    // SISWA
    UserModel(

      username: "siswa",

      password: "123456",

      role: "siswa",

      nama: "Clarisha",

      kelas: "XI RPL 1",

      foto:
          "https://i.pravatar.cc/300?img=5",

      mapel: [],
    ),
  ];

  // =========================
  // E-LEARNING
  // =========================

  static List<Map<String, dynamic>>
      elearningList = [

    {

      "nama": "Bahasa Indonesia",

      "icon": Icons.computer,

      "color": Colors.blue,

      "tugas":
          "Membuat Drama",
    },

    {

      "nama": "IPA",

      "icon": Icons.storage,

      "color": Colors.green,

      "tugas":
          "Mengamati makhluk hidup",
    },

    {

      "nama": "Matematika",

      "icon": Icons.calculate,

      "color": Colors.orange,

      "tugas":
          "Latihan Integral",
    },

    {

      "nama":
          "Bahasa Inggris",

      "icon": Icons.language,

      "color": Colors.purple,

      "tugas":
          "Speaking Assignment",
    },
  ];

  // =========================
  // ABSENSI
  // =========================

  static List<Map<String, dynamic>>
      absensiSiswa = [

    {
      "nama": "Andi",
      "status": "Hadir",
    },

    {
      "nama": "Budi",
      "status": "Izin",
    },

    {
      "nama": "Citra",
      "status": "Sakit",
    },

    {
      "nama": "Dina",
      "status": "Hadir",
    },
  ];
}