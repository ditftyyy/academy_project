import 'package:flutter/material.dart';

class MateriGuru extends StatelessWidget {

  const MateriGuru({super.key});

  @override
  Widget build(BuildContext context) {

    return Scaffold(

      appBar: AppBar(
        title: const Text(
          "Materi Guru",
        ),
      ),

      body: const Center(

        child: Text(
          "Halaman Materi Guru",
        ),
      ),
    );
  }
}
