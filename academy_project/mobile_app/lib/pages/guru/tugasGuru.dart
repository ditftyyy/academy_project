import 'package:flutter/material.dart';

class TugasGuru extends StatelessWidget {

  const TugasGuru({super.key});

  @override
  Widget build(BuildContext context) {

    return Scaffold(

      appBar: AppBar(
        title: const Text(
          "Tugas Guru",
        ),
      ),

      body: const Center(

        child: Text(
          "Halaman Tugas Guru",
        ),
      ),
    );
  }
}